<?php
/**
 * Assessment Result — Score display for candidates
 * Entry: ?id={attempt_id}
 */
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../backend/helpers/session.php';
require_once __DIR__ . '/../../database/repositories/AssessmentRepository.php';

if (!isLoggedIn() || getCurrentUserRole() !== 'employee') {
    header('Location: ' . AUTH_URL . 'login.php');
    exit;
}

$assessmentRepo = new AssessmentRepository($pdo);
$attemptId = intval($_GET['id'] ?? 0);
$attempt = $assessmentRepo->getAttemptById($attemptId);

if (!$attempt || $attempt['employee_id'] != getCurrentUserId()) {
    setFlash('error', 'Assessment result not found.');
    header('Location: ' . EMPLOYEE_URL . 'applications.php');
    exit;
}

$assessment = $assessmentRepo->findById($attempt['assessment_id']);
$answers = $assessmentRepo->getAnswers($attemptId);
$questions = $assessmentRepo->getQuestionsForCandidate($attempt['assessment_id']);
$totalQuestions = count($questions);
$answeredCount = count($answers);
$correctCount = 0;
foreach ($answers as $a) {
    if ($a['is_correct']) $correctCount++;
}

$score = floatval($attempt['score'] ?? 0);
$passingScore = intval($assessment['passing_score'] ?? 70);
$passed = $score >= $passingScore;
$timeTaken = intval($attempt['time_taken_minutes'] ?? 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment Result | Hireable</title>
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,wght@0,400;0,500;0,700;1,400&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= CSS_URL ?>/global.css">
    <style>
        body { background: #0f172a; color: #f9fafb; margin: 0; font-family: 'Manrope', sans-serif; }
        .result-container { max-width: 720px; margin: 0 auto; padding: 40px 24px 80px; }
        .result-back { display: inline-flex; align-items: center; gap: 6px; color: #9ca3af; font-size: 0.85rem; text-decoration: none; margin-bottom: 32px; }
        .result-back:hover { color: #e5e7eb; }
        .result-hero { text-align: center; margin-bottom: 48px; }
        .result-icon-circle {
            width: 100px; height: 100px; border-radius: 50%; display: inline-flex;
            align-items: center; justify-content: center; margin-bottom: 20px;
        }
        .result-icon-circle.passed { background: rgba(16,185,129,0.15); }
        .result-icon-circle.failed { background: rgba(239,68,68,0.15); }
        .result-icon-circle .material-symbols-outlined { font-size: 48px; font-variation-settings: 'FILL' 1; }
        .result-icon-circle.passed .material-symbols-outlined { color: #10b981; }
        .result-icon-circle.failed .material-symbols-outlined { color: #ef4444; }
        .result-title { font-family: 'Newsreader', serif; font-size: 2rem; font-weight: 700; margin: 0 0 8px; }
        .result-subtitle { color: #9ca3af; font-size: 0.95rem; margin: 0; }
        .result-score-ring { position: relative; width: 180px; height: 180px; margin: 32px auto; }
        .result-score-ring svg { transform: rotate(-90deg); }
        .result-score-ring circle { fill: none; stroke-width: 10; }
        .result-score-ring .ring-bg { stroke: #1f2937; }
        .result-score-ring .ring-fg { stroke-linecap: round; transition: stroke-dashoffset 1.5s ease; }
        .ring-fg.passed { stroke: #10b981; }
        .ring-fg.failed { stroke: #ef4444; }
        .result-score-value {
            position: absolute; inset: 0; display: flex; flex-direction: column;
            align-items: center; justify-content: center;
        }
        .result-score-number { font-family: 'Newsreader', serif; font-size: 3rem; font-weight: 700; line-height: 1; }
        .result-score-label { font-size: 0.75rem; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.1em; }
        .result-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 40px; }
        .result-stat { background: #1f2937; border: 1px solid #374151; border-radius: 12px; padding: 20px 16px; text-align: center; }
        .result-stat-value { font-family: 'Newsreader', serif; font-size: 1.5rem; font-weight: 700; color: #f9fafb; margin-bottom: 4px; }
        .result-stat-label { font-size: 0.65rem; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.1em; }
        .result-breakdown-title {
            font-family: 'Newsreader', serif; font-size: 1.25rem; font-weight: 700;
            margin: 0 0 20px; padding-bottom: 12px; border-bottom: 1px solid #1f2937;
        }
        .result-q-list { display: flex; flex-direction: column; gap: 12px; }
        .result-q-item {
            display: flex; align-items: center; gap: 14px; padding: 16px 20px;
            background: #1f2937; border: 1px solid #374151; border-radius: 10px;
        }
        .result-q-icon { font-size: 22px; flex-shrink: 0; }
        .result-q-icon.correct { color: #10b981; }
        .result-q-icon.wrong { color: #ef4444; }
        .result-q-icon.skipped { color: #6b7280; }
        .result-q-text { flex: 1; font-size: 0.9rem; color: #e5e7eb; line-height: 1.4; }
        .result-q-points { font-size: 0.8rem; font-weight: 700; color: #9ca3af; white-space: nowrap; }
        .result-cta { display: flex; justify-content: center; margin-top: 40px; }
        .result-cta a {
            display: inline-flex; align-items: center; gap: 8px; padding: 14px 32px;
            background: linear-gradient(135deg, #10b981, #059669); color: #fff;
            border-radius: 10px; font-weight: 600; text-decoration: none; transition: opacity 0.2s;
        }
        .result-cta a:hover { opacity: 0.9; }
        @media (max-width: 600px) { .result-stats { grid-template-columns: repeat(2, 1fr); } }
    </style>
</head>
<body>
    <div class="result-container">
        <a href="<?= SHARED_URL ?>skill-assesment.php" class="result-back">
            <span class="material-symbols-outlined" style="font-size:18px">arrow_back</span> Back to Assessments
        </a>

        <div class="result-hero">
            <div class="result-icon-circle <?= $passed ? 'passed' : 'failed' ?>">
                <span class="material-symbols-outlined"><?= $passed ? 'check_circle' : 'cancel' ?></span>
            </div>
            <h1 class="result-title"><?= $passed ? 'Assessment Passed!' : 'Assessment Not Passed' ?></h1>
            <p class="result-subtitle"><?= htmlspecialchars($assessment['title']) ?></p>
        </div>

        <?php
            $circumference = 2 * M_PI * 80;
            $dashOffset = $circumference - ($score / 100) * $circumference;
        ?>
        <div class="result-score-ring">
            <svg width="180" height="180" viewBox="0 0 180 180">
                <circle class="ring-bg" cx="90" cy="90" r="80" />
                <circle class="ring-fg <?= $passed ? 'passed' : 'failed' ?>" cx="90" cy="90" r="80"
                    stroke-dasharray="<?= $circumference ?>" stroke-dashoffset="<?= $dashOffset ?>" />
            </svg>
            <div class="result-score-value">
                <span class="result-score-number"><?= round($score) ?>%</span>
                <span class="result-score-label">Your Score</span>
            </div>
        </div>

        <div class="result-stats">
            <div class="result-stat">
                <div class="result-stat-value"><?= $correctCount ?>/<?= $totalQuestions ?></div>
                <div class="result-stat-label">Correct</div>
            </div>
            <div class="result-stat">
                <div class="result-stat-value"><?= $timeTaken ?> min</div>
                <div class="result-stat-label">Time Taken</div>
            </div>
            <div class="result-stat">
                <div class="result-stat-value"><?= $passingScore ?>%</div>
                <div class="result-stat-label">Passing Score</div>
            </div>
            <div class="result-stat">
                <div class="result-stat-value"><?= $answeredCount ?>/<?= $totalQuestions ?></div>
                <div class="result-stat-label">Answered</div>
            </div>
        </div>

        <h3 class="result-breakdown-title">Question Breakdown</h3>
        <div class="result-q-list">
            <?php
            $answerMap = [];
            foreach ($answers as $a) { $answerMap[$a['question_id']] = $a; }
            foreach ($questions as $idx => $q):
                $ans = $answerMap[$q['id']] ?? null;
                if (!$ans) { $status = 'skipped'; $icon = 'remove_circle'; }
                elseif ($ans['is_correct']) { $status = 'correct'; $icon = 'check_circle'; }
                else { $status = 'wrong'; $icon = 'cancel'; }
                $pts = $ans ? intval($ans['points_earned']) : 0;
            ?>
            <div class="result-q-item">
                <span class="material-symbols-outlined result-q-icon <?= $status ?>" style="font-variation-settings:'FILL' 1"><?= $icon ?></span>
                <span class="result-q-text">Q<?= $idx + 1 ?>. <?= htmlspecialchars(mb_strimwidth($q['question_text'], 0, 100, '...')) ?></span>
                <span class="result-q-points"><?= $pts ?>/<?= $q['points'] ?> pts</span>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="result-cta">
            <a href="<?= SHARED_URL ?>skill-assesment.php">
                <span class="material-symbols-outlined">arrow_back</span> Return to Assessments
            </a>
        </div>
    </div>
</body>
</html>
