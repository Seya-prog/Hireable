<!DOCTYPE html>
<html lang="en">
<head>
    <?php $pageTitle = 'Edit Assessment | Hireable Employer'; ?>
    <?php include __DIR__ . '/../components/head.php'; ?>
</head>
<body class="dash-page">
    <?php $activePage = 'skill-assessments'; ?>
    <?php include __DIR__ . '/../components/employer-sidebar.php'; ?>

    <main class="dash-main" style="margin-left: 260px;">
        <div class="emp-header">
            <div>
                <a href="assessment-detail.php" class="emp-back-link">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Back to Assessment
                </a>
                <h2 class="page-title">Edit Assessment</h2>
                <p class="page-subtitle">React Frontend Assessment • 15 Questions</p>
            </div>
        </div>

        <form class="emp-form" style="max-width: 800px;">
            <!-- Basic Info -->
            <section class="emp-form-section">
                <h3 class="emp-form-section-title">Assessment Details</h3>
                <div class="emp-form-grid">
                    <div class="assess-field assess-field--full">
                        <label class="assess-label">Assessment Title</label>
                        <input class="assess-input" type="text" value="React Frontend Assessment">
                    </div>
                    <div class="assess-field">
                        <label class="assess-label">Linked Position</label>
                        <select class="assess-input assess-select">
                            <option>Senior Software Engineer</option>
                            <option>VP of Product Innovation</option>
                            <option>Marketing Lead</option>
                            <option>Data Analyst</option>
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
                    <div class="assess-field">
                        <label class="assess-label">Time Limit</label>
                        <select class="assess-input assess-select">
                            <option>15 minutes</option>
                            <option>30 minutes</option>
                            <option selected>45 minutes</option>
                            <option>60 minutes</option>
                            <option>90 minutes</option>
                        </select>
                    </div>
                    <div class="assess-field">
                        <label class="assess-label">Passing Score (%)</label>
                        <input class="assess-input" type="number" value="70" min="0" max="100">
                    </div>
                </div>
            </section>

            <!-- Questions -->
            <section class="emp-form-section">
                <div class="assess-questions-head">
                    <h3 class="emp-form-section-title" style="margin:0;">Questions</h3>
                    <div class="assess-add-btns">
                        <button type="button" class="assess-add-q-btn">
                            <span class="material-symbols-outlined">add</span>
                            Multiple Choice
                        </button>
                        <button type="button" class="assess-add-q-btn">
                            <span class="material-symbols-outlined">code</span>
                            Code Challenge
                        </button>
                    </div>
                </div>

                <div class="assess-question-card">
                    <div class="assess-q-header">
                        <span class="assess-q-number">Q1</span>
                        <span class="assess-q-type-badge">Multiple Choice</span>
                        <div class="assess-q-actions">
                            <button class="emp-action-btn"><span class="material-symbols-outlined">drag_indicator</span></button>
                            <button class="emp-action-btn"><span class="material-symbols-outlined">delete</span></button>
                        </div>
                    </div>
                    <textarea class="assess-textarea" rows="2">What is the virtual DOM in React?</textarea>
                    <div class="assess-q-options">
                        <div class="assess-q-option">
                            <input type="radio" name="q1" checked>
                            <input class="assess-input assess-input--sm" value="A lightweight copy of the actual DOM">
                        </div>
                        <div class="assess-q-option">
                            <input type="radio" name="q1">
                            <input class="assess-input assess-input--sm" value="A browser-specific API">
                        </div>
                        <div class="assess-q-option">
                            <input type="radio" name="q1">
                            <input class="assess-input assess-input--sm" value="A CSS rendering engine">
                        </div>
                        <div class="assess-q-option">
                            <input type="radio" name="q1">
                            <input class="assess-input assess-input--sm" value="A database model">
                        </div>
                    </div>
                </div>

                <div class="assess-question-card">
                    <div class="assess-q-header">
                        <span class="assess-q-number">Q2</span>
                        <span class="assess-q-type-badge assess-q-type-badge--code">Code</span>
                        <div class="assess-q-actions">
                            <button class="emp-action-btn"><span class="material-symbols-outlined">drag_indicator</span></button>
                            <button class="emp-action-btn"><span class="material-symbols-outlined">delete</span></button>
                        </div>
                    </div>
                    <textarea class="assess-textarea" rows="2">Write a custom hook that debounces a value</textarea>
                    <div class="assess-code-preview">
                        <code>function useDebounce(value, delay) {<br>&nbsp;&nbsp;// Implementation here<br>}</code>
                    </div>
                </div>

                <div class="assess-question-card">
                    <div class="assess-q-header">
                        <span class="assess-q-number">Q3</span>
                        <span class="assess-q-type-badge">Multiple Choice</span>
                        <div class="assess-q-actions">
                            <button class="emp-action-btn"><span class="material-symbols-outlined">drag_indicator</span></button>
                            <button class="emp-action-btn"><span class="material-symbols-outlined">delete</span></button>
                        </div>
                    </div>
                    <textarea class="assess-textarea" rows="2">Explain the difference between useMemo and useCallback</textarea>
                    <div class="assess-q-options">
                        <div class="assess-q-option">
                            <input type="radio" name="q3" checked>
                            <input class="assess-input assess-input--sm" value="useMemo caches a value, useCallback caches a function">
                        </div>
                        <div class="assess-q-option">
                            <input type="radio" name="q3">
                            <input class="assess-input assess-input--sm" value="They are exactly the same">
                        </div>
                        <div class="assess-q-option">
                            <input type="radio" name="q3">
                            <input class="assess-input assess-input--sm" value="useMemo is for state, useCallback is for effects">
                        </div>
                    </div>
                </div>
            </section>

            <div class="emp-form-actions">
                <a href="assessment-detail.php" class="assess-save-btn assess-save-btn--draft" style="text-decoration:none;">Cancel</a>
                <button type="button" class="assess-save-btn assess-save-btn--draft">Save as Draft</button>
                <button type="button" class="assess-save-btn assess-save-btn--publish">Update & Publish</button>
            </div>
        </form>
    </main>
</body>
</html>
