<?php
/**
 * Completed Partner Card (Completed tab)
 * 
 * Variables:
 * $icon    - Material icon name
 * $score   - Score percentage (e.g. "94%")
 * $date    - Pass date (e.g. "Passed Oct 05")
 * $title   - Assessment title
 * $company - Company name
 */
?>
<div class="skill-card">
    <div class="skill-card-top">
        <div class="skill-card-company-logo">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1; color: #695d46;"><?= $icon ?></span>
        </div>
        <div class="skill-card-score">
            <span class="skill-score-badge"><?= $score ?> Score</span>
            <span class="skill-score-date"><?= $date ?></span>
        </div>
    </div>
    <h4 class="skill-card-title"><?= $title ?></h4>
    <p class="skill-card-requester">Requested by <span><?= $company ?></span></p>
    <button class="skill-download-btn">
        <span class="material-symbols-outlined">download</span> Download Certificate
    </button>
</div>
