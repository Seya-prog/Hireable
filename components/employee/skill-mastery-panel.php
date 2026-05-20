<?php
/**
 * Skill Mastery Panel (Employee sidebar)
 * Expects: $completedAssessments (from parent page) to be available
 */
$recentAchievements = array_slice($completedAssessments ?? [], 0, 3);
$totalCompleted = count($completedAssessments ?? []);
?>
<div class="skill-right">
    <div class="skill-progress-panel">
        <div class="skill-progress-header">
            <h4 class="skill-progress-title">Your Mastery</h4>
            <p class="skill-progress-updated">Assessments completed: <?= $totalCompleted ?></p>
        </div>
        <div class="skill-rank">
            <div class="skill-rank-row">
                <span class="skill-rank-label">Completed</span>
                <span class="skill-rank-value"><?= $totalCompleted ?> Assessments</span>
            </div>
            <div class="skill-rank-bar">
                <div class="skill-rank-fill" style="width: <?= min($totalCompleted * 10, 100) ?>%"></div>
            </div>
            <p class="skill-rank-hint">Complete more assessments to boost your profile visibility to employers.</p>
        </div>
        <div class="skill-achievements">
            <h5 class="skill-meta-label">Recent Achievements</h5>
            <?php if (empty($recentAchievements)): ?>
                <p style="color:#7a6b5a; font-size:0.85rem;">No achievements yet. Complete an assessment to earn your first!</p>
            <?php else: ?>
                <ul class="skill-achievement-list">
                    <?php foreach ($recentAchievements as $ach): ?>
                    <li class="skill-achievement-item">
                        <span class="material-symbols-outlined skill-check-icon" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                        <div>
                            <p class="skill-achievement-name"><?= htmlspecialchars($ach['title']) ?></p>
                            <p class="skill-achievement-detail">Scored <?= $ach['score'] !== null ? round($ach['score']) . '%' : '—' ?> • <?= $ach['completed_at'] ? date('M j', strtotime($ach['completed_at'])) : '' ?></p>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        <div class="skill-progress-image">
            <div class="skill-progress-image-overlay"></div>
            <div class="skill-progress-image-text">
                <p>"Curating your path to excellence, one skill at a time."</p>
            </div>
        </div>
    </div>
</div>
