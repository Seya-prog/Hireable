<?php
/**
 * AssessmentRepository — All assessment-related database queries
 * Includes secure methods for proctored exam flow.
 */
class AssessmentRepository {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // ─── Assessment CRUD ────────────────────────────────────

    public function create(int $employerId, array $data): int {
        $stmt = $this->pdo->prepare(
            'INSERT INTO assessments (employer_id, job_id, title, description, difficulty,
                                     time_limit_minutes, passing_score, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $employerId, $data['job_id'] ?? null, $data['title'],
            $data['description'] ?? null, $data['difficulty'] ?? null,
            $data['time_limit_minutes'] ?? 45, $data['passing_score'] ?? 70,
            $data['status'] ?? 'draft'
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function addQuestion(int $assessmentId, array $q, int $sortOrder): int {
        $stmt = $this->pdo->prepare(
            'INSERT INTO assessment_questions (assessment_id, question_type, question_text, options,
                                              correct_answer, points, sort_order)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $options = isset($q['options']) ? json_encode($q['options']) : null;
        $stmt->execute([
            $assessmentId, $q['type'] ?? 'multiple_choice', $q['text'],
            $options, $q['correct_answer'] ?? '', $q['points'] ?? 10, $sortOrder
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function findById(int $id): ?array {
        $stmt = $this->pdo->prepare('SELECT * FROM assessments WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function findByEmployer(int $employerId): array {
        $stmt = $this->pdo->prepare('SELECT * FROM assessments WHERE employer_id = ? ORDER BY created_at DESC');
        $stmt->execute([$employerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ─── Questions ──────────────────────────────────────────

    /**
     * Get all questions WITH correct answers (server-side grading only)
     */
    public function getQuestions(int $assessmentId): array {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM assessment_questions WHERE assessment_id = ? ORDER BY sort_order'
        );
        $stmt->execute([$assessmentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get questions WITHOUT correct answers (safe to send to candidate)
     */
    public function getQuestionsForCandidate(int $assessmentId): array {
        $stmt = $this->pdo->prepare(
            'SELECT id, assessment_id, question_type, question_text, options, points, sort_order
             FROM assessment_questions WHERE assessment_id = ? ORDER BY sort_order'
        );
        $stmt->execute([$assessmentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ─── Attempts (Secure) ──────────────────────────────────

    /**
     * Create a new attempt with all security metadata
     */
    public function createAttempt(int $assessmentId, int $employeeId, int $timeLimitMinutes, array $meta): int {
        $stmt = $this->pdo->prepare(
            'INSERT INTO assessment_attempts
             (assessment_id, employee_id, deadline_at, question_order, ip_address,
              session_id, browser_fingerprint, device_token, geo_lat, geo_lng)
             VALUES (?, ?, DATE_ADD(NOW(), INTERVAL ? MINUTE), ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $assessmentId,
            $employeeId,
            $timeLimitMinutes,
            json_encode($meta['question_order'] ?? []),
            $meta['ip_address'] ?? '',
            $meta['session_id'] ?? '',
            $meta['browser_fingerprint'] ?? '',
            $meta['device_token'] ?? '',
            $meta['geo_lat'] ?? null,
            $meta['geo_lng'] ?? null,
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Check if user already has a completed or in-progress attempt
     */
    public function hasExistingAttempt(int $assessmentId, int $employeeId): ?array {
        $stmt = $this->pdo->prepare(
            'SELECT id, status FROM assessment_attempts
             WHERE assessment_id = ? AND employee_id = ?
             ORDER BY started_at DESC LIMIT 1'
        );
        $stmt->execute([$assessmentId, $employeeId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Find an active (in_progress) attempt for this user
     */
    public function findActiveAttempt(int $attemptId, int $employeeId): ?array {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM assessment_attempts
             WHERE id = ? AND employee_id = ? AND status = "in_progress"'
        );
        $stmt->execute([$attemptId, $employeeId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Get an attempt with full details (for employer review)
     */
    public function getAttemptById(int $attemptId): ?array {
        $stmt = $this->pdo->prepare('SELECT * FROM assessment_attempts WHERE id = ?');
        $stmt->execute([$attemptId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Check if attempt has expired based on deadline_at
     */
    public function isAttemptExpired(int $attemptId): bool {
        $stmt = $this->pdo->prepare(
            'SELECT (deadline_at < NOW()) AS expired FROM assessment_attempts WHERE id = ?'
        );
        $stmt->execute([$attemptId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (bool) $row['expired'] : true;
    }

    /**
     * Get remaining seconds for the attempt timer
     */
    public function getRemainingSeconds(int $attemptId): int {
        $stmt = $this->pdo->prepare(
            'SELECT GREATEST(0, TIMESTAMPDIFF(SECOND, NOW(), deadline_at)) AS remaining
             FROM assessment_attempts WHERE id = ?'
        );
        $stmt->execute([$attemptId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int) $row['remaining'] : 0;
    }

    // ─── Answers ────────────────────────────────────────────

    /**
     * Save or update a single answer (auto-save)
     */
    public function saveAnswer(int $attemptId, int $questionId, string $answer, bool $isCorrect, int $points): void {
        $stmt = $this->pdo->prepare(
            'INSERT INTO assessment_answers (attempt_id, question_id, answer_text, is_correct, points_earned)
             VALUES (?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE answer_text=VALUES(answer_text), is_correct=VALUES(is_correct),
                                     points_earned=VALUES(points_earned), answered_at=NOW()'
        );
        $stmt->execute([$attemptId, $questionId, $answer, $isCorrect ? 1 : 0, $points]);
    }

    /**
     * Save a raw answer without grading (for auto-save during exam)
     */
    public function saveRawAnswer(int $attemptId, int $questionId, string $answer): void {
        $stmt = $this->pdo->prepare(
            'INSERT INTO assessment_answers (attempt_id, question_id, answer_text)
             VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE answer_text=VALUES(answer_text), answered_at=NOW()'
        );
        $stmt->execute([$attemptId, $questionId, $answer]);
    }

    /**
     * Get all saved answers for an attempt
     */
    public function getAnswers(int $attemptId): array {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM assessment_answers WHERE attempt_id = ? ORDER BY question_id'
        );
        $stmt->execute([$attemptId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ─── Completion & Grading ───────────────────────────────

    /**
     * Complete an attempt: set score, status, and integrity
     */
    public function completeAttempt(int $attemptId, float $score, int $totalPoints, int $integrityScore): bool {
        $flagged = $integrityScore < 60 ? 1 : 0;
        return $this->pdo->prepare(
            'UPDATE assessment_attempts
             SET score = ?, total_points = ?, status = "completed", completed_at = NOW(),
                 time_taken_minutes = TIMESTAMPDIFF(MINUTE, started_at, NOW()),
                 integrity_score = ?, flagged = ?
             WHERE id = ?'
        )->execute([$score, $totalPoints, $integrityScore, $flagged, $attemptId]);
    }

    /**
     * Mark attempt as expired
     */
    public function expireAttempt(int $attemptId): bool {
        return $this->pdo->prepare(
            'UPDATE assessment_attempts SET status = "expired", completed_at = NOW() WHERE id = ?'
        )->execute([$attemptId]);
    }

    // ─── Proctoring Violations ──────────────────────────────

    /**
     * Increment a violation counter and append to violation log
     */
    public function recordViolation(int $attemptId, string $type, int $count = 1, ?string $detail = null): void {
        // Map violation type to DB column
        $columnMap = [
            'tab_switch'      => 'tab_switches',
            'fullscreen_exit' => 'fullscreen_exits',
            'face_absent'     => 'face_absence_count',
            'head_violation'  => 'head_violations',
            'multiple_faces'  => 'multiple_faces_count',
            'phone_detected'  => 'phone_detections',
            'dead_zone'       => 'dead_zone_flags',
            'behavioral'      => 'behavioral_flags',
        ];

        $column = $columnMap[$type] ?? null;
        if ($column) {
            $this->pdo->prepare(
                "UPDATE assessment_attempts SET {$column} = {$column} + ? WHERE id = ?"
            )->execute([$count, $attemptId]);
        }

        // Append to violation log JSON
        $logEntry = json_encode([
            'type'      => $type,
            'count'     => $count,
            'detail'    => $detail,
            'timestamp' => date('c'),
        ]);
        $this->pdo->prepare(
            "UPDATE assessment_attempts
             SET violation_log = JSON_ARRAY_APPEND(COALESCE(violation_log, '[]'), '$', CAST(? AS JSON))
             WHERE id = ?"
        )->execute([$logEntry, $attemptId]);
    }

    /**
     * Update geo position and check for movement
     */
    public function updateGeo(int $attemptId, float $lat, float $lng): float {
        $stmt = $this->pdo->prepare('SELECT geo_lat, geo_lng FROM assessment_attempts WHERE id = ?');
        $stmt->execute([$attemptId]);
        $attempt = $stmt->fetch(PDO::FETCH_ASSOC);

        $distance = 0;
        if ($attempt && $attempt['geo_lat'] && $attempt['geo_lng']) {
            require_once __DIR__ . '/../../backend/helpers/integrity.php';
            $distance = haversineDistance($attempt['geo_lat'], $attempt['geo_lng'], $lat, $lng);
        }

        return $distance;
    }

    // ─── Snapshots ──────────────────────────────────────────

    /**
     * Save a webcam snapshot record
     */
    public function saveSnapshot(int $attemptId, string $photoPath, array $meta): int {
        $stmt = $this->pdo->prepare(
            'INSERT INTO assessment_snapshots
             (attempt_id, photo_path, faces_detected, head_yaw, head_pitch, flag_type, detected_objects)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $attemptId,
            $photoPath,
            $meta['faces_detected'] ?? 1,
            $meta['head_yaw'] ?? null,
            $meta['head_pitch'] ?? null,
            $meta['flag_type'] ?? 'clean',
            isset($meta['detected_objects']) ? json_encode($meta['detected_objects']) : null,
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Get all snapshots for an attempt (employer review)
     */
    public function getSnapshots(int $attemptId): array {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM assessment_snapshots WHERE attempt_id = ? ORDER BY captured_at'
        );
        $stmt->execute([$attemptId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Set the reference photo path for an attempt
     */
    public function setReferencePhoto(int $attemptId, string $path): void {
        $this->pdo->prepare(
            'UPDATE assessment_attempts SET reference_photo_path = ? WHERE id = ?'
        )->execute([$path, $attemptId]);
    }

    // ─── Employee-facing queries ────────────────────────────

    /**
     * Assessments available for an employee (active assessments they haven't completed)
     */
    public function findAvailableForEmployee(int $employeeId): array {
        $stmt = $this->pdo->prepare(
            'SELECT a.*, u.company_name,
                    (SELECT COUNT(*) FROM assessment_attempts att WHERE att.assessment_id = a.id AND att.employee_id = ? AND att.status = "completed") AS completed_count
             FROM assessments a
             JOIN users u ON a.employer_id = u.id
             WHERE a.status = "active"
             HAVING completed_count = 0
             ORDER BY a.created_at DESC'
        );
        $stmt->execute([$employeeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Assessments the employee has completed
     */
    public function findCompletedByEmployee(int $employeeId): array {
        $stmt = $this->pdo->prepare(
            'SELECT a.*, u.company_name, att.id AS attempt_id, att.score, att.completed_at, att.status AS attempt_status
             FROM assessment_attempts att
             JOIN assessments a ON att.assessment_id = a.id
             JOIN users u ON a.employer_id = u.id
             WHERE att.employee_id = ? AND att.status = "completed"
             ORDER BY att.completed_at DESC'
        );
        $stmt->execute([$employeeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Assessments the employee has started but not completed
     */
    public function findInProgressByEmployee(int $employeeId): array {
        $stmt = $this->pdo->prepare(
            'SELECT a.*, u.company_name, att.id AS attempt_id, att.started_at,
                    (SELECT COUNT(*) FROM assessment_questions q WHERE q.assessment_id = a.id) AS total_questions,
                    (SELECT COUNT(*) FROM assessment_answers ans WHERE ans.attempt_id = att.id) AS answered_count
             FROM assessment_attempts att
             JOIN assessments a ON att.assessment_id = a.id
             JOIN users u ON a.employer_id = u.id
             WHERE att.employee_id = ? AND att.status = "in_progress"
             ORDER BY att.started_at DESC'
        );
        $stmt->execute([$employeeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Employer: assessments with attempt/completion stats
     */
    public function findByEmployerWithStats(int $employerId): array {
        $stmt = $this->pdo->prepare(
            'SELECT a.*,
                    (SELECT COUNT(*) FROM assessment_questions aq WHERE aq.assessment_id = a.id) AS total_questions,
                    (SELECT COUNT(*) FROM assessment_attempts att WHERE att.assessment_id = a.id) AS total_attempts,
                    (SELECT COUNT(*) FROM assessment_attempts att WHERE att.assessment_id = a.id AND att.status = "completed") AS completed_attempts,
                    (SELECT ROUND(AVG(att.score),1) FROM assessment_attempts att WHERE att.assessment_id = a.id AND att.status = "completed") AS avg_score
             FROM assessments a
             WHERE a.employer_id = ?
             ORDER BY a.created_at DESC'
        );
        $stmt->execute([$employerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get completed attempts for a specific assessment (employer results view)
     */
    public function getAttemptsByAssessment(int $assessmentId): array {
        $stmt = $this->pdo->prepare(
            'SELECT att.*, u.first_name, u.last_name, u.email
             FROM assessment_attempts att
             JOIN users u ON att.employee_id = u.id
             WHERE att.assessment_id = ?
             ORDER BY att.completed_at DESC'
        );
        $stmt->execute([$assessmentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
