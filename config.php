<?php

/**
 * ---------------------------------------------------------------
 * CONFIGURATION FILE
 * ---------------------------------------------------------------
 */

// --- Database Configuration ---
define('DB_HOST', 'localhost');
define('DB_USER', 'admin_nilayteamirnaaz');
define('DB_PASS', 'VJRhcun3xAL422hHVR6B');
define('DB_NAME', 'admin_nilayteamirnaaz');
define('DB_CHARSET', 'utf8mb4');

// --- Application Configuration ---
define('APP_URL', 'https://nilayteam.ir/irnaaz');
define('APP_ROOT', dirname(__FILE__));

// --- Directory Paths ---
define('PUBLIC_PATH', APP_ROOT . '/public');
define('APP_PATH', APP_ROOT . '/app');
define('TEMPLATES_PATH', APP_ROOT . '/templates');
define('LOG_PATH', APP_ROOT . '/logs'); // مسیر پوشه لاگ‌ها

// --- Session Configuration ---
define('SESSION_NAME', 'IRA_AZ_PANEL_SESSION');

// --- Error Reporting & Debugging ---
// E_ALL for development, 0 for production
error_reporting(E_ALL);

// Load and register the custom error handler
require_once APP_PATH . '/core/ErrorHandler.php';
App\Core\ErrorHandler::register();

?>
