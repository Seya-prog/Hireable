<!DOCTYPE html>
<html lang="en">
<head>
    <?php $pageTitle = 'Find Jobs | Hireable'; ?>
    <?php include __DIR__ . '/../components/head.php'; ?>
</head>
<body class="dash-page">
    <?php $activePage = 'applications'; ?>
    <?php include __DIR__ . '/../components/sidebar.php'; ?>

    <main class="dash-main" style="margin-left: 260px;">
        <div class="app-header">
            <div>
                <h2 class="page-title">Find Jobs</h2>
                <p class="page-subtitle">Discover executive opportunities tailored to your profile.</p>
            </div>
        </div>

        <!-- Search & Filters -->
        <div class="job-search-bar">
            <div class="emp-search-wrap" style="flex: 1;">
                <span class="material-symbols-outlined">search</span>
                <input class="emp-search-input" type="text" placeholder="Search by title, company, or keyword..." style="min-width: 100%; width: 100%;">
            </div>
            <select class="emp-filter-select">
                <option>All Locations</option>
                <option>Addis Ababa</option>
                <option>Nairobi</option>
                <option>Remote</option>
                <option>Lagos</option>
            </select>
            <select class="emp-filter-select">
                <option>All Types</option>
                <option>Full-time</option>
                <option>Part-time</option>
                <option>Contract</option>
            </select>
            <select class="emp-filter-select">
                <option>Experience Level</option>
                <option>Senior</option>
                <option>Executive</option>
                <option>Director</option>
            </select>
        </div>

        <!-- Results -->
        <div class="job-search-results">
            <div class="job-search-count">Showing <strong>24</strong> opportunities matching your profile</div>

            <div class="job-search-list">
                <!-- Job 1 -->
                <a href="application-detail.php" class="job-search-card">
                    <div class="job-search-card-left">
                        <div class="job-search-logo">
                            <span class="material-symbols-outlined" style="color: #695d46;">apartment</span>
                        </div>
                        <div>
                            <h4 class="job-search-title">VP of Product Innovation</h4>
                            <p class="job-search-company">Lumina Global • Addis Ababa</p>
                            <div class="job-search-tags">
                                <span class="emp-cand-skill-tag">Product Strategy</span>
                                <span class="emp-cand-skill-tag">Leadership</span>
                                <span class="emp-cand-skill-tag">SaaS</span>
                            </div>
                        </div>
                    </div>
                    <div class="job-search-card-right">
                        <span class="job-search-match emp-match-badge emp-match--high">92% Match</span>
                        <span class="job-search-salary">$140k – $180k</span>
                        <span class="job-search-posted">Posted 3 days ago</span>
                    </div>
                </a>

                <!-- Job 2 -->
                <a href="application-detail.php" class="job-search-card">
                    <div class="job-search-card-left">
                        <div class="job-search-logo">
                            <span class="material-symbols-outlined" style="color: #695d46;">rocket_launch</span>
                        </div>
                        <div>
                            <h4 class="job-search-title">Chief Product Officer</h4>
                            <p class="job-search-company">Nexus Systems • Remote</p>
                            <div class="job-search-tags">
                                <span class="emp-cand-skill-tag">Product Vision</span>
                                <span class="emp-cand-skill-tag">Executive Leadership</span>
                                <span class="emp-cand-skill-tag">GTM</span>
                            </div>
                        </div>
                    </div>
                    <div class="job-search-card-right">
                        <span class="job-search-match emp-match-badge emp-match--high">88% Match</span>
                        <span class="job-search-salary">$200k – $280k</span>
                        <span class="job-search-posted">Posted 1 week ago</span>
                    </div>
                </a>

                <!-- Job 3 -->
                <a href="application-detail.php" class="job-search-card">
                    <div class="job-search-card-left">
                        <div class="job-search-logo">
                            <span class="material-symbols-outlined" style="color: #695d46;">business</span>
                        </div>
                        <div>
                            <h4 class="job-search-title">Director of Operations</h4>
                            <p class="job-search-company">Evergreen Scale • Nairobi</p>
                            <div class="job-search-tags">
                                <span class="emp-cand-skill-tag">Operations</span>
                                <span class="emp-cand-skill-tag">Process Design</span>
                                <span class="emp-cand-skill-tag">Analytics</span>
                            </div>
                        </div>
                    </div>
                    <div class="job-search-card-right">
                        <span class="job-search-match emp-match-badge emp-match--mid">76% Match</span>
                        <span class="job-search-salary">$110k – $150k</span>
                        <span class="job-search-posted">Posted 2 days ago</span>
                    </div>
                </a>

                <!-- Job 4 -->
                <a href="application-detail.php" class="job-search-card">
                    <div class="job-search-card-left">
                        <div class="job-search-logo">
                            <span class="material-symbols-outlined" style="color: #695d46;">groups</span>
                        </div>
                        <div>
                            <h4 class="job-search-title">Executive Strategy Lead</h4>
                            <p class="job-search-company">Vanguard Partners • Lagos</p>
                            <div class="job-search-tags">
                                <span class="emp-cand-skill-tag">Strategy</span>
                                <span class="emp-cand-skill-tag">Consulting</span>
                                <span class="emp-cand-skill-tag">M&A</span>
                            </div>
                        </div>
                    </div>
                    <div class="job-search-card-right">
                        <span class="job-search-match emp-match-badge emp-match--mid">72% Match</span>
                        <span class="job-search-salary">$130k – $170k</span>
                        <span class="job-search-posted">Posted 5 days ago</span>
                    </div>
                </a>

                <!-- Job 5 -->
                <a href="application-detail.php" class="job-search-card">
                    <div class="job-search-card-left">
                        <div class="job-search-logo">
                            <span class="material-symbols-outlined" style="color: #695d46;">code</span>
                        </div>
                        <div>
                            <h4 class="job-search-title">Head of Engineering</h4>
                            <p class="job-search-company">TechCorp International • Remote</p>
                            <div class="job-search-tags">
                                <span class="emp-cand-skill-tag">Engineering</span>
                                <span class="emp-cand-skill-tag">Architecture</span>
                                <span class="emp-cand-skill-tag">Team Building</span>
                            </div>
                        </div>
                    </div>
                    <div class="job-search-card-right">
                        <span class="job-search-match emp-match-badge emp-match--low">64% Match</span>
                        <span class="job-search-salary">$160k – $220k</span>
                        <span class="job-search-posted">Posted 1 day ago</span>
                    </div>
                </a>
            </div>
        </div>
    </main>
</body>
</html>
