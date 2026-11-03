<?php
/**
 * CloudASPIRE Database Connection Layer
 * Pure, high-performance PDO connection setup.
 * Bridges legacy $conn variable with unified app/db.php PDO instance.
 */

require_once __DIR__ . '/db.php';

if (!isset($conn) && isset($pdo)) {
    $conn = $pdo;
}
if (!isset($pdo) && isset($conn)) {
    $pdo = $conn;
}

