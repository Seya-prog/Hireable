<?php
/**
 * Application Pipeline Card
 * 
 * Variables:
 * $logo       - Company logo URL
 * $title      - Job title
 * $company    - Company name
 * $appliedDate - Application date
 * $status     - Status text (e.g. "Interviewing")
 * $statusType - CSS modifier (interview|review|offer)
 * $nextStep   - Next step text
 */
?>
<a href="../employee/application-detail.php" class="app-card" data-filter-item data-status="<?= $statusType ?>" data-searchable="<?= strtolower($title . ' ' . $company) ?>" style="text-decoration: none; color: inherit;">
    <div class="app-card-left">
        <div class="app-card-logo">
            <img src="<?= $logo ?>" alt="<?= $company ?> logo">
        </div>
        <div>
            <h4 class="app-card-title"><?= $title ?></h4>
            <div class="app-card-meta">
                <span><?= $company ?></span>
                <span class="app-dot"></span>
                <span><?= $appliedDate ?></span>
            </div>
        </div>
    </div>
    <div class="app-card-right">
        <div class="app-card-status-wrap">
            <span class="app-badge app-badge--<?= $statusType ?>"><?= $status ?></span>
            <p class="app-card-next">Next: <span><?= $nextStep ?></span></p>
        </div>
        <span class="app-more-btn material-symbols-outlined">chevron_right</span>
    </div>
</a>
