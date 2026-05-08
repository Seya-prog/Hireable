<!DOCTYPE html>
<html lang="en">
<head>
    <?php $pageTitle = 'Resume Generator | Hireable'; ?>
    <?php include __DIR__ . '/../components/head.php'; ?>
</head>
<body class="dash-page">
    <?php $activePage = 'resume-generator'; ?>
    <?php include __DIR__ . '/../components/sidebar.php'; ?>

    <main class="dash-main" style="margin-left: 260px;">
        <!-- Header -->
        <div class="resume-header">
            <div>
                <h2 class="page-title">Resume Generator</h2>
                <p class="page-subtitle">Craft a polished, ATS-optimized resume tailored to your target role. Choose a template, customize, and export.</p>
            </div>
        </div>

        <div class="resume-content">
            <!-- Left: Builder -->
            <div class="resume-builder">
                <!-- Template Selection -->
                <section>
                    <div class="resume-section-head">
                        <h3 class="resume-section-title">Choose a Template</h3>
                        <span class="resume-section-badge">4 Available</span>
                    </div>
                    <div class="resume-template-grid">
                        <div class="resume-template-card resume-template-card--active">
                            <div class="resume-template-preview resume-template-preview--executive">
                                <div class="resume-tpl-bar"></div>
                                <div class="resume-tpl-line resume-tpl-line--wide"></div>
                                <div class="resume-tpl-line"></div>
                                <div class="resume-tpl-line resume-tpl-line--short"></div>
                                <div class="resume-tpl-spacer"></div>
                                <div class="resume-tpl-line resume-tpl-line--wide"></div>
                                <div class="resume-tpl-line"></div>
                            </div>
                            <p class="resume-template-name">Executive</p>
                            <span class="resume-template-check material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                        </div>
                        <div class="resume-template-card">
                            <div class="resume-template-preview resume-template-preview--modern">
                                <div class="resume-tpl-sidebar"></div>
                                <div class="resume-tpl-body">
                                    <div class="resume-tpl-line resume-tpl-line--wide"></div>
                                    <div class="resume-tpl-line"></div>
                                    <div class="resume-tpl-line resume-tpl-line--short"></div>
                                </div>
                            </div>
                            <p class="resume-template-name">Modern</p>
                        </div>
                        <div class="resume-template-card">
                            <div class="resume-template-preview resume-template-preview--minimal">
                                <div class="resume-tpl-line resume-tpl-line--wide"></div>
                                <div class="resume-tpl-divider"></div>
                                <div class="resume-tpl-line"></div>
                                <div class="resume-tpl-line resume-tpl-line--short"></div>
                                <div class="resume-tpl-spacer"></div>
                                <div class="resume-tpl-line"></div>
                                <div class="resume-tpl-line resume-tpl-line--short"></div>
                            </div>
                            <p class="resume-template-name">Minimal</p>
                        </div>
                        <div class="resume-template-card">
                            <div class="resume-template-preview resume-template-preview--creative">
                                <div class="resume-tpl-accent"></div>
                                <div class="resume-tpl-line resume-tpl-line--wide"></div>
                                <div class="resume-tpl-line"></div>
                                <div class="resume-tpl-line resume-tpl-line--short"></div>
                                <div class="resume-tpl-spacer"></div>
                                <div class="resume-tpl-line"></div>
                            </div>
                            <p class="resume-template-name">Creative</p>
                        </div>
                    </div>
                </section>

                <!-- Personal Info Section -->
                <section>
                    <div class="resume-section-head">
                        <h3 class="resume-section-title">Personal Information</h3>
                        <button class="resume-autofill-btn">
                            <span class="material-symbols-outlined">auto_fix_high</span>
                            Auto-fill from Profile
                        </button>
                    </div>
                    <div class="resume-form-grid">
                        <div class="resume-field">
                            <label class="resume-label">Full Name</label>
                            <input class="resume-input" type="text" value="John Doe" placeholder="Your full name">
                        </div>
                        <div class="resume-field">
                            <label class="resume-label">Job Title</label>
                            <input class="resume-input" type="text" value="Chief Strategy Officer" placeholder="Target job title">
                        </div>
                        <div class="resume-field">
                            <label class="resume-label">Email</label>
                            <input class="resume-input" type="email" value="john.doe@example.com" placeholder="Email address">
                        </div>
                        <div class="resume-field">
                            <label class="resume-label">Phone</label>
                            <input class="resume-input" type="tel" value="+251 900 000 000" placeholder="Phone number">
                        </div>
                        <div class="resume-field resume-field--full">
                            <label class="resume-label">Professional Summary</label>
                            <textarea class="resume-textarea" rows="4" placeholder="Write a compelling summary...">Seasoned executive with 15+ years leading digital transformation and international expansion initiatives. Proven track record of driving EBITDA growth through strategic innovation and cross-functional leadership.</textarea>
                        </div>
                    </div>
                </section>

                <!-- Experience Section -->
                <section>
                    <div class="resume-section-head">
                        <h3 class="resume-section-title">Work Experience</h3>
                        <button class="resume-add-btn">
                            <span class="material-symbols-outlined">add</span>
                            Add Position
                        </button>
                    </div>
                    <div class="resume-exp-list">
                        <div class="resume-exp-card">
                            <div class="resume-exp-card-header">
                                <div class="resume-exp-card-drag">
                                    <span class="material-symbols-outlined">drag_indicator</span>
                                </div>
                                <div class="resume-exp-card-info">
                                    <h4 class="resume-exp-title">Chief Strategy Officer</h4>
                                    <p class="resume-exp-company">Global Meridian Corp • Jan 2018 – Present</p>
                                </div>
                                <button class="resume-exp-remove">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </div>
                            <ul class="resume-exp-bullets">
                                <li>Leading international expansion initiatives across EMEA markets</li>
                                <li>Managed a multi-disciplinary team of 40+ senior consultants</li>
                                <li>Spearheaded digital transformation resulting in 22% EBITDA growth</li>
                            </ul>
                        </div>
                        <div class="resume-exp-card">
                            <div class="resume-exp-card-header">
                                <div class="resume-exp-card-drag">
                                    <span class="material-symbols-outlined">drag_indicator</span>
                                </div>
                                <div class="resume-exp-card-info">
                                    <h4 class="resume-exp-title">Director of Operations</h4>
                                    <p class="resume-exp-company">Vanguard Logistics • Mar 2014 – Dec 2017</p>
                                </div>
                                <button class="resume-exp-remove">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </div>
                            <ul class="resume-exp-bullets">
                                <li>Optimized supply chain operations reducing costs by 18%</li>
                                <li>Led cross-border integration of 3 acquired logistics firms</li>
                            </ul>
                        </div>
                    </div>
                </section>

                <!-- Skills Section -->
                <section>
                    <div class="resume-section-head">
                        <h3 class="resume-section-title">Skills</h3>
                    </div>
                    <div class="resume-skills-wrap">
                        <div class="resume-skill-tag">Strategic Planning <button class="resume-skill-remove">&times;</button></div>
                        <div class="resume-skill-tag">Digital Transformation <button class="resume-skill-remove">&times;</button></div>
                        <div class="resume-skill-tag">Team Leadership <button class="resume-skill-remove">&times;</button></div>
                        <div class="resume-skill-tag">EMEA Markets <button class="resume-skill-remove">&times;</button></div>
                        <div class="resume-skill-tag">P&L Management <button class="resume-skill-remove">&times;</button></div>
                        <div class="resume-skill-tag">M&A Integration <button class="resume-skill-remove">&times;</button></div>
                        <input class="resume-skill-input" type="text" placeholder="Add a skill...">
                    </div>
                </section>

                <!-- Education Section -->
                <section>
                    <div class="resume-section-head">
                        <h3 class="resume-section-title">Education</h3>
                        <button class="resume-add-btn">
                            <span class="material-symbols-outlined">add</span>
                            Add Education
                        </button>
                    </div>
                    <div class="resume-edu-card">
                        <div class="resume-edu-icon">
                            <span class="material-symbols-outlined">school</span>
                        </div>
                        <div>
                            <h4 class="resume-edu-degree">BSc in Computer Science</h4>
                            <p class="resume-edu-school">Addis Ababa University • 2018 – 2022</p>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Right: Preview & Actions -->
            <div class="resume-sidebar">
                <div class="resume-preview-panel">
                    <div class="resume-preview-header">
                        <h4 class="resume-preview-title">Live Preview</h4>
                        <div class="resume-preview-actions">
                            <button class="resume-preview-btn" title="Zoom In">
                                <span class="material-symbols-outlined">zoom_in</span>
                            </button>
                            <button class="resume-preview-btn" title="Zoom Out">
                                <span class="material-symbols-outlined">zoom_out</span>
                            </button>
                        </div>
                    </div>
                    <div class="resume-preview-doc">
                        <div class="resume-preview-page">
                            <div class="resume-pv-name">John Doe</div>
                            <div class="resume-pv-jobtitle">Chief Strategy Officer</div>
                            <div class="resume-pv-contact">john.doe@example.com • +251 900 000 000</div>
                            <div class="resume-pv-divider"></div>
                            <div class="resume-pv-heading">Professional Summary</div>
                            <div class="resume-pv-text">Seasoned executive with 15+ years leading digital transformation and international expansion initiatives.</div>
                            <div class="resume-pv-heading">Experience</div>
                            <div class="resume-pv-exp-title">Chief Strategy Officer</div>
                            <div class="resume-pv-exp-sub">Global Meridian Corp • 2018 – Present</div>
                            <div class="resume-pv-text">• Leading international expansion across EMEA</div>
                            <div class="resume-pv-text">• 22% EBITDA growth via digital transformation</div>
                            <div class="resume-pv-heading">Skills</div>
                            <div class="resume-pv-text">Strategic Planning • Digital Transformation • Team Leadership • EMEA Markets</div>
                        </div>
                    </div>
                </div>

                <!-- Export Actions -->
                <div class="resume-export-panel">
                    <h4 class="resume-export-title">Export Resume</h4>
                    <button class="resume-export-btn resume-export-btn--primary">
                        <span class="material-symbols-outlined">picture_as_pdf</span>
                        Download PDF
                    </button>
                    <button class="resume-export-btn">
                        <span class="material-symbols-outlined">description</span>
                        Download DOCX
                    </button>
                    <button class="resume-export-btn">
                        <span class="material-symbols-outlined">share</span>
                        Share Link
                    </button>
                </div>

                <!-- AI Suggestions -->
                <div class="resume-ai-panel">
                    <div class="resume-ai-header">
                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">auto_awesome</span>
                        <h4 class="resume-ai-title">AI Suggestions</h4>
                    </div>
                    <div class="resume-ai-tips">
                        <div class="resume-ai-tip">
                            <span class="material-symbols-outlined resume-ai-tip-icon">lightbulb</span>
                            <p>Add quantifiable metrics to your second role for stronger impact.</p>
                        </div>
                        <div class="resume-ai-tip">
                            <span class="material-symbols-outlined resume-ai-tip-icon">lightbulb</span>
                            <p>Consider adding a "Certifications" section — you have 4 verified badges.</p>
                        </div>
                        <div class="resume-ai-tip">
                            <span class="material-symbols-outlined resume-ai-tip-icon">trending_up</span>
                            <p>Your resume scores <strong>82/100</strong> for ATS compatibility.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
