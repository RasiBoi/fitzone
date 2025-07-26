<?php
/*
 * FitZone Fitness Center
 * Main Configuration File
 */

// Prevent direct script access
if (!defined('FITZONE_APP')) {
    define('FITZONE_APP', true);
}

// Database Configuration
define('DB_HOST', 'localhost');      // Database host
define('DB_NAME', 'fitzone_db');     // Database name
define('DB_USER', 'root');           // Database username
define('DB_PASS', '');               // Database password

// Site Configuration
define('SITE_NAME', 'FitZone Fitness Center');
define('SITE_URL', 'http://localhost/fitzone/');
define('ADMIN_EMAIL', 'admin@fitzone.com');

// File Paths
define('ROOT_PATH', dirname(__DIR__) . '/');
define('INCLUDES_PATH', ROOT_PATH . 'includes/');
define('ASSETS_PATH', ROOT_PATH . 'assets/');
define('UPLOADS_PATH', ASSETS_PATH . 'uploads/');

// Session Configuration
define('SESSION_NAME', 'fitzone_session');
define('SESSION_LIFETIME', 7200);    // 2 hours

// User Roles
define('ROLE_ADMIN', 'admin');
define('ROLE_STAFF', 'staff');
define('ROLE_MEMBER', 'member');
define('ROLE_GUEST', 'guest');

// Error Reporting
// Uncomment for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Comment for production
// error_reporting(0);
// ini_set('display_errors', 0);

// Time Zone
date_default_timezone_set('Asia/Colombo');

// Security
define('HASH_COST', 12);             // Password hashing cost