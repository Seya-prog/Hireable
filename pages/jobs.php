<!DOCTYPE html>
<html lang="en">
<head>
    <?php $pageTitle = 'Job Postings | Hireable Employer'; ?>
    <?php include __DIR__ . '/../components/head.php'; ?>
</head>
<body class="dash-page">
    <?php $activePage = 'jobs'; ?>
    <?php include __DIR__ . '/../components/employer-sidebar.php'; ?>

    <main class="dash-main" style="margin-left: 260px;">
        <div class="emp-header">
            <div>
                <h2 class="page-title">Job Postings</h2>
                <p class="page-subtitle">Create, manage, and track all your open positions.</p>
            </div>
            <a href="job-create.php" class="emp-cta-btn">
                <span class="material-symbols-outlined">add</span>
                Create New Job
            </a>
        </div>

        <!-- Filters -->
        <div class="emp-filters">
            <div class="emp-filter-tabs">
                <button class="emp-filter-tab emp-filter-tab--active">All Jobs <span class="emp-filter-count">12</span></button>
                <button class="emp-filter-tab">Active <span class="emp-filter-count">8</span></button>
                <button class="emp-filter-tab">Draft <span class="emp-filter-count">2</span></button>
                <button class="emp-filter-tab">Closed <span class="emp-filter-count">2</span></button>
            </div>
            <div class="emp-filter-actions">
                <div class="emp-search-wrap">
                    <span class="material-symbols-outlined">search</span>
                    <input class="emp-search-input" type="text" placeholder="Search jobs...">
                </div>
            </div>
        </div>

        <!-- Job Cards Grid -->
        <div class="emp-job-grid">
            <?php
            $jobTitle = 'VP of Product Innovation'; $department = 'Product'; $location = 'Addis Ababa';
            $jobStatus = 'Active'; $statusType = 'active'; $applicants = 34; $posted = '3 days ago';
            include __DIR__ . '/../components/job-card.php';

            $jobTitle = 'Senior Software Engineer'; $department = 'Engineering'; $location = 'Remote';
            $jobStatus = 'Active'; $statusType = 'active'; $applicants = 52; $posted = '1 week ago';
            include __DIR__ . '/../components/job-card.php';

            $jobTitle = 'Marketing Lead'; $department = 'Marketing'; $location = 'Nairobi';
            $jobStatus = 'Active'; $statusType = 'active'; $applicants = 18; $posted = '2 days ago';
            include __DIR__ . '/../components/job-card.php';

            $jobTitle = 'Data Analyst'; $department = 'Analytics'; $location = 'Addis Ababa';
            $jobStatus = 'Active'; $statusType = 'active'; $applicants = 27; $posted = '5 days ago';
            include __DIR__ . '/../components/job-card.php';

            $jobTitle = 'UX Designer'; $department = 'Design'; $location = 'Remote';
            $jobStatus = 'Draft'; $statusType = 'draft'; $applicants = 0; $posted = 'Not published';
            include __DIR__ . '/../components/job-card.php';

            $jobTitle = 'Operations Manager'; $department = 'Operations'; $location = 'Lagos';
            $jobStatus = 'Closed'; $statusType = 'closed'; $applicants = 41; $posted = '3 weeks ago';
            include __DIR__ . '/../components/job-card.php';
            ?>
        </div>
    </main>
</body>
</html>
