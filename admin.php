<?php
// Set timezone to India / Local Time
date_default_timezone_set('Asia/Kolkata');

// Include database & security
require_once __DIR__ . '/app/db.php';
require_once __DIR__ . '/app/security.php';
Security::init($pdo);
Security::initSecureSession();

// Check if already logged in as admin
if (Security::isAuthenticated()) {
    header("Location: /admin/dashboard");
    exit();
}

// If logged in as regular employee without admin rights, redirect to employee dashboard
if (isset($_SESSION['employee_logged_in']) && $_SESSION['employee_logged_in'] === true) {
    header("Location: /employee_dashboard");
    exit();
}

// Redirect to the common unified role-based login page
header("Location: /login.php");
exit();
