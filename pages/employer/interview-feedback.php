<?php
require_once __DIR__ . '/../../backend/helpers/session.php';
require_once __DIR__ . '/../../backend/helpers/csrf.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/app.php';

if (!isLoggedIn() || getCurrentUserRole() !== 'employer') {
    header('Location: ' . AUTH_URL . 'login.php'); exit;
}

$employerId = getCurrentUserId();
$interviewId = intval($_GET['id'] ?? 0);

// Load interview details
$stmt = $pdo->prepare(
    'SELECT i.*, u.first_name, u.last_name, j.title AS job_title
     FROM interviews i
     JOIN users u ON i.employee_id = u.id
     JOIN applications a ON i.application_id = a.id
     JOIN jobs j ON a.job_id = j.id
     WHERE i.id = ? AND i.employer_id = ?'
);
$stmt->execute([$interviewId, $employerId]);
$interview = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$interview) {
    header('Location: ' . EMPLOYER_URL . 'interviews.php'); exit;
}

$candidateName = htmlspecialchars($interview['first_name'] . ' ' . $interview['last_name']);
$jobTitle = htmlspecialchars($interview['job_title']);
$interviewDate = date('M j, Y', strtotime($interview['scheduled_date']));

// Check if feedback already exists
$stmt = $pdo->prepare('SELECT * FROM interview_feedback WHERE interview_id = ? AND reviewer_id = ?');
$stmt->execute([$interviewId, $employerId]);
$existing = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php $pageTitle = 'Interview Feedback | Hireable Employer'; ?>
    <?php $pageCss = ['employer.css', 'toast.css'];
    include __DIR__ . '/../../components/shared/head.php'; ?>
</head>
<body class="dash-page">
    <?php include __DIR__ . '/../../components/shared/toast.php'; ?>
    <?php $activePage = 'interviews'; ?>
    <?php include __DIR__ . '/../../components/employer/employer-sidebar.php'; ?>

    <main class="dash-main" style="margin-left: 260px;">
        <div class="emp-header">
            <div>
                <a href="../employer/interviews.php" class="emp-back-link">
                    <span class="material-symbols-outlined">arrow_back</span> Back to Interviews
                </a>
                <h2 class="page-title">Interview Feedback</h2>
                <p class="page-subtitle"><?= $candidateName ?> • <?= $jobTitle ?> • Interviewed <?= $interviewDate ?></p>
            </div>
        </div>

        <form class="emp-form" style="max-width: 720px;" method="POST" action="/action/employer.interviews.feedback">
            <?= csrfField() ?>
            <input type="hidden" name="interview_id" value="<?= $interviewId ?>">

            <!-- Overall Rating -->
            <section class="emp-form-section">
                <h3 class="emp-form-section-title">Overall Rating</h3>
                <div class="emp-rating-stars" id="overall-stars">
                    <?php for ($i = 1; $i <= 5; $i++): 
                        $filled = $existing && $i <= ($existing['overall_rating'] ?? 0);
                    ?>
                    <button type="button" class="emp-star <?= $filled ? 'emp-star--filled' : '' ?>" data-val="<?= $i ?>" onclick="setRating('overall', <?= $i ?>)">
                        <span class="material-symbols-outlined">star</span>
                    </button>
                    <?php endfor; ?>
                </div>
                <input type="hidden" name="overall_rating" id="overall-rating-val" value="<?= $existing['overall_rating'] ?? 0 ?>">
                <p class="emp-rating-label" id="overall-label"><?= $existing ? ($existing['overall_rating'] . ' out of 5') : 'Select a rating' ?></p>
            </section>

            <!-- Category Ratings -->
            <section class="emp-form-section">
                <h3 class="emp-form-section-title">Competency Ratings</h3>
                <div class="emp-feedback-categories">
                    <?php
                    $categories = [
                        'technical_rating' => 'Technical Skills',
                        'communication_rating' => 'Communication',
                        'problem_solving_rating' => 'Problem Solving',
                        'culture_fit_rating' => 'Culture Fit',
                    ];
                    foreach ($categories as $field => $label):
                        $val = $existing[$field] ?? 3;
                    ?>
                    <div class="emp-feedback-cat">
                        <span class="emp-feedback-cat-label"><?= $label ?></span>
                        <div class="emp-feedback-slider-wrap">
                            <input type="range" min="1" max="5" value="<?= $val ?>" name="<?= $field ?>" class="emp-feedback-slider" oninput="this.nextElementSibling.textContent=this.value+'/5'">
                            <span class="emp-feedback-slider-val"><?= $val ?>/5</span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- Written Feedback -->
            <section class="emp-form-section">
                <h3 class="emp-form-section-title">Written Feedback</h3>
                <div class="assess-field">
                    <label class="assess-label">Strengths</label>
                    <textarea class="assess-textarea" rows="3" name="strengths" placeholder="What stood out positively about this candidate?"><?= htmlspecialchars($existing['strengths'] ?? '') ?></textarea>
                </div>
                <div class="assess-field" style="margin-top: 1.25rem;">
                    <label class="assess-label">Areas for Improvement</label>
                    <textarea class="assess-textarea" rows="3" name="improvements" placeholder="Any concerns or areas where the candidate could improve?"><?= htmlspecialchars($existing['improvements'] ?? '') ?></textarea>
                </div>
                <div class="assess-field" style="margin-top: 1.25rem;">
                    <label class="assess-label">Additional Notes</label>
                    <textarea class="assess-textarea" rows="3" name="additional_notes" placeholder="Any other observations or comments..."><?= htmlspecialchars($existing['additional_notes'] ?? '') ?></textarea>
                </div>
            </section>

            <!-- Recommendation -->
            <section class="emp-form-section">
                <h3 class="emp-form-section-title">Recommendation</h3>
                <?php $rec = $existing['recommendation'] ?? ''; ?>
                <div class="emp-recommendation-options">
                    <?php
                    $options = ['strong_yes'=>['thumb_up','Strong Yes'], 'yes'=>['check','Yes'], 'maybe'=>['help','Maybe'], 'no'=>['close','No']];
                    foreach ($options as $val => [$icon, $label]):
                    ?>
                    <label class="emp-rec-option">
                        <input type="radio" name="recommendation" value="<?= $val ?>" <?= $rec === $val ? 'checked' : '' ?>>
                        <div class="emp-rec-card emp-rec--<?= str_replace('_', '-', $val) ?>">
                            <span class="material-symbols-outlined"><?= $icon ?></span>
                            <span><?= $label ?></span>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
            </section>

            <div class="emp-form-actions">
                <a href="../employer/interviews.php" class="assess-save-btn assess-save-btn--draft" style="text-decoration:none;">Cancel</a>
                <button type="submit" class="assess-save-btn assess-save-btn--publish"><?= $existing ? 'Update Feedback' : 'Submit Feedback' ?></button>
            </div>
        </form>
    </main>

    <script>
    function setRating(type, val) {
        document.getElementById(type + '-rating-val').value = val;
        const labels = {1:'1 — Not Recommended',2:'2 — Below Average',3:'3 — Average',4:'4 — Strong Candidate',5:'5 — Exceptional'};
        document.getElementById(type + '-label').textContent = labels[val] || val + ' out of 5';
        document.querySelectorAll('#' + type + '-stars .emp-star').forEach((s,i) => {
            s.classList.toggle('emp-star--filled', i < val);
        });
    }
    </script>
</body>
</html>
