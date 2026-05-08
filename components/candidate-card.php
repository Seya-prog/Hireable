<?php
/**
 * Candidate Card Component
 * Variables: $candidateName, $candidateEmail, $initials, $appliedRole, $matchPercent, $matchLevel, $skills, $stage, $stageType
 */
?>
<div class="emp-cand-card">
    <div class="emp-cand-card-top">
        <div class="emp-cand-left">
            <div class="emp-avatar"><?= $initials ?></div>
            <div>
                <h4 class="emp-cand-name"><?= $candidateName ?></h4>
                <p class="emp-cand-email"><?= $candidateEmail ?></p>
            </div>
        </div>
        <span class="emp-match-badge emp-match--<?= $matchLevel ?>"><?= $matchPercent ?>%</span>
    </div>
    <p class="emp-cand-role">Applied for: <strong><?= $appliedRole ?></strong></p>
    <div class="emp-cand-skills">
        <?php foreach ($skills as $skill): ?>
            <span class="emp-cand-skill-tag"><?= $skill ?></span>
        <?php endforeach; ?>
    </div>
    <div class="emp-cand-card-bottom">
        <span class="emp-stage-badge emp-stage--<?= $stageType ?>"><?= $stage ?></span>
        <div class="emp-cand-actions">
            <button class="emp-action-btn" title="Shortlist"><span class="material-symbols-outlined">bookmark</span></button>
            <a href="interview-schedule.php" class="emp-action-btn" title="Schedule"><span class="material-symbols-outlined">calendar_month</span></a>
            <a href="candidate-detail.php" class="emp-action-btn" title="View"><span class="material-symbols-outlined">visibility</span></a>
        </div>
    </div>
</div>
