<?php
/**
 * Skill Assessment Card (Available tab)
 * 
 * Variables:
 * $icon     - Material icon name
 * $expiry   - Expiry text (e.g. "Expiring in 3d")
 * $title    - Assessment title
 * $company  - Company name
 * $duration - Time duration
 * $level    - Difficulty level
 */
?>
<div class="skill-card">
    <div class="skill-card-top">
        <div class="skill-card-company-logo">
            <span class="material-symbols-outlined" style="color: #695d46;"><?= $icon ?></span>
        </div>
        <span class="skill-expiry-badge"><?= $expiry ?></span>
    </div>
    <h4 class="skill-card-title"><?= $title ?></h4>
    <p class="skill-card-requester">Requested by <span><?= $company ?></span></p>
    <div class="skill-card-info">
        <div class="skill-card-info-item">
            <span class="material-symbols-outlined">schedule</span> <?= $duration ?>
        </div>
        <div class="skill-card-info-item">
            <span class="material-symbols-outlined">bar_chart</span> <?= $level ?>
        </div>
    </div>
    <button class="skill-start-btn">Start Assessment</button>
</div>
