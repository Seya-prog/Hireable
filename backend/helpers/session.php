<?php
/**
 * Session Helper
 * Start/manage sessions and provide user access functions.
 * Include at the top of any file that needs session data.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 */
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

/**
 * Get current user's ID
 */
function getCurrentUserId(): ?int {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user's role
 */
function getCurrentUserRole(): ?string {
    return $_SESSION['user_role'] ?? null;
}

/**
 * Get current user's full name
 */
function getCurrentUserName(): string {
    return ($_SESSION['user_first_name'] ?? '') . ' ' . ($_SESSION['user_last_name'] ?? '');
}

/**
 * Get current user's initials (for avatar)
 */
function getCurrentUserInitials(): string {
    $first = $_SESSION['user_first_name'][0] ?? '';
    $last = $_SESSION['user_last_name'][0] ?? '';
    return strtoupper($first . $last);
}

/**
 * Get a specific session value
 */
function getSessionValue(string $key, $default = null) {
    return $_SESSION[$key] ?? $default;
}

/**
 * Set session data after login
 */
function setUserSession(array $user): void {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_first_name'] = $user['first_name'];
    $_SESSION['user_last_name'] = $user['last_name'];
    $_SESSION['user_profile_photo'] = $user['profile_photo'] ?? null;
    $_SESSION['user_company_name'] = $user['company_name'] ?? null;
}

/**
 * Destroy session (logout)
 */
function destroySession(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

/**
 * Set a flash message (shown once on next page load)
 */
function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/**
 * Get and clear flash message
 */
function getFlash(): ?array {
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

/**
 * Check if current user is in assessment mode (has an active lock)
 */
function isInAssessmentMode(): bool {
    return !empty($_SESSION['assessment_attempt_id']);
}

/**
 * Set assessment mode in session
 */
function setAssessmentMode(int $attemptId): void {
    $_SESSION['assessment_attempt_id'] = $attemptId;
}

/**
 * Clear assessment mode from session
 */
function clearAssessmentMode(): void {
    unset($_SESSION['assessment_attempt_id']);
}

/**
 * Get current assessment attempt ID
 */
function getAssessmentAttemptId(): ?int {
    return $_SESSION['assessment_attempt_id'] ?? null;
}
