<div class="skill-header">
    <div>
        <h2 class="page-title skill-page-title">Skill Assessments</h2>
        <p class="page-subtitle skill-page-desc">
            Validate your expertise with industry-standard certifications. Our curated assessments are designed to bridge the gap between potential and placement.
        </p>
        <?php $currentTab = isset($_GET['tab']) ? $_GET['tab'] : 'available'; ?>
        <nav class="skill-tabs">
            <a href="../shared/skill-assesment.php" class="skill-tab <?= $currentTab === 'available' ? 'active' : '' ?>">Available</a>
            <a href="../shared/skill-assesment.php?tab=completed" class="skill-tab <?= $currentTab === 'completed' ? 'active' : '' ?>">Completed</a>
            <a href="../shared/skill-assesment.php?tab=progress" class="skill-tab <?= $currentTab === 'progress' ? 'active' : '' ?>">In Progress</a>
        </nav>
    </div>
</div>
