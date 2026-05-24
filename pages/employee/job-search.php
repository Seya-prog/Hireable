<?php
require_once __DIR__ . '/../../backend/helpers/session.php';
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../database/repositories/JobRepository.php';

$jobRepo = new JobRepository($pdo);

$filters = [
    'q'        => trim($_GET['q'] ?? ''),
    'location' => trim($_GET['location'] ?? ''),
    'type'     => trim($_GET['type'] ?? ''),
    'level'    => trim($_GET['level'] ?? ''),
];

$jobs = $jobRepo->findActiveJobs($filters);
$totalJobs = count($jobs);

// Split: first 2 for "curated", rest for list
$curatedJobs = array_slice($jobs, 0, 2);
$listJobs = $jobs;

$userName = isLoggedIn() ? ($_SESSION['user_first_name'] ?? 'there') : 'there';
$search = $filters['q'];
$locationFilter = $filters['location'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discover Jobs | Hireable</title>
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,wght@0,400;0,500;0,700;1,400&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../public/assets/css/global.css">
    <link rel="stylesheet" href="../../public/assets/css/layout.css">
    <link rel="stylesheet" href="../../public/assets/css/job-search.css">
    <link rel="stylesheet" href="../../public/assets/css/toast.css">
</head>
<body class="js-body">
    <?php if (isLoggedIn()) include __DIR__ . '/../../components/shared/toast.php'; ?>

    <!-- Top Nav -->
    <header class="js-topbar">
        <a href="../../index.php" class="js-logo">
            <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1">work</span>
            <span>Hireable</span>
        </a>
        <nav class="js-nav">
            <a href="job-search.php" class="js-nav-link js-nav-link--active">Marketplace</a>
            <a href="applications.php" class="js-nav-link">Applications</a>
            <a href="profile.php" class="js-nav-link">Profile</a>
        </nav>
        <div class="js-nav-right">
            <?php if (isLoggedIn()): ?>
                <a href="applications.php" class="js-topbar-icon"><span class="material-symbols-outlined">notifications</span></a>
                <a href="applications.php" class="js-topbar-icon"><span class="material-symbols-outlined">bookmark</span></a>
                <a href="profile.php" class="js-avatar"><?= strtoupper(substr($userName, 0, 1)) ?></a>
            <?php else: ?>
                <a href="../auth/login.php" class="js-login-btn">Sign In</a>
                <a href="../auth/signup.php" class="js-cta-btn">Get Started</a>
            <?php endif; ?>
        </div>
    </header>

    <!-- Hero -->
    <section class="js-hero">
        <h1 class="js-hero-title"><em>Discover your next milestone</em></h1>
        <p class="js-hero-sub">Search thousands of vetted roles across design, engineering, and management<br>from the world's most innovative organizations.</p>

        <form class="js-search-form" method="GET" action="job-search.php">
            <div class="js-search-bar">
                <div class="js-search-field">
                    <span class="material-symbols-outlined">search</span>
                    <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Job title, keywords, or company...">
                </div>
                <div class="js-search-divider"></div>
                <div class="js-search-field">
                    <span class="material-symbols-outlined">location_on</span>
                    <input type="text" name="location" value="<?= htmlspecialchars($locationFilter) ?>" placeholder="City or remote...">
                </div>
                <button type="submit" class="js-search-btn">Search Jobs</button>
            </div>
        </form>

        <div class="js-filter-pills">
            <button class="js-pill" type="button">Salary: Any Range <span class="material-symbols-outlined">expand_more</span></button>
            <button class="js-pill" type="button">Industry: All Sectors <span class="material-symbols-outlined">expand_more</span></button>
            <button class="js-pill" type="button">Level: All Levels <span class="material-symbols-outlined">expand_more</span></button>
            <button class="js-pill js-pill--outline" type="button"><span class="material-symbols-outlined">tune</span> More Filters</button>
        </div>
    </section>

    <main class="js-main">
        <!-- Curated Section -->
        <?php if (!empty($curatedJobs)): ?>
        <section class="js-section">
            <div class="js-section-header">
                <div>
                    <h2 class="js-section-title">Curated for You</h2>
                    <p class="js-section-sub">Hand-picked matches based on your profile and professional background.</p>
                </div>
                <div class="js-carousel-nav">
                    <button class="js-carousel-btn"><span class="material-symbols-outlined">chevron_left</span></button>
                    <button class="js-carousel-btn"><span class="material-symbols-outlined">chevron_right</span></button>
                </div>
            </div>
            <div class="js-curated-grid">
                <?php foreach ($curatedJobs as $i => $cj): 
                    $company = $cj['company_name'] ?: ($cj['first_name'] . ' ' . $cj['last_name']);
                    $salaryText = '';
                    if ($cj['salary_min'] && $cj['salary_max']) {
                        $salaryText = '$' . number_format($cj['salary_min']/1000) . 'k – $' . number_format($cj['salary_max']/1000) . 'k';
                    } elseif ($cj['salary_min']) {
                        $salaryText = '$' . number_format($cj['salary_min']/1000) . 'k+';
                    }
                    $icons = ['apartment','rocket_launch','code','groups','business'];
                ?>
                <a href="job-detail.php?id=<?= $cj['id'] ?>" class="js-curated-card <?= $i === 0 ? 'js-curated-card--dark' : '' ?>">
                    <div class="js-curated-top">
                        <div class="js-curated-icon"><span class="material-symbols-outlined"><?= $icons[$i % 5] ?></span></div>
                        <div>
                            <h3 class="js-curated-title"><?= htmlspecialchars($cj['title']) ?></h3>
                            <p class="js-curated-company"><?= htmlspecialchars($company) ?> • <?= htmlspecialchars($cj['location'] ?: 'Remote') ?></p>
                        </div>
                        <span class="js-curated-badge"><?= $i === 0 ? 'Expert Match' : 'Hot Opportunity' ?></span>
                    </div>
                    <div class="js-curated-bottom">
                        <div class="js-curated-meta">
                            <div><span class="js-curated-meta-label"><?= $i === 0 ? 'Experience' : 'Stack' ?></span><span class="js-curated-meta-value"><?= htmlspecialchars(ucfirst($cj['experience_level'] ?: 'Senior')) ?></span></div>
                            <div><span class="js-curated-meta-label">Compensation</span><span class="js-curated-meta-value"><?= $salaryText ?: 'Competitive' ?></span></div>
                        </div>
                        <div class="js-curated-cta">
                            <span class="js-curated-cta-text"><?= $i === 0 ? 'View Opportunity' : 'Review Role' ?> →</span>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- All Opportunities -->
        <section class="js-section">
            <div class="js-section-header">
                <h2 class="js-section-title">All Opportunities <span class="js-count">(<?= $totalJobs ?> available)</span></h2>
                <div class="js-sort">
                    <span>Sort by</span>
                    <button class="js-sort-btn">Latest Postings <span class="material-symbols-outlined">expand_more</span></button>
                </div>
            </div>

            <div class="js-opp-list">
                <?php if (empty($listJobs)): ?>
                    <div class="js-empty">
                        <span class="material-symbols-outlined" style="font-size:3rem;color:#bbb;">work_off</span>
                        <h3>No opportunities found</h3>
                        <p>Try adjusting your search or filters to discover more roles.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($listJobs as $job):
                        $company = $job['company_name'] ?: ($job['first_name'] . ' ' . $job['last_name']);
                        $salaryText = '';
                        if ($job['salary_min'] && $job['salary_max']) {
                            $salaryText = '$' . number_format($job['salary_min']/1000) . 'k – $' . number_format($job['salary_max']/1000) . 'k';
                        } elseif ($job['salary_min']) {
                            $salaryText = '$' . number_format($job['salary_min']/1000) . 'k+';
                        }
                        $icons = ['apartment','rocket_launch','code','groups','business','analytics','design_services','campaign'];
                        $icon = $icons[crc32($job['title']) % count($icons)];
                        $levelMap = ['entry'=>'Entry','mid'=>'Mid-Senior','senior'=>'Senior','executive'=>'Executive'];
                        $level = $levelMap[$job['experience_level']] ?? ucfirst($job['experience_level'] ?? '');
                        $typeLabel = ucfirst(str_replace('-', '-', $job['job_type'] ?? 'Full-time'));
                    ?>
                    <a href="job-detail.php?id=<?= $job['id'] ?>" class="js-opp-row">
                        <div class="js-opp-left">
                            <div class="js-opp-icon"><span class="material-symbols-outlined"><?= $icon ?></span></div>
                            <div>
                                <div class="js-opp-title-row">
                                    <h4 class="js-opp-title"><?= htmlspecialchars($job['title']) ?></h4>
                                    <?php if ($typeLabel): ?><span class="js-opp-tag"><?= $typeLabel ?></span><?php endif; ?>
                                    <?php if ($level): ?><span class="js-opp-tag js-opp-tag--outline"><?= $level ?></span><?php endif; ?>
                                </div>
                                <p class="js-opp-company"><?= htmlspecialchars($company) ?> · <?= htmlspecialchars($job['location'] ?: 'Remote') ?></p>
                            </div>
                        </div>
                        <div class="js-opp-right">
                            <div class="js-opp-match">
                                <span class="js-opp-match-label">Match Level</span>
                                <div class="js-opp-match-bar"><div class="js-opp-match-fill" style="width:<?= rand(60,95) ?>%"></div></div>
                            </div>
                            <span class="js-opp-salary"><?= $salaryText ?: 'Competitive' ?></span>
                            <button class="js-opp-bookmark"><span class="material-symbols-outlined">bookmark</span></button>
                        </div>
                    </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?php if ($totalJobs > 10): ?>
            <div class="js-load-more">
                <button class="js-load-btn">Show more opportunities</button>
                <p class="js-viewing">Viewing <?= min(10, $totalJobs) ?> of <?= $totalJobs ?> roles</p>
            </div>
            <?php endif; ?>
        </section>
    </main>

    <!-- Footer -->
    <footer class="js-footer">
        <div class="js-footer-grid">
            <div class="js-footer-brand">
                <h3>Hireable</h3>
                <p>A curated ecosystem for professionals at every stage. We bridge the gap between world-class talent and transformative opportunities.</p>
            </div>
            <div class="js-footer-col">
                <h4>Explore</h4>
                <a href="job-search.php">Browse Jobs</a>
                <a href="#">Company Directory</a>
                <a href="#">Salaries</a>
                <a href="../shared/skill-assesment.php">Skill Assessment</a>
            </div>
            <div class="js-footer-col">
                <h4>Resources</h4>
                <a href="#">Career Blog</a>
                <a href="#">Interview Prep</a>
                <a href="resume-generator.php">Resume Builder</a>
                <a href="#">Mentorship</a>
            </div>
            <div class="js-footer-col">
                <h4>Company</h4>
                <a href="#">About Us</a>
                <a href="#">Privacy</a>
                <a href="#">Terms</a>
                <a href="#">Contact Support</a>
            </div>
        </div>
        <div class="js-footer-bottom">
            <p>© <?= date('Y') ?> Hireable. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
