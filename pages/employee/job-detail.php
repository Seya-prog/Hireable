<?php
/**
 * Public Job Detail Page — standalone (no sidebar)
 * Allows employees to view job details and apply with CV upload + cover letter
 */
require_once __DIR__ . '/../../backend/helpers/session.php';
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../backend/helpers/csrf.php';
require_once __DIR__ . '/../../database/repositories/JobRepository.php';
require_once __DIR__ . '/../../database/repositories/ApplicationRepository.php';

$jobId = intval($_GET['id'] ?? 0);
if (!$jobId) { header('Location: job-search.php'); exit; }

$jobRepo = new JobRepository($pdo);
$appRepo = new ApplicationRepository($pdo);
$job = $jobRepo->findById($jobId);

if (!$job || $job['status'] !== 'active') {
    header('Location: job-search.php');
    exit;
}

// Check if user already applied
$alreadyApplied = false;
if (isLoggedIn() && getCurrentUserRole() === 'employee') {
    $alreadyApplied = $appRepo->isDuplicate($jobId, getCurrentUserId());
}

$company     = htmlspecialchars($job['company_name'] ?: ($job['first_name'] . ' ' . $job['last_name']));
$companyLogo = $job['company_logo'] ?? null;
$companyInit = strtoupper(substr($job['company_name'] ?? 'C', 0, 1));
$daysAgo     = floor((time() - strtotime($job['created_at'])) / 86400);
$postedLabel = $daysAgo === 0 ? 'Today' : ($daysAgo === 1 ? 'Yesterday' : $daysAgo . ' days ago');
$salary      = '';
if ($job['salary_min'] && $job['salary_max']) {
    $salary = '$' . number_format($job['salary_min']) . ' – $' . number_format($job['salary_max']);
} elseif ($job['salary_min']) {
    $salary = 'From $' . number_format($job['salary_min']);
}
$typeLabel  = ucfirst(str_replace('-', ' ', $job['job_type'] ?? 'Full time'));
$levelLabel = ucfirst($job['experience_level'] ?? 'Mid');
$skills     = !empty($job['skills_required']) ? array_map('trim', explode(',', $job['skills_required'])) : [];
$userName   = isLoggedIn() ? ($_SESSION['user_first_name'] ?? 'there') : 'there';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($job['title']) ?> at <?= $company ?> | Hireable</title>
    <meta name="description" content="Apply for <?= htmlspecialchars($job['title']) ?> at <?= $company ?>. <?= htmlspecialchars(substr($job['description'] ?? '', 0, 150)) ?>">
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,wght@0,400;0,500;0,700;1,400&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../public/assets/css/global.css">
    <link rel="stylesheet" href="../../public/assets/css/job-search.css">
    <link rel="stylesheet" href="../../public/assets/css/toast.css">
    <style>
    /* ── Job Detail Standalone ── */
    .jd-page { max-width: 900px; margin: 0 auto; padding: 2rem 1.5rem 4rem; }

    .jd-back { display: inline-flex; align-items: center; gap: 0.375rem; font-size: 0.8rem; font-weight: 600; color: #695d46; text-decoration: none; margin-bottom: 1.5rem; transition: color 0.2s; }
    .jd-back:hover { color: #170f07; }

    .jd-header { display: flex; gap: 1.5rem; align-items: flex-start; margin-bottom: 2rem; }
    .jd-logo { width: 64px; height: 64px; border-radius: 14px; object-fit: contain; background: #fff; border: 1px solid rgba(208,197,187,0.3); padding: 6px; flex-shrink: 0; }
    .jd-logo-fallback { width: 64px; height: 64px; border-radius: 14px; background: #f4eedb; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 700; color: #695d46; flex-shrink: 0; }
    .jd-title { font-family: 'Newsreader', serif; font-size: 1.75rem; font-weight: 700; color: #170f07; margin: 0 0 0.25rem; }
    .jd-company-name { font-size: 0.9rem; font-weight: 600; color: #8a5b1c; margin: 0 0 0.375rem; }
    .jd-meta-row { display: flex; flex-wrap: wrap; gap: 1rem; margin-top: 0.5rem; }
    .jd-meta-item { display: flex; align-items: center; gap: 0.375rem; font-size: 0.8rem; color: #7e766e; }
    .jd-meta-item .material-symbols-outlined { font-size: 1rem; }

    .jd-actions { display: flex; gap: 0.75rem; margin-bottom: 2.5rem; }
    .jd-apply-btn { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.875rem 2rem; background: #170f07; color: #fff; border: none; border-radius: 9999px; font-family: 'Manrope', sans-serif; font-size: 0.875rem; font-weight: 700; cursor: pointer; transition: opacity 0.2s; text-decoration: none; }
    .jd-apply-btn:hover { opacity: 0.9; }
    .jd-apply-btn:disabled, .jd-apply-btn--disabled { opacity: 0.5; cursor: not-allowed; }
    .jd-save-btn { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.875rem 1.5rem; border: 1.5px solid #d0c5bb; border-radius: 9999px; background: none; font-family: 'Manrope', sans-serif; font-size: 0.875rem; font-weight: 600; color: #4d453f; cursor: pointer; transition: all 0.2s; text-decoration: none; }
    .jd-save-btn:hover { border-color: #170f07; color: #170f07; }

    .jd-grid { display: grid; grid-template-columns: 1fr 300px; gap: 2.5rem; align-items: start; }
    .jd-main { display: flex; flex-direction: column; gap: 2rem; }
    .jd-sidebar { display: flex; flex-direction: column; gap: 1.25rem; position: sticky; top: 1.5rem; }

    .jd-section { background: #fff; border: 1px solid rgba(208,197,187,0.3); border-radius: 16px; padding: 1.75rem; }
    .jd-section-title { font-family: 'Newsreader', serif; font-weight: 700; font-size: 1.15rem; color: #170f07; margin: 0 0 1rem; }
    .jd-section p, .jd-section li { font-size: 0.875rem; color: #4d453f; line-height: 1.8; }
    .jd-section ul { padding-left: 1.25rem; margin: 0; }
    .jd-section ul li { margin-bottom: 0.5rem; }

    .jd-skills { display: flex; flex-wrap: wrap; gap: 0.5rem; }
    .jd-skill-tag { font-size: 0.7rem; font-weight: 600; padding: 0.375rem 0.875rem; background: #f4eedb; border-radius: 9999px; color: #4d453f; }

    .jd-sidebar-card { background: #f4eedb; border-radius: 16px; padding: 1.5rem; }
    .jd-sidebar-card h4 { font-family: 'Newsreader', serif; font-weight: 700; font-size: 1rem; color: #170f07; margin: 0 0 1rem; }
    .jd-info-row { display: flex; justify-content: space-between; font-size: 0.8rem; padding: 0.625rem 0; border-bottom: 1px solid rgba(208,197,187,0.25); }
    .jd-info-row:last-child { border-bottom: none; }
    .jd-info-row span:first-child { color: #7e766e; }
    .jd-info-row span:last-child { font-weight: 600; color: #170f07; }

    .jd-company-card { background: #fff; border: 1px solid rgba(208,197,187,0.3); border-radius: 16px; padding: 1.5rem; text-align: center; }
    .jd-company-card-logo { width: 48px; height: 48px; border-radius: 12px; object-fit: contain; margin: 0 auto 0.75rem; border: 1px solid rgba(208,197,187,0.3); padding: 4px; }
    .jd-company-card-init { width: 48px; height: 48px; border-radius: 12px; background: #f4eedb; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; font-weight: 700; color: #695d46; margin: 0 auto 0.75rem; }
    .jd-company-card h4 { font-size: 0.9rem; font-weight: 700; color: #170f07; margin: 0 0 0.25rem; }
    .jd-company-card p { font-size: 0.75rem; color: #7e766e; margin: 0 0 0.75rem; }
    .jd-company-link { font-size: 0.75rem; font-weight: 700; color: #695d46; text-decoration: none; }
    .jd-company-link:hover { text-decoration: underline; }

    /* ── Apply Modal ── */
    .jd-modal-overlay { display: none; position: fixed; inset: 0; background: rgba(23,15,7,0.5); z-index: 1000; align-items: center; justify-content: center; }
    .jd-modal-overlay.active { display: flex; }
    .jd-modal { background: #fff; border-radius: 20px; padding: 2.5rem; max-width: 560px; width: 90%; max-height: 85vh; overflow-y: auto; box-shadow: 0 24px 48px rgba(23,15,7,0.15); animation: jdSlideUp 0.3s ease; }
    @keyframes jdSlideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .jd-modal-title { font-family: 'Newsreader', serif; font-size: 1.35rem; font-weight: 700; color: #170f07; margin: 0 0 0.25rem; }
    .jd-modal-sub { font-size: 0.8rem; color: #7e766e; margin: 0 0 1.5rem; }
    .jd-modal-close { position: absolute; top: 1rem; right: 1rem; background: none; border: none; cursor: pointer; color: #7e766e; }

    .jd-form-group { margin-bottom: 1.25rem; }
    .jd-form-label { display: block; font-size: 0.7rem; font-weight: 700; color: #170f07; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 0.5rem; }
    .jd-form-input, .jd-form-textarea { width: 100%; padding: 0.75rem 1rem; background: #fff; border: 1.5px solid #d0c5bb; border-radius: 10px; font-family: 'Manrope', sans-serif; font-size: 0.875rem; color: #170f07; outline: none; transition: border-color 0.2s; box-sizing: border-box; }
    .jd-form-input:focus, .jd-form-textarea:focus { border-color: #170f07; box-shadow: 0 0 0 3px rgba(23,15,7,0.06); }
    .jd-form-textarea { resize: vertical; min-height: 100px; line-height: 1.6; }
    .jd-form-hint { font-size: 0.7rem; color: #a89f96; margin-top: 0.375rem; }

    .jd-upload-area { border: 2px dashed #d0c5bb; border-radius: 12px; padding: 1.5rem; text-align: center; cursor: pointer; transition: border-color 0.2s, background 0.2s; }
    .jd-upload-area:hover { border-color: #8a5b1c; background: rgba(244,238,219,0.3); }
    .jd-upload-area.has-file { border-color: #155724; background: rgba(212,237,218,0.2); }
    .jd-upload-icon { font-size: 2rem; color: #d0c5bb; margin-bottom: 0.5rem; }
    .jd-upload-text { font-size: 0.8rem; color: #7e766e; }
    .jd-upload-text strong { color: #8a5b1c; }
    .jd-upload-filename { font-size: 0.8rem; font-weight: 600; color: #155724; margin-top: 0.5rem; }

    .jd-modal-actions { display: flex; gap: 0.75rem; justify-content: flex-end; margin-top: 1.5rem; }
    .jd-modal-cancel { padding: 0.75rem 1.5rem; border: 1.5px solid #d0c5bb; border-radius: 9999px; background: none; font-family: 'Manrope', sans-serif; font-size: 0.8rem; font-weight: 600; color: #4d453f; cursor: pointer; }
    .jd-modal-submit { padding: 0.75rem 2rem; background: #170f07; color: #fff; border: none; border-radius: 9999px; font-family: 'Manrope', sans-serif; font-size: 0.8rem; font-weight: 700; cursor: pointer; transition: opacity 0.2s; }
    .jd-modal-submit:hover { opacity: 0.9; }

    @media (max-width: 768px) {
        .jd-grid { grid-template-columns: 1fr; }
        .jd-header { flex-direction: column; align-items: center; text-align: center; }
        .jd-meta-row { justify-content: center; }
        .jd-actions { justify-content: center; }
    }
    </style>
</head>
<body class="js-body">
    <?php if (isLoggedIn()) include __DIR__ . '/../../components/shared/toast.php'; ?>

    <!-- Top Nav -->
    <header class="js-topbar">
        <a href="../../public/index.php" class="js-logo">
            <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1">work</span>
            <span>Hireable</span>
        </a>
        <nav class="js-nav">
            <a href="job-search.php" class="js-nav-link">Marketplace</a>
            <?php if (isLoggedIn() && getCurrentUserRole() === 'employee'): ?>
            <a href="applications.php" class="js-nav-link">Applications</a>
            <a href="profile.php" class="js-nav-link">Profile</a>
            <?php endif; ?>
        </nav>
        <div class="js-nav-right">
            <?php if (isLoggedIn()): ?>
                <a href="profile.php" class="js-avatar"><?= strtoupper(substr($userName, 0, 1)) ?></a>
            <?php else: ?>
                <a href="../auth/login.php" class="js-login-btn">Sign In</a>
                <a href="../auth/signup.php" class="js-cta-btn">Get Started</a>
            <?php endif; ?>
        </div>
    </header>

    <main class="jd-page">
        <!-- Back -->
        <a href="job-search.php" class="jd-back">
            <span class="material-symbols-outlined">arrow_back</span>
            Back to Jobs
        </a>

        <!-- Header -->
        <div class="jd-header">
            <?php if ($companyLogo): ?>
                <img class="jd-logo" src="<?= htmlspecialchars($companyLogo) ?>" alt="<?= $company ?>" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                <div class="jd-logo-fallback" style="display:none"><?= $companyInit ?></div>
            <?php else: ?>
                <div class="jd-logo-fallback"><?= $companyInit ?></div>
            <?php endif; ?>
            <div>
                <h1 class="jd-title"><?= htmlspecialchars($job['title']) ?></h1>
                <p class="jd-company-name"><?= $company ?></p>
                <div class="jd-meta-row">
                    <span class="jd-meta-item"><span class="material-symbols-outlined">location_on</span> <?= htmlspecialchars($job['location'] ?: 'Remote') ?></span>
                    <span class="jd-meta-item"><span class="material-symbols-outlined">work</span> <?= $typeLabel ?></span>
                    <span class="jd-meta-item"><span class="material-symbols-outlined">trending_up</span> <?= $levelLabel ?> Level</span>
                    <span class="jd-meta-item"><span class="material-symbols-outlined">schedule</span> Posted <?= $postedLabel ?></span>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="jd-actions">
            <?php if ($alreadyApplied): ?>
                <span class="jd-apply-btn jd-apply-btn--disabled"><span class="material-symbols-outlined">check_circle</span> Already Applied</span>
            <?php elseif (isLoggedIn() && getCurrentUserRole() === 'employee'): ?>
                <button class="jd-apply-btn" id="openApplyModal"><span class="material-symbols-outlined">send</span> Apply Now</button>
            <?php else: ?>
                <a href="../auth/login.php" class="jd-apply-btn"><span class="material-symbols-outlined">login</span> Sign In to Apply</a>
            <?php endif; ?>
            <button class="jd-save-btn" onclick="this.querySelector('span').textContent=this.classList.toggle('saved')?'bookmark_added':'bookmark_border'">
                <span class="material-symbols-outlined">bookmark_border</span> Save
            </button>
        </div>

        <!-- Content Grid -->
        <div class="jd-grid">
            <div class="jd-main">
                <?php if (!empty($job['description'])): ?>
                <div class="jd-section">
                    <h2 class="jd-section-title">About This Role</h2>
                    <p><?= nl2br(htmlspecialchars($job['description'])) ?></p>
                </div>
                <?php endif; ?>

                <?php if (!empty($job['responsibilities'])): ?>
                <div class="jd-section">
                    <h2 class="jd-section-title">Responsibilities</h2>
                    <ul>
                        <?php foreach (array_filter(array_map('trim', explode("\n", $job['responsibilities']))) as $item): ?>
                            <li><?= htmlspecialchars($item) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <?php if (!empty($job['requirements'])): ?>
                <div class="jd-section">
                    <h2 class="jd-section-title">Requirements</h2>
                    <ul>
                        <?php foreach (array_filter(array_map('trim', explode("\n", $job['requirements']))) as $item): ?>
                            <li><?= htmlspecialchars($item) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <?php if (!empty($skills)): ?>
                <div class="jd-section">
                    <h2 class="jd-section-title">Required Skills</h2>
                    <div class="jd-skills">
                        <?php foreach ($skills as $skill): ?>
                            <span class="jd-skill-tag"><?= htmlspecialchars($skill) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($job['benefits'])): ?>
                <div class="jd-section">
                    <h2 class="jd-section-title">Benefits & Perks</h2>
                    <ul>
                        <?php foreach (array_filter(array_map('trim', explode("\n", $job['benefits']))) as $item): ?>
                            <li><?= htmlspecialchars($item) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="jd-sidebar">
                <div class="jd-sidebar-card">
                    <h4>Job Overview</h4>
                    <div class="jd-info-row"><span>Salary</span><span><?= $salary ?: 'Competitive' ?></span></div>
                    <div class="jd-info-row"><span>Type</span><span><?= $typeLabel ?></span></div>
                    <div class="jd-info-row"><span>Level</span><span><?= $levelLabel ?></span></div>
                    <div class="jd-info-row"><span>Location</span><span><?= htmlspecialchars($job['location'] ?: 'Remote') ?></span></div>
                    <?php if (!empty($job['department'])): ?>
                    <div class="jd-info-row"><span>Department</span><span><?= htmlspecialchars($job['department']) ?></span></div>
                    <?php endif; ?>
                    <?php if (!empty($job['application_deadline'])): ?>
                    <div class="jd-info-row"><span>Deadline</span><span><?= date('M j, Y', strtotime($job['application_deadline'])) ?></span></div>
                    <?php endif; ?>
                </div>

                <div class="jd-company-card">
                    <?php if ($companyLogo): ?>
                        <img class="jd-company-card-logo" src="<?= htmlspecialchars($companyLogo) ?>" alt="<?= $company ?>" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                        <div class="jd-company-card-init" style="display:none"><?= $companyInit ?></div>
                    <?php else: ?>
                        <div class="jd-company-card-init"><?= $companyInit ?></div>
                    <?php endif; ?>
                    <h4><?= $company ?></h4>
                    <p><?= htmlspecialchars($job['company_industry'] ?? 'Technology') ?> · <?= htmlspecialchars($job['company_size'] ?? '') ?> employees</p>
                    <?php if (!empty($job['company_website'])): ?>
                        <a href="<?= htmlspecialchars($job['company_website']) ?>" target="_blank" class="jd-company-link">Visit Website →</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Apply Modal -->
    <?php if (isLoggedIn() && getCurrentUserRole() === 'employee' && !$alreadyApplied): ?>
    <div class="jd-modal-overlay" id="applyModal">
        <div class="jd-modal" style="position:relative">
            <button class="jd-modal-close" id="closeApplyModal"><span class="material-symbols-outlined">close</span></button>
            <h2 class="jd-modal-title">Apply to <?= htmlspecialchars($job['title']) ?></h2>
            <p class="jd-modal-sub">at <?= $company ?></p>

            <form action="/action/employee.applications.apply" method="POST" enctype="multipart/form-data">
                <?= csrfField() ?>
                <input type="hidden" name="job_id" value="<?= $jobId ?>">

                <!-- Resume Upload -->
                <div class="jd-form-group">
                    <label class="jd-form-label">Resume / CV</label>
                    <label class="jd-upload-area" id="uploadArea">
                        <input type="file" name="resume" id="resumeInput" accept=".pdf,.doc,.docx" style="display:none">
                        <span class="material-symbols-outlined jd-upload-icon">cloud_upload</span>
                        <p class="jd-upload-text"><strong>Click to upload</strong> or drag & drop<br>PDF, DOC, DOCX (max 5MB)</p>
                        <p class="jd-upload-filename" id="fileName" style="display:none"></p>
                    </label>
                    <p class="jd-form-hint">Upload your latest resume to stand out</p>
                </div>

                <!-- Cover Letter -->
                <div class="jd-form-group">
                    <label class="jd-form-label">Cover Letter</label>
                    <textarea class="jd-form-textarea" name="cover_letter" rows="5" placeholder="Tell the employer why you're a great fit for this role..."></textarea>
                    <p class="jd-form-hint">A personalized cover letter can increase your chances significantly</p>
                </div>

                <!-- Screening Questions (if company has requirements) -->
                <?php if (!empty($job['requirements'])): ?>
                <div class="jd-form-group">
                    <label class="jd-form-label">Screening Questions</label>
                    <p style="font-size:0.8rem;color:#7e766e;margin:0 0 0.75rem;">Please answer the following to help the employer evaluate your fit:</p>
                    <?php 
                    $reqs = array_filter(array_map('trim', explode("\n", $job['requirements'])));
                    foreach (array_slice($reqs, 0, 3) as $i => $req): ?>
                        <div style="margin-bottom:1rem;">
                            <p style="font-size:0.8rem;font-weight:600;color:#170f07;margin:0 0 0.5rem;"><?= ($i+1) ?>. <?= htmlspecialchars($req) ?></p>
                            <textarea class="jd-form-textarea" name="screening_answers[]" rows="2" placeholder="Your answer..." style="min-height:60px;"></textarea>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <div class="jd-modal-actions">
                    <button type="button" class="jd-modal-cancel" id="cancelApply">Cancel</button>
                    <button type="submit" class="jd-modal-submit">Submit Application</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <script>
    // Apply modal
    const openBtn = document.getElementById('openApplyModal');
    const modal = document.getElementById('applyModal');
    const closeBtn = document.getElementById('closeApplyModal');
    const cancelBtn = document.getElementById('cancelApply');

    if (openBtn && modal) {
        openBtn.addEventListener('click', () => modal.classList.add('active'));
        closeBtn.addEventListener('click', () => modal.classList.remove('active'));
        cancelBtn.addEventListener('click', () => modal.classList.remove('active'));
        modal.addEventListener('click', (e) => { if (e.target === modal) modal.classList.remove('active'); });
    }

    // File upload display
    const resumeInput = document.getElementById('resumeInput');
    const uploadArea = document.getElementById('uploadArea');
    const fileName = document.getElementById('fileName');
    if (resumeInput) {
        resumeInput.addEventListener('change', () => {
            if (resumeInput.files.length > 0) {
                fileName.textContent = '📄 ' + resumeInput.files[0].name;
                fileName.style.display = 'block';
                uploadArea.classList.add('has-file');
            }
        });
    }

    // Scroll header
    const header = document.querySelector('.js-topbar');
    if (header) window.addEventListener('scroll', () => header.classList.toggle('scrolled', window.scrollY > 40));
    </script>
</body>
</html>
