<?php
require_once __DIR__ . '/../../backend/helpers/session.php';
require_once __DIR__ . '/../../backend/helpers/csrf.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../database/repositories/AssessmentRepository.php';
require_once __DIR__ . '/../../database/repositories/JobRepository.php';

if (!isLoggedIn() || getCurrentUserRole() !== 'employer') {
    header('Location: ' . AUTH_URL . 'login.php');
    exit;
}

$employerId = getCurrentUserId();
$assessRepo = new AssessmentRepository($pdo);
$jobRepo    = new JobRepository($pdo);
$assessments = $assessRepo->findByEmployer($employerId);

// Edit mode
$isEdit = false;
$job = null;
$editId = intval($_GET['id'] ?? 0);
if ($editId) {
    $job = $jobRepo->findById($editId);
    if ($job && $jobRepo->isOwner($editId, $employerId)) {
        $isEdit = true;
    } else {
        header('Location: ../employer/jobs.php');
        exit;
    }
}

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php $pageTitle = ($isEdit ? 'Edit Job' : 'Create Job') . ' | Hireable Employer'; ?>
    <?php $pageCss = ['employer.css', 'toast.css'];
    include __DIR__ . '/../../components/shared/head.php'; ?>
</head>
<body class="dash-page">
    <?php include __DIR__ . '/../../components/shared/toast.php'; ?>
    <?php $activePage = 'jobs'; ?>
    <?php include __DIR__ . '/../../components/employer/employer-sidebar.php'; ?>

    <main class="dash-main" style="margin-left: 260px;">
        <div class="emp-header">
            <div>
                <a href="../employer/jobs.php" class="emp-back-link">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Back to Job Postings
                </a>
                <h2 class="page-title"><?= $isEdit ? 'Edit Job Post' : 'Create New Job' ?></h2>
                <p class="page-subtitle"><?= $isEdit ? 'Update the details of your job posting.' : 'Fill in the details to publish a new job posting.' ?></p>
            </div>
        </div>

        <form class="emp-form" method="POST" action="/action/<?= $isEdit ? 'employer.jobs.update' : 'employer.jobs.create' ?>" id="job-form">
            <?= csrfField() ?>
            <?php if ($isEdit): ?><input type="hidden" name="job_id" value="<?= $editId ?>"><?php endif; ?>
            <!-- Basic Info -->
            <section class="emp-form-section">
                <h3 class="emp-form-section-title">Basic Information</h3>
                <div class="emp-form-grid">
                    <div class="assess-field assess-field--full">
                        <label class="assess-label">Job Title *</label>
                        <input class="assess-input" type="text" name="title" placeholder="e.g. Senior Software Engineer" required value="<?= htmlspecialchars($job['title'] ?? '') ?>">
                    </div>
                    <div class="assess-field">
                        <label class="assess-label">Department</label>
                        <select class="assess-input assess-select" name="department">
                            <option value="">Select department...</option>
                            <?php $depts = ['Engineering','Product','Marketing','Design','Operations','Analytics','Finance','Human Resources']; foreach($depts as $d): ?>
                            <option <?= ($job['department'] ?? '') === $d ? 'selected' : '' ?>><?= $d ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="assess-field">
                        <label class="assess-label">Location</label>
                        <input class="assess-input" type="text" name="location" placeholder="e.g. Addis Ababa, Remote" value="<?= htmlspecialchars($job['location'] ?? '') ?>">
                    </div>
                    <div class="assess-field">
                        <label class="assess-label">Employment Type</label>
                        <select class="assess-input assess-select" name="job_type">
                            <option value="full-time" <?= ($job['job_type'] ?? '') === 'full-time' ? 'selected' : '' ?>>Full-time</option>
                            <option value="part-time" <?= ($job['job_type'] ?? '') === 'part-time' ? 'selected' : '' ?>>Part-time</option>
                            <option value="contract" <?= ($job['job_type'] ?? '') === 'contract' ? 'selected' : '' ?>>Contract</option>
                            <option value="internship" <?= ($job['job_type'] ?? '') === 'internship' ? 'selected' : '' ?>>Internship</option>
                        </select>
                    </div>
                    <div class="assess-field">
                        <label class="assess-label">Experience Level</label>
                        <select class="assess-input assess-select" name="experience_level">
                            <option value="entry" <?= ($job['experience_level'] ?? '') === 'entry' ? 'selected' : '' ?>>Entry Level</option>
                            <option value="mid" <?= ($job['experience_level'] ?? '') === 'mid' ? 'selected' : '' ?>>Mid Level</option>
                            <option value="senior" <?= (!$isEdit || ($job['experience_level'] ?? '') === 'senior') ? 'selected' : '' ?>>Senior Level</option>
                            <option value="executive" <?= ($job['experience_level'] ?? '') === 'executive' ? 'selected' : '' ?>>Executive</option>
                        </select>
                    </div>
                    <div class="assess-field">
                        <label class="assess-label">Application Deadline</label>
                        <input class="assess-input" type="date" name="application_deadline" value="<?= htmlspecialchars($job['application_deadline'] ?? '') ?>">
                    </div>
                </div>
            </section>

            <!-- Compensation -->
            <section class="emp-form-section">
                <h3 class="emp-form-section-title">Compensation</h3>
                <div class="emp-form-grid">
                    <div class="assess-field">
                        <label class="assess-label">Salary Min (USD)</label>
                        <input class="assess-input" type="number" name="salary_min" placeholder="e.g. 80000" value="<?= htmlspecialchars($job['salary_min'] ?? '') ?>">
                    </div>
                    <div class="assess-field">
                        <label class="assess-label">Salary Max (USD)</label>
                        <input class="assess-input" type="number" name="salary_max" placeholder="e.g. 120000" value="<?= htmlspecialchars($job['salary_max'] ?? '') ?>">
                    </div>
                </div>
            </section>

            <!-- Description -->
            <section class="emp-form-section">
                <h3 class="emp-form-section-title">Job Description</h3>
                <div class="assess-field">
                    <label class="assess-label">Description *</label>
                    <textarea class="assess-textarea" rows="6" name="description" placeholder="Describe the role, responsibilities, and what a typical day looks like..." required><?= htmlspecialchars($job['description'] ?? '') ?></textarea>
                </div>
                <div class="assess-field" style="margin-top: 1.25rem;">
                    <label class="assess-label">Requirements</label>
                    <textarea class="assess-textarea" rows="5" name="requirements" placeholder="List the required qualifications, skills, and experience..."><?= htmlspecialchars($job['requirements'] ?? '') ?></textarea>
                </div>
                <div class="assess-field" style="margin-top: 1.25rem;">
                    <label class="assess-label">Responsibilities</label>
                    <textarea class="assess-textarea" rows="5" name="responsibilities" placeholder="Outline the key responsibilities of this role..."><?= htmlspecialchars($job['responsibilities'] ?? '') ?></textarea>
                </div>
                <div class="assess-field" style="margin-top: 1.25rem;">
                    <label class="assess-label">Benefits</label>
                    <textarea class="assess-textarea" rows="4" name="benefits" placeholder="Health insurance, stock options, flexible hours, etc."><?= htmlspecialchars($job['benefits'] ?? '') ?></textarea>
                </div>
            </section>

            <!-- Skills -->
            <section class="emp-form-section">
                <h3 class="emp-form-section-title">Required Skills</h3>
                <div class="emp-skills-input-wrap">
                    <input class="assess-input" type="text" placeholder="Type a skill and press Enter..." id="skill-input">
                    <input type="hidden" name="skills_required" id="skills-hidden" value="<?= htmlspecialchars($job['skills_required'] ?? '') ?>">
                    <div class="emp-skills-tags" id="skills-list"><?php if ($isEdit && !empty($job['skills_required'])): foreach(array_map('trim', explode(',', $job['skills_required'])) as $sk): ?><span class="emp-skill-tag"><?= htmlspecialchars($sk) ?> <button type="button" class="emp-skill-rm">&times;</button></span><?php endforeach; endif; ?></div>
                </div>
            </section>

            <!-- Assessment -->
            <section class="emp-form-section">
                <h3 class="emp-form-section-title">Skill Assessment</h3>
                <p class="emp-form-hint">Optionally link an assessment to screen candidates automatically.</p>
                <select class="assess-input assess-select" style="max-width: 400px;" name="assessment_id">
                    <option value="">None — no assessment required</option>
                    <?php foreach ($assessments as $a): ?>
                        <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['title']) ?> (<?= ucfirst($a['status']) ?>)</option>
                    <?php endforeach; ?>
                </select>
                <a href="../shared/skill-assesment.php?tab=create" class="emp-form-inline-link">
                    <span class="material-symbols-outlined">add</span> Create a new assessment
                </a>
            </section>

            <!-- Hidden status field set by JS -->
            <input type="hidden" name="status" id="job-status" value="draft">

            <div class="emp-form-actions">
                <a href="../employer/jobs.php" class="assess-save-btn assess-save-btn--draft">Cancel</a>
                <button type="submit" class="assess-save-btn assess-save-btn--draft" id="btn-draft"><?= $isEdit ? 'Save Draft' : 'Save as Draft' ?></button>
                <button type="submit" class="assess-save-btn assess-save-btn--publish" id="btn-publish"><?= $isEdit ? 'Update & Publish' : 'Publish Job' ?></button>
            </div>
        </form>
    </main>

    <script src="../../public/assets/js/job-create.js"></script>
</body>
</html>

