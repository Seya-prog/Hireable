<?php
/**
 * Certification List Item
 * 
 * Variables:
 * $name  - Certification name
 * $date  - Certification date
 * $score - Score percentage (e.g. "98%")
 */
?>
<div class="skill-cert-item">
    <div class="skill-cert-left">
        <div class="skill-cert-icon">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">verified</span>
        </div>
        <div>
            <h6 class="skill-cert-name"><?= $name ?></h6>
            <p class="skill-cert-date"><?= $date ?></p>
        </div>
    </div>
    <div class="skill-cert-right">
        <div class="skill-cert-score-block">
            <span class="skill-cert-score-value"><?= $score ?></span>
            <span class="skill-cert-score-label">Score</span>
        </div>
        <button class="skill-cert-download-btn">
            <span class="material-symbols-outlined">download</span> Download
        </button>
    </div>
</div>
