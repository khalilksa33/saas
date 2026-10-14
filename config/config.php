<?php
/**
 * Application Configuration
 * Compatible with Localhost & Hostinger Shared Web Hosting
 */

// Load .env file if available
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $val) = explode('=', $line, 2);
            $key = trim($key);
            $val = trim($val, " \t\n\r\0\x0B\"'");
            if (!getenv($key)) {
                putenv("$key=$val");
                $_ENV[$key] = $val;
            }
        }
    }
}

// App Settings
define('APP_NAME', getenv('APP_NAME') ?: 'iLoveDigital');
define('APP_TAGLINE', 'All-in-One Growth & Content Studio for Digital Creators');
define('APP_VERSION', '1.0.0');
define('APP_ENV', getenv('APP_ENV') ?: 'production'); // 'production' or 'development'

// Google Tag Manager & Google Analytics 4 (khalilksa33@gmail.com)
define('GTM_ID', getenv('GTM_ID') ?: '');
define('GA_MEASUREMENT_ID', getenv('GA_MEASUREMENT_ID') ?: '');

// Production error reporting and logging
if (APP_ENV === 'production') {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
} else {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

// Determine Base URL dynamically or from env
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'ilovedigital.online';
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$basePath = ($scriptDir === '/' || $scriptDir === '') ? '' : rtrim($scriptDir, '/');
$computedBaseUrl = $protocol . $host . $basePath;

define('APP_URL', rtrim(getenv('APP_URL') ?: $computedBaseUrl, '/'));
define('BASE_PATH', $basePath);

// Google OAuth 2.0 Credentials (Sign in with Google / Gmail)
define('GOOGLE_CLIENT_ID', getenv('GOOGLE_CLIENT_ID') ?: '');
define('GOOGLE_CLIENT_SECRET', getenv('GOOGLE_CLIENT_SECRET') ?: '');
define('GOOGLE_REDIRECT_URI', APP_URL . '/auth/google/callback');

// Database Settings
// Supported: 'sqlite' or 'mysql'
define('DB_CONNECTION', getenv('DB_CONNECTION') ?: 'sqlite');
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_DATABASE', getenv('DB_DATABASE') ?: __DIR__ . '/../data/database.sqlite');
define('DB_USERNAME', getenv('DB_USERNAME') ?: 'root');
define('DB_PASSWORD', getenv('DB_PASSWORD') ?: '');

// Session lifetime (7 days)
define('SESSION_LIFETIME', 60 * 60 * 24 * 7);

// Timezone
date_default_timezone_set(getenv('APP_TIMEZONE') ?: 'UTC');
