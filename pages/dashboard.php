<!DOCTYPE html>
<html lang="en">
<head>
    <?php $pageTitle = 'Dashboard | Hireable Employer'; ?>
    <?php include __DIR__ . '/../components/head.php'; ?>
</head>
<body class="dash-page">
    <?php $activePage = 'dashboard'; ?>
    <?php include __DIR__ . '/../components/employer-sidebar.php'; ?>

    <main class="dash-main" style="margin-left: 260px;">
        <!-- Header -->
        <div class="emp-header">
            <div>
                <h2 class="page-title">Welcome back, Acme Corp</h2>
                <p class="page-subtitle">Here's what's happening with your hiring pipeline today.</p>
            </div>
            <a href="jobs.php" class="emp-cta-btn">
                <span class="material-symbols-outlined">add</span>
                Post a Job
            </a>
        </div>

        <!-- Stats Row -->
        <section class="emp-stats">
            <?php
            $label = 'Active Jobs'; $value = '8'; $icon = 'work'; $highlight = false;
            include __DIR__ . '/../components/app-stat-card.php';

            $label = 'Total Applicants'; $value = '143'; $icon = 'group'; $highlight = false;
            include __DIR__ . '/../components/app-stat-card.php';

            $label = 'Interviews This Week'; $value = '6'; $icon = 'event'; $highlight = true;
            include __DIR__ . '/../components/app-stat-card.php';

            $label = 'Offers Extended'; $value = '3'; $icon = 'handshake'; $highlight = false;
            include __DIR__ . '/../components/app-stat-card.php';
            ?>
        </section>

        <div class="emp-content">
            <!-- Recent Applicants -->
            <div class="emp-main-col">
                <section>
                    <div class="emp-section-head">
                        <h3 class="emp-section-title">Recent Applicants</h3>
                        <a href="candidates.php" class="emp-view-all">View All</a>
                    </div>
                    <div class="emp-table">
                        <div class="emp-table-header">
                            <span>Candidate</span>
                            <span>Position</span>
                            <span>Match</span>
                            <span>Status</span>
                            <span>Action</span>
                        </div>
                        <div class="emp-table-row">
                            <div class="emp-table-candidate">
                                <div class="emp-avatar">SM</div>
                                <div>
                                    <p class="emp-candidate-name">Sarah M.</p>
                                    <p class="emp-candidate-email">sarah.m@email.com</p>
                                </div>
                            </div>
                            <span class="emp-table-cell">VP of Product</span>
                            <span class="emp-match-badge emp-match--high">92%</span>
                            <span class="emp-stage-badge emp-stage--interview">Interview</span>
                            <a href="candidate-detail.php" class="emp-action-btn">
                                <span class="material-symbols-outlined">visibility</span>
                            </a>
                        </div>
                        <div class="emp-table-row">
                            <div class="emp-table-candidate">
                                <div class="emp-avatar">DK</div>
                                <div>
                                    <p class="emp-candidate-name">Daniel K.</p>
                                    <p class="emp-candidate-email">daniel.k@email.com</p>
                                </div>
                            </div>
                            <span class="emp-table-cell">Senior Engineer</span>
                            <span class="emp-match-badge emp-match--mid">78%</span>
                            <span class="emp-stage-badge emp-stage--screening">Screening</span>
                            <a href="candidate-detail.php" class="emp-action-btn">
                                <span class="material-symbols-outlined">visibility</span>
                            </a>
                        </div>
                        <div class="emp-table-row">
                            <div class="emp-table-candidate">
                                <div class="emp-avatar">AL</div>
                                <div>
                                    <p class="emp-candidate-name">Aisha L.</p>
                                    <p class="emp-candidate-email">aisha.l@email.com</p>
                                </div>
                            </div>
                            <span class="emp-table-cell">Marketing Lead</span>
                            <span class="emp-match-badge emp-match--high">88%</span>
                            <span class="emp-stage-badge emp-stage--applied">Applied</span>
                            <a href="candidate-detail.php" class="emp-action-btn">
                                <span class="material-symbols-outlined">visibility</span>
                            </a>
                        </div>
                        <div class="emp-table-row">
                            <div class="emp-table-candidate">
                                <div class="emp-avatar">JT</div>
                                <div>
                                    <p class="emp-candidate-name">James T.</p>
                                    <p class="emp-candidate-email">james.t@email.com</p>
                                </div>
                            </div>
                            <span class="emp-table-cell">Data Analyst</span>
                            <span class="emp-match-badge emp-match--low">64%</span>
                            <span class="emp-stage-badge emp-stage--offer">Offer</span>
                            <a href="candidate-detail.php" class="emp-action-btn">
                                <span class="material-symbols-outlined">visibility</span>
                            </a>
                        </div>
                    </div>
                </section>

                <!-- Active Job Postings -->
                <section>
                    <div class="emp-section-head">
                        <h3 class="emp-section-title">Active Job Postings</h3>
                        <a href="jobs.php" class="emp-view-all">Manage All</a>
                    </div>
                    <div class="emp-job-grid">
                        <?php
                        $jobTitle = 'VP of Product Innovation'; $department = 'Product'; $location = 'Addis Ababa';
                        $jobStatus = 'Active'; $statusType = 'active'; $applicants = 34; $posted = '3 days ago';
                        include __DIR__ . '/../components/job-card.php';

                        $jobTitle = 'Senior Software Engineer'; $department = 'Engineering'; $location = 'Remote';
                        $jobStatus = 'Active'; $statusType = 'active'; $applicants = 52; $posted = '1 week ago';
                        include __DIR__ . '/../components/job-card.php';

                        $jobTitle = 'Marketing Lead'; $department = 'Marketing'; $location = 'Nairobi';
                        $jobStatus = 'Active'; $statusType = 'active'; $applicants = 18; $posted = '2 days ago';
                        include __DIR__ . '/../components/job-card.php';
                        ?>
                    </div>
                </section>
            </div>

            <!-- Sidebar -->
            <div class="emp-sidebar">
                <!-- Upcoming Interviews -->
                <section>
                    <h3 class="emp-section-title emp-section-title--sm">Upcoming Interviews</h3>
                    <div class="emp-interview-list">
                        <?php
                        $candidate = 'Sarah M.'; $position = 'VP of Product';
                        $interviewDate = 'Today • 2:00 PM'; $methodIcon = 'video_call'; $methodText = 'Zoom';
                        include __DIR__ . '/../components/interview-card.php';

                        $candidate = 'Daniel K.'; $position = 'Senior Engineer';
                        $interviewDate = 'Tomorrow • 10:30 AM'; $methodIcon = 'phone_in_talk'; $methodText = 'Phone Call';
                        include __DIR__ . '/../components/interview-card.php';
                        ?>
                    </div>
                    <a href="interviews.php" class="emp-view-all-link">View All Interviews</a>
                </section>

                <!-- Quick Actions -->
                <section class="emp-quick-actions">
                    <h3 class="emp-section-title emp-section-title--sm">Quick Actions</h3>
                    <a href="job-create.php" class="emp-quick-btn">
                        <span class="material-symbols-outlined">edit_note</span>
                        Create Job Post
                    </a>
                    <a href="candidates.php" class="emp-quick-btn">
                        <span class="material-symbols-outlined">person_search</span>
                        Review Candidates
                    </a>
                    <a href="skill-assesment.php?tab=create" class="emp-quick-btn">
                        <span class="material-symbols-outlined">quiz</span>
                        Create Assessment
                    </a>
                </section>

                <!-- Hiring Funnel -->
                <section class="emp-funnel-panel">
                    <h3 class="emp-section-title emp-section-title--sm">Hiring Funnel</h3>
                    <div class="emp-funnel">
                        <div class="emp-funnel-stage">
                            <div class="emp-funnel-bar" style="width: 100%;"></div>
                            <div class="emp-funnel-label">
                                <span>Applied</span><span>143</span>
                            </div>
                        </div>
                        <div class="emp-funnel-stage">
                            <div class="emp-funnel-bar" style="width: 60%;"></div>
                            <div class="emp-funnel-label">
                                <span>Screened</span><span>86</span>
                            </div>
                        </div>
                        <div class="emp-funnel-stage">
                            <div class="emp-funnel-bar" style="width: 30%;"></div>
                            <div class="emp-funnel-label">
                                <span>Interviewed</span><span>42</span>
                            </div>
                        </div>
                        <div class="emp-funnel-stage">
                            <div class="emp-funnel-bar" style="width: 10%;"></div>
                            <div class="emp-funnel-label">
                                <span>Offered</span><span>12</span>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </main>
</body>
</html>
