<?php
/**
 * Employer - My Assessments List
 */
?>
<section>
    <div class="emp-section-head">
        <h3 class="emp-section-title">Active Assessments</h3>
        <a href="skill-assesment.php?tab=create" class="emp-cta-btn emp-cta-btn--sm">
            <span class="material-symbols-outlined">add</span>
            Create New
        </a>
    </div>
    <div class="emp-assess-grid">
        <?php
        $assessTitle = 'React Frontend Assessment'; $linkedJob = 'Senior Software Engineer';
        $questionCount = 15; $completions = 12; $avgScore = '78%';
        $assessStatus = 'Active'; $assessType = 'active';
        include __DIR__ . '/assessment-card.php';

        $assessTitle = 'Product Strategy Case Study'; $linkedJob = 'VP of Product Innovation';
        $questionCount = 8; $completions = 6; $avgScore = '82%';
        $assessStatus = 'Active'; $assessType = 'active';
        include __DIR__ . '/assessment-card.php';

        $assessTitle = 'Data Analysis Challenge'; $linkedJob = 'Data Analyst';
        $questionCount = 20; $completions = 19; $avgScore = '71%';
        $assessStatus = 'Active'; $assessType = 'active';
        include __DIR__ . '/assessment-card.php';
        ?>
    </div>
</section>

<section style="margin-top: 3rem;">
    <div class="emp-section-head">
        <h3 class="emp-section-title">Draft Assessments</h3>
    </div>
    <div class="emp-assess-grid">
        <?php
        $assessTitle = 'Marketing Strategy Quiz'; $linkedJob = 'Marketing Lead';
        $questionCount = 10; $completions = 0; $avgScore = '—';
        $assessStatus = 'Draft'; $assessType = 'draft';
        include __DIR__ . '/assessment-card.php';
        ?>
    </div>
</section>
