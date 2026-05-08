<!DOCTYPE html>
<html lang="en">
<head>
    <?php $pageTitle = 'Schedule Interview | Hireable Employer'; ?>
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
                <h2 class="page-title">Schedule Interview</h2>
                <p class="page-subtitle">Set up a new interview with a candidate.</p>
            </div>
        </div>

        <form class="emp-form" style="max-width: 720px;">
            <section class="emp-form-section">
                <h3 class="emp-form-section-title">Candidate & Position</h3>
                <div class="emp-form-grid">
                    <div class="assess-field">
                        <label class="assess-label">Candidate</label>
                        <select class="assess-input assess-select">
                            <option value="">Select candidate...</option>
                            <option selected>Sarah M. — VP of Product</option>
                            <option>Miriam W. — VP of Product</option>
                            <option>Daniel K. — Senior Engineer</option>
                            <option>Aisha L. — Marketing Lead</option>
                            <option>Tariku B. — Senior Engineer</option>
                            <option>James T. — Data Analyst</option>
                        </select>
                    </div>
                    <div class="assess-field">
                        <label class="assess-label">Position</label>
                        <select class="assess-input assess-select">
                            <option>VP of Product Innovation</option>
                            <option>Senior Software Engineer</option>
                            <option>Marketing Lead</option>
                            <option>Data Analyst</option>
                        </select>
                    </div>
                </div>
            </section>

            <section class="emp-form-section">
                <h3 class="emp-form-section-title">Date & Time</h3>
                <div class="emp-form-grid">
                    <div class="assess-field">
                        <label class="assess-label">Date</label>
                        <input class="assess-input" type="date" value="2026-05-09">
                    </div>
                    <div class="assess-field">
                        <label class="assess-label">Start Time</label>
                        <input class="assess-input" type="time" value="14:00">
                    </div>
                    <div class="assess-field">
                        <label class="assess-label">Duration</label>
                        <select class="assess-input assess-select">
                            <option>30 minutes</option>
                            <option>45 minutes</option>
                            <option selected>60 minutes</option>
                            <option>90 minutes</option>
                        </select>
                    </div>
                    <div class="assess-field">
                        <label class="assess-label">Interview Type</label>
                        <select class="assess-input assess-select">
                            <option>Phone Screen</option>
                            <option selected>Video Call (Zoom)</option>
                            <option>Video Call (Google Meet)</option>
                            <option>In-Person</option>
                        </select>
                    </div>
                </div>
            </section>

            <section class="emp-form-section">
                <h3 class="emp-form-section-title">Interview Details</h3>
                <div class="assess-field">
                    <label class="assess-label">Meeting Link / Location</label>
                    <input class="assess-input" type="text" placeholder="e.g. https://zoom.us/j/123456 or Office HQ, Room 3B">
                </div>
                <div class="assess-field" style="margin-top: 1.25rem;">
                    <label class="assess-label">Interview Panel (optional)</label>
                    <input class="assess-input" type="text" placeholder="e.g. John D., Lisa K.">
                </div>
                <div class="assess-field" style="margin-top: 1.25rem;">
                    <label class="assess-label">Notes for Candidate</label>
                    <textarea class="assess-textarea" rows="3" placeholder="Any preparation instructions, dress code, or special notes..."></textarea>
                </div>
            </section>

            <section class="emp-form-section">
                <h3 class="emp-form-section-title">Notifications</h3>
                <label class="emp-checkbox-label">
                    <input type="checkbox" checked> Send email invitation to candidate
                </label>
                <label class="emp-checkbox-label">
                    <input type="checkbox" checked> Send calendar invite to interview panel
                </label>
                <label class="emp-checkbox-label">
                    <input type="checkbox"> Send reminder 1 hour before
                </label>
            </section>

            <div class="emp-form-actions">
                <a href="interviews.php" class="assess-save-btn assess-save-btn--draft" style="text-decoration:none;">Cancel</a>
                <button type="button" class="assess-save-btn assess-save-btn--publish">Schedule Interview</button>
            </div>
        </form>
    </main>
</body>
</html>
