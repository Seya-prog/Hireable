<?php
require_once __DIR__ . '/../../backend/helpers/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../database/repositories/ApplicationRepository.php';
require_once __DIR__ . '/../../database/repositories/JobRepository.php';
require_once __DIR__ . '/../../database/repositories/InterviewRepository.php';
require_once __DIR__ . '/../../database/repositories/AssessmentRepository.php';

if (!isLoggedIn() || getCurrentUserRole() !== 'employee') {
    header('Location: ' . AUTH_URL . 'login.php'); exit;
}

$userId = getCurrentUserId();
$jobId  = intval($_GET['id'] ?? 0);

// This page can be loaded via ?id=job_id (from job search) or ?app_id=application_id
$appId = intval($_GET['app_id'] ?? 0);
$appRepo = new ApplicationRepository($pdo);
$jobRepo = new JobRepository($pdo);
$interviewRepo = new InterviewRepository($pdo);
$assessRepo = new AssessmentRepository($pdo);

$application = null;
$job = null;

if ($appId) {
    $application = $appRepo->findById($appId);
    if ($application && $application['employee_id'] == $userId) {
        $job = $jobRepo->findById($application['job_id']);
    }
} elseif ($jobId) {
    $job = $jobRepo->findById($jobId);
    // Check if user already applied
    $stmt = $pdo->prepare('SELECT * FROM applications WHERE job_id = ? AND employee_id = ?');
    $stmt->execute([$jobId, $userId]);
    $application = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$job) {
    header('Location: ' . EMPLOYEE_URL . 'applications.php'); exit;
}

// Get employer info
$stmt = $pdo->prepare('SELECT company_name, first_name, last_name FROM users WHERE id = ?');
$stmt->execute([$job['employer_id']]);
$employer = $stmt->fetch(PDO::FETCH_ASSOC);
$companyName = $employer['company_name'] ?: ($employer['first_name'] . ' ' . $employer['last_name']);

$status = $application['status'] ?? null;
$statusLabels = ['applied'=>'Applied','screening'=>'Under Review','interview'=>'Interviewing','offer'=>'Offer','hired'=>'Hired','rejected'=>'Rejected'];
$statusLabel = $statusLabels[$status] ?? 'Not Applied';
$statusClass = $status ?: 'new';

// Interviews for this application
$interviews = [];
if ($application) {
    $stmt = $pdo->prepare('SELECT * FROM interviews WHERE application_id = ? ORDER BY scheduled_date ASC, start_time ASC');
    $stmt->execute([$application['id']]);
    $interviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Assessment attempts linked to this job's assessments
$assessmentAttempt = null;
if ($job['id']) {
    $stmt = $pdo->prepare(
        'SELECT att.*, a.title AS assessment_title, a.passing_score
         FROM assessment_attempts att
         JOIN assessments a ON att.assessment_id = a.id
         WHERE a.job_id = ? AND att.employee_id = ? AND att.status = "completed"
         LIMIT 1'
    );
    $stmt->execute([$job['id'], $userId]);
    $assessmentAttempt = $stmt->fetch(PDO::FETCH_ASSOC);
}

$salary = '';
if ($job['salary_min'] && $job['salary_max']) {
    $salary = '$' . number_format($job['salary_min']/1000) . 'k – $' . number_format($job['salary_max']/1000) . 'k';
} elseif ($job['salary_min']) {
    $salary = '$' . number_format($job['salary_min']/1000) . 'k+';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php $pageTitle = htmlspecialchars($job['title']) . ' | Hireable'; ?>
    <?php $pageCss = ['applications.css', 'employee.css', 'toast.css'];
    include __DIR__ . '/../../components/shared/head.php'; ?>
</head>
<body class="dash-page">
    <?php include __DIR__ . '/../../components/shared/toast.php'; ?>
    <?php $activePage = 'applications'; ?>
    <?php include __DIR__ . '/../../components/employee/sidebar.php'; ?>

    <main class="dash-main" style="margin-left: 260px;">
        <a href="../employee/applications.php" class="emp-back-link" style="margin-bottom: 1.5rem; display: inline-flex;">
            <span class="material-symbols-outlined">arrow_back</span> Back to Applications
        </a>

        <div class="app-detail-layout">
            <div class="app-detail-main">
                <!-- Header -->
                <div class="app-detail-header">
                    <div class="app-detail-company">
                        <div class="app-detail-logo">
                            <span class="material-symbols-outlined" style="font-size: 2rem; color: #695d46;">apartment</span>
                        </div>
                        <div>
                            <h2 class="page-title" style="font-size: 1.75rem;"><?= htmlspecialchars($job['title']) ?></h2>
                            <p class="page-subtitle"><?= htmlspecialchars($companyName) ?> • <?= htmlspecialchars($job['location'] ?: 'Remote') ?> • <?= ucfirst(str_replace('-', ' ', $job['job_type'] ?? 'Full-time')) ?></p>
                        </div>
                    </div>
                    <?php if ($status): ?>
                        <span class="app-badge app-badge--<?= $statusClass ?>"><?= $statusLabel ?></span>
                    <?php endif; ?>
                </div>

                <!-- Timeline -->
                <?php if ($application): ?>
                <section class="app-detail-section">
                    <h3 class="app-detail-section-title">Application Timeline</h3>
                    <div class="emp-timeline">
                        <div class="emp-timeline-item">
                            <div class="emp-timeline-dot"></div>
                            <div class="emp-timeline-content">
                                <h4 class="emp-timeline-title">Application Submitted</h4>
                                <p class="emp-timeline-period"><?= date('M j, Y', strtotime($application['applied_at'])) ?></p>
                                <p class="emp-timeline-desc">Your application and resume were submitted successfully.</p>
                            </div>
                        </div>
                        <?php if (in_array($status, ['screening','interview','offer','hired'])): ?>
                        <div class="emp-timeline-item">
                            <div class="emp-timeline-dot"></div>
                            <div class="emp-timeline-content">
                                <h4 class="emp-timeline-title">Application Reviewed</h4>
                                <p class="emp-timeline-desc">Your application was reviewed by the hiring team.</p>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if ($assessmentAttempt): ?>
                        <div class="emp-timeline-item">
                            <div class="emp-timeline-dot"></div>
                            <div class="emp-timeline-content">
                                <h4 class="emp-timeline-title">Assessment Completed</h4>
                                <p class="emp-timeline-period"><?= $assessmentAttempt['completed_at'] ? date('M j, Y', strtotime($assessmentAttempt['completed_at'])) : '' ?></p>
                                <p class="emp-timeline-desc">Scored <?= round($assessmentAttempt['score']) ?>% on <?= htmlspecialchars($assessmentAttempt['assessment_title']) ?>.</p>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php foreach ($interviews as $iv):
                            $ivDate = date('M j, Y', strtotime($iv['scheduled_date']));
                            $ivTime = date('g:i A', strtotime($iv['start_time']));
                            $ivDone = $iv['status'] === 'completed';
                        ?>
                        <div class="emp-timeline-item">
                            <div class="emp-timeline-dot" style="background: <?= $ivDone ? '#155724' : '#856404' ?>;"></div>
                            <div class="emp-timeline-content">
                                <h4 class="emp-timeline-title" style="color: <?= $ivDone ? '#155724' : '#856404' ?>;">
                                    Interview — <?= $ivDone ? 'Completed' : 'Scheduled' ?>
                                </h4>
                                <p class="emp-timeline-period"><?= $ivDate ?> • <?= $ivTime ?> • <?= ucfirst(str_replace('_', ' ', $iv['interview_type'])) ?></p>
                                <?php if ($iv['notes_for_candidate']): ?>
                                    <p class="emp-timeline-desc"><?= htmlspecialchars($iv['notes_for_candidate']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php endif; ?>

                <!-- Job Description -->
                <section class="app-detail-section">
                    <h3 class="app-detail-section-title">Job Description</h3>
                    <div class="app-detail-description">
                        <p><?= nl2br(htmlspecialchars($job['description'] ?? '')) ?></p>
                        <?php if ($job['responsibilities']): ?>
                            <h4>Key Responsibilities</h4>
                            <ul><?php foreach (explode("\n", $job['responsibilities']) as $r): if (trim($r)): ?><li><?= htmlspecialchars(trim($r)) ?></li><?php endif; endforeach; ?></ul>
                        <?php endif; ?>
                        <?php if ($job['requirements']): ?>
                            <h4>Requirements</h4>
                            <ul><?php foreach (explode("\n", $job['requirements']) as $r): if (trim($r)): ?><li><?= htmlspecialchars(trim($r)) ?></li><?php endif; endforeach; ?></ul>
                        <?php endif; ?>
                    </div>
                </section>

                <!-- Apply button if not applied -->
                <?php if (!$application): ?>
                <section class="app-detail-section">
                    <form method="POST" action="/action/employee.applications.apply">
                        <input type="hidden" name="job_id" value="<?= $job['id'] ?>">
                        <div class="assess-field">
                            <label class="assess-label">Cover Letter (optional)</label>
                            <textarea class="assess-textarea" rows="4" name="cover_letter" placeholder="Tell the employer why you're a great fit..."></textarea>
                        </div>
                        <button type="submit" class="assess-save-btn assess-save-btn--publish" style="margin-top:1rem;">Apply Now</button>
                    </form>
                </section>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="app-detail-sidebar">
                <section class="emp-detail-panel">
                    <h4 class="emp-section-title emp-section-title--sm">Status</h4>
                    <div class="emp-detail-info">
                        <div class="emp-detail-row"><span>Stage</span><span><?= $statusLabel ?></span></div>
                        <?php if ($application): ?>
                            <div class="emp-detail-row"><span>Applied On</span><span><?= date('M j, Y', strtotime($application['applied_at'])) ?></span></div>
                        <?php endif; ?>
                        <?php if (!empty($interviews)):
                            $next = null;
                            foreach ($interviews as $iv) { if ($iv['status'] === 'scheduled') { $next = $iv; break; } }
                            if ($next): ?>
                            <div class="emp-detail-row"><span>Next Step</span><span>Interview <?= date('M j', strtotime($next['scheduled_date'])) ?></span></div>
                        <?php endif; endif; ?>
                    </div>
                </section>

                <?php if ($salary): ?>
                <section class="emp-detail-panel">
                    <h4 class="emp-section-title emp-section-title--sm">Compensation</h4>
                    <div class="emp-detail-info">
                        <div class="emp-detail-row"><span>Salary</span><span><?= $salary ?></span></div>
                        <div class="emp-detail-row"><span>Type</span><span><?= ucfirst(str_replace('-', ' ', $job['job_type'] ?? '')) ?></span></div>
                        <div class="emp-detail-row"><span>Level</span><span><?= ucfirst($job['experience_level'] ?? '') ?></span></div>
                    </div>
                </section>
                <?php endif; ?>

                <?php if ($assessmentAttempt): ?>
                <section class="emp-detail-panel">
                    <h4 class="emp-section-title emp-section-title--sm">Assessment</h4>
                    <div class="app-detail-assess-card">
                        <div class="app-detail-assess-score"><?= round($assessmentAttempt['score']) ?>%</div>
                        <div>
                            <p class="app-detail-assess-title"><?= htmlspecialchars($assessmentAttempt['assessment_title']) ?></p>
                            <p class="app-detail-assess-meta">Completed <?= $assessmentAttempt['completed_at'] ? date('M j', strtotime($assessmentAttempt['completed_at'])) : '' ?> • <?= $assessmentAttempt['time_taken_minutes'] ?? 0 ?> min</p>
                        </div>
                    </div>
                    <a href="../employee/assessment-result.php?id=<?= $assessmentAttempt['id'] ?>" class="emp-quick-btn" style="margin-top: 0.75rem;">
                        <span class="material-symbols-outlined">visibility</span> View Full Results
                    </a>
                </section>
                <?php endif; ?>

                <?php if ($job['skills_required']): ?>
                <section class="emp-detail-panel">
                    <h4 class="emp-section-title emp-section-title--sm">Required Skills</h4>
                    <div style="display:flex; flex-wrap:wrap; gap:0.4rem; margin-top:0.5rem;">
                        <?php foreach (explode(',', $job['skills_required']) as $sk): ?>
                            <span class="emp-skill-chip"><?= htmlspecialchars(trim($sk)) ?></span>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php endif; ?>

                <?php if ($application): ?>
                <section class="emp-detail-panel">
                    <h4 class="emp-section-title emp-section-title--sm">Quick Actions</h4>
                    <a href="resume-generator.php" class="emp-quick-btn"><span class="material-symbols-outlined">description</span> View Resume</a>
                    <a href="mailto:<?= htmlspecialchars($employer['first_name'] ?? '') ?>" class="emp-quick-btn"><span class="material-symbols-outlined">mail</span> Contact Recruiter</a>
                </section>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>
