<?php
// Set timezone
date_default_timezone_set('Asia/Kolkata');

// Include database connection & security
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/security.php';
require_once __DIR__ . '/../app/AttendanceHelper.php';

Security::init($pdo);
Security::initSecureSession();

// Check if admin is logged in
if (!Security::isAuthenticated()) {
    header("Location: ../admin");
    exit();
}

$adminId = $_SESSION['admin_id'] ?? null;
Security::requirePermission('settings.view', $adminId, '../admin/dashboard');

$canManageSettings = Security::hasPermission('settings.manage', $adminId);

$pageTitle = 'GPS Location & Geofencing';
$currentPage = 'gps_location';

$flashMessage = $_SESSION['office_flash'] ?? '';
$flashType = $_SESSION['office_flash_type'] ?? '';
unset($_SESSION['office_flash'], $_SESSION['office_flash_type']);

// Handle Settings Update POST
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['save_office_settings'])) {
    if (!isset($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'], 'office_location_settings')) {
        $_SESSION['office_flash'] = 'Invalid or expired security token. Please refresh and try again.';
        $_SESSION['office_flash_type'] = 'error';
    } elseif (!$canManageSettings) {
        $_SESSION['office_flash'] = 'Access Denied: You do not possess authorization to update office location settings.';
        $_SESSION['office_flash_type'] = 'error';
    } else {
        $officeName         = trim($_POST['office_name'] ?? '');
        $officeAddress      = trim($_POST['office_address'] ?? '');
        $officeLat          = trim($_POST['office_latitude'] ?? '');
        $officeLng          = trim($_POST['office_longitude'] ?? '');
        $officeRadius       = trim($_POST['office_radius_meters'] ?? '');
        $locationEnabled    = isset($_POST['location_attendance_enabled']) ? 1 : 0;
        $wfhEarlyWindow     = max(0, min(120, (int)($_POST['wfh_early_window_minutes'] ?? 15)));
        $wfhGracePeriod     = max(0, min(120, (int)($_POST['wfh_grace_period_minutes'] ?? 15)));
        $wfhLateThreshold   = max(0, min(360, (int)($_POST['wfh_late_threshold_minutes'] ?? 30)));
        $wfhAutoReschedule  = isset($_POST['wfh_auto_reschedule_allowed']) ? 1 : 0;

        if (empty($officeName)) {
            $_SESSION['office_flash'] = 'Office Name is required.';
            $_SESSION['office_flash_type'] = 'error';
        } elseif ($officeLat === '' || !is_numeric($officeLat) || $officeLng === '' || !is_numeric($officeLng)) {
            $_SESSION['office_flash'] = 'Valid numeric Latitude and Longitude coordinates are required.';
            $_SESSION['office_flash_type'] = 'error';
        } elseif (!Security::validateCoordinates((float)$officeLat, (float)$officeLng)) {
            $_SESSION['office_flash'] = 'Coordinates out of bounds: Latitude must be between -90 and 90, Longitude between -180 and 180.';
            $_SESSION['office_flash_type'] = 'error';
        } elseif (!is_numeric($officeRadius) || (int)$officeRadius < 10 || (int)$officeRadius > 50000) {
            $_SESSION['office_flash'] = 'Allowed attendance radius must be an integer between 10 and 50,000 meters.';
            $_SESSION['office_flash_type'] = 'error';
        } else {
            try {
                $stmtCheck = $pdo->query("SELECT id FROM site_settings WHERE is_active = 1 ORDER BY updated_at DESC LIMIT 1");
                $existingSettingId = $stmtCheck->fetchColumn();

                if ($existingSettingId) {
                    $upStmt = $pdo->prepare("UPDATE site_settings SET 
                        office_name = ?, 
                        office_address = ?, 
                        office_latitude = ?, 
                        office_longitude = ?, 
                        office_radius_meters = ?, 
                        location_attendance_enabled = ?,
                        wfh_early_window_minutes = ?,
                        wfh_grace_period_minutes = ?,
                        wfh_late_threshold_minutes = ?,
                        wfh_auto_reschedule_allowed = ?,
                        updated_at = NOW()
                        WHERE id = ?");
                    $upStmt->execute([
                        $officeName,
                        $officeAddress ?: null,
                        (float)$officeLat,
                        (float)$officeLng,
                        (int)$officeRadius,
                        $locationEnabled,
                        $wfhEarlyWindow,
                        $wfhGracePeriod,
                        $wfhLateThreshold,
                        $wfhAutoReschedule,
                        $existingSettingId
                    ]);
                } else {
                    $inStmt = $pdo->prepare("INSERT INTO site_settings 
                        (office_name, office_address, office_latitude, office_longitude, office_radius_meters, location_attendance_enabled, wfh_early_window_minutes, wfh_grace_period_minutes, wfh_late_threshold_minutes, wfh_auto_reschedule_allowed, is_active) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
                    $inStmt->execute([
                        $officeName,
                        $officeAddress ?: null,
                        (float)$officeLat,
                        (float)$officeLng,
                        (int)$officeRadius,
                        $locationEnabled,
                        $wfhEarlyWindow,
                        $wfhGracePeriod,
                        $wfhLateThreshold,
                        $wfhAutoReschedule
                    ]);
                }

                Security::logAudit($adminId, 'update_office_location', 'site_settings', $existingSettingId ?: (int)$pdo->lastInsertId(), [
                    'office_name' => $officeName,
                    'latitude' => (float)$officeLat,
                    'longitude' => (float)$officeLng,
                    'radius' => (int)$officeRadius,
                    'location_enabled' => $locationEnabled
                ]);

                $_SESSION['office_flash'] = 'Office location coordinates and geofence parameters saved successfully.';
                $_SESSION['office_flash_type'] = 'success';
            } catch (PDOException $e) {
                error_log("Office location settings update error: " . $e->getMessage());
                $_SESSION['office_flash'] = 'Database error while saving office location settings. Please try again.';
                $_SESSION['office_flash_type'] = 'error';
            }
        }
    }
    header('Location: office_location');
    exit();
}

// Fetch current saved office settings
$officeSettings = AttendanceHelper::getOfficeLocationSettings($pdo);
$csrfToken = Security::generateCSRFToken('office_location_settings');

// Count employee distribution (on-site vs wfh)
$onSiteCount = 0;
$wfhCount = 0;
try {
    $stEmp = $pdo->query("SELECT work_mode, COUNT(*) as c FROM employees GROUP BY work_mode");
    while ($r = $stEmp->fetch(PDO::FETCH_ASSOC)) {
        if ($r['work_mode'] === 'wfh') $wfhCount = (int)$r['c'];
        else $onSiteCount += (int)$r['c'];
    }
} catch (PDOException $e) {}

include 'header.php';
?>

<!-- Leaflet CSS for Map Display -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

<style>
.office-grid {
    display: grid;
    grid-template-columns: 360px 1fr;
    gap: 24px;
    align-items: start;
    margin-top: 20px;
}
@media (max-width: 1024px) {
    .office-grid {
        grid-template-columns: 1fr;
    }
}

.map-card {
    background: var(--adm-surface);
    border: 1px solid var(--adm-border);
    border-radius: var(--adm-radius-lg);
    box-shadow: var(--adm-shadow-card);
    overflow: hidden;
    display: flex;
    flex-direction: column;
}
.map-toolbar {
    padding: 12px 18px;
    background: var(--adm-surface-subtle);
    border-bottom: 1px solid var(--adm-border-subtle);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}
#officeMap {
    height: 520px;
    width: 100%;
    z-index: 1;
}
@media (max-width: 768px) {
    #officeMap {
        height: 380px;
    }
}

.form-panel {
    background: var(--adm-surface);
    border: 1px solid var(--adm-border);
    border-radius: var(--adm-radius-lg);
    box-shadow: var(--adm-shadow-card);
    padding: 24px;
}

.form-group-saas {
    margin-bottom: 18px;
}
.form-group-saas label {
    display: block;
    font-size: 0.84rem;
    font-weight: 700;
    margin-bottom: 6px;
    color: var(--adm-text-main);
}
.form-control-saas {
    width: 100%;
    height: 42px;
    padding: 0 14px;
    border: 1.5px solid var(--adm-border);
    border-radius: var(--adm-radius-md);
    background: var(--adm-bg);
    color: var(--adm-text-main);
    font-family: inherit;
    font-size: 0.92rem;
    transition: all var(--adm-transition);
}
.form-control-saas:focus {
    border-color: var(--adm-brand);
    outline: none;
    box-shadow: 0 0 0 3px var(--adm-brand-light);
}

.stat-pills {
    display: flex;
    gap: 12px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}
.stat-pill {
    padding: 8px 14px;
    border-radius: var(--adm-radius-pill);
    background: var(--adm-surface-subtle);
    border: 1px solid var(--adm-border-subtle);
    font-size: 0.82rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--adm-text-body);
}

.tester-box {
    margin-top: 20px;
    padding: 16px;
    background: var(--adm-surface-subtle);
    border: 1px solid var(--adm-border-subtle);
    border-radius: var(--adm-radius-md);
}
.tester-title {
    font-size: 0.84rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--adm-muted);
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 6px;
}
</style>

<div class="content" style="max-width: 1360px; margin: 0 auto; padding: 24px 20px 60px;">

    <!-- Breadcrumb & Header Ribbon -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px; margin-bottom: 20px;">
        <div>
            <div style="font-size: 0.82rem; color: var(--adm-muted); margin-bottom: 4px; display: flex; align-items: center; gap: 6px;">
                <a href="dashboard" style="color: inherit; text-decoration: none;">Admin</a>
                <span>&rsaquo;</span>
                <a href="settings" style="color: inherit; text-decoration: none;">Settings</a>
                <span>&rsaquo;</span>
                <span style="color: var(--adm-brand); font-weight: 700;">GPS Location</span>
            </div>
            <h1 style="font-family: 'Outfit', sans-serif; font-size: 1.8rem; font-weight: 800; color: var(--adm-text-main); letter-spacing: -0.02em;">
                GPS Location &amp; Attendance Geofencing
            </h1>
        </div>

        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <a href="settings" class="btn-saas" style="padding: 8px 16px; border-radius: var(--adm-radius-pill); background: var(--adm-surface); border: 1px solid var(--adm-border); color: var(--adm-text-body); font-size: 0.85rem; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-user-gear"></i>
                <span>Account</span>
            </a>
            <a href="site_settings" class="btn-saas" style="padding: 8px 16px; border-radius: var(--adm-radius-pill); background: var(--adm-surface); border: 1px solid var(--adm-border); color: var(--adm-text-body); font-size: 0.85rem; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-sliders"></i>
                <span>Branding &amp; SMTP</span>
            </a>
            <a href="gps_location" class="btn-saas" style="padding: 8px 16px; border-radius: var(--adm-radius-pill); background: var(--adm-brand); border: 1px solid var(--adm-brand); color: #ffffff; font-size: 0.85rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-location-dot"></i>
                <span>GPS Location</span>
            </a>
        </div>
    </div>

    <!-- Flash Alert -->
    <?php if (!empty($flashMessage)): ?>
        <div class="alert-saas alert-<?php echo ($flashType === 'success') ? 'success' : 'danger'; ?>" style="margin-bottom: 20px; padding: 14px 18px; border-radius: var(--adm-radius-md); font-weight: 600; display: flex; align-items: center; gap: 10px; background: <?php echo ($flashType === 'success') ? 'var(--adm-success-light)' : 'var(--adm-danger-light)'; ?>; color: <?php echo ($flashType === 'success') ? 'var(--adm-success)' : 'var(--adm-danger)'; ?>; border: 1px solid <?php echo ($flashType === 'success') ? 'var(--adm-success)' : 'var(--adm-danger-border)'; ?>;">
            <i class="fa-solid <?php echo ($flashType === 'success') ? 'fa-circle-check' : 'fa-circle-exclamation'; ?>"></i>
            <span><?php echo htmlspecialchars($flashMessage); ?></span>
        </div>
    <?php endif; ?>

    <!-- Main Two-Column Layout (Form + Map) -->
    <div class="office-grid">

        <!-- Left: Configuration Form -->
        <div class="form-panel">
            <h2 style="font-size: 1.15rem; font-weight: 800; color: var(--adm-text-main); margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-sliders" style="color: var(--adm-brand);"></i>
                <span>Parameters</span>
            </h2>
            <p style="font-size: 0.82rem; color: var(--adm-muted); margin-bottom: 20px; line-height: 1.5;">
                Configure the primary office location parameter.
            </p>

            <form method="POST" action="" id="officeSettingsForm">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">

                <!-- Visible Parameter: Name Field Only -->
                <div class="form-group-saas">
                    <label for="office_name">Office Location Name <span style="color:var(--adm-danger);">*</span></label>
                    <input type="text" id="office_name" name="office_name" class="form-control-saas" 
                        value="<?php echo htmlspecialchars($officeSettings['office_name']); ?>" 
                        required maxlength="150" <?php echo !$canManageSettings ? 'readonly' : ''; ?>
                        placeholder="e.g. Cloud Aspire Headquarters - Main Campus">
                </div>

                <!-- Hidden Parameter Fields (Preserves backend validation, calculations & geofencing rules) -->
                <input type="hidden" id="office_address" name="office_address" value="<?php echo htmlspecialchars($officeSettings['office_address'] ?? ''); ?>">
                <input type="hidden" id="office_latitude" name="office_latitude" value="<?php echo htmlspecialchars((string)$officeSettings['office_latitude']); ?>">
                <input type="hidden" id="office_longitude" name="office_longitude" value="<?php echo htmlspecialchars((string)$officeSettings['office_longitude']); ?>">
                <input type="hidden" id="office_radius_meters" name="office_radius_meters" value="<?php echo htmlspecialchars((string)$officeSettings['office_radius_meters']); ?>">
                <input type="hidden" name="location_attendance_enabled" value="<?php echo !empty($officeSettings['location_attendance_enabled']) ? '1' : '0'; ?>">
                <input type="hidden" id="wfh_early_window_minutes" name="wfh_early_window_minutes" value="<?php echo htmlspecialchars((string)($officeSettings['wfh_early_window_minutes'] ?? 15)); ?>">
                <input type="hidden" id="wfh_grace_period_minutes" name="wfh_grace_period_minutes" value="<?php echo htmlspecialchars((string)($officeSettings['wfh_grace_period_minutes'] ?? 15)); ?>">
                <input type="hidden" id="wfh_late_threshold_minutes" name="wfh_late_threshold_minutes" value="<?php echo htmlspecialchars((string)($officeSettings['wfh_late_threshold_minutes'] ?? 30)); ?>">
                <?php if (!empty($officeSettings['wfh_auto_reschedule_allowed'])): ?>
                    <input type="hidden" name="wfh_auto_reschedule_allowed" value="1">
                <?php endif; ?>

                <?php if ($canManageSettings): ?>
                    <button type="submit" name="save_office_settings" class="btn-saas" style="width: 100%; height: 46px; margin-top: 16px; border: none; border-radius: var(--adm-radius-md); background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%); color: #ffffff; font-weight: 800; font-size: 0.95rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 14px rgba(79, 70, 229, 0.35);">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Save Parameters</span>
                    </button>
                <?php else: ?>
                    <div style="margin-top: 16px; padding: 10px 14px; background: rgba(239, 68, 68, 0.08); border: 1px solid rgba(239, 68, 68, 0.25); color: #dc2626; border-radius: var(--adm-radius-md); font-size: 0.82rem; font-weight: 600;">
                        <i class="fa-solid fa-lock"></i> Read-only mode: You need the <code>manage_settings</code> permission to save changes.
                    </div>
                <?php endif; ?>
            </form>

            <!-- Distance Testing Sandbox -->
            <div class="tester-box">
                <div class="tester-title">
                    <i class="fa-solid fa-vial"></i>
                    <span>Distance &amp; Geofence Tester</span>
                </div>
                <p style="font-size: 0.78rem; color: var(--adm-muted); margin-bottom: 10px;">
                    Click anywhere on the map or enter coordinates below to test distance calculation against current settings.
                </p>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 8px;">
                    <input type="number" step="any" id="test_lat" placeholder="Test Lat" class="form-control-saas" style="height: 36px; font-size: 0.82rem;">
                    <input type="number" step="any" id="test_lng" placeholder="Test Lng" class="form-control-saas" style="height: 36px; font-size: 0.82rem;">
                </div>
                <button type="button" onclick="runDistanceTest()" class="btn-saas" style="width: 100%; height: 34px; padding: 0 12px; border-radius: var(--adm-radius-sm); background: var(--adm-surface); border: 1px solid var(--adm-border); font-size: 0.8rem; font-weight: 700; color: var(--adm-text-body); cursor: pointer;">
                    Calculate Distance &amp; Result
                </button>
                <div id="testResultBox" style="display: none; margin-top: 10px; padding: 8px 12px; border-radius: var(--adm-radius-sm); font-size: 0.8rem; font-weight: 700;"></div>
            </div>

        </div>

        <!-- Right: Interactive Leaflet Map -->
        <div class="map-card">
            <div class="map-toolbar">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 0.88rem; font-weight: 800; color: var(--adm-text-main); display: flex; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-map-location-dot" style="color: var(--adm-brand);"></i>
                        <span>Satellite &amp; OpenStreetMap Boundary</span>
                    </span>
                </div>

                <div style="display: flex; gap: 8px;">
                    <?php if ($canManageSettings): ?>
                        <button type="button" id="btnDetectAdminGPS" class="btn-saas" onclick="detectAdminGPS(this)" style="height: 32px; padding: 0 12px; border-radius: var(--adm-radius-pill); background: var(--adm-brand-light); border: 1px solid var(--adm-brand-border); color: var(--adm-brand); font-size: 0.78rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                            <i class="fa-solid fa-crosshairs"></i>
                            <span>Detect My GPS</span>
                        </button>
                    <?php endif; ?>
                    <a id="btnGmapsExternal" href="https://www.google.com/maps?q=<?php echo urlencode($officeSettings['office_latitude'] . ',' . $officeSettings['office_longitude']); ?>" target="_blank" rel="noopener noreferrer" class="btn-saas" style="height: 32px; padding: 0 12px; border-radius: var(--adm-radius-pill); background: var(--adm-surface); border: 1px solid var(--adm-border); color: var(--adm-text-body); font-size: 0.78rem; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        <span>Google Maps</span>
                    </a>
                </div>
            </div>

            <!-- Map Container -->
            <div id="officeMap"></div>

            <div style="padding: 12px 18px; background: var(--adm-surface-subtle); border-top: 1px solid var(--adm-border-subtle); font-size: 0.78rem; color: var(--adm-muted); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
                <span>💡 <strong>Tip:</strong> Drag the blue marker or click anywhere on the map to relocate the office center.</span>
                <span id="mapCoordinatesDisplay" style="font-family: monospace; font-weight: 700; color: var(--adm-text-main);"></span>
            </div>
        </div>

    </div>

</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
    let officeLat = <?php echo (float)$officeSettings['office_latitude']; ?>;
    let officeLng = <?php echo (float)$officeSettings['office_longitude']; ?>;
    let officeRadius = <?php echo (int)$officeSettings['office_radius_meters']; ?>;

    const map = L.map('officeMap').setView([officeLat, officeLng], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Office Pin Marker (Draggable)
    const officeMarker = L.marker([officeLat, officeLng], {
        draggable: <?php echo $canManageSettings ? 'true' : 'false'; ?>
    }).addTo(map);

    officeMarker.bindPopup("<strong><?php echo htmlspecialchars($officeSettings['office_name']); ?></strong><br>Center of allowed attendance zone.").openPopup();

    // Geofence Circle
    const geofenceCircle = L.circle([officeLat, officeLng], {
        color: '#4f46e5',
        fillColor: '#6366f1',
        fillOpacity: 0.18,
        radius: officeRadius,
        weight: 2
    }).addTo(map);

    // Test Marker for Distance Testing Sandbox
    let testMarker = null;

    function updateCoordinatesDisplay(lat, lng) {
        document.getElementById('mapCoordinatesDisplay').textContent = `Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}`;
        document.getElementById('btnGmapsExternal').href = `https://www.google.com/maps?q=${lat},${lng}`;
    }
    updateCoordinatesDisplay(officeLat, officeLng);

    // Drag marker event
    officeMarker.on('dragend', function(e) {
        const pos = e.target.getLatLng();
        document.getElementById('office_latitude').value = pos.lat.toFixed(8);
        document.getElementById('office_longitude').value = pos.lng.toFixed(8);
        geofenceCircle.setLatLng(pos);
        updateCoordinatesDisplay(pos.lat, pos.lng);
    });

    // Click map to reposition office
    map.on('click', function(e) {
        <?php if ($canManageSettings): ?>
            officeMarker.setLatLng(e.latlng);
            geofenceCircle.setLatLng(e.latlng);
            document.getElementById('office_latitude').value = e.latlng.lat.toFixed(8);
            document.getElementById('office_longitude').value = e.latlng.lng.toFixed(8);
            updateCoordinatesDisplay(e.latlng.lat, e.latlng.lng);
        <?php else: ?>
            // If read only, populate test sandbox
            document.getElementById('test_lat').value = e.latlng.lat.toFixed(8);
            document.getElementById('test_lng').value = e.latlng.lng.toFixed(8);
            runDistanceTest();
        <?php endif; ?>
    });

    // Update from Form Inputs
    function updateMapFromInputs() {
        const latVal = parseFloat(document.getElementById('office_latitude').value);
        const lngVal = parseFloat(document.getElementById('office_longitude').value);
        if (!isNaN(latVal) && !isNaN(lngVal) && latVal >= -90 && latVal <= 90 && lngVal >= -180 && lngVal <= 180) {
            const newPos = [latVal, lngVal];
            officeMarker.setLatLng(newPos);
            geofenceCircle.setLatLng(newPos);
            map.panTo(newPos);
            updateCoordinatesDisplay(latVal, lngVal);
        }
    }

    function updateRadiusFromInput() {
        const radVal = parseInt(document.getElementById('office_radius_meters').value, 10);
        if (!isNaN(radVal) && radVal >= 10 && radVal <= 50000) {
            geofenceCircle.setRadius(radVal);
        }
    }

    // Detect Admin Device GPS with permission handling, error handling, and accuracy fallback
    async function detectAdminGPS(triggerBtn) {
        const btn = triggerBtn || document.getElementById('btnDetectAdminGPS') || (typeof event !== 'undefined' ? event?.currentTarget : null);
        const origText = btn ? btn.innerHTML : '';

        if (!navigator.geolocation) {
            alert("Geolocation is not supported by your browser.");
            return;
        }

        // Check if insecure HTTP context is blocking geolocation
        if (!window.isSecureContext && window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1') {
            alert("Geolocation requires a secure context (HTTPS or localhost). Please access the site via HTTPS.");
            return;
        }

        // Check browser permissions query if supported
        if (navigator.permissions && navigator.permissions.query) {
            try {
                const permStatus = await navigator.permissions.query({ name: 'geolocation' });
                if (permStatus.state === 'denied') {
                    alert("Location access has been blocked. Please enable location permissions for this site in your browser settings (click the padlock icon in the address bar) and try again.");
                    return;
                }
            } catch (e) {
                // Ignore permissions API failure if query not supported for geolocation
            }
        }

        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Detecting...';
        }

        function handleSuccess(pos) {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = origText;
            }
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            const accuracy = Math.round(pos.coords.accuracy || 0);

            const latInput = document.getElementById('office_latitude');
            const lngInput = document.getElementById('office_longitude');
            if (latInput) latInput.value = lat.toFixed(8);
            if (lngInput) lngInput.value = lng.toFixed(8);

            const newPos = [lat, lng];
            officeMarker.setLatLng(newPos);
            geofenceCircle.setLatLng(newPos);
            map.setView(newPos, 17);
            updateCoordinatesDisplay(lat, lng);

            officeMarker.bindPopup(`<strong><?php echo htmlspecialchars($officeSettings['office_name']); ?></strong><br>GPS Detected (±${accuracy}m accuracy)`).openPopup();
        }

        function handleError(err) {
            // If high accuracy timed out or unavailable, try low accuracy fallback
            if (err.code === 3 || err.code === 2) {
                if (btn) {
                    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Retrying (Network)...';
                }
                navigator.geolocation.getCurrentPosition(
                    handleSuccess,
                    function(fallbackErr) {
                        if (btn) {
                            btn.disabled = false;
                            btn.innerHTML = origText;
                        }
                        displayGpsError(fallbackErr);
                    },
                    { enableHighAccuracy: false, timeout: 15000, maximumAge: 60000 }
                );
                return;
            }

            if (btn) {
                btn.disabled = false;
                btn.innerHTML = origText;
            }
            displayGpsError(err);
        }

        function displayGpsError(err) {
            let msg = "GPS detection error: ";
            switch (err.code) {
                case 1: // PERMISSION_DENIED
                    msg = "Location permission denied. Please allow location access in your browser settings and try again.";
                    break;
                case 2: // POSITION_UNAVAILABLE
                    msg = "Location information is unavailable. Ensure device GPS / location services are active.";
                    break;
                case 3: // TIMEOUT
                    msg = "Location request timed out. Please check your network or GPS connection.";
                    break;
                default:
                    msg += err.message || "Unknown error occurred.";
            }
            alert(msg);
        }

        navigator.geolocation.getCurrentPosition(
            handleSuccess,
            handleError,
            { enableHighAccuracy: true, timeout: 8000, maximumAge: 0 }
        );
    }

    // Haversine Distance Formula in JS for Testing Widget
    function haversineDistance(lat1, lon1, lat2, lon2) {
        const R = 6371000; // meters
        const phi1 = lat1 * Math.PI / 180;
        const phi2 = lat2 * Math.PI / 180;
        const deltaPhi = (lat2 - lat1) * Math.PI / 180;
        const deltaLambda = (lon2 - lon1) * Math.PI / 180;

        const a = Math.sin(deltaPhi / 2) * Math.sin(deltaPhi / 2) +
                  Math.cos(phi1) * Math.cos(phi2) *
                  Math.sin(deltaLambda / 2) * Math.sin(deltaLambda / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return Math.round(R * c);
    }

    function runDistanceTest() {
        const offLat = parseFloat(document.getElementById('office_latitude').value);
        const offLng = parseFloat(document.getElementById('office_longitude').value);
        const offRad = parseInt(document.getElementById('office_radius_meters').value, 10) || 100;

        const tLat = parseFloat(document.getElementById('test_lat').value);
        const tLng = parseFloat(document.getElementById('test_lng').value);

        const resBox = document.getElementById('testResultBox');

        if (isNaN(tLat) || isNaN(tLng)) {
            alert("Please enter test latitude and longitude coordinates.");
            return;
        }

        const dist = haversineDistance(offLat, offLng, tLat, tLng);
        const isAllowed = dist <= offRad;

        if (testMarker) map.removeLayer(testMarker);
        testMarker = L.circleMarker([tLat, tLng], {
            radius: 8,
            color: isAllowed ? '#059669' : '#dc2626',
            fillColor: isAllowed ? '#10b981' : '#ef4444',
            fillOpacity: 0.8
        }).addTo(map);

        testMarker.bindPopup(`<strong>Test Location</strong><br>Distance: ${dist}m<br>Status: ${isAllowed ? 'ALLOWED' : 'REJECTED'}`).openPopup();

        resBox.style.display = 'block';
        if (isAllowed) {
            resBox.style.background = 'var(--adm-success-light)';
            resBox.style.color = 'var(--adm-success)';
            resBox.style.border = '1px solid var(--adm-success)';
            resBox.innerHTML = `<i class="fa-solid fa-circle-check"></i> <strong>VERIFIED:</strong> Distance is <strong>${dist}m</strong> (Inside ${offRad}m radius). Check-in would be permitted!`;
        } else {
            resBox.style.background = 'var(--adm-danger-light)';
            resBox.style.color = 'var(--adm-danger)';
            resBox.style.border = '1px solid var(--adm-danger-border)';
            resBox.innerHTML = `<i class="fa-solid fa-circle-xmark"></i> <strong>OUTSIDE GEOFENCE:</strong> Distance is <strong>${dist}m</strong> (Exceeds ${offRad}m radius). Check-in would be rejected!`;
        }
    }
</script>

<?php include 'footer.php'; ?>
