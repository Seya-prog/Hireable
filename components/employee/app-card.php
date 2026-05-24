<?php
/**
 * Application Pipeline Card
 * 
 * Variables:
 * $app        - Full application row (includes company_logo, id, job_id)
 * $logo       - Company logo URL (optional, uses $app['company_logo'] as fallback)
 * $title      - Job title
 * $company    - Company name
 * $appliedDate - Application date
 * $status     - Status text (e.g. "Interviewing")
 * $statusType - CSS modifier (interview|review|offer)
 * $nextStep   - Next step text
 */
$logoUrl = $app['company_logo'] ?? ($logo ?: '');
$companyInit = strtoupper(substr($company ?? 'C', 0, 1));
$appId = $app['id'] ?? 0;
?>
<a href="../employee/application-detail.php?app_id=<?= $appId ?>" class="app-card" data-filter-item data-status="<?= $statusType ?>" data-searchable="<?= strtolower($title . ' ' . $company) ?>" style="text-decoration: none; color: inherit;">
    <div class="app-card-left">
        <div class="app-card-logo">
            <?php if ($logoUrl): ?>
                <img src="<?= htmlspecialchars($logoUrl) ?>" alt="<?= $company ?>" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                <div class="app-card-init" style="display:none"><?= $companyInit ?></div>
            <?php else: ?>
                <div class="app-card-init"><?= $companyInit ?></div>
            <?php endif; ?>
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
            <?php if ($nextStep): ?>
            <p class="app-card-next">Next: <span><?= $nextStep ?></span></p>
            <?php endif; ?>
        </div>
        <span class="app-more-btn material-symbols-outlined">chevron_right</span>
    </div>
</a>
