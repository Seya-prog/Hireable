<!DOCTYPE html>
<html lang="en">
<head>
    <?php $pageTitle = 'Create Job | Hireable Employer'; ?>
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
                <h2 class="page-title">Create New Job</h2>
                <p class="page-subtitle">Fill in the details to publish a new job posting.</p>
            </div>
        </div>

        <form class="emp-form">
            <!-- Basic Info -->
            <section class="emp-form-section">
                <h3 class="emp-form-section-title">Basic Information</h3>
                <div class="emp-form-grid">
                    <div class="assess-field assess-field--full">
                        <label class="assess-label">Job Title</label>
                        <input class="assess-input" type="text" placeholder="e.g. Senior Software Engineer">
                    </div>
                    <div class="assess-field">
                        <label class="assess-label">Department</label>
                        <select class="assess-input assess-select">
                            <option value="">Select department...</option>
                            <option>Engineering</option>
                            <option>Product</option>
                            <option>Marketing</option>
                            <option>Design</option>
                            <option>Operations</option>
                            <option>Analytics</option>
                        </select>
                    </div>
                    <div class="assess-field">
                        <label class="assess-label">Location</label>
                        <input class="assess-input" type="text" placeholder="e.g. Addis Ababa, Remote">
                    </div>
                    <div class="assess-field">
                        <label class="assess-label">Employment Type</label>
                        <select class="assess-input assess-select">
                            <option>Full-time</option>
                            <option>Part-time</option>
                            <option>Contract</option>
                            <option>Internship</option>
                        </select>
                    </div>
                    <div class="assess-field">
                        <label class="assess-label">Experience Level</label>
                        <select class="assess-input assess-select">
                            <option>Entry Level</option>
                            <option>Mid Level</option>
                            <option selected>Senior Level</option>
                            <option>Executive</option>
                        </select>
                    </div>
                </div>
            </section>

            <!-- Compensation -->
            <section class="emp-form-section">
                <h3 class="emp-form-section-title">Compensation</h3>
                <div class="emp-form-grid">
                    <div class="assess-field">
                        <label class="assess-label">Salary Min (USD)</label>
                        <input class="assess-input" type="text" placeholder="e.g. 80,000">
                    </div>
                    <div class="assess-field">
                        <label class="assess-label">Salary Max (USD)</label>
                        <input class="assess-input" type="text" placeholder="e.g. 120,000">
                    </div>
                </div>
            </section>

            <!-- Description -->
            <section class="emp-form-section">
                <h3 class="emp-form-section-title">Job Description</h3>
                <div class="assess-field">
                    <label class="assess-label">Description</label>
                    <textarea class="assess-textarea" rows="6" placeholder="Describe the role, responsibilities, and what a typical day looks like..."></textarea>
                </div>
                <div class="assess-field" style="margin-top: 1.25rem;">
                    <label class="assess-label">Requirements</label>
                    <textarea class="assess-textarea" rows="5" placeholder="List the required qualifications, skills, and experience..."></textarea>
                </div>
                <div class="assess-field" style="margin-top: 1.25rem;">
                    <label class="assess-label">Benefits</label>
                    <textarea class="assess-textarea" rows="4" placeholder="Health insurance, stock options, flexible hours, etc."></textarea>
                </div>
            </section>

            <!-- Skills -->
            <section class="emp-form-section">
                <h3 class="emp-form-section-title">Required Skills</h3>
                <div class="emp-skills-input-wrap">
                    <input class="assess-input" type="text" placeholder="Type a skill and press Enter..." id="skill-input">
                    <div class="emp-skills-tags" id="skills-list">
                        <span class="emp-cand-skill-tag">React <button class="emp-tag-remove">&times;</button></span>
                        <span class="emp-cand-skill-tag">TypeScript <button class="emp-tag-remove">&times;</button></span>
                        <span class="emp-cand-skill-tag">Node.js <button class="emp-tag-remove">&times;</button></span>
                    </div>
                </div>
            </section>

            <!-- Assessment -->
            <section class="emp-form-section">
                <h3 class="emp-form-section-title">Skill Assessment</h3>
                <p class="emp-form-hint">Optionally link an assessment to screen candidates automatically.</p>
                <select class="assess-input assess-select" style="max-width: 400px;">
                    <option value="">None — no assessment required</option>
                    <option>React Frontend Assessment (15 questions)</option>
                    <option>Product Strategy Case Study (8 questions)</option>
                    <option>Data Analysis Challenge (20 questions)</option>
                </select>
                <a href="skill-assesment.php?tab=create" class="emp-form-inline-link">
                    <span class="material-symbols-outlined">add</span> Create a new assessment
                </a>
            </section>

            <div class="emp-form-actions">
                <a href="jobs.php" class="assess-save-btn assess-save-btn--draft">Cancel</a>
                <button type="button" class="assess-save-btn assess-save-btn--draft">Save as Draft</button>
                <button type="button" class="assess-save-btn assess-save-btn--publish">Publish Job</button>
            </div>
        </form>
    </main>
</body>
</html>
