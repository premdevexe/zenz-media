<?php
// ============================================================================
// Enterprise Leave Management Console
// ============================================================================

// Include database connection and security modules
require_once '../app/db.php';
require_once '../app/security.php';
require_once '../app/LeaveHelper.php';

Security::init($pdo);
Security::initSecureSession();

// Check if admin is logged in
if (!Security::isAuthenticated()) {
    header("Location: ../admin");
    exit();
}

$adminId = (int)($_SESSION['admin_id'] ?? 1);
$canView = Security::hasPermission('leaves.view', $adminId) || Security::hasPermission('leaves.manage', $adminId);
if (!$canView) {
    Security::requirePermission('leaves.view', $adminId, '../admin/dashboard');
}
$canManage = Security::hasPermission('leaves.manage', $adminId);

$pageTitle = 'Leave Management';
$currentPage = 'leave';

// Initialize variables
$message = '';
$messageType = '';

// Handle Actions via POST (CSRF protected & RBAC gated)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!$canManage) {
        $message = "Access Denied: You do not have permission to manage leave requests.";
        $messageType = "error";
    } elseif (!isset($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'], 'leave_action')) {
        $message = "Invalid security token. Please refresh and try again.";
        $messageType = "error";
    } else {
        $action  = $_POST['action'];
        $leaveId = (int)($_POST['id'] ?? 0);
        
        if ($action === 'approve') {
            $res = LeaveHelper::approveLeave($pdo, $leaveId, $adminId);
            $message = $res['message'];
            $messageType = $res['success'] ? 'success' : 'error';
        } elseif ($action === 'reject') {
            $rejectionReason = trim($_POST['rejection_reason'] ?? '');
            $res = LeaveHelper::rejectLeave($pdo, $leaveId, $adminId, $rejectionReason);
            $message = $res['message'];
            $messageType = $res['success'] ? 'success' : 'error';
        } elseif ($action === 'delete') {
            $res = LeaveHelper::deleteLeave($pdo, $leaveId, $adminId);
            $message = $res['message'];
            $messageType = $res['success'] ? 'success' : 'error';
        } elseif ($action === 'delete_code') {
            try {
                $stmt = $pdo->prepare("UPDATE leave_requests SET leave_code = NULL WHERE id = ?");
                $stmt->execute([$leaveId]);
                $message = "Leave code deleted successfully.";
                $messageType = 'success';
            } catch (PDOException $e) {
                error_log("Error deleting leave code: " . $e->getMessage());
                $message = "Error deleting leave code. Please try again.";
                $messageType = 'error';
            }
        } elseif ($action === 'mark_used') {
            try {
                $stmt = $pdo->prepare("SELECT leave_code FROM leave_requests WHERE id = ?");
                $stmt->execute([$leaveId]);
                $leaveCode = $stmt->fetchColumn();
                
                if ($leaveCode) {
                    $histStmt = $pdo->prepare("INSERT INTO leave_code_history (leave_id, leave_code, action, performed_by) VALUES (?, ?, 'used', ?)");
                    $histStmt->execute([$leaveId, $leaveCode, $adminId]);
                    $message = "Leave code marked as used.";
                    $messageType = 'success';
                } else {
                    $message = "No leave code found to mark as used.";
                    $messageType = 'error';
                }
            } catch (PDOException $e) {
                error_log("Error marking code as used: " . $e->getMessage());
                $message = "Error marking leave code as used. Please try again.";
                $messageType = 'error';
            }
        } elseif ($action === 'generate_leave') {
            $employeeId = (int)($_POST['employee_id'] ?? 0);
            $leaveType  = $_POST['leave_type'] ?? 'casual';
            $startDate  = $_POST['start_date'] ?? '';
            $endDate    = $_POST['end_date'] ?? '';
            $reason     = trim($_POST['reason'] ?? '');
            $gender     = trim($_POST['gender'] ?? '');

            $res = LeaveHelper::generateLeave($pdo, $adminId, $employeeId, $leaveType, $startDate, $endDate, $reason, $gender);
            $message = $res['message'];
            $messageType = $res['success'] ? 'success' : 'error';
        }
    }
    
    // Redirect to prevent form resubmission
    header("Location: leave?msg=" . urlencode($message) . "&type=" . $messageType);
    exit();
}

$csrfToken = Security::generateCSRFToken('leave_action');

// Fetch all employees for dropdown select options
try {
    $stmt = $pdo->query("SELECT id, employee_id, first_name, last_name, department, gender, hire_date, status FROM employees ORDER BY first_name ASC");
    $employeesList = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $employeesList = [];
}

if (isset($_GET['msg'])) {
    $message = $_GET['msg'];
    $messageType = $_GET['type'] ?? 'info';
}

// Fetch general stats
$stats = ['total' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0];
try {
    $stmt = $pdo->query("SELECT status, COUNT(*) as count FROM leave_requests GROUP BY status");
    $rawStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rawStats as $row) {
        $stats['total'] += (int)$row['count'];
        if ($row['status'] === 'pending')  $stats['pending']  += (int)$row['count'];
        if ($row['status'] === 'approved') $stats['approved'] += (int)$row['count'];
        if ($row['status'] === 'rejected') $stats['rejected'] += (int)$row['count'];
    }
} catch (PDOException $e) {
    // Fail silently
}

// Fetch all leave requests with employee and approver details
try {
    $sql = "
        SELECT lr.*, e.employee_id as emp_code, e.first_name, e.last_name, e.department, e.position, e.gender, e.avatar,
               ab.first_name as approver_first_name, ab.last_name as approver_last_name
        FROM leave_requests lr
        LEFT JOIN employees e ON lr.employee_id = e.id
        LEFT JOIN employees ab ON lr.approved_by = ab.id
        ORDER BY lr.created_at DESC
    ";
    $stmt = $pdo->query($sql);
    $leaves = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $leaves = [];
    $message = 'Error loading leave requests: ' . $e->getMessage();
    $messageType = 'error';
}

// Fetch all leave code usage history mapped by leave_id
$leaveHistoryMap = [];
try {
    $hStmt = $pdo->query("
        SELECT lch.*, e.first_name, e.last_name 
        FROM leave_code_history lch 
        LEFT JOIN employees e ON lch.performed_by = e.id 
        ORDER BY lch.created_at DESC
    ");
    while ($hRow = $hStmt->fetch(PDO::FETCH_ASSOC)) {
        $leaveHistoryMap[$hRow['leave_id']][] = $hRow;
    }
} catch (PDOException $e) {
    $leaveHistoryMap = [];
}

$leavePoliciesJson = json_encode(LeaveHelper::$leavePolicies);
?>

<?php include 'header.php'; ?>

<!-- Page Header (Exact User Titles Preserved) -->
<div class="page-header">
    <div>
        <h1 class="page-title">Leave Management</h1>
        <p class="page-subtitle">Review, approve, and manage employee leave requests</p>
    </div>
    <?php if ($canManage): ?>
    <button class="btn btn-primary" onclick="openGenerateLeaveModal()">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        Generate Leave
    </button>
    <?php endif; ?>
</div>

<!-- Alert Messages -->
<?php if ($message): ?>
<div class="alert alert-<?php echo htmlspecialchars($messageType); ?>" id="pageAlert">
    <?php if ($messageType === 'success'): ?>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
        </svg>
    <?php else: ?>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
        </svg>
    <?php endif; ?>
    <span><?php echo htmlspecialchars($message); ?></span>
    <button type="button" class="alert-close" onclick="document.getElementById('pageAlert').remove()">&times;</button>
</div>
<?php endif; ?>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card stat-card--total" onclick="filterByStatus('all')" style="cursor: pointer;" title="Show all requests">
        <div class="stat-details">
            <span class="stat-label">Total Requests</span>
            <span class="stat-value"><?php echo $stats['total']; ?></span>
        </div>
        <div class="stat-icon-wrapper total">
            <i class="fa-solid fa-calendar-days"></i>
        </div>
    </div>
    <div class="stat-card stat-card--pending" onclick="filterByStatus('pending')" style="cursor: pointer;" title="Show pending requests">
        <div class="stat-details">
            <span class="stat-label">Pending</span>
            <span class="stat-value"><?php echo $stats['pending']; ?></span>
        </div>
        <div class="stat-icon-wrapper pending">
            <i class="fa-solid fa-clock"></i>
        </div>
    </div>
    <div class="stat-card stat-card--approved" onclick="filterByStatus('approved')" style="cursor: pointer;" title="Show approved requests">
        <div class="stat-details">
            <span class="stat-label">Approved</span>
            <span class="stat-value"><?php echo $stats['approved']; ?></span>
        </div>
        <div class="stat-icon-wrapper active">
            <i class="fa-solid fa-circle-check"></i>
        </div>
    </div>
    <div class="stat-card stat-card--rejected" onclick="filterByStatus('rejected')" style="cursor: pointer;" title="Show rejected requests">
        <div class="stat-details">
            <span class="stat-label">Rejected</span>
            <span class="stat-value"><?php echo $stats['rejected']; ?></span>
        </div>
        <div class="stat-icon-wrapper inactive">
            <i class="fa-solid fa-circle-xmark"></i>
        </div>
    </div>
</div>

<!-- Structured List / Table View -->
<div class="table-card">
    <!-- Search & Filter Controls Toolbar -->
    <div class="table-toolbar">
        <div class="toolbar-search">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input type="text" id="leaveSearchInput" class="form-input search-input" placeholder="Search by employee, department, leave code, reason..." oninput="handleTableFilters()">
            <button type="button" class="clear-search-btn" id="clearSearchBtn" onclick="clearSearch()" style="display:none;" title="Clear search">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="toolbar-filters">
            <div class="filter-group">
                <label class="filter-label">Status:</label>
                <select id="statusFilter" class="form-input filter-select" onchange="handleTableFilters()">
                    <option value="all">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Leave Type:</label>
                <select id="typeFilter" class="form-input filter-select" onchange="handleTableFilters()">
                    <option value="all">All Leave Types</option>
                    <option value="casual">Casual Leave (CL)</option>
                    <option value="sick">Sick Leave (SL)</option>
                    <option value="annual">Annual Leave (AL)</option>
                    <option value="maternity">Maternity Leave (ML)</option>
                    <option value="paternity">Paternity Leave (PL)</option>
                    <option value="early_departure">Early Departure (ED)</option>
                </select>
            </div>

            <button type="button" class="btn btn-secondary btn-sm" onclick="resetFilters()" title="Reset all filters">
                <i class="fa-solid fa-rotate-left"></i> Reset
            </button>
        </div>
    </div>

    <!-- Active Filter Summary -->
    <div class="filter-summary-bar" id="filterSummaryBar">
        <span id="filterCountNotice">Showing <?php echo count($leaves); ?> requests</span>
    </div>

    <!-- Clean List / Table View -->
    <div class="table-responsive">
        <table class="saas-table" id="leavesTable">
            <thead>
                <tr>
                    <th style="min-width: 220px;">Employee</th>
                    <th style="min-width: 150px;">Leave Type</th>
                    <th style="min-width: 170px;">Dates & Duration</th>
                    <th style="min-width: 200px;">Reason & Notes</th>
                    <th style="min-width: 190px;">Status & Leave Code</th>
                    <th style="min-width: 140px; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody id="leavesTableBody">
                <?php if (empty($leaves)): ?>
                <tr class="no-records-row">
                    <td colspan="6">
                        <div class="empty-state" style="border:none; box-shadow:none; padding: 40px 10px; margin: 0;">
                            <div class="empty-state-icon">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>
                            <h3>No leave requests found</h3>
                            <p>When employees submit leave applications, they will appear here in list view.</p>
                        </div>
                    </td>
                </tr>
                <?php else: ?>
                    <?php 
                    $todayDate = date('Y-m-d');
                    foreach ($leaves as $leave): 
                        $fullName = trim($leave['first_name'] . ' ' . $leave['last_name']);
                        $empInitials = strtoupper(substr($leave['first_name'] ?? 'E', 0, 1) . substr($leave['last_name'] ?? 'M', 0, 1)) ?: 'EM';
                        $empCode = !empty($leave['emp_code']) ? $leave['emp_code'] : ('EMP-' . $leave['employee_id']);
                        $dept = $leave['department'] ?: 'General';
                        $gender = strtolower(trim($leave['gender'] ?? ''));
                        
                        $startDateFmt = date('M d, Y', strtotime($leave['start_date']));
                        $endDateFmt   = date('M d, Y', strtotime($leave['end_date']));
                        $isSameDay    = ($leave['start_date'] === $leave['end_date']);
                        $dateRangeStr = $isSameDay ? $startDateFmt : ($startDateFmt . ' &ndash; ' . $endDateFmt);
                        
                        $days = (int)$leave['days'];
                        $typeKey = $leave['leave_type'];
                        $st = $leave['status'];
                        
                        $hasCode = (!empty($leave['leave_code']));
                        $isCodeActive = ($hasCode && strtotime($leave['end_date']) >= strtotime($todayDate));
                        $usageCount = isset($leaveHistoryMap[$leave['id']]) ? count($leaveHistoryMap[$leave['id']]) : 0;
                        
                        // Leave Type styling
                        $typeMeta = LeaveHelper::$leavePolicies[$typeKey] ?? [
                            'code' => strtoupper(substr($typeKey, 0, 2)),
                            'name' => ucfirst(str_replace('_', ' ', $typeKey)),
                            'icon' => 'fa-calendar'
                        ];
                    ?>
                    <tr class="leave-row" 
                        data-id="<?php echo $leave['id']; ?>"
                        data-status="<?php echo htmlspecialchars($st); ?>"
                        data-type="<?php echo htmlspecialchars($typeKey); ?>"
                        data-search="<?php echo htmlspecialchars(strtolower($fullName . ' ' . $empCode . ' ' . $dept . ' ' . $typeMeta['name'] . ' ' . ($leave['leave_code'] ?? '') . ' ' . $leave['reason'])); ?>"
                    >
                        <!-- Employee Column -->
                        <td>
                            <div class="emp-profile-cell">
                                <div class="emp-avatar-box">
                                    <?php if (!empty($leave['avatar'])): ?>
                                        <img src="/assets/uploads/avatars/<?php echo htmlspecialchars($leave['avatar']); ?>" alt="<?php echo htmlspecialchars($fullName); ?>" class="emp-avatar-img">
                                    <?php else: ?>
                                        <div class="emp-avatar-initials <?php echo $gender ? 'avatar-' . $gender : ''; ?>">
                                            <?php echo $empInitials; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="emp-info-wrap">
                                    <div class="emp-name-line">
                                        <strong class="emp-name"><?php echo htmlspecialchars($fullName); ?></strong>
                                    </div>
                                    <div class="emp-sub-line">
                                        <span class="emp-code"><?php echo htmlspecialchars($empCode); ?></span>
                                        <span class="bullet-sep">&bull;</span>
                                        <span class="emp-dept"><?php echo htmlspecialchars($dept); ?></span>
                                    </div>
                                    <div class="emp-gender-line">
                                        <?php if ($gender === 'female'): ?>
                                            <span class="gender-tag female"><i class="fa-solid fa-venus"></i> Female</span>
                                        <?php elseif ($gender === 'male'): ?>
                                            <span class="gender-tag male"><i class="fa-solid fa-mars"></i> Male</span>
                                        <?php elseif ($gender === 'other'): ?>
                                            <span class="gender-tag other"><i class="fa-solid fa-genderless"></i> Other</span>
                                        <?php else: ?>
                                            <span class="gender-tag unset" title="Gender not specified in profile"><i class="fa-regular fa-circle-question"></i> Unset</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Leave Type Column -->
                        <td>
                            <div class="leave-type-pill type-<?php echo htmlspecialchars($typeKey); ?>">
                                <?php if ($typeKey === 'casual'): ?>
                                    <i class="fa-solid fa-mug-hot"></i>
                                <?php elseif ($typeKey === 'sick'): ?>
                                    <i class="fa-solid fa-heart-pulse"></i>
                                <?php elseif ($typeKey === 'annual'): ?>
                                    <i class="fa-solid fa-umbrella-beach"></i>
                                <?php elseif ($typeKey === 'maternity'): ?>
                                    <i class="fa-solid fa-person-breastfeeding"></i>
                                <?php elseif ($typeKey === 'paternity'): ?>
                                    <i class="fa-solid fa-baby"></i>
                                <?php elseif ($typeKey === 'early_departure'): ?>
                                    <i class="fa-solid fa-person-walking-arrow-right"></i>
                                <?php else: ?>
                                    <i class="fa-solid fa-calendar-check"></i>
                                <?php endif; ?>
                                <span class="type-name"><?php echo htmlspecialchars($typeMeta['name']); ?></span>
                                <span class="type-badge"><?php echo htmlspecialchars($typeMeta['code'] ?? 'LV'); ?></span>
                            </div>
                            <?php if ($typeKey === 'early_departure' && !empty($leave['early_departure_time'])): ?>
                                <div class="early-time-note">
                                    <i class="fa-regular fa-clock"></i> Leave at <?php echo date('h:i A', strtotime($leave['early_departure_time'])); ?>
                                </div>
                            <?php endif; ?>
                        </td>

                        <!-- Dates & Duration Column -->
                        <td>
                            <div class="date-range-box">
                                <div class="date-range-text">
                                    <i class="fa-regular fa-calendar-days text-muted"></i>
                                    <span><?php echo $dateRangeStr; ?></span>
                                </div>
                                <div class="duration-badge-box">
                                    <span class="duration-pill"><?php echo $days; ?> <?php echo $days === 1 ? 'Day' : 'Days'; ?></span>
                                </div>
                            </div>
                        </td>

                        <!-- Reason & Details Column -->
                        <td>
                            <div class="reason-preview" title="<?php echo htmlspecialchars($leave['reason']); ?>">
                                <?php 
                                    $reasonText = htmlspecialchars($leave['reason']);
                                    echo strlen($reasonText) > 75 ? substr($reasonText, 0, 75) . '...' : $reasonText;
                                ?>
                            </div>
                            <?php if ($st === 'rejected' && !empty($leave['rejection_reason'])): ?>
                                <div class="rejection-notice-box" title="Rejection Reason: <?php echo htmlspecialchars($leave['rejection_reason']); ?>">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    <span>Declined: <?php echo htmlspecialchars(substr($leave['rejection_reason'], 0, 45) . (strlen($leave['rejection_reason']) > 45 ? '...' : '')); ?></span>
                                </div>
                            <?php endif; ?>
                        </td>

                        <!-- Status & Leave Code Column -->
                        <td>
                            <div class="status-cell-wrap">
                                <span class="badge status-badge status-<?php echo htmlspecialchars($st); ?>">
                                    <?php if ($st === 'approved'): ?>
                                        <i class="fa-solid fa-circle-check"></i> Approved
                                    <?php elseif ($st === 'rejected'): ?>
                                        <i class="fa-solid fa-circle-xmark"></i> Rejected
                                    <?php else: ?>
                                        <i class="fa-solid fa-hourglass-half"></i> Pending Review
                                    <?php endif; ?>
                                </span>

                                <?php if ($st === 'approved' && $hasCode): ?>
                                <div class="leave-code-widget <?php echo $isCodeActive ? 'code-active' : 'code-expired'; ?>">
                                    <div class="code-header-line">
                                        <span class="code-val"><?php echo htmlspecialchars($leave['leave_code']); ?></span>
                                        <span class="code-state-tag <?php echo $isCodeActive ? 'tag-active' : 'tag-expired'; ?>">
                                            <?php echo $isCodeActive ? 'Active' : 'Expired'; ?>
                                        </span>
                                    </div>
                                    <div class="code-quick-actions">
                                        <button type="button" class="btn-code-action mark-used-btn" onclick="submitLeaveAction('mark_used', <?php echo $leave['id']; ?>)" title="Mark leave code as used">
                                            <i class="fa-solid fa-check-double"></i> Mark Used
                                        </button>
                                        <?php if ($usageCount > 0): ?>
                                            <span class="usage-count-tag" title="<?php echo $usageCount; ?> usage logs recorded"><?php echo $usageCount; ?> used</span>
                                        <?php endif; ?>
                                        <button type="button" class="btn-code-action del-code-btn" onclick="if(confirm('Delete leave code <?php echo htmlspecialchars($leave['leave_code']); ?>?')) submitLeaveAction('delete_code', <?php echo $leave['id']; ?>)" title="Remove leave code">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </td>

                        <!-- Actions Column -->
                        <td style="text-align: right;">
                            <div class="table-actions-cluster">
                                <!-- View Details -->
                                <button type="button" class="btn-action-round view" onclick="openViewLeaveModal(<?php echo htmlspecialchars(json_encode($leave)); ?>, <?php echo htmlspecialchars(json_encode($leaveHistoryMap[$leave['id']] ?? [])); ?>)" title="View complete details">
                                    <i class="fa-solid fa-eye"></i>
                                </button>

                                <?php if ($canManage && $st === 'pending'): ?>
                                <!-- Approve -->
                                <button type="button" class="btn-action-round approve" onclick="if(confirm('Approve leave application for <?php echo addslashes($fullName); ?> (<?php echo $days; ?> days)?')) submitLeaveAction('approve', <?php echo $leave['id']; ?>)" title="Approve Request">
                                    <i class="fa-solid fa-check"></i>
                                </button>
                                <!-- Reject -->
                                <button type="button" class="btn-action-round reject" onclick="openRejectLeaveModal(<?php echo $leave['id']; ?>, '<?php echo addslashes($fullName); ?>', '<?php echo addslashes($typeMeta['name']); ?>')" title="Decline Request">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                                <?php endif; ?>

                                <?php if ($canManage): ?>
                                <!-- Delete -->
                                <button type="button" class="btn-action-round delete" onclick="if(confirm('Permanently delete this leave record for <?php echo addslashes($fullName); ?>? This cannot be undone.')) submitLeaveAction('delete', <?php echo $leave['id']; ?>)" title="Delete Record">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Filter Zero-Results State -->
    <div id="noFilterMatch" class="empty-state" style="display:none; padding: 40px 10px; margin: 0; border:none; box-shadow:none;">
        <div class="empty-state-icon">
            <i class="fa-solid fa-filter-circle-xmark" style="font-size: 38px; color: var(--adm-muted);"></i>
        </div>
        <h3>No matching requests</h3>
        <p>No leave requests match your search or filter criteria.</p>
        <button type="button" class="btn btn-secondary btn-sm" onclick="resetFilters()">Reset Filters</button>
    </div>
</div>

<!-- ========================================================================= -->
<!-- Generate Leave Modal (With Gender & Policy Eligibility)                   -->
<!-- ========================================================================= -->
<div class="modal" id="generateLeaveModal">
    <div class="modal-backdrop" onclick="closeModal('generateLeaveModal')"></div>
    <div class="modal-content modal-content-lg">
        <div class="modal-header">
            <div>
                <h2 class="modal-title">Generate Approved Leave</h2>
                <p class="modal-subtitle">Directly issue an approved leave entitlement with attendance roster sync</p>
            </div>
            <button type="button" class="modal-close" onclick="closeModal('generateLeaveModal')">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form method="POST" action="leave" id="generateLeaveForm" onsubmit="return validateGenerateLeaveSubmit()">
            <input type="hidden" name="action" value="generate_leave">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
            
            <div class="modal-body">
                <!-- 1. Employee Selection -->
                <div class="form-group">
                    <label class="form-label" for="gen_employee_id">Select Employee <span class="required">*</span></label>
                    <select name="employee_id" id="gen_employee_id" class="form-input" required onchange="handleGenerateEmployeeChange()">
                        <option value="">-- Choose Employee --</option>
                        <?php foreach ($employeesList as $emp): 
                            $empLabel = trim($emp['first_name'] . ' ' . $emp['last_name']);
                            $empCode  = !empty($emp['employee_id']) ? $emp['employee_id'] : ('EMP-' . $emp['id']);
                            $empDept  = $emp['department'] ?: 'General';
                        ?>
                            <option value="<?php echo $emp['id']; ?>" 
                                    data-name="<?php echo htmlspecialchars($empLabel); ?>"
                                    data-code="<?php echo htmlspecialchars($empCode); ?>"
                                    data-dept="<?php echo htmlspecialchars($empDept); ?>"
                                    data-gender="<?php echo htmlspecialchars($emp['gender'] ?? ''); ?>"
                                    data-hire-date="<?php echo htmlspecialchars($emp['hire_date'] ?? ''); ?>"
                                    data-status="<?php echo htmlspecialchars($emp['status'] ?? 'active'); ?>"
                            >
                                <?php echo htmlspecialchars($empLabel . ' (' . $empCode . ' &bull; ' . $empDept . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- 2. Gender Selection & Policy Guidance -->
                <div class="form-group">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <label class="form-label" for="gen_gender">Employee Gender <span class="required">*</span></label>
                        <span id="gen_gender_badge" class="badge-source">Select an employee</span>
                    </div>
                    <select name="gender" id="gen_gender" class="form-input" required onchange="handleGenerateGenderChange()">
                        <option value="">-- Specify Gender --</option>
                        <option value="female">Female</option>
                        <option value="male">Male</option>
                        <option value="other">Other / Non-Binary</option>
                    </select>
                    <div id="gen_gender_notice" class="field-notice-box">
                        <i class="fa-solid fa-circle-info"></i> Gender is evaluated to determine statutory Maternity and Paternity leave eligibility under organizational policy.
                    </div>
                </div>

                <!-- 3. Leave Type Selection (Filtered dynamically based on Gender & Policy) -->
                <div class="form-group">
                    <label class="form-label" for="gen_leave_type">Leave Classification <span class="required">*</span></label>
                    <select name="leave_type" id="gen_leave_type" class="form-input" required onchange="handleGenerateTypeChange()">
                        <option value="casual">Casual Leave (CL) &bull; Urgent personal time off</option>
                        <option value="sick">Sick Leave (SL) &bull; Medical recovery or consultations</option>
                        <option value="annual">Annual Leave (AL) &bull; Planned vacations & prolonged rest</option>
                        <option value="maternity" id="opt_maternity">Maternity Leave (ML) &bull; Eligible female employees</option>
                        <option value="paternity" id="opt_paternity">Paternity Leave (PL) &bull; Eligible male employees</option>
                        <option value="early_departure">Early Departure (ED) &bull; Leaving shift early</option>
                    </select>
                    
                    <!-- Dynamic Policy Overview Card -->
                    <div id="gen_policy_card" class="policy-card-info">
                        <div class="policy-card-header">
                            <span class="policy-pill" id="polPill">Casual Leave (CL)</span>
                            <span class="policy-allowance" id="polAllowance">Annual Allowance: 12 Days</span>
                        </div>
                        <p class="policy-desc" id="polDesc">Short personal time off or urgent family commitments.</p>
                        <div class="policy-rule" id="polRule">Eligibility: Available to all active staff members.</div>
                    </div>
                </div>

                <!-- 4. Date Range -->
                <div class="form-row-2">
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label" for="gen_start_date">Start Date <span class="required">*</span></label>
                        <input type="date" name="start_date" id="gen_start_date" class="form-input" required onchange="calculateGeneratedDays()" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label" for="gen_end_date">End Date <span class="required">*</span></label>
                        <input type="date" name="end_date" id="gen_end_date" class="form-input" required onchange="calculateGeneratedDays()" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>
                <div id="gen_calc_days_badge" class="calc-days-pill">
                    <i class="fa-solid fa-clock-rotate-left"></i> Duration: <strong id="gen_calc_days_val">1 Day</strong>
                </div>

                <!-- 5. Reason -->
                <div class="form-group">
                    <label class="form-label" for="gen_reason">Administrative Justification / Reason <span class="required">*</span></label>
                    <textarea name="reason" id="gen_reason" class="form-input" rows="3" required placeholder="Specify the approved reason or reference for generating this leave record..."></textarea>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('generateLeaveModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-check"></i> Generate &amp; Approve
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- View Leave Details Modal                                                  -->
<!-- ========================================================================= -->
<div class="modal" id="viewLeaveModal">
    <div class="modal-backdrop" onclick="closeModal('viewLeaveModal')"></div>
    <div class="modal-content modal-content-lg">
        <div class="modal-header">
            <div>
                <h2 class="modal-title">Leave Application Details</h2>
                <p class="modal-subtitle" id="viewModalSubtitle">Detailed review of leave request and audit history</p>
            </div>
            <button type="button" class="modal-close" onclick="closeModal('viewLeaveModal')">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body" id="viewModalBody">
            <!-- Populated dynamically via openViewLeaveModal() -->
        </div>
        <div class="modal-footer" id="viewModalFooter">
            <button type="button" class="btn btn-secondary" onclick="closeModal('viewLeaveModal')">Close</button>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- Reject Leave Modal                                                        -->
<!-- ========================================================================= -->
<div class="modal" id="rejectLeaveModal">
    <div class="modal-backdrop" onclick="closeModal('rejectLeaveModal')"></div>
    <div class="modal-content">
        <div class="modal-header">
            <div>
                <h2 class="modal-title" style="color: var(--adm-danger, #e11d48);">Decline Leave Request</h2>
                <p class="modal-subtitle" id="rejectModalEmpName">Specify the reason for declining this request</p>
            </div>
            <button type="button" class="modal-close" onclick="closeModal('rejectLeaveModal')">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form method="POST" action="leave" id="rejectLeaveForm">
            <input type="hidden" name="action" value="reject">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
            <input type="hidden" name="id" id="reject_leave_id" value="">
            
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label" for="reject_reason_text">Reason for Decline / Remarks <span class="required">*</span></label>
                    <textarea name="rejection_reason" id="reject_reason_text" class="form-input" rows="4" required placeholder="Please explain why this leave request is declined (will be visible to the employee)..."></textarea>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('rejectLeaveModal')">Cancel</button>
                <button type="submit" class="btn btn-danger" style="background:#e11d48; color:white; border:none; padding:8px 16px; border-radius:6px; cursor:pointer;">
                    <i class="fa-solid fa-ban"></i> Confirm Decline
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- Unified Styles                                                            -->
<!-- ========================================================================= -->
<style>
/* Stats Grid */
.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 16px; margin-bottom: 24px; }
.stat-card {
    background: var(--adm-surface, #fff);
    border: 1px solid var(--adm-border, #e2e8f0);
    border-radius: var(--adm-radius-lg, 14px);
    padding: 20px;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    position: relative;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    transition: all 0.25s ease;
}
.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    border-color: var(--adm-brand-border, #cbd5e1);
}
.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 3px;
    background: transparent;
    transition: background 0.25s ease;
}
.stat-card--total::before    { background: linear-gradient(90deg, #4f46e5, #7c3aed); }
.stat-card--pending::before  { background: linear-gradient(90deg, #d97706, #f59e0b); }
.stat-card--approved::before { background: linear-gradient(90deg, #059669, #10b981); }
.stat-card--rejected::before { background: linear-gradient(90deg, #e11d48, #f43f5e); }
.stat-details { display: flex; flex-direction: column; gap: 4px; min-width: 0; }
.stat-label { margin: 0; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: var(--adm-muted, #64748b); }
.stat-value { margin: 0; font-size: 1.75rem; font-weight: 800; line-height: 1.1; color: var(--adm-text-main, #0f172a); }
.stat-icon-wrapper { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 1.25rem; }
.stat-icon-wrapper.total    { background: rgba(79, 70, 229, 0.12); color: #4f46e5; }
.stat-icon-wrapper.pending  { background: rgba(217, 119, 6, 0.12);  color: #d97706; }
.stat-icon-wrapper.active   { background: rgba(5, 150, 105, 0.12); color: #059669; }
.stat-icon-wrapper.inactive { background: rgba(225, 29, 72, 0.12); color: #e11d48; }

/* Table Container & Card */
.table-card {
    background: var(--adm-surface, #ffffff);
    border: 1px solid var(--adm-border, #e2e8f0);
    border-radius: var(--adm-radius-xl, 16px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.03);
    overflow: hidden;
    margin-bottom: 32px;
}
.table-toolbar {
    padding: 16px 20px;
    background: var(--adm-surface-subtle, #f8fafc);
    border-bottom: 1px solid var(--adm-border, #e2e8f0);
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
}
.toolbar-search {
    position: relative;
    flex: 1;
    min-width: 240px;
    max-width: 420px;
}
.search-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--adm-muted, #94a3b8);
    font-size: 14px;
    pointer-events: none;
}
.search-input {
    width: 100%;
    padding: 9px 36px 9px 38px;
    border: 1px solid var(--adm-border, #cbd5e1);
    border-radius: 8px;
    background: var(--adm-surface, #ffffff);
    font-size: 13.5px;
    color: var(--adm-text-main, #0f172a);
    transition: all 0.2s ease;
}
.search-input:focus {
    border-color: #4f46e5;
    outline: none;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
}
.clear-search-btn {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: transparent;
    border: none;
    color: var(--adm-muted, #94a3b8);
    cursor: pointer;
    padding: 4px;
    font-size: 13px;
}
.clear-search-btn:hover { color: #0f172a; }
.toolbar-filters {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 12px;
}
.filter-group {
    display: flex;
    align-items: center;
    gap: 6px;
}
.filter-label {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--adm-muted, #64748b);
}
.filter-select {
    padding: 8px 12px;
    border: 1px solid var(--adm-border, #cbd5e1);
    border-radius: 8px;
    background: var(--adm-surface, #ffffff);
    font-size: 13px;
    color: var(--adm-text-main, #0f172a);
    cursor: pointer;
}
.filter-select:focus {
    border-color: #4f46e5;
    outline: none;
}
.filter-summary-bar {
    padding: 8px 20px;
    background: #f1f5f9;
    border-bottom: 1px solid var(--adm-border, #e2e8f0);
    font-size: 12px;
    font-weight: 600;
    color: var(--adm-muted, #64748b);
}

/* SaaS Table Styling */
.table-responsive {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}
.saas-table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
    font-size: 13.5px;
}
.saas-table th {
    background: var(--adm-surface-subtle, #f8fafc);
    padding: 13px 18px;
    font-size: 0.76rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--adm-muted, #64748b);
    border-bottom: 1px solid var(--adm-border, #e2e8f0);
    white-space: nowrap;
}
.saas-table td {
    padding: 14px 18px;
    border-bottom: 1px solid var(--adm-border-subtle, #f1f5f9);
    vertical-align: middle;
    color: var(--adm-text-main, #0f172a);
}
.saas-table tr:hover td {
    background: rgba(248, 250, 252, 0.85);
}

/* Employee Profile Cell */
.emp-profile-cell { display: flex; align-items: center; gap: 12px; min-width: 0; }
.emp-avatar-box { flex-shrink: 0; }
.emp-avatar-img { width: 38px; height: 38px; border-radius: 50%; object-fit: cover; border: 1px solid #e2e8f0; }
.emp-avatar-initials {
    width: 38px; height: 38px; border-radius: 50%;
    background: linear-gradient(135deg, #4f46e5, #6366f1);
    color: #fff; display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 800; letter-spacing: 0.5px;
}
.emp-avatar-initials.avatar-female { background: linear-gradient(135deg, #ec4899, #f43f5e); }
.emp-avatar-initials.avatar-male   { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.emp-avatar-initials.avatar-other  { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }

.emp-info-wrap { display: flex; flex-direction: column; gap: 2px; min-width: 0; }
.emp-name { font-size: 14px; font-weight: 700; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.emp-sub-line { font-size: 11.5px; color: #64748b; display: flex; align-items: center; gap: 6px; }
.emp-code { font-family: monospace; font-weight: 600; color: #475569; }
.bullet-sep { opacity: 0.4; }
.emp-gender-line { margin-top: 2px; }
.gender-tag {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    padding: 1px 6px;
    border-radius: 4px;
}
.gender-tag.female { background: rgba(236, 72, 153, 0.12); color: #be185d; }
.gender-tag.male   { background: rgba(59, 130, 246, 0.12); color: #1d4ed8; }
.gender-tag.other  { background: rgba(139, 92, 246, 0.12); color: #6d28d9; }
.gender-tag.unset  { background: rgba(148, 163, 184, 0.15); color: #64748b; font-style: italic; }

/* Leave Type Pill */
.leave-type-pill {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 5px 10px;
    border-radius: 8px;
    font-size: 12.5px;
    font-weight: 700;
    white-space: nowrap;
}
.leave-type-pill.type-casual          { background: rgba(2, 132, 199, 0.1); color: #0284c7; border: 1px solid rgba(2, 132, 199, 0.25); }
.leave-type-pill.type-sick            { background: rgba(225, 29, 72, 0.1); color: #e11d48; border: 1px solid rgba(225, 29, 72, 0.25); }
.leave-type-pill.type-annual          { background: rgba(5, 150, 105, 0.1); color: #059669; border: 1px solid rgba(5, 150, 105, 0.25); }
.leave-type-pill.type-maternity       { background: rgba(219, 39, 119, 0.1); color: #db2777; border: 1px solid rgba(219, 39, 119, 0.25); }
.leave-type-pill.type-paternity       { background: rgba(79, 70, 229, 0.1); color: #4f46e5; border: 1px solid rgba(79, 70, 229, 0.25); }
.leave-type-pill.type-early_departure { background: rgba(217, 119, 6, 0.1); color: #d97706; border: 1px solid rgba(217, 119, 6, 0.25); }

.type-badge {
    font-size: 10px;
    font-weight: 800;
    padding: 1px 5px;
    border-radius: 4px;
    background: rgba(0,0,0,0.06);
}
.early-time-note { font-size: 11px; color: #d97706; margin-top: 3px; font-weight: 600; }

/* Date Range Box */
.date-range-box { display: flex; flex-direction: column; gap: 4px; }
.date-range-text { font-size: 13px; font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 6px; white-space: nowrap; }
.duration-pill {
    display: inline-block;
    padding: 2px 7px;
    border-radius: 6px;
    background: #f1f5f9;
    color: #475569;
    font-size: 11px;
    font-weight: 700;
}

/* Reason Preview */
.reason-preview {
    font-size: 13px;
    color: #334155;
    line-height: 1.4;
    max-width: 260px;
    word-break: break-word;
}
.rejection-notice-box {
    margin-top: 6px;
    padding: 4px 8px;
    background: rgba(239, 68, 68, 0.08);
    border-left: 2px solid #ef4444;
    border-radius: 4px;
    font-size: 11.5px;
    color: #b91c1c;
    display: flex;
    align-items: center;
    gap: 6px;
}

/* Status & Code Cell */
.status-cell-wrap { display: flex; flex-direction: column; gap: 6px; min-width: 170px; }
.badge { padding: 4px 10px; border-radius: 9999px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px; width: fit-content; }
.status-badge.status-pending  { background: rgba(217, 119, 6, 0.12); color: #d97706; }
.status-badge.status-approved { background: rgba(5, 150, 105, 0.12); color: #059669; }
.status-badge.status-rejected { background: rgba(225, 29, 72, 0.12); color: #e11d48; }

.leave-code-widget {
    padding: 7px 10px;
    border-radius: 8px;
    border: 1px dashed;
    display: flex;
    flex-direction: column;
    gap: 5px;
}
.leave-code-widget.code-active {
    background: rgba(16, 185, 129, 0.05);
    border-color: rgba(16, 185, 129, 0.35);
}
.leave-code-widget.code-expired {
    background: rgba(100, 116, 139, 0.05);
    border-color: rgba(100, 116, 139, 0.3);
}
.code-header-line { display: flex; align-items: center; justify-content: space-between; gap: 6px; }
.code-val { font-family: monospace; font-size: 13.5px; font-weight: 800; letter-spacing: 0.5px; color: #0f172a; }
.code-state-tag { font-size: 9.5px; font-weight: 800; text-transform: uppercase; padding: 1px 5px; border-radius: 4px; }
.code-state-tag.tag-active  { background: #10b981; color: #fff; }
.code-state-tag.tag-expired { background: #94a3b8; color: #fff; }

.code-quick-actions { display: flex; align-items: center; gap: 6px; }
.btn-code-action {
    background: #fff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 3px 7px;
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
    color: #334155;
    transition: all 0.15s ease;
}
.btn-code-action:hover { background: #f8fafc; border-color: #94a3b8; color: #0f172a; }
.btn-code-action.mark-used-btn:hover { background: rgba(79, 70, 229, 0.08); border-color: #4f46e5; color: #4f46e5; }
.btn-code-action.del-code-btn:hover  { background: rgba(239, 68, 68, 0.08); border-color: #ef4444; color: #ef4444; }
.usage-count-tag { font-size: 10px; font-weight: 700; background: #e2e8f0; color: #475569; padding: 2px 5px; border-radius: 4px; }

/* Table Action Buttons */
.table-actions-cluster { display: inline-flex; align-items: center; gap: 6px; }
.btn-action-round {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    border: 1px solid transparent;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.18s ease;
    font-size: 13px;
    background: transparent;
}
.btn-action-round.view { background: #f1f5f9; color: #475569; border-color: #e2e8f0; }
.btn-action-round.view:hover { background: #4f46e5; color: #fff; border-color: #4f46e5; transform: translateY(-1px); }

.btn-action-round.approve { background: rgba(16, 185, 129, 0.12); color: #059669; border-color: rgba(16, 185, 129, 0.25); }
.btn-action-round.approve:hover { background: #059669; color: #fff; border-color: #059669; transform: translateY(-1px); }

.btn-action-round.reject { background: rgba(225, 29, 72, 0.12); color: #e11d48; border-color: rgba(225, 29, 72, 0.25); }
.btn-action-round.reject:hover { background: #e11d48; color: #fff; border-color: #e11d48; transform: translateY(-1px); }

.btn-action-round.delete { background: #f8fafc; color: #94a3b8; border-color: #e2e8f0; }
.btn-action-round.delete:hover { background: rgba(239, 68, 68, 0.1); color: #ef4444; border-color: #fca5a5; transform: translateY(-1px); }

/* Modal Styling */
.modal { display: none; position: fixed; inset: 0; z-index: 1000; align-items: center; justify-content: center; padding: 24px; }
.modal.show { display: flex; }
.modal-backdrop { position: absolute; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.45); backdrop-filter: blur(4px); }
.modal-content {
    background: var(--adm-surface, #ffffff);
    border: 1px solid var(--adm-border, #cbd5e1);
    border-radius: 16px;
    width: 100%;
    max-width: 520px;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
    z-index: 1001;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}
.modal-content-lg { max-width: 650px; }
.modal-header {
    padding: 20px 24px;
    border-bottom: 1px solid var(--adm-border, #e2e8f0);
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}
.modal-title { margin: 0; font-size: 19px; font-weight: 800; color: #0f172a; }
.modal-subtitle { margin: 3px 0 0 0; font-size: 13px; color: #64748b; }
.modal-close {
    background: transparent;
    border: none;
    color: #64748b;
    cursor: pointer;
    padding: 6px;
    border-radius: 8px;
    font-size: 16px;
    transition: all 0.15s ease;
}
.modal-close:hover { background: #f1f5f9; color: #0f172a; }
.modal-body { padding: 22px 24px; display: flex; flex-direction: column; gap: 16px; }
.modal-footer {
    padding: 18px 24px;
    border-top: 1px solid var(--adm-border, #e2e8f0);
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    background: var(--adm-surface-subtle, #f8fafc);
}
.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.form-label { font-size: 13.5px; font-weight: 700; color: #334155; }
.required { color: #ef4444; }
.form-input {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid var(--adm-border, #cbd5e1);
    border-radius: 8px;
    background: #fff;
    color: #0f172a;
    font-family: inherit;
    font-size: 13.5px;
    transition: all 0.2s ease;
}
.form-input:focus {
    border-color: #4f46e5;
    outline: none;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
}

/* Helper Notices inside Generate Modal */
.badge-source { font-size: 11px; font-weight: 700; color: #64748b; }
.field-notice-box {
    margin-top: 4px;
    font-size: 12px;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 6px;
}
.field-notice-box.alert-warn {
    color: #b45309;
    background: rgba(245, 158, 11, 0.08);
    padding: 6px 10px;
    border-radius: 6px;
    border: 1px solid rgba(245, 158, 11, 0.2);
}
.field-notice-box.alert-success {
    color: #047857;
    background: rgba(16, 185, 129, 0.08);
    padding: 6px 10px;
    border-radius: 6px;
    border: 1px solid rgba(16, 185, 129, 0.2);
}

.policy-card-info {
    margin-top: 8px;
    padding: 12px 14px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.policy-card-header { display: flex; align-items: center; justify-content: space-between; gap: 8px; }
.policy-pill { font-size: 12px; font-weight: 800; color: #4f46e5; text-transform: uppercase; }
.policy-allowance { font-size: 11.5px; font-weight: 700; color: #475569; }
.policy-desc { margin: 2px 0 0 0; font-size: 12px; color: #64748b; line-height: 1.4; }
.policy-rule { font-size: 11.5px; color: #334155; font-weight: 600; margin-top: 2px; }

.calc-days-pill {
    padding: 6px 12px;
    background: #eef2ff;
    color: #4338ca;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    width: fit-content;
}

/* Alert Styling */
.alert {
    padding: 14px 18px;
    border-radius: 10px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 14px;
    position: relative;
}
.alert-success { background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; }
.alert-error   { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
.alert-info    { background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; }
.alert-close {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    background: transparent;
    border: none;
    font-size: 18px;
    cursor: pointer;
    color: inherit;
    opacity: 0.6;
}
.alert-close:hover { opacity: 1; }

/* Responsive Adjustments */
@media (max-width: 768px) {
    .stats-grid { grid-template-columns: 1fr 1fr; gap: 12px; }
    .table-toolbar { flex-direction: column; align-items: stretch; }
    .toolbar-search { max-width: 100%; min-width: 100%; }
    .toolbar-filters { justify-content: flex-start; }
    .form-row-2 { grid-template-columns: 1fr; gap: 12px; }
    .modal { padding: 12px; }
}
@media (max-width: 480px) {
    .stats-grid { grid-template-columns: 1fr; }
}
</style>

<!-- ========================================================================= -->
<!-- Frontend Scripts: Dynamic Leave Eligibility, Filters & Modals             -->
<!-- ========================================================================= -->
<script>
const LEAVE_POLICIES = <?php echo $leavePoliciesJson ?: '{}'; ?>;

// ── Open / Close Modals ──
function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = '';
    }
}

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        ['generateLeaveModal', 'viewLeaveModal', 'rejectLeaveModal'].forEach(id => closeModal(id));
    }
});

// ── Generate Leave Modal Lifecycle ──
function openGenerateLeaveModal() {
    const selEmp = document.getElementById('gen_employee_id');
    if (selEmp) {
        selEmp.selectedIndex = 0;
    }
    const selGender = document.getElementById('gen_gender');
    if (selGender) {
        selGender.value = '';
    }
    handleGenerateEmployeeChange();
    calculateGeneratedDays();
    openModal('generateLeaveModal');
}

function handleGenerateEmployeeChange() {
    const empSelect = document.getElementById('gen_employee_id');
    const genderSelect = document.getElementById('gen_gender');
    const badgeEl = document.getElementById('gen_gender_badge');
    const noticeEl = document.getElementById('gen_gender_notice');

    if (!empSelect || !genderSelect) return;

    const opt = empSelect.options[empSelect.selectedIndex];
    if (!opt || !opt.value) {
        genderSelect.value = '';
        if (badgeEl) badgeEl.textContent = 'Select an employee';
        if (noticeEl) {
            noticeEl.className = 'field-notice-box';
            noticeEl.innerHTML = '<i class="fa-solid fa-circle-info"></i> Select an employee to inspect organizational policy eligibility.';
        }
        filterGenerateLeaveTypeOptions('');
        return;
    }

    const empGender = (opt.dataset.gender || '').toLowerCase().trim();
    if (empGender && ['male', 'female', 'other'].includes(empGender)) {
        genderSelect.value = empGender;
        if (badgeEl) badgeEl.innerHTML = '<span style="color:#059669; font-weight:700;"><i class="fa-solid fa-circle-check"></i> Profile Record</span>';
        if (noticeEl) {
            noticeEl.className = 'field-notice-box alert-success';
            noticeEl.innerHTML = '<i class="fa-solid fa-check"></i> Employee gender detected from profile (' + capitalize(empGender) + '). Policy rules active.';
        }
    } else {
        genderSelect.value = '';
        if (badgeEl) badgeEl.innerHTML = '<span style="color:#d97706; font-weight:700;"><i class="fa-solid fa-triangle-exclamation"></i> Action Required</span>';
        if (noticeEl) {
            noticeEl.className = 'field-notice-box alert-warn';
            noticeEl.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Gender not set in employee profile. Please select gender to ensure accurate Maternity/Paternity policy enforcement.';
        }
    }

    filterGenerateLeaveTypeOptions(genderSelect.value);
    calculateGeneratedDays();
}

function handleGenerateGenderChange() {
    const genderSelect = document.getElementById('gen_gender');
    const badgeEl = document.getElementById('gen_gender_badge');
    const noticeEl = document.getElementById('gen_gender_notice');

    const gVal = (genderSelect.value || '').toLowerCase().trim();
    if (gVal) {
        if (badgeEl) badgeEl.innerHTML = '<span style="color:#4f46e5; font-weight:700;"><i class="fa-solid fa-pen-to-square"></i> Admin Specified</span>';
        if (noticeEl) {
            noticeEl.className = 'field-notice-box alert-success';
            noticeEl.innerHTML = '<i class="fa-solid fa-circle-check"></i> Policy rules evaluated for ' + capitalize(gVal) + ' gender.';
        }
    } else {
        if (badgeEl) badgeEl.textContent = 'Required';
        if (noticeEl) {
            noticeEl.className = 'field-notice-box alert-warn';
            noticeEl.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Please select employee gender to verify leave eligibility.';
        }
    }

    filterGenerateLeaveTypeOptions(gVal);
}

function filterGenerateLeaveTypeOptions(effectiveGender) {
    const typeSelect = document.getElementById('gen_leave_type');
    const optMat = document.getElementById('opt_maternity');
    const optPat = document.getElementById('opt_paternity');

    if (!typeSelect) return;

    const currentVal = typeSelect.value;

    // Maternity Policy Rule: Female Only
    if (optMat) {
        if (effectiveGender === 'female') {
            optMat.disabled = false;
            optMat.textContent = 'Maternity Leave (ML) • 180 Days (Eligible Female Employees)';
        } else if (effectiveGender === 'male') {
            optMat.disabled = true;
            optMat.textContent = 'Maternity Leave (ML) • Ineligible (Female employees only)';
            if (currentVal === 'maternity') typeSelect.value = 'casual';
        } else {
            optMat.disabled = true;
            optMat.textContent = 'Maternity Leave (ML) • Ineligible (Specify Female Gender)';
            if (currentVal === 'maternity') typeSelect.value = 'casual';
        }
    }

    // Paternity Policy Rule: Male Only
    if (optPat) {
        if (effectiveGender === 'male') {
            optPat.disabled = false;
            optPat.textContent = 'Paternity Leave (PL) • 15 Days (Eligible Male Employees)';
        } else if (effectiveGender === 'female') {
            optPat.disabled = true;
            optPat.textContent = 'Paternity Leave (PL) • Ineligible (Male employees only)';
            if (currentVal === 'paternity') typeSelect.value = 'casual';
        } else {
            optPat.disabled = true;
            optPat.textContent = 'Paternity Leave (PL) • Ineligible (Specify Male Gender)';
            if (currentVal === 'paternity') typeSelect.value = 'casual';
        }
    }

    updatePolicyCard(typeSelect.value);
}

function handleGenerateTypeChange() {
    const typeSelect = document.getElementById('gen_leave_type');
    if (typeSelect) {
        updatePolicyCard(typeSelect.value);
    }
}

function updatePolicyCard(leaveType) {
    const pol = LEAVE_POLICIES[leaveType] || {
        name: 'Leave',
        code: 'LV',
        allowance: 0,
        description: 'General organizational leave',
        gender: 'all'
    };

    const pillEl      = document.getElementById('polPill');
    const allowanceEl = document.getElementById('polAllowance');
    const descEl      = document.getElementById('polDesc');
    const ruleEl      = document.getElementById('polRule');

    if (pillEl)      pillEl.textContent = pol.name + ' (' + pol.code + ')';
    if (allowanceEl) allowanceEl.textContent = 'Annual Allowance: ' + pol.allowance + ' Days';
    if (descEl)      descEl.textContent = pol.description;
    
    if (ruleEl) {
        if (pol.gender === 'female') {
            ruleEl.innerHTML = '<span style="color:#db2777; font-weight:700;"><i class="fa-solid fa-venus"></i> Statutory Female Policy:</span> Minimum 80 days of active service required.';
        } else if (pol.gender === 'male') {
            ruleEl.innerHTML = '<span style="color:#4f46e5; font-weight:700;"><i class="fa-solid fa-mars"></i> Statutory Male Policy:</span> Granted upon childbirth or adoption.';
        } else {
            ruleEl.innerHTML = 'Eligibility: Available to all active staff members.';
        }
    }
}

function calculateGeneratedDays() {
    const startInput = document.getElementById('gen_start_date');
    const endInput   = document.getElementById('gen_end_date');
    const badgeEl    = document.getElementById('gen_calc_days_val');

    if (!startInput || !endInput || !badgeEl) return;

    if (startInput.value && endInput.value) {
        const d1 = new Date(startInput.value);
        const d2 = new Date(endInput.value);
        if (d2 >= d1) {
            const diffTime = Math.abs(d2 - d1);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
            badgeEl.textContent = diffDays + (diffDays === 1 ? ' Day' : ' Days');
            return;
        }
    }
    badgeEl.textContent = 'Invalid Range';
}

function validateGenerateLeaveSubmit() {
    const empSelect = document.getElementById('gen_employee_id');
    const genderSelect = document.getElementById('gen_gender');
    const typeSelect = document.getElementById('gen_leave_type');
    const startInput = document.getElementById('gen_start_date');
    const endInput   = document.getElementById('gen_end_date');

    if (!empSelect.value) {
        alert('Please select an employee.');
        empSelect.focus();
        return false;
    }

    const genderVal = (genderSelect.value || '').toLowerCase().trim();
    if (!genderVal) {
        alert('Please specify the employee gender to verify leave policy eligibility.');
        genderSelect.focus();
        return false;
    }

    const leaveType = typeSelect.value;
    if (leaveType === 'maternity' && genderVal !== 'female') {
        alert('Policy Ineligibility: Maternity Leave is available exclusively to female employees under organization policy.');
        return false;
    }
    if (leaveType === 'paternity' && genderVal !== 'male') {
        alert('Policy Ineligibility: Paternity Leave is available exclusively to male employees under organization policy.');
        return false;
    }

    if (startInput.value && endInput.value) {
        if (new Date(endInput.value) < new Date(startInput.value)) {
            alert('End date cannot be prior to start date.');
            endInput.focus();
            return false;
        }
    }

    return true;
}

// ── View Leave Details Modal ──
function openViewLeaveModal(leave, historyLogs) {
    const body = document.getElementById('viewModalBody');
    const footer = document.getElementById('viewModalFooter');
    if (!body) return;

    const fullName = (leave.first_name || '') + ' ' + (leave.last_name || '');
    const empCode  = leave.emp_code || ('EMP-' + leave.employee_id);
    const dept     = leave.department || 'General';
    const pos      = leave.position || 'Employee';
    const gender   = (leave.gender || 'Not Specified').toUpperCase();
    const typeKey  = leave.leave_type;
    const pol      = LEAVE_POLICIES[typeKey] || { name: capitalize(typeKey.replace('_', ' ')), code: 'LV' };

    const startDateFmt = new Date(leave.start_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    const endDateFmt   = new Date(leave.end_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    const dateRangeStr = (leave.start_date === leave.end_date) ? startDateFmt : (startDateFmt + ' — ' + endDateFmt);

    let historyHtml = '<div style="font-size: 12px; color: #64748b; font-style: italic;">No audit usage history recorded yet.</div>';
    if (historyLogs && historyLogs.length > 0) {
        historyHtml = '<div style="display:flex; flex-direction:column; gap:6px; max-height:120px; overflow-y:auto;">';
        historyLogs.forEach(h => {
            const hDate = new Date(h.created_at).toLocaleString();
            const hBy   = (h.first_name ? (h.first_name + ' ' + h.last_name) : 'Administrator');
            historyHtml += `
                <div style="font-size: 12px; padding: 6px 10px; background: #f8fafc; border-radius: 6px; border-left: 3px solid #4f46e5;">
                    <strong>Marked as ${h.action}</strong> on ${hDate} by ${hBy}
                </div>`;
        });
        historyHtml += '</div>';
    }

    let codeSection = '';
    if (leave.leave_code) {
        const isPast = (new Date(leave.end_date) < new Date());
        const tag = isPast ? '<span class="code-state-tag tag-expired">Expired</span>' : '<span class="code-state-tag tag-active">Active</span>';
        codeSection = `
            <div style="padding: 12px 16px; background: #f8fafc; border-radius: 10px; border: 1px dashed #cbd5e1; margin-top: 10px;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <div>
                        <span style="font-size: 11px; text-transform:uppercase; font-weight:700; color:#64748b;">Leave Code:</span>
                        <div style="font-family: monospace; font-size: 17px; font-weight: 800; color: #0f172a; letter-spacing: 1px;">
                            ${leave.leave_code} ${tag}
                        </div>
                    </div>
                    <div>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="submitLeaveAction('mark_used', ${leave.id})">
                            <i class="fa-solid fa-check-double"></i> Mark Used
                        </button>
                    </div>
                </div>
                <div style="margin-top: 10px; padding-top: 10px; border-top: 1px dashed #e2e8f0;">
                    <div style="font-size: 12px; font-weight: 700; margin-bottom: 6px; color:#475569;">Code History:</div>
                    ${historyHtml}
                </div>
            </div>
        `;
    }

    let statusAlert = '';
    if (leave.status === 'approved') {
        const approverName = leave.approver_first_name ? (leave.approver_first_name + ' ' + leave.approver_last_name) : 'Administrator';
        const appDate = leave.approved_at ? new Date(leave.approved_at).toLocaleString() : 'N/A';
        statusAlert = `
            <div style="padding: 10px 14px; background: rgba(5, 150, 105, 0.08); border-left: 3px solid #059669; border-radius: 6px; font-size: 12.5px; color: #065f46;">
                <strong><i class="fa-solid fa-circle-check"></i> Approved:</strong> Application approved by ${approverName} on ${appDate}.
            </div>
        `;
    } else if (leave.status === 'rejected') {
        statusAlert = `
            <div style="padding: 10px 14px; background: rgba(225, 29, 72, 0.08); border-left: 3px solid #e11d48; border-radius: 6px; font-size: 12.5px; color: #991b1b;">
                <strong><i class="fa-solid fa-circle-xmark"></i> Declined:</strong> ${leave.rejection_reason || 'No specific remarks provided.'}
            </div>
        `;
    }

    body.innerHTML = `
        <!-- Profile Banner -->
        <div style="display:flex; align-items:center; gap: 14px; padding-bottom: 14px; border-bottom: 1px solid #e2e8f0;">
            <div style="width: 46px; height: 46px; border-radius: 50%; background: #4f46e5; color:#fff; display:flex; align-items:center; justify-content:center; font-size: 16px; font-weight:800;">
                ${fullName.charAt(0)}
            </div>
            <div>
                <h3 style="margin:0; font-size: 16px; font-weight: 800; color:#0f172a;">${fullName}</h3>
                <div style="font-size: 12px; color:#64748b;">
                    <span>${empCode}</span> &bull; <span>${dept}</span> &bull; <span>${pos}</span> &bull; <strong>Gender: ${gender}</strong>
                </div>
            </div>
        </div>

        ${statusAlert}

        <!-- Details Grid -->
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-top: 10px;">
            <div style="padding: 10px 14px; background: #f8fafc; border-radius: 8px;">
                <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b;">Leave Classification</span>
                <div style="font-size: 14px; font-weight: 700; color: #0f172a; margin-top: 2px;">
                    ${pol.name} (${pol.code || 'LV'})
                </div>
            </div>

            <div style="padding: 10px 14px; background: #f8fafc; border-radius: 8px;">
                <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b;">Duration &amp; Range</span>
                <div style="font-size: 14px; font-weight: 700; color: #0f172a; margin-top: 2px;">
                    ${dateRangeStr} (${leave.days} ${leave.days == 1 ? 'Day' : 'Days'})
                </div>
            </div>
        </div>

        <!-- Reason -->
        <div style="margin-top: 10px;">
            <label style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: #64748b;">Applicant Reason / Statement</label>
            <div style="margin-top: 4px; padding: 12px 14px; background: #f8fafc; border-radius: 8px; font-size: 13.5px; color: #1e293b; line-height: 1.5; white-space: pre-wrap;">
                ${leave.reason ? escapeHtml(leave.reason) : 'No reason provided.'}
            </div>
        </div>

        ${codeSection}
    `;

    // Footer actions
    let footerButtons = '<button type="button" class="btn btn-secondary" onclick="closeModal(\'viewLeaveModal\')">Close</button>';
    if (leave.status === 'pending') {
        footerButtons = `
            <button type="button" class="btn btn-secondary" onclick="closeModal(\'viewLeaveModal\')">Close</button>
            <button type="button" class="btn btn-danger" style="background:#e11d48; color:white; border:none; padding:8px 16px; border-radius:6px; cursor:pointer;" onclick="closeModal(\'viewLeaveModal\'); openRejectLeaveModal(${leave.id}, '${escapeHtml(fullName)}', '${escapeHtml(pol.name)}')">
                <i class="fa-solid fa-xmark"></i> Reject
            </button>
            <button type="button" class="btn btn-primary" onclick="if(confirm('Approve this leave request?')) submitLeaveAction('approve', ${leave.id})">
                <i class="fa-solid fa-check"></i> Approve
            </button>
        `;
    }
    footer.innerHTML = footerButtons;

    openModal('viewLeaveModal');
}

// ── Reject Leave Modal ──
function openRejectLeaveModal(id, empName, leaveName) {
    document.getElementById('reject_leave_id').value = id;
    const subTitle = document.getElementById('rejectModalEmpName');
    if (subTitle) {
        subTitle.textContent = 'Decline application for ' + empName + ' (' + leaveName + ')';
    }
    const reasonInput = document.getElementById('reject_reason_text');
    if (reasonInput) reasonInput.value = '';
    openModal('rejectLeaveModal');
}

// ── Filter & Search Handlers ──
function handleTableFilters() {
    const searchVal = (document.getElementById('leaveSearchInput').value || '').toLowerCase().trim();
    const statusVal = document.getElementById('statusFilter').value;
    const typeVal   = document.getElementById('typeFilter').value;
    const clearBtn  = document.getElementById('clearSearchBtn');

    if (clearBtn) {
        clearBtn.style.display = searchVal ? 'block' : 'none';
    }

    const rows = document.querySelectorAll('#leavesTableBody tr.leave-row');
    let visibleCount = 0;

    rows.forEach(r => {
        const rSearch  = r.dataset.search || '';
        const rStatus  = r.dataset.status || '';
        const rType    = r.dataset.type   || '';

        const matchSearch = !searchVal || rSearch.includes(searchVal);
        const matchStatus = (statusVal === 'all') || (rStatus === statusVal);
        const matchType   = (typeVal === 'all')   || (rType === typeVal);

        if (matchSearch && matchStatus && matchType) {
            r.style.display = '';
            visibleCount++;
        } else {
            r.style.display = 'none';
        }
    });

    const countNotice = document.getElementById('filterCountNotice');
    if (countNotice) {
        countNotice.textContent = 'Showing ' + visibleCount + ' of ' + rows.length + ' requests';
    }

    const noMatchEl = document.getElementById('noFilterMatch');
    if (noMatchEl) {
        noMatchEl.style.display = (visibleCount === 0 && rows.length > 0) ? 'block' : 'none';
    }
}

function clearSearch() {
    const input = document.getElementById('leaveSearchInput');
    if (input) {
        input.value = '';
        handleTableFilters();
    }
}

function filterByStatus(st) {
    const sel = document.getElementById('statusFilter');
    if (sel) {
        sel.value = st;
        handleTableFilters();
    }
}

function resetFilters() {
    const searchInput = document.getElementById('leaveSearchInput');
    const statusSel   = document.getElementById('statusFilter');
    const typeSel     = document.getElementById('typeFilter');

    if (searchInput) searchInput.value = '';
    if (statusSel)   statusSel.value = 'all';
    if (typeSel)     typeSel.value = 'all';

    handleTableFilters();
}

// ── Action Submitter ──
function submitLeaveAction(action, id, extraParams) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'leave';
    
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = 'csrf_token';
    csrf.value = '<?php echo htmlspecialchars($csrfToken); ?>';
    form.appendChild(csrf);

    const act = document.createElement('input');
    act.type = 'hidden';
    act.name = 'action';
    act.value = action;
    form.appendChild(act);

    const idInput = document.createElement('input');
    idInput.type = 'hidden';
    idInput.name = 'id';
    idInput.value = id;
    form.appendChild(idInput);

    if (extraParams && typeof extraParams === 'object') {
        for (const k in extraParams) {
            const p = document.createElement('input');
            p.type = 'hidden';
            p.name = k;
            p.value = extraParams[k];
            form.appendChild(p);
        }
    }

    document.body.appendChild(form);
    form.submit();
}

// ── Utilities ──
function capitalize(str) {
    if (!str) return '';
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>"']/g, function(m) {
        return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[m];
    });
}
</script>

<?php include 'footer.php'; ?>
