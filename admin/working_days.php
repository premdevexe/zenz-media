<?php
// ============================================================================
// Enterprise SaaS Monthly Working Days & Calendar - Minimal & Clean
// ============================================================================

date_default_timezone_set('Asia/Kolkata');

require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/security.php';
require_once __DIR__ . '/../app/AttendanceHelper.php';

Security::init($pdo);
Security::initSecureSession();

if (!Security::isAuthenticated()) {
    header("Location: ../admin");
    exit();
}

$adminId = (int)($_SESSION['admin_id'] ?? 1);
$canView = Security::hasPermission('shifts.view', $adminId) || Security::hasPermission('attendance.view', $adminId) || Security::hasPermission('settings.view', $adminId);
if (!$canView) {
    Security::requirePermission('shifts.view', $adminId, '../admin/dashboard');
}
$canManage = Security::hasPermission('shifts.manage', $adminId) || Security::hasPermission('attendance.manage', $adminId) || Security::hasPermission('settings.manage', $adminId);

$pageTitle = 'Monthly Working Days';
$currentPage = 'working_days';

// ----------------------------------------------------------------------------
// Request Parameters: Year and Month
// ----------------------------------------------------------------------------
$selectedYear  = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
$selectedMonth = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('n');

if ($selectedYear < 2000 || $selectedYear > 2099) {
    $selectedYear = (int)date('Y');
}
if ($selectedMonth < 1 || $selectedMonth > 12) {
    $selectedMonth = (int)date('n');
}

// Compute calendar details with leap year & month length handling
$firstDayTimestamp = mktime(0, 0, 0, $selectedMonth, 1, $selectedYear);
$daysInMonth       = (int)date('t', $firstDayTimestamp);
$firstDayOfWeek    = (int)date('w', $firstDayTimestamp); // 0 (Sun) to 6 (Sat)
$monthName         = date('F', $firstDayTimestamp);

// Navigation links: Previous and Next Month (with year boundary wrapping)
$prevMonth = $selectedMonth - 1;
$prevYear  = $selectedYear;
if ($prevMonth < 1) {
    $prevMonth = 12;
    $prevYear--;
}

$nextMonth = $selectedMonth + 1;
$nextYear  = $selectedYear;
if ($nextMonth > 12) {
    $nextMonth = 1;
    $nextYear++;
}

// ----------------------------------------------------------------------------
// Process POST Actions with CSRF Protection
// ----------------------------------------------------------------------------
$flashMessage = $_SESSION['working_days_flash'] ?? '';
$flashType    = $_SESSION['working_days_flash_type'] ?? '';
unset($_SESSION['working_days_flash'], $_SESSION['working_days_flash_type']);

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['action'])) {
    if (!isset($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'], 'working_days')) {
        $_SESSION['working_days_flash'] = 'Invalid or expired security token. Please refresh and try again.';
        $_SESSION['working_days_flash_type'] = 'error';
        header("Location: working_days?year={$selectedYear}&month={$selectedMonth}");
        exit();
    }

    if (!$canManage) {
        $_SESSION['working_days_flash'] = 'Access Denied: You do not possess authorization to update calendar settings.';
        $_SESSION['working_days_flash_type'] = 'error';
        header("Location: working_days?year={$selectedYear}&month={$selectedMonth}");
        exit();
    }

    $action = $_POST['action'];

    // Action: Save Days for the Month
    if ($action === 'save_month') {
        $daysData = $_POST['days'] ?? [];
        if (!is_array($daysData) || empty($daysData)) {
            $_SESSION['working_days_flash'] = 'No calendar data provided.';
            $_SESSION['working_days_flash_type'] = 'error';
        } else {
            try {
                $pdo->beginTransaction();

                $stmtUpsert = $pdo->prepare("
                    INSERT INTO monthly_working_days (`date`, `year`, `month`, `day_type`, `title`, `notes`)
                    VALUES (?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE 
                        `day_type` = VALUES(`day_type`),
                        `title`    = VALUES(`title`),
                        `notes`    = VALUES(`notes`),
                        `updated_at` = CURRENT_TIMESTAMP
                ");

                $savedCount = 0;
                $allowedTypes = ['working_day', 'weekly_off', 'holiday'];

                foreach ($daysData as $dayNum => $d) {
                    $dayNum = (int)$dayNum;
                    if ($dayNum < 1 || $dayNum > $daysInMonth) continue;

                    $dateStr = sprintf('%04d-%02d-%02d', $selectedYear, $selectedMonth, $dayNum);
                    $type    = in_array($d['type'] ?? '', $allowedTypes, true) ? $d['type'] : 'working_day';
                    $title   = trim($d['title'] ?? '');
                    $notes   = trim($d['notes'] ?? '');

                    $stmtUpsert->execute([
                        $dateStr,
                        $selectedYear,
                        $selectedMonth,
                        $type,
                        $title !== '' ? $title : null,
                        $notes !== '' ? $notes : null
                    ]);
                    $savedCount++;
                }

                $pdo->commit();

                Security::logAudit($adminId, 'update_monthly_working_days', 'monthly_working_days', null, [
                    'year' => $selectedYear,
                    'month' => $selectedMonth,
                    'days_updated' => $savedCount
                ]);

                $_SESSION['working_days_flash'] = "Calendar settings for {$monthName} {$selectedYear} saved successfully ({$savedCount} days updated).";
                $_SESSION['working_days_flash_type'] = 'success';
            } catch (Exception $e) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                error_log("Error saving monthly working days: " . $e->getMessage());
                $_SESSION['working_days_flash'] = 'Database error occurred while saving calendar settings.';
                $_SESSION['working_days_flash_type'] = 'error';
            }
        }
        header("Location: working_days?year={$selectedYear}&month={$selectedMonth}");
        exit();
    }
}

// ----------------------------------------------------------------------------
// Load Saved Days for the Selected Month
// ----------------------------------------------------------------------------
$savedDays = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM monthly_working_days WHERE `year` = ? AND `month` = ?");
    $stmt->execute([$selectedYear, $selectedMonth]);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $dayNum = (int)date('j', strtotime($row['date']));
        $savedDays[$dayNum] = $row;
    }
} catch (PDOException $e) {
    error_log("Load monthly working days error: " . $e->getMessage());
}

// ----------------------------------------------------------------------------
// Calculate Summary Counts for Selected Month
// ----------------------------------------------------------------------------
$totalDays = $daysInMonth;
$workingDaysCount = 0;
$weeklyOffCount   = 0;
$holidaysCount    = 0;

for ($day = 1; $day <= $daysInMonth; $day++) {
    $dateStr = sprintf('%04d-%02d-%02d', $selectedYear, $selectedMonth, $day);
    if (isset($savedDays[$day])) {
        $type = $savedDays[$day]['day_type'];
    } else {
        // Automatic default: Sundays are weekly off, Monday-Saturday are working days
        $w = (int)date('w', strtotime($dateStr));
        $type = ($w === 0) ? 'weekly_off' : 'working_day';
    }

    if ($type === 'working_day') {
        $workingDaysCount++;
    } elseif ($type === 'weekly_off') {
        $weeklyOffCount++;
    } elseif ($type === 'holiday') {
        $holidaysCount++;
    }
}

$csrfToken = Security::generateCSRFToken('working_days');
include __DIR__ . '/header.php';
?>

<style>
/* ── Minimal Clean Monthly Working Days Layout ── */
.mwd-wrap {
    display: flex;
    flex-direction: column;
    gap: 12px;
    max-width: 1200px;
    margin: 0 auto;
}

/* ── Compact Header Bar ── */
.mwd-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    background: var(--adm-surface);
    border: 1px solid var(--adm-border);
    border-radius: var(--adm-radius-lg);
    padding: 10px 16px;
    box-shadow: var(--adm-shadow-sm);
}

.mwd-header-left {
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
}

.mwd-title {
    font-family: 'Outfit', sans-serif;
    font-size: 1.22rem;
    font-weight: 700;
    color: var(--adm-text-main);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}
.mwd-title i {
    color: var(--adm-brand);
    font-size: 1.15rem;
}

/* ── Month & Year Nav Controls ── */
.mwd-nav {
    display: flex;
    align-items: center;
    gap: 6px;
}

.mwd-btn-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    border-radius: var(--adm-radius-sm);
    background: var(--adm-surface-subtle);
    border: 1px solid var(--adm-border);
    color: var(--adm-text-main);
    text-decoration: none;
    font-size: 0.8rem;
    transition: all 0.15s ease;
}
.mwd-btn-icon:hover {
    background: var(--adm-brand-light);
    border-color: var(--adm-brand);
    color: var(--adm-brand);
}

.mwd-select {
    height: 30px;
    padding: 2px 8px;
    border-radius: var(--adm-radius-sm);
    border: 1px solid var(--adm-border);
    background: var(--adm-surface);
    color: var(--adm-text-main);
    font-family: inherit;
    font-size: 0.82rem;
    font-weight: 600;
    cursor: pointer;
    outline: none;
    transition: border-color 0.15s;
}
.mwd-select:focus {
    border-color: var(--adm-brand);
}

.mwd-btn-subtle {
    display: inline-flex;
    align-items: center;
    padding: 2px 8px;
    height: 30px;
    font-size: 0.78rem;
    font-weight: 600;
    color: var(--adm-brand);
    background: var(--adm-brand-light);
    border: 1px solid var(--adm-brand-border);
    border-radius: var(--adm-radius-sm);
    text-decoration: none;
    transition: all 0.15s;
}
.mwd-btn-subtle:hover {
    background: var(--adm-brand);
    color: #fff;
}

.mwd-header-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.mwd-btn-ghost {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    height: 32px;
    border-radius: var(--adm-radius-sm);
    background: var(--adm-surface-subtle);
    border: 1px solid var(--adm-border);
    color: var(--adm-text-body);
    font-family: inherit;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s;
}
.mwd-btn-ghost:hover {
    background: var(--adm-surface);
    border-color: var(--adm-brand);
    color: var(--adm-brand);
}

.mwd-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 16px;
    height: 32px;
    border-radius: var(--adm-radius-sm);
    background: var(--adm-brand-gradient);
    border: none;
    color: #fff;
    font-family: inherit;
    font-size: 0.82rem;
    font-weight: 700;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(79, 70, 229, 0.25);
    transition: all 0.15s;
}
.mwd-btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(79, 70, 229, 0.35);
}

/* ── Compact Automatic Summary Bar ── */
.mwd-summary-bar {
    display: flex;
    align-items: center;
    gap: 14px;
    flex-wrap: wrap;
    background: var(--adm-surface);
    border: 1px solid var(--adm-border);
    border-radius: var(--adm-radius-md);
    padding: 8px 16px;
    font-size: 0.82rem;
    box-shadow: var(--adm-shadow-sm);
}

.mwd-summary-item {
    display: flex;
    align-items: center;
    gap: 6px;
}

.mwd-summary-label {
    color: var(--adm-muted);
    font-weight: 500;
}

.mwd-summary-val {
    color: var(--adm-text-main);
    font-weight: 700;
}

.mwd-summary-sep {
    width: 1px;
    height: 16px;
    background: var(--adm-border);
}

.mwd-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
}
.dot-working { background: #10b981; box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2); }
.dot-off     { background: #f59e0b; box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.2); }
.dot-holiday { background: #e11d48; box-shadow: 0 0 0 2px rgba(225, 29, 72, 0.2); }

/* ── Calendar Card & Grid ── */
.mwd-calendar-card {
    background: var(--adm-surface);
    border: 1px solid var(--adm-border);
    border-radius: var(--adm-radius-lg);
    padding: 12px;
    box-shadow: var(--adm-shadow-sm);
}

.mwd-weekdays {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 6px;
    margin-bottom: 6px;
    text-align: center;
}

.mwd-weekday {
    font-size: 0.74rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    color: var(--adm-muted);
    padding: 6px 0;
}
.mwd-weekday.is-sunday {
    color: #d97706;
}

.mwd-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 6px;
}

.mwd-cell-empty {
    min-height: 68px;
    background: var(--adm-surface-subtle);
    border: 1px dashed var(--adm-border-subtle);
    border-radius: var(--adm-radius-sm);
    opacity: 0.4;
}

.mwd-cell {
    min-height: 68px;
    background: var(--adm-surface);
    border: 1px solid var(--adm-border);
    border-radius: var(--adm-radius-sm);
    padding: 6px 8px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    gap: 4px;
    transition: all 0.15s ease;
    box-sizing: border-box;
}

.mwd-cell:hover {
    border-color: var(--adm-brand);
    box-shadow: var(--adm-shadow-sm);
}

/* Distinct visual colors for day types */
.mwd-cell.type-working_day {
    border-top: 3px solid #10b981;
}
.mwd-cell.type-weekly_off {
    border-top: 3px solid #f59e0b;
    background: rgba(245, 158, 11, 0.02);
}
.mwd-cell.type-holiday {
    border-top: 3px solid #e11d48;
    background: rgba(225, 29, 72, 0.03);
}

.mwd-cell.is-today {
    box-shadow: 0 0 0 2px var(--adm-brand);
}

.mwd-cell-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.mwd-day-num {
    font-family: 'Outfit', sans-serif;
    font-size: 0.96rem;
    font-weight: 800;
    color: var(--adm-text-main);
    line-height: 1;
}

.mwd-today-badge {
    font-size: 0.60rem;
    font-weight: 700;
    color: #fff;
    background: var(--adm-brand);
    border-radius: 9999px;
    padding: 1px 5px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.mwd-cell-content {
    display: flex;
    flex-direction: column;
    gap: 3px;
}

/* Status Type Selector */
.mwd-type-select {
    width: 100%;
    height: 25px;
    padding: 1px 6px;
    border-radius: var(--adm-radius-sm);
    font-family: inherit;
    font-size: 0.74rem;
    font-weight: 600;
    cursor: pointer;
    outline: none;
    transition: all 0.15s;
    border: 1px solid transparent;
}

.mwd-type-select.type-working_day {
    background: #ecfdf5;
    color: #047857;
    border-color: rgba(5, 150, 105, 0.28);
}
.mwd-type-select.type-weekly_off {
    background: #fffbeb;
    color: #b45309;
    border-color: rgba(217, 119, 6, 0.28);
}
.mwd-type-select.type-holiday {
    background: #fff1f2;
    color: #be123c;
    border-color: rgba(225, 29, 72, 0.28);
}

/* Dark mode theme overrides */
[data-theme="dark"] .mwd-type-select.type-working_day {
    background: rgba(16, 185, 129, 0.16);
    color: #34d399;
    border-color: rgba(16, 185, 129, 0.32);
}
[data-theme="dark"] .mwd-type-select.type-weekly_off {
    background: rgba(245, 158, 11, 0.16);
    color: #fbbf24;
    border-color: rgba(245, 158, 11, 0.32);
}
[data-theme="dark"] .mwd-type-select.type-holiday {
    background: rgba(225, 29, 72, 0.16);
    color: #fb7185;
    border-color: rgba(225, 29, 72, 0.32);
}

/* Holiday Title Input (Compact & only shown when Holiday is marked) */
.mwd-holiday-wrap {
    margin-top: 1px;
}

.mwd-holiday-input {
    width: 100%;
    height: 22px;
    padding: 2px 6px;
    font-size: 0.70rem;
    font-family: inherit;
    border-radius: var(--adm-radius-sm);
    border: 1px solid var(--adm-border);
    background: var(--adm-surface);
    color: var(--adm-text-main);
    box-sizing: border-box;
    transition: border-color 0.15s;
}
.mwd-holiday-input:focus {
    border-color: #e11d48;
    outline: none;
}

/* ── Responsive Styling ── */
@media (max-width: 768px) {
    .mwd-header { padding: 10px 12px; }
    .mwd-grid { gap: 4px; }
    .mwd-cell { min-height: 60px; padding: 4px 5px; }
    .mwd-day-num { font-size: 0.88rem; }
    .mwd-type-select { font-size: 0.68rem; height: 23px; padding: 1px 3px; }
    .mwd-weekday { font-size: 0.68rem; }
}

@media (max-width: 520px) {
    .mwd-header-left { gap: 10px; width: 100%; justify-content: space-between; }
    .mwd-header-actions { width: 100%; justify-content: flex-end; }
    .mwd-summary-bar { gap: 8px; font-size: 0.76rem; }
    .mwd-summary-sep { display: none; }
    .mwd-grid { gap: 3px; }
    .mwd-cell { min-height: 52px; padding: 3px 3px; }
    .mwd-day-num { font-size: 0.82rem; }
    .mwd-type-select { font-size: 0.62rem; height: 22px; }
}
</style>

<div class="mwd-wrap">

    <!-- ── Flash Notifications ── -->
    <?php if (!empty($flashMessage)): ?>
        <div class="alert alert-<?php echo ($flashType === 'success') ? 'success' : 'danger'; ?>" role="alert" style="margin-bottom: 0;">
            <i class="fa-solid <?php echo ($flashType === 'success') ? 'fa-circle-check' : 'fa-triangle-exclamation'; ?>"></i>
            <div><?php echo htmlspecialchars($flashMessage); ?></div>
        </div>
    <?php endif; ?>

    <!-- ── Compact Header & Month Navigation ── -->
    <div class="mwd-header">
        <div class="mwd-header-left">
            <h1 class="mwd-title">
                <i class="fa-solid fa-calendar-days"></i>
                <span>Monthly Working Days</span>
            </h1>

            <div class="mwd-nav">
                <a href="?year=<?php echo $prevYear; ?>&month=<?php echo $prevMonth; ?>" class="mwd-btn-icon" title="Previous Month">
                    <i class="fa-solid fa-chevron-left"></i>
                </a>

                <select id="navMonth" class="mwd-select" onchange="jumpToMonth()">
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?php echo $m; ?>" <?php echo $m === $selectedMonth ? 'selected' : ''; ?>>
                            <?php echo date('F', mktime(0, 0, 0, $m, 1, 2000)); ?>
                        </option>
                    <?php endfor; ?>
                </select>

                <select id="navYear" class="mwd-select" onchange="jumpToMonth()">
                    <?php 
                    $curY = (int)date('Y');
                    for ($y = $curY - 3; $y <= $curY + 5; $y++): ?>
                        <option value="<?php echo $y; ?>" <?php echo $y === $selectedYear ? 'selected' : ''; ?>>
                            <?php echo $y; ?>
                        </option>
                    <?php endfor; ?>
                </select>

                <a href="?year=<?php echo $nextYear; ?>&month=<?php echo $nextMonth; ?>" class="mwd-btn-icon" title="Next Month">
                    <i class="fa-solid fa-chevron-right"></i>
                </a>

                <?php if ($selectedYear !== (int)date('Y') || $selectedMonth !== (int)date('n')): ?>
                    <a href="?year=<?php echo date('Y'); ?>&month=<?php echo date('n'); ?>" class="mwd-btn-subtle" title="Current Month">Today</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="mwd-header-actions">
            <button type="button" class="mwd-btn-ghost" onclick="autoFillSundaysOff()" title="Auto-fill Sundays as Weekly Off and Mon–Sat as Working" <?php echo !$canManage ? 'disabled' : ''; ?>>
                <i class="fa-solid fa-wand-magic-sparkles"></i>
                <span>Auto-fill Defaults</span>
            </button>
            <button type="submit" form="workingDaysForm" class="mwd-btn-primary" <?php echo !$canManage ? 'disabled' : ''; ?>>
                <i class="fa-solid fa-floppy-disk"></i>
                <span>Save Changes</span>
            </button>
        </div>
    </div>

    <!-- ── Automatic Summary Bar ── -->
    <div class="mwd-summary-bar">
        <div class="mwd-summary-item">
            <span class="mwd-summary-label">Month:</span>
            <strong class="mwd-summary-val"><?php echo $monthName . ' ' . $selectedYear; ?></strong>
        </div>
        <div class="mwd-summary-sep"></div>
        <div class="mwd-summary-item">
            <span class="mwd-summary-label">Total Days:</span>
            <strong class="mwd-summary-val" id="summaryTotal"><?php echo $totalDays; ?></strong>
        </div>
        <div class="mwd-summary-sep"></div>
        <div class="mwd-summary-item">
            <span class="mwd-dot dot-working"></span>
            <span class="mwd-summary-label">Working Days:</span>
            <strong class="mwd-summary-val" id="summaryWorking"><?php echo $workingDaysCount; ?></strong>
        </div>
        <div class="mwd-summary-sep"></div>
        <div class="mwd-summary-item">
            <span class="mwd-dot dot-off"></span>
            <span class="mwd-summary-label">Weekly Offs:</span>
            <strong class="mwd-summary-val" id="summaryOffs"><?php echo $weeklyOffCount; ?></strong>
        </div>
        <div class="mwd-summary-sep"></div>
        <div class="mwd-summary-item">
            <span class="mwd-dot dot-holiday"></span>
            <span class="mwd-summary-label">Holidays:</span>
            <strong class="mwd-summary-val" id="summaryHolidays"><?php echo $holidaysCount; ?></strong>
        </div>
    </div>

    <!-- ── Monthly Calendar Form ── -->
    <form method="post" id="workingDaysForm">
        <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
        <input type="hidden" name="action" value="save_month">

        <div class="mwd-calendar-card">
            <!-- Weekday Header: Sun to Sat -->
            <div class="mwd-weekdays">
                <div class="mwd-weekday is-sunday">Sun</div>
                <div class="mwd-weekday">Mon</div>
                <div class="mwd-weekday">Tue</div>
                <div class="mwd-weekday">Wed</div>
                <div class="mwd-weekday">Thu</div>
                <div class="mwd-weekday">Fri</div>
                <div class="mwd-weekday">Sat</div>
            </div>

            <!-- Calendar Days Grid -->
            <div class="mwd-grid">
                <!-- Empty Offset Cells Before Month Starts -->
                <?php for ($empty = 0; $empty < $firstDayOfWeek; $empty++): ?>
                    <div class="mwd-cell-empty"></div>
                <?php endfor; ?>

                <!-- Month Days -->
                <?php for ($day = 1; $day <= $daysInMonth; $day++): ?>
                    <?php
                    $dateStr     = sprintf('%04d-%02d-%02d', $selectedYear, $selectedMonth, $day);
                    $weekdayNum  = (int)date('w', strtotime($dateStr)); // 0 = Sun
                    $isToday     = ($dateStr === date('Y-m-d'));

                    if (isset($savedDays[$day])) {
                        $currentType  = $savedDays[$day]['day_type'];
                        $currentTitle = $savedDays[$day]['title'] ?? '';
                        $currentNotes = $savedDays[$day]['notes'] ?? '';
                    } else {
                        // Automatic Day Marking: Sundays are weekly off, Mon-Sat are working days
                        $currentType  = ($weekdayNum === 0) ? 'weekly_off' : 'working_day';
                        $currentTitle = ($weekdayNum === 0) ? 'Sunday Off' : '';
                        $currentNotes = '';
                    }
                    ?>
                    <div class="mwd-cell type-<?php echo htmlspecialchars($currentType); ?> <?php echo $isToday ? 'is-today' : ''; ?>" id="cell_<?php echo $day; ?>" data-day="<?php echo $day; ?>" data-weekday="<?php echo $weekdayNum; ?>">
                        <div class="mwd-cell-header">
                            <span class="mwd-day-num"><?php echo $day; ?></span>
                            <?php if ($isToday): ?>
                                <span class="mwd-today-badge">Today</span>
                            <?php endif; ?>
                        </div>

                        <div class="mwd-cell-content">
                            <select 
                                name="days[<?php echo $day; ?>][type]" 
                                id="select_<?php echo $day; ?>" 
                                class="mwd-type-select type-<?php echo htmlspecialchars($currentType); ?>" 
                                onchange="onDayTypeChange(<?php echo $day; ?>, this.value)"
                                <?php echo !$canManage ? 'disabled' : ''; ?>>
                                <option value="working_day" <?php echo $currentType === 'working_day' ? 'selected' : ''; ?>>Working</option>
                                <option value="weekly_off" <?php echo $currentType === 'weekly_off' ? 'selected' : ''; ?>>Weekly Off</option>
                                <option value="holiday" <?php echo $currentType === 'holiday' ? 'selected' : ''; ?>>Holiday</option>
                            </select>

                            <div class="mwd-holiday-wrap" id="wrap_title_<?php echo $day; ?>" style="<?php echo $currentType === 'holiday' ? '' : 'display: none;'; ?>">
                                <input 
                                    type="text" 
                                    name="days[<?php echo $day; ?>][title]" 
                                    id="title_<?php echo $day; ?>" 
                                    class="mwd-holiday-input" 
                                    placeholder="Holiday name" 
                                    value="<?php echo htmlspecialchars($currentTitle); ?>"
                                    maxlength="100"
                                    <?php echo !$canManage ? 'readonly' : ''; ?>>
                            </div>

                            <input type="hidden" name="days[<?php echo $day; ?>][notes]" value="<?php echo htmlspecialchars($currentNotes); ?>">
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </form>
</div>

<script>
// Month & Year Navigation Jump
function jumpToMonth() {
    const month = document.getElementById('navMonth').value;
    const year  = document.getElementById('navYear').value;
    window.location.href = `working_days?year=${year}&month=${month}`;
}

// Live Summary Recalculation
function recalculateSummary() {
    let working = 0;
    let offs = 0;
    let holidays = 0;

    const selects = document.querySelectorAll('.mwd-type-select');
    selects.forEach(sel => {
        if (sel.value === 'working_day') working++;
        else if (sel.value === 'weekly_off') offs++;
        else if (sel.value === 'holiday') holidays++;
    });

    const elTotal = document.getElementById('summaryTotal');
    const elWorking = document.getElementById('summaryWorking');
    const elOffs = document.getElementById('summaryOffs');
    const elHolidays = document.getElementById('summaryHolidays');

    if (elTotal) elTotal.textContent = selects.length;
    if (elWorking) elWorking.textContent = working;
    if (elOffs) elOffs.textContent = offs;
    if (elHolidays) elHolidays.textContent = holidays;
}

// Handle Day Type Change
function onDayTypeChange(dayNum, newType) {
    const cell = document.getElementById('cell_' + dayNum);
    const select = document.getElementById('select_' + dayNum);
    const wrap = document.getElementById('wrap_title_' + dayNum);
    const titleInput = document.getElementById('title_' + dayNum);

    if (cell) {
        cell.classList.remove('type-working_day', 'type-weekly_off', 'type-holiday');
        cell.classList.add('type-' + newType);
    }

    if (select) {
        select.classList.remove('type-working_day', 'type-weekly_off', 'type-holiday');
        select.classList.add('type-' + newType);
    }

    if (newType === 'holiday') {
        if (wrap) wrap.style.display = 'block';
        if (titleInput && !titleInput.value.trim()) {
            titleInput.placeholder = 'Holiday name...';
            titleInput.focus();
        }
    } else {
        if (wrap) wrap.style.display = 'none';
        if (newType === 'weekly_off' && titleInput && !titleInput.value.trim()) {
            titleInput.value = (cell && cell.dataset.weekday === '0') ? 'Sunday Off' : 'Weekly Off';
        } else if (newType === 'working_day' && titleInput) {
            titleInput.value = '';
        }
    }

    recalculateSummary();
}

// Automatic Day Marking: Reset to standard defaults (Sundays Off, Mon-Sat Working)
function autoFillSundaysOff() {
    const daysInMonth = <?php echo $daysInMonth; ?>;

    for (let day = 1; day <= daysInMonth; day++) {
        const cell = document.getElementById('cell_' + day);
        const select = document.getElementById('select_' + day);
        const titleInput = document.getElementById('title_' + day);
        if (!cell || !select) continue;

        const weekday = parseInt(cell.dataset.weekday, 10);
        if (weekday === 0) {
            // Sunday = Weekly Off
            select.value = 'weekly_off';
            if (titleInput) titleInput.value = 'Sunday Off';
            onDayTypeChange(day, 'weekly_off');
        } else {
            // Monday–Saturday = Working Day
            select.value = 'working_day';
            if (titleInput) titleInput.value = '';
            onDayTypeChange(day, 'working_day');
        }
    }

    recalculateSummary();
}
</script>

<?php include __DIR__ . '/footer.php'; ?>
