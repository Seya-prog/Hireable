<?php
// Employer Sidebar
// Usage: Set $activePage before including
// Valid values: 'dashboard', 'jobs', 'candidates', 'interviews', 'skill-assessments'

$activePage = $activePage ?? '';

$navItems = [
    ['id' => 'dashboard',         'label' => 'Dashboard',         'icon' => 'space_dashboard', 'href' => 'dashboard.php'],
    ['id' => 'jobs',              'label' => 'Job Postings',      'icon' => 'work',            'href' => 'jobs.php'],
    ['id' => 'candidates',        'label' => 'Candidates',        'icon' => 'group',           'href' => 'candidates.php'],
    ['id' => 'interviews',        'label' => 'Interviews',        'icon' => 'event',           'href' => 'interviews.php'],
    ['id' => 'skill-assessments', 'label' => 'Skill Assessments', 'icon' => 'quiz',            'href' => 'skill-assesment.php'],
];
?>
<aside class="dash-sidebar">
    <div class="dash-sidebar-brand">
        <h1 class="dash-sidebar-title">Employer Hub</h1>
        <p class="dash-sidebar-subtitle">Talent Acquisition</p>
    </div>
    <nav class="dash-sidebar-nav">
        <?php foreach ($navItems as $item): ?>
            <a class="dash-nav-link <?= $activePage === $item['id'] ? 'active' : '' ?>"
               href="<?= $item['href'] ?>">
                <span class="material-symbols-outlined"
                      <?= $activePage === $item['id'] ? 'style="font-variation-settings: \'FILL\' 1;"' : '' ?>><?= $item['icon'] ?></span>
                <span><?= $item['label'] ?></span>
            </a>
        <?php endforeach; ?>
    </nav>
    <div class="dash-sidebar-upgrade">
        <button class="dash-upgrade-btn">Upgrade to Premium</button>
    </div>
    <div class="dash-sidebar-footer">
        <a class="dash-footer-link" href="../index.php">
            <span class="material-symbols-outlined">help</span>
            <span>Help Center</span>
        </a>
        <a class="dash-footer-link" href="logout.php">
            <span class="material-symbols-outlined">logout</span>
            <span>Sign Out</span>
        </a>
    </div>
</aside>
