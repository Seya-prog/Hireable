<?php
require_once __DIR__ . '/../../backend/helpers/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../database/repositories/ApplicationRepository.php';
require_once __DIR__ . '/../../database/repositories/JobRepository.php';
require_once __DIR__ . '/../../database/repositories/UserRepository.php';

if (!isLoggedIn() || getCurrentUserRole() !== 'employer') {
    header('Location: ' . AUTH_URL . 'login.php');
    exit;
}

$employerId = getCurrentUserId();
$apps       = new ApplicationRepository($pdo);
$jobRepo    = new JobRepository($pdo);
$userRepo   = new UserRepository($pdo);

$appCounts    = $apps->countByStatusForEmployer($employerId);
$applicants   = $apps->findDetailedByEmployer($employerId);
$employerJobs = $jobRepo->findByEmployer($employerId);

// Attach skills to each applicant
foreach ($applicants as &$a) {
    $a['skills'] = $userRepo->getSkills($a['employee_id']);
}
unset($a);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php $pageTitle = 'Candidates | Hireable Employer'; ?>
    <?php $pageCss = ['employer.css', 'toast.css'];
    include __DIR__ . '/../../components/shared/head.php'; ?>
</head>
<body class="dash-page">
    <?php $activePage = 'candidates'; ?>
    <?php include __DIR__ . '/../../components/employer/employer-sidebar.php'; ?>

    <main class="dash-main" style="margin-left: 260px;">
        <div class="emp-header">
            <div>
                <h2 class="page-title">Candidates</h2>
                <p class="page-subtitle">Review, shortlist, and manage your applicant pipeline.</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="emp-filters">
            <div class="emp-filter-tabs">
                <button class="emp-filter-tab emp-filter-tab--active" data-filter="all">All <span class="emp-filter-count"><?= $appCounts['total'] ?></span></button>
                <button class="emp-filter-tab" data-filter="screening">Screening <span class="emp-filter-count"><?= $appCounts['screening'] ?></span></button>
                <button class="emp-filter-tab" data-filter="interview">Interviewing <span class="emp-filter-count"><?= $appCounts['interview'] ?></span></button>
                <button class="emp-filter-tab" data-filter="offer">Offered <span class="emp-filter-count"><?= $appCounts['offer'] ?></span></button>
                <button class="emp-filter-tab" data-filter="rejected">Rejected <span class="emp-filter-count"><?= $appCounts['rejected'] ?></span></button>
            </div>
            <div class="emp-filter-actions">
                <div class="emp-search-wrap">
                    <span class="material-symbols-outlined">search</span>
                    <input class="emp-search-input" type="text" placeholder="Search candidates...">
                </div>
                <select class="emp-filter-select" data-filter-by="position">
                    <option value="">All Positions</option>
                    <?php foreach ($employerJobs as $ej): ?>
                        <option value="<?= strtolower(htmlspecialchars($ej['title'])) ?>"><?= htmlspecialchars($ej['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Candidate Cards Grid -->
        <div class="emp-cand-grid" data-filter-container>
            <?php if (empty($applicants)): ?>
                <p style="text-align:center; color:#7a6b5a; padding:3rem; grid-column:1/-1;">No candidates yet. Applicants will appear here when they apply to your jobs.</p>
            <?php else: ?>
                <?php foreach ($applicants as $a):
                    $candidateName  = htmlspecialchars($a['first_name'] . ' ' . substr($a['last_name'],0,1) . '.');
                    $candidateEmail = htmlspecialchars($a['email']);
                    $initials       = strtoupper(substr($a['first_name'],0,1) . substr($a['last_name'],0,1));
                    $appliedRole    = htmlspecialchars($a['job_title']);
                    $matchPercent   = 0; // computed later or from assessment
                    $matchLevel     = 'low';
                    $skills         = array_map(fn($s) => $s['skill_name'], array_slice($a['skills'], 0, 4));
                    $stage          = ucfirst($a['status']);
                    $stageType      = $a['status'];
                    $applicationId  = $a['id'];
                    include __DIR__ . '/../../components/employer/candidate-card.php';
                endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
    <script src="<?= JS_URL ?>/filters.js"></script>
</body>
</html>
