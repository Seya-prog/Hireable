<?php
require_once __DIR__ . '/../../backend/helpers/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../database/repositories/AssessmentRepository.php';

if (!isLoggedIn() || getCurrentUserRole() !== 'employer') {
    header('Location: ' . AUTH_URL . 'login.php'); exit;
}

$employerId = getCurrentUserId();
$assessId = intval($_GET['id'] ?? 0);
$assessRepo = new AssessmentRepository($pdo);

$assessment = $assessRepo->findById($assessId);
if (!$assessment || $assessment['employer_id'] != $employerId) {
    header('Location: ' . SHARED_URL . 'skill-assesment.php?tab=my-assessments'); exit;
}

// Get questions
$questions = $assessRepo->getQuestions($assessId);
$totalQ = count($questions);

// Get linked job
$linkedJob = '';
if ($assessment['job_id']) {
    $stmt = $pdo->prepare('SELECT title FROM jobs WHERE id = ?');
    $stmt->execute([$assessment['job_id']]);
    $j = $stmt->fetch(PDO::FETCH_ASSOC);
    $linkedJob = $j['title'] ?? '';
}

// Get all completed attempts for this assessment
$stmt = $pdo->prepare(
    'SELECT att.*, u.first_name, u.last_name, u.email
     FROM assessment_attempts att
     JOIN users u ON att.employee_id = u.id
     WHERE att.assessment_id = ? AND att.status = "completed"
     ORDER BY att.completed_at DESC'
);
$stmt->execute([$assessId]);
$attempts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$completedCount = count($attempts);
$avgScore = $completedCount > 0 ? round(array_sum(array_column($attempts, 'score')) / $completedCount) : 0;
$passCount = 0;
$totalTime = 0;
foreach ($attempts as $att) {
    if ($att['score'] >= ($assessment['passing_score'] ?? 70)) $passCount++;
    $totalTime += intval($att['time_taken_minutes'] ?? 0);
}
$passRate = $completedCount > 0 ? round($passCount / $completedCount * 100) : 0;
$avgTime = $completedCount > 0 ? round($totalTime / $completedCount) : 0;

// Per-question performance
$qPerf = [];
if ($totalQ > 0 && $completedCount > 0) {
    foreach ($questions as $q) {
        $stmt2 = $pdo->prepare(
            'SELECT COUNT(*) as total, SUM(is_correct) as correct_count
             FROM assessment_answers
             WHERE question_id = ?'
        );
        $stmt2->execute([$q['id']]);
        $row = $stmt2->fetch(PDO::FETCH_ASSOC);
        $pct = $row['total'] > 0 ? round($row['correct_count'] / $row['total'] * 100) : 0;
        $qPerf[] = ['text' => $q['question_text'], 'pct' => $pct];
    }
}

$levelLabel = ucfirst($assessment['difficulty'] ?? 'intermediate');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php $pageTitle = htmlspecialchars($assessment['title']) . ' | Hireable Employer'; ?>
    <?php $pageCss = ['employer.css', 'toast.css'];
    include __DIR__ . '/../../components/shared/head.php'; ?>
</head>
<body class="dash-page">
    <?php $activePage = 'skill-assessments'; ?>
    <?php include __DIR__ . '/../../components/employer/employer-sidebar.php'; ?>

    <main class="dash-main" style="margin-left: 260px;">
        <div class="emp-header">
            <div>
                <a href="../shared/skill-assesment.php?tab=my-assessments" class="emp-back-link">
                    <span class="material-symbols-outlined">arrow_back</span> Back to Assessments
                </a>
                <h2 class="page-title"><?= htmlspecialchars($assessment['title']) ?></h2>
                <p class="page-subtitle">
                    <?= $linkedJob ? 'Linked to: ' . htmlspecialchars($linkedJob) . ' • ' : '' ?>
                    <?= $totalQ ?> Questions • <?= $levelLabel ?>
                </p>
            </div>
            <div class="emp-header-actions">
                <a href="../employer/assessment-edit.php?id=<?= $assessId ?>" class="assess-save-btn assess-save-btn--draft" style="text-decoration:none;">Edit Assessment</a>
                <span class="emp-status-badge emp-status--<?= $assessment['status'] === 'active' ? 'active' : 'paused' ?>"><?= ucfirst($assessment['status']) ?></span>
            </div>
        </div>

        <!-- Summary Stats -->
        <div class="assess-results-summary">
            <div class="assess-result-stat">
                <span class="assess-result-stat-value"><?= $completedCount ?></span>
                <span class="assess-result-stat-label">Completed</span>
            </div>
            <div class="assess-result-stat">
                <span class="assess-result-stat-value"><?= $avgScore ?>%</span>
                <span class="assess-result-stat-label">Average Score</span>
            </div>
            <div class="assess-result-stat">
                <span class="assess-result-stat-value"><?= $passRate ?>%</span>
                <span class="assess-result-stat-label">Pass Rate</span>
            </div>
            <div class="assess-result-stat">
                <span class="assess-result-stat-value"><?= $avgTime ?>m</span>
                <span class="assess-result-stat-label">Avg. Time</span>
            </div>
        </div>

        <!-- Question Performance -->
        <?php if (!empty($qPerf)): ?>
        <section style="margin-bottom: 2.5rem;">
            <h3 class="emp-section-title" style="margin-bottom: 1.5rem;">Question Performance</h3>
            <div class="emp-question-perf">
                <?php foreach ($qPerf as $idx => $qp):
                    $scoreClass = $qp['pct'] >= 80 ? 'high' : ($qp['pct'] >= 50 ? 'mid' : 'low');
                ?>
                <div class="emp-qp-item">
                    <div class="emp-qp-top">
                        <span class="emp-qp-num">Q<?= $idx + 1 ?></span>
                        <span class="emp-qp-text"><?= htmlspecialchars(mb_strimwidth($qp['text'], 0, 80, '...')) ?></span>
                        <span class="emp-score emp-score--<?= $scoreClass ?>"><?= $qp['pct'] ?>% correct</span>
                    </div>
                    <div class="emp-progress-bar"><div class="emp-progress-fill" style="width:<?= $qp['pct'] ?>%;"></div></div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- Individual Results Table -->
        <section>
            <div class="emp-section-head">
                <h3 class="emp-section-title">Individual Results</h3>
            </div>
            <?php if (empty($attempts)): ?>
                <div style="padding:2rem; text-align:center; color:#7a6b5a;">
                    <span class="material-symbols-outlined" style="font-size:2.5rem; opacity:0.4;">quiz</span>
                    <p style="margin-top:0.75rem;">No candidates have completed this assessment yet.</p>
                </div>
            <?php else: ?>
            <div class="emp-table">
                <div class="emp-table-header" style="grid-template-columns: 2fr 0.8fr 0.6fr 0.8fr 0.5fr;">
                    <span>Candidate</span><span>Score</span><span>Time</span><span>Status</span><span>Action</span>
                </div>
                <?php foreach ($attempts as $att):
                    $name = htmlspecialchars($att['first_name'] . ' ' . substr($att['last_name'], 0, 1) . '.');
                    $initials = strtoupper(substr($att['first_name'],0,1) . substr($att['last_name'],0,1));
                    $sc = round($att['score']);
                    $scoreClass = $sc >= 80 ? 'high' : ($sc >= 60 ? 'mid' : 'low');
                    $passed = $sc >= ($assessment['passing_score'] ?? 70);
                ?>
                <div class="emp-table-row" style="grid-template-columns: 2fr 0.8fr 0.6fr 0.8fr 0.5fr;">
                    <div class="emp-table-candidate">
                        <div class="emp-avatar"><?= $initials ?></div>
                        <div><p class="emp-candidate-name"><?= $name ?></p><p class="emp-candidate-email"><?= htmlspecialchars($att['email']) ?></p></div>
                    </div>
                    <span class="emp-score emp-score--<?= $scoreClass ?>"><?= $sc ?>%</span>
                    <span class="emp-table-cell"><?= intval($att['time_taken_minutes'] ?? 0) ?> min</span>
                    <span class="emp-stage-badge emp-stage--<?= $passed ? 'offer' : 'screening' ?>"><?= $passed ? 'Passed' : 'Failed' ?></span>
                    <a href="../employer/assessment-integrity.php?id=<?= $att['id'] ?>" class="emp-action-btn"><span class="material-symbols-outlined">visibility</span></a>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
