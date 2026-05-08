<!DOCTYPE html>
<html lang="en">
<head>
    <?php $pageTitle = 'Candidate Detail | Hireable Employer'; ?>
    <?php include __DIR__ . '/../components/head.php'; ?>
</head>
<body class="dash-page">
    <?php $activePage = 'candidates'; ?>
    <?php include __DIR__ . '/../components/employer-sidebar.php'; ?>

    <main class="dash-main" style="margin-left: 260px;">
        <div class="emp-header">
            <div>
                <a href="candidates.php" class="emp-back-link">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Back to Candidates
                </a>
                <h2 class="page-title">Sarah M.</h2>
                <p class="page-subtitle">Applied for VP of Product Innovation • 3 days ago</p>
            </div>
            <div class="emp-header-actions">
                <button class="assess-save-btn assess-save-btn--draft">Reject</button>
                <a href="interview-schedule.php" class="assess-save-btn assess-save-btn--publish" style="text-decoration:none;">Schedule Interview</a>
            </div>
        </div>

        <div class="emp-detail-layout">
            <div class="emp-detail-main">
                <!-- Match & Assessment -->
                <div class="emp-detail-stats">
                    <div class="emp-detail-stat-card">
                        <span class="emp-detail-stat-value">92%</span>
                        <span class="emp-detail-stat-label">Match Score</span>
                    </div>
                    <div class="emp-detail-stat-card">
                        <span class="emp-detail-stat-value">94%</span>
                        <span class="emp-detail-stat-label">Assessment Score</span>
                    </div>
                    <div class="emp-detail-stat-card">
                        <span class="emp-detail-stat-value">8 yrs</span>
                        <span class="emp-detail-stat-label">Experience</span>
                    </div>
                    <div class="emp-detail-stat-card">
                        <span class="emp-detail-stat-value">28 min</span>
                        <span class="emp-detail-stat-label">Assessment Time</span>
                    </div>
                </div>

                <!-- Resume / About -->
                <section class="emp-candidate-section">
                    <h3 class="emp-section-title">About</h3>
                    <p class="emp-candidate-bio">Seasoned product leader with 8+ years of experience driving innovation at scale. Led cross-functional teams of 30+ at top-tier tech companies, specializing in data-driven product strategy and go-to-market execution. Proven track record of launching products that generated $50M+ in annual revenue.</p>
                </section>

                <!-- Experience -->
                <section class="emp-candidate-section">
                    <h3 class="emp-section-title">Experience</h3>
                    <div class="emp-timeline">
                        <div class="emp-timeline-item">
                            <div class="emp-timeline-dot"></div>
                            <div class="emp-timeline-content">
                                <h4 class="emp-timeline-title">Director of Product</h4>
                                <p class="emp-timeline-company">TechCorp International</p>
                                <p class="emp-timeline-period">2021 – Present • 4 years</p>
                                <p class="emp-timeline-desc">Led product strategy for a suite of enterprise SaaS tools. Managed a team of 15 PMs and designers. Increased user adoption by 40% through data-driven feature prioritization.</p>
                            </div>
                        </div>
                        <div class="emp-timeline-item">
                            <div class="emp-timeline-dot"></div>
                            <div class="emp-timeline-content">
                                <h4 class="emp-timeline-title">Senior Product Manager</h4>
                                <p class="emp-timeline-company">InnovateLabs</p>
                                <p class="emp-timeline-period">2018 – 2021 • 3 years</p>
                                <p class="emp-timeline-desc">Owned the core platform roadmap. Launched 5 major features that contributed to a 60% increase in recurring revenue.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Education -->
                <section class="emp-candidate-section">
                    <h3 class="emp-section-title">Education</h3>
                    <div class="emp-timeline">
                        <div class="emp-timeline-item">
                            <div class="emp-timeline-dot"></div>
                            <div class="emp-timeline-content">
                                <h4 class="emp-timeline-title">MBA, Technology Management</h4>
                                <p class="emp-timeline-company">Stanford University</p>
                                <p class="emp-timeline-period">2016 – 2018</p>
                            </div>
                        </div>
                        <div class="emp-timeline-item">
                            <div class="emp-timeline-dot"></div>
                            <div class="emp-timeline-content">
                                <h4 class="emp-timeline-title">BSc Computer Science</h4>
                                <p class="emp-timeline-company">Addis Ababa University</p>
                                <p class="emp-timeline-period">2012 – 2016</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Assessment Breakdown -->
                <section class="emp-candidate-section">
                    <h3 class="emp-section-title">Assessment Breakdown</h3>
                    <div class="emp-assess-breakdown">
                        <div class="emp-assess-bk-item">
                            <div class="emp-assess-bk-top">
                                <span>Product Strategy</span>
                                <span class="emp-score emp-score--high">96%</span>
                            </div>
                            <div class="emp-progress-bar"><div class="emp-progress-fill" style="width: 96%;"></div></div>
                        </div>
                        <div class="emp-assess-bk-item">
                            <div class="emp-assess-bk-top">
                                <span>Leadership & Communication</span>
                                <span class="emp-score emp-score--high">92%</span>
                            </div>
                            <div class="emp-progress-bar"><div class="emp-progress-fill" style="width: 92%;"></div></div>
                        </div>
                        <div class="emp-assess-bk-item">
                            <div class="emp-assess-bk-top">
                                <span>Data Analytics</span>
                                <span class="emp-score emp-score--high">88%</span>
                            </div>
                            <div class="emp-progress-bar"><div class="emp-progress-fill" style="width: 88%;"></div></div>
                        </div>
                        <div class="emp-assess-bk-item">
                            <div class="emp-assess-bk-top">
                                <span>Technical Aptitude</span>
                                <span class="emp-score emp-score--mid">78%</span>
                            </div>
                            <div class="emp-progress-bar"><div class="emp-progress-fill" style="width: 78%;"></div></div>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Sidebar -->
            <div class="emp-detail-sidebar">
                <section class="emp-detail-panel">
                    <h4 class="emp-section-title emp-section-title--sm">Contact</h4>
                    <div class="emp-detail-info">
                        <div class="emp-detail-row"><span>Email</span><span>sarah.m@email.com</span></div>
                        <div class="emp-detail-row"><span>Phone</span><span>+251 911 234 567</span></div>
                        <div class="emp-detail-row"><span>Location</span><span>Addis Ababa</span></div>
                        <div class="emp-detail-row"><span>LinkedIn</span><span>linkedin.com/in/sarahm</span></div>
                    </div>
                </section>

                <section class="emp-detail-panel">
                    <h4 class="emp-section-title emp-section-title--sm">Skills</h4>
                    <div class="emp-cand-skills">
                        <span class="emp-cand-skill-tag">Product Strategy</span>
                        <span class="emp-cand-skill-tag">Leadership</span>
                        <span class="emp-cand-skill-tag">Agile</span>
                        <span class="emp-cand-skill-tag">Data Analytics</span>
                        <span class="emp-cand-skill-tag">Roadmapping</span>
                        <span class="emp-cand-skill-tag">User Research</span>
                        <span class="emp-cand-skill-tag">Figma</span>
                    </div>
                </section>

                <section class="emp-detail-panel">
                    <h4 class="emp-section-title emp-section-title--sm">Pipeline Stage</h4>
                    <div class="emp-pipeline-tracker">
                        <div class="emp-pipeline-step emp-pipeline-step--done"><span class="material-symbols-outlined">check_circle</span> Applied</div>
                        <div class="emp-pipeline-step emp-pipeline-step--done"><span class="material-symbols-outlined">check_circle</span> Screened</div>
                        <div class="emp-pipeline-step emp-pipeline-step--current"><span class="material-symbols-outlined">radio_button_checked</span> Interview</div>
                        <div class="emp-pipeline-step"><span class="material-symbols-outlined">radio_button_unchecked</span> Offer</div>
                    </div>
                </section>

                <section class="emp-detail-panel">
                    <h4 class="emp-section-title emp-section-title--sm">Actions</h4>
                    <a href="interview-schedule.php" class="emp-quick-btn"><span class="material-symbols-outlined">calendar_month</span> Schedule Interview</a>
                    <a href="interview-feedback.php" class="emp-quick-btn"><span class="material-symbols-outlined">rate_review</span> Add Feedback</a>
                    <a href="#" class="emp-quick-btn"><span class="material-symbols-outlined">download</span> Download Resume</a>
                </section>
            </div>
        </div>
    </main>
</body>
</html>
