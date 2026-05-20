<?php
/**
 * Input Validation Helpers
 */

/**
 * Sanitize a string input
 */
function sanitize(string $input): string {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email format
 */
function isValidEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate password strength
 * At least 8 chars, 1 uppercase, 1 lowercase, 1 number
 */
function isValidPassword(string $password): bool {
    return strlen($password) >= 8
        && preg_match('/[A-Z]/', $password)
        && preg_match('/[a-z]/', $password)
        && preg_match('/[0-9]/', $password);
}

/**
 * Validate required fields - returns array of missing field names
 */
function validateRequired(array $fields, array $data): array {
    $missing = [];
    foreach ($fields as $field) {
        if (!isset($data[$field]) || trim($data[$field]) === '') {
            $missing[] = $field;
        }
    }
    return $missing;
}

/**
 * Get POST value safely
 */
function getPost(string $key, $default = ''): string {
    return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
}
