<?php
// ============================================================================
// Enterprise Leave Management & Attendance Integration Helper
// ============================================================================

require_once __DIR__ . '/security.php';
require_once __DIR__ . '/Mailer.php';

class LeaveHelper {

    /**
     * Standard Annual Entitlements per Leave Type
     */
    public static $defaultAllowances = [
        'casual'          => 12,
        'sick'            => 10,
        'annual'          => 12,
        'maternity'       => 180,
        'paternity'       => 15,
    ];

    /**
     * Organizational Leave Policy Configuration
     */
    public static $leavePolicies = [
        'casual' => [
            'code'             => 'CL',
            'name'             => 'Casual Leave',
            'allowance'        => 12,
            'gender'           => 'all',
            'requires_gender'  => false,
            'min_service_days' => 0,
            'is_statutory'     => false,
            'description'      => 'Short personal time off or urgent family commitments.'
        ],
        'sick' => [
            'code'             => 'SL',
            'name'             => 'Sick Leave',
            'allowance'        => 10,
            'gender'           => 'all',
            'requires_gender'  => false,
            'min_service_days' => 0,
            'is_statutory'     => false,
            'description'      => 'Medical recovery, health consultations or illness.'
        ],
        'annual' => [
            'code'             => 'AL',
            'name'             => 'Annual Leave',
            'allowance'        => 12,
            'gender'           => 'all',
            'requires_gender'  => false,
            'min_service_days' => 0,
            'is_statutory'     => false,
            'description'      => 'Earned annual vacations and planned personal time.'
        ],
        'maternity' => [
            'code'             => 'ML',
            'name'             => 'Maternity Leave',
            'allowance'        => 180,
            'gender'           => 'female',
            'requires_gender'  => true,
            'min_service_days' => 80,
            'is_statutory'     => true,
            'description'      => 'Prenatal and postnatal leave for eligible female employees.'
        ],
        'paternity' => [
            'code'             => 'PL',
            'name'             => 'Paternity Leave',
            'allowance'        => 15,
            'gender'           => 'male',
            'requires_gender'  => true,
            'min_service_days' => 0,
            'is_statutory'     => true,
            'description'      => 'Leave for eligible male employees upon child birth or adoption.'
        ],
        'early_departure' => [
            'code'             => 'ED',
            'name'             => 'Early Departure',
            'allowance'        => 0,
            'gender'           => 'all',
            'requires_gender'  => false,
            'min_service_days' => 0,
            'is_statutory'     => false,
            'description'      => 'Leaving shift early for urgent scheduled appointments.'
        ]
    ];

    /**
     * Check if an employee is eligible for a given leave type under organizational policy
     *
     * @param PDO $pdo
     * @param int $employeeId
     * @param string $leaveType
     * @param string|null $startDate
     * @param string|null $overrideGender
     * @return array
     */
    public static function checkEligibility($pdo, $employeeId, $leaveType, $startDate = null, $overrideGender = null) {
        $leaveType = strtolower(trim($leaveType));

        if (!isset(self::$leavePolicies[$leaveType])) {
            return [
                'eligible'        => false,
                'requires_gender' => false,
                'message'         => "Invalid or unsupported leave type: '{$leaveType}'."
            ];
        }

        $policy = self::$leavePolicies[$leaveType];

        try {
            $stmt = $pdo->prepare("SELECT id, first_name, last_name, gender, hire_date, status FROM employees WHERE id = ?");
            $stmt->execute([(int)$employeeId]);
            $emp = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$emp) {
                return [
                    'eligible'        => false,
                    'requires_gender' => false,
                    'message'         => 'Employee record not found.'
                ];
            }

            if ($emp['status'] === 'inactive') {
                return [
                    'eligible'        => false,
                    'requires_gender' => false,
                    'message'         => 'Inactive employees cannot be granted leave under organization policy.'
                ];
            }

            // Determine effective gender
            $effectiveGender = !empty($emp['gender']) ? strtolower(trim($emp['gender'])) : null;
            if (!empty($overrideGender)) {
                $overrideGender = strtolower(trim($overrideGender));
                if (in_array($overrideGender, ['male', 'female', 'other'], true)) {
                    $effectiveGender = $overrideGender;
                }
            }

            // Check gender-specific requirements
            if ($policy['gender'] !== 'all') {
                if (empty($effectiveGender)) {
                    return [
                        'eligible'         => false,
                        'requires_gender'  => true,
                        'effective_gender' => null,
                        'message'          => "Employee gender is required to determine eligibility for {$policy['name']} under organization leave policy."
                    ];
                }

                if ($effectiveGender !== $policy['gender']) {
                    $expectedGenderLabel = ucfirst($policy['gender']);
                    $currentGenderLabel  = ucfirst($effectiveGender);
                    return [
                        'eligible'         => false,
                        'requires_gender'  => false,
                        'effective_gender' => $effectiveGender,
                        'message'          => "Ineligible: {$policy['name']} is available exclusively to {$expectedGenderLabel} employees (Current employee profile: {$currentGenderLabel})."
                    ];
                }
            }

            // Check minimum service days requirement (e.g. Maternity Benefit Act requires min 80 days of service)
            if ($policy['min_service_days'] > 0 && !empty($emp['hire_date'])) {
                $hireDate = new DateTime($emp['hire_date']);
                $targetDate = !empty($startDate) ? new DateTime($startDate) : new DateTime();
                if ($targetDate >= $hireDate) {
                    $serviceDays = (int)$targetDate->diff($hireDate)->days;
                    if ($serviceDays < $policy['min_service_days']) {
                        return [
                            'eligible'         => false,
                            'requires_gender'  => false,
                            'effective_gender' => $effectiveGender,
                            'message'          => "Employee has completed {$serviceDays} day(s) of service, which does not meet the policy requirement of {$policy['min_service_days']} days for {$policy['name']}."
                        ];
                    }
                }
            }

            return [
                'eligible'         => true,
                'requires_gender'  => false,
                'effective_gender' => $effectiveGender,
                'policy'           => $policy,
                'message'          => "Employee is eligible for {$policy['name']}."
            ];
        } catch (PDOException $e) {
            error_log("checkEligibility error: " . $e->getMessage());
            return [
                'eligible'        => false,
                'requires_gender' => false,
                'message'         => 'Database error evaluating leave eligibility.'
            ];
        }
    }

    /**
     * Retrieve all leave types applicable to an employee based on gender and policy rules
     */
    public static function getApplicableLeaveTypes($pdo, $employeeId, $overrideGender = null) {
        $applicable = [];
        foreach (self::$leavePolicies as $slug => $pol) {
            $check = self::checkEligibility($pdo, $employeeId, $slug, null, $overrideGender);
            if ($check['eligible']) {
                $applicable[$slug] = $pol;
            }
        }
        return $applicable;
    }

    /**
     * Initialize or ensure employee leave balances exist for a given year
     */
    public static function initEmployeeBalances($pdo, $employeeId, $year = null) {
        if ($year === null) {
            $year = (int)date('Y');
        }

        try {
            $empGender = $pdo->query("SELECT gender FROM employees WHERE id = " . (int)$employeeId)->fetchColumn();
            $empGender = $empGender ? strtolower(trim($empGender)) : null;

            $stmt = $pdo->prepare("SELECT leave_type FROM leave_balances WHERE employee_id = ? AND year = ?");
            $stmt->execute([(int)$employeeId, (int)$year]);
            $existingTypes = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $insertStmt = $pdo->prepare("
                INSERT INTO leave_balances (employee_id, leave_type, year, total_days, used_days)
                VALUES (?, ?, ?, ?, 0)
                ON DUPLICATE KEY UPDATE id=id
            ");

            // Base allowances for all employees
            $allowances = [
                'casual' => 12,
                'sick'   => 10,
                'annual' => 12,
            ];

            // Gender-specific statutory allowances
            if ($empGender === 'female') {
                $allowances['maternity'] = 180;
            } elseif ($empGender === 'male') {
                $allowances['paternity'] = 15;
            }

            foreach ($allowances as $type => $totalDays) {
                if (!in_array($type, $existingTypes, true)) {
                    $insertStmt->execute([(int)$employeeId, $type, (int)$year, (int)$totalDays]);
                }
            }
            return true;
        } catch (PDOException $e) {
            error_log("initEmployeeBalances error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Retrieve leave balances for an employee in a given year
     */
    public static function getEmployeeBalances($pdo, $employeeId, $year = null) {
        if ($year === null) {
            $year = (int)date('Y');
        }

        self::initEmployeeBalances($pdo, $employeeId, $year);

        $balances = [];
        try {
            $stmt = $pdo->prepare("SELECT * FROM leave_balances WHERE employee_id = ? AND year = ?");
            $stmt->execute([(int)$employeeId, (int)$year]);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $total = (int)$row['total_days'];
                $used  = (int)$row['used_days'];
                $rem   = max(0, $total - $used);
                $balances[$row['leave_type']] = [
                    'id'             => $row['id'],
                    'total_days'     => $total,
                    'used_days'      => $used,
                    'remaining_days' => $rem
                ];
            }
        } catch (PDOException $e) {
            error_log("getEmployeeBalances error: " . $e->getMessage());
        }

        return $balances;
    }

    /**
     * Check if a date range overlaps with an existing pending or approved leave
     */
    public static function checkOverlap($pdo, $employeeId, $startDate, $endDate, $excludeLeaveId = 0) {
        try {
            $sql = "
                SELECT id, leave_type, start_date, end_date, status 
                FROM leave_requests 
                WHERE employee_id = ? 
                  AND status IN ('pending', 'approved')
                  AND id != ?
                  AND NOT (end_date < ? OR start_date > ?)
                LIMIT 1
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([(int)$employeeId, (int)$excludeLeaveId, $startDate, $endDate]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
        } catch (PDOException $e) {
            error_log("checkOverlap error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate a unique, professional Leave Code
     */
    public static function generateLeaveCode($pdo) {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        for ($i = 0; $i < 10; $i++) {
            $code = 'LV-' . substr(str_shuffle($chars), 0, 6);
            $stmt = $pdo->prepare("SELECT id FROM leave_requests WHERE leave_code = ? LIMIT 1");
            $stmt->execute([$code]);
            if (!$stmt->fetchColumn()) {
                return $code;
            }
        }
        return 'LV-' . strtoupper(bin2hex(random_bytes(3)));
    }

    /**
     * Approve a leave request end-to-end
     */
    public static function approveLeave($pdo, $leaveId, $adminId) {
        try {
            $stmt = $pdo->prepare("
                SELECT lr.*, e.first_name, e.last_name, e.email 
                FROM leave_requests lr
                JOIN employees e ON e.id = lr.employee_id
                WHERE lr.id = ?
            ");
            $stmt->execute([(int)$leaveId]);
            $leave = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$leave) {
                return ['success' => false, 'message' => 'Leave request not found.'];
            }
            if ($leave['status'] === 'approved') {
                return ['success' => false, 'message' => 'This leave request is already approved.'];
            }

            // Check for existing approved leave overlap
            $overlap = self::checkOverlap($pdo, (int)$leave['employee_id'], $leave['start_date'], $leave['end_date'], (int)$leaveId);
            if ($overlap && $overlap['status'] === 'approved') {
                $codeStr = !empty($overlap['leave_code']) ? " ({$overlap['leave_code']})" : "";
                return [
                    'success' => false, 
                    'message' => "Conflict: Employee already has an approved leave{$codeStr} from {$overlap['start_date']} to {$overlap['end_date']}."
                ];
            }

            // Check eligibility under organizational leave policy
            $eligibility = self::checkEligibility($pdo, (int)$leave['employee_id'], $leave['leave_type'], $leave['start_date']);
            if (!$eligibility['eligible']) {
                return [
                    'success' => false, 
                    'message' => 'Eligibility validation failed: ' . $eligibility['message']
                ];
            }

            // Ensure sufficient leave balance entitlement
            $year = (int)date('Y', strtotime($leave['start_date']));
            $empId = (int)$leave['employee_id'];
            $days = (int)$leave['days'];
            $leaveType = $leave['leave_type'];

            if (in_array($leaveType, ['annual', 'sick', 'casual', 'maternity', 'paternity'], true)) {
                $balances = self::getEmployeeBalances($pdo, $empId, $year);
                $rem = $balances[$leaveType]['remaining_days'] ?? 0;
                if ($rem < $days) {
                    return [
                        'success' => false,
                        'message' => "Insufficient leave balance for " . ucfirst(str_replace('_', ' ', $leaveType)) . " leave. Remaining: {$rem} day(s), Requested: {$days} day(s)."
                    ];
                }
            }

            $pdo->beginTransaction();

            $leaveCode  = self::generateLeaveCode($pdo);
            $approvedAt = date('Y-m-d H:i:s');
            $year       = (int)date('Y', strtotime($leave['start_date']));
            $days       = (int)$leave['days'];
            $empId      = (int)$leave['employee_id'];
            $leaveType  = $leave['leave_type'];

            // 1. Update leave request record
            $upStmt = $pdo->prepare("
                UPDATE leave_requests 
                SET status = 'approved', leave_code = ?, approved_by = ?, approved_at = ?, rejection_reason = NULL 
                WHERE id = ?
            ");
            $upStmt->execute([$leaveCode, $adminId, $approvedAt, $leaveId]);

            // 2. Ensure and update leave balance
            if (in_array($leaveType, ['annual', 'sick', 'casual', 'maternity', 'paternity'], true)) {
                self::initEmployeeBalances($pdo, $empId, $year);
                $balStmt = $pdo->prepare("
                    UPDATE leave_balances 
                    SET used_days = used_days + ? 
                    WHERE employee_id = ? AND leave_type = ? AND year = ?
                ");
                $balStmt->execute([$days, $empId, $leaveType, $year]);
            }

            // 3. Integrate with Attendance: Record on_leave for each date in range
            $startDt = new DateTime($leave['start_date']);
            $endDt   = new DateTime($leave['end_date']);
            $endDt->modify('+1 day'); // inclusive period
            $interval = new DateInterval('P1D');
            $period   = new DatePeriod($startDt, $interval, $endDt);

            $attStmt = $pdo->prepare("
                INSERT INTO attendance (employee_id, date, status, attendance_status, leave_code, notes)
                VALUES (?, ?, 'on_leave', 'on_leave', ?, ?)
                ON DUPLICATE KEY UPDATE 
                    status = 'on_leave',
                    attendance_status = 'on_leave',
                    leave_code = VALUES(leave_code),
                    notes = VALUES(notes)
            ");

            $typeLabel = ucfirst(str_replace('_', ' ', $leaveType));
            $noteText  = "Approved {$typeLabel} Leave ({$leaveCode})";

            $today = date('Y-m-d');
            $coversToday = false;

            foreach ($period as $dt) {
                $curDate = $dt->format('Y-m-d');
                $attStmt->execute([$empId, $curDate, $leaveCode, $noteText]);
                if ($curDate === $today) {
                    $coversToday = true;
                }
            }

            // 4. Update employee current status to on_leave if leave covers today
            if ($coversToday) {
                $pdo->prepare("UPDATE employees SET status = 'on_leave' WHERE id = ?")->execute([$empId]);
            }

            $pdo->commit();

            Security::logAudit($adminId, 'approve_leave', 'leave_requests', $leaveId, [
                'leave_code' => $leaveCode,
                'employee_id' => $empId,
                'days' => $days
            ]);

            // 5. Non-blocking email alert to employee
            try {
                if (!empty($leave['email'])) {
                    $mailer = new Mailer($pdo);
                    $empName = trim($leave['first_name'] . ' ' . $leave['last_name']);
                    $mailer->sendLeaveApprovalAlert(
                        $leave['email'],
                        $empName,
                        $typeLabel,
                        $leaveCode,
                        $leave['start_date'],
                        $leave['end_date'],
                        $days
                    );
                }
            } catch (Throwable $mailEx) {
                error_log("Leave approval mail error: " . $mailEx->getMessage());
            }

            return [
                'success'    => true,
                'leave_code' => $leaveCode,
                'message'    => "Leave approved successfully. Leave Code {$leaveCode} generated and attendance roster updated."
            ];
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            error_log("approveLeave error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error approving leave: ' . $e->getMessage()];
        }
    }

    /**
     * Reject a leave request end-to-end
     */
    public static function rejectLeave($pdo, $leaveId, $adminId, $rejectionReason = '') {
        try {
            $stmt = $pdo->prepare("
                SELECT lr.*, e.first_name, e.last_name, e.email 
                FROM leave_requests lr
                JOIN employees e ON e.id = lr.employee_id
                WHERE lr.id = ?
            ");
            $stmt->execute([(int)$leaveId]);
            $leave = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$leave) {
                return ['success' => false, 'message' => 'Leave request not found.'];
            }

            $pdo->beginTransaction();

            $wasApproved = ($leave['status'] === 'approved');
            $empId       = (int)$leave['employee_id'];
            $leaveType   = $leave['leave_type'];
            $days        = (int)$leave['days'];
            $year        = (int)date('Y', strtotime($leave['start_date']));
            $leaveCode   = $leave['leave_code'];
            $approvedAt  = date('Y-m-d H:i:s');

            // 1. If previously approved, refund leave balance
            if ($wasApproved && in_array($leaveType, ['annual', 'sick', 'casual', 'maternity', 'paternity'], true)) {
                $pdo->prepare("
                    UPDATE leave_balances 
                    SET used_days = GREATEST(0, used_days - ?) 
                    WHERE employee_id = ? AND leave_type = ? AND year = ?
                ")->execute([$days, $empId, $leaveType, $year]);

                // Revert or clear attendance records created for this leave code
                if (!empty($leaveCode)) {
                    // Revert records that already had punch activity back to present
                    $pdo->prepare("
                        UPDATE attendance 
                        SET status = 'present', attendance_status = 'present', leave_code = NULL, notes = NULL 
                        WHERE employee_id = ? AND leave_code = ? AND check_in IS NOT NULL
                    ")->execute([$empId, $leaveCode]);

                    // Remove placeholder records that had no punch activity
                    $pdo->prepare("
                        DELETE FROM attendance 
                        WHERE employee_id = ? AND leave_code = ? AND check_in IS NULL
                    ")->execute([$empId, $leaveCode]);
                }

                // Check if employee has any other active approved leave covering today
                $today = date('Y-m-d');
                $stActive = $pdo->prepare("
                    SELECT id FROM leave_requests 
                    WHERE employee_id = ? AND status = 'approved' AND ? BETWEEN start_date AND end_date
                    LIMIT 1
                ");
                $stActive->execute([$empId, $today]);
                if (!$stActive->fetchColumn()) {
                    $pdo->prepare("UPDATE employees SET status = 'active' WHERE id = ? AND status = 'on_leave'")->execute([$empId]);
                }
            }

            // 2. Update leave request to rejected
            $upStmt = $pdo->prepare("
                UPDATE leave_requests 
                SET status = 'rejected', rejection_reason = ?, approved_by = ?, approved_at = ? 
                WHERE id = ?
            ");
            $upStmt->execute([$rejectionReason !== '' ? $rejectionReason : 'Request declined by administrator', $adminId, $approvedAt, $leaveId]);

            $pdo->commit();

            Security::logAudit($adminId, 'reject_leave', 'leave_requests', $leaveId, [
                'employee_id' => $empId,
                'reason' => $rejectionReason
            ]);

            // 3. Non-blocking email alert to employee
            try {
                if (!empty($leave['email'])) {
                    $mailer = new Mailer($pdo);
                    $empName = trim($leave['first_name'] . ' ' . $leave['last_name']);
                    $typeLabel = ucfirst(str_replace('_', ' ', $leaveType));
                    $mailer->sendLeaveRejectionAlert(
                        $leave['email'],
                        $empName,
                        $typeLabel,
                        $leave['start_date'],
                        $leave['end_date'],
                        $rejectionReason
                    );
                }
            } catch (Throwable $mailEx) {
                error_log("Leave rejection mail error: " . $mailEx->getMessage());
            }

            return ['success' => true, 'message' => 'Leave request has been rejected.'];
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            error_log("rejectLeave error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error rejecting leave: ' . $e->getMessage()];
        }
    }

    /**
     * Delete a leave request permanently
     */
    public static function deleteLeave($pdo, $leaveId, $adminId) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM leave_requests WHERE id = ?");
            $stmt->execute([(int)$leaveId]);
            $leave = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$leave) {
                return ['success' => false, 'message' => 'Leave request not found.'];
            }

            $pdo->beginTransaction();

            $wasApproved = ($leave['status'] === 'approved');
            $empId       = (int)$leave['employee_id'];
            $leaveType   = $leave['leave_type'];
            $days        = (int)$leave['days'];
            $year        = (int)date('Y', strtotime($leave['start_date']));
            $leaveCode   = $leave['leave_code'];

            if ($wasApproved) {
                // Refund balance
                if (in_array($leaveType, ['annual', 'sick', 'casual', 'maternity', 'paternity'], true)) {
                    $pdo->prepare("
                        UPDATE leave_balances 
                        SET used_days = GREATEST(0, used_days - ?) 
                        WHERE employee_id = ? AND leave_type = ? AND year = ?
                    ")->execute([$days, $empId, $leaveType, $year]);
                }

                // Remove or revert attendance records
                if (!empty($leaveCode)) {
                    $pdo->prepare("
                        UPDATE attendance 
                        SET status = 'present', attendance_status = 'present', leave_code = NULL, notes = NULL 
                        WHERE employee_id = ? AND leave_code = ? AND check_in IS NOT NULL
                    ")->execute([$empId, $leaveCode]);

                    $pdo->prepare("
                        DELETE FROM attendance 
                        WHERE employee_id = ? AND leave_code = ? AND check_in IS NULL
                    ")->execute([$empId, $leaveCode]);
                }

                // Revert employee status if needed
                $today = date('Y-m-d');
                $stActive = $pdo->prepare("
                    SELECT id FROM leave_requests 
                    WHERE employee_id = ? AND status = 'approved' AND ? BETWEEN start_date AND end_date AND id != ?
                    LIMIT 1
                ");
                $stActive->execute([$empId, $today, $leaveId]);
                if (!$stActive->fetchColumn()) {
                    $pdo->prepare("UPDATE employees SET status = 'active' WHERE id = ? AND status = 'on_leave'")->execute([$empId]);
                }
            }

            $delStmt = $pdo->prepare("DELETE FROM leave_requests WHERE id = ?");
            $delStmt->execute([$leaveId]);

            $pdo->commit();

            Security::logAudit($adminId, 'delete_leave', 'leave_requests', $leaveId, null);

            return ['success' => true, 'message' => 'Leave record permanently deleted.'];
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            error_log("deleteLeave error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error deleting leave: ' . $e->getMessage()];
        }
    }

    /**
     * Admin directly generates an approved leave for an employee
     */
    public static function generateLeave($pdo, $adminId, $employeeId, $leaveType, $startDate, $endDate, $reason, $overrideGender = null) {
        try {
            $startDt = new DateTime($startDate);
            $endDt   = new DateTime($endDate);

            if ($endDt < $startDt) {
                return ['success' => false, 'message' => 'End date cannot be earlier than start date.'];
            }

            // Check eligibility under organizational leave policy
            $eligibility = self::checkEligibility($pdo, (int)$employeeId, $leaveType, $startDate, $overrideGender);
            if (!$eligibility['eligible']) {
                return [
                    'success'         => false,
                    'requires_gender' => $eligibility['requires_gender'] ?? false,
                    'message'         => 'Eligibility validation failed: ' . $eligibility['message']
                ];
            }

            // If override gender provided and employee record lacks gender, persist it
            if (!empty($overrideGender)) {
                $cleanGender = strtolower(trim($overrideGender));
                if (in_array($cleanGender, ['male', 'female', 'other'], true)) {
                    $pdo->prepare("UPDATE employees SET gender = ? WHERE id = ? AND (gender IS NULL OR gender = '')")->execute([$cleanGender, (int)$employeeId]);
                }
            }

            // Check for overlap
            $overlap = self::checkOverlap($pdo, $employeeId, $startDate, $endDate);
            if ($overlap) {
                return [
                    'success' => false, 
                    'message' => "Conflict: Employee already has a {$overlap['status']} leave from {$overlap['start_date']} to {$overlap['end_date']}."
                ];
            }

            $days = (int)$endDt->diff($startDt)->days + 1;
            $leaveCode  = self::generateLeaveCode($pdo);
            $approvedAt = date('Y-m-d H:i:s');
            $year       = (int)date('Y', strtotime($startDate));

            // Ensure sufficient leave balance entitlement
            if (in_array($leaveType, ['annual', 'sick', 'casual', 'maternity', 'paternity'], true)) {
                $balances = self::getEmployeeBalances($pdo, (int)$employeeId, $year);
                $rem = $balances[$leaveType]['remaining_days'] ?? 0;
                if ($rem < $days) {
                    return [
                        'success' => false,
                        'message' => "Insufficient leave balance for " . ucfirst(str_replace('_', ' ', $leaveType)) . " leave. Remaining: {$rem} day(s), Requested: {$days} day(s)."
                    ];
                }
            }

            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
                INSERT INTO leave_requests (employee_id, leave_type, start_date, end_date, days, reason, status, approved_by, approved_at, leave_code)
                VALUES (?, ?, ?, ?, ?, ?, 'approved', ?, ?, ?)
            ");
            $stmt->execute([(int)$employeeId, $leaveType, $startDate, $endDate, $days, $reason, $adminId, $approvedAt, $leaveCode]);
            $leaveId = (int)$pdo->lastInsertId();

            // Update balance
            if (in_array($leaveType, ['annual', 'sick', 'casual', 'maternity', 'paternity'], true)) {
                self::initEmployeeBalances($pdo, $employeeId, $year);
                $pdo->prepare("
                    UPDATE leave_balances 
                    SET used_days = used_days + ? 
                    WHERE employee_id = ? AND leave_type = ? AND year = ?
                ")->execute([$days, (int)$employeeId, $leaveType, $year]);
            }

            // Attendance Integration
            $attEndDt = (new DateTime($endDate))->modify('+1 day');
            $period   = new DatePeriod($startDt, new DateInterval('P1D'), $attEndDt);

            $attStmt = $pdo->prepare("
                INSERT INTO attendance (employee_id, date, status, attendance_status, leave_code, notes)
                VALUES (?, ?, 'on_leave', 'on_leave', ?, ?)
                ON DUPLICATE KEY UPDATE 
                    status = 'on_leave',
                    attendance_status = 'on_leave',
                    leave_code = VALUES(leave_code),
                    notes = VALUES(notes)
            ");

            $typeLabel = ucfirst(str_replace('_', ' ', $leaveType));
            $noteText  = "Approved {$typeLabel} Leave ({$leaveCode})";
            $today = date('Y-m-d');
            $coversToday = false;

            foreach ($period as $dt) {
                $curDate = $dt->format('Y-m-d');
                $attStmt->execute([(int)$employeeId, $curDate, $leaveCode, $noteText]);
                if ($curDate === $today) {
                    $coversToday = true;
                }
            }

            if ($coversToday) {
                $pdo->prepare("UPDATE employees SET status = 'on_leave' WHERE id = ?")->execute([(int)$employeeId]);
            }

            $pdo->commit();

            Security::logAudit($adminId, 'generate_leave', 'leave_requests', $leaveId, [
                'employee_id' => $employeeId,
                'leave_code' => $leaveCode,
                'days' => $days
            ]);

            // Email alert
            try {
                $empStmt = $pdo->prepare("SELECT first_name, last_name, email FROM employees WHERE id = ? LIMIT 1");
                $empStmt->execute([(int)$employeeId]);
                $emp = $empStmt->fetch(PDO::FETCH_ASSOC);
                if ($emp && !empty($emp['email'])) {
                    $mailer = new Mailer($pdo);
                    $empName = trim($emp['first_name'] . ' ' . $emp['last_name']);
                    $mailer->sendLeaveApprovalAlert($emp['email'], $empName, $typeLabel, $leaveCode, $startDate, $endDate, $days);
                }
            } catch (Throwable $mailEx) {
                error_log("Generate leave email alert error: " . $mailEx->getMessage());
            }

            return [
                'success'    => true,
                'leave_code' => $leaveCode,
                'message'    => "Leave generated successfully. Leave Code: {$leaveCode} generated and attendance roster updated."
            ];
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            error_log("generateLeave error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error generating leave: ' . $e->getMessage()];
        }
    }
}
