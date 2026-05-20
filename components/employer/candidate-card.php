<?php
/**
 * Candidate Card Component
 * Variables: $candidateName, $candidateEmail, $initials, $appliedRole, $matchPercent, $matchLevel, $skills, $stage, $stageType, $applicationId
 */
?>
<div class="emp-cand-card" data-filter-item data-status="<?= $stageType ?>" data-searchable="<?= strtolower($candidateName . ' ' . $candidateEmail . ' ' . $appliedRole) ?>" data-position="<?= strtolower($appliedRole) ?>">
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
            <button class="emp-action-btn emp-bookmark-btn" title="Shortlist" onclick="this.classList.toggle('emp-bookmarked');this.querySelector('span').textContent=this.classList.contains('emp-bookmarked')?'bookmark_added':'bookmark'"><span class="material-symbols-outlined">bookmark</span></button>
            <a href="../employer/interview-schedule.php?app_id=<?= $applicationId ?? 0 ?>" class="emp-action-btn" title="Schedule Interview"><span class="material-symbols-outlined">calendar_month</span></a>
            <a href="../employer/candidate-detail.php?id=<?= $applicationId ?? 0 ?>" class="emp-action-btn" title="View Profile"><span class="material-symbols-outlined">visibility</span></a>
        </div>
    </div>
</div>
