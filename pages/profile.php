<!DOCTYPE html>
<html lang="en">
<head>
    <?php $pageTitle = 'Edit Profile | Hireable'; ?>
    <?php include __DIR__ . '/../components/head.php'; ?>
</head>
<body class="dash-page">
    <?php $activePage = 'profile'; ?>
    <?php include __DIR__ . '/../components/sidebar.php'; ?>

    <main class="dash-main" style="margin-left: 260px;">
        <!-- Top App Bar -->
        <div class="profile-topbar">
            <div class="profile-topbar-left">
                <span class="profile-topbar-brand">Hireable</span>
                <nav class="profile-topbar-nav">
                    <a href="job-search.php">Directorships</a>
                    <a href="job-search.php">Board Roles</a>
                    <a href="job-search.php">Advisory</a>
                </nav>
            </div>
            <div class="profile-topbar-right">
                <a class="profile-find-jobs-btn" href="job-search.php">Find Jobs</a>
                <div class="profile-topbar-icons">
                    <span class="material-symbols-outlined profile-topbar-icon">notifications</span>
                    <span class="material-symbols-outlined profile-topbar-icon">mail</span>
                    <div class="profile-topbar-avatar">JD</div>
                </div>
            </div>
        </div>

        <!-- Edit Profile Form -->
        <div class="profile-canvas">
            <div class="profile-canvas-header">
                <h2 class="page-title profile-canvas-title">Edit Professional Profile</h2>
                <p class="page-subtitle">Refine your executive presence. This information is used to match you with high-value board and directorship opportunities.</p>
            </div>

            <form class="profile-form-sections">
                <!-- Section 1: Personal Information -->
                <section class="profile-section">
                    <div class="profile-section-label">
                        <h3 class="profile-section-title">Personal Information</h3>
                        <p class="profile-section-desc">Your basic identity details visible to hiring partners.</p>
                    </div>
                    <div class="profile-section-fields">
                        <div class="profile-field-grid-2">
                            <div class="profile-field-group">
                                <label class="profile-field-label">First Name</label>
                                <input class="profile-field-input profile-field-input--bold" type="text" name="firstName" value="John">
                            </div>
                            <div class="profile-field-group">
                                <label class="profile-field-label">Last Name</label>
                                <input class="profile-field-input profile-field-input--bold" type="text" name="lastName" value="Doe">
                            </div>
                            <div class="profile-field-group">
                                <label class="profile-field-label">Email</label>
                                <input class="profile-field-input" type="email" name="email" value="john.doe@example.com">
                            </div>
                            <div class="profile-field-group">
                                <label class="profile-field-label">Phone</label>
                                <input class="profile-field-input" type="tel" name="phone" value="+251 900 000 000">
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Section 2: Location -->
                <section class="profile-section">
                    <div class="profile-section-label">
                        <h3 class="profile-section-title">Location Settings</h3>
                        <p class="profile-section-desc">Specify your primary residence for regional opportunities.</p>
                    </div>
                    <div class="profile-section-fields">
                        <div class="profile-field-grid-2">
                            <div class="profile-field-group">
                                <label class="profile-field-label">Country</label>
                                <select class="profile-field-input profile-field-select">
                                    <option>Ethiopia</option>
                                    <option>Kenya</option>
                                    <option>Rwanda</option>
                                    <option>Uganda</option>
                                    <option>Tanzania</option>
                                    <option>Nigeria</option>
                                    <option>South Africa</option>
                                </select>
                            </div>
                            <div class="profile-field-group">
                                <label class="profile-field-label">City</label>
                                <select class="profile-field-input profile-field-select">
                                    <option>Addis Ababa</option>
                                    <option>Nairobi</option>
                                    <option>Kigali</option>
                                    <option>Lagos</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Section 2: Professional Summary -->
                <section class="profile-section">
                    <div class="profile-section-label">
                        <h3 class="profile-section-title">Professional Summary</h3>
                        <p class="profile-section-desc">A concise statement of your leadership philosophy and core competencies.</p>
                    </div>
                    <div class="profile-section-fields">
                        <div class="profile-field-group">
                            <label class="profile-field-label">About You</label>
                            <textarea class="profile-textarea" rows="6" placeholder="Draft your executive summary here..."></textarea>
                        </div>
                    </div>
                </section>

                <!-- Section 3: Work Experience -->
                <section class="profile-section">
                    <div class="profile-section-label">
                        <h3 class="profile-section-title">Work Experience</h3>
                        <p class="profile-section-desc">Detail your most significant professional milestones and impact.</p>
                        <button class="profile-add-btn" type="button">
                            <span class="material-symbols-outlined">add</span>
                            Add Experience
                        </button>
                    </div>
                    <div class="profile-section-fields profile-experience-list">
                        <!-- Experience Card 1 -->
                        <div class="profile-exp-card profile-exp-card--primary">
                            <div class="profile-field-grid-2">
                                <div class="profile-field-group">
                                    <label class="profile-field-label">Job Title</label>
                                    <input class="profile-field-input profile-field-input--bold" type="text" value="Chief Strategy Officer">
                                </div>
                                <div class="profile-field-group">
                                    <label class="profile-field-label">Company</label>
                                    <input class="profile-field-input" type="text" value="Global Meridian Corp">
                                </div>
                                <div class="profile-field-group">
                                    <label class="profile-field-label">Start Date</label>
                                    <input class="profile-field-input" type="text" value="Jan 2018">
                                </div>
                                <div class="profile-field-group">
                                    <label class="profile-field-label">End Date</label>
                                    <input class="profile-field-input" type="text" value="Present">
                                </div>
                            </div>
                            <div class="profile-field-group">
                                <label class="profile-field-label">Responsibilities</label>
                                <textarea class="profile-field-input profile-field-textarea" rows="4">Leading international expansion initiatives across EMEA markets. Managed a multi-disciplinary team of 40+ senior consultants. Spearheaded the digital transformation strategy resulting in 22% EBITDA growth.</textarea>
                            </div>
                        </div>
                        <!-- Experience Card 2 -->
                        <div class="profile-exp-card profile-exp-card--secondary">
                            <div class="profile-field-grid-2">
                                <div class="profile-field-group">
                                    <label class="profile-field-label">Job Title</label>
                                    <input class="profile-field-input profile-field-input--bold" type="text" value="Director of Operations">
                                </div>
                                <div class="profile-field-group">
                                    <label class="profile-field-label">Company</label>
                                    <input class="profile-field-input" type="text" value="Vanguard Logistics">
                                </div>
                            </div>
                            <button class="profile-remove-btn" type="button">
                                <span class="material-symbols-outlined">delete</span>
                                Remove Position
                            </button>
                        </div>
                    </div>
                </section>

                <!-- Section 4: Education -->
                <section class="profile-section">
                    <div class="profile-section-label">
                        <h3 class="profile-section-title">Education</h3>
                        <p class="profile-section-desc">Your academic background and qualifications.</p>
                        <button class="profile-add-btn" type="button">
                            <span class="material-symbols-outlined">add</span>
                            Add Education
                        </button>
                    </div>
                    <div class="profile-section-fields">
                        <div class="profile-exp-card profile-exp-card--primary">
                            <div class="profile-field-grid-2">
                                <div class="profile-field-group">
                                    <label class="profile-field-label">School / University</label>
                                    <input class="profile-field-input profile-field-input--bold" type="text" value="Addis Ababa University">
                                </div>
                                <div class="profile-field-group">
                                    <label class="profile-field-label">Degree</label>
                                    <input class="profile-field-input" type="text" value="BSc in Computer Science">
                                </div>
                                <div class="profile-field-group">
                                    <label class="profile-field-label">Start Year</label>
                                    <input class="profile-field-input" type="number" value="2018" min="1950" max="2099">
                                </div>
                                <div class="profile-field-group">
                                    <label class="profile-field-label">End Year</label>
                                    <input class="profile-field-input" type="number" value="2022" min="1950" max="2099">
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Section 5: Skills & Links -->
                <section class="profile-section">
                    <div class="profile-section-label">
                        <h3 class="profile-section-title">Skills & Links</h3>
                        <p class="profile-section-desc">Highlight your key competencies and professional presence.</p>
                    </div>
                    <div class="profile-section-fields">
                        <div class="profile-field-grid-2">
                            <div class="profile-field-group">
                                <label class="profile-field-label">Skills (comma separated)</label>
                                <input class="profile-field-input" type="text" value="UI/UX, Figma, HTML, CSS, JavaScript">
                            </div>
                            <div class="profile-field-group">
                                <label class="profile-field-label">Portfolio URL</label>
                                <input class="profile-field-input" type="url" placeholder="https://your-portfolio.com">
                            </div>
                            <div class="profile-field-group">
                                <label class="profile-field-label">LinkedIn URL</label>
                                <input class="profile-field-input" type="url" placeholder="https://linkedin.com/in/username">
                            </div>
                            <div class="profile-field-group">
                                <label class="profile-field-label">GitHub URL</label>
                                <input class="profile-field-input" type="url" placeholder="https://github.com/username">
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Footer Actions -->
                <div class="profile-form-actions">
                    <button class="profile-discard-btn" type="button">Discard Changes</button>
                    <button class="profile-save-btn" type="submit">Save Changes</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>