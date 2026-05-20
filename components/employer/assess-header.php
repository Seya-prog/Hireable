<?php
/**
 * Employer Assessment Header (tabs)
 * Uses $currentTab variable from parent page
 */
?>
<div class="emp-header">
    <div>
        <h2 class="page-title">Skill Assessments</h2>
        <p class="page-subtitle">Create, manage, and review assessments for your job postings.</p>
    </div>
</div>
<nav class="skill-tabs" style="margin-bottom: 2rem;">
    <a class="skill-tab <?= $currentTab === 'my-assessments' ? 'active' : '' ?>"
       href="../shared/skill-assesment.php?tab=my-assessments">My Assessments</a>
    <a class="skill-tab <?= $currentTab === 'create' ? 'active' : '' ?>"
       href="../shared/skill-assesment.php?tab=create">Create New</a>
    <a class="skill-tab <?= $currentTab === 'results' ? 'active' : '' ?>"
       href="../shared/skill-assesment.php?tab=results">Results</a>
</nav>
