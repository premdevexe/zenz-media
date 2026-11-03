-- Cloud Aspire Employee Portal: Production Migration Patch
-- Safe, idempotent patch to upgrade live database without data loss
-- Date: 2026-11-03 14:12:38

SET FOREIGN_KEY_CHECKS = 0;

-- STEP 1: Add Missing Columns to Existing Tables
ALTER TABLE `attendance` ADD COLUMN IF NOT EXISTS `work_summary` text NULL DEFAULT NULL AFTER `remarks`;
ALTER TABLE `attendance` ADD COLUMN IF NOT EXISTS `completed_tasks` text NULL DEFAULT NULL AFTER `work_summary`;
ALTER TABLE `attendance` ADD COLUMN IF NOT EXISTS `pending_tasks` text NULL DEFAULT NULL AFTER `completed_tasks`;
ALTER TABLE `attendance` ADD COLUMN IF NOT EXISTS `check_in_lat` decimal(10,8) NULL DEFAULT NULL AFTER `pending_tasks`;
ALTER TABLE `attendance` ADD COLUMN IF NOT EXISTS `check_in_lng` decimal(11,8) NULL DEFAULT NULL AFTER `check_in_lat`;
ALTER TABLE `attendance` ADD COLUMN IF NOT EXISTS `check_in_dist` int(11) NULL DEFAULT NULL AFTER `check_in_lng`;
ALTER TABLE `attendance` ADD COLUMN IF NOT EXISTS `check_out_lat` decimal(10,8) NULL DEFAULT NULL AFTER `check_in_dist`;
ALTER TABLE `attendance` ADD COLUMN IF NOT EXISTS `check_out_lng` decimal(11,8) NULL DEFAULT NULL AFTER `check_out_lat`;
ALTER TABLE `attendance` ADD COLUMN IF NOT EXISTS `check_out_dist` int(11) NULL DEFAULT NULL AFTER `check_out_lng`;
ALTER TABLE `attendance` ADD COLUMN IF NOT EXISTS `is_flagged_for_review` tinyint(1) NOT NULL DEFAULT '0' AFTER `check_out_dist`;
ALTER TABLE `attendance` ADD COLUMN IF NOT EXISTS `flag_reason` varchar(255) NULL DEFAULT NULL AFTER `is_flagged_for_review`;
ALTER TABLE `attendance` ADD COLUMN IF NOT EXISTS `closed_by_system` tinyint(1) NOT NULL DEFAULT '0' AFTER `flag_reason`;
ALTER TABLE `attendance` ADD COLUMN IF NOT EXISTS `checkout_type` varchar(30) NOT NULL DEFAULT 'manual' AFTER `closed_by_system`;
ALTER TABLE `attendance` ADD COLUMN IF NOT EXISTS `scheduled_checkout` time NULL DEFAULT NULL AFTER `checkout_type`;
ALTER TABLE `attendance` ADD COLUMN IF NOT EXISTS `summary_submitted_at` datetime NULL DEFAULT NULL AFTER `scheduled_checkout`;
ALTER TABLE `employees` ADD COLUMN IF NOT EXISTS `role_id` int(11) NULL DEFAULT NULL AFTER `first_login_completed`;
ALTER TABLE `employees` ADD COLUMN IF NOT EXISTS `gender` enum('male','female','other') NULL DEFAULT NULL AFTER `role_id`;
ALTER TABLE `employees` ADD COLUMN IF NOT EXISTS `work_mode` enum('on_site','wfh') NOT NULL DEFAULT 'on_site' AFTER `gender`;
ALTER TABLE `employees` ADD COLUMN IF NOT EXISTS `shift_id` int(11) NULL DEFAULT '1' AFTER `work_mode`;
ALTER TABLE `site_settings` ADD COLUMN IF NOT EXISTS `office_name` varchar(150) NOT NULL DEFAULT 'Cloud Aspire Headquarters' AFTER `updated_at`;
ALTER TABLE `site_settings` ADD COLUMN IF NOT EXISTS `office_address` varchar(255) NULL DEFAULT NULL AFTER `office_name`;
ALTER TABLE `site_settings` ADD COLUMN IF NOT EXISTS `office_latitude` decimal(10,8) NULL DEFAULT '11.94160000' AFTER `office_address`;
ALTER TABLE `site_settings` ADD COLUMN IF NOT EXISTS `office_longitude` decimal(11,8) NULL DEFAULT '79.80830000' AFTER `office_latitude`;
ALTER TABLE `site_settings` ADD COLUMN IF NOT EXISTS `office_radius_meters` int(11) NOT NULL DEFAULT '100' AFTER `office_longitude`;
ALTER TABLE `site_settings` ADD COLUMN IF NOT EXISTS `location_attendance_enabled` tinyint(1) NOT NULL DEFAULT '1' AFTER `office_radius_meters`;
ALTER TABLE `site_settings` ADD COLUMN IF NOT EXISTS `employee_index_enabled` tinyint(1) NOT NULL DEFAULT '1' AFTER `location_attendance_enabled`;
ALTER TABLE `site_settings` ADD COLUMN IF NOT EXISTS `wfh_early_window_minutes` int(11) NOT NULL DEFAULT '15' AFTER `employee_index_enabled`;
ALTER TABLE `site_settings` ADD COLUMN IF NOT EXISTS `wfh_grace_period_minutes` int(11) NOT NULL DEFAULT '15' AFTER `wfh_early_window_minutes`;
ALTER TABLE `site_settings` ADD COLUMN IF NOT EXISTS `wfh_late_threshold_minutes` int(11) NOT NULL DEFAULT '30' AFTER `wfh_grace_period_minutes`;
ALTER TABLE `site_settings` ADD COLUMN IF NOT EXISTS `wfh_auto_reschedule_allowed` tinyint(1) NOT NULL DEFAULT '0' AFTER `wfh_late_threshold_minutes`;

-- STEP 2: Create Missing Tables
-- Table: roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: permissions
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `module` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: role_permissions
CREATE TABLE IF NOT EXISTS `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `permission_id` (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: admin_roles
CREATE TABLE IF NOT EXISTS `admin_roles` (
  `admin_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`admin_id`,`role_id`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: admin_audit_logs
CREATE TABLE IF NOT EXISTS `admin_audit_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) DEFAULT NULL,
  `admin_username` varchar(100) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `target_type` varchar(50) DEFAULT NULL,
  `target_id` int(11) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_admin_id` (`admin_id`),
  KEY `idx_action` (`action`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=206 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: attendance_requests
CREATE TABLE IF NOT EXISTS `attendance_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `requested_work_mode` enum('wfh','on_site_exception') NOT NULL DEFAULT 'wfh',
  `reason` text NOT NULL,
  `supporting_details` text DEFAULT NULL,
  `requested_check_in_time` time NOT NULL,
  `approved_check_in_time` time DEFAULT NULL,
  `approved_check_out_time` time DEFAULT NULL,
  `status` enum('pending','approved','rejected','cancelled','checked_in','expired') NOT NULL DEFAULT 'pending',
  `approver_id` int(11) DEFAULT NULL,
  `decision_timestamp` datetime DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `actual_check_in` time DEFAULT NULL,
  `submitted_lat` decimal(10,8) DEFAULT NULL,
  `submitted_lng` decimal(11,8) DEFAULT NULL,
  `submitted_dist` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_emp_date` (`employee_id`,`attendance_date`),
  KEY `idx_status` (`status`),
  KEY `idx_date_status` (`attendance_date`,`status`),
  KEY `fk_att_req_approver` (`approver_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: attendance_summary_tokens
CREATE TABLE IF NOT EXISTS `attendance_summary_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `attendance_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `shift_date` date NOT NULL,
  `scheduled_checkout_time` time NOT NULL,
  `token_hash` varchar(64) NOT NULL,
  `status` enum('pending','completed','expired','cancelled') NOT NULL DEFAULT 'pending',
  `email_sent` tinyint(1) NOT NULL DEFAULT 0,
  `email_sent_at` datetime DEFAULT NULL,
  `email_delivery_status` enum('pending','sent','failed') NOT NULL DEFAULT 'pending',
  `email_error_message` varchar(255) DEFAULT NULL,
  `email_attempts` int(11) NOT NULL DEFAULT 0,
  `last_reminder_sent_at` datetime DEFAULT NULL,
  `reminder_count` int(11) NOT NULL DEFAULT 0,
  `expires_at` datetime NOT NULL,
  `completed_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_attendance_token` (`attendance_id`),
  KEY `idx_token_hash` (`token_hash`),
  KEY `idx_employee_date` (`employee_id`,`shift_date`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: daily_work_summaries
CREATE TABLE IF NOT EXISTS `daily_work_summaries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `attendance_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `work_summary` text NOT NULL,
  `completed_tasks` text NOT NULL,
  `pending_tasks` text DEFAULT NULL,
  `additional_notes` text DEFAULT NULL,
  `submission_source` varchar(30) NOT NULL DEFAULT 'modal',
  `submitted_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_attendance_summary` (`attendance_id`),
  KEY `idx_attendance` (`attendance_id`),
  KEY `idx_employee_date` (`employee_id`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: monthly_working_days
CREATE TABLE IF NOT EXISTS `monthly_working_days` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `year` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `day_type` enum('working_day','weekly_off','holiday') NOT NULL DEFAULT 'working_day',
  `title` varchar(100) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `date` (`date`),
  UNIQUE KEY `uniq_working_day_date` (`date`),
  KEY `idx_year_month` (`year`,`month`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- STEP 3: Seed Default Roles, Permissions and Admin Assignment
-- Seed: roles
INSERT IGNORE INTO `roles` (`id`, `name`, `slug`, `description`, `is_system`, `created_at`, `updated_at`) VALUES
('1', 'Super Administrator', 'super_admin', 'Full root access to all system functions, security controls, and workforce modules.', '1', '2026-10-23 10:50:26', '2026-10-23 10:50:26'),
('2', 'HR Operations Manager', 'hr_manager', 'Manages employee directory, attendance verification, leave approvals, shifts, and daily work summaries.', '1', '2026-10-23 10:50:26', '2026-10-28 15:33:06'),
('3', 'Manager', 'manager', 'Assigns and reviews daily employee tasks, project deliverables, and reviews work summaries.', '1', '2026-10-23 10:50:26', '2026-10-28 15:33:06'),
('4', 'Department Supervisor', 'supervisor', NULL, '1', '2026-10-23 10:50:26', '2026-10-28 17:34:02'),
('5', 'Staff', 'staff', 'Standard corporate staff members with self-service capabilities.', '1', '2026-10-28 15:33:06', '2026-10-28 17:33:54'),
('6', 'Member', 'member', 'Team member with access to attendance, assigned tasks, and personal account profile.', '1', '2026-10-28 15:33:06', '2026-10-28 15:33:06');

-- Seed: permissions
INSERT IGNORE INTO `permissions` (`id`, `name`, `slug`, `module`, `description`, `created_at`) VALUES
('1', 'Attendance: Approve / Reject WFH & Exceptions', 'attendance.approve_requests', 'Attendance', 'Authorize or reject employee work from home and on-site location exception requests', '2026-10-28 14:54:09'),
('2', 'Employees: Create Employee', 'employees.create', 'Employees', 'Add new employees to the workforce directory', '2026-10-23 10:50:26'),
('3', 'Employees: Edit Employee', 'employees.edit', 'Employees', 'Modify employee profile, compensation, and work mode', '2026-10-23 10:50:26'),
('4', 'Employees: Delete Employee', 'employees.delete', 'Employees', 'Remove employees and revoke portal access', '2026-10-23 10:50:26'),
('5', 'Attendance: View Records', 'attendance.view', 'Attendance', 'View daily and monthly attendance logs', '2026-10-23 10:50:26'),
('6', 'Attendance: Manage & Edit', 'attendance.manage', 'Attendance', 'Edit check-in, check-out, status, and manual overrides', '2026-10-23 10:50:26'),
('7', 'Attendance: Review Work Summaries', 'attendance.review_summaries', 'Attendance', 'Review employee daily work summaries and blockers', '2026-10-23 10:50:26'),
('8', 'Attendance: Export CSV', 'attendance.export', 'Attendance', 'Export attendance reports to CSV and spreadsheets', '2026-10-23 10:50:26'),
('9', 'Tasks: View All Tasks', 'tasks.view', 'Tasks', 'View tasks across all departments and employees', '2026-10-23 10:50:26'),
('10', 'Tasks: Create & Assign', 'tasks.create', 'Tasks', 'Create new tasks and assign to employees', '2026-10-23 10:50:26'),
('11', 'Tasks: Manage & Update', 'tasks.manage', 'Tasks', 'Update task status, priority, and verify completion', '2026-10-23 10:50:26'),
('12', 'Projects: View Submissions', 'projects.view', 'Projects', 'Review employee submitted drive links and deliverables', '2026-10-23 10:50:26'),
('13', 'Projects: Manage & Review', 'projects.manage', 'Projects', 'Approve, request changes, or reject submitted projects', '2026-10-23 10:50:26'),
('14', 'Leaves: View Requests', 'leaves.view', 'Leaves', 'View leave applications and calendar balances', '2026-10-23 10:50:26'),
('15', 'Leaves: Approve / Reject', 'leaves.manage', 'Leaves', 'Approve or reject leave requests and generate leave codes', '2026-10-23 10:50:26'),
('16', 'Shifts: View & Configure', 'shifts.view', 'Shifts', 'View shift schedules and grace thresholds', '2026-10-23 10:50:26'),
('17', 'Shifts: Manage Shifts', 'shifts.manage', 'Shifts', 'Create, edit, and activate operational shift timings', '2026-10-23 10:50:26'),
('18', 'Reports: View Analytics', 'reports.view', 'Reports', 'Access executive dashboards, KPI telemetry, and analytics', '2026-10-23 10:50:26'),
('19', 'Reports: Export Reports', 'reports.export', 'Reports', 'Export detailed workforce analytics', '2026-10-23 10:50:26'),
('20', 'Settings: View Settings', 'settings.view', 'Settings', 'View system settings and console configurations', '2026-10-23 10:50:26'),
('21', 'Settings: Manage Settings', 'settings.manage', 'Settings', 'Manage branding, SMTP, and office geofence locations', '2026-10-23 10:50:26'),
('22', 'Roles: View Roles', 'roles.view', 'Role Management', 'View roles and their assigned permission matrices', '2026-10-23 10:50:26'),
('23', 'Roles: Manage Roles', 'roles.manage', 'Role Management', 'Create and edit roles and customize permission allocations', '2026-10-23 10:50:26'),
('24', 'Employees: View Directory', 'employees.view', 'Employees', 'View employee listings, profile cards, and details', '2026-10-28 15:33:06');

-- Seed: role_permissions
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`) VALUES
('1', '1'),
('1', '2'),
('1', '3'),
('1', '4'),
('1', '5'),
('1', '6'),
('1', '7'),
('1', '8'),
('1', '9'),
('1', '10'),
('1', '11'),
('1', '12'),
('1', '13'),
('1', '14'),
('1', '15'),
('1', '16'),
('1', '17'),
('1', '18'),
('1', '19'),
('1', '20'),
('1', '21'),
('1', '22'),
('1', '23'),
('1', '24'),
('2', '1'),
('2', '2'),
('2', '3'),
('2', '5'),
('2', '6'),
('2', '7'),
('2', '8'),
('2', '14'),
('2', '15'),
('2', '16'),
('2', '17'),
('2', '18'),
('2', '19'),
('2', '24'),
('3', '1'),
('3', '5'),
('3', '7'),
('3', '9'),
('3', '10'),
('3', '11'),
('3', '12'),
('3', '13'),
('3', '18'),
('4', '1'),
('4', '5'),
('4', '7'),
('4', '9'),
('4', '12'),
('4', '14'),
('4', '18'),
('5', '5'),
('5', '9'),
('5', '12'),
('5', '14'),
('6', '5'),
('6', '9');

-- Seed: admin_roles
INSERT IGNORE INTO `admin_roles` (`admin_id`, `role_id`) VALUES
('1', '1'),
('2', '1');

-- STEP 4: Ensure Existing Employees Have Default Shift and Role
UPDATE `employees` SET `shift_id` = 1 WHERE `shift_id` IS NULL;
UPDATE `employees` SET `work_mode` = 'on_site' WHERE `work_mode` IS NULL;
UPDATE `employees` SET `role_id` = 2 WHERE `role_id` IS NULL;

SET FOREIGN_KEY_CHECKS = 1;
