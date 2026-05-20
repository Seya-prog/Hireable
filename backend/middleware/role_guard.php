<?php
/**
 * Role Guard Middleware
 * Include AFTER auth.php to restrict pages by role.
 * 
 * Usage:
 *   require_once __DIR__ . '/../backend/middleware/auth.php';
 *   $requiredRole = 'employer'; // or 'employee'
 *   require_once __DIR__ . '/../backend/middleware/role_guard.php';
 */

if (!isset($requiredRole)) {
    die('Role guard: $requiredRole must be set before including this file.');
}

$currentRole = getCurrentUserRole();

if ($currentRole !== $requiredRole) {
    setFlash('error', 'You do not have permission to access this page.');
    
    // Redirect to the correct dashboard based on role
    $scriptPath = dirname($_SERVER['SCRIPT_NAME']);
    $depth = substr_count(str_replace('\\', '/', $scriptPath), '/');
    $base = str_repeat('../', $depth);
    
    if ($currentRole === 'employer') {
        header('Location: ' . $base . 'pages/employer/dashboard.php');
    } else {
        header('Location: ' . $base . 'pages/employee/applications.php');
    }
    exit;
}
