<?php
/**
 * Employer - Create Assessment Tab
 * Manual question builder + AI generation
 * Posts to /action/employer.assessments.create with CSRF protection
 */
require_once __DIR__ . '/../../backend/helpers/csrf.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../database/repositories/JobRepository.php';

// Load employer's jobs for linking
$jobRepo = new JobRepository($pdo);
$employerJobs = $jobRepo->findByEmployer(getCurrentUserId());
?>
<section>
    <div class="emp-section-head">
        <h3 class="emp-section-title">Create New Assessment</h3>
    </div>

    <!-- Creation Mode Toggle -->
    <div class="assess-create-modes">
        <button class="assess-mode-btn assess-mode-btn--active" id="mode-manual">
            <span class="material-symbols-outlined">edit_note</span>
            Build Manually
        </button>
        <button class="assess-mode-btn" id="mode-ai">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">auto_awesome</span>
            Generate with AI
        </button>
    </div>

    <!-- Manual Builder -->
    <div class="assess-create-panel" id="panel-manual">
        <form method="POST" action="/action/employer.assessments.create" id="assess-form">
            <?= csrfField() ?>
            <div class="assess-form-grid">
                <div class="assess-field">
                    <label class="assess-label" for="assess-title">Assessment Title</label>
                    <input class="assess-input" id="assess-title" name="title" type="text" placeholder="e.g. React Senior Developer Test" required>
                </div>
                <div class="assess-field">
                    <label class="assess-label" for="assess-job">Link to Job Post</label>
                    <select class="assess-input assess-select" id="assess-job" name="job_id">
                        <option value="">Select a job post...</option>
                        <?php foreach ($employerJobs as $job): ?>
                            <option value="<?= $job['id'] ?>"><?= htmlspecialchars($job['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="assess-field assess-field--full">
                    <label class="assess-label" for="assess-desc">Description</label>
                    <textarea class="assess-textarea" id="assess-desc" name="description" rows="3" placeholder="Describe the purpose and scope of this assessment..."></textarea>
                </div>
                <div class="assess-field">
                    <label class="assess-label" for="assess-time">Time Limit (minutes)</label>
                    <input class="assess-input" id="assess-time" name="time_limit_minutes" type="number" value="45" min="5" max="180">
                </div>
                <div class="assess-field">
                    <label class="assess-label" for="assess-diff">Difficulty</label>
                    <select class="assess-input assess-select" id="assess-diff" name="difficulty">
                        <option value="beginner">Beginner</option>
                        <option value="intermediate">Intermediate</option>
                        <option value="advanced" selected>Advanced</option>
                        <option value="expert">Expert</option>
                    </select>
                </div>
                <div class="assess-field">
                    <label class="assess-label" for="assess-pass">Passing Score (%)</label>
                    <input class="assess-input" id="assess-pass" name="passing_score" type="number" value="70" min="1" max="100">
                </div>
                <div class="assess-field">
                    <label class="assess-label" for="assess-status">Status</label>
                    <select class="assess-input assess-select" id="assess-status" name="status">
                        <option value="draft">Draft</option>
                        <option value="active">Active (Publish Now)</option>
                    </select>
                </div>
            </div>

            <!-- Question Builder -->
            <div class="assess-questions">
                <div class="assess-questions-head">
                    <h4 class="assess-questions-title">Questions</h4>
                    <div class="assess-add-btns">
                        <button type="button" class="assess-add-q-btn" onclick="AssessCreate.addQuestion('multiple_choice')">
                            <span class="material-symbols-outlined">radio_button_checked</span> MCQ
                        </button>
                        <button type="button" class="assess-add-q-btn" onclick="AssessCreate.addQuestion('code')">
                            <span class="material-symbols-outlined">code</span> Coding
                        </button>
                        <button type="button" class="assess-add-q-btn" onclick="AssessCreate.addQuestion('open_ended')">
                            <span class="material-symbols-outlined">short_text</span> Free Text
                        </button>
                    </div>
                </div>
                <div id="questions-container">
                    <!-- Questions added dynamically via JS -->
                </div>
            </div>

            <div class="assess-form-actions">
                <button type="submit" class="assess-save-btn assess-save-btn--draft" name="status" value="draft">Save as Draft</button>
                <button type="submit" class="assess-save-btn assess-save-btn--publish" name="status" value="active">Publish Assessment</button>
            </div>
        </form>
    </div>

    <!-- AI Generator -->
    <div class="assess-create-panel assess-create-panel--hidden" id="panel-ai">
        <div class="assess-ai-generator">
            <div class="assess-ai-icon">
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1; font-size: 2.5rem;">auto_awesome</span>
            </div>
            <h4 class="assess-ai-heading">AI Assessment Generator</h4>
            <p class="assess-ai-desc">Select a job posting and our AI will generate a tailored assessment based on the job requirements, skills needed, and industry standards.</p>

            <div class="assess-ai-form">
                <div class="assess-field">
                    <label class="assess-label">Select Job Post</label>
                    <select class="assess-input assess-select" id="ai-job-select">
                        <option value="">Choose a job posting...</option>
                        <?php foreach ($employerJobs as $job): ?>
                            <option value="<?= $job['id'] ?>"><?= htmlspecialchars($job['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="assess-ai-options">
                    <div class="assess-field">
                        <label class="assess-label">Number of Questions</label>
                        <select class="assess-input assess-select" id="ai-count">
                            <option>5</option>
                            <option selected>10</option>
                            <option>15</option>
                            <option>20</option>
                        </select>
                    </div>
                    <div class="assess-field">
                        <label class="assess-label">Question Types</label>
                        <select class="assess-input assess-select" id="ai-types">
                            <option selected>Mixed (MCQ + Coding + Free Text)</option>
                            <option>MCQ Only</option>
                            <option>Coding Only</option>
                            <option>Free Text Only</option>
                        </select>
                    </div>
                    <div class="assess-field">
                        <label class="assess-label">Difficulty</label>
                        <select class="assess-input assess-select" id="ai-diff">
                            <option>beginner</option>
                            <option>intermediate</option>
                            <option selected>advanced</option>
                            <option>expert</option>
                        </select>
                    </div>
                </div>
                <button type="button" class="assess-generate-btn" id="ai-generate-btn">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">auto_awesome</span>
                    Generate Assessment
                </button>
            </div>
        </div>
    </div>
</section>

<script>
const AssessCreate = (() => {
    let questionCount = 0;

    // Mode toggle
    document.getElementById('mode-manual')?.addEventListener('click', () => {
        document.getElementById('panel-manual').classList.remove('assess-create-panel--hidden');
        document.getElementById('panel-ai').classList.add('assess-create-panel--hidden');
        document.getElementById('mode-manual').classList.add('assess-mode-btn--active');
        document.getElementById('mode-ai').classList.remove('assess-mode-btn--active');
    });
    document.getElementById('mode-ai')?.addEventListener('click', () => {
        document.getElementById('panel-ai').classList.remove('assess-create-panel--hidden');
        document.getElementById('panel-manual').classList.add('assess-create-panel--hidden');
        document.getElementById('mode-ai').classList.add('assess-mode-btn--active');
        document.getElementById('mode-manual').classList.remove('assess-mode-btn--active');
    });

    function addQuestion(type) {
        const container = document.getElementById('questions-container');
        const idx = questionCount++;
        const prefix = `questions[${idx}]`;
        const letters = 'ABCD';

        let optionsHtml = '';
        if (type === 'multiple_choice') {
            optionsHtml = `
                <div class="assess-q-options">
                    ${[0,1,2,3].map(i => `
                    <div class="assess-q-option">
                        <input type="radio" name="${prefix}[correct_answer_idx]" value="${i}" ${i === 0 ? 'checked' : ''}>
                        <input class="assess-input assess-input--sm" type="text" name="${prefix}[options][${i}]"
                            placeholder="Option ${letters[i]}" required>
                    </div>`).join('')}
                </div>
                <p style="font-size:0.65rem; color:#7e766e; margin:8px 0 0;">
                    <span class="material-symbols-outlined" style="font-size:14px; vertical-align:middle;">info</span>
                    Select the radio button next to the correct answer
                </p>`;
        } else if (type === 'code') {
            optionsHtml = `
                <div class="assess-field" style="margin-top:0.75rem;">
                    <label class="assess-label">Expected Answer / Solution</label>
                    <textarea class="assess-textarea" name="${prefix}[correct_answer]" rows="4"
                        placeholder="Provide the expected code solution..." style="font-family:monospace;"></textarea>
                </div>`;
        } else {
            optionsHtml = `
                <div class="assess-field" style="margin-top:0.75rem;">
                    <label class="assess-label">Expected Answer Keywords</label>
                    <input class="assess-input" type="text" name="${prefix}[correct_answer]"
                        placeholder="Key terms for grading (comma-separated)">
                </div>`;
        }

        const typeBadge = type === 'code' ? 'assess-q-type-badge--code' : '';
        const typeLabel = type === 'multiple_choice' ? 'MCQ' : (type === 'code' ? 'Coding' : 'Free Text');

        const card = document.createElement('div');
        card.className = 'assess-question-card';
        card.innerHTML = `
            <div class="assess-q-header">
                <span class="assess-q-number">Q${idx + 1}</span>
                <span class="assess-q-type-badge ${typeBadge}">${typeLabel}</span>
                <div class="assess-q-actions">
                    <button type="button" class="emp-action-btn" onclick="this.closest('.assess-question-card').remove()">
                        <span class="material-symbols-outlined">delete</span>
                    </button>
                </div>
            </div>
            <input type="hidden" name="${prefix}[type]" value="${type}">
            <input class="assess-input" type="text" name="${prefix}[text]" placeholder="Enter question..." required>
            <div class="assess-field" style="margin-top:0.75rem;">
                <label class="assess-label">Points</label>
                <input class="assess-input" type="number" name="${prefix}[points]" value="10" min="1" max="100" style="max-width:100px;">
            </div>
            ${optionsHtml}
        `;

        container.appendChild(card);
    }

    // Handle MCQ correct_answer before form submit
    document.getElementById('assess-form')?.addEventListener('submit', function(e) {
        // For each MCQ, set correct_answer from selected radio + option text
        this.querySelectorAll('.assess-question-card').forEach(card => {
            const typeInput = card.querySelector('input[name$="[type]"]');
            if (typeInput && typeInput.value === 'multiple_choice') {
                const selectedRadio = card.querySelector('input[type="radio"]:checked');
                if (selectedRadio) {
                    const idx = selectedRadio.value;
                    const prefix = typeInput.name.replace('[type]', '');
                    const optionInput = card.querySelector(`input[name="${prefix}[options][${idx}]"]`);
                    if (optionInput) {
                        // Create hidden input for correct_answer
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

    return { addQuestion };
})();
</script>
