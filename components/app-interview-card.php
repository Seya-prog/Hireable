<?php
/**
 * Interview Card (Employee Applications sidebar)
 * 
 * Variables:
 * $date        - Date/time (e.g. "Oct 25 • 2:00 PM")
 * $company     - Company name
 * $description - Interview description
 * $methodIcon  - Material icon for method
 * $methodText  - Method description
 */
?>
<a href="application-detail.php" class="app-interview-card" style="text-decoration: none; color: inherit; display: block;">
    <p class="app-interview-date"><?= $date ?></p>
    <h5 class="app-interview-company"><?= $company ?></h5>
    <p class="app-interview-desc"><?= $description ?></p>
    <div class="app-interview-method">
        <span class="material-symbols-outlined"><?= $methodIcon ?></span>
        <span><?= $methodText ?></span>
    </div>
</a>
