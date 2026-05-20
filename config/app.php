<?php
/**
 * App Configuration
 * Central constants for paths, URLs, and app settings.
 */

// Root directory (one level up from config/)
define('ROOT_DIR', dirname(__DIR__));

// Base URL (adjust if app is in a subdirectory)
define('BASE_URL', '/');

// Clean URLs via .htaccess
define('ACTION_URL', '/action/');    // Form POST targets
define('API_URL', '/api/');          // AJAX JSON endpoints

// Asset URLs (served from public/)
define('ASSETS_URL', '/public/assets');
define('CSS_URL', ASSETS_URL . '/css');
define('JS_URL', ASSETS_URL . '/js');

// Page URLs
define('AUTH_URL', '/pages/auth/');
define('EMPLOYEE_URL', '/pages/employee/');
define('EMPLOYER_URL', '/pages/employer/');
define('SHARED_URL', '/pages/shared/');
