<?php
/**
 * Interview Card Component (Employer)
 * 
 * Variables:
 * $candidate     - Candidate name
 * $position      - Position applied for
 * $interviewDate - Date/time text
 * $methodIcon    - Material icon name
 * $methodText    - Method description
 */
?>
<a href="../employer/candidate-detail.php" class="emp-int-card" style="text-decoration:none; color:inherit; display:block;">
    <div class="emp-int-card-top">
        <div class="emp-int-avatar"><?= strtoupper(substr($candidate, 0, 1)) . strtoupper(substr(strstr($candidate, ' '), 1, 1)) ?></div>
        <div>
            <p class="emp-int-candidate"><?= $candidate ?></p>
            <p class="emp-int-position"><?= $position ?></p>
        </div>
    </div>
    <div class="emp-int-card-bottom">
        <span class="emp-int-time"><?= $interviewDate ?></span>
        <div class="emp-int-method">
            <span class="material-symbols-outlined"><?= $methodIcon ?></span>
            <span><?= $methodText ?></span>
        </div>
    </div>
</a>
