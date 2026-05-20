<?php
require_once __DIR__ . '/../../backend/helpers/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../database/repositories/JobRepository.php';
require_once __DIR__ . '/../../database/repositories/ApplicationRepository.php';
require_once __DIR__ . '/../../database/repositories/InterviewRepository.php';
require_once __DIR__ . '/../../database/repositories/UserRepository.php';

if (!isLoggedIn() || getCurrentUserRole() !== 'employer') {
    header('Location: ' . AUTH_URL . 'login.php');
    exit;
}

$employerId = getCurrentUserId();
$jobs       = new JobRepository($pdo);
$apps       = new ApplicationRepository($pdo);
$interviews = new InterviewRepository($pdo);
$users      = new UserRepository($pdo);

$user        = $users->findById($employerId);
$companyName = $user['company_name'] ?: ($user['first_name'] . ' ' . $user['last_name']);

// Stats
$jobCounts   = $jobs->countByStatus($employerId);
$appCounts   = $apps->countByStatusForEmployer($employerId);
$weekStats   = $interviews->countThisWeek($employerId);

// Recent data
$recentApplicants  = $apps->findRecentByEmployer($employerId, 4);
$activeJobs        = $jobs->findByEmployer($employerId, 'active');
$upcomingInterviews = $interviews->findUpcoming($employerId, 2);

// Funnel
$funnelTotal = $appCounts['total'] ?: 1; // avoid division by zero
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php $pageTitle = 'Dashboard | Hireable Employer'; ?>
    <?php $pageCss = ['employer.css', 'applications.css', 'toast.css'];
    include __DIR__ . '/../../components/shared/head.php'; ?>
</head>
<body class="dash-page">
    <?php include __DIR__ . '/../../components/shared/toast.php'; ?>
    <?php $activePage = 'dashboard'; ?>
    <?php include __DIR__ . '/../../components/employer/employer-sidebar.php'; ?>

    <main class="dash-main" style="margin-left: 260px;">
        <!-- Header -->
        <div class="emp-header">
            <div>
                <h2 class="page-title">Welcome back, <?= htmlspecialchars($companyName) ?></h2>
                <p class="page-subtitle">Here's what's happening with your hiring pipeline today.</p>
            </div>
            <a href="../employer/job-create.php" class="emp-cta-btn">
                <span class="material-symbols-outlined">add</span>
                Post a Job
            </a>
        </div>

        <!-- Stats Row -->
        <section class="emp-stats">
            <?php
            $label = 'Active Jobs'; $value = $jobCounts['active']; $icon = 'work'; $highlight = false;
            include __DIR__ . '/../../components/shared/app-stat-card.php';

            $label = 'Total Applicants'; $value = $appCounts['total']; $icon = 'group'; $highlight = false;
            include __DIR__ . '/../../components/shared/app-stat-card.php';

            $label = 'Interviews This Week'; $value = (int)$weekStats['total']; $icon = 'event'; $highlight = true;
            include __DIR__ . '/../../components/shared/app-stat-card.php';

            $label = 'Offers Extended'; $value = $appCounts['offer']; $icon = 'handshake'; $highlight = false;
            include __DIR__ . '/../../components/shared/app-stat-card.php';
            ?>
        </section>

        <div class="emp-content">
            <!-- Recent Applicants -->
            <div class="emp-main-col">
                <section>
                    <div class="emp-section-head">
                        <h3 class="emp-section-title">Recent Applicants</h3>
                        <a href="../employer/candidates.php" class="emp-view-all">View All</a>
                    </div>
                    <div class="emp-table">
                        <div class="emp-table-header">
                            <span>Candidate</span>
                            <span>Position</span>
                            <span>Status</span>
                            <span>Applied</span>
                            <span>Action</span>
                        </div>
                        <?php if (empty($recentApplicants)): ?>
                            <p style="text-align:center; color:#7a6b5a; padding:2rem;">No applicants yet. Post a job to start receiving applications.</p>
                        <?php else: ?>
                            <?php foreach ($recentApplicants as $applicant):
                                $initials = strtoupper(substr($applicant['first_name'],0,1) . substr($applicant['last_name'],0,1));
                                $name = htmlspecialchars($applicant['first_name'] . ' ' . substr($applicant['last_name'],0,1) . '.');
                                $stageMap = ['applied'=>'applied','screening'=>'screening','interview'=>'interview','offer'=>'offer','rejected'=>'rejected','withdrawn'=>'withdrawn'];
                            ?>
                            <div class="emp-table-row">
                                <div class="emp-table-candidate">
                                    <div class="emp-avatar"><?= $initials ?></div>
                                    <div>
                                        <p class="emp-candidate-name"><?= $name ?></p>
                                        <p class="emp-candidate-email"><?= htmlspecialchars($applicant['email']) ?></p>
                                    </div>
                                </div>
                                <span class="emp-table-cell"><?= htmlspecialchars($applicant['job_title']) ?></span>
                                <span class="emp-stage-badge emp-stage--<?= $applicant['status'] ?>"><?= ucfirst($applicant['status']) ?></span>
                                <span class="emp-table-cell"><?= date('M j', strtotime($applicant['applied_at'])) ?></span>
                                <a href="../employer/candidate-detail.php?id=<?= $applicant['id'] ?>" class="emp-action-btn">
                                    <span class="material-symbols-outlined">visibility</span>
                                </a>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </section>

                <!-- Active Job Postings -->
                <section>
                    <div class="emp-section-head">
                        <h3 class="emp-section-title">Active Job Postings</h3>
                        <a href="../employer/jobs.php" class="emp-view-all">Manage All</a>
                    </div>
                    <div class="emp-job-grid">
                        <?php if (empty($activeJobs)): ?>
                            <p style="text-align:center; color:#7a6b5a; padding:2rem;">No active jobs. Create your first job posting!</p>
                        <?php else: ?>
                            <?php foreach (array_slice($activeJobs, 0, 3) as $job):
                                $jobTitle = htmlspecialchars($job['title']);
                                $department = htmlspecialchars($job['department'] ?? 'General');
                                $location = htmlspecialchars($job['location'] ?? 'Not specified');
                                $jobStatus = ucfirst($job['status']);
                                $statusType = $job['status'];
                                $applicants = (int)$job['applicant_count'];
                                $posted = date('M j', strtotime($job['created_at']));
                                include __DIR__ . '/../../components/employer/job-card.php';
                            endforeach; ?>
                        <?php endif; ?>
                    </div>
                </section>
            </div>

            <!-- Sidebar -->
            <div class="emp-sidebar">
                <!-- Upcoming Interviews -->
                <section>
                    <h3 class="emp-section-title emp-section-title--sm">Upcoming Interviews</h3>
                    <div class="emp-interview-list">
                        <?php if (empty($upcomingInterviews)): ?>
                            <p style="color:#7a6b5a; font-size:0.85rem;">No upcoming interviews scheduled.</p>
                        <?php else: ?>
                            <?php foreach ($upcomingInterviews as $iv):
                                $candidate = htmlspecialchars($iv['first_name'] . ' ' . substr($iv['last_name'],0,1) . '.');
                                $position = htmlspecialchars($iv['job_title']);
                                $isToday = ($iv['scheduled_date'] === date('Y-m-d'));
                                $dateLabel = $isToday ? 'Today' : date('M j', strtotime($iv['scheduled_date']));
                                $timeLabel = $iv['start_time'] ? date('g:i A', strtotime($iv['start_time'])) : '';
                                $interviewDate = $dateLabel . ($timeLabel ? ' • ' . $timeLabel : '');
                                $methodIcon = ($iv['interview_type'] === 'video') ? 'video_call' : (($iv['interview_type'] === 'phone') ? 'phone_in_talk' : 'meeting_room');
                                $methodText = ucfirst($iv['interview_type'] ?? 'Interview');
                                include __DIR__ . '/../../components/employer/interview-card.php';
                            endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <a href="../employer/interviews.php" class="emp-view-all-link">View All Interviews</a>
                </section>

                <!-- Quick Actions -->
                <section class="emp-quick-actions">
                    <h3 class="emp-section-title emp-section-title--sm">Quick Actions</h3>
                    <a href="../employer/job-create.php" class="emp-quick-btn">
                        <span class="material-symbols-outlined">edit_note</span>
                        Create Job Post
                    </a>
                    <a href="../employer/candidates.php" class="emp-quick-btn">
                        <span class="material-symbols-outlined">person_search</span>
                        Review Candidates
                    </a>
                    <a href="../shared/skill-assesment.php?tab=create" class="emp-quick-btn">
                        <span class="material-symbols-outlined">quiz</span>
                        Create Assessment
                    </a>
                </section>

                <!-- Hiring Funnel -->
                <section class="emp-funnel-panel">
                    <h3 class="emp-section-title emp-section-title--sm">Hiring Funnel</h3>
                    <div class="emp-funnel">
                        <?php
                        $stages = [
                            ['label' => 'Applied',     'count' => $appCounts['applied']],
                            ['label' => 'Screened',    'count' => $appCounts['screening']],
                            ['label' => 'Interviewed', 'count' => $appCounts['interview']],
                            ['label' => 'Offered',     'count' => $appCounts['offer']],
                        ];
                        foreach ($stages as $stage):
                            $pct = $funnelTotal > 0 ? round(($stage['count'] / $funnelTotal) * 100) : 0;
                        ?>
                        <div class="emp-funnel-stage">
                            <div class="emp-funnel-bar" style="width: <?= max($pct, 2) ?>%;"></div>
                            <div class="emp-funnel-label">
                                <span><?= $stage['label'] ?></span><span><?= $stage['count'] ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            </div>
        </div>
    </main>
</body>
</html>
