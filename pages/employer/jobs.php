<?php
require_once __DIR__ . '/../../backend/helpers/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../database/repositories/JobRepository.php';

if (!isLoggedIn() || getCurrentUserRole() !== 'employer') {
    header('Location: ' . AUTH_URL . 'login.php');
    exit;
}

$employerId = getCurrentUserId();
$jobRepo    = new JobRepository($pdo);
$jobCounts  = $jobRepo->countByStatus($employerId);
$allJobs    = $jobRepo->findByEmployer($employerId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php $pageTitle = 'Job Postings | Hireable Employer'; ?>
    <?php $pageCss = ['employer.css', 'toast.css'];
    include __DIR__ . '/../../components/shared/head.php'; ?>
</head>
<body class="dash-page">
    <?php $activePage = 'jobs'; ?>
    <?php include __DIR__ . '/../../components/employer/employer-sidebar.php'; ?>

    <main class="dash-main" style="margin-left: 260px;">
        <div class="emp-header">
            <div>
                <h2 class="page-title">Job Postings</h2>
                <p class="page-subtitle">Create, manage, and track all your open positions.</p>
            </div>
            <a href="../employer/job-create.php" class="emp-cta-btn">
                <span class="material-symbols-outlined">add</span>
                Create New Job
            </a>
        </div>

        <!-- Filters -->
        <div class="emp-filters">
            <div class="emp-filter-tabs">
                <button class="emp-filter-tab emp-filter-tab--active" data-filter="all">All Jobs <span class="emp-filter-count"><?= $jobCounts['total'] ?></span></button>
                <button class="emp-filter-tab" data-filter="active">Active <span class="emp-filter-count"><?= $jobCounts['active'] ?></span></button>
                <button class="emp-filter-tab" data-filter="draft">Draft <span class="emp-filter-count"><?= $jobCounts['draft'] ?></span></button>
                <button class="emp-filter-tab" data-filter="closed">Closed <span class="emp-filter-count"><?= $jobCounts['closed'] ?></span></button>
            </div>
            <div class="emp-filter-actions">
                <div class="emp-search-wrap">
                    <span class="material-symbols-outlined">search</span>
                    <input class="emp-search-input" type="text" placeholder="Search jobs...">
                </div>
            </div>
        </div>

        <!-- Job Cards Grid -->
        <div class="emp-job-grid" data-filter-container>
            <?php if (empty($allJobs)): ?>
                <p style="text-align:center; color:#7a6b5a; padding:3rem; grid-column:1/-1;">No jobs posted yet. Click "Create New Job" to get started!</p>
            <?php else: ?>
                <?php foreach ($allJobs as $job):
                    $jobTitle   = htmlspecialchars($job['title']);
                    $department = htmlspecialchars($job['department'] ?? 'General');
                    $location   = htmlspecialchars($job['location'] ?? 'Not specified');
                    $jobStatus  = ucfirst($job['status']);
                    $statusType = $job['status'];
                    $applicants = (int)$job['applicant_count'];
                    $daysAgo    = floor((time() - strtotime($job['created_at'])) / 86400);
                    $posted     = $daysAgo === 0 ? 'Today' : ($daysAgo === 1 ? 'Yesterday' : $daysAgo . ' days ago');
                    if ($job['status'] === 'draft') $posted = 'Not published';
                    include __DIR__ . '/../../components/employer/job-card.php';
                endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
    <script src="<?= JS_URL ?>/filters.js"></script>
</body>
</html>
