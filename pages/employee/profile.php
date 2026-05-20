<?php
require_once __DIR__ . '/../../backend/helpers/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../database/repositories/UserRepository.php';

if (!isLoggedIn() || getCurrentUserRole() !== 'employee') {
    header('Location: ' . AUTH_URL . 'login.php');
    exit;
}

$userId   = getCurrentUserId();
$userRepo = new UserRepository($pdo);

$user       = $userRepo->findById($userId);
$skills     = $userRepo->getSkills($userId);
$experience = $userRepo->getExperience($userId);
$education  = $userRepo->getEducation($userId);

$skillNames = implode(', ', array_map(fn($s) => $s['skill_name'], $skills));
$initials   = getCurrentUserInitials();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php $pageTitle = 'Edit Profile | Hireable'; ?>
    <?php $pageCss = ['profile.css', 'toast.css'];
    include __DIR__ . '/../../components/shared/head.php'; ?>
</head>
<body class="dash-page">
    <?php include __DIR__ . '/../../components/shared/toast.php'; ?>
    <?php $activePage = 'profile'; ?>
    <?php include __DIR__ . '/../../components/employee/sidebar.php'; ?>

    <main class="dash-main" style="margin-left: 260px;">
        <!-- Top App Bar -->
        <div class="profile-topbar">
            <div class="profile-topbar-left">
                <span class="profile-topbar-brand">Hireable</span>
                <nav class="profile-topbar-nav">
                    <a href="../employee/job-search.php">Browse Jobs</a>
                    <a href="../employee/applications.php">Applications</a>
                    <a href="../shared/skill-assesment.php">Assessments</a>
                </nav>
            </div>
            <div class="profile-topbar-right">
                <a class="profile-find-jobs-btn" href="../employee/job-search.php">Find Jobs</a>
                <div class="profile-topbar-icons">
                    <span class="material-symbols-outlined profile-topbar-icon">notifications</span>
                    <span class="material-symbols-outlined profile-topbar-icon">mail</span>
                    <div class="profile-topbar-avatar"><?= $initials ?></div>
                </div>
            </div>
        </div>

        <!-- Edit Profile Form -->
        <div class="profile-canvas">
            <div class="profile-canvas-header">
                <h2 class="page-title profile-canvas-title">Edit Professional Profile</h2>
                <p class="page-subtitle">Refine your professional presence. This information is used to match you with opportunities.</p>
            </div>

            <form class="profile-form-sections" method="POST" action="/action/employee.profile.update">
                <!-- Section 1: Personal Information -->
                <section class="profile-section">
                    <div class="profile-section-label">
                        <h3 class="profile-section-title">Personal Information</h3>
                        <p class="profile-section-desc">Your basic identity details visible to hiring partners.</p>
                    </div>
                    <div class="profile-section-fields">
                        <div class="profile-field-grid-2">
                            <div class="profile-field-group">
                                <label class="profile-field-label">First Name</label>
                                <input class="profile-field-input profile-field-input--bold" type="text" name="first_name" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>">
                            </div>
                            <div class="profile-field-group">
                                <label class="profile-field-label">Last Name</label>
                                <input class="profile-field-input profile-field-input--bold" type="text" name="last_name" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>">
                            </div>
                            <div class="profile-field-group">
                                <label class="profile-field-label">Email</label>
                                <input class="profile-field-input" type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" readonly>
                            </div>
                            <div class="profile-field-group">
                                <label class="profile-field-label">Phone</label>
                                <input class="profile-field-input" type="tel" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Section 2: Location -->
                <section class="profile-section">
                    <div class="profile-section-label">
                        <h3 class="profile-section-title">Location Settings</h3>
                        <p class="profile-section-desc">Specify your location for regional opportunities.</p>
                    </div>
                    <div class="profile-section-fields">
                        <div class="profile-field-grid-2">
                            <div class="profile-field-group">
                                <label class="profile-field-label">Country</label>
                                <?php
                                $countries = ['Ethiopia','Kenya','Rwanda','Uganda','Tanzania','Nigeria','South Africa','Ghana','Egypt','Morocco'];
                                ?>
                                <select class="profile-field-input profile-field-select" name="country">
                                    <option value="">Select Country</option>
                                    <?php foreach ($countries as $c): ?>
                                        <option <?= ($user['country'] ?? '') === $c ? 'selected' : '' ?>><?= $c ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="profile-field-group">
                                <label class="profile-field-label">City</label>
                                <input class="profile-field-input" type="text" name="city" value="<?= htmlspecialchars($user['city'] ?? '') ?>" placeholder="e.g. Addis Ababa">
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Section 3: Professional Summary -->
                <section class="profile-section">
                    <div class="profile-section-label">
                        <h3 class="profile-section-title">Professional Summary</h3>
                        <p class="profile-section-desc">A concise statement of your expertise and career goals.</p>
                    </div>
                    <div class="profile-section-fields">
                        <div class="profile-field-group">
                            <label class="profile-field-label">Headline</label>
                            <input class="profile-field-input" type="text" name="headline" value="<?= htmlspecialchars($user['headline'] ?? '') ?>" placeholder="e.g. Full-Stack Developer | 5 Years Experience">
                        </div>
                        <div class="profile-field-group">
                            <label class="profile-field-label">About You</label>
                            <textarea class="profile-textarea" rows="6" name="bio" placeholder="Write about your professional experience, goals, and expertise..."><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                        </div>
                    </div>
                </section>

                <!-- Section 4: Work Experience -->
                <section class="profile-section">
                    <div class="profile-section-label">
                        <h3 class="profile-section-title">Work Experience</h3>
                        <p class="profile-section-desc">Detail your most significant professional milestones.</p>
                        <button class="profile-add-btn" type="button" id="addExperienceBtn">
                            <span class="material-symbols-outlined">add</span>
                            Add Experience
                        </button>
                    </div>
                    <div class="profile-section-fields profile-experience-list" id="experienceList">
                        <?php if (empty($experience)): ?>
                            <p style="color:#7a6b5a; font-size:0.9rem;">No experience added yet. Click "Add Experience" to start.</p>
                        <?php else: ?>
                            <?php foreach ($experience as $i => $exp): ?>
                            <div class="profile-exp-card <?= $i === 0 ? 'profile-exp-card--primary' : 'profile-exp-card--secondary' ?>">
                                <input type="hidden" name="exp_id[]" value="<?= $exp['id'] ?>">
                                <div class="profile-field-grid-2">
                                    <div class="profile-field-group">
                                        <label class="profile-field-label">Job Title</label>
                                        <input class="profile-field-input profile-field-input--bold" type="text" name="exp_title[]" value="<?= htmlspecialchars($exp['job_title']) ?>">
                                    </div>
                                    <div class="profile-field-group">
                                        <label class="profile-field-label">Company</label>
                                        <input class="profile-field-input" type="text" name="exp_company[]" value="<?= htmlspecialchars($exp['company']) ?>">
                                    </div>
                                    <div class="profile-field-group">
                                        <label class="profile-field-label">Start Date</label>
                                        <input class="profile-field-input" type="date" name="exp_start[]" value="<?= $exp['start_date'] ?>">
                                    </div>
                                    <div class="profile-field-group">
                                        <label class="profile-field-label">End Date</label>
                                        <input class="profile-field-input" type="date" name="exp_end[]" value="<?= $exp['is_current'] ? '' : $exp['end_date'] ?>" <?= $exp['is_current'] ? 'disabled' : '' ?>>
                                        <label style="font-size:0.75rem; margin-top:0.3rem; display:flex; align-items:center; gap:0.3rem;">
                                            <input type="checkbox" name="exp_current[]" value="<?= $i ?>" <?= $exp['is_current'] ? 'checked' : '' ?>> Current
                                        </label>
                                    </div>
                                </div>
                                <div class="profile-field-group">
                                    <label class="profile-field-label">Description</label>
                                    <textarea class="profile-field-input profile-field-textarea" rows="3" name="exp_desc[]"><?= htmlspecialchars($exp['description'] ?? '') ?></textarea>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </section>

                <!-- Section 5: Education -->
                <section class="profile-section">
                    <div class="profile-section-label">
                        <h3 class="profile-section-title">Education</h3>
                        <p class="profile-section-desc">Your academic background and qualifications.</p>
                        <button class="profile-add-btn" type="button" id="addEducationBtn">
                            <span class="material-symbols-outlined">add</span>
                            Add Education
                        </button>
                    </div>
                    <div class="profile-section-fields" id="educationList">
                        <?php if (empty($education)): ?>
                            <p style="color:#7a6b5a; font-size:0.9rem;">No education added yet.</p>
                        <?php else: ?>
                            <?php foreach ($education as $edu): ?>
                            <div class="profile-exp-card profile-exp-card--primary">
                                <input type="hidden" name="edu_id[]" value="<?= $edu['id'] ?>">
                                <div class="profile-field-grid-2">
                                    <div class="profile-field-group">
                                        <label class="profile-field-label">School / University</label>
                                        <input class="profile-field-input profile-field-input--bold" type="text" name="edu_institution[]" value="<?= htmlspecialchars($edu['institution']) ?>">
                                    </div>
                                    <div class="profile-field-group">
                                        <label class="profile-field-label">Degree</label>
                                        <input class="profile-field-input" type="text" name="edu_degree[]" value="<?= htmlspecialchars($edu['degree']) ?>">
                                    </div>
                                    <div class="profile-field-group">
                                        <label class="profile-field-label">Field of Study</label>
                                        <input class="profile-field-input" type="text" name="edu_field[]" value="<?= htmlspecialchars($edu['field_of_study'] ?? '') ?>">
                                    </div>
                                    <div class="profile-field-group">
                                        <label class="profile-field-label">Graduation Year</label>
                                        <input class="profile-field-input" type="number" name="edu_end_year[]" value="<?= $edu['end_date'] ? date('Y', strtotime($edu['end_date'])) : '' ?>" min="1950" max="2099">
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </section>

                <!-- Section 6: Skills & Links -->
                <section class="profile-section">
                    <div class="profile-section-label">
                        <h3 class="profile-section-title">Skills & Links</h3>
                        <p class="profile-section-desc">Highlight your key competencies and professional presence.</p>
                    </div>
                    <div class="profile-section-fields">
                        <div class="profile-field-grid-2">
                            <div class="profile-field-group">
                                <label class="profile-field-label">Skills (comma separated)</label>
                                <input class="profile-field-input" type="text" name="skills" value="<?= htmlspecialchars($skillNames) ?>">
                            </div>
                            <div class="profile-field-group">
                                <label class="profile-field-label">Years of Experience</label>
                                <input class="profile-field-input" type="number" name="years_of_experience" value="<?= (int)($user['years_of_experience'] ?? 0) ?>" min="0" max="50">
                            </div>
                            <div class="profile-field-group">
                                <label class="profile-field-label">Portfolio URL</label>
                                <input class="profile-field-input" type="url" name="portfolio_url" value="<?= htmlspecialchars($user['portfolio_url'] ?? '') ?>" placeholder="https://your-portfolio.com">
                            </div>
                            <div class="profile-field-group">
                                <label class="profile-field-label">LinkedIn URL</label>
                                <input class="profile-field-input" type="url" name="linkedin_url" value="<?= htmlspecialchars($user['linkedin_url'] ?? '') ?>" placeholder="https://linkedin.com/in/username">
                            </div>
                            <div class="profile-field-group">
                                <label class="profile-field-label">GitHub URL</label>
                                <input class="profile-field-input" type="url" name="github_url" value="<?= htmlspecialchars($user['github_url'] ?? '') ?>" placeholder="https://github.com/username">
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Footer Actions -->
                <div class="profile-form-actions">
                    <button class="profile-discard-btn" type="button" onclick="window.location.reload()">Discard Changes</button>
                    <button class="profile-save-btn" type="submit">Save Changes</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>