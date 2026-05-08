<!DOCTYPE html>
<html lang="en">
<head>
    <?php $pageTitle = 'Assessment Results | Hireable Employer'; ?>
    <?php include __DIR__ . '/../components/head.php'; ?>
</head>
<body class="dash-page">
    <?php $activePage = 'skill-assessments'; ?>
    <?php include __DIR__ . '/../components/employer-sidebar.php'; ?>

    <main class="dash-main" style="margin-left: 260px;">
        <div class="emp-header">
            <div>
                <a href="skill-assesment.php?tab=my-assessments" class="emp-back-link">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Back to Assessments
                </a>
                <h2 class="page-title">React Frontend Assessment</h2>
                <p class="page-subtitle">Linked to: Senior Software Engineer • 15 Questions • Advanced</p>
            </div>
            <div class="emp-header-actions">
                <a href="assessment-edit.php" class="assess-save-btn assess-save-btn--draft" style="text-decoration:none;">Edit Assessment</a>
                <span class="emp-status-badge emp-status--active">Active</span>
            </div>
        </div>

        <!-- Summary Stats -->
        <div class="assess-results-summary">
            <div class="assess-result-stat">
                <span class="assess-result-stat-value">12</span>
                <span class="assess-result-stat-label">Completed</span>
            </div>
            <div class="assess-result-stat">
                <span class="assess-result-stat-value">78%</span>
                <span class="assess-result-stat-label">Average Score</span>
            </div>
            <div class="assess-result-stat">
                <span class="assess-result-stat-value">83%</span>
                <span class="assess-result-stat-label">Pass Rate</span>
            </div>
            <div class="assess-result-stat">
                <span class="assess-result-stat-value">34m</span>
                <span class="assess-result-stat-label">Avg. Time</span>
            </div>
        </div>

        <!-- Question Performance -->
        <section style="margin-bottom: 2.5rem;">
            <h3 class="emp-section-title" style="margin-bottom: 1.5rem;">Question Performance</h3>
            <div class="emp-question-perf">
                <div class="emp-qp-item">
                    <div class="emp-qp-top">
                        <span class="emp-qp-num">Q1</span>
                        <span class="emp-qp-text">What is the virtual DOM in React?</span>
                        <span class="emp-score emp-score--high">92% correct</span>
                    </div>
                    <div class="emp-progress-bar"><div class="emp-progress-fill" style="width:92%;"></div></div>
                </div>
                <div class="emp-qp-item">
                    <div class="emp-qp-top">
                        <span class="emp-qp-num">Q2</span>
                        <span class="emp-qp-text">Write a custom hook that debounces a value</span>
                        <span class="emp-score emp-score--mid">67% correct</span>
                    </div>
                    <div class="emp-progress-bar"><div class="emp-progress-fill" style="width:67%;"></div></div>
                </div>
                <div class="emp-qp-item">
                    <div class="emp-qp-top">
                        <span class="emp-qp-num">Q3</span>
                        <span class="emp-qp-text">Explain the difference between useMemo and useCallback</span>
                        <span class="emp-score emp-score--high">85% correct</span>
                    </div>
                    <div class="emp-progress-bar"><div class="emp-progress-fill" style="width:85%;"></div></div>
                </div>
                <div class="emp-qp-item">
                    <div class="emp-qp-top">
                        <span class="emp-qp-num">Q4</span>
                        <span class="emp-qp-text">Implement a state management solution without libraries</span>
                        <span class="emp-score emp-score--low">42% correct</span>
                    </div>
                    <div class="emp-progress-bar"><div class="emp-progress-fill" style="width:42%;"></div></div>
                </div>
            </div>
        </section>

        <!-- Individual Results Table -->
        <section>
            <div class="emp-section-head">
                <h3 class="emp-section-title">Individual Results</h3>
            </div>
            <div class="emp-table">
                <div class="emp-table-header" style="grid-template-columns: 2fr 0.8fr 0.6fr 0.8fr 0.5fr;">
                    <span>Candidate</span>
                    <span>Score</span>
                    <span>Time</span>
                    <span>Status</span>
                    <span>Action</span>
                </div>
                <div class="emp-table-row" style="grid-template-columns: 2fr 0.8fr 0.6fr 0.8fr 0.5fr;">
                    <div class="emp-table-candidate">
                        <div class="emp-avatar">SM</div>
                        <div><p class="emp-candidate-name">Sarah M.</p><p class="emp-candidate-email">sarah.m@email.com</p></div>
                    </div>
                    <span class="emp-score emp-score--high">94%</span>
                    <span class="emp-table-cell">28 min</span>
                    <span class="emp-stage-badge emp-stage--offer">Passed</span>
                    <a href="candidate-detail.php" class="emp-action-btn"><span class="material-symbols-outlined">visibility</span></a>
                </div>
                <div class="emp-table-row" style="grid-template-columns: 2fr 0.8fr 0.6fr 0.8fr 0.5fr;">
                    <div class="emp-table-candidate">
                        <div class="emp-avatar">DK</div>
                        <div><p class="emp-candidate-name">Daniel K.</p><p class="emp-candidate-email">daniel.k@email.com</p></div>
                    </div>
                    <span class="emp-score emp-score--mid">74%</span>
                    <span class="emp-table-cell">42 min</span>
                    <span class="emp-stage-badge emp-stage--offer">Passed</span>
                    <a href="candidate-detail.php" class="emp-action-btn"><span class="material-symbols-outlined">visibility</span></a>
                </div>
                <div class="emp-table-row" style="grid-template-columns: 2fr 0.8fr 0.6fr 0.8fr 0.5fr;">
                    <div class="emp-table-candidate">
                        <div class="emp-avatar">TB</div>
                        <div><p class="emp-candidate-name">Tariku B.</p><p class="emp-candidate-email">tariku.b@email.com</p></div>
                    </div>
                    <span class="emp-score emp-score--low">48%</span>
                    <span class="emp-table-cell">45 min</span>
                    <span class="emp-stage-badge emp-stage--screening">Failed</span>
                    <a href="candidate-detail.php" class="emp-action-btn"><span class="material-symbols-outlined">visibility</span></a>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
