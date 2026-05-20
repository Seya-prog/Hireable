<?php
require_once __DIR__ . '/../../backend/helpers/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../database/repositories/JobRepository.php';
require_once __DIR__ . '/../../database/repositories/ApplicationRepository.php';

if (!isLoggedIn() || getCurrentUserRole() !== 'employer') {
    header('Location: ' . AUTH_URL . 'login.php');
    exit;
}

$jobId = intval($_GET['id'] ?? 0);
if (!$jobId) { header('Location: ../employer/jobs.php'); exit; }

$employerId = getCurrentUserId();
$jobRepo    = new JobRepository($pdo);
$appRepo    = new ApplicationRepository($pdo);

$job = $jobRepo->findById($jobId);
if (!$job || !$jobRepo->isOwner($jobId, $employerId)) {
    header('Location: ../employer/jobs.php');
    exit;
}

$applicants = $appRepo->findByJob($jobId);
$appStatusCounts = [];
foreach ($applicants as $a) {
    $appStatusCounts[$a['status']] = ($appStatusCounts[$a['status']] ?? 0) + 1;
}
$totalApplicants = count($applicants);
$shortlisted     = ($appStatusCounts['screening'] ?? 0) + ($appStatusCounts['interview'] ?? 0);
$interviewCount  = $appStatusCounts['interview'] ?? 0;

$daysAgo = floor((time() - strtotime($job['created_at'])) / 86400);
$postedLabel = $daysAgo === 0 ? 'Today' : ($daysAgo === 1 ? 'Yesterday' : $daysAgo . ' days ago');

$skillsList = !empty($job['skills_required']) ? array_map('trim', explode(',', $job['skills_required'])) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php $pageTitle = htmlspecialchars($job['title']) . ' | Hireable Employer'; ?>
    <?php $pageCss = ['employer.css', 'toast.css'];
    include __DIR__ . '/../../components/shared/head.php'; ?>
</head>
<body class="dash-page">
    <?php $activePage = 'jobs'; ?>
    <?php include __DIR__ . '/../../components/employer/employer-sidebar.php'; ?>

    <main class="dash-main" style="margin-left: 260px;">
        <div class="emp-header">
            <div>
                <a href="../employer/jobs.php" class="emp-back-link">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Back to Job Postings
                </a>
                <h2 class="page-title"><?= htmlspecialchars($job['title']) ?></h2>
                <p class="page-subtitle"><?= htmlspecialchars($job['department'] ?? '') ?> • <?= htmlspecialchars($job['location'] ?? 'Remote') ?> • <?= ucfirst(str_replace('-',' ',$job['job_type'])) ?> • <?= ucfirst($job['experience_level']) ?> Level</p>
            </div>
            <div class="emp-header-actions">
                <a href="../employer/assessment-edit.php?id=<?= $jobId ?>" class="assess-save-btn assess-save-btn--draft" style="text-decoration:none;">Edit Post</a>
                <span class="emp-status-badge emp-status--<?= $job['status'] ?>"><?= ucfirst($job['status']) ?></span>
            </div>
        </div>

        <div class="emp-detail-layout">
            <!-- Main Content -->
            <div class="emp-detail-main">
                <!-- Stats -->
                <div class="emp-detail-stats">
                    <div class="emp-detail-stat-card">
                        <span class="emp-detail-stat-value"><?= $totalApplicants ?></span>
                        <span class="emp-detail-stat-label">Total Applicants</span>
                    </div>
                    <div class="emp-detail-stat-card">
                        <span class="emp-detail-stat-value"><?= $shortlisted ?></span>
                        <span class="emp-detail-stat-label">Shortlisted</span>
                    </div>
                    <div class="emp-detail-stat-card">
                        <span class="emp-detail-stat-value"><?= $interviewCount ?></span>
                        <span class="emp-detail-stat-label">Interviews</span>
                    </div>
                    <div class="emp-detail-stat-card">
                        <span class="emp-detail-stat-value"><?= $appStatusCounts['offer'] ?? 0 ?></span>
                        <span class="emp-detail-stat-label">Offers</span>
                    </div>
                </div>

                <!-- Applicants Table -->
                <section>
                    <div class="emp-section-head">
                        <h3 class="emp-section-title">Applicants</h3>
                        <div class="emp-filter-actions">
                            <div class="emp-search-wrap">
                                <span class="material-symbols-outlined">search</span>
                                <input class="emp-search-input" type="text" placeholder="Search applicants...">
                            </div>
                        </div>
                    </div>
                    <div class="emp-table" data-filter-container>
                        <div class="emp-table-header" style="grid-template-columns: 2fr 0.8fr 0.8fr 0.5fr;">
                            <span>Candidate</span>
                            <span>Stage</span>
                            <span>Applied</span>
                            <span>Action</span>
                        </div>
                        <?php if (empty($applicants)): ?>
                            <p style="text-align:center; color:#7a6b5a; padding:2rem;">No applicants yet.</p>
                        <?php else: ?>
                            <?php foreach ($applicants as $a):
                                $initials = strtoupper(substr($a['first_name'],0,1) . substr($a['last_name'],0,1));
                            ?>
                            <div class="emp-table-row" data-filter-item data-searchable="<?= strtolower($a['first_name'] . ' ' . $a['last_name'] . ' ' . $a['email']) ?>" style="grid-template-columns: 2fr 0.8fr 0.8fr 0.5fr;">
                                <div class="emp-table-candidate">
                                    <div class="emp-avatar"><?= $initials ?></div>
                                    <div>
                                        <p class="emp-candidate-name"><?= htmlspecialchars($a['first_name'] . ' ' . substr($a['last_name'],0,1) . '.') ?></p>
                                        <p class="emp-candidate-email"><?= htmlspecialchars($a['email']) ?></p>
                                    </div>
                                </div>
                                <span class="emp-stage-badge emp-stage--<?= $a['status'] ?>"><?= ucfirst($a['status']) ?></span>
                                <span class="emp-table-cell"><?= date('M j', strtotime($a['applied_at'])) ?></span>
                                <a href="../employer/candidate-detail.php?id=<?= $a['id'] ?>" class="emp-action-btn"><span class="material-symbols-outlined">visibility</span></a>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </section>

                <?php if (!empty($job['description'])): ?>
                <section class="emp-candidate-section">
                    <h3 class="emp-section-title">Description</h3>
                    <p class="emp-candidate-bio"><?= nl2br(htmlspecialchars($job['description'])) ?></p>
                </section>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="emp-detail-sidebar">
                <section class="emp-detail-panel">
                    <h4 class="emp-section-title emp-section-title--sm">Job Details</h4>
                    <div class="emp-detail-info">
                        <div class="emp-detail-row"><span>Posted</span><span><?= $postedLabel ?></span></div>
                        <?php if ($job['salary_min'] || $job['salary_max']): ?>
                        <div class="emp-detail-row"><span>Salary</span><span>$<?= number_format($job['salary_min']) ?> – $<?= number_format($job['salary_max']) ?></span></div>
                        <?php endif; ?>
                        <div class="emp-detail-row"><span>Type</span><span><?= ucfirst(str_replace('-',' ',$job['job_type'])) ?></span></div>
                        <div class="emp-detail-row"><span>Level</span><span><?= ucfirst($job['experience_level']) ?></span></div>
                        <?php if ($job['application_deadline']): ?>
                        <div class="emp-detail-row"><span>Deadline</span><span><?= date('M j, Y', strtotime($job['application_deadline'])) ?></span></div>
                        <?php endif; ?>
                    </div>
                </section>

                <?php if (!empty($skillsList)): ?>
                <section class="emp-detail-panel">
                    <h4 class="emp-section-title emp-section-title--sm">Required Skills</h4>
                    <div class="emp-cand-skills">
                        <?php foreach ($skillsList as $skill): ?>
                            <span class="emp-cand-skill-tag"><?= htmlspecialchars($skill) ?></span>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php endif; ?>

                <section class="emp-detail-panel">
                    <h4 class="emp-section-title emp-section-title--sm">Quick Actions</h4>
                    <a href="../employer/interview-schedule.php" class="emp-quick-btn">
                        <span class="material-symbols-outlined">calendar_month</span>
                        Schedule Interview
                    </a>
                    <a href="../shared/skill-assesment.php?tab=results" class="emp-quick-btn">
                        <span class="material-symbols-outlined">quiz</span>
                        View Assessment Results
                    </a>
                </section>
            </div>
        </div>
    </main>
    <script src="<?= JS_URL ?>/filters.js"></script>
</body>
</html>
