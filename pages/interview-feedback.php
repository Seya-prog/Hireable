<!DOCTYPE html>
<html lang="en">
<head>
    <?php $pageTitle = 'Interview Feedback | Hireable Employer'; ?>
    <?php include __DIR__ . '/../components/head.php'; ?>
</head>
<body class="dash-page">
    <?php $activePage = 'interviews'; ?>
    <?php include __DIR__ . '/../components/employer-sidebar.php'; ?>

    <main class="dash-main" style="margin-left: 260px;">
        <div class="emp-header">
            <div>
                <a href="interviews.php" class="emp-back-link">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Back to Interviews
                </a>
                <h2 class="page-title">Interview Feedback</h2>
                <p class="page-subtitle">Kebede M. • UX Designer • Interviewed May 5, 2026</p>
            </div>
        </div>

        <form class="emp-form" style="max-width: 720px;">
            <!-- Overall Rating -->
            <section class="emp-form-section">
                <h3 class="emp-form-section-title">Overall Rating</h3>
                <div class="emp-rating-stars">
                    <button type="button" class="emp-star emp-star--filled"><span class="material-symbols-outlined">star</span></button>
                    <button type="button" class="emp-star emp-star--filled"><span class="material-symbols-outlined">star</span></button>
                    <button type="button" class="emp-star emp-star--filled"><span class="material-symbols-outlined">star</span></button>
                    <button type="button" class="emp-star emp-star--filled"><span class="material-symbols-outlined">star</span></button>
                    <button type="button" class="emp-star"><span class="material-symbols-outlined">star</span></button>
                </div>
                <p class="emp-rating-label">4 out of 5 — Strong Candidate</p>
            </section>

            <!-- Category Ratings -->
            <section class="emp-form-section">
                <h3 class="emp-form-section-title">Competency Ratings</h3>
                <div class="emp-feedback-categories">
                    <div class="emp-feedback-cat">
                        <span class="emp-feedback-cat-label">Technical Skills</span>
                        <div class="emp-feedback-slider-wrap">
                            <input type="range" min="1" max="5" value="4" class="emp-feedback-slider">
                            <span class="emp-feedback-slider-val">4/5</span>
                        </div>
                    </div>
                    <div class="emp-feedback-cat">
                        <span class="emp-feedback-cat-label">Communication</span>
                        <div class="emp-feedback-slider-wrap">
                            <input type="range" min="1" max="5" value="5" class="emp-feedback-slider">
                            <span class="emp-feedback-slider-val">5/5</span>
                        </div>
                    </div>
                    <div class="emp-feedback-cat">
                        <span class="emp-feedback-cat-label">Problem Solving</span>
                        <div class="emp-feedback-slider-wrap">
                            <input type="range" min="1" max="5" value="4" class="emp-feedback-slider">
                            <span class="emp-feedback-slider-val">4/5</span>
                        </div>
                    </div>
                    <div class="emp-feedback-cat">
                        <span class="emp-feedback-cat-label">Culture Fit</span>
                        <div class="emp-feedback-slider-wrap">
                            <input type="range" min="1" max="5" value="3" class="emp-feedback-slider">
                            <span class="emp-feedback-slider-val">3/5</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Written Feedback -->
            <section class="emp-form-section">
                <h3 class="emp-form-section-title">Written Feedback</h3>
                <div class="assess-field">
                    <label class="assess-label">Strengths</label>
                    <textarea class="assess-textarea" rows="3" placeholder="What stood out positively about this candidate?"></textarea>
                </div>
                <div class="assess-field" style="margin-top: 1.25rem;">
                    <label class="assess-label">Areas for Improvement</label>
                    <textarea class="assess-textarea" rows="3" placeholder="Any concerns or areas where the candidate could improve?"></textarea>
                </div>
                <div class="assess-field" style="margin-top: 1.25rem;">
                    <label class="assess-label">Additional Notes</label>
                    <textarea class="assess-textarea" rows="3" placeholder="Any other observations or comments..."></textarea>
                </div>
            </section>

            <!-- Recommendation -->
            <section class="emp-form-section">
                <h3 class="emp-form-section-title">Recommendation</h3>
                <div class="emp-recommendation-options">
                    <label class="emp-rec-option">
                        <input type="radio" name="recommendation" value="strong-yes">
                        <div class="emp-rec-card emp-rec--strong-yes">
                            <span class="material-symbols-outlined">thumb_up</span>
                            <span>Strong Yes</span>
                        </div>
                    </label>
                    <label class="emp-rec-option">
                        <input type="radio" name="recommendation" value="yes" checked>
                        <div class="emp-rec-card emp-rec--yes">
                            <span class="material-symbols-outlined">check</span>
                            <span>Yes</span>
                        </div>
                    </label>
                    <label class="emp-rec-option">
                        <input type="radio" name="recommendation" value="maybe">
                        <div class="emp-rec-card emp-rec--maybe">
                            <span class="material-symbols-outlined">help</span>
                            <span>Maybe</span>
                        </div>
                    </label>
                    <label class="emp-rec-option">
                        <input type="radio" name="recommendation" value="no">
                        <div class="emp-rec-card emp-rec--no">
                            <span class="material-symbols-outlined">close</span>
                            <span>No</span>
                        </div>
                    </label>
                </div>
            </section>

            <div class="emp-form-actions">
                <a href="interviews.php" class="assess-save-btn assess-save-btn--draft" style="text-decoration:none;">Cancel</a>
                <button type="button" class="assess-save-btn assess-save-btn--publish">Submit Feedback</button>
            </div>
        </form>
    </main>
</body>
</html>
