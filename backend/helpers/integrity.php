<?php
/**
 * Integrity Score Calculator
 * Computes a 0-100 integrity score based on proctoring violation data.
 * Used by AssessmentController after exam completion.
 */

/**
 * Violation weights — points deducted per occurrence
 */
const INTEGRITY_WEIGHTS = [
    'tab_switch'       => ['per' => 5,  'max' => 30],
    'fullscreen_exit'  => ['per' => 8,  'max' => 24],
    'geo_change'       => ['per' => 25, 'max' => 25],
    'ip_change'        => ['per' => 20, 'max' => 20],
    'devtools'         => ['per' => 30, 'max' => 30],
    'fast_answer'      => ['per' => 3,  'max' => 15],
    'secondary_device' => ['per' => 35, 'max' => 35],
    'token_mismatch'   => ['per' => 40, 'max' => 40],
    'face_absent'      => ['per' => 8,  'max' => 32],
    'multiple_faces'   => ['per' => 15, 'max' => 45],
    'looking_away'     => ['per' => 5,  'max' => 25],
    'camera_denied'    => ['per' => 50, 'max' => 50],
    'phone_detected'   => ['per' => 20, 'max' => 60],
    'object_detected'  => ['per' => 10, 'max' => 30],
    'dead_zone'        => ['per' => 15, 'max' => 30],
    'low_mouse'        => ['per' => 8,  'max' => 24],
    'gaze_fixed'       => ['per' => 10, 'max' => 30],
    'window_resized'   => ['per' => 10, 'max' => 20],
    'hover_skip'       => ['per' => 12, 'max' => 24],
];

/**
 * Calculate integrity score from violation counts
 *
 * @param array $violations ['tab_switch' => 3, 'fullscreen_exit' => 1, ...]
 * @return int Score 0-100
 */
function calculateIntegrityScore(array $violations): int {
    $totalDeduction = 0;

    foreach ($violations as $type => $count) {
        if (!isset(INTEGRITY_WEIGHTS[$type])) continue;
        $weight = INTEGRITY_WEIGHTS[$type];
        $deduction = min($count * $weight['per'], $weight['max']);
        $totalDeduction += $deduction;
    }

    return max(0, 100 - $totalDeduction);
}

/**
 * Determine integrity label for display
 */
function getIntegrityLabel(int $score): array {
    if ($score >= 80) return ['label' => 'Clean',                     'color' => 'green',  'icon' => 'verified'];
    if ($score >= 60) return ['label' => 'Minor flags',               'color' => 'yellow', 'icon' => 'info'];
    if ($score >= 40) return ['label' => 'Flagged — review needed',   'color' => 'orange', 'icon' => 'warning'];
    return                   ['label' => 'Severe violations',         'color' => 'red',    'icon' => 'gpp_bad'];
}

/**
 * Check if attempt should be auto-flagged
 */
function shouldAutoFlag(int $score): bool {
    return $score < 60;
}

/**
 * Check if attempt should be force-submitted
 */
function shouldForceSubmit(int $score): bool {
    return $score < 30;
}

/**
 * Calculate distance between two GPS coordinates in meters (Haversine formula)
 */
function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float {
    $earthRadius = 6371000; // meters
    $dLat = deg2rad($lat2 - $lat1);
    $dLng = deg2rad($lng2 - $lng1);
    $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    return $earthRadius * $c;
}

/**
 * Build violation summary from attempt data for employer display
 */
function buildViolationSummary(array $attempt): array {
    $summary = [];
    $fields = [
        'tab_switches'        => 'Tab switches',
        'fullscreen_exits'    => 'Fullscreen exits',
        'face_absence_count'  => 'Face absent',
        'head_violations'     => 'Head position violations',
        'multiple_faces_count'=> 'Multiple faces detected',
        'phone_detections'    => 'Phone detected',
        'dead_zone_flags'     => 'Screen dead zones',
        'behavioral_flags'    => 'Behavioral anomalies',
    ];

    foreach ($fields as $key => $label) {
        if (isset($attempt[$key]) && $attempt[$key] > 0) {
            $summary[] = ['label' => $label, 'count' => $attempt[$key]];
        }
    }

    return $summary;
}
