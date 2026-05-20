<?php
require_once __DIR__ . '/../../backend/helpers/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../database/repositories/InterviewRepository.php';

if (!isLoggedIn() || getCurrentUserRole() !== 'employer') {
    header('Location: ' . AUTH_URL . 'login.php');
    exit;
}

$employerId = getCurrentUserId();
$ivRepo     = new InterviewRepository($pdo);

$grouped         = $ivRepo->findByEmployerGrouped($employerId);
$weekStats       = $ivRepo->countThisWeek($employerId);
$pendingFeedback = $ivRepo->findPendingFeedback($employerId);

// Label helpers
function formatDateLabel(string $date): string {
    $ts = strtotime($date);
    $today = strtotime('today');
    $tomorrow = strtotime('tomorrow');
    if ($ts == $today) return 'Today — ' . date('F j, Y');
    if ($ts == $tomorrow) return 'Tomorrow — ' . date('F j, Y', $ts);
    return date('l — F j, Y', $ts);
}
function isDateToday(string $date): bool {
    return strtotime($date) == strtotime('today');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php $pageTitle = 'Interviews | Hireable Employer'; ?>
    <?php $pageCss = ['employer.css', 'toast.css'];
    include __DIR__ . '/../../components/shared/head.php'; ?>
</head>
<body class="dash-page">
    <?php $activePage = 'interviews'; ?>
    <?php include __DIR__ . '/../../components/employer/employer-sidebar.php'; ?>

    <main class="dash-main" style="margin-left: 260px;">
        <div class="emp-header">
            <div>
                <h2 class="page-title">Interviews</h2>
                <p class="page-subtitle">Schedule, track, and manage all candidate interviews.</p>
            </div>
            <a href="../employer/interview-schedule.php" class="emp-cta-btn">
                <span class="material-symbols-outlined">add</span>
                Schedule Interview
            </a>
        </div>

        <div class="emp-interview-content">
            <!-- Main: Interview Schedule -->
            <div class="emp-interview-main">
                <?php if (empty($grouped)): ?>
                    <p style="text-align:center; color:#7a6b5a; padding:3rem;">No interviews scheduled yet.</p>
                <?php else: ?>
                    <?php foreach ($grouped as $date => $dayInterviews): ?>
                    <section>
                        <h3 class="emp-day-label">
                            <span class="emp-day-dot <?= isDateToday($date) ? 'emp-day-dot--today' : '' ?>"></span>
                            <?= formatDateLabel($date) ?>
                        </h3>
                        <div class="emp-interview-day-list">
                            <?php foreach ($dayInterviews as $iv):
                                $candidate = htmlspecialchars($iv['first_name'] . ' ' . substr($iv['last_name'],0,1) . '.');
                                $position = htmlspecialchars($iv['job_title']);
                                $startTime = $iv['start_time'] ? date('g:i A', strtotime($iv['start_time'])) : '';
                                $dur = (int)($iv['duration_minutes'] ?? 60);
                                $endTime = $iv['start_time'] ? date('g:i A', strtotime($iv['start_time'] . " +{$dur} minutes")) : '';
                                $interviewDate = $startTime . ($endTime ? ' – ' . $endTime : '');
                                $methodIcon = match($iv['interview_type'] ?? '') {
                                    'video' => 'video_call',
                                    'phone' => 'phone_in_talk',
                                    'in_person','in-person' => 'meeting_room',
                                    default => 'video_call'
                                };
                                $methodText = match($iv['interview_type'] ?? '') {
                                    'video' => $iv['meeting_link'] ? 'Video Call' : 'Video Call',
                                    'phone' => 'Phone Call',
                                    'in_person','in-person' => $iv['location'] ? 'In-person, ' . htmlspecialchars($iv['location']) : 'In-person',
                                    default => 'Interview'
                                };
                                include __DIR__ . '/../../components/employer/interview-card.php';
                            endforeach; ?>
                        </div>
                    </section>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Sidebar: Summary -->
            <div class="emp-interview-sidebar">
                <section class="emp-int-summary-panel">
                    <h3 class="emp-section-title emp-section-title--sm">This Week</h3>
                    <div class="emp-int-summary-stats">
                        <div class="emp-int-summary-stat">
                            <span class="emp-int-summary-value"><?= (int)$weekStats['scheduled'] ?></span>
                            <span class="emp-int-summary-label">Scheduled</span>
                        </div>
                        <div class="emp-int-summary-stat">
                            <span class="emp-int-summary-value"><?= (int)$weekStats['completed'] ?></span>
                            <span class="emp-int-summary-label">Completed</span>
                        </div>
                        <div class="emp-int-summary-stat">
                            <span class="emp-int-summary-value"><?= count($pendingFeedback) ?></span>
                            <span class="emp-int-summary-label">Pending Feedback</span>
                        </div>
                    </div>
                </section>

                <section class="emp-int-feedback-panel">
                    <h3 class="emp-section-title emp-section-title--sm">Pending Feedback</h3>
                    <?php if (empty($pendingFeedback)): ?>
                        <p style="color:#7a6b5a; font-size:0.85rem;">All feedback is up to date!</p>
                    <?php else: ?>
                        <?php foreach ($pendingFeedback as $pf):
                            $pfInitials = strtoupper(substr($pf['first_name'],0,1) . substr($pf['last_name'],0,1));
                        ?>
                        <div class="emp-int-feedback-item">
                            <div class="emp-avatar"><?= $pfInitials ?></div>
                            <div>
                                <p class="emp-int-fb-name"><?= htmlspecialchars($pf['first_name'] . ' ' . substr($pf['last_name'],0,1) . '.') ?></p>
                                <p class="emp-int-fb-role"><?= htmlspecialchars($pf['job_title']) ?> • <?= date('M j', strtotime($pf['scheduled_date'])) ?></p>
                            </div>
                            <a href="../employer/interview-feedback.php?id=<?= $pf['id'] ?>" class="emp-int-fb-btn" style="text-decoration:none;">Add Feedback</a>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </section>
            </div>
        </div>
    </main>
</body>
</html>
