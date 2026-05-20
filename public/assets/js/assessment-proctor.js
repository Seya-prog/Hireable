/**
 * Assessment Proctor — Client-side anti-cheat system
 * 
 * Layers:
 *  1. Server-synced countdown timer
 *  2. Fullscreen enforcement
 *  3. Tab/window switch detection
 *  4. Clipboard, keyboard shortcut, and context menu blocking
 *  5. Mouse behavioral analysis (distance, dead zones, quadrants)
 *  6. Webcam face/head/object monitoring (TensorFlow.js)
 *  7. Periodic heartbeat with violation reporting
 *  8. Auto-save answers on navigation
 */

const Proctor = (() => {
    // ─── State ──────────────────────────────────────────
    let attemptId = 0;
    let assessmentId = 0;
    let deadline = 0; // Unix timestamp (seconds)
    let timerInterval = null;
    let heartbeatInterval = null;
    let isSubmitting = false;

    // Violation buffer (sent with each heartbeat)
    const violationBuffer = [];

    // Counters
    const counters = {
        tabSwitches: 0,
        fullscreenExits: 0,
        mouseDistance: 0,
        quadrantVisits: { tl: 0, tr: 0, bl: 0, br: 0 },
    };

    // Mouse tracking
    let lastMouseX = 0, lastMouseY = 0;
    let mouseIdleSince = Date.now();
    let lastMouseMoveTime = Date.now();

    // Webcam
    let videoStream = null;
    let videoEl = null;
    let faceModel = null;
    let objectModel = null;
    let snapshotInterval = null;
    let detectionInterval = null;

    // ─── Init ───────────────────────────────────────────
    function init(config) {
        attemptId = config.attemptId;
        assessmentId = config.assessmentId;
        deadline = Math.floor(Date.now() / 1000) + config.remainingSeconds;

        startTimer();
        enforceFullscreen();
        watchTabSwitches();
        blockClipboard();
        blockKeyboardShortcuts();
        blockContextMenu();
        trackMouse();
        startHeartbeat();

        // Webcam is initialized separately after user consent
        console.log('[Proctor] Initialized for attempt', attemptId);
    }

    // ─── Timer ──────────────────────────────────────────
    function startTimer() {
        const timerEl = document.querySelector('.proctor-timer-text');
        const timerContainer = document.querySelector('.proctor-timer');

        timerInterval = setInterval(() => {
            const remaining = Math.max(0, deadline - Math.floor(Date.now() / 1000));
            const mins = Math.floor(remaining / 60);
            const secs = remaining % 60;

            if (timerEl) {
                timerEl.textContent = `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
            }

            // Color urgency
            if (timerContainer) {
                timerContainer.classList.remove('timer-ok', 'timer-warn', 'timer-danger');
                if (remaining > 300) timerContainer.classList.add('timer-ok');
                else if (remaining > 60) timerContainer.classList.add('timer-warn');
                else timerContainer.classList.add('timer-danger');
            }

            if (remaining <= 0) {
                clearInterval(timerInterval);
                autoSubmit('Time expired');
            }
        }, 1000);
    }

    // ─── Fullscreen ─────────────────────────────────────
    function enforceFullscreen() {
        document.addEventListener('fullscreenchange', () => {
            if (!document.fullscreenElement && !isSubmitting) {
                counters.fullscreenExits++;
                addViolation('fullscreen_exit', 'Exited fullscreen');
                showWarning('Please return to fullscreen to continue your assessment.');
                showFullscreenOverlay();
            }
        });
    }

    function requestFullscreen() {
        const el = document.documentElement;
        if (el.requestFullscreen) el.requestFullscreen();
        else if (el.webkitRequestFullscreen) el.webkitRequestFullscreen();
        else if (el.msRequestFullscreen) el.msRequestFullscreen();
        hideFullscreenOverlay();
    }

    function showFullscreenOverlay() {
        let overlay = document.getElementById('proctor-fs-overlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.id = 'proctor-fs-overlay';
            overlay.className = 'proctor-overlay';
            overlay.innerHTML = `
                <div class="proctor-overlay-content">
                    <span class="material-symbols-outlined" style="font-size:48px;color:#f59e0b">fullscreen</span>
                    <h3>Fullscreen Required</h3>
                    <p>You must remain in fullscreen mode during the assessment.</p>
                    <button onclick="Proctor.requestFullscreen()" class="proctor-btn">Return to Fullscreen</button>
                </div>
            `;
            document.body.appendChild(overlay);
        }
        overlay.style.display = 'flex';
    }

    function hideFullscreenOverlay() {
        const overlay = document.getElementById('proctor-fs-overlay');
        if (overlay) overlay.style.display = 'none';
    }

    // ─── Tab Switch Detection ───────────────────────────
    function watchTabSwitches() {
        document.addEventListener('visibilitychange', () => {
            if (document.hidden && !isSubmitting) {
                counters.tabSwitches++;
                addViolation('tab_switch', 'Tab became hidden');
                showWarning(`Tab switch detected (${counters.tabSwitches}). This is being recorded.`);
            }
        });

        window.addEventListener('blur', () => {
            if (!isSubmitting) {
                addViolation('tab_switch', 'Window lost focus');
            }
        });

        window.addEventListener('beforeunload', (e) => {
            if (!isSubmitting) {
                e.preventDefault();
                e.returnValue = 'Your assessment is in progress. Are you sure you want to leave?';
            }
        });
    }

    // ─── Clipboard Block ────────────────────────────────
    function blockClipboard() {
        const area = document.querySelector('.take-assess-content') || document.body;
        ['copy', 'cut', 'paste'].forEach(evt => {
            area.addEventListener(evt, (e) => {
                e.preventDefault();
                addViolation('clipboard', `${evt} attempted`);
                showWarning('Copy/paste is disabled during the assessment.');
            });
        });
    }

    // ─── Keyboard Shortcuts Block ───────────────────────
    function blockKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            // Block: Ctrl+C, Ctrl+V, Ctrl+U, Ctrl+S, Ctrl+Shift+I/J, F12
            const blocked = (
                (e.ctrlKey && ['c','v','u','s','a'].includes(e.key.toLowerCase())) ||
                (e.ctrlKey && e.shiftKey && ['i','j','c'].includes(e.key.toLowerCase())) ||
                e.key === 'F12'
            );

            if (blocked) {
                e.preventDefault();
                e.stopPropagation();
                addViolation('keyboard_shortcut', `Blocked: ${e.key}`);
            }
        });
    }

    // ─── Context Menu Block ─────────────────────────────
    function blockContextMenu() {
        document.addEventListener('contextmenu', (e) => {
            e.preventDefault();
        });
    }

    // ─── Mouse Tracking ─────────────────────────────────
    function trackMouse() {
        document.addEventListener('mousemove', (e) => {
            const dx = e.clientX - lastMouseX;
            const dy = e.clientY - lastMouseY;
            counters.mouseDistance += Math.sqrt(dx * dx + dy * dy);
            lastMouseX = e.clientX;
            lastMouseY = e.clientY;
            lastMouseMoveTime = Date.now();

            // Track quadrant visits
            const midX = window.innerWidth / 2;
            const midY = window.innerHeight / 2;
            if (e.clientX < midX && e.clientY < midY) counters.quadrantVisits.tl++;
            else if (e.clientX >= midX && e.clientY < midY) counters.quadrantVisits.tr++;
            else if (e.clientX < midX && e.clientY >= midY) counters.quadrantVisits.bl++;
            else counters.quadrantVisits.br++;
        });
    }

    // ─── Heartbeat ──────────────────────────────────────
    function startHeartbeat() {
        heartbeatInterval = setInterval(sendHeartbeat, 30000);
    }

    async function sendHeartbeat() {
        // Check for behavioral anomalies
        checkBehavioralAnomalies();

        const payload = {
            attempt_id: attemptId,
            violations: [...violationBuffer],
        };

        // Add geo if available
        if (navigator.geolocation) {
            try {
                const pos = await new Promise((resolve, reject) => {
                    navigator.geolocation.getCurrentPosition(resolve, reject, { timeout: 5000 });
                });
                payload.geo_lat = pos.coords.latitude;
                payload.geo_lng = pos.coords.longitude;
            } catch (e) { /* Geo not available */ }
        }

        // Clear buffer after copying
        violationBuffer.length = 0;

        try {
            const resp = await fetch('/api/assessment/heartbeat', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
                credentials: 'same-origin',
            });

            const data = await resp.json();

            // Sync timer with server
            if (data.remaining_seconds !== undefined) {
                deadline = Math.floor(Date.now() / 1000) + data.remaining_seconds;
            }

            // Force submit if integrity too low
            if (data.force_submit) {
                autoSubmit('Assessment terminated due to integrity violations');
            }

            if (data.expired) {
                autoSubmit('Time expired');
            }
        } catch (e) {
            console.warn('[Proctor] Heartbeat failed:', e);
        }
    }

    function checkBehavioralAnomalies() {
        // Low mouse movement check
        if (counters.mouseDistance < 50 && Date.now() - lastMouseMoveTime > 20000) {
            addViolation('behavioral', 'Very low mouse movement over extended period');
        }

        // Dead zone check: if any quadrant has <5% of total visits
        const total = Object.values(counters.quadrantVisits).reduce((a, b) => a + b, 0);
        if (total > 100) {
            for (const [quad, visits] of Object.entries(counters.quadrantVisits)) {
                if (visits / total < 0.05) {
                    addViolation('dead_zone', `Quadrant ${quad} has minimal cursor activity`);
                }
            }
        }
    }

    // ─── Webcam Proctoring ──────────────────────────────
    async function initWebcam() {
        try {
            videoStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'user', width: 640, height: 480 },
            });

            videoEl = document.getElementById('proctor-video');
            if (!videoEl) {
                videoEl = document.createElement('video');
                videoEl.id = 'proctor-video';
                videoEl.className = 'proctor-video-feed';
                videoEl.autoplay = true;
                videoEl.playsInline = true;
                videoEl.muted = true;
                const container = document.querySelector('.proctor-webcam-container');
                if (container) container.appendChild(videoEl);
            }
            videoEl.srcObject = videoStream;

            // Take reference photo after 2 seconds
            setTimeout(() => captureSnapshot(true), 2000);

            // Periodic snapshots every 60s
            snapshotInterval = setInterval(() => captureSnapshot(false), 60000);

            // Face/object detection every 3s (if TF.js loaded)
            if (typeof tf !== 'undefined') {
                await loadDetectionModels();
                detectionInterval = setInterval(runDetection, 3000);
            }

            return true;
        } catch (e) {
            addViolation('camera_denied', 'Camera permission denied or unavailable');
            return false;
        }
    }

    async function loadDetectionModels() {
        try {
            // Face landmarks model
            if (typeof faceLandmarksDetection !== 'undefined') {
                faceModel = await faceLandmarksDetection.createDetector(
                    faceLandmarksDetection.SupportedModels.MediaPipeFaceMesh,
                    { runtime: 'tfjs', maxFaces: 5, refineLandmarks: false }
                );
            }
            // Object detection (COCO-SSD)
            if (typeof cocoSsd !== 'undefined') {
                objectModel = await cocoSsd.load();
            }
        } catch (e) {
            console.warn('[Proctor] Model loading failed:', e);
        }
    }

    async function runDetection() {
        if (!videoEl || videoEl.readyState < 2) return;

        // Face detection
        if (faceModel) {
            try {
                const faces = await faceModel.estimateFaces(videoEl);
                if (faces.length === 0) {
                    addViolation('face_absent', 'No face detected');
                } else if (faces.length > 1) {
                    addViolation('multiple_faces', `${faces.length} faces detected`);
                } else {
                    // Head position analysis from landmarks
                    const kp = faces[0].keypoints;
                    if (kp && kp.length > 0) {
                        const nose = kp.find(p => p.name === 'noseTip');
                        const leftEye = kp.find(p => p.name === 'leftEye');
                        const rightEye = kp.find(p => p.name === 'rightEye');
                        if (nose && leftEye && rightEye) {
                            const eyeCenter = (leftEye.x + rightEye.x) / 2;
                            const yaw = (nose.x - eyeCenter) * 2;
                            if (Math.abs(yaw) > 30) {
                                addViolation('head_violation', `Head yaw: ${yaw.toFixed(1)}°`);
                            }
                        }
                    }
                }
            } catch (e) { /* Detection frame error */ }
        }

        // Object detection
        if (objectModel) {
            try {
                const predictions = await objectModel.detect(videoEl);
                const flaggedObjects = ['cell phone', 'book', 'laptop', 'tablet'];
                for (const pred of predictions) {
                    if (flaggedObjects.includes(pred.class) && pred.score > 0.5) {
                        addViolation('phone_detected', `${pred.class} detected (${(pred.score * 100).toFixed(0)}% confidence)`);
                    }
                }
            } catch (e) { /* Detection frame error */ }
        }
    }

    async function captureSnapshot(isReference = false) {
        if (!videoEl || videoEl.readyState < 2) return;

        const canvas = document.createElement('canvas');
        canvas.width = videoEl.videoWidth || 640;
        canvas.height = videoEl.videoHeight || 480;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(videoEl, 0, 0);

        const imageData = canvas.toDataURL('image/jpeg', 0.7);

        try {
            await fetch('/api/assessment/snapshot', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    attempt_id: attemptId,
                    image: imageData,
                    is_reference: isReference,
                    faces_detected: 1,
                    flag_type: 'clean',
                }),
                credentials: 'same-origin',
            });
        } catch (e) {
            console.warn('[Proctor] Snapshot upload failed');
        }
    }

    // ─── Auto-Save Answer ───────────────────────────────
    async function saveAnswer(questionId, answer) {
        try {
            const resp = await fetch('/api/assessment/save-answer', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    attempt_id: attemptId,
                    question_id: questionId,
                    answer: answer,
                }),
                credentials: 'same-origin',
            });

            const data = await resp.json();
            if (data.expired) {
                autoSubmit('Time expired');
            }
            return data.success;
        } catch (e) {
            console.warn('[Proctor] Auto-save failed:', e);
            return false;
        }
    }

    // ─── Submit ─────────────────────────────────────────
    async function autoSubmit(reason) {
        if (isSubmitting) return;
        isSubmitting = true;

        clearInterval(timerInterval);
        clearInterval(heartbeatInterval);
        clearInterval(snapshotInterval);
        clearInterval(detectionInterval);

        showWarning(reason || 'Assessment is being submitted...');

        try {
            const resp = await fetch('/api/assessment/submit', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    attempt_id: attemptId,
                    assessment_id: assessmentId,
                }),
                credentials: 'same-origin',
            });

            const data = await resp.json();
            if (data.redirect) {
                window.location.href = data.redirect;
            } else {
                window.location.href = '/pages/employee/applications.php';
            }
        } catch (e) {
            window.location.href = '/pages/employee/applications.php';
        }
    }

    // ─── Helpers ────────────────────────────────────────
    function addViolation(type, detail) {
        violationBuffer.push({ type, count: 1, detail, timestamp: new Date().toISOString() });
    }

    function showWarning(message) {
        let banner = document.getElementById('proctor-warning');
        if (!banner) {
            banner = document.createElement('div');
            banner.id = 'proctor-warning';
            banner.className = 'proctor-warning-banner';
            document.body.appendChild(banner);
        }
        banner.textContent = message;
        banner.classList.add('proctor-warning-visible');
        setTimeout(() => banner.classList.remove('proctor-warning-visible'), 5000);
    }

    function destroy() {
        clearInterval(timerInterval);
        clearInterval(heartbeatInterval);
        clearInterval(snapshotInterval);
        clearInterval(detectionInterval);
        if (videoStream) {
            videoStream.getTracks().forEach(t => t.stop());
        }
    }

    // ─── Public API ─────────────────────────────────────
    return {
        init,
        initWebcam,
        requestFullscreen,
        saveAnswer,
        autoSubmit,
        destroy,
        getCounters: () => ({ ...counters }),
    };
})();
