<?php
require_once __DIR__ . '/../../backend/helpers/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../database/repositories/ApplicationRepository.php';
require_once __DIR__ . '/../../database/repositories/InterviewRepository.php';

if (!isLoggedIn() || getCurrentUserRole() !== 'employee') {
    header('Location: ' . AUTH_URL . 'login.php');
    exit;
}

$employeeId = getCurrentUserId();
$appRepo    = new ApplicationRepository($pdo);
$ivRepo     = new InterviewRepository($pdo);

$applications = $appRepo->findByEmployee($employeeId);
$appCounts    = $appRepo->countByEmployee($employeeId);
$interviews   = $ivRepo->findByEmployee($employeeId);

// Compute stats
$totalActive   = 0;
$interviewing  = $appCounts['interview'] ?? 0;
$offers        = $appCounts['offer'] ?? 0;
foreach ($appCounts as $status => $count) {
    if (!in_array($status, ['rejected', 'withdrawn'])) {
        $totalActive += $count;
    }
}
$responseRate = $totalActive > 0
    ? round((($interviewing + $offers + ($appCounts['screening'] ?? 0)) / $totalActive) * 100)
    : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php $pageTitle = 'Applications | Hireable'; ?>
    <?php $pageCss = ['applications.css', 'employer.css', 'toast.css'];
    include __DIR__ . '/../../components/shared/head.php'; ?>
</head>
<body class="dash-page">
    <?php include __DIR__ . '/../../components/shared/toast.php'; ?>
    <?php $activePage = 'applications'; ?>
    <?php include __DIR__ . '/../../components/employee/sidebar.php'; ?>

    <main class="dash-main" style="margin-left: 260px;">
        <!-- Header -->
        <div class="app-header">
            <div>
                <h2 class="page-title">Application Tracker</h2>
                <p class="page-subtitle">Track and manage your job applications.</p>
            </div>
            <a href="../employee/job-search.php" class="app-new-btn" style="text-decoration: none;">
                <span class="material-symbols-outlined">add</span>
                New Application
            </a>
        </div>

        <!-- Status Summary Bar -->
        <section class="app-stats">
            <?php
            $label = 'Total Active'; $value = $totalActive; $highlight = false;
            include __DIR__ . '/../../components/shared/app-stat-card.php';

            $label = 'Interviewing'; $value = $interviewing; $highlight = false;
            include __DIR__ . '/../../components/shared/app-stat-card.php';

            $label = 'Offers Received'; $value = $offers; $highlight = true;
            include __DIR__ . '/../../components/shared/app-stat-card.php';

            $label = 'Response Rate'; $value = $responseRate . '%'; $highlight = false;
            include __DIR__ . '/../../components/shared/app-stat-card.php';
            ?>
        </section>

        <div class="app-content">
            <!-- Applications List -->
            <div class="app-pipeline">
                <div class="app-pipeline-header">
                    <div class="emp-filter-tabs">
                        <button class="emp-filter-tab emp-filter-tab--active" data-filter="all">All <span class="emp-filter-count"><?= array_sum($appCounts) ?></span></button>
                        <button class="emp-filter-tab" data-filter="applied">Applied <span class="emp-filter-count"><?= $appCounts['applied'] ?? 0 ?></span></button>
                        <button class="emp-filter-tab" data-filter="review">Reviewing <span class="emp-filter-count"><?= $appCounts['screening'] ?? 0 ?></span></button>
                        <button class="emp-filter-tab" data-filter="interview">Interview <span class="emp-filter-count"><?= $appCounts['interview'] ?? 0 ?></span></button>
                        <button class="emp-filter-tab" data-filter="offer">Offer <span class="emp-filter-count"><?= $appCounts['offer'] ?? 0 ?></span></button>
                    </div>
                    <div class="emp-filter-actions">
                        <div class="emp-search-wrap">
                            <span class="material-symbols-outlined">search</span>
                            <input class="emp-search-input" type="text" placeholder="Search applications...">
                        </div>
                    </div>
                </div>

                <div class="app-cards" data-filter-container>
                    <?php if (empty($applications)): ?>
                        <p style="text-align:center; color:#7a6b5a; padding:3rem;">No applications yet. Browse jobs and start applying!</p>
                    <?php else: ?>
                        <?php foreach ($applications as $app):
                            $logo = '';
                            $title = htmlspecialchars($app['job_title']);
                            $company = htmlspecialchars($app['company_name'] ?: ($app['emp_first'] . ' ' . $app['emp_last']));
                            $appliedDate = 'Applied ' . date('M j, Y', strtotime($app['applied_at']));
                            $statusMap = [
                                'applied' => 'Applied', 'screening' => 'Reviewing',
                                'interview' => 'Interviewing', 'offer' => 'Offer Phase',
                                'rejected' => 'Rejected', 'withdrawn' => 'Withdrawn'
                            ];
                            $status = $statusMap[$app['status']] ?? ucfirst($app['status']);
                            $typeMap = [
                                'applied' => 'applied', 'screening' => 'review',
                                'interview' => 'interview', 'offer' => 'offer',
                                'rejected' => 'rejected', 'withdrawn' => 'withdrawn'
                            ];
                            $statusType = $typeMap[$app['status']] ?? 'applied';
                            $nextStep = '';
                            include __DIR__ . '/../../components/employee/app-card.php';
                        endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Sidebar -->
            <div class="app-sidebar">
                <!-- Upcoming Interviews -->
                <section>
                    <h3 class="app-section-title app-section-title--row">
                        Interviews
                        <span class="app-section-tag">Upcoming</span>
                    </h3>
                    <div class="app-interview-list">
                        <?php if (empty($interviews)): ?>
                            <p style="color:#7a6b5a; font-size:0.85rem;">No upcoming interviews.</p>
                        <?php else: ?>
                            <?php foreach (array_slice($interviews, 0, 3) as $iv):
                                $date = date('M j', strtotime($iv['scheduled_date']));
                                if ($iv['start_time']) $date .= ' • ' . date('g:i A', strtotime($iv['start_time']));
                                $company = htmlspecialchars($iv['company_name'] ?? '');
                                $description = htmlspecialchars($iv['job_title']);
                                $methodIcon = match($iv['interview_type'] ?? '') {
                                    'video' => 'video_call', 'phone' => 'phone_in_talk',
                                    default => 'meeting_room'
                                };
                                $methodText = ucfirst($iv['interview_type'] ?? 'Interview');
                                include __DIR__ . '/../../components/employee/app-interview-card.php';
                            endforeach; ?>
                        <?php endif; ?>
                    </div>
                </section>

                <!-- Market Insights -->
                <section class="app-insights">
                    <div class="app-insights-content">
                        <h4 class="app-insights-title">Your Progress</h4>
                        <p class="app-insights-text">You have <?= $totalActive ?> active applications and <?= $interviewing ?> interviews in progress.</p>
                        <a class="app-insights-link" href="../employee/profile.php">View Profile</a>
                    </div>
                    <div class="app-insights-icon">
                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">trending_up</span>
                    </div>
                </section>
            </div>
        </div>
    </main>
    <script src="<?= JS_URL ?>/filters.js"></script>
</body>
</html>