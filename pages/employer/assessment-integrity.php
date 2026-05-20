<?php
/**
 * Assessment Integrity Review — Employer dashboard for reviewing candidate proctoring data
 * Entry: ?id={attempt_id}
 */
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../backend/helpers/session.php';
require_once __DIR__ . '/../../backend/helpers/integrity.php';
require_once __DIR__ . '/../../database/repositories/AssessmentRepository.php';

if (!isLoggedIn() || getCurrentUserRole() !== 'employer') {
    header('Location: ' . AUTH_URL . 'login.php');
    exit;
}

$assessmentRepo = new AssessmentRepository($pdo);
$attemptId = intval($_GET['id'] ?? 0);
$attempt = $assessmentRepo->getAttemptById($attemptId);

if (!$attempt) {
    setFlash('error', 'Attempt not found.');
    header('Location: ' . SHARED_URL . 'skill-assesment.php');
    exit;
}

// Verify employer owns this assessment
$assessment = $assessmentRepo->findById($attempt['assessment_id']);
if (!$assessment || $assessment['employer_id'] != getCurrentUserId()) {
    setFlash('error', 'Unauthorized access.');
    header('Location: ' . EMPLOYER_URL . 'dashboard.php');
    exit;
}

// Get candidate info
$stmtUser = $pdo->prepare('SELECT first_name, last_name, email FROM users WHERE id = ?');
$stmtUser->execute([$attempt['employee_id']]);
$candidate = $stmtUser->fetch(PDO::FETCH_ASSOC);
$candidateFullName = ($candidate['first_name'] ?? '') . ' ' . ($candidate['last_name'] ?? '');

$snapshots = $assessmentRepo->getSnapshots($attemptId);
$answers = $assessmentRepo->getAnswers($attemptId);
$integrityScore = intval($attempt['integrity_score'] ?? 100);
$integrityInfo = getIntegrityLabel($integrityScore);
$violationSummary = buildViolationSummary($attempt);
$violationLog = json_decode($attempt['violation_log'] ?? '[]', true) ?: [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Integrity Review | Hireable Employer</title>
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,wght@0,400;0,500;0,700;1,400&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= CSS_URL ?>/global.css">
    <link rel="stylesheet" href="<?= CSS_URL ?>/layout.css">
    <link rel="stylesheet" href="<?= CSS_URL ?>/employer.css">
    <style>
        /* Integrity Dashboard Styles */
        .integrity-page { padding: 2rem 2.5rem; }
        .integrity-grid { display: grid; grid-template-columns: 1fr 360px; gap: 2.5rem; align-items: start; }

        /* Score Ring */
        .integrity-score-card {
            background: #fff; border: 1px solid rgba(208,197,187,0.3);
            border-radius: 16px; padding: 2rem; text-align: center;
        }
        .integrity-ring { position: relative; width: 160px; height: 160px; margin: 0 auto 16px; }
        .integrity-ring svg { transform: rotate(-90deg); }
        .integrity-ring circle { fill: none; stroke-width: 10; }
        .integrity-ring .ring-bg { stroke: #e3e0db; }
        .integrity-ring .ring-fg { stroke-linecap: round; }
        .ring-fg--green { stroke: #155724; }
        .ring-fg--yellow { stroke: #856404; }
        .ring-fg--orange { stroke: #e65100; }
        .ring-fg--red { stroke: #93000a; }
        .integrity-ring-value {
            position: absolute; inset: 0; display: flex; flex-direction: column;
            align-items: center; justify-content: center;
        }
        .integrity-ring-num { font-family: 'Newsreader',serif; font-size: 2.5rem; font-weight: 700; color: #170f07; line-height: 1; }
        .integrity-ring-label { font-size: 0.65rem; color: #7e766e; text-transform: uppercase; letter-spacing: 0.1em; margin-top: 4px; }

        .integrity-verdict {
            display: inline-flex; align-items: center; gap: 6px; padding: 6px 16px;
            border-radius: 20px; font-size: 0.75rem; font-weight: 700; margin-top: 12px;
        }
        .verdict--green { background: #d4edda; color: #155724; }
        .verdict--yellow { background: #fff3cd; color: #856404; }
        .verdict--orange { background: #ffe0b2; color: #e65100; }
        .verdict--red { background: #ffdad6; color: #93000a; }

        /* Info Cards */
        .integrity-info-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin: 24px 0; }
        .integrity-info-item {
            background: #f4eedb; border-radius: 10px; padding: 16px; text-align: center;
        }
        .integrity-info-value { font-family: 'Newsreader',serif; font-size: 1.3rem; font-weight: 700; color: #170f07; }
        .integrity-info-label { font-size: 0.6rem; color: #7e766e; text-transform: uppercase; letter-spacing: 0.1em; margin-top: 2px; }

        /* Violation List */
        .violation-panel {
            background: #fff; border: 1px solid rgba(208,197,187,0.3);
            border-radius: 16px; padding: 1.5rem; margin-bottom: 1.5rem;
        }
        .violation-panel-title {
            font-family: 'Newsreader',serif; font-weight: 700; font-size: 1.1rem;
            color: #170f07; margin: 0 0 1rem; display: flex; align-items: center; gap: 8px;
        }
        .violation-row {
            display: flex; align-items: center; justify-content: space-between;
            padding: 10px 0; border-bottom: 1px solid rgba(208,197,187,0.15);
        }
        .violation-row:last-child { border-bottom: none; }
        .violation-label { font-size: 0.85rem; color: #4d453f; }
        .violation-count {
            font-size: 0.8rem; font-weight: 700; padding: 3px 10px;
            border-radius: 20px; background: #ffdad6; color: #93000a;
        }
        .violation-count--zero { background: #d4edda; color: #155724; }

        /* Snapshot Gallery */
        .snapshot-gallery { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 10px; }
        .snapshot-item {
            position: relative; border-radius: 8px; overflow: hidden;
            border: 2px solid rgba(208,197,187,0.3); aspect-ratio: 4/3;
        }
        .snapshot-item img { width: 100%; height: 100%; object-fit: cover; }
        .snapshot-badge {
            position: absolute; bottom: 4px; left: 4px; font-size: 0.55rem; font-weight: 700;
            padding: 2px 6px; border-radius: 4px; text-transform: uppercase;
        }
        .snapshot-badge--clean { background: #d4edda; color: #155724; }
        .snapshot-badge--flag { background: #ffdad6; color: #93000a; }
        .snapshot-time {
            position: absolute; top: 4px; right: 4px; font-size: 0.55rem;
            background: rgba(0,0,0,0.6); color: #fff; padding: 2px 6px; border-radius: 4px;
        }

        /* Timeline */
        .violation-timeline { max-height: 400px; overflow-y: auto; }
        .timeline-entry {
            display: flex; gap: 12px; padding: 10px 0;
            border-bottom: 1px solid rgba(208,197,187,0.1);
        }
        .timeline-entry:last-child { border-bottom: none; }
        .timeline-time { font-size: 0.65rem; color: #7e766e; white-space: nowrap; min-width: 70px; }
        .timeline-type {
            font-size: 0.65rem; font-weight: 700; padding: 2px 8px;
            border-radius: 4px; background: #fff3cd; color: #856404; white-space: nowrap;
        }
        .timeline-detail { font-size: 0.75rem; color: #4d453f; flex: 1; }

        /* Candidate Header */
        .candidate-header {
            display: flex; align-items: center; gap: 16px;
            padding-bottom: 1.5rem; margin-bottom: 1.5rem;
            border-bottom: 1px solid rgba(208,197,187,0.3);
        }
        .candidate-header .emp-avatar { width: 48px; height: 48px; font-size: 0.9rem; }

        .no-snapshots {
            text-align: center; padding: 40px 20px; color: #7e766e;
        }
        .no-snapshots .material-symbols-outlined { font-size: 48px; display: block; margin-bottom: 8px; }

        @media (max-width: 900px) {
            .integrity-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body class="dash-page">
    <?php include __DIR__ . '/../../components/shared/toast.php'; ?>
    <?php $activePage = 'assessments'; ?>
    <?php include __DIR__ . '/../../components/employer/employer-sidebar.php'; ?>

    <main class="dash-main integrity-page" style="margin-left: 260px;">
        <a href="<?= SHARED_URL ?>skill-assesment.php" class="emp-back-link">
            <span class="material-symbols-outlined">arrow_back</span> Back to Assessments
        </a>

        <!-- Header -->
        <div class="emp-header">
            <div>
                <h2 class="page-title">Integrity Review</h2>
                <p class="page-subtitle"><?= htmlspecialchars($assessment['title']) ?></p>
            </div>
        </div>

        <!-- Candidate Info -->
        <div class="candidate-header">
            <div class="emp-avatar"><?= strtoupper(substr($candidate['first_name'] ?? 'U', 0, 1)) . strtoupper(substr($candidate['last_name'] ?? '', 0, 1)) ?></div>
            <div>
                <p class="emp-candidate-name" style="font-size:1rem"><?= htmlspecialchars($candidateFullName) ?></p>
                <p class="emp-candidate-email"><?= htmlspecialchars($candidate['email'] ?? '') ?></p>
            </div>
            <div style="margin-left:auto; text-align:right;">
                <p style="margin:0; font-size:0.8rem; color:#7e766e;">Score: <strong style="color:#170f07"><?= round($attempt['score'] ?? 0) ?>%</strong></p>
                <p style="margin:0; font-size:0.75rem; color:#7e766e;">Completed: <?= date('M j, Y g:i A', strtotime($attempt['completed_at'] ?? $attempt['started_at'])) ?></p>
            </div>
        </div>

        <div class="integrity-grid">
            <!-- Left Column: Violations & Snapshots -->
            <div>
                <!-- Violation Summary -->
                <div class="violation-panel">
                    <h3 class="violation-panel-title">
                        <span class="material-symbols-outlined" style="font-size:20px; color:#93000a;">warning</span>
                        Violation Summary
                    </h3>
                    <?php if (empty($violationSummary)): ?>
                        <p style="color:#155724; font-size:0.85rem; display:flex; align-items:center; gap:6px;">
                            <span class="material-symbols-outlined" style="font-size:18px; font-variation-settings:'FILL' 1">verified</span>
                            No violations detected
                        </p>
                    <?php else: ?>
                        <?php foreach ($violationSummary as $v): ?>
                        <div class="violation-row">
                            <span class="violation-label"><?= htmlspecialchars($v['label']) ?></span>
                            <span class="violation-count <?= $v['count'] === 0 ? 'violation-count--zero' : '' ?>"><?= $v['count'] ?></span>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Violation Timeline -->
                <?php if (!empty($violationLog)): ?>
                <div class="violation-panel">
                    <h3 class="violation-panel-title">
                        <span class="material-symbols-outlined" style="font-size:20px; color:#856404;">timeline</span>
                        Violation Timeline
                    </h3>
                    <div class="violation-timeline">
                        <?php foreach ($violationLog as $entry):
                            $logTime = isset($entry['timestamp']) ? date('H:i:s', strtotime($entry['timestamp'])) : '--:--';
                        ?>
                        <div class="timeline-entry">
                            <span class="timeline-time"><?= $logTime ?></span>
                            <span class="timeline-type"><?= htmlspecialchars($entry['type'] ?? 'unknown') ?></span>
                            <span class="timeline-detail"><?= htmlspecialchars($entry['detail'] ?? '') ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Snapshot Gallery -->
                <div class="violation-panel">
                    <h3 class="violation-panel-title">
                        <span class="material-symbols-outlined" style="font-size:20px; color:#004085;">photo_camera</span>
                        Webcam Snapshots (<?= count($snapshots) ?>)
                    </h3>
                    <?php if (empty($snapshots)): ?>
                        <div class="no-snapshots">
                            <span class="material-symbols-outlined">no_photography</span>
                            <p>No snapshots recorded</p>
                        </div>
                    <?php else: ?>
                        <div class="snapshot-gallery">
                            <?php foreach ($snapshots as $snap):
                                $flagged = $snap['flag_type'] !== 'clean';
                                $snapTime = date('H:i:s', strtotime($snap['captured_at']));
                            ?>
                            <div class="snapshot-item">
                                <img src="/<?= htmlspecialchars($snap['photo_path']) ?>" alt="Snapshot" loading="lazy">
                                <span class="snapshot-time"><?= $snapTime ?></span>
                                <span class="snapshot-badge <?= $flagged ? 'snapshot-badge--flag' : 'snapshot-badge--clean' ?>">
                                    <?= $flagged ? str_replace('_', ' ', $snap['flag_type']) : 'Clean' ?>
                                </span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Column: Score & Details -->
            <div>
                <!-- Integrity Score -->
                <div class="integrity-score-card">
                    <?php
                        $circumference = 2 * M_PI * 70;
                        $dashOffset = $circumference - ($integrityScore / 100) * $circumference;
                        $ringColor = $integrityInfo['color'];
                    ?>
                    <div class="integrity-ring">
                        <svg width="160" height="160" viewBox="0 0 160 160">
                            <circle class="ring-bg" cx="80" cy="80" r="70" />
                            <circle class="ring-fg ring-fg--<?= $ringColor ?>" cx="80" cy="80" r="70"
                                stroke-dasharray="<?= $circumference ?>"
                                stroke-dashoffset="<?= $dashOffset ?>" />
                        </svg>
                        <div class="integrity-ring-value">
                            <span class="integrity-ring-num"><?= $integrityScore ?></span>
                            <span class="integrity-ring-label">Integrity</span>
                        </div>
                    </div>
                    <div class="integrity-verdict verdict--<?= $ringColor ?>">
                        <span class="material-symbols-outlined" style="font-size:16px; font-variation-settings:'FILL' 1"><?= $integrityInfo['icon'] ?></span>
                        <?= $integrityInfo['label'] ?>
                    </div>
                </div>

                <!-- Exam Details -->
                <div class="violation-panel" style="margin-top:16px;">
                    <h3 class="violation-panel-title" style="font-size:0.95rem;">Exam Details</h3>
                    <div class="violation-row">
                        <span class="violation-label">Score</span>
                        <span style="font-weight:700; font-size:0.85rem;"><?= round($attempt['score'] ?? 0) ?>%</span>
                    </div>
                    <div class="violation-row">
                        <span class="violation-label">Time Taken</span>
                        <span style="font-weight:700; font-size:0.85rem;"><?= intval($attempt['time_taken_minutes'] ?? 0) ?> min</span>
                    </div>
                    <div class="violation-row">
                        <span class="violation-label">Status</span>
                        <span class="emp-stage-badge emp-stage--<?= $attempt['status'] === 'completed' ? 'offer' : 'applied' ?>">
                            <?= ucfirst($attempt['status']) ?>
                        </span>
                    </div>
                    <div class="violation-row">
                        <span class="violation-label">IP Address</span>
                        <span style="font-size:0.8rem; font-family:monospace;"><?= htmlspecialchars($attempt['ip_address'] ?? 'N/A') ?></span>
                    </div>
                    <div class="violation-row">
                        <span class="violation-label">Flagged</span>
                        <span style="font-weight:700; color:<?= $attempt['flagged'] ? '#93000a' : '#155724' ?>">
                            <?= $attempt['flagged'] ? 'Yes' : 'No' ?>
                        </span>
                    </div>
                </div>

                <!-- Integrity Breakdown -->
                <div class="integrity-info-grid">
                    <div class="integrity-info-item">
                        <div class="integrity-info-value"><?= intval($attempt['tab_switches'] ?? 0) ?></div>
                        <div class="integrity-info-label">Tab Switches</div>
                    </div>
                    <div class="integrity-info-item">
                        <div class="integrity-info-value"><?= intval($attempt['face_absence_count'] ?? 0) ?></div>
                        <div class="integrity-info-label">Face Absent</div>
                    </div>
                    <div class="integrity-info-item">
                        <div class="integrity-info-value"><?= intval($attempt['phone_detections'] ?? 0) ?></div>
                        <div class="integrity-info-label">Phone Found</div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
