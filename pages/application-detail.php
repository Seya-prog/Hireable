<!DOCTYPE html>
<html lang="en">
<head>
    <?php $pageTitle = 'Application Detail | Hireable'; ?>
    <?php include __DIR__ . '/../components/head.php'; ?>
</head>
<body class="dash-page">
    <?php $activePage = 'applications'; ?>
    <?php include __DIR__ . '/../components/sidebar.php'; ?>

    <main class="dash-main" style="margin-left: 260px;">
        <!-- Back Link -->
        <a href="applications.php" class="emp-back-link" style="margin-bottom: 1.5rem; display: inline-flex;">
            <span class="material-symbols-outlined">arrow_back</span>
            Back to Applications
        </a>

        <div class="app-detail-layout">
            <!-- Main Content -->
            <div class="app-detail-main">
                <!-- Company Header -->
                <div class="app-detail-header">
                    <div class="app-detail-company">
                        <div class="app-detail-logo">
                            <span class="material-symbols-outlined" style="font-size: 2rem; color: #695d46;">apartment</span>
                        </div>
                        <div>
                            <h2 class="page-title" style="font-size: 1.75rem;">VP of Product Innovation</h2>
                            <p class="page-subtitle">Lumina Global • Addis Ababa • Full-time</p>
                        </div>
                    </div>
                    <span class="app-badge app-badge--interview">Interviewing</span>
                </div>

                <!-- Timeline -->
                <section class="app-detail-section">
                    <h3 class="app-detail-section-title">Application Timeline</h3>
                    <div class="emp-timeline">
                        <div class="emp-timeline-item">
                            <div class="emp-timeline-dot"></div>
                            <div class="emp-timeline-content">
                                <h4 class="emp-timeline-title">Application Submitted</h4>
                                <p class="emp-timeline-period">Oct 12, 2023</p>
                                <p class="emp-timeline-desc">Your application and resume were submitted successfully.</p>
                            </div>
                        </div>
                        <div class="emp-timeline-item">
                            <div class="emp-timeline-dot"></div>
                            <div class="emp-timeline-content">
                                <h4 class="emp-timeline-title">Application Reviewed</h4>
                                <p class="emp-timeline-period">Oct 14, 2023</p>
                                <p class="emp-timeline-desc">Your application was reviewed by the hiring team. You were shortlisted for the next round.</p>
                            </div>
                        </div>
                        <div class="emp-timeline-item">
                            <div class="emp-timeline-dot"></div>
                            <div class="emp-timeline-content">
                                <h4 class="emp-timeline-title">Assessment Completed</h4>
                                <p class="emp-timeline-period">Oct 18, 2023</p>
                                <p class="emp-timeline-desc">You scored 92% on the Product Strategy Case Study assessment.</p>
                            </div>
                        </div>
                        <div class="emp-timeline-item">
                            <div class="emp-timeline-dot" style="background: #155724;"></div>
                            <div class="emp-timeline-content">
                                <h4 class="emp-timeline-title" style="color: #155724;">Round 1 Interview — Completed</h4>
                                <p class="emp-timeline-period">Oct 20, 2023 • Video Call</p>
                                <p class="emp-timeline-desc">Behavioral interview with Sarah K., Head of People. Feedback was positive.</p>
                            </div>
                        </div>
                        <div class="emp-timeline-item">
                            <div class="emp-timeline-dot" style="background: #856404;"></div>
                            <div class="emp-timeline-content">
                                <h4 class="emp-timeline-title" style="color: #856404;">Round 2 Interview — Upcoming</h4>
                                <p class="emp-timeline-period">Oct 25, 2023 • 2:00 PM • Zoom</p>
                                <p class="emp-timeline-desc">Panel interview: Technical & Leadership with the VP of Engineering and CTO.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Job Description -->
                <section class="app-detail-section">
                    <h3 class="app-detail-section-title">Job Description</h3>
                    <div class="app-detail-description">
                        <p>We're looking for a visionary product leader to drive our next generation of enterprise solutions. You will own the product roadmap, lead cross-functional teams, and report directly to the CEO.</p>
                        <h4>Key Responsibilities</h4>
                        <ul>
                            <li>Define and execute the product vision and strategy for enterprise solutions</li>
                            <li>Lead a team of 15+ product managers and designers</li>
                            <li>Partner with engineering to deliver high-quality features on schedule</li>
                            <li>Drive data-informed decision making across the organization</li>
                            <li>Present product strategy to the board and key stakeholders</li>
                        </ul>
                        <h4>Requirements</h4>
                        <ul>
                            <li>8+ years of product management experience in B2B SaaS</li>
                            <li>Proven track record of launching products at scale</li>
                            <li>Strong leadership and communication skills</li>
                            <li>MBA or equivalent experience preferred</li>
                        </ul>
                    </div>
                </section>

                <!-- Notes -->
                <section class="app-detail-section">
                    <h3 class="app-detail-section-title">My Notes</h3>
                    <textarea class="assess-textarea" rows="4" placeholder="Add personal notes about this application...">Great culture fit. Researched their recent Series C funding — $45M raised. Team seems strong, CTO has experience at Google. Prepare case study for Round 2.</textarea>
                </section>
            </div>

            <!-- Sidebar -->
            <div class="app-detail-sidebar">
                <section class="emp-detail-panel">
                    <h4 class="emp-section-title emp-section-title--sm">Status</h4>
                    <div class="emp-detail-info">
                        <div class="emp-detail-row"><span>Stage</span><span>Interviewing</span></div>
                        <div class="emp-detail-row"><span>Applied On</span><span>Oct 12, 2023</span></div>
                        <div class="emp-detail-row"><span>Next Step</span><span>Panel Oct 25</span></div>
                        <div class="emp-detail-row"><span>Response Time</span><span>2 days</span></div>
                    </div>
                </section>

                <section class="emp-detail-panel">
                    <h4 class="emp-section-title emp-section-title--sm">Compensation</h4>
                    <div class="emp-detail-info">
                        <div class="emp-detail-row"><span>Salary</span><span>$140k – $180k</span></div>
                        <div class="emp-detail-row"><span>Equity</span><span>0.1% – 0.3%</span></div>
                        <div class="emp-detail-row"><span>Benefits</span><span>Full Package</span></div>
                    </div>
                </section>

                <section class="emp-detail-panel">
                    <h4 class="emp-section-title emp-section-title--sm">Assessment</h4>
                    <div class="app-detail-assess-card">
                        <div class="app-detail-assess-score">92%</div>
                        <div>
                            <p class="app-detail-assess-title">Product Strategy Case Study</p>
                            <p class="app-detail-assess-meta">Completed Oct 18 • 35 min</p>
                        </div>
                    </div>
                    <a href="assessment-result-employee.php" class="emp-quick-btn" style="margin-top: 0.75rem;">
                        <span class="material-symbols-outlined">visibility</span>
                        View Full Results
                    </a>
                </section>

                <section class="emp-detail-panel">
                    <h4 class="emp-section-title emp-section-title--sm">Quick Actions</h4>
                    <a href="#" class="emp-quick-btn"><span class="material-symbols-outlined">download</span> Download Resume Sent</a>
                    <a href="#" class="emp-quick-btn"><span class="material-symbols-outlined">mail</span> Contact Recruiter</a>
                    <button class="emp-quick-btn" style="color: #93000a; width: 100%; text-align: left; cursor: pointer; border: 1px solid rgba(208,197,187,0.3); background: #fff;">
                        <span class="material-symbols-outlined">close</span> Withdraw Application
                    </button>
                </section>
            </div>
        </div>
    </main>
</body>
</html>
