<?php
require_once __DIR__ . '/../../backend/helpers/session.php';
require_once __DIR__ . '/../../backend/helpers/csrf.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../database/repositories/AssessmentRepository.php';
require_once __DIR__ . '/../../database/repositories/JobRepository.php';

if (!isLoggedIn() || getCurrentUserRole() !== 'employer') {
    header('Location: ' . AUTH_URL . 'login.php'); exit;
}

$employerId = getCurrentUserId();
$assessId = intval($_GET['id'] ?? 0);
$assessRepo = new AssessmentRepository($pdo);
$jobRepo = new JobRepository($pdo);

$assessment = $assessRepo->findById($assessId);
if (!$assessment || $assessment['employer_id'] != $employerId) {
    header('Location: ' . SHARED_URL . 'skill-assesment.php?tab=my-assessments'); exit;
}

$questions = $assessRepo->getQuestions($assessId);
$totalQ = count($questions);

// Parse JSON options for each question
foreach ($questions as &$q) {
    $q['parsed_options'] = [];
    if ($q['question_type'] === 'multiple_choice' && !empty($q['options'])) {
        $decoded = json_decode($q['options'], true);
        if (is_array($decoded)) {
            $q['parsed_options'] = $decoded;
        }
    }
}
unset($q);

// Get employer's jobs for linking
$jobs = $jobRepo->findByEmployer($employerId);

$difficultyLevels = ['beginner','intermediate','advanced','expert'];
$timeLimits = [15,30,45,60,90];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php $pageTitle = 'Edit Assessment | Hireable Employer'; ?>
    <?php $pageCss = ['employer.css', 'toast.css'];
    include __DIR__ . '/../../components/shared/head.php'; ?>
</head>
<body class="dash-page">
    <?php include __DIR__ . '/../../components/shared/toast.php'; ?>
    <?php $activePage = 'skill-assessments'; ?>
    <?php include __DIR__ . '/../../components/employer/employer-sidebar.php'; ?>

    <main class="dash-main" style="margin-left: 260px;">
        <div class="emp-header">
            <div>
                <a href="../employer/assessment-detail.php?id=<?= $assessId ?>" class="emp-back-link">
                    <span class="material-symbols-outlined">arrow_back</span> Back to Assessment
                </a>
                <h2 class="page-title">Edit Assessment</h2>
                <p class="page-subtitle"><?= htmlspecialchars($assessment['title']) ?> • <?= $totalQ ?> Questions</p>
            </div>
        </div>

        <form class="emp-form" style="max-width: 800px;" method="POST" action="/action/employer.assessments.update">
            <?= csrfField() ?>
            <input type="hidden" name="assessment_id" value="<?= $assessId ?>">

            <!-- Basic Info -->
            <section class="emp-form-section">
                <h3 class="emp-form-section-title">Assessment Details</h3>
                <div class="emp-form-grid">
                    <div class="assess-field assess-field--full">
                        <label class="assess-label">Assessment Title</label>
                        <input class="assess-input" type="text" name="title" value="<?= htmlspecialchars($assessment['title']) ?>" required>
                    </div>
                    <div class="assess-field">
                        <label class="assess-label">Linked Position</label>
                        <select class="assess-input assess-select" name="job_id">
                            <option value="">None</option>
                            <?php foreach ($jobs as $j): ?>
                                <option value="<?= $j['id'] ?>" <?= ($j['id'] == $assessment['job_id']) ? 'selected' : '' ?>><?= htmlspecialchars($j['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="assess-field">
                        <label class="assess-label">Difficulty</label>
                        <select class="assess-input assess-select" name="difficulty">
                            <?php foreach ($difficultyLevels as $d): ?>
                                <option value="<?= $d ?>" <?= $d === ($assessment['difficulty'] ?? '') ? 'selected' : '' ?>><?= ucfirst($d) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="assess-field">
                        <label class="assess-label">Time Limit</label>
                        <select class="assess-input assess-select" name="time_limit_minutes">
                            <?php foreach ($timeLimits as $t): ?>
                                <option value="<?= $t ?>" <?= $t == ($assessment['time_limit_minutes'] ?? 45) ? 'selected' : '' ?>><?= $t ?> minutes</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="assess-field">
                        <label class="assess-label">Passing Score (%)</label>
                        <input class="assess-input" type="number" name="passing_score" value="<?= $assessment['passing_score'] ?? 70 ?>" min="0" max="100">
                    </div>
                </div>
            </section>

            <!-- Questions -->
            <section class="emp-form-section">
                <div class="assess-questions-head">
                    <h3 class="emp-form-section-title" style="margin:0;">Questions</h3>
                    <div class="assess-add-btns">
                        <button type="button" class="assess-add-q-btn" onclick="addMCQuestion()">
                            <span class="material-symbols-outlined">add</span> Multiple Choice
                        </button>
                        <button type="button" class="assess-add-q-btn" onclick="addCodeQuestion()">
                            <span class="material-symbols-outlined">code</span> Code Challenge
                        </button>
                    </div>
                </div>

                <div id="questions-list">
                <?php foreach ($questions as $idx => $q):
                    $opts = $q['parsed_options'];
                    $isCode = ($q['question_type'] === 'code');
                    $isOpen = ($q['question_type'] === 'open_ended');
                ?>
                <div class="assess-question-card" data-q-id="<?= $q['id'] ?>">
                    <div class="assess-q-header">
                        <span class="assess-q-number">Q<?= $idx + 1 ?></span>
                        <span class="assess-q-type-badge <?= $isCode ? 'assess-q-type-badge--code' : '' ?>"><?= $isCode ? 'Code' : ($isOpen ? 'Free Text' : 'Multiple Choice') ?></span>
                        <div class="assess-q-actions">
                            <button type="button" class="emp-action-btn" onclick="this.closest('.assess-question-card').remove(); renumberQuestions();">
                                <span class="material-symbols-outlined">delete</span>
                            </button>
                        </div>
                    </div>
                    <input type="hidden" name="questions[<?= $idx ?>][id]" value="<?= $q['id'] ?>">
                    <input type="hidden" name="questions[<?= $idx ?>][type]" value="<?= $q['question_type'] ?>">
                    <input type="hidden" name="questions[<?= $idx ?>][points]" value="<?= $q['points'] ?>">
                    <textarea class="assess-textarea" rows="2" name="questions[<?= $idx ?>][text]"><?= htmlspecialchars($q['question_text']) ?></textarea>

                    <?php if ($q['question_type'] === 'multiple_choice' && !empty($opts)): ?>
                    <div class="assess-q-options">
                        <?php foreach ($opts as $oi => $optText): 
                            $isCorrect = (strtolower(trim($optText)) === strtolower(trim($q['correct_answer'] ?? '')));
                        ?>
                        <div class="assess-q-option">
                            <input type="radio" name="questions[<?= $idx ?>][correct_answer_idx]" value="<?= $oi ?>" <?= $isCorrect ? 'checked' : '' ?>>
                            <input class="assess-input assess-input--sm" name="questions[<?= $idx ?>][options][<?= $oi ?>]" value="<?= htmlspecialchars($optText) ?>">
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php elseif ($isCode || $isOpen): ?>
                    <div class="assess-field" style="margin-top:0.75rem;">
                        <label class="assess-label">Expected Answer</label>
                        <textarea class="assess-textarea" rows="3" name="questions[<?= $idx ?>][correct_answer]" <?= $isCode ? 'style="font-family:monospace; background:#1a1a2e; color:#e0e0e0;"' : '' ?>><?= htmlspecialchars($q['correct_answer'] ?? '') ?></textarea>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
                </div>
            </section>

            <input type="hidden" name="status" id="assess-status" value="<?= $assessment['status'] ?>">

            <div class="emp-form-actions">
                <a href="../employer/assessment-detail.php?id=<?= $assessId ?>" class="assess-save-btn assess-save-btn--draft" style="text-decoration:none;">Cancel</a>
                <button type="submit" class="assess-save-btn assess-save-btn--draft" onclick="document.getElementById('assess-status').value='draft';">Save as Draft</button>
                <button type="submit" class="assess-save-btn assess-save-btn--publish" onclick="document.getElementById('assess-status').value='active';">Update & Publish</button>
            </div>
        </form>
    </main>

    <script>
    let qCount = <?= $totalQ ?>;

    function addMCQuestion() {
        const list = document.getElementById('questions-list');
        const idx = qCount++;
        const card = document.createElement('div');
        card.className = 'assess-question-card';
        card.innerHTML = `
            <div class="assess-q-header">
                <span class="assess-q-number">Q${idx + 1}</span>
                <span class="assess-q-type-badge">Multiple Choice</span>
                <div class="assess-q-actions">
                    <button type="button" class="emp-action-btn" onclick="this.closest('.assess-question-card').remove(); renumberQuestions();"><span class="material-symbols-outlined">delete</span></button>
                </div>
            </div>
            <input type="hidden" name="questions[${idx}][id]" value="">
            <input type="hidden" name="questions[${idx}][type]" value="multiple_choice">
            <input type="hidden" name="questions[${idx}][points]" value="10">
            <textarea class="assess-textarea" rows="2" name="questions[${idx}][text]" placeholder="Enter question text..."></textarea>
            <div class="assess-q-options">
                <div class="assess-q-option"><input type="radio" name="questions[${idx}][correct_answer_idx]" value="0" checked><input class="assess-input assess-input--sm" name="questions[${idx}][options][0]" placeholder="Option A (correct)"></div>
                <div class="assess-q-option"><input type="radio" name="questions[${idx}][correct_answer_idx]" value="1"><input class="assess-input assess-input--sm" name="questions[${idx}][options][1]" placeholder="Option B"></div>
                <div class="assess-q-option"><input type="radio" name="questions[${idx}][correct_answer_idx]" value="2"><input class="assess-input assess-input--sm" name="questions[${idx}][options][2]" placeholder="Option C"></div>
                <div class="assess-q-option"><input type="radio" name="questions[${idx}][correct_answer_idx]" value="3"><input class="assess-input assess-input--sm" name="questions[${idx}][options][3]" placeholder="Option D"></div>
            </div>`;
        list.appendChild(card);
    }

    function addCodeQuestion() {
        const list = document.getElementById('questions-list');
        const idx = qCount++;
        const card = document.createElement('div');
        card.className = 'assess-question-card';
        card.innerHTML = `
            <div class="assess-q-header">
                <span class="assess-q-number">Q${idx + 1}</span>
                <span class="assess-q-type-badge assess-q-type-badge--code">Code</span>
                <div class="assess-q-actions">
                    <button type="button" class="emp-action-btn" onclick="this.closest('.assess-question-card').remove(); renumberQuestions();"><span class="material-symbols-outlined">delete</span></button>
                </div>
            </div>
            <input type="hidden" name="questions[${idx}][id]" value="">
            <input type="hidden" name="questions[${idx}][type]" value="code">
            <input type="hidden" name="questions[${idx}][points]" value="20">
            <textarea class="assess-textarea" rows="2" name="questions[${idx}][text]" placeholder="Describe the coding challenge..."></textarea>
            <div class="assess-field" style="margin-top:0.75rem;">
                <label class="assess-label">Expected Answer</label>
                <textarea class="assess-textarea" rows="4" name="questions[${idx}][correct_answer]" placeholder="Expected solution..." style="font-family:monospace; background:#1a1a2e; color:#e0e0e0;"></textarea>
            </div>`;
        list.appendChild(card);
    }

    function renumberQuestions() {
        document.querySelectorAll('.assess-question-card .assess-q-number').forEach((el, i) => {
            el.textContent = 'Q' + (i + 1);
        });
    }

    // Handle MCQ correct_answer before form submit
    document.querySelector('form')?.addEventListener('submit', function() {
        this.querySelectorAll('.assess-question-card').forEach(card => {
            const typeInput = card.querySelector('input[name$="[type]"]');
            if (typeInput && typeInput.value === 'multiple_choice') {
                const selectedRadio = card.querySelector('input[type="radio"]:checked');
                if (selectedRadio) {
                    const idx = selectedRadio.value;
                    const prefix = typeInput.name.replace('[type]', '');
                    const optionInput = card.querySelector(`input[name="${prefix}[options][${idx}]"]`);
                    if (optionInput) {
                        let hidden = card.querySelector('input[name$="[correct_answer]"]');
                        if (!hidden) {
                            hidden = document.createElement('input');
                            hidden.type = 'hidden';
                            hidden.name = prefix + '[correct_answer]';
                            card.appendChild(hidden);
                        }
                        hidden.value = optionInput.value;
                    }
                }
            }
        });
    });
    </script>
</body>
</html>
