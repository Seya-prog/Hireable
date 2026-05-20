<?php
/**
 * Employer - Assessment Results Tab
 * Expects: $assessRepo (AssessmentRepository), $employerId (int) to be set by parent page
 */
if (!isset($assessRepo) || !isset($employerId)) return;

$allAssessments = $assessRepo->findByEmployer($employerId);

// Gather all attempts across all assessments
$allAttempts = [];
foreach ($allAssessments as $a) {
    $attempts = $assessRepo->getAttemptsByAssessment($a['id']);
    foreach ($attempts as &$att) {
        $att['assessment_title'] = $a['title'];
        $att['passing_score']    = $a['passing_score'];
    }
    unset($att);
    $allAttempts = array_merge($allAttempts, $attempts);
}

// Sort by completed_at desc
usort($allAttempts, fn($a, $b) => strtotime($b['completed_at'] ?? '0') - strtotime($a['completed_at'] ?? '0'));

$totalAttempts = count($allAttempts);
$completedAttempts = array_filter($allAttempts, fn($a) => $a['status'] === 'completed');
$scores = array_filter(array_column($completedAttempts, 'score'), fn($s) => $s !== null);
$avgScore = !empty($scores) ? round(array_sum($scores) / count($scores)) : 0;
$passCount = 0;
foreach ($completedAttempts as $ca) {
    if (($ca['score'] ?? 0) >= ($ca['passing_score'] ?? 70)) $passCount++;
}
$passRate = count($completedAttempts) > 0 ? round(($passCount / count($completedAttempts)) * 100) : 0;
?>
<section>
    <div class="emp-section-head">
        <h3 class="emp-section-title">Assessment Results</h3>
        <select class="emp-filter-select" id="assessResultsFilter">
            <option value="">All Assessments</option>
            <?php foreach ($allAssessments as $a): ?>
                <option value="<?= htmlspecialchars(strtolower($a['title'])) ?>"><?= htmlspecialchars($a['title']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Results Summary -->
    <div class="assess-results-summary">
        <div class="assess-result-stat">
            <span class="assess-result-stat-value"><?= $totalAttempts ?></span>
            <span class="assess-result-stat-label">Total Attempts</span>
        </div>
        <div class="assess-result-stat">
            <span class="assess-result-stat-value"><?= $avgScore ?>%</span>
            <span class="assess-result-stat-label">Average Score</span>
        </div>
        <div class="assess-result-stat">
            <span class="assess-result-stat-value"><?= $passRate ?>%</span>
            <span class="assess-result-stat-label">Pass Rate</span>
        </div>
    </div>

    <!-- Results Table -->
    <div class="emp-table" id="assessResultsTable">
        <div class="emp-table-header emp-table-header--results">
            <span>Candidate</span>
            <span>Assessment</span>
            <span>Score</span>
            <span>Status</span>
            <span>Action</span>
        </div>
        <?php if (empty($allAttempts)): ?>
            <p style="text-align:center; color:#7a6b5a; padding:2rem;">No assessment results yet.</p>
        <?php else: ?>
            <?php foreach ($allAttempts as $att):
                $initials = strtoupper(substr($att['first_name'],0,1) . substr($att['last_name'],0,1));
                $name = htmlspecialchars($att['first_name'] . ' ' . substr($att['last_name'],0,1) . '.');
                $score = $att['score'] !== null ? round($att['score']) : 0;
                $passed = $score >= ($att['passing_score'] ?? 70);
                $scoreClass = $score >= 80 ? 'high' : ($score >= 60 ? 'mid' : 'low');
                $statusLabel = $att['status'] === 'completed' ? ($passed ? 'Passed' : 'Failed') : 'In Progress';
                $statusClass = $passed ? 'offer' : 'screening';
            ?>
            <div class="emp-table-row emp-table-row--results" data-assessment="<?= htmlspecialchars(strtolower($att['assessment_title'])) ?>">
                <div class="emp-table-candidate">
                    <div class="emp-avatar"><?= $initials ?></div>
                    <p class="emp-candidate-name"><?= $name ?></p>
                </div>
                <span class="emp-table-cell"><?= htmlspecialchars($att['assessment_title']) ?></span>
                <span class="emp-score emp-score--<?= $scoreClass ?>"><?= $score ?>%</span>
                <span class="emp-stage-badge emp-stage--<?= $statusClass ?>"><?= $statusLabel ?></span>
                <a href="../employer/assessment-integrity.php?id=<?= $att['id'] ?>" class="emp-action-btn"><span class="material-symbols-outlined">visibility</span></a>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<script>
(function() {
    const filter = document.getElementById('assessResultsFilter');
    const table = document.getElementById('assessResultsTable');
    if (!filter || !table) return;

    filter.addEventListener('change', function() {
        const val = this.value;
        const rows = table.querySelectorAll('.emp-table-row--results');
        rows.forEach(row => {
            if (!val || row.dataset.assessment === val) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
})();
</script>

