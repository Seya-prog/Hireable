<?php
/**
 * Auth Middleware
 * Include at the top of any protected page.
 * Redirects to login if not authenticated.
 */

require_once __DIR__ . '/../helpers/session.php';

if (!isLoggedIn()) {
    setFlash('error', 'Please log in to access this page.');
    header('Location: ' . getLoginUrl());
    exit;
}

/**
 * Get login URL relative to current script
 */
function getLoginUrl(): string {
    // Calculate relative path from current script to pages/auth/login.php
    $scriptPath = dirname($_SERVER['SCRIPT_NAME']);
    $depth = substr_count(str_replace('\\', '/', $scriptPath), '/');
    return str_repeat('../', $depth) . 'pages/auth/login.php';
}
