<!DOCTYPE html>
<html lang="en">
<head>
    <?php $pageTitle = 'Take Assessment | Hireable'; ?>
    <?php include __DIR__ . '/../components/head.php'; ?>
</head>
<body class="dash-page">
    <?php $activePage = 'skill-assessments'; ?>
    <?php include __DIR__ . '/../components/sidebar.php'; ?>

    <main class="dash-main" style="margin-left: 260px;">
        <!-- Assessment Header -->
        <div class="take-assess-header">
            <div class="take-assess-info">
                <a href="skill-assesment.php?tab=available" class="emp-back-link">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Exit Assessment
                </a>
                <h2 class="page-title" style="font-size: 1.5rem; margin-top: 0.5rem;">React Senior Developer Test</h2>
                <p class="page-subtitle">Requested by Acme Studio • Expert Level</p>
            </div>
            <div class="take-assess-timer">
                <span class="material-symbols-outlined">timer</span>
                <span class="take-assess-timer-text">38:24</span>
                <span class="take-assess-timer-label">Remaining</span>
            </div>
        </div>

        <!-- Progress -->
        <div class="take-assess-progress">
            <div class="take-assess-progress-info">
                <span>Question 3 of 15</span>
                <span>20% Complete</span>
            </div>
            <div class="emp-progress-bar" style="height: 8px;">
                <div class="emp-progress-fill" style="width: 20%; background: #155724;"></div>
            </div>
        </div>

        <!-- Question Area -->
        <div class="take-assess-content">
            <div class="take-assess-question-area">
                <!-- Question Card -->
                <div class="take-assess-question">
                    <div class="take-assess-q-header">
                        <span class="take-assess-q-num">Question 3</span>
                        <span class="assess-q-type-badge">Multiple Choice</span>
                        <span class="take-assess-q-points">10 Points</span>
                    </div>
                    <h3 class="take-assess-q-text">What is the primary purpose of the useCallback hook in React?</h3>
                    
                    <div class="take-assess-options">
                        <label class="take-assess-option">
                            <input type="radio" name="answer" value="a">
                            <div class="take-assess-option-content">
                                <span class="take-assess-option-letter">A</span>
                                <span>To memoize a computed value and prevent unnecessary recalculations</span>
                            </div>
                        </label>
                        <label class="take-assess-option">
                            <input type="radio" name="answer" value="b">
                            <div class="take-assess-option-content">
                                <span class="take-assess-option-letter">B</span>
                                <span>To memoize a callback function and prevent unnecessary re-renders of child components</span>
                            </div>
                        </label>
                        <label class="take-assess-option">
                            <input type="radio" name="answer" value="c">
                            <div class="take-assess-option-content">
                                <span class="take-assess-option-letter">C</span>
                                <span>To create side effects that run after every render</span>
                            </div>
                        </label>
                        <label class="take-assess-option">
                            <input type="radio" name="answer" value="d">
                            <div class="take-assess-option-content">
                                <span class="take-assess-option-letter">D</span>
                                <span>To manage component state with a reducer pattern</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="take-assess-nav">
                    <button class="take-assess-nav-btn take-assess-nav-btn--prev">
                        <span class="material-symbols-outlined">arrow_back</span>
                        Previous
                    </button>
                    <div class="take-assess-q-dots">
                        <span class="take-assess-dot take-assess-dot--done"></span>
                        <span class="take-assess-dot take-assess-dot--done"></span>
                        <span class="take-assess-dot take-assess-dot--current"></span>
                        <span class="take-assess-dot"></span>
                        <span class="take-assess-dot"></span>
                        <span class="take-assess-dot"></span>
                        <span class="take-assess-dot"></span>
                        <span class="take-assess-dot"></span>
                        <span class="take-assess-dot"></span>
                        <span class="take-assess-dot"></span>
                        <span class="take-assess-dot"></span>
                        <span class="take-assess-dot"></span>
                        <span class="take-assess-dot"></span>
                        <span class="take-assess-dot"></span>
                        <span class="take-assess-dot"></span>
                    </div>
                    <button class="take-assess-nav-btn take-assess-nav-btn--next">
                        Next
                        <span class="material-symbols-outlined">arrow_forward</span>
                    </button>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="take-assess-sidebar">
                <div class="take-assess-overview">
                    <h4 class="emp-section-title emp-section-title--sm">Question Overview</h4>
                    <div class="take-assess-q-grid">
                        <span class="take-assess-q-box take-assess-q-box--done">1</span>
                        <span class="take-assess-q-box take-assess-q-box--done">2</span>
                        <span class="take-assess-q-box take-assess-q-box--current">3</span>
                        <span class="take-assess-q-box">4</span>
                        <span class="take-assess-q-box">5</span>
                        <span class="take-assess-q-box">6</span>
                        <span class="take-assess-q-box">7</span>
                        <span class="take-assess-q-box">8</span>
                        <span class="take-assess-q-box">9</span>
                        <span class="take-assess-q-box">10</span>
                        <span class="take-assess-q-box">11</span>
                        <span class="take-assess-q-box">12</span>
                        <span class="take-assess-q-box">13</span>
                        <span class="take-assess-q-box">14</span>
                        <span class="take-assess-q-box">15</span>
                    </div>
                    <div class="take-assess-legend">
                        <span><span class="take-assess-dot take-assess-dot--done"></span> Answered</span>
                        <span><span class="take-assess-dot take-assess-dot--current"></span> Current</span>
                        <span><span class="take-assess-dot"></span> Unanswered</span>
                    </div>
                </div>
                <button class="take-assess-submit-btn">
                    <span class="material-symbols-outlined">check_circle</span>
                    Submit Assessment
                </button>
            </div>
        </div>
    </main>
</body>
</html>
