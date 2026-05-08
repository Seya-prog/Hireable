<?php
/**
 * Job Card Component
 * Variables: $jobTitle, $department, $location, $jobStatus, $statusType, $applicants, $posted
 */
?>
<a href="job-detail.php" class="emp-job-card" style="text-decoration:none; color:inherit;">
    <div class="emp-job-card-top">
        <div>
            <h4 class="emp-job-card-title"><?= $jobTitle ?></h4>
            <p class="emp-job-card-meta"><?= $department ?> • <?= $location ?></p>
        </div>
        <span class="emp-status-badge emp-status--<?= $statusType ?>"><?= $jobStatus ?></span>
    </div>
    <div class="emp-job-card-bottom">
        <div class="emp-job-card-stat">
            <span class="material-symbols-outlined">group</span>
            <span><?= $applicants ?> applicants</span>
        </div>
        <span class="emp-job-card-date"><?= $posted ?></span>
    </div>
</a>
