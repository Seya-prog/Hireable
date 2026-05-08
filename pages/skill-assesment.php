<!DOCTYPE html>
<html lang="en">

<head>
    <?php $pageTitle = 'Skill Assessments | Hireable'; ?>
    <?php include __DIR__ . '/../components/head.php'; ?>
</head>

<body class="dash-page">
    <?php
    session_start();
    $userRole = $_SESSION['user_role'] ?? 'employer';
    $activePage = 'skill-assessments';

    if ($userRole === 'employer') {
        include __DIR__ . '/../components/employer-sidebar.php';
        $currentTab = isset($_GET['tab']) ? $_GET['tab'] : 'my-assessments';
    } else {
        include __DIR__ . '/../components/sidebar.php';
        $currentTab = isset($_GET['tab']) ? $_GET['tab'] : 'available';
    }
    ?>

    <main class="dash-main skill-main" style="margin-left: 260px;">
        <?php if ($userRole === 'employer'): ?>
            <!-- Employer Assessment Header -->
            <?php include __DIR__ . '/../components/assess-header.php'; ?>
        <?php else: ?>
            <!-- Employee Header + Tabs -->
            <?php include __DIR__ . '/../components/skill-header.php'; ?>
        <?php endif; ?>

        <div class="skill-content <?= $userRole === 'employer' ? 'skill-content--employer' : '' ?>">
            <!-- Left Side -->
            <div class="skill-left">

                <?php if ($userRole === 'employer'): ?>
                    <!-- ========== EMPLOYER CONTENT ========== -->
                    <?php if ($currentTab === 'my-assessments'): ?>
                        <?php include __DIR__ . '/../components/assess-list.php'; ?>
                    <?php elseif ($currentTab === 'create'): ?>
                        <?php include __DIR__ . '/../components/assess-create.php'; ?>
                    <?php elseif ($currentTab === 'results'): ?>
                        <?php include __DIR__ . '/../components/assess-results.php'; ?>
                    <?php endif; ?>

                <?php else: ?>
                    <!-- ========== EMPLOYEE CONTENT ========== -->
                    <?php if ($currentTab === 'available'): ?>
                        <!-- ========== AVAILABLE TAB ========== -->
                        <section>
                            <div class="skill-section-head">
                                <h3 class="skill-section-title">Requested by Hiring Partners</h3>
                                <span class="skill-pending-badge">2 Pending</span>
                            </div>
                            <div class="skill-card-grid">
                                <?php
                                $icon = 'business';
                                $expiry = 'Expiring in 3d';
                                $title = 'React Senior Developer Test';
                                $company = 'Acme Studio';
                                $duration = '45 Mins';
                                $level = 'Expert';
                                include __DIR__ . '/../components/skill-assessment-card.php';

                                $icon = 'palette';
                                $expiry = 'Expiring in 5d';
                                $title = 'Product Design Systems';
                                $company = 'Lumina Creative';
                                $duration = '60 Mins';
                                $level = 'Intermediate';
                                include __DIR__ . '/../components/skill-assessment-card.php';
                                ?>
                            </div>
                        </section>

                        <section>
                            <div class="skill-section-head">
                                <div>
                                    <h3 class="skill-section-title">Curated for You</h3>
                                    <p class="skill-section-subtitle">Based on your Product Designer profile</p>
                                </div>
                            </div>
                            <div class="skill-recommendations">
                                <div class="skill-recommend-item">
                                    <div class="skill-recommend-left">
                                        <div class="skill-recommend-icon skill-recommend-icon--secondary">
                                            <span class="material-symbols-outlined">draw</span>
                                        </div>
                                        <div>
                                            <h5 class="skill-recommend-title">Figma Mastery</h5>
                                            <p class="skill-recommend-desc">Advanced Auto Layout &amp; Variables</p>
                                        </div>
                                    </div>
                                    <div class="skill-recommend-right">
                                        <div class="skill-recommend-stats">
                                            <div class="skill-recommend-stat-value">2,400 Candidates</div>
                                            <div class="skill-recommend-stat-label">Already Certified</div>
                                        </div>
                                        <span class="material-symbols-outlined skill-recommend-arrow">arrow_forward</span>
                                    </div>
                                </div>
                                <div class="skill-recommend-item">
                                    <div class="skill-recommend-left">
                                        <div class="skill-recommend-icon skill-recommend-icon--tertiary">
                                            <span class="material-symbols-outlined">group</span>
                                        </div>
                                        <div>
                                            <h5 class="skill-recommend-title">User Research Fundamentals</h5>
                                            <p class="skill-recommend-desc">Quantitative &amp; Qualitative Methodologies</p>
                                        </div>
                                    </div>
                                    <div class="skill-recommend-right">
                                        <div class="skill-recommend-stats">
                                            <div class="skill-recommend-stat-value">1,150 Candidates</div>
                                            <div class="skill-recommend-stat-label">Already Certified</div>
                                        </div>
                                        <span class="material-symbols-outlined skill-recommend-arrow">arrow_forward</span>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <?php include __DIR__ . '/../components/skill-catalog.php'; ?>

                    <?php elseif ($currentTab === 'completed'): ?>
                        <!-- ========== COMPLETED TAB ========== -->
                        <section>
                            <div class="skill-section-head">
                                <h3 class="skill-section-title">Requested by Hiring Partners</h3>
                                <span class="skill-pending-badge">2 Completed</span>
                            </div>
                            <div class="skill-card-grid">
                                <?php
                                $icon = 'verified_user';
                                $score = '94%';
                                $date = 'Passed Oct 05';
                                $title = 'Design Systems Ops';
                                $company = 'Lumina Creative';
                                include __DIR__ . '/../components/skill-completed-card.php';

                                $icon = 'verified_user';
                                $score = '89%';
                                $date = 'Passed Sep 12';
                                $title = 'Full-Stack Architecture';
                                $company = 'Acme Studio';
                                include __DIR__ . '/../components/skill-completed-card.php';
                                ?>
                            </div>
                        </section>

                        <section class="skill-certifications-panel">
                            <div class="skill-section-head">
                                <div>
                                    <h3 class="skill-section-title">Completed Certifications</h3>
                                    <p class="skill-section-subtitle">Your verified professional achievements</p>
                                </div>
                                <span class="skill-pending-badge">12 Total</span>
                            </div>
                            <div class="skill-cert-list">
                                <?php
                                $name = 'Information Architecture';
                                $date = 'Certified on Oct 12, 2023';
                                $score = '98%';
                                include __DIR__ . '/../components/skill-cert-item.php';

                                $name = 'UX Writing Essentials';
                                $date = 'Certified on Sep 28, 2023';
                                $score = '92%';
                                include __DIR__ . '/../components/skill-cert-item.php';

                                $name = 'Design Thinking Fundamentals';
                                $date = 'Certified on Aug 15, 2023';
                                $score = '87%';
                                include __DIR__ . '/../components/skill-cert-item.php';

                                $name = 'Visual Communication';
                                $date = 'Certified on July 22, 2023';
                                $score = '95%';
                                include __DIR__ . '/../components/skill-cert-item.php';
                                ?>
                            </div>
                            <div style="text-align: center; margin-top: 2rem;">
                                <button class="skill-load-more-btn">Load More Certificates</button>
                            </div>
                        </section>

                        <?php include __DIR__ . '/../components/skill-catalog.php'; ?>

                    <?php elseif ($currentTab === 'progress'): ?>
                        <!-- ========== IN PROGRESS TAB ========== -->
                        <section>
                            <div class="skill-section-head">
                                <h3 class="skill-section-title">Continue Your Assessments</h3>
                                <span class="skill-pending-badge">2 Active</span>
                            </div>
                            <div class="skill-progress-cards">
                                <?php
                                $icon = 'palette';
                                $title = 'Product Design Systems';
                                $requester = 'Requested by Lumina Creative';
                                $timeLeft = '12:45 Remaining';
                                $expires = 'Time expires in 1 day';
                                $progress = 65;
                                $questions = '13/20 Questions';
                                $level = 'Intermediate';
                                include __DIR__ . '/../components/skill-progress-card.php';

                                $icon = 'accessibility_new';
                                $title = 'Web Accessibility Specialist';
                                $requester = 'Global Certification';
                                $timeLeft = '25:10 Remaining';
                                $expires = 'Self-paced Assessment';
                                $progress = 22;
                                $questions = '4/18 Questions';
                                $level = 'Advanced';
                                include __DIR__ . '/../components/skill-progress-card.php';
                                ?>
                            </div>
                        </section>

                        <section class="skill-certifications-panel">
                            <div class="skill-section-head">
                                <div>
                                    <h3 class="skill-section-title">Completed Certifications</h3>
                                    <p class="skill-section-subtitle">Your verified professional achievements</p>
                                </div>
                                <a class="skill-view-all-link" href="skill-assesment.php?tab=completed">View All
                                    Certificates</a>
                            </div>
                            <div class="skill-cert-list">
                                <?php
                                $name = 'Information Architecture';
                                $date = 'Certified on Oct 12, 2023';
                                $score = '98%';
                                include __DIR__ . '/../components/skill-cert-item.php';

                                $name = 'UX Writing Essentials';
                                $date = 'Certified on Sep 28, 2023';
                                $score = '92%';
                                include __DIR__ . '/../components/skill-cert-item.php';
                                ?>
                            </div>
                        </section>

                        <?php include __DIR__ . '/../components/skill-catalog.php'; ?>

                    <?php endif; ?>
                <?php endif; /* end employee role */ ?>

            </div>

            <!-- Right Side: Mastery Panel (employee) or Summary Panel (employer) -->
            <?php if ($userRole === 'employer'): ?>
                <!-- Employer sidebar summary can go here -->
            <?php else: ?>
                <?php include __DIR__ . '/../components/skill-mastery-panel.php'; ?>
            <?php endif; ?>
        </div>
    </main>
</body>

</html>