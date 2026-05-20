<?php
/**
 * Employer - My Assessments List
 * Expects: $assessRepo (AssessmentRepository), $employerId (int) to be set by parent page
 */
if (!isset($assessRepo) || !isset($employerId)) return;

$assessments = $assessRepo->findByEmployerWithStats($employerId);
$activeAssessments = array_filter($assessments, fn($a) => $a['status'] === 'active');
$draftAssessments  = array_filter($assessments, fn($a) => $a['status'] === 'draft');
?>
<section>
    <div class="emp-section-head">
        <h3 class="emp-section-title">Active Assessments</h3>
        <a href="../shared/skill-assesment.php?tab=create" class="emp-cta-btn emp-cta-btn--sm">
            <span class="material-symbols-outlined">add</span>
            Create New
        </a>
    </div>
    <div class="emp-assess-grid">
        <?php if (empty($activeAssessments)): ?>
            <p style="color:#7a6b5a; padding:1rem;">No active assessments. Create one to start evaluating candidates.</p>
        <?php else: ?>
            <?php foreach ($activeAssessments as $a):
                $assessTitle = htmlspecialchars($a['title']);
                $linkedJob = ''; // could link via job_id
                $questionCount = (int)($a['total_questions'] ?? 0);
                $completions = (int)($a['completed_attempts'] ?? 0);
                $avgScore = $a['avg_score'] ? round($a['avg_score']) . '%' : '—';
                $assessStatus = 'Active'; $assessType = 'active';
                include __DIR__ . '/assessment-card.php';
            endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<?php if (!empty($draftAssessments)): ?>
<section style="margin-top: 3rem;">
    <div class="emp-section-head">
        <h3 class="emp-section-title">Draft Assessments</h3>
    </div>
    <div class="emp-assess-grid">
        <?php foreach ($draftAssessments as $a):
            $assessTitle = htmlspecialchars($a['title']);
            $linkedJob = '';
            $questionCount = 0;
            $completions = 0;
            $avgScore = '—';
            $assessStatus = 'Draft'; $assessType = 'draft';
            include __DIR__ . '/assessment-card.php';
        endforeach; ?>
    </div>
</section>
<?php endif; ?>
