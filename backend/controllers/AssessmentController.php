<?php
/**
 * AssessmentController — Secure assessment lifecycle
 * Handles: create, start, submit, heartbeat, save-answer, snapshot
 */

require_once __DIR__ . '/../helpers/csrf.php';
require_once __DIR__ . '/../helpers/device_lock.php';
require_once __DIR__ . '/../helpers/integrity.php';

class AssessmentController {
    private AssessmentRepository $assessments;
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->assessments = new AssessmentRepository($pdo);
    }

    // ─── Employer: Create Assessment ────────────────────────

    public function create(): void {
        if (!isLoggedIn() || getCurrentUserRole() !== 'employer') {
            header('Location: ' . AUTH_URL . 'login.php');
            exit;
        }

        $title = sanitize(getPost('title'));
        if (empty($title)) {
            setFlash('error', 'Assessment title is required.');
            header('Location: ' . SHARED_URL . 'skill-assesment.php?tab=create');
            exit;
        }

        try {
            $this->pdo->beginTransaction();

            $assessmentId = $this->assessments->create(getCurrentUserId(), [
                'title'              => $title,
                'description'        => sanitize(getPost('description')),
                'difficulty'         => sanitize(getPost('difficulty')),
                'time_limit_minutes' => intval(getPost('time_limit_minutes')) ?: 45,
                'passing_score'      => intval(getPost('passing_score')) ?: 70,
                'job_id'             => intval(getPost('job_id')) ?: null,
                'status'             => sanitize(getPost('status')) ?: 'draft',
            ]);

            $questions = $_POST['questions'] ?? [];
            foreach ($questions as $i => $q) {
                if (empty(trim($q['text'] ?? ''))) continue;
                $this->assessments->addQuestion($assessmentId, $q, $i);
            }

            $this->pdo->commit();
            setFlash('success', "Assessment \"$title\" created successfully!");
            header('Location: ' . SHARED_URL . 'skill-assesment.php');
            exit;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            setFlash('error', 'Failed to create assessment.');
            header('Location: ' . SHARED_URL . 'skill-assesment.php?tab=create');
            exit;
        }
    }

    // ─── Employer: Update Assessment ────────────────────────

    public function update(): void {
        if (!isLoggedIn() || getCurrentUserRole() !== 'employer') {
            header('Location: ' . AUTH_URL . 'login.php'); exit;
        }

        $assessmentId = intval(getPost('assessment_id'));
        $assessment = $this->assessments->findById($assessmentId);

        if (!$assessment || $assessment['employer_id'] != getCurrentUserId()) {
            setFlash('error', 'Assessment not found.');
            header('Location: ' . SHARED_URL . 'skill-assesment.php'); exit;
        }

        $title = sanitize(getPost('title'));
        if (empty($title)) {
            setFlash('error', 'Assessment title is required.');
            header('Location: ' . EMPLOYER_URL . "assessment-edit.php?id=$assessmentId"); exit;
        }

        try {
            $this->pdo->beginTransaction();

            // Update assessment details
            $stmt = $this->pdo->prepare(
                'UPDATE assessments SET title=?, description=?, difficulty=?, time_limit_minutes=?, 
                 passing_score=?, job_id=?, status=?, updated_at=NOW() WHERE id=?'
            );
            $stmt->execute([
                $title,
                sanitize(getPost('description') ?? $assessment['description']),
                sanitize(getPost('difficulty')) ?: $assessment['difficulty'],
                intval(getPost('time_limit_minutes')) ?: $assessment['time_limit_minutes'],
                intval(getPost('passing_score')) ?: $assessment['passing_score'],
                intval(getPost('job_id')) ?: null,
                sanitize(getPost('status')) ?: $assessment['status'],
                $assessmentId,
            ]);

            // Delete old questions and re-insert
            $this->pdo->prepare('DELETE FROM assessment_questions WHERE assessment_id = ?')->execute([$assessmentId]);

            $questions = $_POST['questions'] ?? [];
            foreach ($questions as $i => $q) {
                if (empty(trim($q['text'] ?? ''))) continue;

                // Build correct_answer for MCQ
                if (($q['type'] ?? '') === 'multiple_choice' && isset($q['options'])) {
                    $correctIdx = intval($q['correct_answer_idx'] ?? 0);
                    $q['correct_answer'] = $q['options'][$correctIdx] ?? '';
                }

                $this->assessments->addQuestion($assessmentId, $q, $i);
            }

            $this->pdo->commit();
            setFlash('success', "Assessment \"$title\" updated successfully!");
            header('Location: ' . EMPLOYER_URL . "assessment-detail.php?id=$assessmentId"); exit;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            setFlash('error', 'Failed to update assessment.');
            header('Location: ' . EMPLOYER_URL . "assessment-edit.php?id=$assessmentId"); exit;
        }
    }

    // ─── Employee: Start Assessment (API — JSON) ────────────

    public function apiStart(): void {
        header('Content-Type: application/json');

        if (!isLoggedIn() || getCurrentUserRole() !== 'employee') {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $assessmentId = intval($input['assessment_id'] ?? 0);
        $employeeId = getCurrentUserId();

        if (!$assessmentId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Assessment ID required']);
            exit;
        }

        // Check assessment exists and is active
        $assessment = $this->assessments->findById($assessmentId);
        if (!$assessment || $assessment['status'] !== 'active') {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Assessment not found or inactive']);
            exit;
        }

        // One-attempt guard
        $existing = $this->assessments->hasExistingAttempt($assessmentId, $employeeId);
        if ($existing) {
            if ($existing['status'] === 'completed') {
                http_response_code(409);
                echo json_encode(['success' => false, 'error' => 'You have already completed this assessment']);
                exit;
            }
            if ($existing['status'] === 'in_progress') {
                // Resume existing attempt
                $attempt = $this->assessments->findActiveAttempt($existing['id'], $employeeId);
                if ($attempt && !$this->assessments->isAttemptExpired($existing['id'])) {
                    $questions = $this->assessments->getQuestionsForCandidate($assessmentId);
                    $savedAnswers = $this->assessments->getAnswers($existing['id']);
                    echo json_encode([
                        'success'           => true,
                        'resumed'           => true,
                        'attempt_id'        => $existing['id'],
                        'questions'         => $questions,
                        'saved_answers'     => $savedAnswers,
                        'remaining_seconds' => $this->assessments->getRemainingSeconds($existing['id']),
                    ]);
                    exit;
                }
            }
        }

        // Check cross-device lock
        $existingLock = getAssessmentLock($this->pdo, $employeeId);
        if ($existingLock) {
            http_response_code(409);
            echo json_encode(['success' => false, 'error' => 'You have an active assessment on another device']);
            exit;
        }

        try {
            $this->pdo->beginTransaction();

            // Generate device token
            $deviceToken = generateDeviceToken();

            // Get questions and create shuffled order
            $questions = $this->assessments->getQuestionsForCandidate($assessmentId);
            $questionIds = array_column($questions, 'id');
            shuffle($questionIds);

            // Shuffle MCQ options for each question
            foreach ($questions as &$q) {
                if ($q['question_type'] === 'multiple_choice' && !empty($q['options'])) {
                    $opts = json_decode($q['options'], true);
                    if (is_array($opts)) {
                        shuffle($opts);
                        $q['options'] = json_encode($opts);
                    }
                }
            }
            unset($q);

            // Reorder questions based on shuffle
            $orderMap = array_flip($questionIds);
            usort($questions, function ($a, $b) use ($orderMap) {
                return ($orderMap[$a['id']] ?? 0) - ($orderMap[$b['id']] ?? 0);
            });

            // Create attempt
            $attemptId = $this->assessments->createAttempt(
                $assessmentId,
                $employeeId,
                $assessment['time_limit_minutes'],
                [
                    'question_order'     => $questionIds,
                    'ip_address'         => $_SERVER['REMOTE_ADDR'] ?? '',
                    'session_id'         => session_id(),
                    'browser_fingerprint'=> $input['browser_fingerprint'] ?? '',
                    'device_token'       => $deviceToken,
                    'geo_lat'            => $input['geo_lat'] ?? null,
                    'geo_lng'            => $input['geo_lng'] ?? null,
                ]
            );

            // Create device lock
            $deadline = date('Y-m-d H:i:s', strtotime("+{$assessment['time_limit_minutes']} minutes"));
            createAssessmentLock($this->pdo, $employeeId, $attemptId, $deviceToken, $deadline);
            setDeviceTokenCookie($deviceToken, strtotime($deadline));

            // Set assessment mode in session
            setAssessmentMode($attemptId);

            $this->pdo->commit();

            echo json_encode([
                'success'           => true,
                'attempt_id'        => $attemptId,
                'device_token'      => $deviceToken,
                'questions'         => $questions,
                'remaining_seconds' => $assessment['time_limit_minutes'] * 60,
                'assessment'        => [
                    'title'       => $assessment['title'],
                    'description' => $assessment['description'],
                    'difficulty'  => $assessment['difficulty'],
                    'passing_score' => $assessment['passing_score'],
                ],
            ]);
            exit;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to start assessment']);
            exit;
        }
    }

    // ─── Employee: Auto-Save Answer (API — JSON) ────────────

    public function apiSaveAnswer(): void {
        header('Content-Type: application/json');

        if (!isLoggedIn() || getCurrentUserRole() !== 'employee') {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }

        // Validate device
        if (!validateDeviceAccess($this->pdo, getCurrentUserId())) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Device mismatch']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $attemptId  = intval($input['attempt_id'] ?? 0);
        $questionId = intval($input['question_id'] ?? 0);
        $answer     = $input['answer'] ?? '';

        if (!$attemptId || !$questionId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing parameters']);
            exit;
        }

        // Verify attempt ownership and active status
        $attempt = $this->assessments->findActiveAttempt($attemptId, getCurrentUserId());
        if (!$attempt) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Invalid attempt']);
            exit;
        }

        // Check expiry
        if ($this->assessments->isAttemptExpired($attemptId)) {
            $this->assessments->expireAttempt($attemptId);
            releaseAssessmentLock($this->pdo, getCurrentUserId());
            clearAssessmentMode();
            http_response_code(410);
            echo json_encode(['success' => false, 'error' => 'Time expired', 'expired' => true]);
            exit;
        }

        // Save without grading (grading happens on final submit)
        $this->assessments->saveRawAnswer($attemptId, $questionId, $answer);

        echo json_encode([
            'success'           => true,
            'remaining_seconds' => $this->assessments->getRemainingSeconds($attemptId),
        ]);
        exit;
    }

    // ─── Employee: Heartbeat (API — JSON) ───────────────────

    public function apiHeartbeat(): void {
        header('Content-Type: application/json');

        if (!isLoggedIn() || getCurrentUserRole() !== 'employee') {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }

        $employeeId = getCurrentUserId();

        // Validate device
        if (!validateDeviceAccess($this->pdo, $employeeId)) {
            // Log violation — different device trying to access
            $input = json_decode(file_get_contents('php://input'), true) ?? [];
            $attemptId = intval($input['attempt_id'] ?? 0);
            if ($attemptId) {
                $this->assessments->recordViolation($attemptId, 'secondary_device', 1, 'Heartbeat from different device');
            }
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Device mismatch', 'force_submit' => true]);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $attemptId = intval($input['attempt_id'] ?? 0);

        if (!$attemptId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing attempt_id']);
            exit;
        }

        // Verify attempt
        $attempt = $this->assessments->findActiveAttempt($attemptId, $employeeId);
        if (!$attempt) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Invalid attempt']);
            exit;
        }

        // Check expiry
        if ($this->assessments->isAttemptExpired($attemptId)) {
            $this->assessments->expireAttempt($attemptId);
            releaseAssessmentLock($this->pdo, $employeeId);
            clearAssessmentMode();
            echo json_encode(['success' => false, 'expired' => true, 'force_submit' => true]);
            exit;
        }

        // Record violations from client
        $violations = $input['violations'] ?? [];
        foreach ($violations as $v) {
            $type = $v['type'] ?? '';
            $count = intval($v['count'] ?? 1);
            $detail = $v['detail'] ?? null;
            if ($type) {
                $this->assessments->recordViolation($attemptId, $type, $count, $detail);
            }
        }

        // Check geo movement
        if (isset($input['geo_lat'], $input['geo_lng'])) {
            $distance = $this->assessments->updateGeo(
                $attemptId,
                floatval($input['geo_lat']),
                floatval($input['geo_lng'])
            );
            if ($distance > 500) {
                $this->assessments->recordViolation($attemptId, 'geo_change', 1, "Moved {$distance}m");
            }
        }

        // Calculate live integrity score
        $freshAttempt = $this->assessments->getAttemptById($attemptId);
        $violationCounts = [
            'tab_switch'      => $freshAttempt['tab_switches'] ?? 0,
            'fullscreen_exit' => $freshAttempt['fullscreen_exits'] ?? 0,
            'face_absent'     => $freshAttempt['face_absence_count'] ?? 0,
            'multiple_faces'  => $freshAttempt['multiple_faces_count'] ?? 0,
            'phone_detected'  => $freshAttempt['phone_detections'] ?? 0,
            'dead_zone'       => $freshAttempt['dead_zone_flags'] ?? 0,
            'behavioral'      => $freshAttempt['behavioral_flags'] ?? 0,
        ];
        $integrityScore = calculateIntegrityScore($violationCounts);
        $forceSubmit = shouldForceSubmit($integrityScore);

        echo json_encode([
            'success'           => true,
            'remaining_seconds' => $this->assessments->getRemainingSeconds($attemptId),
            'integrity_score'   => $integrityScore,
            'force_submit'      => $forceSubmit,
        ]);
        exit;
    }

    // ─── Employee: Save Snapshot (API — JSON) ───────────────

    public function apiSnapshot(): void {
        header('Content-Type: application/json');

        if (!isLoggedIn() || getCurrentUserRole() !== 'employee') {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }

        if (!validateDeviceAccess($this->pdo, getCurrentUserId())) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Device mismatch']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $attemptId = intval($input['attempt_id'] ?? 0);
        $imageData = $input['image'] ?? '';

        if (!$attemptId || empty($imageData)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing data']);
            exit;
        }

        // Decode base64 image
        $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $imageData);
        $imageBytes = base64_decode($imageData);
        if (!$imageBytes) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid image']);
            exit;
        }

        // Save to disk
        $dir = ROOT_DIR . '/storage/snapshots/' . $attemptId;
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $filename = 'snap_' . time() . '_' . bin2hex(random_bytes(4)) . '.jpg';
        $path = $dir . '/' . $filename;
        file_put_contents($path, $imageBytes);

        $relativePath = 'storage/snapshots/' . $attemptId . '/' . $filename;

        // Save record
        $snapshotId = $this->assessments->saveSnapshot($attemptId, $relativePath, [
            'faces_detected'   => intval($input['faces_detected'] ?? 1),
            'head_yaw'         => $input['head_yaw'] ?? null,
            'head_pitch'       => $input['head_pitch'] ?? null,
            'flag_type'        => $input['flag_type'] ?? 'clean',
            'detected_objects' => $input['detected_objects'] ?? null,
        ]);

        // If reference photo, set it
        if (!empty($input['is_reference'])) {
            $this->assessments->setReferencePhoto($attemptId, $relativePath);
        }

        echo json_encode(['success' => true, 'snapshot_id' => $snapshotId]);
        exit;
    }

    // ─── Employee: Final Submit ─────────────────────────────

    public function submit(): void {
        if (!isLoggedIn() || getCurrentUserRole() !== 'employee') {
            header('Location: ' . AUTH_URL . 'login.php');
            exit;
        }

        $employeeId   = getCurrentUserId();
        $attemptId    = intval(getPost('attempt_id'));
        $assessmentId = intval(getPost('assessment_id'));

        // Validate attempt ownership
        $attempt = $this->assessments->findActiveAttempt($attemptId, $employeeId);
        if (!$attempt) {
            setFlash('error', 'Assessment attempt not found or already completed.');
            header('Location: ' . EMPLOYEE_URL . 'applications.php');
            exit;
        }

        try {
            $this->pdo->beginTransaction();

            // Grade all answers server-side
            $questions = $this->assessments->getQuestions($assessmentId);
            $savedAnswers = $this->assessments->getAnswers($attemptId);
            $answerMap = [];
            foreach ($savedAnswers as $sa) {
                $answerMap[$sa['question_id']] = $sa['answer_text'];
            }

            $totalPoints = 0;
            $earnedPoints = 0;

            foreach ($questions as $q) {
                $userAnswer = $answerMap[$q['id']] ?? '';
                $isCorrect = strtolower(trim($userAnswer)) === strtolower(trim($q['correct_answer']));
                $pointsEarned = $isCorrect ? $q['points'] : 0;
                $totalPoints += $q['points'];
                $earnedPoints += $pointsEarned;

                // Update answer with grading results
                $this->assessments->saveAnswer($attemptId, $q['id'], $userAnswer, $isCorrect, $pointsEarned);
            }

            $score = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100, 2) : 0;

            // Calculate final integrity score
            $freshAttempt = $this->assessments->getAttemptById($attemptId);
            $violationCounts = [
                'tab_switch'      => $freshAttempt['tab_switches'] ?? 0,
                'fullscreen_exit' => $freshAttempt['fullscreen_exits'] ?? 0,
                'face_absent'     => $freshAttempt['face_absence_count'] ?? 0,
                'multiple_faces'  => $freshAttempt['multiple_faces_count'] ?? 0,
                'phone_detected'  => $freshAttempt['phone_detections'] ?? 0,
                'dead_zone'       => $freshAttempt['dead_zone_flags'] ?? 0,
                'behavioral'      => $freshAttempt['behavioral_flags'] ?? 0,
            ];
            $integrityScore = calculateIntegrityScore($violationCounts);

            // Complete with integrity
            $this->assessments->completeAttempt($attemptId, $score, $totalPoints, $integrityScore);

            // Release device lock
            releaseAssessmentLock($this->pdo, $employeeId);
            clearAssessmentMode();

            $this->pdo->commit();
            setFlash('success', "Assessment completed! Your score: $score%");
            header('Location: ' . EMPLOYEE_URL . "assessment-result.php?id=$attemptId");
            exit;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            setFlash('error', 'Failed to submit assessment.');
            header("Location: " . EMPLOYEE_URL . "take-assessment.php?id=$assessmentId");
            exit;
        }
    }

    // ─── Employee: Final Submit (API — JSON) ────────────────

    public function apiSubmit(): void {
        header('Content-Type: application/json');

        if (!isLoggedIn() || getCurrentUserRole() !== 'employee') {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }

        $employeeId = getCurrentUserId();

        if (!validateDeviceAccess($this->pdo, $employeeId)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Device mismatch']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $attemptId    = intval($input['attempt_id'] ?? 0);
        $assessmentId = intval($input['assessment_id'] ?? 0);

        $attempt = $this->assessments->findActiveAttempt($attemptId, $employeeId);
        if (!$attempt) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Invalid attempt']);
            exit;
        }

        try {
            $this->pdo->beginTransaction();

            $questions = $this->assessments->getQuestions($assessmentId);
            $savedAnswers = $this->assessments->getAnswers($attemptId);
            $answerMap = [];
            foreach ($savedAnswers as $sa) {
                $answerMap[$sa['question_id']] = $sa['answer_text'];
            }

            $totalPoints = 0;
            $earnedPoints = 0;

            foreach ($questions as $q) {
                $userAnswer = $answerMap[$q['id']] ?? '';
                $isCorrect = strtolower(trim($userAnswer)) === strtolower(trim($q['correct_answer']));
                $pointsEarned = $isCorrect ? $q['points'] : 0;
                $totalPoints += $q['points'];
                $earnedPoints += $pointsEarned;
                $this->assessments->saveAnswer($attemptId, $q['id'], $userAnswer, $isCorrect, $pointsEarned);
            }

            $score = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100, 2) : 0;

            $freshAttempt = $this->assessments->getAttemptById($attemptId);
            $violationCounts = [
                'tab_switch'      => $freshAttempt['tab_switches'] ?? 0,
                'fullscreen_exit' => $freshAttempt['fullscreen_exits'] ?? 0,
                'face_absent'     => $freshAttempt['face_absence_count'] ?? 0,
                'multiple_faces'  => $freshAttempt['multiple_faces_count'] ?? 0,
                'phone_detected'  => $freshAttempt['phone_detections'] ?? 0,
            ];
            $integrityScore = calculateIntegrityScore($violationCounts);

            $this->assessments->completeAttempt($attemptId, $score, $totalPoints, $integrityScore);
            releaseAssessmentLock($this->pdo, $employeeId);
            clearAssessmentMode();

            $this->pdo->commit();

            echo json_encode([
                'success'         => true,
                'score'           => $score,
                'total_points'    => $totalPoints,
                'earned_points'   => $earnedPoints,
                'integrity_score' => $integrityScore,
                'flagged'         => shouldAutoFlag($integrityScore),
                'redirect'        => EMPLOYEE_URL . "assessment-result.php?id=$attemptId",
            ]);
            exit;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Submission failed']);
            exit;
        }
    }
}
