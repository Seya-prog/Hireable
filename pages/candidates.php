<!DOCTYPE html>
<html lang="en">
<head>
    <?php $pageTitle = 'Candidates | Hireable Employer'; ?>
    <?php include __DIR__ . '/../components/head.php'; ?>
</head>
<body class="dash-page">
    <?php $activePage = 'candidates'; ?>
    <?php include __DIR__ . '/../components/employer-sidebar.php'; ?>

    <main class="dash-main" style="margin-left: 260px;">
        <div class="emp-header">
            <div>
                <h2 class="page-title">Candidates</h2>
                <p class="page-subtitle">Review, shortlist, and manage your applicant pipeline.</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="emp-filters">
            <div class="emp-filter-tabs">
                <button class="emp-filter-tab emp-filter-tab--active">All <span class="emp-filter-count">143</span></button>
                <button class="emp-filter-tab">Shortlisted <span class="emp-filter-count">24</span></button>
                <button class="emp-filter-tab">Interviewing <span class="emp-filter-count">18</span></button>
                <button class="emp-filter-tab">Offered <span class="emp-filter-count">3</span></button>
                <button class="emp-filter-tab">Rejected <span class="emp-filter-count">12</span></button>
            </div>
            <div class="emp-filter-actions">
                <div class="emp-search-wrap">
                    <span class="material-symbols-outlined">search</span>
                    <input class="emp-search-input" type="text" placeholder="Search candidates...">
                </div>
                <select class="emp-filter-select">
                    <option>All Positions</option>
                    <option>VP of Product Innovation</option>
                    <option>Senior Software Engineer</option>
                    <option>Marketing Lead</option>
                    <option>Data Analyst</option>
                </select>
            </div>
        </div>

        <!-- Candidate Cards Grid -->
        <div class="emp-cand-grid">
            <?php
            $candidateName = 'Sarah M.'; $candidateEmail = 'sarah.m@email.com'; $initials = 'SM';
            $appliedRole = 'VP of Product Innovation'; $matchPercent = 92; $matchLevel = 'high';
            $skills = ['Product Strategy', 'Leadership', 'Agile', 'Data Analytics'];
            $stage = 'Interview'; $stageType = 'interview';
            include __DIR__ . '/../components/candidate-card.php';

            $candidateName = 'Aisha L.'; $candidateEmail = 'aisha.l@email.com'; $initials = 'AL';
            $appliedRole = 'Marketing Lead'; $matchPercent = 88; $matchLevel = 'high';
            $skills = ['Digital Marketing', 'SEO', 'Brand Strategy', 'Analytics'];
            $stage = 'Applied'; $stageType = 'applied';
            include __DIR__ . '/../components/candidate-card.php';

            $candidateName = 'Daniel K.'; $candidateEmail = 'daniel.k@email.com'; $initials = 'DK';
            $appliedRole = 'Senior Software Engineer'; $matchPercent = 78; $matchLevel = 'mid';
            $skills = ['React', 'Node.js', 'TypeScript', 'AWS'];
            $stage = 'Screening'; $stageType = 'screening';
            include __DIR__ . '/../components/candidate-card.php';

            $candidateName = 'James T.'; $candidateEmail = 'james.t@email.com'; $initials = 'JT';
            $appliedRole = 'Data Analyst'; $matchPercent = 64; $matchLevel = 'low';
            $skills = ['SQL', 'Python', 'Tableau'];
            $stage = 'Offer'; $stageType = 'offer';
            include __DIR__ . '/../components/candidate-card.php';

            $candidateName = 'Miriam W.'; $candidateEmail = 'miriam.w@email.com'; $initials = 'MW';
            $appliedRole = 'VP of Product Innovation'; $matchPercent = 85; $matchLevel = 'high';
            $skills = ['Roadmapping', 'User Research', 'Cross-functional'];
            $stage = 'Screening'; $stageType = 'screening';
            include __DIR__ . '/../components/candidate-card.php';

            $candidateName = 'Tariku B.'; $candidateEmail = 'tariku.b@email.com'; $initials = 'TB';
            $appliedRole = 'Senior Software Engineer'; $matchPercent = 71; $matchLevel = 'mid';
            $skills = ['Java', 'Spring Boot', 'Microservices'];
            $stage = 'Applied'; $stageType = 'applied';
            include __DIR__ . '/../components/candidate-card.php';
            ?>
        </div>
    </main>
</body>
</html>
