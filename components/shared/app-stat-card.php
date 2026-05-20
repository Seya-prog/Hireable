<?php
/**
 * Application Stat Card
 * 
 * Variables:
 * $label     - Stat label (e.g. "Total Active")
 * $value     - Stat value (e.g. "12")
 * $highlight - Optional boolean for highlight style
 */
$highlightClass = !empty($highlight) ? ' app-stat-card--highlight' : '';
?>
<div class="app-stat-card<?= $highlightClass ?>">
    <span class="app-stat-label"><?= $label ?></span>
    <span class="app-stat-value"><?= $value ?></span>
</div>
