<?php
/**
 * CSRF Protection Helper
 * Generates and validates tokens to prevent cross-site request forgery.
 */

/**
 * Generate a CSRF token and store in session
 */
function generateCsrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Output a hidden CSRF input field for forms
 */
function csrfField(): string {
    $token = generateCsrfToken();
    return '<input type="hidden" name="_csrf" value="' . htmlspecialchars($token) . '">';
}

/**
 * Validate a submitted CSRF token against the session token
 */
function validateCsrfToken(?string $token): bool {
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Regenerate the CSRF token (call after successful validation)
 */
function regenerateCsrfToken(): string {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}
