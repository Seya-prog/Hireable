<!DOCTYPE html>
<html lang="en">
<head>
    <?php $pageTitle = 'Assessment Result | Hireable'; ?>
    <?php include __DIR__ . '/../components/head.php'; ?>
</head>
<body class="dash-page">
    <?php $activePage = 'skill-assessments'; ?>
    <?php include __DIR__ . '/../components/sidebar.php'; ?>

    <main class="dash-main" style="margin-left: 260px;">
        <a href="skill-assesment.php?tab=completed" class="emp-back-link" style="margin-bottom: 1.5rem; display: inline-flex;">
            <span class="material-symbols-outlined">arrow_back</span>
            Back to Assessments
        </a>

        <!-- Result Header -->
        <div class="assess-emp-result-header">
            <div>
                <h2 class="page-title" style="font-size: 1.75rem;">Design Systems Ops</h2>
                <p class="page-subtitle">Requested by Lumina Creative • Completed Oct 05, 2023</p>
            </div>
            <div class="assess-emp-score-circle">
                <span class="assess-emp-score-value">94%</span>
                <span class="assess-emp-score-label">Score</span>
            </div>
        </div>

        <!-- Stats -->
        <div class="assess-results-summary" style="margin-bottom: 2.5rem;">
            <div class="assess-result-stat">
                <span class="assess-result-stat-value">94%</span>
                <span class="assess-result-stat-label">Overall Score</span>
            </div>
            <div class="assess-result-stat">
                <span class="assess-result-stat-value">18/20</span>
                <span class="assess-result-stat-label">Correct Answers</span>
            </div>
            <div class="assess-result-stat">
                <span class="assess-result-stat-value">28 min</span>
                <span class="assess-result-stat-label">Time Taken</span>
            </div>
            <div class="assess-result-stat">
                <span class="assess-result-stat-value" style="color: #155724;">Passed</span>
                <span class="assess-result-stat-label">Status</span>
            </div>
        </div>

        <div class="app-detail-layout">
            <div class="app-detail-main">
                <!-- Category Breakdown -->
                <section class="app-detail-section">
                    <h3 class="app-detail-section-title">Performance by Category</h3>
                    <div class="emp-assess-breakdown">
                        <div class="emp-assess-bk-item">
                            <div class="emp-assess-bk-top">
                                <span>Design System Architecture</span>
                                <span class="emp-score emp-score--high">100%</span>
                            </div>
                            <div class="emp-progress-bar"><div class="emp-progress-fill" style="width: 100%;"></div></div>
                        </div>
                        <div class="emp-assess-bk-item">
                            <div class="emp-assess-bk-top">
                                <span>Component Libraries</span>
                                <span class="emp-score emp-score--high">90%</span>
                            </div>
                            <div class="emp-progress-bar"><div class="emp-progress-fill" style="width: 90%;"></div></div>
                        </div>
                        <div class="emp-assess-bk-item">
                            <div class="emp-assess-bk-top">
                                <span>Design Tokens & Variables</span>
                                <span class="emp-score emp-score--high">95%</span>
                            </div>
                            <div class="emp-progress-bar"><div class="emp-progress-fill" style="width: 95%;"></div></div>
                        </div>
                        <div class="emp-assess-bk-item">
                            <div class="emp-assess-bk-top">
                                <span>Cross-platform Consistency</span>
                                <span class="emp-score emp-score--mid">80%</span>
                            </div>
                            <div class="emp-progress-bar"><div class="emp-progress-fill" style="width: 80%;"></div></div>
                        </div>
                    </div>
                </section>

                <!-- Question Review -->
                <section class="app-detail-section">
                    <h3 class="app-detail-section-title">Question Review</h3>
                    <div class="assess-emp-questions">
                        <div class="assess-emp-q-item assess-emp-q-item--correct">
                            <div class="assess-emp-q-top">
                                <span class="assess-emp-q-num">Q1</span>
                                <span class="material-symbols-outlined assess-emp-q-icon--correct">check_circle</span>
                            </div>
                            <p class="assess-emp-q-text">What is the primary benefit of using design tokens in a design system?</p>
                            <p class="assess-emp-q-answer"><strong>Your answer:</strong> Centralized management of design decisions across platforms</p>
                        </div>
                        <div class="assess-emp-q-item assess-emp-q-item--correct">
                            <div class="assess-emp-q-top">
                                <span class="assess-emp-q-num">Q2</span>
                                <span class="material-symbols-outlined assess-emp-q-icon--correct">check_circle</span>
                            </div>
                            <p class="assess-emp-q-text">Which approach is recommended for versioning a component library?</p>
                            <p class="assess-emp-q-answer"><strong>Your answer:</strong> Semantic versioning with changelog documentation</p>
                        </div>
                        <div class="assess-emp-q-item assess-emp-q-item--wrong">
                            <div class="assess-emp-q-top">
                                <span class="assess-emp-q-num">Q3</span>
                                <span class="material-symbols-outlined assess-emp-q-icon--wrong">cancel</span>
                            </div>
                            <p class="assess-emp-q-text">In atomic design, which level represents a group of atoms forming a functional unit?</p>
                            <p class="assess-emp-q-answer"><strong>Your answer:</strong> Organisms</p>
                            <p class="assess-emp-q-correct"><strong>Correct answer:</strong> Molecules</p>
                        </div>
                        <div class="assess-emp-q-item assess-emp-q-item--correct">
                            <div class="assess-emp-q-top">
                                <span class="assess-emp-q-num">Q4</span>
                                <span class="material-symbols-outlined assess-emp-q-icon--correct">check_circle</span>
                            </div>
                            <p class="assess-emp-q-text">What tool is most commonly used for managing design tokens in a CI/CD pipeline?</p>
                            <p class="assess-emp-q-answer"><strong>Your answer:</strong> Style Dictionary</p>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Sidebar -->
            <div class="app-detail-sidebar">
                <section class="emp-detail-panel">
                    <h4 class="emp-section-title emp-section-title--sm">Certificate</h4>
                    <div class="assess-emp-cert-preview">
                        <span class="material-symbols-outlined" style="font-size: 3rem; color: #695d46;">workspace_premium</span>
                        <p class="assess-emp-cert-text">Design Systems Ops</p>
                        <p class="assess-emp-cert-date">Certified Oct 05, 2023</p>
                    </div>
                    <a href="#" class="emp-quick-btn" style="margin-top: 0.75rem;">
                        <span class="material-symbols-outlined">download</span>
                        Download Certificate
                    </a>
                    <a href="#" class="emp-quick-btn">
                        <span class="material-symbols-outlined">share</span>
                        Share to LinkedIn
                    </a>
                </section>

                <section class="emp-detail-panel">
                    <h4 class="emp-section-title emp-section-title--sm">Assessment Info</h4>
                    <div class="emp-detail-info">
                        <div class="emp-detail-row"><span>Questions</span><span>20</span></div>
                        <div class="emp-detail-row"><span>Difficulty</span><span>Intermediate</span></div>
                        <div class="emp-detail-row"><span>Time Limit</span><span>45 min</span></div>
                        <div class="emp-detail-row"><span>Passing Score</span><span>70%</span></div>
                    </div>
                </section>
            </div>
        </div>
    </main>
</body>
</html>
