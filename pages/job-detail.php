<!DOCTYPE html>
<html lang="en">
<head>
    <?php $pageTitle = 'Job Detail | Hireable Employer'; ?>
    <?php include __DIR__ . '/../components/head.php'; ?>
</head>
<body class="dash-page">
    <?php $activePage = 'jobs'; ?>
    <?php include __DIR__ . '/../components/employer-sidebar.php'; ?>

    <main class="dash-main" style="margin-left: 260px;">
        <div class="emp-header">
            <div>
                <a href="jobs.php" class="emp-back-link">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Back to Job Postings
                </a>
                <h2 class="page-title">VP of Product Innovation</h2>
                <p class="page-subtitle">Product • Addis Ababa • Full-time • Senior Level</p>
            </div>
            <div class="emp-header-actions">
                <button class="assess-save-btn assess-save-btn--draft">Edit Post</button>
                <span class="emp-status-badge emp-status--active">Active</span>
            </div>
        </div>

        <div class="emp-detail-layout">
            <!-- Main Content -->
            <div class="emp-detail-main">
                <!-- Stats -->
                <div class="emp-detail-stats">
                    <div class="emp-detail-stat-card">
                        <span class="emp-detail-stat-value">34</span>
                        <span class="emp-detail-stat-label">Total Applicants</span>
                    </div>
                    <div class="emp-detail-stat-card">
                        <span class="emp-detail-stat-value">12</span>
                        <span class="emp-detail-stat-label">Shortlisted</span>
                    </div>
                    <div class="emp-detail-stat-card">
                        <span class="emp-detail-stat-value">5</span>
                        <span class="emp-detail-stat-label">Interviews</span>
                    </div>
                    <div class="emp-detail-stat-card">
                        <span class="emp-detail-stat-value">87%</span>
                        <span class="emp-detail-stat-label">Avg Match</span>
                    </div>
                </div>

                <!-- Applicants Table -->
                <section>
                    <div class="emp-section-head">
                        <h3 class="emp-section-title">Applicants</h3>
                        <div class="emp-filter-actions">
                            <div class="emp-search-wrap">
                                <span class="material-symbols-outlined">search</span>
                                <input class="emp-search-input" type="text" placeholder="Search applicants...">
                            </div>
                        </div>
                    </div>
                    <div class="emp-table">
                        <div class="emp-table-header" style="grid-template-columns: 2fr 0.7fr 0.8fr 0.8fr 0.5fr;">
                            <span>Candidate</span>
                            <span>Match</span>
                            <span>Assessment</span>
                            <span>Stage</span>
                            <span>Action</span>
                        </div>
                        <div class="emp-table-row" style="grid-template-columns: 2fr 0.7fr 0.8fr 0.8fr 0.5fr;">
                            <div class="emp-table-candidate">
                                <div class="emp-avatar">SM</div>
                                <div>
                                    <p class="emp-candidate-name">Sarah M.</p>
                                    <p class="emp-candidate-email">sarah.m@email.com</p>
                                </div>
                            </div>
                            <span class="emp-match-badge emp-match--high">92%</span>
                            <span class="emp-score emp-score--high">94%</span>
                            <span class="emp-stage-badge emp-stage--interview">Interview</span>
                            <a href="candidate-detail.php" class="emp-action-btn"><span class="material-symbols-outlined">visibility</span></a>
                        </div>
                        <div class="emp-table-row" style="grid-template-columns: 2fr 0.7fr 0.8fr 0.8fr 0.5fr;">
                            <div class="emp-table-candidate">
                                <div class="emp-avatar">MW</div>
                                <div>
                                    <p class="emp-candidate-name">Miriam W.</p>
                                    <p class="emp-candidate-email">miriam.w@email.com</p>
                                </div>
                            </div>
                            <span class="emp-match-badge emp-match--high">85%</span>
                            <span class="emp-score emp-score--mid">76%</span>
                            <span class="emp-stage-badge emp-stage--screening">Screening</span>
                            <a href="candidate-detail.php" class="emp-action-btn"><span class="material-symbols-outlined">visibility</span></a>
                        </div>
                        <div class="emp-table-row" style="grid-template-columns: 2fr 0.7fr 0.8fr 0.8fr 0.5fr;">
                            <div class="emp-table-candidate">
                                <div class="emp-avatar">TB</div>
                                <div>
                                    <p class="emp-candidate-name">Tariku B.</p>
                                    <p class="emp-candidate-email">tariku.b@email.com</p>
                                </div>
                            </div>
                            <span class="emp-match-badge emp-match--mid">71%</span>
                            <span class="emp-table-cell">—</span>
                            <span class="emp-stage-badge emp-stage--applied">Applied</span>
                            <a href="candidate-detail.php" class="emp-action-btn"><span class="material-symbols-outlined">visibility</span></a>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Sidebar -->
            <div class="emp-detail-sidebar">
                <section class="emp-detail-panel">
                    <h4 class="emp-section-title emp-section-title--sm">Job Details</h4>
                    <div class="emp-detail-info">
                        <div class="emp-detail-row"><span>Posted</span><span>3 days ago</span></div>
                        <div class="emp-detail-row"><span>Salary Range</span><span>$120k – $160k</span></div>
                        <div class="emp-detail-row"><span>Employment</span><span>Full-time</span></div>
                        <div class="emp-detail-row"><span>Experience</span><span>Senior (8+ yrs)</span></div>
                        <div class="emp-detail-row"><span>Assessment</span><span>Product Strategy</span></div>
                    </div>
                </section>

                <section class="emp-detail-panel">
                    <h4 class="emp-section-title emp-section-title--sm">Required Skills</h4>
                    <div class="emp-cand-skills">
                        <span class="emp-cand-skill-tag">Product Strategy</span>
                        <span class="emp-cand-skill-tag">Leadership</span>
                        <span class="emp-cand-skill-tag">Agile</span>
                        <span class="emp-cand-skill-tag">Data Analytics</span>
                        <span class="emp-cand-skill-tag">Roadmapping</span>
                        <span class="emp-cand-skill-tag">User Research</span>
                    </div>
                </section>

                <section class="emp-detail-panel">
                    <h4 class="emp-section-title emp-section-title--sm">Quick Actions</h4>
                    <a href="interview-schedule.php" class="emp-quick-btn">
                        <span class="material-symbols-outlined">calendar_month</span>
                        Schedule Interview
                    </a>
                    <a href="skill-assesment.php?tab=results" class="emp-quick-btn">
                        <span class="material-symbols-outlined">quiz</span>
                        View Assessment Results
                    </a>
                </section>
            </div>
        </div>
    </main>
</body>
</html>
