<?php
/**
 * Progress Card (In Progress tab)
 * 
 * Variables:
 * $icon      - Material icon name
 * $title     - Assessment title
 * $requester - Requester text (e.g. "Requested by Lumina Creative")
 * $timeLeft  - Time remaining (e.g. "12:45 Remaining")
 * $expires   - Expiry info (e.g. "Time expires in 1 day")
 * $progress  - Progress percentage (e.g. 65)
 * $questions - Questions progress (e.g. "13/20 Questions")
 * $level     - Difficulty level
 */
?>
<div class="skill-progress-card">
    <div class="skill-progress-card-top">
        <div class="skill-progress-card-left">
            <div class="skill-progress-card-logo">
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1; color: #695d46;"><?= $icon ?></span>
            </div>
            <div>
                <h4 class="skill-progress-card-title"><?= $title ?></h4>
                <p class="skill-progress-card-requester"><?= $requester ?></p>
            </div>
        </div>
        <div class="skill-progress-card-timer">
            <div class="skill-progress-card-time">
                <span class="material-symbols-outlined">timer</span>
                <span><?= $timeLeft ?></span>
            </div>
            <p class="skill-progress-card-expires"><?= $expires ?></p>
        </div>
    </div>
    <div class="skill-progress-bar-section">
        <div class="skill-progress-bar-labels">
            <span class="skill-progress-bar-label">Current Progress</span>
            <span class="skill-progress-bar-value"><?= $progress ?>% Complete</span>
        </div>
        <div class="skill-progress-bar-track">
            <div class="skill-progress-bar-fill" style="width: <?= $progress ?>%"></div>
        </div>
    </div>
    <div class="skill-progress-card-footer">
        <div class="skill-progress-card-meta">
            <div class="skill-card-info-item">
                <span class="material-symbols-outlined">format_list_bulleted</span> <?= $questions ?>
            </div>
            <div class="skill-card-info-item">
                <span class="material-symbols-outlined">bar_chart</span> <?= $level ?>
            </div>
        </div>
        <button class="skill-resume-btn">
            Resume Test <span class="material-symbols-outlined">arrow_forward</span>
        </button>
    </div>
</div>
