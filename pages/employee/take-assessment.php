<?php
/**
 * Take Assessment — Proctored exam page
 * 
 * Entry: ?id={assessment_id}  (starts new attempt)
 *    or: ?attempt={attempt_id} (resumes existing)
 */
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../backend/helpers/session.php';
require_once __DIR__ . '/../../backend/helpers/validation.php';
require_once __DIR__ . '/../../backend/helpers/csrf.php';
require_once __DIR__ . '/../../backend/helpers/device_lock.php';
require_once __DIR__ . '/../../database/repositories/AssessmentRepository.php';

// Auth guard
if (!isLoggedIn() || getCurrentUserRole() !== 'employee') {
    header('Location: ' . AUTH_URL . 'login.php');
    exit;
}

$assessmentRepo = new AssessmentRepository($pdo);
$employeeId = getCurrentUserId();

// Determine assessment
$assessmentId = intval($_GET['id'] ?? 0);
$attemptIdParam = intval($_GET['attempt'] ?? 0);

$assessment = null;
$existingAttempt = null;

if ($attemptIdParam) {
    // Resuming — load attempt and its assessment
    $existingAttempt = $assessmentRepo->getAttemptById($attemptIdParam);
    if (!$existingAttempt || $existingAttempt['employee_id'] != $employeeId || $existingAttempt['status'] !== 'in_progress') {
        setFlash('error', 'Invalid or completed assessment attempt.');
        header('Location: ' . SHARED_URL . 'skill-assesment.php');
        exit;
    }
    $assessmentId = $existingAttempt['assessment_id'];
    $assessment = $assessmentRepo->findById($assessmentId);
} elseif ($assessmentId) {
    $assessment = $assessmentRepo->findById($assessmentId);
}

if (!$assessment || $assessment['status'] !== 'active') {
    setFlash('error', 'Assessment not found or inactive.');
    header('Location: ' . SHARED_URL . 'skill-assesment.php');
    exit;
}

// Load questions (WITHOUT answers — safe for client)
$questions = $assessmentRepo->getQuestionsForCandidate($assessmentId);

// Decode options JSON for each question
foreach ($questions as &$q) {
    if (!empty($q['options'])) {
        $q['options_array'] = json_decode($q['options'], true) ?: [];
    } else {
        $q['options_array'] = [];
    }
}
unset($q);

$totalQuestions = count($questions);
$csrfToken = generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($assessment['title']) ?> | Hireable Assessment</title>
    <link rel="stylesheet" href="<?= CSS_URL ?>/global.css">
    <link rel="stylesheet" href="<?= CSS_URL ?>/proctor.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0">

    <!-- TensorFlow.js for face/object detection -->
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@4.17.0/dist/tf.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/coco-ssd@2.2.3/dist/coco-ssd.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/face-landmarks-detection@1.0.5/dist/face-landmarks-detection.min.js" defer></script>
</head>
<body class="proctored-assessment" style="background:#0f172a; color:#f9fafb; margin:0;">

    <!-- ─── Consent Modal (shown first) ──────────────── -->
    <div id="proctorConsent" class="proctor-consent">
        <div class="proctor-consent-card">
            <span class="material-symbols-outlined" style="font-size:48px; color:#f59e0b;">shield</span>
            <h2>Proctored Assessment</h2>
            <p>This assessment is monitored to ensure fairness. By continuing, you agree to:</p>
            <ul class="proctor-consent-list">
                <li><span class="material-symbols-outlined">videocam</span> Webcam access for identity verification</li>
                <li><span class="material-symbols-outlined">fullscreen</span> Fullscreen mode during the entire exam</li>
                <li><span class="material-symbols-outlined">location_on</span> Location verification</li>
                <li><span class="material-symbols-outlined">visibility</span> Tab and window monitoring</li>
                <li><span class="material-symbols-outlined">timer</span> Timed assessment: <?= intval($assessment['time_limit_minutes']) ?> minutes</li>
                <li><span class="material-symbols-outlined">block</span> Copy, paste, and screenshots are disabled</li>
            </ul>
            <div class="proctor-consent-actions">
                <button class="proctor-consent-decline" onclick="window.location.href='<?= SHARED_URL ?>skill-assesment.php'">Decline</button>
                <button class="proctor-consent-accept" id="btnAcceptProctor">Accept & Start</button>
            </div>
        </div>
    </div>

    <!-- ─── Proctor Header Bar ───────────────────────── -->
    <div class="proctor-bar" id="proctorBar" style="display:none;">
        <div class="proctor-bar-left">
            <div>
                <div class="proctor-bar-title"><?= htmlspecialchars($assessment['title']) ?></div>
                <div class="proctor-bar-subtitle"><?= ucfirst($assessment['difficulty'] ?? 'Intermediate') ?> Level • <?= intval($assessment['time_limit_minutes']) ?> min</div>
            </div>
        </div>
        <div class="proctor-bar-right">
            <div class="proctor-webcam-container">
                <div class="proctor-webcam-indicator"></div>
            </div>
            <div class="proctor-timer timer-ok" id="proctorTimer">
                <span class="material-symbols-outlined">timer</span>
                <span class="proctor-timer-text">--:--</span>
            </div>
        </div>
    </div>

    <!-- ─── Progress ─────────────────────────────────── -->
    <div class="proctor-progress" id="proctorProgress" style="display:none;">
        <div class="proctor-progress-info">
            <span id="progressLabel">Question 1 of <?= $totalQuestions ?></span>
            <span id="progressPercent">0% Complete</span>
        </div>
        <div class="proctor-progress-track">
            <div class="proctor-progress-fill" id="progressFill" style="width:0%"></div>
        </div>
    </div>

    <!-- ─── Main Content ─────────────────────────────── -->
    <div class="proctor-content" id="proctorContent" style="display:none;">
        <div class="proctor-question-area">
            <?php foreach ($questions as $idx => $q): ?>
            <div class="proctor-question-card" data-question-id="<?= $q['id'] ?>" data-index="<?= $idx ?>">
                <div class="proctor-q-header">
                    <span class="proctor-q-num">Question <?= $idx + 1 ?></span>
                    <span class="proctor-q-type"><?= ucfirst(str_replace('_', ' ', $q['question_type'])) ?></span>
                    <span class="proctor-q-points"><?= intval($q['points']) ?> Points</span>
                </div>
                <div class="proctor-q-text"><?= htmlspecialchars($q['question_text']) ?></div>

                <?php if ($q['question_type'] === 'multiple_choice' && !empty($q['options_array'])): ?>
                    <div class="proctor-options">
                        <?php foreach ($q['options_array'] as $optIdx => $option): 
                            $letter = chr(65 + $optIdx);
                            $optValue = is_array($option) ? ($option['value'] ?? $option['text'] ?? '') : $option;
                            $optLabel = is_array($option) ? ($option['text'] ?? $option['value'] ?? '') : $option;
                        ?>
                        <label class="proctor-option" data-value="<?= htmlspecialchars($optValue) ?>">
                            <input type="radio" name="q_<?= $q['id'] ?>" value="<?= htmlspecialchars($optValue) ?>">
                            <span class="proctor-option-letter"><?= $letter ?></span>
                            <span class="proctor-option-text"><?= htmlspecialchars($optLabel) ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                <?php elseif ($q['question_type'] === 'code'): ?>
                    <textarea class="proctor-code-input" name="q_<?= $q['id'] ?>" placeholder="Write your code here..."></textarea>
                <?php else: ?>
                    <textarea class="proctor-text-input" name="q_<?= $q['id'] ?>" placeholder="Type your answer here..."></textarea>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>

            <!-- Navigation -->
            <div class="proctor-nav">
                <button class="proctor-nav-btn" id="btnPrev" disabled>
                    <span class="material-symbols-outlined">arrow_back</span> Previous
                </button>
                <div class="proctor-nav-dots" id="navDots">
                    <?php for ($i = 0; $i < $totalQuestions; $i++): ?>
                        <span class="proctor-dot<?= $i === 0 ? ' current' : '' ?>" data-index="<?= $i ?>"></span>
                    <?php endfor; ?>
                </div>
                <button class="proctor-nav-btn" id="btnNext">
                    Next <span class="material-symbols-outlined">arrow_forward</span>
                </button>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="proctor-sidebar">
            <div class="proctor-overview">
                <div class="proctor-overview-title">Question Overview</div>
                <div class="proctor-q-grid" id="qGrid">
                    <?php for ($i = 0; $i < $totalQuestions; $i++): ?>
                        <span class="proctor-q-box<?= $i === 0 ? ' current' : '' ?>" data-index="<?= $i ?>"><?= $i + 1 ?></span>
                    <?php endfor; ?>
                </div>
            </div>
            <button class="proctor-submit-btn" id="btnSubmit">
                <span class="material-symbols-outlined">check_circle</span>
                Submit Assessment
            </button>
        </div>
    </div>

    <!-- Hidden data -->
    <input type="hidden" id="assessmentId" value="<?= $assessmentId ?>">
    <input type="hidden" id="totalQuestions" value="<?= $totalQuestions ?>">
    <input type="hidden" id="csrfToken" value="<?= htmlspecialchars($csrfToken) ?>">

    <!-- Proctor JS -->
    <script src="<?= JS_URL ?>/assessment-proctor.js"></script>
    <script>
    (function() {
        const assessmentId = <?= $assessmentId ?>;
        const totalQuestions = <?= $totalQuestions ?>;
        const questionCards = document.querySelectorAll('.proctor-question-card');
        const dots = document.querySelectorAll('.proctor-dot');
        const qBoxes = document.querySelectorAll('.proctor-q-box');
        const answeredSet = new Set();
        let currentIndex = 0;
        let attemptId = null;

        // ─── Consent Flow ───────────────────────────
        document.getElementById('btnAcceptProctor').addEventListener('click', async () => {
            document.getElementById('proctorConsent').style.display = 'none';
            document.getElementById('proctorBar').style.display = 'flex';
            document.getElementById('proctorProgress').style.display = 'block';
            document.getElementById('proctorContent').style.display = 'flex';

            // Request fullscreen
            Proctor.requestFullscreen();

            // Initialize webcam
            await Proctor.initWebcam();

            // Get geo position
            let geoLat = null, geoLng = null;
            try {
                const pos = await new Promise((resolve, reject) => {
                    navigator.geolocation.getCurrentPosition(resolve, reject, { timeout: 10000 });
                });
                geoLat = pos.coords.latitude;
                geoLng = pos.coords.longitude;
            } catch(e) {}

            // Browser fingerprint
            const fp = [
                screen.width, screen.height, screen.colorDepth,
                Intl.DateTimeFormat().resolvedOptions().timeZone,
                navigator.language, navigator.hardwareConcurrency
            ].join('|');
            const fingerprint = await hashString(fp);

            // Start attempt via API
            try {
                const resp = await fetch('/api/assessment/start', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        assessment_id: assessmentId,
                        browser_fingerprint: fingerprint,
                        geo_lat: geoLat,
                        geo_lng: geoLng,
                    }),
                    credentials: 'same-origin',
                });

                const data = await resp.json();
                if (!data.success) {
                    alert(data.error || 'Failed to start assessment');
                    window.location.href = '<?= SHARED_URL ?>skill-assesment.php';
                    return;
                }

                attemptId = data.attempt_id;

                // Restore saved answers if resuming
                if (data.saved_answers) {
                    data.saved_answers.forEach(sa => {
                        const card = document.querySelector(`[data-question-id="${sa.question_id}"]`);
                        if (card) {
                            const radio = card.querySelector(`input[value="${sa.answer_text}"]`);
                            if (radio) {
                                radio.checked = true;
                                radio.closest('.proctor-option').classList.add('selected');
                            }
                            const textarea = card.querySelector('textarea');
                            if (textarea) textarea.value = sa.answer_text;
                            answeredSet.add(sa.question_id.toString());
                        }
                    });
                    updateUI();
                }

                // Init proctor
                Proctor.init({
                    attemptId: attemptId,
                    assessmentId: assessmentId,
                    remainingSeconds: data.remaining_seconds,
                });

            } catch(e) {
                alert('Network error. Please check your connection.');
                window.location.href = '<?= SHARED_URL ?>skill-assesment.php';
            }
        });

        // ─── Question Navigation ────────────────────
        function showQuestion(index) {
            // Save current answer before navigating
            if (attemptId) saveCurrentAnswer();

            currentIndex = index;
            questionCards.forEach((c, i) => {
                c.classList.toggle('active', i === index);
            });
            dots.forEach((d, i) => {
                d.classList.toggle('current', i === index);
            });
            qBoxes.forEach((b, i) => {
                b.classList.toggle('current', i === index);
            });

            document.getElementById('btnPrev').disabled = index === 0;
            document.getElementById('btnNext').textContent = index === totalQuestions - 1 ? 'Finish' : '';
            document.getElementById('btnNext').innerHTML = index === totalQuestions - 1
                ? 'Finish <span class="material-symbols-outlined">done_all</span>'
                : 'Next <span class="material-symbols-outlined">arrow_forward</span>';

            const pct = Math.round(((answeredSet.size) / totalQuestions) * 100);
            document.getElementById('progressLabel').textContent = `Question ${index + 1} of ${totalQuestions}`;
            document.getElementById('progressPercent').textContent = `${pct}% Complete`;
            document.getElementById('progressFill').style.width = `${pct}%`;
        }

        document.getElementById('btnNext').addEventListener('click', () => {
            if (currentIndex < totalQuestions - 1) showQuestion(currentIndex + 1);
        });

        document.getElementById('btnPrev').addEventListener('click', () => {
            if (currentIndex > 0) showQuestion(currentIndex - 1);
        });

        // Click on sidebar grid to jump
        qBoxes.forEach(box => {
            box.addEventListener('click', () => showQuestion(parseInt(box.dataset.index)));
        });

        // ─── Answer Selection ───────────────────────
        document.querySelectorAll('.proctor-option').forEach(opt => {
            opt.addEventListener('click', () => {
                const card = opt.closest('.proctor-question-card');
                card.querySelectorAll('.proctor-option').forEach(o => o.classList.remove('selected'));
                opt.classList.add('selected');
                opt.querySelector('input').checked = true;

                const qId = card.dataset.questionId;
                answeredSet.add(qId);
                updateUI();
            });
        });

        // Textarea changes
        document.querySelectorAll('.proctor-code-input, .proctor-text-input').forEach(ta => {
            ta.addEventListener('input', () => {
                const qId = ta.closest('.proctor-question-card').dataset.questionId;
                if (ta.value.trim()) answeredSet.add(qId);
                else answeredSet.delete(qId);
                updateUI();
            });
        });

        function updateUI() {
            qBoxes.forEach((box, i) => {
                const card = questionCards[i];
                const qId = card.dataset.questionId;
                box.classList.toggle('done', answeredSet.has(qId));
            });
            dots.forEach((dot, i) => {
                const card = questionCards[i];
                const qId = card.dataset.questionId;
                dot.classList.toggle('done', answeredSet.has(qId));
            });
        }

        // ─── Auto-Save ─────────────────────────────
        function saveCurrentAnswer() {
            const card = questionCards[currentIndex];
            if (!card || !attemptId) return;

            const qId = card.dataset.questionId;
            const radio = card.querySelector('input[type="radio"]:checked');
            const textarea = card.querySelector('textarea');
            let answer = '';

            if (radio) answer = radio.value;
            else if (textarea) answer = textarea.value;

            if (answer) {
                Proctor.saveAnswer(parseInt(qId), answer);
            }
        }

        // ─── Submit ─────────────────────────────────
        document.getElementById('btnSubmit').addEventListener('click', () => {
            saveCurrentAnswer();
            const unanswered = totalQuestions - answeredSet.size;
            showSubmitModal(unanswered);
        });

        // ─── Custom Submit Modal ────────────────────
        function showSubmitModal(unanswered) {
            // Remove existing modal if any
            const existing = document.getElementById('submitModal');
            if (existing) existing.remove();

            const modal = document.createElement('div');
            modal.id = 'submitModal';
            modal.style.cssText = 'position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);';

            const warnHtml = unanswered > 0
                ? `<div style="display:flex;align-items:center;gap:8px;padding:0.7rem 1rem;background:rgba(245,158,11,0.1);border:1px solid rgba(245,158,11,0.25);border-radius:8px;margin-bottom:1rem;">
                     <span class="material-symbols-outlined" style="color:#f59e0b;font-size:20px;">warning</span>
                     <span style="font-size:0.85rem;color:#fbbf24;">You have <strong>${unanswered}</strong> unanswered question${unanswered > 1 ? 's' : ''}.</span>
                   </div>`
                : `<div style="display:flex;align-items:center;gap:8px;padding:0.7rem 1rem;background:rgba(34,197,94,0.08);border:1px solid rgba(34,197,94,0.2);border-radius:8px;margin-bottom:1rem;">
                     <span class="material-symbols-outlined" style="color:#22c55e;font-size:20px;">check_circle</span>
                     <span style="font-size:0.85rem;color:#86efac;">All questions answered!</span>
                   </div>`;

            modal.innerHTML = `
                <div style="background:#1e293b;border:1px solid rgba(255,255,255,0.08);border-radius:16px;padding:2rem;max-width:400px;width:90%;text-align:center;box-shadow:0 24px 48px rgba(0,0,0,0.4);">
                    <span class="material-symbols-outlined" style="font-size:48px;color:#f59e0b;margin-bottom:0.75rem;display:block;">assignment_turned_in</span>
                    <h3 style="font-size:1.2rem;font-weight:700;color:#f9fafb;margin:0 0 0.4rem;">Submit Assessment?</h3>
                    <p style="font-size:0.85rem;color:#94a3b8;margin:0 0 1.25rem;line-height:1.5;">Once submitted, you cannot change your answers. This action is final.</p>
                    ${warnHtml}
                    <div style="display:flex;gap:0.75rem;justify-content:center;margin-top:1.25rem;">
                        <button id="submitModalCancel" style="padding:0.6rem 1.5rem;border-radius:8px;border:1px solid rgba(255,255,255,0.12);background:transparent;color:#94a3b8;font-weight:600;font-size:0.85rem;cursor:pointer;transition:all 0.2s;">Cancel</button>
                        <button id="submitModalConfirm" style="padding:0.6rem 1.5rem;border-radius:8px;border:none;background:#f59e0b;color:#0f172a;font-weight:700;font-size:0.85rem;cursor:pointer;transition:all 0.2s;">Submit Now</button>
                    </div>
                </div>`;

            document.body.appendChild(modal);

            document.getElementById('submitModalCancel').addEventListener('click', () => modal.remove());
            document.getElementById('submitModalConfirm').addEventListener('click', () => {
                modal.remove();
                Proctor.autoSubmit('Candidate submitted');
            });
            modal.addEventListener('click', (e) => { if (e.target === modal) modal.remove(); });
        }

        // ─── Helpers ────────────────────────────────
        async function hashString(str) {
            const encoder = new TextEncoder();
            const data = encoder.encode(str);
            const hashBuffer = await crypto.subtle.digest('SHA-256', data);
            const hashArray = Array.from(new Uint8Array(hashBuffer));
            return hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
        }

        // Show first question
        showQuestion(0);
    })();
    </script>
</body>
</html>
