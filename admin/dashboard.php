<?php
// Set timezone to India / Local Time
date_default_timezone_set('Asia/Kolkata');

// Include database connection first
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/security.php';
Security::init($pdo);
Security::initSecureSession();

// Check if admin is logged in
if (!Security::isAuthenticated()) {
    if (isset($_SESSION['employee_logged_in']) && $_SESSION['employee_logged_in'] === true) {
        header("Location: /employee_dashboard");
        exit();
    }
    header("Location: /login.php");
    exit();
}

// Check session timeout
if (!Security::setSessionTimeout()) {
    header("Location: ../admin?msg=timeout");
    exit();
}

// Set page variables for header
$pageTitle = 'Dashboard';
$currentPage = 'dashboard';

// Fetch real stats from database
$totalEmployees = 0;
$presentToday = 0;
$absentToday = 0;
$lateToday = 0;
$earlyLeaveToday = 0;
$monthlyAttendanceRate = 0;
$pendingLeaves = 0;
$pendingAadhar = 0;
$activeTasks = 0;
$recentAttendance = [];
$chartDates = [];
$chartPresent = [];
$chartLate = [];

$today = date('Y-m-d');
$monthStart = date('Y-m-01');
$monthEnd = date('Y-m-t');

// Pre-fill default 7-day windows in case of empty records or exceptions
for ($i = 6; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-$i days"));
    $chartDates[] = date('M d', strtotime($d));
    $chartPresent[] = 0;
    $chartLate[] = 0;
}

// Determine dynamic greeting
$hour = (int)date('G');
if ($hour < 12) {
    $greeting = "Good morning";
} elseif ($hour < 17) {
    $greeting = "Good afternoon";
} else {
    $greeting = "Good evening";
}
$adminName = htmlspecialchars($_SESSION['admin_username'] ?? 'Administrator');

// Handle quick toggle action for Employee Panel Index Page
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_employee_index') {
    $token = $_POST['csrf_token'] ?? '';
    if (Security::validateCSRFToken($token, 'dashboard_toggle_index')) {
        $newState = !empty($_POST['employee_index_enabled']) ? 1 : 0;
        try {
            $stmt = $pdo->prepare("UPDATE site_settings SET employee_index_enabled = ? WHERE is_active = 1");
            $stmt->execute([$newState]);
            if ($stmt->rowCount() === 0) {
                $pdo->prepare("UPDATE site_settings SET employee_index_enabled = ?")->execute([$newState]);
            }
            Security::logAudit($_SESSION['admin_id'] ?? 1, 'toggle_employee_index', 'site_settings', 0, ['employee_index_enabled' => $newState]);
            $statusText = $newState ? 'turned ON' : 'turned OFF';
            $_SESSION['dashboard_flash'] = "Employee Panel index page successfully {$statusText}.";
            $_SESSION['dashboard_flash_type'] = 'success';
        } catch (PDOException $e) {
            $_SESSION['dashboard_flash'] = "Database error while updating Employee Panel status.";
            $_SESSION['dashboard_flash_type'] = 'error';
        }
    } else {
        $_SESSION['dashboard_flash'] = "Invalid security token. Please refresh and try again.";
        $_SESSION['dashboard_flash_type'] = 'error';
    }
    header("Location: dashboard");
    exit();
}

$empIndexEnabled = 1;
try {
    $stmtEmp = $pdo->query("SELECT employee_index_enabled FROM site_settings WHERE is_active = 1 ORDER BY updated_at DESC LIMIT 1");
    if ($rowEmp = $stmtEmp->fetch(PDO::FETCH_ASSOC)) {
        if (isset($rowEmp['employee_index_enabled'])) {
            $empIndexEnabled = (int)$rowEmp['employee_index_enabled'];
        }
    }
} catch (PDOException $e) {}

$csrfTokenDashToggle = Security::generateCSRFToken('dashboard_toggle_index');

try {
    // 1. Total employees
    $stmt = $pdo->query("SELECT COUNT(*) FROM employees");
    $totalEmployees = (int)$stmt->fetchColumn();

    // 2. Present Today
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM attendance WHERE date = ? AND (attendance_status IN ('present', 'completed', 'half_day') OR status = 'present')");
    $stmt->execute([$today]);
    $presentToday = (int)$stmt->fetchColumn();

    // 3. On Leave Today
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM attendance WHERE date = ? AND (attendance_status = 'on_leave' OR status = 'on_leave')");
    $stmt->execute([$today]);
    $onLeaveToday = (int)$stmt->fetchColumn();
    
    $absentToday = max(0, $totalEmployees - $presentToday - $onLeaveToday);

    // 4. Late Employees Today
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM attendance WHERE date = ? AND (attendance_status = 'delayed' OR status = 'late' OR delay_minutes > 0)");
    $stmt->execute([$today]);
    $lateToday = (int)$stmt->fetchColumn();

    // 5. Early Leave Today
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM attendance WHERE date = ? AND (attendance_status = 'left_early' OR early_exit_minutes > 0)");
    $stmt->execute([$today]);
    $earlyLeaveToday = (int)$stmt->fetchColumn();

    // 6. Monthly Attendance Rate
    $stmt = $pdo->prepare("SELECT attendance_status, status, COUNT(*) as cnt FROM attendance WHERE date BETWEEN ? AND ? GROUP BY attendance_status, status");
    $stmt->execute([$monthStart, $monthEnd]);
    $attStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $totalAtt = 0;
    $presentAtt = 0;
    foreach ($attStats as $s) {
        $totalAtt += $s['cnt'];
        $st = $s['attendance_status'] ?: $s['status'];
        if (in_array($st, ['present', 'completed', 'half_day', 'delayed', 'late'])) {
            $presentAtt += $s['cnt'];
        }
    }
    $monthlyAttendanceRate = $totalAtt > 0 ? round(($presentAtt / $totalAtt) * 100, 1) : 0;

    // 7. Action Items (Pending Leaves, KYC, Tasks)
    $stmtLeaves = $pdo->query("SHOW TABLES LIKE 'leave_requests'");
    if ($stmtLeaves->rowCount() > 0) {
        $pendingLeaves = (int)$pdo->query("SELECT COUNT(*) FROM leave_requests WHERE status = 'pending'")->fetchColumn();
    }
    
    $pendingAadhar = (int)$pdo->query("SELECT COUNT(*) FROM employees WHERE aadhar_verified = 1")->fetchColumn();
    
    $stmtTasks = $pdo->query("SHOW TABLES LIKE 'tasks'");
    if ($stmtTasks->rowCount() > 0) {
        $activeTasks = (int)$pdo->query("SELECT COUNT(*) FROM tasks WHERE status IN ('sent', 'in_progress', 'verify')")->fetchColumn();
    }

    // 8. Recent Attendance Activity Feed
    $stmt = $pdo->query("
        SELECT a.*, e.first_name, e.last_name, e.avatar, e.department, e.position, e.employee_id as emp_code
        FROM attendance a
        LEFT JOIN employees e ON a.employee_id = e.id
        ORDER BY a.date DESC, a.check_in DESC
        LIMIT 6
    ");
    $recentAttendance = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 9. Chart Data: Last 7 Days Attendance
    $chartDates = [];
    $chartPresent = [];
    $chartLate = [];
    for ($i = 6; $i >= 0; $i--) {
        $d = date('Y-m-d', strtotime("-$i days"));
        $chartDates[] = date('M d', strtotime($d));
        
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM attendance WHERE date = ? AND (attendance_status IN ('present', 'completed', 'half_day', 'delayed') OR status IN ('present', 'late'))");
        $stmt->execute([$d]);
        $chartPresent[] = (int)$stmt->fetchColumn();
        
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM attendance WHERE date = ? AND (attendance_status = 'delayed' OR delay_minutes > 0)");
        $stmt->execute([$d]);
        $chartLate[] = (int)$stmt->fetchColumn();
    }

} catch (PDOException $e) {
    error_log("Dashboard stats error: " . $e->getMessage());
}

?>

<?php include __DIR__ . '/header.php'; ?>

<!-- Custom Dashboard Styling & Animations -->
<style>
    /* ── Entrance Animation ── */
    @keyframes dashFadeUp {
        from {
            opacity: 0;
            transform: translateY(14px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-up {
        animation: dashFadeUp 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    /* Staggered Delays */
    .stagger-1 { animation-delay: 0.04s; }
    .stagger-2 { animation-delay: 0.08s; }
    .stagger-3 { animation-delay: 0.12s; }
    .stagger-4 { animation-delay: 0.16s; }
    .stagger-5 { animation-delay: 0.20s; }
    .stagger-6 { animation-delay: 0.24s; }

    /* ── Dynamic Animated Icon Suite ── */
    @keyframes iconFloat {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-4px); }
    }

    @keyframes iconPulseGlow {
        0%, 100% { transform: scale(1); filter: drop-shadow(0 0 2px rgba(79, 70, 229, 0.35)); }
        50% { transform: scale(1.14); filter: drop-shadow(0 0 8px rgba(79, 70, 229, 0.65)); }
    }

    @keyframes iconBounceSoft {
        0%, 100% { transform: translateY(0); }
        30% { transform: translateY(-5px); }
        50% { transform: translateY(0); }
        70% { transform: translateY(-2px); }
    }

    @keyframes iconSlowSpin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    @keyframes iconSparkleBeat {
        0%, 100% { transform: scale(1) rotate(0deg); opacity: 1; }
        50% { transform: scale(1.18) rotate(12deg); opacity: 0.85; }
    }

    .icon-anim-float {
        animation: iconFloat 3.2s ease-in-out infinite;
        display: inline-block;
    }

    .icon-anim-pulse {
        animation: iconPulseGlow 2.5s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        display: inline-block;
    }

    .icon-anim-bounce {
        animation: iconBounceSoft 2.4s ease-in-out infinite;
        display: inline-block;
    }

    .icon-anim-spin {
        animation: iconSlowSpin 14s linear infinite;
        display: inline-block;
    }

    .icon-anim-sparkle {
        animation: iconSparkleBeat 2.2s ease-in-out infinite;
        display: inline-block;
    }

    .dash-card:hover .stat-icon-wrap i,
    .btn-dash-action:hover i,
    .insight-card:hover .insight-icon-wrap i {
        animation: iconBounceSoft 0.7s ease-in-out 1;
    }

    /* ── Dashboard Hero Header ── */
    .dash-hero-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 18px;
        margin-bottom: 28px;
    }

    .dash-greeting-title {
        font-family: 'Outfit', sans-serif;
        font-size: 1.9rem;
        font-weight: 800;
        color: var(--adm-text-main);
        letter-spacing: -0.02em;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .dash-greeting-subtitle {
        font-size: 0.95rem;
        color: var(--adm-muted);
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .live-clock-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 5px 14px;
        background: var(--adm-surface);
        border: 1px solid var(--adm-border);
        border-radius: var(--adm-radius-pill);
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--adm-text-main);
        box-shadow: var(--adm-shadow-sm);
    }
    .pulse-green {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--adm-success);
        box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.25);
        animation: pulseLive 2s infinite;
    }
    @keyframes pulseLive {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.25); opacity: 0.7; }
    }

    /* ── Action Required Insights Banners ── */
    .insights-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(min(100%, 320px), 1fr));
        gap: 16px;
        margin-bottom: 28px;
        min-width: 0;
        max-width: 100%;
    }

    .insight-card {
        padding: 16px 20px;
        border-radius: var(--adm-radius-lg);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        transition: transform var(--adm-transition), box-shadow var(--adm-transition);
        box-shadow: var(--adm-shadow-sm);
    }
    .insight-card:hover {
        transform: translateY(-2px);
    }

    .insight-card.warning {
        background: var(--adm-warning-light);
        border: 1px solid rgba(217, 119, 6, 0.3);
        color: var(--adm-warning);
    }
    .insight-card.info {
        background: var(--adm-brand-light);
        border: 1px solid var(--adm-brand-border);
        color: var(--adm-brand);
    }

    .insight-left {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .insight-icon-wrap {
        width: 44px;
        height: 44px;
        border-radius: var(--adm-radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        background: var(--adm-surface);
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        flex-shrink: 0;
    }
    .insight-card.warning .insight-icon-wrap { color: var(--adm-warning); }
    .insight-card.info .insight-icon-wrap { color: var(--adm-brand); }

    .insight-text strong {
        display: block;
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--adm-text-main);
        margin-bottom: 2px;
    }
    .insight-text span {
        font-size: 0.82rem;
        opacity: 0.85;
    }

    .insight-btn {
        padding: 7px 14px;
        border-radius: var(--adm-radius-md);
        font-size: 0.82rem;
        font-weight: 700;
        text-decoration: none;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--adm-surface);
        color: var(--adm-text-main);
        border: 1px solid var(--adm-border);
        transition: all var(--adm-transition);
    }
    .insight-btn:hover {
        background: var(--adm-brand);
        color: #fff;
        border-color: var(--adm-brand);
    }

    /* ── Primary Summary Metrics Cards (Exact Employee Directory Component Design) ── */
    .emp-kpi-grid,
    .cards-grid {
        display: grid !important;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)) !important;
        gap: 16px !important;
        margin-bottom: 24px !important;
    }

    .emp-kpi-card,
    .dash-card {
        background: var(--adm-surface) !important;
        border: 1px solid var(--adm-border) !important;
        border-radius: var(--adm-radius-lg) !important;
        padding: 20px !important;
        box-shadow: var(--adm-shadow-card) !important;
        display: flex !important;
        flex-direction: row !important;
        align-items: flex-start !important;
        justify-content: space-between !important;
        text-align: left !important;
        position: relative !important;
        overflow: hidden !important;
        transition: all var(--adm-transition) !important;
        cursor: pointer !important;
        box-sizing: border-box !important;
    }

    .emp-kpi-card:hover,
    .dash-card:hover {
        transform: translateY(-3px) !important;
        box-shadow: var(--adm-shadow-hover) !important;
        border-color: var(--adm-brand-border) !important;
    }

    /* 3px Top Accent Stripe matching Employee Directory */
    .emp-kpi-card::before,
    .dash-card::before {
        content: '' !important;
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 3px !important;
        background: transparent;
        transition: background var(--adm-transition);
    }

    .emp-kpi-card.kpi-workforce::before,
    .dash-card--workforce::before { background: linear-gradient(90deg, #4f46e5, #7c3aed) !important; }

    .emp-kpi-card.kpi-present::before,
    .dash-card--present::before { background: linear-gradient(90deg, #059669, #10b981) !important; }

    .emp-kpi-card.kpi-absent::before,
    .dash-card--absent::before { background: linear-gradient(90deg, #e11d48, #f43f5e) !important; }

    .emp-kpi-card.kpi-late::before,
    .dash-card--late::before { background: linear-gradient(90deg, #d97706, #f59e0b) !important; }

    .emp-kpi-card.kpi-early::before,
    .dash-card--early::before { background: linear-gradient(90deg, #8b5cf6, #a855f7) !important; }

    .emp-kpi-card.kpi-rate::before,
    .dash-card--rate::before { background: linear-gradient(90deg, #2563eb, #06b6d4) !important; }

    /* Left Info Column */
    .emp-kpi-info,
    .dash-card-info {
        display: flex !important;
        flex-direction: column !important;
        gap: 4px !important;
        min-width: 0 !important;
        text-align: left !important;
    }

    .emp-kpi-label,
    .dash-card-label {
        font-size: 0.8rem !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.04em !important;
        color: var(--adm-muted) !important;
        margin: 0 !important;
        line-height: 1.2 !important;
    }

    .emp-kpi-value,
    .dash-card-value {
        font-family: 'Outfit', sans-serif !important;
        font-size: 1.75rem !important;
        font-weight: 800 !important;
        color: var(--adm-text-main) !important;
        line-height: 1.1 !important;
        margin: 0 !important;
    }

    .emp-kpi-sub,
    .dash-card-sub {
        font-size: 0.78rem !important;
        color: var(--adm-faint) !important;
        display: flex !important;
        align-items: center !important;
        gap: 5px !important;
        margin-top: 2px !important;
        line-height: 1.2 !important;
    }

    /* Right Icon Badge (44x44, radius-md) */
    .emp-kpi-icon,
    .dash-card-icon {
        width: 44px !important;
        height: 44px !important;
        border-radius: var(--adm-radius-md) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 1.25rem !important;
        flex-shrink: 0 !important;
        margin: 0 !important;
    }

    .dash-card-icon--blue,
    .emp-kpi-card.kpi-workforce .emp-kpi-icon { background: var(--adm-brand-light) !important; color: var(--adm-brand) !important; }

    .dash-card-icon--green,
    .emp-kpi-card.kpi-present .emp-kpi-icon { background: var(--adm-success-light) !important; color: var(--adm-success) !important; }

    .dash-card-icon--red,
    .emp-kpi-card.kpi-absent .emp-kpi-icon { background: var(--adm-danger-light) !important; color: var(--adm-danger) !important; }

    .dash-card-icon--orange,
    .emp-kpi-card.kpi-late .emp-kpi-icon { background: var(--adm-warning-light) !important; color: var(--adm-warning) !important; }

    .dash-card-icon--purple,
    .emp-kpi-card.kpi-early .emp-kpi-icon { background: rgba(139, 92, 246, 0.16) !important; color: #8b5cf6 !important; }

    .dash-card-icon--cyan,
    .emp-kpi-card.kpi-rate .emp-kpi-icon { background: rgba(37, 99, 235, 0.12) !important; color: #2563eb !important; }

    /* ── Analytics & Visual Charts Grid (Compact Layout) ── */
    .charts-grid-layout {
        display: grid;
        grid-template-columns: 1.85fr 1fr;
        gap: 16px;
        margin-bottom: 20px;
        min-width: 0;
        max-width: 100%;
    }
    .charts-grid-layout > * {
        min-width: 0;
        max-width: 100%;
    }

    .chart-panel-card {
        background: var(--adm-surface-card);
        border: 1px solid var(--adm-border);
        border-radius: var(--adm-radius-lg);
        padding: 16px 20px;
        box-shadow: var(--adm-shadow-card);
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
        position: relative;
        min-width: 0;
        max-width: 100%;
        box-sizing: border-box;
    }
    .chart-canvas-container canvas {
        max-width: 100% !important;
    }

    .chart-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--adm-border-subtle);
    }

    .chart-panel-title {
        font-family: 'Outfit', sans-serif;
        font-size: 1.08rem;
        font-weight: 700;
        color: var(--adm-text-main);
        letter-spacing: -0.01em;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .chart-panel-title i {
        color: var(--adm-brand);
        font-size: 1.05rem;
    }

    .chart-panel-desc {
        font-size: 0.80rem;
        color: var(--adm-muted);
        margin-top: 2px;
    }

    .chart-canvas-container {
        position: relative;
        width: 100%;
        height: 240px;
        min-height: 240px;
    }

    /* ── Live Attendance Activity Table ── */
    .feed-panel-card {
        background: var(--adm-surface-card);
        border: 1px solid var(--adm-border);
        border-radius: var(--adm-radius-xl);
        padding: 28px;
        box-shadow: var(--adm-shadow-card);
        margin-bottom: 32px;
    }

    .table-clean {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.9rem;
    }
    .table-clean th {
        text-align: left;
        padding: 12px 16px;
        font-size: 0.76rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--adm-faint);
        border-bottom: 1px solid var(--adm-border);
        background: var(--adm-surface-subtle);
    }
    .table-clean th:first-child { border-top-left-radius: var(--adm-radius-md); }
    .table-clean th:last-child { border-top-right-radius: var(--adm-radius-md); text-align: right; }

    .table-clean td {
        padding: 14px 16px;
        border-bottom: 1px solid var(--adm-border-subtle);
        color: var(--adm-text-body);
        vertical-align: middle;
    }
    .table-clean tr:last-child td {
        border-bottom: none;
    }
    .table-clean tr:hover td {
        background: var(--adm-surface-subtle);
    }

    .emp-row-user {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .emp-row-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        object-fit: cover;
        background: var(--adm-surface-subtle);
        flex-shrink: 0;
    }
    .emp-row-initials {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        font-weight: 800;
        color: #fff;
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        flex-shrink: 0;
    }
    .emp-row-name {
        font-weight: 700;
        color: var(--adm-text-main);
        display: block;
    }
    .emp-row-code {
        font-size: 0.75rem;
        color: var(--adm-faint);
    }

    /* Badges */
    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: var(--adm-radius-pill);
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: capitalize;
    }
    .badge-status.present   { background: var(--adm-success-light); color: var(--adm-success); }
    .badge-status.delayed   { background: var(--adm-warning-light); color: var(--adm-warning); }
    .badge-status.completed { background: rgba(59, 130, 246, 0.14); color: #2563eb; }
    .badge-status.absent    { background: var(--adm-danger-light); color: var(--adm-danger); }

    /* Empty state */
    .empty-state-box {
        text-align: center;
        padding: 40px 20px;
        color: var(--adm-faint);
    }
    .empty-state-box i {
        font-size: 2.5rem;
        color: var(--adm-faint);
        margin-bottom: 12px;
        opacity: 0.5;
    }

    /* ── Responsive ── */
    @media (max-width: 1024px) {
        .charts-grid-layout { grid-template-columns: 1fr; }
    }
    @media (max-width: 768px) {
        .dash-hero-header { flex-direction: column; align-items: flex-start; }
        .emp-kpi-grid,
        .cards-grid { grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) !important; gap: 14px !important; }
        .chart-panel-card { padding: 20px 16px; }
        .feed-panel-card { padding: 20px 16px; overflow-x: auto; }
        .insights-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 480px) {
        .dash-hero-header { padding: 16px 14px; }
        .insights-grid { grid-template-columns: 1fr; margin-bottom: 20px; }
        .insight-card { padding: 12px; flex-direction: column; align-items: flex-start; gap: 10px; }
        .insight-btn { width: 100%; justify-content: center; }
        .emp-kpi-grid,
        .cards-grid { grid-template-columns: 1fr !important; gap: 12px !important; margin-bottom: 24px !important; }
        .chart-panel-card { padding: 16px 12px; }
        .chart-panel-header { flex-direction: column; align-items: flex-start; gap: 8px; margin-bottom: 16px; }
        .feed-panel-card { padding: 16px 12px; }
        .chart-canvas-container { max-width: 100%; overflow: hidden; width: 100%; min-width: 0; }
    }
</style>

<!-- ========================================
     HERO GREETING & TELEMETRY BAR
     ======================================== -->
<div class="dash-hero-header animate-up">
    <div>
        <h1 class="dash-greeting-title">
            <span><?php echo $greeting; ?>, <?php echo $adminName; ?></span>
            <span style="font-size: 1.4rem;">👋</span>
        </h1>
        <div class="dash-greeting-subtitle">
            <div class="live-clock-pill">
                <span class="pulse-green"></span>
                <span id="liveClockDisplay"><?php echo date('l, M d, Y &bull; h:i:s A'); ?></span>
            </div>
        </div>
    </div>
</div>

<!-- ─── Flash Feedback Notification ─── -->
<?php if (!empty($_SESSION['dashboard_flash'])): ?>
    <div class="alert animate-up" style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px; padding: 14px 18px; border-radius: 12px; background: <?php echo ($_SESSION['dashboard_flash_type'] ?? '') === 'success' ? 'rgba(5, 150, 105, 0.1)' : 'rgba(225, 29, 72, 0.1)'; ?>; border: 1px solid <?php echo ($_SESSION['dashboard_flash_type'] ?? '') === 'success' ? '#059669' : '#e11d48'; ?>; color: <?php echo ($_SESSION['dashboard_flash_type'] ?? '') === 'success' ? '#059669' : '#e11d48'; ?>; font-weight: 600;">
        <i class="fa-solid <?php echo ($_SESSION['dashboard_flash_type'] ?? '') === 'success' ? 'fa-circle-check' : 'fa-triangle-exclamation'; ?>" style="font-size: 1.15rem;"></i>
        <span><?php echo htmlspecialchars($_SESSION['dashboard_flash']); ?></span>
    </div>
    <?php unset($_SESSION['dashboard_flash'], $_SESSION['dashboard_flash_type']); ?>
<?php endif; ?>

<!-- ─── Employee Panel Index Access Control Banner ─── -->
<div class="dash-portal-status-banner animate-up" style="background: var(--adm-surface); border: 1px solid var(--adm-border); border-radius: var(--adm-radius-lg); padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; margin-bottom: 24px; box-shadow: var(--adm-shadow-sm);">
    <div style="display: flex; align-items: center; gap: 12px;">
        <div style="width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; background: <?php echo $empIndexEnabled ? 'rgba(5, 150, 105, 0.12)' : 'rgba(225, 29, 72, 0.12)'; ?>; color: <?php echo $empIndexEnabled ? 'var(--adm-success)' : 'var(--adm-danger)'; ?>;">
            <i class="fa-solid fa-power-off"></i>
        </div>
        <div>
            <div style="font-weight: 700; font-size: 0.95rem; color: var(--adm-text-main); display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                <span>Employee Panel Index Page:</span>
                <?php if ($empIndexEnabled): ?>
                    <span class="badge-status present" style="font-weight: 700; padding: 2px 10px; border-radius: 9999px; font-size: 0.76rem; display: inline-flex; align-items: center; gap: 4px;">
                        <span class="pulse-green" style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: var(--adm-success);"></span>
                        ON &bull; Live Access
                    </span>
                <?php else: ?>
                    <span class="badge-status absent" style="font-weight: 700; padding: 2px 10px; border-radius: 9999px; font-size: 0.76rem; display: inline-flex; align-items: center; gap: 4px;">
                        <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: var(--adm-danger);"></span>
                        OFF &bull; Offline Notice
                    </span>
                <?php endif; ?>
            </div>
            <div style="font-size: 0.78rem; color: var(--adm-muted); margin-top: 3px;">
                <?php if ($empIndexEnabled): ?>
                    Employees and public visitors can access the index page normally.
                <?php else: ?>
                    Employee access to the index page is blocked with a clean offline notice. Admin Panel is unaffected.
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
        <form method="POST" action="" style="margin: 0; display: inline;">
            <input type="hidden" name="action" value="toggle_employee_index">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfTokenDashToggle); ?>">
            <input type="hidden" name="employee_index_enabled" value="<?php echo $empIndexEnabled ? '0' : '1'; ?>">
            <button type="submit" class="btn-dash-action" style="padding: 8px 16px; font-size: 0.82rem; font-weight: 700; cursor: pointer; border-radius: var(--adm-radius-md); background: <?php echo $empIndexEnabled ? '#e11d48' : '#059669'; ?>; color: #fff; border: none; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-power-off"></i>
                <span><?php echo $empIndexEnabled ? 'Turn OFF' : 'Turn ON'; ?></span>
            </button>
        </form>
        <a href="site_settings" class="btn-dash-action" style="padding: 8px 12px; font-size: 0.82rem; color: var(--adm-text-body); background: var(--adm-surface-subtle); border: 1px solid var(--adm-border); border-radius: var(--adm-radius-md); text-decoration: none; display: inline-flex; align-items: center; gap: 5px;" title="Full Settings Configuration">
            <i class="fa-solid fa-sliders"></i>
            <span>Settings</span>
        </a>
        <a href="/" target="_blank" class="btn-dash-action" style="padding: 8px 12px; font-size: 0.82rem; color: var(--adm-text-body); background: var(--adm-surface-subtle); border: 1px solid var(--adm-border); border-radius: var(--adm-radius-md); text-decoration: none; display: inline-flex; align-items: center; gap: 5px;" title="Test Employee Index Page">
            <i class="fa-solid fa-arrow-up-right-from-square"></i>
            <span>View Site</span>
        </a>
        <?php if (!$empIndexEnabled): ?>
            <a href="/?preview=1" target="_blank" class="btn-dash-action" style="padding: 8px 12px; font-size: 0.82rem; color: var(--adm-text-body); background: var(--adm-surface-subtle); border: 1px solid var(--adm-border); border-radius: var(--adm-radius-md); text-decoration: none; display: inline-flex; align-items: center; gap: 5px;" title="Preview Index Page as Administrator">
                <i class="fa-solid fa-eye"></i>
                <span>Preview</span>
            </a>
        <?php endif; ?>
    </div>
</div>

<?php if ($pendingLeaves > 0 || $pendingAadhar > 0): ?>
    <div class="insights-grid animate-up stagger-1">
        <?php if ($pendingLeaves > 0 && Security::hasPermission('leaves.view', $currentAdminId)): ?>
            <div class="insight-card warning">
                <div class="insight-left">
                    <div class="insight-icon-wrap">
                        <i class="fa-solid fa-calendar-xmark icon-anim-pulse"></i>
                    </div>
                    <div class="insight-text">
                        <strong><?php echo $pendingLeaves; ?> Leave Request<?php echo $pendingLeaves > 1 ? 's' : ''; ?> Pending</strong>
                        <span>Employees awaiting administrative approval.</span>
                    </div>
                </div>
                <a href="leave" class="insight-btn">
                    <span>Review Leaves</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        <?php endif; ?>

        <?php if ($pendingAadhar > 0 && Security::hasPermission('employees.view', $currentAdminId)): ?>
            <div class="insight-card info">
                <div class="insight-left">
                    <div class="insight-icon-wrap">
                        <i class="fa-solid fa-id-card icon-anim-pulse"></i>
                    </div>
                    <div class="insight-text">
                        <strong><?php echo $pendingAadhar; ?> Aadhar Verification<?php echo $pendingAadhar > 1 ? 's' : ''; ?> Pending</strong>
                        <span>Identity KYC document verification required.</span>
                    </div>
                </div>
                <a href="employee" class="insight-btn">
                    <span>Verify KYC</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<!-- ========================================
     PRIMARY KPI STAT CARDS (Exact Employee Directory Component Design)
     ======================================== -->
<div class="emp-kpi-grid cards-grid">
    
    <!-- 1. Total Workforce -->
    <div class="emp-kpi-card kpi-workforce dash-card dash-card--workforce animate-up stagger-1" onclick="window.location.href='employee'" style="cursor: pointer;" title="View all employees">
        <div class="emp-kpi-info dash-card-info">
            <span class="emp-kpi-label dash-card-label">Total Workforce</span>
            <span class="emp-kpi-value dash-card-value"><?php echo number_format($totalEmployees); ?></span>
            <span class="emp-kpi-sub dash-card-sub"><i class="fa-solid fa-users"></i> Registered members</span>
        </div>
        <div class="emp-kpi-icon dash-card-icon dash-card-icon--blue">
            <i class="fa-solid fa-users"></i>
        </div>
    </div>

    <!-- 2. Present Today -->
    <div class="emp-kpi-card kpi-present dash-card dash-card--present animate-up stagger-2" onclick="window.location.href='attendance'" style="cursor: pointer;" title="View present employees today">
        <div class="emp-kpi-info dash-card-info">
            <span class="emp-kpi-label dash-card-label">Present Today</span>
            <span class="emp-kpi-value dash-card-value"><?php echo number_format($presentToday); ?></span>
            <span class="emp-kpi-sub dash-card-sub"><i class="fa-solid fa-user-check"></i> On-site & WFH</span>
        </div>
        <div class="emp-kpi-icon dash-card-icon dash-card-icon--green">
            <i class="fa-solid fa-user-check"></i>
        </div>
    </div>

    <!-- 3. Absent Today -->
    <div class="emp-kpi-card kpi-absent dash-card dash-card--absent animate-up stagger-3" onclick="window.location.href='attendance'" style="cursor: pointer;" title="View absent employees">
        <div class="emp-kpi-info dash-card-info">
            <span class="emp-kpi-label dash-card-label">Absent Today</span>
            <span class="emp-kpi-value dash-card-value"><?php echo number_format($absentToday); ?></span>
            <span class="emp-kpi-sub dash-card-sub"><i class="fa-solid fa-user-xmark"></i> Not checked in</span>
        </div>
        <div class="emp-kpi-icon dash-card-icon dash-card-icon--red">
            <i class="fa-solid fa-user-xmark"></i>
        </div>
    </div>

    <!-- 4. Late Today -->
    <div class="emp-kpi-card kpi-late dash-card dash-card--late animate-up stagger-4" onclick="window.location.href='attendance'" style="cursor: pointer;" title="View delayed arrivals">
        <div class="emp-kpi-info dash-card-info">
            <span class="emp-kpi-label dash-card-label">Late Today</span>
            <span class="emp-kpi-value dash-card-value"><?php echo number_format($lateToday); ?></span>
            <span class="emp-kpi-sub dash-card-sub"><i class="fa-solid fa-clock"></i> Delayed punch-in</span>
        </div>
        <div class="emp-kpi-icon dash-card-icon dash-card-icon--orange">
            <i class="fa-solid fa-clock"></i>
        </div>
    </div>

    <!-- 5. Early Leave Today -->
    <div class="emp-kpi-card kpi-early dash-card dash-card--early animate-up stagger-5" onclick="window.location.href='attendance'" style="cursor: pointer;" title="View early departures">
        <div class="emp-kpi-info dash-card-info">
            <span class="emp-kpi-label dash-card-label">Early Departures</span>
            <span class="emp-kpi-value dash-card-value"><?php echo number_format($earlyLeaveToday); ?></span>
            <span class="emp-kpi-sub dash-card-sub"><i class="fa-solid fa-person-walking-arrow-right"></i> Before shift end</span>
        </div>
        <div class="emp-kpi-icon dash-card-icon dash-card-icon--purple">
            <i class="fa-solid fa-person-walking-arrow-right"></i>
        </div>
    </div>

    <!-- 6. Monthly Attendance Efficiency -->
    <div class="emp-kpi-card kpi-rate dash-card dash-card--rate animate-up stagger-6" onclick="window.location.href='reports'" style="cursor: pointer;" title="View monthly attendance rate">
        <div class="emp-kpi-info dash-card-info">
            <span class="emp-kpi-label dash-card-label">Monthly Efficiency</span>
            <span class="emp-kpi-value dash-card-value"><?php echo $monthlyAttendanceRate; ?>%</span>
            <span class="emp-kpi-sub dash-card-sub"><i class="fa-solid fa-chart-line"></i> Overall rate</span>
        </div>
        <div class="emp-kpi-icon dash-card-icon dash-card-icon--cyan">
            <i class="fa-solid fa-chart-line"></i>
        </div>
    </div>

</div>

<!-- ========================================
     ANALYTICS & VISUAL CHARTS (Dark/Light Adaptive)
     ======================================== -->
<div class="charts-grid-layout animate-up stagger-3">
    
    <!-- 7-Day Attendance Trend Chart -->
    <div class="chart-panel-card">
        <div class="chart-panel-header">
            <div>
                <h2 class="chart-panel-title">
                    <i class="fa-solid fa-chart-column"></i>
                    Attendance Telemetry (Last 7 Days)
                </h2>
                <p class="chart-panel-desc">Daily comparison of on-time attendance versus delayed arrivals.</p>
            </div>
            <span class="dash-card-chip" style="font-size: 0.75rem;">Real-time</span>
        </div>

        <div class="chart-canvas-container">
            <canvas id="attendanceChart"></canvas>
        </div>
    </div>

    <!-- Today's Attendance Composition Doughnut Chart -->
    <div class="chart-panel-card">
        <div class="chart-panel-header">
            <div>
                <h2 class="chart-panel-title">
                    <i class="fa-solid fa-chart-pie"></i>
                    Today's Composition
                </h2>
                <p class="chart-panel-desc">Present, absent, and delayed distribution.</p>
            </div>
        </div>

        <div class="chart-canvas-container">
            <canvas id="todayChart"></canvas>
        </div>
    </div>

</div>

<!-- ========================================
     RECENT LIVE ATTENDANCE ACTIVITY FEED
     ======================================== -->
<div class="feed-panel-card animate-up stagger-4">
    <div class="chart-panel-header">
        <div>
            <h2 class="chart-panel-title">
                <i class="fa-solid fa-clock-rotate-left"></i>
                Live Attendance &amp; Punch Activity
            </h2>
            <p class="chart-panel-desc">Recent punch telemetry across shifts and employee departments.</p>
        </div>
        <a href="attendance" class="btn-dash-action" style="padding: 6px 14px; font-size: 0.82rem;">
            <span>View All Attendance</span>
            <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>

    <?php if (!empty($recentAttendance)): ?>
        <div style="overflow-x: auto;">
            <table class="table-clean">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Date</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentAttendance as $row): 
                        $empName = trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')) ?: 'Employee';
                        $empInitials = strtoupper(substr($row['first_name'] ?? 'E', 0, 1) . substr($row['last_name'] ?? 'M', 0, 1));
                        $checkInDisplay = !empty($row['check_in']) ? date('h:i A', strtotime($row['check_in'])) : '—';
                        $checkOutDisplay = !empty($row['check_out']) ? date('h:i A', strtotime($row['check_out'])) : 'Active';
                        $attStatus = strtolower($row['attendance_status'] ?: ($row['status'] ?: 'present'));
                    ?>
                        <tr>
                            <td>
                                <div class="emp-row-user">
                                    <?php if (!empty($row['avatar'])): ?>
                                        <img src="/assets/uploads/avatars/<?php echo htmlspecialchars($row['avatar']); ?>" alt="" class="emp-row-avatar">
                                    <?php else: ?>
                                        <div class="emp-row-initials"><?php echo $empInitials; ?></div>
                                    <?php endif; ?>
                                    <div>
                                        <span class="emp-row-name"><?php echo htmlspecialchars($empName); ?></span>
                                        <span class="emp-row-code"><?php echo htmlspecialchars($row['emp_code'] ?? 'EMP-' . $row['employee_id']); ?></span>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($row['department'] ?: 'Operations'); ?></td>
                            <td><?php echo date('M d, Y', strtotime($row['date'])); ?></td>
                            <td>
                                <span style="font-weight: 600; color: var(--adm-text-main);">
                                    <i class="fa-solid fa-right-to-bracket" style="color: var(--adm-success); font-size: 0.8rem; margin-right: 4px;"></i>
                                    <?php echo $checkInDisplay; ?>
                                </span>
                            </td>
                            <td>
                                <span style="font-weight: 600;">
                                    <?php if ($checkOutDisplay === 'Active'): ?>
                                        <span class="badge-status present" style="font-size: 0.75rem; padding: 2px 8px;">
                                            <span class="pulse-green" style="width: 6px; height: 6px;"></span> On Duty
                                        </span>
                                    <?php else: ?>
                                        <i class="fa-solid fa-right-from-bracket" style="color: var(--adm-faint); font-size: 0.8rem; margin-right: 4px;"></i>
                                        <?php echo $checkOutDisplay; ?>
                                    <?php endif; ?>
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <span class="badge-status <?php echo in_array($attStatus, ['present', 'completed', 'half_day', 'delayed']) ? $attStatus : 'present'; ?>">
                                    <i class="fa-solid <?php echo ($attStatus === 'delayed') ? 'fa-clock' : 'fa-circle-check'; ?>" style="font-size: 0.75rem;"></i>
                                    <?php echo ucfirst($attStatus); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty-state-box">
            <i class="fa-solid fa-calendar-xmark"></i>
            <h4 style="font-size: 1.1rem; color: var(--adm-text-main); margin-bottom: 4px;">No Attendance Records Recorded Yet</h4>
            <p style="font-size: 0.88rem; color: var(--adm-muted);">Employee punch-in records will stream here in real time as shifts begin.</p>
        </div>
    <?php endif; ?>
</div>

<!-- Chart.js Library (Local + Fallbacks) -->
<script src="/assets/js/chart.umd.min.js"></script>
<script>
if (typeof Chart === 'undefined') {
    document.write('<script src="../assets/js/chart.umd.min.js"><\/script>');
}
if (typeof Chart === 'undefined') {
    document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"><\/script>');
}
</script>

<!-- Dynamic Theme-Adaptive Chart & Live Clock Script -->
<script>
(function() {
    // ── Live Clock Ticker ──
    const clockEl = document.getElementById('liveClockDisplay');
    function updateLiveClock() {
        if (!clockEl) return;
        const now = new Date();
        const options = { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric', 
            hour: '2-digit', 
            minute: '2-digit', 
            second: '2-digit',
            hour12: true 
        };
        clockEl.innerHTML = now.toLocaleDateString('en-US', options);
    }
    updateLiveClock();
    setInterval(updateLiveClock, 1000);

    function initDashboardCharts() {
        // Ensure Chart.js is loaded
        if (typeof Chart === 'undefined') {
            setTimeout(initDashboardCharts, 150);
            return;
        }

        // ── Chart.js Theme Adaptive Helper ──
        const htmlRoot = document.documentElement;

        function getChartThemeColors() {
            const isDark = htmlRoot.getAttribute('data-theme') === 'dark';
            return {
                textColor: isDark ? '#94a3b8' : '#64748b',
                gridColor: isDark ? 'rgba(255, 255, 255, 0.07)' : 'rgba(0, 0, 0, 0.05)',
                barPresent: '#10b981',
                barLate: '#f59e0b',
                donutColors: ['#10b981', '#ef4444', '#f59e0b', '#8b5cf6']
            };
        }

        let colors = getChartThemeColors();

        // ── 1. Attendance Bar Chart (7 Days) ──
        const ctxAtt = document.getElementById('attendanceChart')?.getContext('2d');
        let attendanceChart = null;
        if (ctxAtt) {
            attendanceChart = new Chart(ctxAtt, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($chartDates ?: []); ?>,
                    datasets: [
                        {
                            label: 'Present (On-time)',
                            data: <?php echo json_encode($chartPresent ?: []); ?>,
                            backgroundColor: colors.barPresent,
                            borderRadius: 6,
                            barPercentage: 0.6,
                            categoryPercentage: 0.7
                        },
                        {
                            label: 'Late / Delayed',
                            data: <?php echo json_encode($chartLate ?: []); ?>,
                            backgroundColor: colors.barLate,
                            borderRadius: 6,
                            barPercentage: 0.6,
                            categoryPercentage: 0.7
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 800,
                        easing: 'easeOutQuart'
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                color: colors.textColor,
                                font: { family: "'Plus Jakarta Sans', sans-serif", weight: '600', size: 12 },
                                usePointStyle: true,
                                pointStyle: 'circle',
                                padding: 18
                            }
                        },
                        tooltip: {
                            padding: 12,
                            cornerRadius: 8,
                            titleFont: { family: "'Plus Jakarta Sans', sans-serif", weight: '700' },
                            bodyFont: { family: "'Plus Jakarta Sans', sans-serif" }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: colors.textColor, font: { weight: '600', size: 11 } }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: colors.gridColor },
                            ticks: { 
                                stepSize: 1, 
                                precision: 0,
                                color: colors.textColor,
                                font: { size: 11 }
                            }
                        }
                    }
                }
            });
        }

        // ── 2. Today's Doughnut Chart ──
        const ctxToday = document.getElementById('todayChart')?.getContext('2d');
        let todayChart = null;
        if (ctxToday) {
            const rawTodayData = [
                <?php echo (int)$presentToday; ?>, 
                <?php echo (int)$absentToday; ?>, 
                <?php echo (int)$lateToday; ?>, 
                <?php echo (int)$earlyLeaveToday; ?>
            ];
            const sumToday = rawTodayData.reduce((a, b) => a + b, 0);
            const hasData = sumToday > 0;

            todayChart = new Chart(ctxToday, {
                type: 'doughnut',
                data: {
                    labels: hasData ? ['Present', 'Absent', 'Late', 'Early Exit'] : ['No Attendance Logged Yet'],
                    datasets: [{
                        data: hasData ? rawTodayData : [1],
                        backgroundColor: hasData ? colors.donutColors : [colors.gridColor || 'rgba(148, 163, 184, 0.25)'],
                        borderWidth: hasData ? 2 : 1,
                        borderColor: htmlRoot.getAttribute('data-theme') === 'dark' ? '#1e293b' : '#ffffff',
                        hoverOffset: hasData ? 6 : 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',
                    animation: {
                        animateScale: true,
                        animateRotate: true,
                        duration: 800
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: colors.textColor,
                                font: { family: "'Plus Jakarta Sans', sans-serif", weight: '600', size: 11 },
                                usePointStyle: true,
                                pointStyle: 'circle',
                                padding: 12,
                                boxWidth: 8
                            }
                        },
                        tooltip: {
                            enabled: hasData,
                            padding: 10,
                            cornerRadius: 8,
                            titleFont: { family: "'Plus Jakarta Sans', sans-serif", weight: '700' },
                            callbacks: {
                                label: function(context) {
                                    if (!hasData) return 'No activity recorded today';
                                    const val = context.raw || 0;
                                    const pct = sumToday > 0 ? Math.round((val / sumToday) * 100) : 0;
                                    return ' ' + context.label + ': ' + val + ' (' + pct + '%)';
                                }
                            }
                        }
                    }
                }
            });
        }

        // ── Theme Switch Observer for Real-Time Chart Color Sync ──
        function syncChartThemes() {
            const newColors = getChartThemeColors();
            if (attendanceChart) {
                attendanceChart.options.scales.x.ticks.color = newColors.textColor;
                attendanceChart.options.scales.y.ticks.color = newColors.textColor;
                attendanceChart.options.scales.y.grid.color  = newColors.gridColor;
                attendanceChart.options.plugins.legend.labels.color = newColors.textColor;
                attendanceChart.update();
            }
            if (todayChart) {
                todayChart.options.plugins.legend.labels.color = newColors.textColor;
                todayChart.data.datasets[0].borderColor = htmlRoot.getAttribute('data-theme') === 'dark' ? '#1e293b' : '#ffffff';
                const curSum = [
                    <?php echo (int)$presentToday; ?>, 
                    <?php echo (int)$absentToday; ?>, 
                    <?php echo (int)$lateToday; ?>, 
                    <?php echo (int)$earlyLeaveToday; ?>
                ].reduce((a, b) => a + b, 0);
                if (curSum > 0) {
                    todayChart.data.datasets[0].backgroundColor = newColors.donutColors;
                } else {
                    todayChart.data.datasets[0].backgroundColor = [newColors.gridColor || 'rgba(148, 163, 184, 0.25)'];
                }
                todayChart.update();
            }
        }

        const themeBtn = document.getElementById('adminThemeToggle');
        themeBtn?.addEventListener('click', () => setTimeout(syncChartThemes, 50));

        try {
            const themeObserver = new MutationObserver(syncChartThemes);
            themeObserver.observe(htmlRoot, { attributes: true, attributeFilter: ['data-theme'] });
        } catch (e) {}
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDashboardCharts);
    } else {
        initDashboardCharts();
    }
})();
</script>


<?php include 'footer.php'; ?>
