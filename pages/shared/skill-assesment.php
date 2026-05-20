<?php
require_once __DIR__ . '/../../backend/helpers/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../database/repositories/AssessmentRepository.php';
require_once __DIR__ . '/../../database/repositories/UserRepository.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php $pageTitle = 'Skill Assessments | Hireable'; ?>
    <?php $pageCss = ['employer.css', 'employee.css', 'assessments.css', 'toast.css'];
    include __DIR__ . '/../../components/shared/head.php'; ?>
</head>

<body class="dash-page">
    <?php
    $userRole = getCurrentUserRole() ?? 'employer';
    $userId   = getCurrentUserId();
    $activePage = 'skill-assessments';
    $assessRepo = new AssessmentRepository($pdo);

    if ($userRole === 'employer') {
        $employerId = $userId;
        include __DIR__ . '/../../components/employer/employer-sidebar.php';
        $currentTab = isset($_GET['tab']) ? $_GET['tab'] : 'my-assessments';
    } else {
        $employeeId = $userId;
        include __DIR__ . '/../../components/employee/sidebar.php';
        $currentTab = isset($_GET['tab']) ? $_GET['tab'] : 'available';

        // Fetch employee assessment data
        $availableAssessments  = $assessRepo->findAvailableForEmployee($employeeId);
        $completedAssessments  = $assessRepo->findCompletedByEmployee($employeeId);
        $inProgressAssessments = $assessRepo->findInProgressByEmployee($employeeId);
    }
    ?>

    <main class="dash-main skill-main" style="margin-left: 260px;">
        <?php if ($userRole === 'employer'): ?>
            <?php include __DIR__ . '/../../components/employer/assess-header.php'; ?>
        <?php else: ?>
            <?php include __DIR__ . '/../../components/employee/skill-header.php'; ?>
        <?php endif; ?>

        <div class="skill-content <?= $userRole === 'employer' ? 'skill-content--employer' : '' ?>">
            <!-- Left Side -->
            <div class="skill-left">

                <?php if ($userRole === 'employer'): ?>
                    <!-- ========== EMPLOYER CONTENT ========== -->
                    <?php if ($currentTab === 'my-assessments'): ?>
                        <?php include __DIR__ . '/../../components/employer/assess-list.php'; ?>
                    <?php elseif ($currentTab === 'create'): ?>
                        <?php include __DIR__ . '/../../components/employer/assess-create.php'; ?>
                    <?php elseif ($currentTab === 'results'): ?>
                        <?php include __DIR__ . '/../../components/employer/assess-results.php'; ?>
                    <?php endif; ?>

                <?php else: ?>
                    <!-- ========== EMPLOYEE CONTENT ========== -->
                    <?php if ($currentTab === 'available'): ?>
                        <!-- ========== AVAILABLE TAB ========== -->
                        <section>
                            <div class="skill-section-head">
                                <h3 class="skill-section-title">Available Assessments</h3>
                                <span class="skill-pending-badge"><?= count($availableAssessments) ?> Available</span>
                            </div>
                            <div class="skill-card-grid">
                                <?php if (empty($availableAssessments)): ?>
                                    <p style="color:#7a6b5a; padding:1rem;">No assessments available right now. Check back later!</p>
                                <?php else: ?>
                                    <?php foreach ($availableAssessments as $a):
                                        $icon = 'quiz';
                                        $expiry = '';
                                        $title = htmlspecialchars($a['title']);
                                        $company = htmlspecialchars($a['company_name'] ?? '');
                                        $duration = ($a['time_limit_minutes'] ?? 45) . ' Mins';
                                        $level = ucfirst($a['difficulty'] ?? 'Intermediate');
                                        include __DIR__ . '/../../components/employee/skill-assessment-card.php';
                                    endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </section>

                    <?php elseif ($currentTab === 'completed'): ?>
                        <!-- ========== COMPLETED TAB ========== -->
                        <section>
                            <div class="skill-section-head">
                                <h3 class="skill-section-title">Completed Assessments</h3>
                                <span class="skill-pending-badge"><?= count($completedAssessments) ?> Completed</span>
                            </div>
                            <div class="skill-card-grid">
                                <?php if (empty($completedAssessments)): ?>
                                    <p style="color:#7a6b5a; padding:1rem;">No completed assessments yet.</p>
                                <?php else: ?>
                                    <?php foreach ($completedAssessments as $c):
                                        $icon = 'verified_user';
                                        $score = $c['score'] !== null ? round($c['score']) . '%' : '—';
                                        $date = $c['completed_at'] ? 'Passed ' . date('M j', strtotime($c['completed_at'])) : '';
                                        $title = htmlspecialchars($c['title']);
                                        $company = htmlspecialchars($c['company_name'] ?? '');
                                        include __DIR__ . '/../../components/employee/skill-completed-card.php';
                                    endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </section>

                    <?php elseif ($currentTab === 'progress'): ?>
                        <!-- ========== IN PROGRESS TAB ========== -->
                        <section>
                            <div class="skill-section-head">
                                <h3 class="skill-section-title">Continue Your Assessments</h3>
                                <span class="skill-pending-badge"><?= count($inProgressAssessments) ?> Active</span>
                            </div>
                            <div class="skill-progress-cards">
                                <?php if (empty($inProgressAssessments)): ?>
                                    <p style="color:#7a6b5a; padding:1rem;">No assessments in progress.</p>
                                <?php else: ?>
                                    <?php foreach ($inProgressAssessments as $ip):
                                        $icon = 'quiz';
                                        $title = htmlspecialchars($ip['title']);
                                        $requester = htmlspecialchars($ip['company_name'] ?? '');
                                        $totalQ = (int)($ip['total_questions'] ?? 0);
                                        $answeredQ = (int)($ip['answered_count'] ?? 0);
                                        $progress = $totalQ > 0 ? round(($answeredQ / $totalQ) * 100) : 0;
                                        $questions = $answeredQ . '/' . $totalQ . ' Questions';
                                        $timeLeft = '';
                                        $expires = '';
                                        $level = ucfirst($ip['difficulty'] ?? 'Intermediate');
                                        include __DIR__ . '/../../components/employee/skill-progress-card.php';
                                    endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </section>

                    <?php endif; ?>
                <?php endif; /* end employee role */ ?>

            </div>

            <!-- Right Side: Mastery Panel (employee) or Summary Panel (employer) -->
            <?php if ($userRole === 'employer'): ?>
                <!-- Employer sidebar summary can go here -->
            <?php else: ?>
                <?php include __DIR__ . '/../../components/employee/skill-mastery-panel.php'; ?>
            <?php endif; ?>
        </div>
    </main>
</body>

</html>