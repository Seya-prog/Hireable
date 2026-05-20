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
$certs      = $userRepo->getCertifications($userId);

$fullName   = htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
$email      = htmlspecialchars($user['email'] ?? '');
$phone      = htmlspecialchars($user['phone'] ?? '');
$headline   = htmlspecialchars($user['headline'] ?? '');
$bio        = htmlspecialchars($user['bio'] ?? '');
$city       = htmlspecialchars(trim(($user['city'] ?? '') . ', ' . ($user['country'] ?? ''), ', '));
$skillNames = array_map(fn($s) => $s['skill_name'], $skills);
$linkedin   = htmlspecialchars($user['linkedin_url'] ?? '');
$portfolio  = htmlspecialchars($user['portfolio_url'] ?? '');
$github     = htmlspecialchars($user['github_url'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php $pageTitle = 'Resume Generator | Hireable'; ?>
    <?php $pageCss = ['resume.css', 'toast.css'];
    include __DIR__ . '/../../components/shared/head.php'; ?>
</head>
<body class="dash-page">
    <?php include __DIR__ . '/../../components/shared/toast.php'; ?>
    <?php $activePage = 'resume-generator'; ?>
    <?php include __DIR__ . '/../../components/employee/sidebar.php'; ?>

    <main class="dash-main" style="margin-left: 260px;">
        <div class="resume-header">
            <div>
                <h2 class="page-title">Resume Generator</h2>
                <p class="page-subtitle">Craft a polished, ATS-optimized resume. Edit, enhance with AI, and export as PDF.</p>
            </div>
        </div>

        <div class="resume-content">
            <!-- Left: Builder -->
            <div class="resume-builder">
                <!-- Template Selection -->
                <section>
                    <div class="resume-section-head">
                        <h3 class="resume-section-title">Choose a Template</h3>
                        <span class="resume-section-badge">4 Available</span>
                    </div>
                    <div class="resume-template-grid">
                        <div class="resume-template-card resume-template-card--active" data-template="executive" onclick="selectTemplate(this)">
                            <div class="resume-template-preview resume-template-preview--executive">
                                <div class="resume-tpl-bar"></div>
                                <div class="resume-tpl-line resume-tpl-line--wide"></div>
                                <div class="resume-tpl-line"></div>
                                <div class="resume-tpl-line resume-tpl-line--short"></div>
                                <div class="resume-tpl-spacer"></div>
                                <div class="resume-tpl-line resume-tpl-line--wide"></div>
                                <div class="resume-tpl-line"></div>
                            </div>
                            <p class="resume-template-name">Executive</p>
                            <span class="resume-template-check material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                        </div>
                        <div class="resume-template-card" data-template="modern" onclick="selectTemplate(this)">
                            <div class="resume-template-preview resume-template-preview--modern">
                                <div class="resume-tpl-sidebar"></div>
                                <div class="resume-tpl-body">
                                    <div class="resume-tpl-line resume-tpl-line--wide"></div>
                                    <div class="resume-tpl-line"></div>
                                    <div class="resume-tpl-line resume-tpl-line--short"></div>
                                </div>
                            </div>
                            <p class="resume-template-name">Modern</p>
                        </div>
                        <div class="resume-template-card" data-template="minimal" onclick="selectTemplate(this)">
                            <div class="resume-template-preview resume-template-preview--minimal">
                                <div class="resume-tpl-line resume-tpl-line--wide"></div>
                                <div class="resume-tpl-divider"></div>
                                <div class="resume-tpl-line"></div>
                                <div class="resume-tpl-line resume-tpl-line--short"></div>
                                <div class="resume-tpl-spacer"></div>
                                <div class="resume-tpl-line"></div>
                                <div class="resume-tpl-line resume-tpl-line--short"></div>
                            </div>
                            <p class="resume-template-name">Minimal</p>
                        </div>
                        <div class="resume-template-card" data-template="creative" onclick="selectTemplate(this)">
                            <div class="resume-template-preview resume-template-preview--creative">
                                <div class="resume-tpl-accent"></div>
                                <div class="resume-tpl-line resume-tpl-line--wide"></div>
                                <div class="resume-tpl-line"></div>
                                <div class="resume-tpl-line resume-tpl-line--short"></div>
                                <div class="resume-tpl-spacer"></div>
                                <div class="resume-tpl-line"></div>
                            </div>
                            <p class="resume-template-name">Creative</p>
                        </div>
                    </div>
                </section>

                <!-- Personal Info -->
                <section>
                    <div class="resume-section-head">
                        <h3 class="resume-section-title">Personal Information</h3>
                        <button class="resume-autofill-btn" onclick="autofillFromProfile()">
                            <span class="material-symbols-outlined">auto_fix_high</span>
                            Auto-fill from Profile
                        </button>
                    </div>
                    <div class="resume-form-grid">
                        <div class="resume-field">
                            <label class="resume-label">Full Name</label>
                            <input class="resume-input" type="text" id="rv-name" value="<?= $fullName ?>" placeholder="Your full name" oninput="updatePreview()">
                        </div>
                        <div class="resume-field">
                            <label class="resume-label">Job Title</label>
                            <input class="resume-input" type="text" id="rv-title" value="<?= $headline ?>" placeholder="Target job title" oninput="updatePreview()">
                        </div>
                        <div class="resume-field">
                            <label class="resume-label">Email</label>
                            <input class="resume-input" type="email" id="rv-email" value="<?= $email ?>" placeholder="Email address" oninput="updatePreview()">
                        </div>
                        <div class="resume-field">
                            <label class="resume-label">Phone</label>
                            <input class="resume-input" type="tel" id="rv-phone" value="<?= $phone ?>" placeholder="Phone number" oninput="updatePreview()">
                        </div>
                        <div class="resume-field">
                            <label class="resume-label">Location</label>
                            <input class="resume-input" type="text" id="rv-location" value="<?= $city ?>" placeholder="City, Country" oninput="updatePreview()">
                        </div>
                        <div class="resume-field">
                            <label class="resume-label">LinkedIn / Portfolio</label>
                            <input class="resume-input" type="url" id="rv-link" value="<?= $linkedin ?: $portfolio ?>" placeholder="https://..." oninput="updatePreview()">
                        </div>
                        <div class="resume-field resume-field--full">
                            <label class="resume-label">
                                Professional Summary
                                <button class="resume-ai-inline-btn" onclick="aiEnhanceSummary()" title="Enhance with AI">
                                    <span class="material-symbols-outlined">auto_awesome</span>
                                </button>
                            </label>
                            <textarea class="resume-textarea" rows="4" id="rv-summary" placeholder="Write a compelling summary..." oninput="updatePreview()"><?= $bio ?></textarea>
                        </div>
                    </div>
                </section>

                <!-- Work Experience -->
                <section>
                    <div class="resume-section-head">
                        <h3 class="resume-section-title">Work Experience</h3>
                        <button class="resume-add-btn" onclick="addExperience()">
                            <span class="material-symbols-outlined">add</span>
                            Add Position
                        </button>
                    </div>
                    <div class="resume-exp-list" id="rv-exp-list">
                        <?php if (empty($experience)): ?>
                            <p id="rv-exp-empty" style="color:#7a6b5a;">No experience added yet. Click "Add Position" or "Auto-fill from Profile".</p>
                        <?php else: ?>
                            <?php foreach ($experience as $i => $exp):
                                $startY = date('M Y', strtotime($exp['start_date']));
                                $endY = $exp['is_current'] ? 'Present' : ($exp['end_date'] ? date('M Y', strtotime($exp['end_date'])) : '');
                                $period = $startY . ' – ' . $endY;
                            ?>
                            <div class="resume-exp-card" data-exp-idx="<?= $i ?>">
                                <div class="resume-exp-card-header">
                                    <div class="resume-exp-card-drag"><span class="material-symbols-outlined">drag_indicator</span></div>
                                    <div class="resume-exp-card-info">
                                        <h4 class="resume-exp-title"><?= htmlspecialchars($exp['job_title']) ?></h4>
                                        <p class="resume-exp-company"><?= htmlspecialchars($exp['company']) ?> • <?= $period ?></p>
                                    </div>
                                    <button class="resume-ai-inline-btn" onclick="aiEnhanceBullets(this)" title="Enhance with AI">
                                        <span class="material-symbols-outlined">auto_awesome</span>
                                    </button>
                                    <button class="resume-exp-remove" onclick="removeExperience(this)"><span class="material-symbols-outlined">delete</span></button>
                                </div>
                                <textarea class="resume-exp-desc" rows="3" oninput="updatePreview()" placeholder="Describe your responsibilities and achievements..."><?= htmlspecialchars($exp['description'] ?? '') ?></textarea>
                                <input type="hidden" class="exp-title" value="<?= htmlspecialchars($exp['job_title']) ?>">
                                <input type="hidden" class="exp-company" value="<?= htmlspecialchars($exp['company']) ?>">
                                <input type="hidden" class="exp-period" value="<?= $period ?>">
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </section>

                <!-- Skills -->
                <section>
                    <div class="resume-section-head">
                        <h3 class="resume-section-title">Skills</h3>
                        <button class="resume-ai-inline-btn" onclick="aiSuggestSkills()" title="Suggest skills with AI" style="padding:0.4rem 0.8rem; border:1.5px solid #d0c5bb; border-radius:8px; font-size:0.7rem; font-weight:700;">
                            <span class="material-symbols-outlined" style="font-size:0.9rem;">auto_awesome</span> Suggest
                        </button>
                    </div>
                    <div class="resume-skills-wrap" id="rv-skills-wrap">
                        <?php foreach ($skillNames as $sk): ?>
                        <div class="resume-skill-tag"><?= htmlspecialchars($sk) ?> <button class="resume-skill-remove" onclick="removeSkill(this)">&times;</button></div>
                        <?php endforeach; ?>
                        <input class="resume-skill-input" type="text" id="rv-skill-input" placeholder="Add a skill..." onkeydown="handleSkillKey(event)">
                    </div>
                </section>

                <!-- Education -->
                <section>
                    <div class="resume-section-head">
                        <h3 class="resume-section-title">Education</h3>
                        <button class="resume-add-btn" onclick="addEducation()">
                            <span class="material-symbols-outlined">add</span>
                            Add Education
                        </button>
                    </div>
                    <div id="rv-edu-list">
                        <?php if (empty($education)): ?>
                            <p id="rv-edu-empty" style="color:#7a6b5a;">No education added yet.</p>
                        <?php else: ?>
                            <?php foreach ($education as $edu):
                                $eduPeriod = ($edu['start_date'] ? date('Y', strtotime($edu['start_date'])) : '') . ' – ' . ($edu['end_date'] ? date('Y', strtotime($edu['end_date'])) : 'Present');
                            ?>
                            <div class="resume-edu-card">
                                <div class="resume-edu-icon"><span class="material-symbols-outlined">school</span></div>
                                <div style="flex:1;">
                                    <h4 class="resume-edu-degree"><?= htmlspecialchars($edu['degree']) ?><?= !empty($edu['field_of_study']) ? ' in ' . htmlspecialchars($edu['field_of_study']) : '' ?></h4>
                                    <p class="resume-edu-school"><?= htmlspecialchars($edu['institution']) ?> • <?= $eduPeriod ?></p>
                                </div>
                                <button class="resume-exp-remove" onclick="removeEducation(this)"><span class="material-symbols-outlined">delete</span></button>
                                <input type="hidden" class="edu-degree" value="<?= htmlspecialchars($edu['degree'] . (!empty($edu['field_of_study']) ? ' in ' . $edu['field_of_study'] : '')) ?>">
                                <input type="hidden" class="edu-school" value="<?= htmlspecialchars($edu['institution']) ?>">
                                <input type="hidden" class="edu-period" value="<?= $eduPeriod ?>">
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </section>
            </div>

            <!-- Right: Preview & Actions -->
            <div class="resume-sidebar">
                <div class="resume-preview-panel">
                    <div class="resume-preview-header">
                        <h4 class="resume-preview-title">Live Preview</h4>
                        <div class="resume-preview-actions">
                            <button class="resume-preview-btn" title="Zoom In" onclick="zoomPreview(1)"><span class="material-symbols-outlined">zoom_in</span></button>
                            <button class="resume-preview-btn" title="Zoom Out" onclick="zoomPreview(-1)"><span class="material-symbols-outlined">zoom_out</span></button>
                        </div>
                    </div>
                    <div class="resume-preview-doc" id="resume-preview-doc">
                        <div class="resume-preview-page" id="resume-preview-page">
                            <!-- Rendered by JS -->
                        </div>
                    </div>
                </div>

                <!-- Export -->
                <div class="resume-export-panel">
                    <h4 class="resume-export-title">Export Resume</h4>
                    <button class="resume-export-btn resume-export-btn--primary" onclick="exportPDF()">
                        <span class="material-symbols-outlined">picture_as_pdf</span>
                        Download PDF
                    </button>
                    <button class="resume-export-btn" onclick="printResume()">
                        <span class="material-symbols-outlined">print</span>
                        Print Resume
                    </button>
                </div>

                <!-- AI Panel -->
                <div class="resume-ai-panel">
                    <div class="resume-ai-header">
                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">auto_awesome</span>
                        <h4 class="resume-ai-title">AI Suggestions</h4>
                    </div>
                    <div class="resume-ai-tips" id="rv-ai-tips">
                        <div class="resume-ai-tip">
                            <span class="material-symbols-outlined resume-ai-tip-icon">lightbulb</span>
                            <p>Click "Auto-fill" then use the <strong>✨ AI buttons</strong> next to each section to enhance your content.</p>
                        </div>
                    </div>
                    <button class="resume-autofill-btn" style="width:100%; justify-content:center; margin-top:1rem;" onclick="getAISuggestions()">
                        <span class="material-symbols-outlined">analytics</span>
                        Analyze Resume
                    </button>
                </div>
            </div>
        </div>
    </main>

    <!-- Profile data for JS auto-fill -->
    <script>
    const profileData = {
        name: <?= json_encode(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''))) ?>,
        title: <?= json_encode($user['headline'] ?? '') ?>,
        email: <?= json_encode($user['email'] ?? '') ?>,
        phone: <?= json_encode($user['phone'] ?? '') ?>,
        location: <?= json_encode(trim(($user['city'] ?? '') . ', ' . ($user['country'] ?? ''), ', ')) ?>,
        link: <?= json_encode($user['linkedin_url'] ?? $user['portfolio_url'] ?? '') ?>,
        summary: <?= json_encode($user['bio'] ?? '') ?>,
        skills: <?= json_encode($skillNames) ?>,
        experience: <?= json_encode(array_map(function($e) {
            $s = date('M Y', strtotime($e['start_date']));
            $end = $e['is_current'] ? 'Present' : ($e['end_date'] ? date('M Y', strtotime($e['end_date'])) : '');
            return ['title' => $e['job_title'], 'company' => $e['company'], 'period' => "$s – $end", 'description' => $e['description'] ?? ''];
        }, $experience)) ?>,
        education: <?= json_encode(array_map(function($e) {
            $p = ($e['start_date'] ? date('Y', strtotime($e['start_date'])) : '') . ' – ' . ($e['end_date'] ? date('Y', strtotime($e['end_date'])) : 'Present');
            return ['degree' => $e['degree'] . (!empty($e['field_of_study']) ? ' in ' . $e['field_of_study'] : ''), 'school' => $e['institution'], 'period' => $p];
        }, $education)) ?>
    };
    </script>
    <script src="/public/assets/js/resume-generator.js"></script>
</body>
</html>
