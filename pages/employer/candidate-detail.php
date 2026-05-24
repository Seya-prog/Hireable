<?php
require_once __DIR__ . '/../../backend/helpers/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../database/repositories/ApplicationRepository.php';
require_once __DIR__ . '/../../database/repositories/UserRepository.php';
require_once __DIR__ . '/../../database/repositories/AssessmentRepository.php';

if (!isLoggedIn() || getCurrentUserRole() !== 'employer') {
    header('Location: ' . AUTH_URL . 'login.php');
    exit;
}

$appId = intval($_GET['id'] ?? 0);
if (!$appId) { header('Location: ../employer/candidates.php'); exit; }

$employerId = getCurrentUserId();
$appRepo    = new ApplicationRepository($pdo);
$userRepo   = new UserRepository($pdo);
$assessRepo = new AssessmentRepository($pdo);

// Verify this application belongs to the employer
$application = $appRepo->findForEmployer($appId, $employerId);
if (!$application) { header('Location: ../employer/candidates.php'); exit; }

// Get full application data
$appDetail = $appRepo->findById($appId);
$candidate = $userRepo->findById($appDetail['employee_id']);
$skills    = $userRepo->getSkills($appDetail['employee_id']);
$experience = $userRepo->getExperience($appDetail['employee_id']);
$education  = $userRepo->getEducation($appDetail['employee_id']);

$candidateName = htmlspecialchars($candidate['first_name'] . ' ' . substr($candidate['last_name'],0,1) . '.');
$daysAgo = floor((time() - strtotime($appDetail['applied_at'])) / 86400);
$appliedAgo = $daysAgo === 0 ? 'Today' : ($daysAgo === 1 ? 'Yesterday' : $daysAgo . ' days ago');
$yrsExp = (int)($candidate['years_of_experience'] ?? 0);

// Pipeline status mapping
$pipelineStages = ['applied', 'screening', 'interview', 'offer'];
$currentStageIdx = array_search($appDetail['status'], $pipelineStages);
if ($currentStageIdx === false) $currentStageIdx = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php $pageTitle = $candidateName . ' | Hireable Employer'; ?>
    <?php $pageCss = ['employer.css', 'toast.css'];
    include __DIR__ . '/../../components/shared/head.php'; ?>
</head>
<body class="dash-page">
    <?php $activePage = 'candidates'; ?>
    <?php include __DIR__ . '/../../components/employer/employer-sidebar.php'; ?>

    <main class="dash-main" style="margin-left: 260px;">
        <div class="emp-header">
            <div>
                <a href="../employer/candidates.php" class="emp-back-link">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Back to Candidates
                </a>
                <h2 class="page-title"><?= $candidateName ?></h2>
                <p class="page-subtitle">Applied for <?= htmlspecialchars($appDetail['job_title']) ?> • <?= $appliedAgo ?></p>
            </div>
            <div class="emp-header-actions">
                <form method="POST" action="/action/employer.applications.reject" style="display:inline;">
                    <input type="hidden" name="application_id" value="<?= $appId ?>">
                    <button class="assess-save-btn assess-save-btn--draft">Reject</button>
                </form>
                <a href="../employer/interview-schedule.php?app_id=<?= $appId ?>" class="assess-save-btn assess-save-btn--publish" style="text-decoration:none;">Schedule Interview</a>
            </div>
        </div>

        <div class="emp-detail-layout">
            <div class="emp-detail-main">
                <!-- Stats -->
                <div class="emp-detail-stats">
                    <div class="emp-detail-stat-card">
                        <span class="emp-detail-stat-value"><?= $yrsExp ?> yrs</span>
                        <span class="emp-detail-stat-label">Experience</span>
                    </div>
                    <div class="emp-detail-stat-card">
                        <span class="emp-detail-stat-value"><?= count($skills) ?></span>
                        <span class="emp-detail-stat-label">Skills</span>
                    </div>
                    <div class="emp-detail-stat-card">
                        <span class="emp-detail-stat-value"><?= count($experience) ?></span>
                        <span class="emp-detail-stat-label">Positions Held</span>
                    </div>
                    <div class="emp-detail-stat-card">
                        <span class="emp-detail-stat-value"><?= count($education) ?></span>
                        <span class="emp-detail-stat-label">Degrees</span>
                    </div>
                </div>

                <!-- About -->
                <section class="emp-candidate-section">
                    <h3 class="emp-section-title">About</h3>
                    <?php if (!empty($candidate['bio'])): ?>
                        <p class="emp-candidate-bio"><?= nl2br(htmlspecialchars($candidate['bio'])) ?></p>
                    <?php elseif (!empty($candidate['headline'])): ?>
                        <p class="emp-candidate-bio"><?= htmlspecialchars($candidate['headline']) ?></p>
                    <?php else: ?>
                        <p class="emp-candidate-bio" style="color:#7a6b5a;">No bio provided.</p>
                    <?php endif; ?>
                </section>

                <!-- Experience -->
                <section class="emp-candidate-section">
                    <h3 class="emp-section-title">Experience</h3>
                    <?php if (empty($experience)): ?>
                        <p style="color:#7a6b5a;">No experience listed.</p>
                    <?php else: ?>
                        <div class="emp-timeline">
                            <?php foreach ($experience as $exp):
                                $startYear = date('Y', strtotime($exp['start_date']));
                                $endYear = $exp['is_current'] ? 'Present' : ($exp['end_date'] ? date('Y', strtotime($exp['end_date'])) : '');
                                $duration = '';
                                if ($exp['end_date'] || $exp['is_current']) {
                                    $end = $exp['is_current'] ? time() : strtotime($exp['end_date']);
                                    $years = round((($end - strtotime($exp['start_date'])) / (365.25 * 86400)), 0);
                                    $duration = " • {$years} year" . ($years != 1 ? 's' : '');
                                }
                            ?>
                            <div class="emp-timeline-item">
                                <div class="emp-timeline-dot"></div>
                                <div class="emp-timeline-content">
                                    <h4 class="emp-timeline-title"><?= htmlspecialchars($exp['job_title']) ?></h4>
                                    <p class="emp-timeline-company"><?= htmlspecialchars($exp['company']) ?></p>
                                    <p class="emp-timeline-period"><?= $startYear ?> – <?= $endYear ?><?= $duration ?></p>
                                    <?php if (!empty($exp['description'])): ?>
                                        <p class="emp-timeline-desc"><?= htmlspecialchars($exp['description']) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>

                <!-- Education -->
                <section class="emp-candidate-section">
                    <h3 class="emp-section-title">Education</h3>
                    <?php if (empty($education)): ?>
                        <p style="color:#7a6b5a;">No education listed.</p>
                    <?php else: ?>
                        <div class="emp-timeline">
                            <?php foreach ($education as $edu): ?>
                            <div class="emp-timeline-item">
                                <div class="emp-timeline-dot"></div>
                                <div class="emp-timeline-content">
                                    <h4 class="emp-timeline-title"><?= htmlspecialchars($edu['degree']) ?><?= !empty($edu['field_of_study']) ? ', ' . htmlspecialchars($edu['field_of_study']) : '' ?></h4>
                                    <p class="emp-timeline-company"><?= htmlspecialchars($edu['institution']) ?></p>
                                    <p class="emp-timeline-period"><?= $edu['start_date'] ? date('Y', strtotime($edu['start_date'])) : '' ?> – <?= $edu['end_date'] ? date('Y', strtotime($edu['end_date'])) : 'Present' ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>
            </div>

            <!-- Sidebar -->
            <div class="emp-detail-sidebar">
                <section class="emp-detail-panel">
                    <h4 class="emp-section-title emp-section-title--sm">Contact</h4>
                    <div class="emp-detail-info">
                        <div class="emp-detail-row"><span>Email</span><span><?= htmlspecialchars($candidate['email']) ?></span></div>
                        <?php if (!empty($candidate['phone'])): ?>
                        <div class="emp-detail-row"><span>Phone</span><span><?= htmlspecialchars($candidate['phone']) ?></span></div>
                        <?php endif; ?>
                        <?php if (!empty($candidate['city']) || !empty($candidate['country'])): ?>
                        <div class="emp-detail-row"><span>Location</span><span><?= htmlspecialchars(trim(($candidate['city'] ?? '') . ', ' . ($candidate['country'] ?? ''), ', ')) ?></span></div>
                        <?php endif; ?>
                        <?php if (!empty($candidate['linkedin_url'])): ?>
                        <div class="emp-detail-row"><span>LinkedIn</span><span><a href="<?= htmlspecialchars($candidate['linkedin_url']) ?>" target="_blank">View Profile</a></span></div>
                        <?php endif; ?>
                    </div>
                </section>

                <section class="emp-detail-panel">
                    <h4 class="emp-section-title emp-section-title--sm">Skills</h4>
                    <div class="emp-cand-skills">
                        <?php if (empty($skills)): ?>
                            <p style="color:#7a6b5a; font-size:0.85rem;">No skills listed.</p>
                        <?php else: ?>
                            <?php foreach ($skills as $s): ?>
                                <span class="emp-cand-skill-tag"><?= htmlspecialchars($s['skill_name']) ?></span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </section>

                <section class="emp-detail-panel">
                    <h4 class="emp-section-title emp-section-title--sm">Pipeline Stage</h4>
                    <div class="emp-pipeline-tracker">
                        <?php foreach ($pipelineStages as $i => $stage):
                            $icon = $i < $currentStageIdx ? 'check_circle' : ($i === $currentStageIdx ? 'radio_button_checked' : 'radio_button_unchecked');
                            $class = $i < $currentStageIdx ? 'emp-pipeline-step--done' : ($i === $currentStageIdx ? 'emp-pipeline-step--current' : '');
                        ?>
                        <div class="emp-pipeline-step <?= $class ?>"><span class="material-symbols-outlined"><?= $icon ?></span> <?= ucfirst($stage) ?></div>
                        <?php endforeach; ?>
                    </div>
                </section>

                <?php if (!empty($appDetail['cover_letter'])): ?>
                <section class="emp-detail-panel">
                    <h4 class="emp-section-title emp-section-title--sm">Cover Letter</h4>
                    <p style="font-size: 0.85rem; color: #4d453f; line-height: 1.7;"><?= nl2br(htmlspecialchars($appDetail['cover_letter'])) ?></p>
                </section>
                <?php endif; ?>

                <section class="emp-detail-panel">
                    <h4 class="emp-section-title emp-section-title--sm">Resume / CV</h4>
                    <?php if (!empty($appDetail['resume_path'])): ?>
                        <div style="display:flex; align-items:center; gap:0.75rem; padding:1rem; background:#f4eedb; border-radius:10px; flex-wrap:wrap;">
                            <span class="material-symbols-outlined" style="font-size:2rem; color:#695d46;">description</span>
                            <div style="flex:1; min-width:0;">
                                <p style="margin:0; font-weight:600; font-size:0.85rem; color:#170f07; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><?= basename($appDetail['resume_path']) ?></p>
                                <p style="margin:0.25rem 0 0; font-size:0.75rem; color:#7e766e;">Uploaded with application</p>
                            </div>
                            <a href="<?= htmlspecialchars($appDetail['resume_path']) ?>" download style="display:inline-flex; align-items:center; gap:0.35rem; padding:0.5rem 0.85rem; background:#170f07; color:#fff; border-radius:8px; font-size:0.7rem; font-weight:700; text-decoration:none; white-space:nowrap; flex-shrink:0;">
                                <span class="material-symbols-outlined" style="font-size:0.95rem;">download</span> Download
                            </a>
                        </div>
                    <?php elseif (!empty($candidate['resume_path'])): ?>
                        <div style="display:flex; align-items:center; gap:0.75rem; padding:1rem; background:#f4eedb; border-radius:10px;">
                            <span class="material-symbols-outlined" style="font-size:2rem; color:#695d46;">description</span>
                            <div style="flex:1;">
                                <p style="margin:0; font-weight:600; font-size:0.85rem; color:#170f07;"><?= basename($candidate['resume_path']) ?></p>
                                <p style="margin:0.25rem 0 0; font-size:0.75rem; color:#7e766e;">From candidate profile</p>
                            </div>
                            <a href="<?= htmlspecialchars($candidate['resume_path']) ?>" class="emp-quick-btn" download style="margin:0; padding:0.5rem 1rem; font-size:0.75rem;">
                                <span class="material-symbols-outlined" style="font-size:1rem;">download</span> Download
                            </a>
                        </div>
                    <?php else: ?>
                        <p style="color:#7a6b5a; font-size:0.85rem;">No resume uploaded.</p>
                    <?php endif; ?>
                </section>

                <section class="emp-detail-panel">
                    <h4 class="emp-section-title emp-section-title--sm">Actions</h4>
                    <a href="../employer/interview-schedule.php?app_id=<?= $appId ?>" class="emp-quick-btn"><span class="material-symbols-outlined">calendar_month</span> Schedule Interview</a>
                </section>
            </div>
        </div>
    </main>
</body>
</html>
