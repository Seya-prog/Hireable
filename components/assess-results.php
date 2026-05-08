<?php
/**
 * Employer - Assessment Results Tab
 */
?>
<section>
    <div class="emp-section-head">
        <h3 class="emp-section-title">Assessment Results</h3>
        <select class="emp-filter-select">
            <option>All Assessments</option>
            <option>React Frontend Assessment</option>
            <option>Product Strategy Case Study</option>
            <option>Data Analysis Challenge</option>
        </select>
    </div>

    <!-- Results Summary -->
    <div class="assess-results-summary">
        <div class="assess-result-stat">
            <span class="assess-result-stat-value">37</span>
            <span class="assess-result-stat-label">Total Attempts</span>
        </div>
        <div class="assess-result-stat">
            <span class="assess-result-stat-value">77%</span>
            <span class="assess-result-stat-label">Average Score</span>
        </div>
        <div class="assess-result-stat">
            <span class="assess-result-stat-value">82%</span>
            <span class="assess-result-stat-label">Pass Rate</span>
        </div>
        <div class="assess-result-stat">
            <span class="assess-result-stat-value">34m</span>
            <span class="assess-result-stat-label">Avg. Time</span>
        </div>
    </div>

    <!-- Results Table -->
    <div class="emp-table">
        <div class="emp-table-header emp-table-header--results">
            <span>Candidate</span>
            <span>Assessment</span>
            <span>Score</span>
            <span>Time</span>
            <span>Status</span>
            <span>Action</span>
        </div>
        <div class="emp-table-row emp-table-row--results">
            <div class="emp-table-candidate">
                <div class="emp-avatar">SM</div>
                <p class="emp-candidate-name">Sarah M.</p>
            </div>
            <span class="emp-table-cell">React Frontend</span>
            <span class="emp-score emp-score--high">92%</span>
            <span class="emp-table-cell">28 min</span>
            <span class="emp-stage-badge emp-stage--offer">Passed</span>
            <a href="candidate-detail.php" class="emp-action-btn"><span class="material-symbols-outlined">visibility</span></a>
        </div>
        <div class="emp-table-row emp-table-row--results">
            <div class="emp-table-candidate">
                <div class="emp-avatar">DK</div>
                <p class="emp-candidate-name">Daniel K.</p>
            </div>
            <span class="emp-table-cell">React Frontend</span>
            <span class="emp-score emp-score--mid">74%</span>
            <span class="emp-table-cell">42 min</span>
            <span class="emp-stage-badge emp-stage--offer">Passed</span>
            <a href="candidate-detail.php" class="emp-action-btn"><span class="material-symbols-outlined">visibility</span></a>
        </div>
        <div class="emp-table-row emp-table-row--results">
            <div class="emp-table-candidate">
                <div class="emp-avatar">TB</div>
                <p class="emp-candidate-name">Tariku B.</p>
            </div>
            <span class="emp-table-cell">React Frontend</span>
            <span class="emp-score emp-score--low">48%</span>
            <span class="emp-table-cell">45 min</span>
            <span class="emp-stage-badge emp-stage--screening">Failed</span>
            <a href="candidate-detail.php" class="emp-action-btn"><span class="material-symbols-outlined">visibility</span></a>
        </div>
        <div class="emp-table-row emp-table-row--results">
            <div class="emp-table-candidate">
                <div class="emp-avatar">MW</div>
                <p class="emp-candidate-name">Miriam W.</p>
            </div>
            <span class="emp-table-cell">Product Strategy</span>
            <span class="emp-score emp-score--high">88%</span>
            <span class="emp-table-cell">35 min</span>
            <span class="emp-stage-badge emp-stage--offer">Passed</span>
            <a href="candidate-detail.php" class="emp-action-btn"><span class="material-symbols-outlined">visibility</span></a>
        </div>
        <div class="emp-table-row emp-table-row--results">
            <div class="emp-table-candidate">
                <div class="emp-avatar">AL</div>
                <p class="emp-candidate-name">Aisha L.</p>
            </div>
            <span class="emp-table-cell">Data Analysis</span>
            <span class="emp-score emp-score--mid">69%</span>
            <span class="emp-table-cell">40 min</span>
            <span class="emp-stage-badge emp-stage--interview">Pending Review</span>
            <a href="candidate-detail.php" class="emp-action-btn"><span class="material-symbols-outlined">visibility</span></a>
        </div>
    </div>
</section>
