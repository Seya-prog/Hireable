<?php
require_once __DIR__ . '/../../backend/helpers/session.php';
require_once __DIR__ . '/../../backend/helpers/csrf.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../database/repositories/ApplicationRepository.php';
require_once __DIR__ . '/../../database/repositories/JobRepository.php';

if (!isLoggedIn() || getCurrentUserRole() !== 'employer') {
    header('Location: ' . AUTH_URL . 'login.php'); exit;
}

$employerId = getCurrentUserId();
$appRepo = new ApplicationRepository($pdo);
$jobRepo = new JobRepository($pdo);

// Get all applications with candidates for this employer
$applications = $appRepo->findDetailedByEmployer($employerId);
$jobs = $jobRepo->findByEmployer($employerId);

// Pre-select from query params
$preAppId = intval($_GET['app_id'] ?? 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php $pageTitle = 'Schedule Interview | Hireable Employer'; ?>
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
                <h2 class="page-title">Schedule Interview</h2>
                <p class="page-subtitle">Set up a new interview with a candidate.</p>
            </div>
        </div>

        <form class="emp-form" style="max-width: 720px;" method="POST" action="/action/employer.interviews.create">
            <?= csrfField() ?>
            <section class="emp-form-section">
                <h3 class="emp-form-section-title">Candidate & Position</h3>
                <div class="emp-form-grid">
                    <div class="assess-field">
                        <label class="assess-label">Candidate (Application)</label>
                        <select class="assess-input assess-select" name="application_id" required>
                            <option value="">Select candidate...</option>
                            <?php foreach ($applications as $app):
                                $name = htmlspecialchars($app['first_name'] . ' ' . $app['last_name']);
                                $jobTitle = htmlspecialchars($app['job_title'] ?? '');
                                $selected = ($app['id'] == $preAppId) ? 'selected' : '';
                            ?>
                            <option value="<?= $app['id'] ?>" data-employee="<?= $app['employee_id'] ?>" <?= $selected ?>><?= $name ?> — <?= $jobTitle ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </section>

            <section class="emp-form-section">
                <h3 class="emp-form-section-title">Date & Time</h3>
                <div class="emp-form-grid">
                    <div class="assess-field">
                        <label class="assess-label">Date</label>
                        <input class="assess-input" type="date" name="scheduled_date" value="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
                    </div>
                    <div class="assess-field">
                        <label class="assess-label">Start Time</label>
                        <input class="assess-input" type="time" name="start_time" value="14:00" required>
                    </div>
                    <div class="assess-field">
                        <label class="assess-label">Duration</label>
                        <select class="assess-input assess-select" name="duration_minutes">
                            <option value="30">30 minutes</option>
                            <option value="45">45 minutes</option>
                            <option value="60" selected>60 minutes</option>
                            <option value="90">90 minutes</option>
                        </select>
                    </div>
                    <div class="assess-field">
                        <label class="assess-label">Interview Type</label>
                        <select class="assess-input assess-select" name="interview_type">
                            <option value="phone">Phone Screen</option>
                            <option value="video_zoom" selected>Video Call (Zoom)</option>
                            <option value="video_meet">Video Call (Google Meet)</option>
                            <option value="in_person">In-Person</option>
                        </select>
                    </div>
                </div>
            </section>

            <section class="emp-form-section">
                <h3 class="emp-form-section-title">Interview Details</h3>
                <div class="assess-field">
                    <label class="assess-label">Meeting Link / Location</label>
                    <input class="assess-input" type="text" name="meeting_link" placeholder="e.g. https://zoom.us/j/123456 or Office HQ, Room 3B">
                </div>
                <div class="assess-field" style="margin-top: 1.25rem;">
                    <label class="assess-label">Interview Panel (optional)</label>
                    <input class="assess-input" type="text" name="panel_members" placeholder="e.g. John D., Lisa K.">
                </div>
                <div class="assess-field" style="margin-top: 1.25rem;">
                    <label class="assess-label">Notes for Candidate</label>
                    <textarea class="assess-textarea" rows="3" name="notes_for_candidate" placeholder="Any preparation instructions, dress code, or special notes..."></textarea>
                </div>
            </section>

            <div class="emp-form-actions">
                <a href="../employer/interviews.php" class="assess-save-btn assess-save-btn--draft" style="text-decoration:none;">Cancel</a>
                <button type="submit" class="assess-save-btn assess-save-btn--publish">Schedule Interview</button>
            </div>
        </form>
    </main>
</body>
</html>
