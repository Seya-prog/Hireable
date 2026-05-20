<?php
// Usage: Set $activePage before including this file
// Example: $activePage = 'profile';
// Valid values: 'profile', 'applications', 'skill-assessments', 'resume-generator'

$activePage = $activePage ?? '';

$navItems = [
    ['id' => 'profile',           'label' => 'Profile',           'icon' => 'person',        'href' => EMPLOYEE_URL . 'profile.php'],
    ['id' => 'applications',      'label' => 'Applications',      'icon' => 'description',   'href' => EMPLOYEE_URL . 'applications.php'],
    ['id' => 'skill-assessments', 'label' => 'Skill Assessments', 'icon' => 'verified_user',  'href' => SHARED_URL . 'skill-assesment.php'],
    ['id' => 'resume-generator',  'label' => 'Resume Generator',  'icon' => 'history_edu',   'href' => EMPLOYEE_URL . 'resume-generator.php'],
];
?>
<aside class="dash-sidebar">
    <div class="dash-sidebar-brand">
        <h1 class="dash-sidebar-title">Dashboard</h1>
        <p class="dash-sidebar-subtitle">Professional Curator</p>
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
        <a class="dash-footer-link" href="/public/index.php">
            <span class="material-symbols-outlined">help</span>
            <span>Help Center</span>
        </a>
        <a class="dash-footer-link" href="/backend/auth/logout.php">
            <span class="material-symbols-outlined">logout</span>
            <span>Sign Out</span>
        </a>
    </div>
</aside>
