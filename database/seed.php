<?php
/**
 * Database Seeder — Populates all tables with realistic test data
 * Run: php database/seed.php
 */
require_once __DIR__ . '/../config/database.php';

echo "🌱 Seeding database...\n";

// ── USERS ──────────────────────────────────────────────
// Password: "password123" for all
$hash = password_hash('password123', PASSWORD_BCRYPT);

$pdo->exec("DELETE FROM users WHERE id > 0");

$pdo->exec("INSERT INTO users (id, first_name, last_name, email, password, role, phone, country, city, headline, bio, years_of_experience, linkedin_url, portfolio_url, github_url, company_name, company_industry, company_size, company_website, company_logo, company_description, company_location, availability, job_preference) VALUES
(1,'Abebe','Tadesse','abebe@hireable.com','$hash','employer','+251911223344','Ethiopia','Addis Ababa',NULL,NULL,NULL,NULL,NULL,NULL,'TechFlow Solutions','Technology','51-200','https://techflow.et','https://logo.clearbit.com/digitalocean.com','Leading Ethiopian software company building cloud solutions for East African businesses.','Addis Ababa',NULL,NULL),
(2,'Sara','Mengistu','sara@hireable.com','$hash','employee','+251922334455','Ethiopia','Addis Ababa','Full-Stack Developer | 5 Years Experience','Passionate full-stack developer with expertise in React, Node.js, and PHP. Built scalable platforms serving 100K+ users across East Africa. Strong background in fintech and e-commerce solutions.',5,'https://linkedin.com/in/saramengistu','https://sara.dev','https://github.com/saramengistu',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'immediate','full-time'),
(3,'Daniel','Kebede','daniel@hireable.com','$hash','employee','+251933445566','Ethiopia','Addis Ababa','Senior Data Analyst | Python & SQL Expert','Data-driven analyst with 4 years of experience transforming raw data into actionable business insights. Proficient in Python, SQL, Tableau, and statistical modeling.',4,'https://linkedin.com/in/danielkebede',NULL,'https://github.com/danielkebede',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2_weeks','any'),
(4,'Miriam','Wolde','miriam@hireable.com','$hash','employee','+251944556677','Kenya','Nairobi','UX Designer | Human-Centered Design','Creative UX designer passionate about crafting intuitive digital experiences. 3 years of experience in user research, wireframing, and prototyping for mobile-first products.',3,'https://linkedin.com/in/miriamwolde','https://miriam.design',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'immediate','remote'),
(5,'Tariku','Bekele','tariku@hireable.com','$hash','employee','+251955667788','Ethiopia','Dire Dawa','DevOps Engineer | AWS & Docker','Infrastructure specialist with 6 years building CI/CD pipelines and managing cloud deployments. AWS certified with experience in Kubernetes and Terraform.',6,'https://linkedin.com/in/tarikubekele',NULL,'https://github.com/tarikubekele',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1_month','contract'),
(6,'Aisha','Lemma','aisha@hireable.com','$hash','employee','+251966778899','Ethiopia','Hawassa','Marketing Lead | Digital Strategy','Strategic marketing professional with 4 years driving growth for tech startups. Expert in SEO, content strategy, and performance marketing across African markets.',4,'https://linkedin.com/in/aishalemma',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'immediate','full-time'),
(7,'Kidist','Hailu','kidist@hireable.com','$hash','employer','+251977889900','Kenya','Nairobi',NULL,NULL,NULL,NULL,NULL,NULL,'Innovate Africa Labs','Technology','11-50','https://innovateafrica.io','https://logo.clearbit.com/andela.com','Pan-African innovation lab focused on AI and machine learning solutions for healthcare and agriculture.','Nairobi',NULL,NULL),
(8,'James','Ochieng','james@hireable.com','$hash','employee','+254711223344','Kenya','Nairobi','Backend Engineer | Java & Spring Boot','Experienced backend developer specializing in enterprise Java applications. Built microservices handling 1M+ daily transactions for financial institutions.',5,NULL,NULL,'https://github.com/jamesochieng',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2_weeks','full-time')
");

echo "  ✓ 8 users (2 employers, 6 employees)\n";

// ── SKILLS ─────────────────────────────────────────────
$pdo->exec("INSERT INTO skills (user_id, skill_name, proficiency) VALUES
(2,'React','advanced'),(2,'Node.js','advanced'),(2,'PHP','advanced'),(2,'TypeScript','intermediate'),(2,'MySQL','advanced'),(2,'Docker','intermediate'),(2,'Git','advanced'),(2,'REST APIs','advanced'),
(3,'Python','advanced'),(3,'SQL','expert'),(3,'Tableau','advanced'),(3,'Excel','advanced'),(3,'Data Modeling','intermediate'),(3,'Machine Learning','beginner'),
(4,'Figma','expert'),(4,'User Research','advanced'),(4,'Wireframing','advanced'),(4,'Prototyping','advanced'),(4,'HTML','intermediate'),(4,'CSS','intermediate'),
(5,'AWS','advanced'),(5,'Docker','expert'),(5,'Kubernetes','advanced'),(5,'Terraform','intermediate'),(5,'Linux','advanced'),(5,'CI/CD','expert'),(5,'Python','intermediate'),
(6,'SEO','advanced'),(6,'Content Strategy','advanced'),(6,'Google Analytics','advanced'),(6,'Social Media','intermediate'),(6,'Brand Strategy','advanced'),
(8,'Java','advanced'),(8,'Spring Boot','advanced'),(8,'Microservices','advanced'),(8,'PostgreSQL','advanced'),(8,'Kafka','intermediate'),(8,'Redis','intermediate')
");
echo "  ✓ Skills\n";

// ── WORK EXPERIENCE ────────────────────────────────────
$pdo->exec("INSERT INTO work_experience (user_id, job_title, company, location, start_date, end_date, is_current, description) VALUES
(2,'Full-Stack Developer','Gebeya Inc','Addis Ababa','2022-03-01',NULL,1,'Building e-commerce platforms serving Ethiopian businesses. Leading frontend architecture migration from jQuery to React.'),
(2,'Junior Developer','iCog Labs','Addis Ababa','2020-01-15','2022-02-28',0,'Developed AI-powered chatbots and contributed to open-source robotics projects.'),
(3,'Data Analyst','Ethio Telecom','Addis Ababa','2023-01-01',NULL,1,'Analyzing subscriber data to optimize network performance and reduce churn. Built automated reporting dashboards.'),
(3,'Junior Analyst','Dashen Bank','Addis Ababa','2021-06-01','2022-12-31',0,'Created financial reports and conducted risk analysis for the credit department.'),
(4,'UX Designer','Andela','Nairobi','2023-06-01',NULL,1,'Designing mobile experiences for pan-African fintech products. Conducting user research across 5 countries.'),
(4,'UI Designer Intern','Safaricom','Nairobi','2022-09-01','2023-05-31',0,'Supported the M-Pesa design team with interface improvements and usability testing.'),
(5,'DevOps Engineer','Flutterwave','Lagos','2022-01-01',NULL,1,'Managing cloud infrastructure serving 100M+ API requests monthly across Africa.'),
(5,'System Administrator','Addis Ababa University','Addis Ababa','2019-06-01','2021-12-31',0,'Maintained campus network and server infrastructure for 50,000+ students.'),
(6,'Marketing Lead','Chapa (Fintech)','Addis Ababa','2023-03-01',NULL,1,'Leading digital marketing strategy driving 300% growth in merchant onboarding.'),
(8,'Backend Engineer','Cellulant','Nairobi','2022-04-01',NULL,1,'Building payment processing microservices handling cross-border transactions.'),
(8,'Software Developer','Twiga Foods','Nairobi','2020-01-01','2022-03-31',0,'Developed supply chain management APIs connecting farmers to urban retailers.')
");
echo "  ✓ Work experience\n";

// ── EDUCATION ──────────────────────────────────────────
$pdo->exec("INSERT INTO education (user_id, institution, degree, field_of_study, start_date, end_date) VALUES
(2,'Addis Ababa University','BSc','Computer Science','2016-09-01','2020-07-15'),
(3,'Addis Ababa Institute of Technology','BSc','Statistics','2017-09-01','2021-07-15'),
(4,'University of Nairobi','BA','Design','2019-09-01','2023-06-30'),
(5,'Bahir Dar University','BSc','Computer Engineering','2015-09-01','2019-07-15'),
(6,'Hawassa University','BA','Marketing Management','2017-09-01','2021-07-15'),
(8,'Kenyatta University','BSc','Computer Science','2016-09-01','2020-07-15')
");
echo "  ✓ Education\n";

// ── CERTIFICATIONS ─────────────────────────────────────
$pdo->exec("INSERT INTO certifications (user_id, name, issuing_organization, issue_date, expiry_date) VALUES
(2,'AWS Cloud Practitioner','Amazon Web Services','2023-06-15','2026-06-15'),
(3,'Google Data Analytics Certificate','Google','2023-03-10',NULL),
(5,'AWS Solutions Architect Associate','Amazon Web Services','2023-01-20','2026-01-20'),
(5,'Certified Kubernetes Administrator','CNCF','2023-09-05','2026-09-05'),
(8,'Oracle Certified Java Programmer','Oracle','2022-11-15',NULL)
");
echo "  ✓ Certifications\n";

// ── JOBS ───────────────────────────────────────────────
$pdo->exec("INSERT INTO jobs (id, employer_id, title, department, location, job_type, experience_level, salary_min, salary_max, description, requirements, responsibilities, skills_required, application_deadline, status, created_at) VALUES
(1,1,'Senior Full-Stack Developer','Engineering','Addis Ababa','full-time','senior',80000,120000,'We are looking for a Senior Full-Stack Developer to lead our product engineering team. You will architect and build scalable web applications serving businesses across East Africa.','5+ years of full-stack development experience\nStrong proficiency in React and Node.js\nExperience with cloud services (AWS/GCP)\nFamiliarity with CI/CD pipelines','Lead frontend and backend development\nMentor junior developers\nDesign system architecture\nConduct code reviews','React, Node.js, TypeScript, AWS, Docker, MySQL','2026-06-30','active',NOW() - INTERVAL 3 DAY),
(2,1,'Data Analyst','Analytics','Addis Ababa','full-time','mid',40000,65000,'Join our data team to help drive business decisions through insights. You will work with large datasets to uncover patterns and build dashboards.','3+ years of data analysis experience\nProficiency in SQL and Python\nExperience with Tableau or Power BI','Analyze business metrics\nBuild automated reports\nCollaborate with product team\nPresent findings to stakeholders','Python, SQL, Tableau, Excel, Data Modeling','2026-07-15','active',NOW() - INTERVAL 5 DAY),
(3,1,'UX Designer','Design','Remote','remote','mid',45000,70000,'We need a talented UX Designer to create intuitive experiences for our products. You will conduct user research and translate insights into beautiful interfaces.','3+ years of UX design experience\nProficiency in Figma\nPortfolio demonstrating user-centered design process','Conduct user research\nCreate wireframes and prototypes\nDesign responsive interfaces\nCollaborate with engineering','Figma, User Research, Wireframing, Prototyping, HTML, CSS','2026-07-01','active',NOW() - INTERVAL 2 DAY),
(4,1,'DevOps Engineer','Infrastructure','Addis Ababa','full-time','senior',75000,110000,'Looking for a DevOps Engineer to build and maintain our cloud infrastructure. You will be responsible for CI/CD, monitoring, and system reliability.','5+ years of DevOps/SRE experience\nAWS or GCP certification\nExperience with Kubernetes and Terraform','Manage cloud infrastructure\nBuild CI/CD pipelines\nImplement monitoring and alerting\nEnsure 99.9% uptime','AWS, Docker, Kubernetes, Terraform, Linux, CI/CD','2026-06-15','active',NOW() - INTERVAL 7 DAY),
(5,1,'Marketing Manager','Marketing','Nairobi','full-time','mid',35000,55000,'Lead our marketing efforts across East Africa. You will develop and execute strategies to grow our brand and user base.','3+ years in digital marketing\nExperience in B2B SaaS marketing\nStrong analytical skills','Develop marketing strategy\nManage ad campaigns\nTrack and report KPIs\nManage social media presence','SEO, Content Strategy, Google Analytics, Social Media','2026-07-30','active',NOW() - INTERVAL 1 DAY),
(6,1,'Junior Frontend Developer','Engineering','Addis Ababa','full-time','entry',20000,35000,'Great opportunity for a passionate junior developer to join our frontend team and learn from senior engineers.','BSc in Computer Science or related\nBasic knowledge of HTML, CSS, JavaScript\nEagerness to learn','Build UI components\nFix bugs\nWrite unit tests\nParticipate in code reviews','HTML, CSS, JavaScript, React, Git',NULL,'draft',NOW() - INTERVAL 10 DAY),
(7,7,'AI/ML Engineer','AI Research','Nairobi','full-time','senior',90000,140000,'Join our AI research lab building machine learning models for healthcare diagnostics in Africa.','MSc in CS/ML or equivalent\n3+ years ML experience\nPublished research is a plus','Build and train ML models\nDeploy models to production\nConduct research\nCollaborate with healthcare partners','Python, TensorFlow, PyTorch, NLP, Computer Vision','2026-08-01','active',NOW() - INTERVAL 4 DAY),
(8,7,'Product Manager','Product','Nairobi','full-time','mid',50000,80000,'We need a Product Manager to drive our agricultural tech product roadmap and bring data-driven solutions to smallholder farmers.','3+ years PM experience\nExperience with agile methodologies\nPassion for African tech','Define product strategy\nManage backlog\nConduct user interviews\nCoordinate with engineering','Product Strategy, Agile, User Research, Data Analysis','2026-07-20','active',NOW() - INTERVAL 6 DAY)
");
echo "  ✓ 8 jobs\n";

// ── APPLICATIONS ───────────────────────────────────────
$pdo->exec("INSERT INTO applications (id, job_id, employee_id, cover_letter, status, applied_at) VALUES
(1,1,2,'I am excited to apply for the Senior Full-Stack Developer role. With 5 years of experience building scalable platforms using React and Node.js, I am confident I can contribute significantly to your engineering team.','interview',NOW() - INTERVAL 3 DAY),
(2,2,3,'As a data analyst with strong SQL and Python skills, I am thrilled to apply for this position. My experience at Ethio Telecom has prepared me well for this role.','screening',NOW() - INTERVAL 4 DAY),
(3,3,4,'I would love to bring my UX design expertise to TechFlow. My experience designing for M-Pesa and Andela has given me deep insight into African user needs.','interview',NOW() - INTERVAL 2 DAY),
(4,4,5,'With 6 years of DevOps experience and AWS certification, I am well-suited for this infrastructure role. I currently manage systems handling 100M+ requests at Flutterwave.','offer',NOW() - INTERVAL 6 DAY),
(5,5,6,'I am passionate about growing tech brands across East Africa. My marketing work at Chapa drove 300% growth and I want to bring that energy to TechFlow.','applied',NOW() - INTERVAL 1 DAY),
(6,1,8,'I bring strong backend experience from Cellulant where I build payment microservices. I am eager to expand into full-stack development with your team.','screening',NOW() - INTERVAL 5 DAY),
(7,7,2,'My full-stack background and interest in AI make me a great fit for this ML Engineering role. I have been studying TensorFlow independently.','applied',NOW() - INTERVAL 2 DAY),
(8,2,8,'While primarily a backend engineer, my strong SQL and data skills make me a strong candidate for the data analyst position.','applied',NOW() - INTERVAL 3 DAY),
(9,8,6,'My marketing experience gives me a unique product perspective. I understand user acquisition and retention from a data-driven standpoint.','screening',NOW() - INTERVAL 4 DAY),
(10,3,2,'I have strong frontend skills and a keen eye for design. I would love to combine my technical and creative abilities as a UX Designer.','applied',NOW() - INTERVAL 1 DAY)
");
echo "  ✓ 10 applications\n";

// ── ASSESSMENTS ────────────────────────────────────────
$pdo->exec("INSERT INTO assessments (id, employer_id, job_id, title, description, difficulty, time_limit_minutes, passing_score, status) VALUES
(1,1,1,'Full-Stack Development Assessment','Test covering React, Node.js, REST APIs, and database design.','advanced',45,70,'active'),
(2,1,2,'Data Analysis Challenge','SQL queries, data interpretation, and visualization tasks.','intermediate',30,70,'active'),
(3,1,3,'UX Design Evaluation','User research methodology, wireframing concepts, and design principles.','intermediate',40,70,'active'),
(4,7,7,'Machine Learning Fundamentals','Covers supervised/unsupervised learning, model evaluation, and Python ML libraries.','advanced',60,75,'active'),
(5,1,NULL,'General Problem Solving','Logic and analytical thinking assessment for all candidates.','beginner',20,60,'draft')
");
echo "  ✓ 5 assessments\n";

// ── ASSESSMENT QUESTIONS ───────────────────────────────
$pdo->exec("INSERT INTO assessment_questions (assessment_id, question_type, question_text, options, correct_answer, points, sort_order) VALUES
(1,'multiple_choice','What hook is used for side effects in React?','[\"useState\",\"useEffect\",\"useReducer\",\"useMemo\"]','useEffect',10,1),
(1,'multiple_choice','Which HTTP method is idempotent?','[\"POST\",\"PUT\",\"PATCH\",\"DELETE\"]','PUT',10,2),
(1,'multiple_choice','What is the purpose of an index in a database?','[\"Store data\",\"Speed up queries\",\"Enforce constraints\",\"Backup data\"]','Speed up queries',10,3),
(1,'open_ended','Explain the difference between SQL and NoSQL databases. When would you use each?',NULL,'',20,4),
(1,'multiple_choice','What does CORS stand for?','[\"Cross-Origin Resource Sharing\",\"Client-Origin Request Service\",\"Cross-Object Resource System\",\"Client-Object Request Sharing\"]','Cross-Origin Resource Sharing',10,5),
(2,'multiple_choice','Which SQL clause filters grouped results?','[\"WHERE\",\"HAVING\",\"GROUP BY\",\"ORDER BY\"]','HAVING',10,1),
(2,'multiple_choice','What type of join returns all rows from both tables?','[\"INNER JOIN\",\"LEFT JOIN\",\"RIGHT JOIN\",\"FULL OUTER JOIN\"]','FULL OUTER JOIN',10,2),
(2,'open_ended','Write a SQL query to find the top 5 customers by total order value.',NULL,'',20,3),
(2,'multiple_choice','Which chart type is best for showing trends over time?','[\"Pie chart\",\"Bar chart\",\"Line chart\",\"Scatter plot\"]','Line chart',10,4),
(3,'multiple_choice','What is the first step in the Design Thinking process?','[\"Define\",\"Ideate\",\"Empathize\",\"Prototype\"]','Empathize',10,1),
(3,'multiple_choice','What is a wireframe?','[\"A coded prototype\",\"A low-fidelity layout sketch\",\"A brand guideline\",\"A user persona\"]','A low-fidelity layout sketch',10,2),
(3,'open_ended','Describe your process for conducting user research for a new mobile app.',NULL,'',20,3),
(4,'multiple_choice','Which algorithm is used for classification?','[\"Linear Regression\",\"K-Means\",\"Random Forest\",\"PCA\"]','Random Forest',10,1),
(4,'multiple_choice','What metric measures model accuracy on imbalanced datasets?','[\"Accuracy\",\"F1 Score\",\"MSE\",\"R-squared\"]','F1 Score',10,2),
(4,'open_ended','Explain overfitting and describe two techniques to prevent it.',NULL,'',20,3)
");
echo "  ✓ Assessment questions\n";

// ── ASSESSMENT ATTEMPTS ────────────────────────────────
$pdo->exec("INSERT INTO assessment_attempts (id, assessment_id, employee_id, score, total_points, time_taken_minutes, status, started_at, completed_at, integrity_score) VALUES
(1,1,2,88.00,60,32,'completed',NOW() - INTERVAL 2 DAY,NOW() - INTERVAL 2 DAY + INTERVAL 32 MINUTE,95),
(2,2,3,92.00,50,22,'completed',NOW() - INTERVAL 3 DAY,NOW() - INTERVAL 3 DAY + INTERVAL 22 MINUTE,100),
(3,3,4,85.00,40,28,'completed',NOW() - INTERVAL 1 DAY,NOW() - INTERVAL 1 DAY + INTERVAL 28 MINUTE,98),
(4,1,8,71.00,60,40,'completed',NOW() - INTERVAL 4 DAY,NOW() - INTERVAL 4 DAY + INTERVAL 40 MINUTE,88),
(5,2,8,65.00,50,28,'completed',NOW() - INTERVAL 2 DAY,NOW() - INTERVAL 2 DAY + INTERVAL 28 MINUTE,92),
(6,4,2,45.00,40,55,'completed',NOW() - INTERVAL 1 DAY,NOW() - INTERVAL 1 DAY + INTERVAL 55 MINUTE,100),
(7,1,5,0,60,0,'in_progress',NOW() - INTERVAL 1 HOUR,NULL,100)
");
echo "  ✓ Assessment attempts\n";

// ── INTERVIEWS ─────────────────────────────────────────
$today = date('Y-m-d');
$tomorrow = date('Y-m-d', strtotime('+1 day'));
$dayAfter = date('Y-m-d', strtotime('+2 days'));
$friday = date('Y-m-d', strtotime('next friday'));
$lastWeek = date('Y-m-d', strtotime('-5 days'));

$pdo->exec("INSERT INTO interviews (application_id, employer_id, employee_id, scheduled_date, start_time, duration_minutes, interview_type, meeting_link, location, notes_for_candidate, status) VALUES
(1,1,2,'$today','14:00:00',60,'video_zoom','https://zoom.us/j/123456','','Please prepare a code walkthrough of a recent project.','scheduled'),
(3,1,4,'$today','16:30:00',45,'phone',NULL,'','We will discuss your UX portfolio and design process.','scheduled'),
(2,1,3,'$tomorrow','10:30:00',60,'video_zoom','https://meet.google.com/abc-def','','Bring your laptop for a live SQL challenge.','scheduled'),
(4,1,5,'$dayAfter','09:00:00',60,'in_person',NULL,'TechFlow HQ, Bole Road','Final round with CTO. Bring ID for building access.','scheduled'),
(6,1,8,'$friday','13:00:00',60,'video_zoom','https://zoom.us/j/789012','','Technical interview covering Java and system design.','scheduled'),
(9,7,6,'$friday','15:30:00',45,'phone',NULL,'','Discuss product marketing strategy for AgriTech vertical.','scheduled'),
(1,1,2,'$lastWeek','11:00:00',60,'video_zoom','https://zoom.us/j/111','','Initial screening interview.','completed')
");
echo "  ✓ 7 interviews\n";

// ── INTERVIEW FEEDBACK (for completed interview) ──────
$pdo->exec("INSERT INTO interview_feedback (interview_id, reviewer_id, overall_rating, technical_rating, communication_rating, problem_solving_rating, culture_fit_rating, strengths, improvements, recommendation) VALUES
(7,1,4,5,4,4,5,'Excellent technical skills. Strong React and Node.js knowledge. Very articulate.','Could improve system design depth for senior-level discussions.','strong_yes')
");
echo "  ✓ Interview feedback\n";

echo "\n✅ Seeding complete! All tables populated.\n";
echo "   Login credentials: any email above + password: password123\n";
echo "   Employer: abebe@hireable.com | Employee: sara@hireable.com\n";
