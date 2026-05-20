<?php
/**
 * Device Lock Helper
 * Prevents cross-device cheating during assessments by binding
 * an attempt to a single session/device via HTTP-only cookie.
 */

require_once __DIR__ . '/../../config/database.php';

/**
 * Generate a cryptographically secure device token
 */
function generateDeviceToken(): string {
    return bin2hex(random_bytes(32));
}

/**
 * Set the device token as an HTTP-only cookie
 */
function setDeviceTokenCookie(string $token, int $expiresAt): void {
    setcookie('_dt', $token, [
        'expires'  => $expiresAt,
        'path'     => '/',
        'httponly'  => true,
        'samesite' => 'Strict',
        'secure'   => isset($_SERVER['HTTPS']),
    ]);
}

/**
 * Get device token from cookie
 */
function getDeviceToken(): ?string {
    return $_COOKIE['_dt'] ?? null;
}

/**
 * Create an assessment lock for the current user
 */
function createAssessmentLock(PDO $pdo, int $userId, int $attemptId, string $deviceToken, string $lockedUntil): void {
    $stmt = $pdo->prepare(
        'INSERT INTO assessment_locks (user_id, attempt_id, session_id, device_token, ip_address, locked_until)
         VALUES (?, ?, ?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE attempt_id=VALUES(attempt_id), session_id=VALUES(session_id),
                                  device_token=VALUES(device_token), ip_address=VALUES(ip_address),
                                  locked_until=VALUES(locked_until)'
    );
    $stmt->execute([
        $userId,
        $attemptId,
        session_id(),
        $deviceToken,
        $_SERVER['REMOTE_ADDR'] ?? '',
        $lockedUntil,
    ]);
}

/**
 * Check if a user has an active assessment lock
 * Returns the lock row or null
 */
function getAssessmentLock(PDO $pdo, int $userId): ?array {
    // Clean expired locks first
    $pdo->prepare('DELETE FROM assessment_locks WHERE locked_until < NOW()')->execute();

    $stmt = $pdo->prepare('SELECT * FROM assessment_locks WHERE user_id = ?');
    $stmt->execute([$userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

/**
 * Validate that the current request matches the locked device
 * Returns: true if valid, false if different device
 */
function validateDeviceAccess(PDO $pdo, int $userId): bool {
    $lock = getAssessmentLock($pdo, $userId);
    if (!$lock) {
        return true; // No lock = no restriction
    }

    $currentSession = session_id();
    $currentToken = getDeviceToken();

    // Check session match
    if ($lock['session_id'] !== $currentSession) {
        return false;
    }

    // Check device token match
    if ($currentToken && $lock['device_token'] !== $currentToken) {
        return false;
    }

    return true;
}

/**
 * Release the assessment lock for a user
 */
function releaseAssessmentLock(PDO $pdo, int $userId): void {
    $pdo->prepare('DELETE FROM assessment_locks WHERE user_id = ?')->execute([$userId]);
    // Clear the device token cookie
    setcookie('_dt', '', ['expires' => time() - 3600, 'path' => '/', 'httponly' => true]);
}

/**
 * Clean up all expired locks (safe to call from any context)
 */
function cleanExpiredLocks(PDO $pdo): void {
    $pdo->prepare('DELETE FROM assessment_locks WHERE locked_until < NOW()')->execute();
}
