<?php
/**
 * Interview Card
 * 
 * Variables:
 * $date        - Date/time (e.g. "Oct 25 • 2:00 PM")
 * $company     - Company name
 * $description - Interview description
 * $methodIcon  - Material icon for method
 * $methodText  - Method description
 */
?>
<div class="app-interview-card">
    <p class="app-interview-date"><?= $date ?></p>
    <h5 class="app-interview-company"><?= $company ?></h5>
    <p class="app-interview-desc"><?= $description ?></p>
    <div class="app-interview-method">
        <span class="material-symbols-outlined"><?= $methodIcon ?></span>
        <span><?= $methodText ?></span>
    </div>
</div>
