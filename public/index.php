<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hireable — Your Career, Supercharged</title>
    <meta name="description" content="Build your profile, prove your skills with AI-proctored assessments, generate stunning resumes, and connect with top employers. Your dream job starts here.">
    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/landing.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
</head>

<body>
    <!-- ===== NAVBAR ===== -->
    <header id="main-header">
        <nav>
            <div class="logo">Hire<strong style="font-family:'Pinyon Script';">able</strong></div>
            <ul>
                <li><a href="#why">Why Hireable</a></li>
                <li><a href="#how-it-works">How It Works</a></li>
                <li><a href="#jobs">Jobs</a></li>
                <li><a href="#employers">For Employers</a></li>
            </ul>
            <div class="nav-actions">
                <a href="../pages/auth/login.php" class="nav-login">Log In</a>
                <a href="../pages/auth/signup.php" class="nav-signup">Get Started</a>
            </div>
        </nav>
    </header>

    <!-- ===== HERO ===== -->
    <div class="hero">
        <h1>Become <em style="font-family: Verdana, Geneva, Tahoma, sans-serif;">Hireable.</em></h1>
        <p>Get hired by top companies. Build your profile, share your work, and connect with employers. Join our
            community of talented professionals and take your career to the next level.</p>
        <p>
            <a href="../pages/auth/signup.php">Sign Up Now</a>
            <a href="../pages/auth/signup.php?role=employer">I'm Hiring</a>
        </p>
        <img src="assets/images/hero.jpg" alt="Hero image" id="hero-image">
    </div>

    <!-- ===== FEATURES ===== -->
    <section id="features">
        <h2>Secure your dream Job with our</h2>
        <article class="feature">
            <img src="assets/icons/book-open.svg" alt="Skill assessment icon" class="feature-icon">
            <h3>Skill assessment</h3>
            <p>Know your strengths and weaknesses with our AI powered skill assessment. Fill the gap between your skills
                and your jobs.</p>
        </article>
        <article class="feature">
            <img src="assets/icons/file-minus.svg" alt="Resume generator icon" class="feature-icon">
            <h3>Resume generator</h3>
            <p>Create a professional ATS optimized resume that highlights your skills and experience. Stand out from the
                crowd and increase your chances of getting hired.</p>
        </article>
        <article class="feature">
            <img src="assets/icons/briefcase.svg" alt="Job recommendations icon" class="feature-icon">
            <h3>Job recommendations</h3>
            <p>Get personalized job recommendations based on your skills and experience. Find the perfect match for your
                career goals.</p>
        </article>
    </section>

    <!-- ===== WHY HIREABLE ===== -->
    <section id="why" class="why-section">
        <h2>Why <em>Hireable?</em></h2>
        <p class="why-subtitle">The hiring process is broken — you send resumes into the void and hope for the best. We give you the tools to take control.</p>

        <div class="why-grid">
            <div class="why-card">
                <div class="why-icon">
                    <img src="assets/icons/book-open.svg" alt="">
                </div>
                <h3>Know Your Real Strengths</h3>
                <p>Our AI-proctored assessments reveal exactly where you stand — no guesswork. Identify skill gaps before employers do.</p>
            </div>
            <div class="why-card">
                <div class="why-icon">
                    <img src="assets/icons/file-minus.svg" alt="">
                </div>
                <h3>Resumes That Actually Work</h3>
                <p>Stop getting filtered out. Our AI resume builder creates ATS-optimized documents that pass automated screening and impress reviewers.</p>
            </div>
            <div class="why-card">
                <div class="why-icon">
                    <img src="assets/icons/briefcase.svg" alt="">
                </div>
                <h3>Connect Directly With Employers</h3>
                <p>No middlemen. Apply directly, schedule interviews, and track your application status — all from one dashboard.</p>
            </div>
            <div class="why-card">
                <div class="why-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#8a5b1c" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <h3>Verified Skills, Trusted Results</h3>
                <p>Employers trust Hireable assessments because they're proctored with AI integrity monitoring. Your score means something.</p>
            </div>
        </div>
    </section>

    <!-- ===== HOW IT WORKS (Zig-Zag) ===== -->
    <section id="how-it-works" class="how-section">
        <h2>How It <em>Works</em></h2>
        <p class="how-subtitle">From sign-up to hired — in 4 simple steps</p>

        <div class="zigzag">
            <!-- Step 1 — left text -->
            <div class="zigzag-row">
                <div class="zigzag-text">
                    <h3>Sign Up & Build Your Profile</h3>
                    <p>Create your free account in seconds. Add your education, experience, skills, and career preferences to build a comprehensive professional profile.</p>
                    <div class="step-tags">
                        <span>✓ Free forever</span>
                        <span>✓ 2-minute setup</span>
                    </div>
                </div>
                <div class="zigzag-center">
                    <div class="zigzag-number">1</div>
                    <div class="zigzag-line"></div>
                </div>
                <div class="zigzag-visual">
                    <div class="zigzag-icon-box">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#8a5b1c" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                </div>
            </div>

            <!-- Step 2 — right text (reversed) -->
            <div class="zigzag-row zigzag-row--reverse">
                <div class="zigzag-text">
                    <h3>Take Skill Assessments</h3>
                    <p>Prove your expertise with AI-proctored skill assessments. Get scored on technical knowledge, problem-solving, and real-world scenarios.</p>
                    <div class="step-tags">
                        <span>✓ AI-proctored</span>
                        <span>✓ Detailed reports</span>
                        <span>✓ Verified badges</span>
                    </div>
                </div>
                <div class="zigzag-center">
                    <div class="zigzag-number">2</div>
                    <div class="zigzag-line"></div>
                </div>
                <div class="zigzag-visual">
                    <div class="zigzag-icon-box">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#8a5b1c" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    </div>
                </div>
            </div>

            <!-- Step 3 — left text -->
            <div class="zigzag-row">
                <div class="zigzag-text">
                    <h3>Generate Your AI Resume</h3>
                    <p>Let our AI craft a professional, ATS-optimized resume tailored to your target roles. Get real-time suggestions, keyword optimization, and polished formatting.</p>
                    <div class="step-tags">
                        <span>✓ ATS-optimized</span>
                        <span>✓ AI-powered writing</span>
                    </div>
                </div>
                <div class="zigzag-center">
                    <div class="zigzag-number">3</div>
                    <div class="zigzag-line"></div>
                </div>
                <div class="zigzag-visual">
                    <div class="zigzag-icon-box">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#8a5b1c" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    </div>
                </div>
            </div>

            <!-- Step 4 — right text (reversed) -->
            <div class="zigzag-row zigzag-row--reverse">
                <div class="zigzag-text">
                    <h3>Apply & Get Hired</h3>
                    <p>Browse curated job listings, apply with one click using your polished profile, track every application in real-time, and connect directly with employers.</p>
                    <div class="step-tags">
                        <span>✓ One-click apply</span>
                        <span>✓ Real-time tracking</span>
                    </div>
                </div>
                <div class="zigzag-center">
                    <div class="zigzag-number">4</div>
                    <div class="zigzag-line zigzag-line--last"></div>
                </div>
                <div class="zigzag-visual">
                    <div class="zigzag-icon-box">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#8a5b1c" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== LATEST JOBS ===== -->
    <section id="jobs" class="jobs-section">
        <h2>Latest <em>Opportunities</em></h2>
        <p class="jobs-subtitle">Explore roles from companies that value verified skills</p>

        <?php
        // Pull latest active jobs from DB
        require_once __DIR__ . '/../config/database.php';
        $stmtJobs = $pdo->query(
            'SELECT j.*, u.company_name
             FROM jobs j
             JOIN users u ON j.employer_id = u.id
             WHERE j.status = "active"
             ORDER BY j.created_at DESC
             LIMIT 6'
        );
        $latestJobs = $stmtJobs->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <?php if (!empty($latestJobs)): ?>
        <div class="jobs-grid">
            <?php foreach ($latestJobs as $job):
                $salary = '';
                if (!empty($job['salary_min']) && !empty($job['salary_max'])) {
                    $salary = '$' . number_format($job['salary_min']) . ' – $' . number_format($job['salary_max']);
                } elseif (!empty($job['salary_min'])) {
                    $salary = 'From $' . number_format($job['salary_min']);
                }
                $posted = date('M j', strtotime($job['created_at']));
            ?>
            <a href="../pages/employer/job-detail.php?id=<?= $job['id'] ?>" class="job-card">
                <div class="job-card-top">
                    <div class="job-company-avatar"><?= strtoupper(substr($job['company_name'] ?? 'C', 0, 1)) ?></div>
                    <div>
                        <span class="job-company"><?= htmlspecialchars($job['company_name'] ?? '') ?></span>
                        <span class="job-location"><?= htmlspecialchars($job['location'] ?? 'Remote') ?></span>
                    </div>
                </div>
                <h3 class="job-title"><?= htmlspecialchars($job['title']) ?></h3>
                <div class="job-meta">
                    <?php if ($salary): ?><span class="job-salary"><?= $salary ?></span><?php endif; ?>
                    <span class="job-type"><?= ucfirst(str_replace('_', ' ', $job['employment_type'] ?? 'full-time')) ?></span>
                    <span class="job-date"><?= $posted ?></span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="jobs-empty">
            <p>No open positions yet — check back soon!</p>
        </div>
        <?php endif; ?>

        <div class="jobs-view-more">
            <a href="../pages/employee/job-search.php">View All Jobs →</a>
        </div>
    </section>

    <!-- ===== FOR EMPLOYERS ===== -->
    <section id="employers" class="employers-section">
        <h2>For <em>Employers</em></h2>
        <p class="employers-subtitle">Stop sifting through hundreds of unqualified resumes. Hireable pre-verifies candidate skills with AI-proctored assessments, giving you a shortlist of truly qualified professionals.</p>
        <ul class="employers-list">
            <li>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#8a5b1c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Post jobs and attract top talent
            </li>
            <li>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#8a5b1c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                AI-proctored skill assessments with integrity scoring
            </li>
            <li>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#8a5b1c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Built-in interview scheduling and feedback tools
            </li>
            <li>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#8a5b1c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Candidate analytics and pipeline management
            </li>
        </ul>
        <a href="../pages/auth/signup.php?role=employer" class="employers-cta">Start Hiring →</a>
    </section>

    <!-- ===== CTA ===== -->
    <section class="cta-section">
        <h2>Ready to become <em>Hireable?</em></h2>
        <p>Join a new generation of professionals who take control of their career journey. Sign up today — it's free, forever.</p>
        <a href="../pages/auth/signup.php" class="cta-btn-main">Create Your Free Account →</a>
    </section>

    <!-- ===== FOOTER ===== -->
    <footer>
        <div class="footer-content">
            <div class="footer-brand">
                <div class="logo">Hire<strong style="font-family:'Pinyon Script';">able</strong></div>
                <p>AI-powered career platform that helps professionals prove their skills, build stunning resumes, and connect with top employers.</p>
            </div>
            <div class="footer-links">
                <h4>Platform</h4>
                <ul>
                    <li><a href="#how-it-works">How It Works</a></li>
                    <li><a href="#jobs">Browse Jobs</a></li>
                    <li><a href="#why">Why Hireable</a></li>
                </ul>
            </div>
            <div class="footer-links">
                <h4>Get Started</h4>
                <ul>
                    <li><a href="../pages/auth/signup.php">Sign Up Free</a></li>
                    <li><a href="../pages/auth/login.php">Log In</a></li>
                    <li><a href="../pages/auth/signup.php?role=employer">For Employers</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 Hireable. All rights reserved.</p>
        </div>
    </footer>

    <script>
    // Scroll header
    const header = document.getElementById('main-header');
    window.addEventListener('scroll', () => {
        header.classList.toggle('scrolled', window.scrollY > 40);
    });

    // Animate on scroll
    const animEls = document.querySelectorAll('.step, .why-card, .job-card');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) entry.target.classList.add('visible');
        });
    }, { threshold: 0.12 });
    animEls.forEach(el => observer.observe(el));

    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', e => {
            e.preventDefault();
            const t = document.querySelector(a.getAttribute('href'));
            if (t) t.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });
    </script>
</body>
</html>