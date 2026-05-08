<!DOCTYPE html>
<html lang="en">
<head>
    <?php $pageTitle = 'Applications | Hireable'; ?>
    <?php include __DIR__ . '/../components/head.php'; ?>
</head>
<body class="dash-page">
    <?php $activePage = 'applications'; ?>
    <?php include __DIR__ . '/../components/sidebar.php'; ?>

    <main class="dash-main" style="margin-left: 260px;">
        <!-- Header -->
        <div class="app-header">
            <div>
                <h2 class="page-title">Application Tracker</h2>
                <p class="page-subtitle">Curating your executive career journey.</p>
            </div>
            <a href="job-search.php" class="app-new-btn" style="text-decoration: none;">
                <span class="material-symbols-outlined">add</span>
                New Application
            </a>
        </div>

        <!-- Status Summary Bar -->
        <section class="app-stats">
            <?php
            $label = 'Total Active'; $value = '12'; $highlight = false;
            include __DIR__ . '/../components/app-stat-card.php';

            $label = 'Interviewing'; $value = '4'; $highlight = false;
            include __DIR__ . '/../components/app-stat-card.php';

            $label = 'Offers Received'; $value = '2'; $highlight = true;
            include __DIR__ . '/../components/app-stat-card.php';

            $label = 'Response Rate'; $value = '68%'; $highlight = false;
            include __DIR__ . '/../components/app-stat-card.php';
            ?>
        </section>

        <div class="app-content">
            <!-- Applications List -->
            <div class="app-pipeline">
                <div class="app-pipeline-header">
                    <h3 class="app-section-title">Active Pipeline</h3>
                    <div class="app-pipeline-actions">
                        <button class="app-action-btn">
                            <span class="material-symbols-outlined">filter_list</span> Filter
                        </button>
                        <button class="app-action-btn">
                            <span class="material-symbols-outlined">sort</span> Sort
                        </button>
                    </div>
                </div>

                <div class="app-cards">
                    <?php
                    $logo = 'https://lh3.googleusercontent.com/aida-public/AB6AXuD7W6n1tDXbKtzRlsNX5qv8ApcQiR6cs4ydzo2TEe8v4THJtGUhCXKHonvZoF2UZ-3V1HpDEHqS7-D8b0VpvmHHtICV_QcXTs0_8ixhlPYXza8iKrreGaPImF_EUvcHlWcrg3zg8F_ywUveRcHYPw8DArgL81ohHfx5Xxhtdcs7ijuRPVK_fuPVZXwK0pVFdQIoB7MO5KIcXbAuC8KqpueXgOoKeDfq3Fl8fLOR6nXgQrZHCCfHTlb6OQQqbkXK98t4E-drvUu6YEZC';
                    $title = 'VP of Product Innovation'; $company = 'Lumina Global';
                    $appliedDate = 'Applied Oct 12, 2023'; $status = 'Interviewing';
                    $statusType = 'interview'; $nextStep = 'Panel Oct 25';
                    include __DIR__ . '/../components/app-card.php';

                    $logo = 'https://lh3.googleusercontent.com/aida-public/AB6AXuCs1fhoKUSerMVYeFrl8u-DgBZ8zRlxgxllDACpI_UzowQ0jlHLMVgwjbYZe-MokjHP8Sro7QoRU6scrGaXSu2-wgAue7dXqZqFdEVwwCYWM5hmcZVF5H12IY7uKHzhcrKhDBOfymyiuU02YztAgjb4GXMKfRM3R5Vc7zIkUaAcrNmF1VMkaHrUiJHDVyCThkbYNDKqD33ntyU0NRBx1w0AsLraoDOlvuZLk0ABqjBx-6bd9BRHIMaP4SjhQ9iyJLB44NAaWA0fLW7A';
                    $title = 'Executive Strategy Lead'; $company = 'Vanguard Partners';
                    $appliedDate = 'Applied Oct 08, 2023'; $status = 'Reviewing';
                    $statusType = 'review'; $nextStep = 'Awaiting Feedback';
                    include __DIR__ . '/../components/app-card.php';

                    $logo = 'https://lh3.googleusercontent.com/aida-public/AB6AXuCzfNeEBykrhnpji_QXLbPwjA48UZMpcxAEGvQX2blBb4G7WFq1qrYfHL86X8axWTnKSC7ncATRHUbOE4jdLELVV0dbWcFzES46RrKnusmFrexkFXWx9SD4jiwQkXRkBtIwyiMrNimIExmMGB7wFnKw41ZeQC_zR2D4petjpYz9ZBfCLvMptmjzsK_hNOwoW55j7M5gijV9gNL6VlFYajb6b0nBFy2Z_UZ6aPfbGVpNFD7Pm20x_JncTLoRis9mdtfXRwuXwGqwsV_x';
                    $title = 'Director of Operations'; $company = 'Evergreen Scale';
                    $appliedDate = 'Applied Oct 05, 2023'; $status = 'Offer Phase';
                    $statusType = 'offer'; $nextStep = 'Contract Review';
                    include __DIR__ . '/../components/app-card.php';
                    ?>
                </div>
            </div>

            <!-- Right Sidebar -->
            <div class="app-sidebar">
                <!-- Upcoming Interviews -->
                <section>
                    <h3 class="app-section-title app-section-title--row">
                        Interviews
                        <span class="app-section-tag">This Week</span>
                    </h3>
                    <div class="app-interview-list">
                        <?php
                        $date = 'Oct 25 • 2:00 PM'; $company = 'Lumina Global';
                        $description = 'Round 2: Technical &amp; Leadership';
                        $methodIcon = 'video_call'; $methodText = 'Zoom Meeting';
                        include __DIR__ . '/../components/app-interview-card.php';

                        $date = 'Oct 27 • 10:30 AM'; $company = 'Nexus Systems';
                        $description = 'Initial Screening';
                        $methodIcon = 'phone_in_talk'; $methodText = 'Call with Sarah M.';
                        include __DIR__ . '/../components/app-interview-card.php';
                        ?>
                    </div>
                </section>

                <!-- Pending Tasks -->
                <section>
                    <h3 class="app-section-title">Pending Tasks</h3>
                    <div class="app-tasks">
                        <?php
                        $taskName = 'Submit Case Study'; $taskDue = 'Due: Tomorrow, 5 PM';
                        include __DIR__ . '/../components/app-task-item.php';

                        $taskName = 'Update Portfolio Assets'; $taskDue = 'Due: Friday';
                        include __DIR__ . '/../components/app-task-item.php';

                        $taskName = 'Follow-up with Vanguard'; $taskDue = 'Pending 3 days';
                        include __DIR__ . '/../components/app-task-item.php';
                        ?>
                    </div>
                    <a href="#" class="app-view-all-btn" style="text-decoration: none;">View All Tasks</a>
                </section>

                <!-- Market Insights -->
                <section class="app-insights">
                    <div class="app-insights-content">
                        <h4 class="app-insights-title">Market Insights</h4>
                        <p class="app-insights-text">Your profile is trending 24% higher than similar executive candidates this week.</p>
                        <a class="app-insights-link" href="profile.php">View Profile Insights</a>
                    </div>
                    <div class="app-insights-icon">
                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">trending_up</span>
                    </div>
                </section>
            </div>
        </div>
    </main>
</body>
</html>