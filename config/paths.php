<?php
/**
 * Global Path Configuration
 * 
 * Include this file at the top of every page to get
 * consistent path constants throughout the application.
 * 
 * Usage:
 *   require_once __DIR__ . '/../config/paths.php';   // from pages/category/
 *   require_once __DIR__ . '/config/paths.php';       // from root index.php
 */

// Absolute filesystem root of the project
define('ROOT_PATH', dirname(__DIR__));

// Component directories
define('COMPONENTS_PATH', ROOT_PATH . '/components');
define('SHARED_COMPONENTS',   COMPONENTS_PATH . '/shared');
define('EMPLOYEE_COMPONENTS', COMPONENTS_PATH . '/employee');
define('EMPLOYER_COMPONENTS', COMPONENTS_PATH . '/employer');

// Base URL for href links (works with php -S and Apache)
// Detects if we're behind a subdirectory automatically
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
$baseUrl = ($scriptDir === '/' || $scriptDir === '\\') ? '/' : rtrim($scriptDir, '/\\') . '/';

// Walk up to project root from wherever the script lives
// e.g. /pages/employee/profile.php → base is /
$depth = substr_count(str_replace('\\', '/', $_SERVER['SCRIPT_NAME']), '/') - 1;
$baseUrl = str_repeat('../', $depth);

define('BASE_URL', $baseUrl);

// Convenience: asset paths for use in HTML
define('CSS_URL',    BASE_URL . 'assets/css/');
define('IMG_URL',    BASE_URL . 'assets/images/');
define('JS_URL',     BASE_URL . 'assets/js/');

// Page URL helpers
define('AUTH_PAGES',     BASE_URL . 'pages/auth/');
define('EMPLOYEE_PAGES', BASE_URL . 'pages/employee/');
define('EMPLOYER_PAGES', BASE_URL . 'pages/employer/');
define('SHARED_PAGES',   BASE_URL . 'pages/shared/');
