<?php
/**
 * Environment File Loader & Constant Definition Layer
 * Loads configuration from .env and defines standard constants.
 */

if (!function_exists('cloudaspire_load_env')) {
    function cloudaspire_load_env(string $path): void {
        if (!file_exists($path) || !is_readable($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || (function_exists('str_starts_with') ? str_starts_with($line, '#') : substr($line, 0, 1) === '#')) {
                continue;
            }

            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value, " \t\n\r\0\x0B\"'");

                if (!array_key_exists($key, $_SERVER) && !array_key_exists($key, $_ENV)) {
                    putenv("$key=$value");
                    $_ENV[$key] = $value;
                    $_SERVER[$key] = $value;
                }
            }
        }
    }
}

cloudaspire_load_env(dirname(__DIR__) . '/.env');
cloudaspire_load_env(__DIR__ . '/.env');

// Define constants from env or fall back to defaults
if (!defined('DB_HOST')) define('DB_HOST', $_ENV['DB_HOST'] ?? (getenv('DB_HOST') ?: 'localhost'));
if (!defined('DB_NAME')) define('DB_NAME', $_ENV['DB_NAME'] ?? (getenv('DB_NAME') ?: 'qaschyan_emp'));
if (!defined('DB_USER')) define('DB_USER', $_ENV['DB_USER'] ?? (getenv('DB_USER') ?: 'qaschyan_emp'));
if (!defined('DB_PASS')) define('DB_PASS', $_ENV['DB_PASSWORD'] ?? ($_ENV['DB_PASS'] ?? (getenv('DB_PASSWORD') ?: (getenv('DB_PASS') ?: 'qaschyan_emp'))));
if (!defined('DB_CHARSET')) define('DB_CHARSET', 'utf8mb4');

if (!defined('SITE_URL')) define('SITE_URL', $_ENV['SITE_URL'] ?? (getenv('SITE_URL') ?: 'https://cloudaspire.in'));
if (!defined('SITE_NAME')) define('SITE_NAME', $_ENV['SITE_NAME'] ?? (getenv('SITE_NAME') ?: 'Cloud Aspire'));
if (!defined('ENCRYPTION_KEY')) define('ENCRYPTION_KEY', $_ENV['ENCRYPTION_KEY'] ?? (getenv('ENCRYPTION_KEY') ?: 'cloudaspire_secure_encryption_key_2026'));

if (!defined('SMTP_HOST')) define('SMTP_HOST', $_ENV['SMTP_HOST'] ?? (getenv('SMTP_HOST') ?: ''));
if (!defined('SMTP_PORT')) define('SMTP_PORT', (int)($_ENV['SMTP_PORT'] ?? (getenv('SMTP_PORT') ?: 587)));
if (!defined('SMTP_USER')) define('SMTP_USER', $_ENV['SMTP_USER'] ?? (getenv('SMTP_USER') ?: ''));
if (!defined('SMTP_PASS')) define('SMTP_PASS', $_ENV['SMTP_PASS'] ?? (getenv('SMTP_PASS') ?: ''));
if (!defined('SMTP_FROM')) define('SMTP_FROM', $_ENV['SMTP_FROM'] ?? (getenv('SMTP_FROM') ?: ''));
if (!defined('SMTP_FROM_NAME')) define('SMTP_FROM_NAME', $_ENV['SMTP_FROM_NAME'] ?? (getenv('SMTP_FROM_NAME') ?: 'Cloud Aspire'));

