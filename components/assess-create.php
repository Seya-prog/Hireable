<?php
/**
 * Employer - Create Assessment Tab
 * Manual question builder + AI generation
 */
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
        <div class="assess-form-grid">
            <div class="assess-field">
                <label class="assess-label">Assessment Title</label>
                <input class="assess-input" type="text" placeholder="e.g. React Senior Developer Test">
            </div>
            <div class="assess-field">
                <label class="assess-label">Link to Job Post</label>
                <select class="assess-input assess-select">
                    <option value="">Select a job post...</option>
                    <option>VP of Product Innovation</option>
                    <option>Senior Software Engineer</option>
                    <option>Marketing Lead</option>
                    <option>Data Analyst</option>
                </select>
            </div>
            <div class="assess-field assess-field--full">
                <label class="assess-label">Description</label>
                <textarea class="assess-textarea" rows="3" placeholder="Describe the purpose and scope of this assessment..."></textarea>
            </div>
            <div class="assess-field">
                <label class="assess-label">Time Limit</label>
                <input class="assess-input" type="text" placeholder="e.g. 45 minutes">
            </div>
            <div class="assess-field">
                <label class="assess-label">Difficulty</label>
                <select class="assess-input assess-select">
                    <option>Beginner</option>
                    <option>Intermediate</option>
                    <option selected>Advanced</option>
                    <option>Expert</option>
                </select>
            </div>
        </div>

        <!-- Question Builder -->
        <div class="assess-questions">
            <div class="assess-questions-head">
                <h4 class="assess-questions-title">Questions</h4>
                <div class="assess-add-btns">
                    <button class="assess-add-q-btn">
                        <span class="material-symbols-outlined">radio_button_checked</span> MCQ
                    </button>
                    <button class="assess-add-q-btn">
                        <span class="material-symbols-outlined">code</span> Coding
                    </button>
                    <button class="assess-add-q-btn">
                        <span class="material-symbols-outlined">short_text</span> Free Text
                    </button>
                </div>
            </div>

            <!-- Sample Question 1 -->
            <div class="assess-question-card">
                <div class="assess-q-header">
                    <span class="assess-q-number">Q1</span>
                    <span class="assess-q-type-badge">MCQ</span>
                    <div class="assess-q-actions">
                        <button class="emp-action-btn"><span class="material-symbols-outlined">drag_indicator</span></button>
                        <button class="emp-action-btn"><span class="material-symbols-outlined">delete</span></button>
                    </div>
                </div>
                <input class="assess-input" type="text" value="What is the virtual DOM in React?" placeholder="Enter question...">
                <div class="assess-q-options">
                    <div class="assess-q-option">
                        <input type="radio" name="q1" checked> 
                        <input class="assess-input assess-input--sm" type="text" value="A lightweight copy of the real DOM">
                    </div>
                    <div class="assess-q-option">
                        <input type="radio" name="q1"> 
                        <input class="assess-input assess-input--sm" type="text" value="A CSS framework">
                    </div>
                    <div class="assess-q-option">
                        <input type="radio" name="q1"> 
                        <input class="assess-input assess-input--sm" type="text" value="A database abstraction">
                    </div>
                    <div class="assess-q-option">
                        <input type="radio" name="q1"> 
                        <input class="assess-input assess-input--sm" type="text" value="A server-side rendering engine">
                    </div>
                </div>
            </div>

            <!-- Sample Question 2 -->
            <div class="assess-question-card">
                <div class="assess-q-header">
                    <span class="assess-q-number">Q2</span>
                    <span class="assess-q-type-badge assess-q-type-badge--code">Coding</span>
                    <div class="assess-q-actions">
                        <button class="emp-action-btn"><span class="material-symbols-outlined">drag_indicator</span></button>
                        <button class="emp-action-btn"><span class="material-symbols-outlined">delete</span></button>
                    </div>
                </div>
                <input class="assess-input" type="text" value="Write a custom hook that debounces an input value" placeholder="Enter question...">
                <div class="assess-code-preview">
                    <code>function useDebounce(value, delay) { ... }</code>
                </div>
            </div>
        </div>

        <div class="assess-form-actions">
            <button class="assess-save-btn assess-save-btn--draft">Save as Draft</button>
            <button class="assess-save-btn assess-save-btn--publish">Publish Assessment</button>
        </div>
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
                    <select class="assess-input assess-select">
                        <option value="">Choose a job posting...</option>
                        <option>VP of Product Innovation</option>
                        <option>Senior Software Engineer</option>
                        <option>Marketing Lead</option>
                        <option>Data Analyst</option>
                    </select>
                </div>
                <div class="assess-ai-options">
                    <div class="assess-field">
                        <label class="assess-label">Number of Questions</label>
                        <select class="assess-input assess-select">
                            <option>5 Questions</option>
                            <option selected>10 Questions</option>
                            <option>15 Questions</option>
                            <option>20 Questions</option>
                        </select>
                    </div>
                    <div class="assess-field">
                        <label class="assess-label">Question Types</label>
                        <select class="assess-input assess-select">
                            <option selected>Mixed (MCQ + Coding + Free Text)</option>
                            <option>MCQ Only</option>
                            <option>Coding Only</option>
                            <option>Free Text Only</option>
                        </select>
                    </div>
                    <div class="assess-field">
                        <label class="assess-label">Difficulty</label>
                        <select class="assess-input assess-select">
                            <option>Beginner</option>
                            <option>Intermediate</option>
                            <option selected>Advanced</option>
                            <option>Expert</option>
                        </select>
                    </div>
                </div>
                <button class="assess-generate-btn">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">auto_awesome</span>
                    Generate Assessment
                </button>
            </div>
        </div>
    </div>
</section>

<script>
// Toggle between manual and AI modes
document.getElementById('mode-manual').addEventListener('click', function() {
    this.classList.add('assess-mode-btn--active');
    document.getElementById('mode-ai').classList.remove('assess-mode-btn--active');
    document.getElementById('panel-manual').classList.remove('assess-create-panel--hidden');
    document.getElementById('panel-ai').classList.add('assess-create-panel--hidden');
});
document.getElementById('mode-ai').addEventListener('click', function() {
    this.classList.add('assess-mode-btn--active');
    document.getElementById('mode-manual').classList.remove('assess-mode-btn--active');
    document.getElementById('panel-ai').classList.remove('assess-create-panel--hidden');
    document.getElementById('panel-manual').classList.add('assess-create-panel--hidden');
});
</script>
