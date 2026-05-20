<?php
/**
 * Assessment Card Component (Employer)
 * Variables: $assessTitle, $linkedJob, $questionCount, $completions, $avgScore, $assessStatus, $assessType
 */
?>
<div class="emp-assess-card">
    <div class="emp-assess-card-top">
        <div>
            <h4 class="emp-assess-card-title"><?= $assessTitle ?></h4>
            <p class="emp-assess-card-job">
                <span class="material-symbols-outlined" style="font-size: 0.875rem;">link</span>
                <?= $linkedJob ?>
            </p>
        </div>
        <span class="emp-status-badge emp-status--<?= $assessType ?>"><?= $assessStatus ?></span>
    </div>
    <div class="emp-assess-card-stats">
        <div class="emp-assess-stat">
            <span class="material-symbols-outlined">help</span>
            <span><?= $questionCount ?> Questions</span>
        </div>
        <div class="emp-assess-stat">
            <span class="material-symbols-outlined">group</span>
            <span><?= $completions ?> Completed</span>
        </div>
        <div class="emp-assess-stat">
            <span class="material-symbols-outlined">trending_up</span>
            <span>Avg: <?= $avgScore ?></span>
        </div>
    </div>
    <div class="emp-assess-card-actions">
        <a href="../employer/assessment-detail.php?id=<?= $a['id'] ?>" class="emp-assess-act-btn" style="text-decoration:none; text-align:center;">View Results</a>
        <a href="../employer/assessment-edit.php?id=<?= $a['id'] ?>" class="emp-assess-act-btn emp-assess-act-btn--outline" style="text-decoration:none; text-align:center;">Edit</a>
    </div>
</div>
