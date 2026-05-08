<!DOCTYPE html>
<html lang="en">
<head>
    <?php $pageTitle = 'Interviews | Hireable Employer'; ?>
    <?php include __DIR__ . '/../components/head.php'; ?>
</head>
<body class="dash-page">
    <?php $activePage = 'interviews'; ?>
    <?php include __DIR__ . '/../components/employer-sidebar.php'; ?>

    <main class="dash-main" style="margin-left: 260px;">
        <div class="emp-header">
            <div>
                <h2 class="page-title">Interviews</h2>
                <p class="page-subtitle">Schedule, track, and manage all candidate interviews.</p>
            </div>
            <a href="interview-schedule.php" class="emp-cta-btn">
                <span class="material-symbols-outlined">add</span>
                Schedule Interview
            </a>
        </div>

        <div class="emp-interview-content">
            <!-- Main: Interview Schedule -->
            <div class="emp-interview-main">
                <!-- Today -->
                <section>
                    <h3 class="emp-day-label">
                        <span class="emp-day-dot emp-day-dot--today"></span>
                        Today — May 7, 2026
                    </h3>
                    <div class="emp-interview-day-list">
                        <?php
                        $candidate = 'Sarah M.'; $position = 'VP of Product';
                        $interviewDate = '2:00 PM – 3:00 PM'; $methodIcon = 'video_call'; $methodText = 'Zoom Meeting';
                        include __DIR__ . '/../components/interview-card.php';

                        $candidate = 'Miriam W.'; $position = 'VP of Product';
                        $interviewDate = '4:30 PM – 5:15 PM'; $methodIcon = 'phone_in_talk'; $methodText = 'Phone Call';
                        include __DIR__ . '/../components/interview-card.php';
                        ?>
                    </div>
                </section>

                <!-- Tomorrow -->
                <section>
                    <h3 class="emp-day-label">
                        <span class="emp-day-dot"></span>
                        Tomorrow — May 8, 2026
                    </h3>
                    <div class="emp-interview-day-list">
                        <?php
                        $candidate = 'Daniel K.'; $position = 'Senior Engineer';
                        $interviewDate = '10:30 AM – 11:30 AM'; $methodIcon = 'video_call'; $methodText = 'Google Meet';
                        include __DIR__ . '/../components/interview-card.php';
                        ?>
                    </div>
                </section>

                <!-- This Week -->
                <section>
                    <h3 class="emp-day-label">
                        <span class="emp-day-dot"></span>
                        Friday — May 9, 2026
                    </h3>
                    <div class="emp-interview-day-list">
                        <?php
                        $candidate = 'Aisha L.'; $position = 'Marketing Lead';
                        $interviewDate = '9:00 AM – 10:00 AM'; $methodIcon = 'meeting_room'; $methodText = 'In-person, HQ';
                        include __DIR__ . '/../components/interview-card.php';

                        $candidate = 'Tariku B.'; $position = 'Senior Engineer';
                        $interviewDate = '1:00 PM – 2:00 PM'; $methodIcon = 'video_call'; $methodText = 'Zoom Meeting';
                        include __DIR__ . '/../components/interview-card.php';

                        $candidate = 'James T.'; $position = 'Data Analyst';
                        $interviewDate = '3:30 PM – 4:15 PM'; $methodIcon = 'phone_in_talk'; $methodText = 'Phone Call';
                        include __DIR__ . '/../components/interview-card.php';
                        ?>
                    </div>
                </section>
            </div>

            <!-- Sidebar: Summary -->
            <div class="emp-interview-sidebar">
                <section class="emp-int-summary-panel">
                    <h3 class="emp-section-title emp-section-title--sm">This Week</h3>
                    <div class="emp-int-summary-stats">
                        <div class="emp-int-summary-stat">
                            <span class="emp-int-summary-value">6</span>
                            <span class="emp-int-summary-label">Scheduled</span>
                        </div>
                        <div class="emp-int-summary-stat">
                            <span class="emp-int-summary-value">2</span>
                            <span class="emp-int-summary-label">Completed</span>
                        </div>
                        <div class="emp-int-summary-stat">
                            <span class="emp-int-summary-value">1</span>
                            <span class="emp-int-summary-label">Pending Feedback</span>
                        </div>
                    </div>
                </section>

                <section class="emp-int-feedback-panel">
                    <h3 class="emp-section-title emp-section-title--sm">Pending Feedback</h3>
                    <div class="emp-int-feedback-item">
                        <div class="emp-avatar">KM</div>
                        <div>
                            <p class="emp-int-fb-name">Kebede M.</p>
                            <p class="emp-int-fb-role">UX Designer • Interviewed May 5</p>
                        </div>
                        <a href="interview-feedback.php" class="emp-int-fb-btn" style="text-decoration:none;">Add Feedback</a>
                    </div>
                </section>
            </div>
        </div>
    </main>
</body>
</html>
