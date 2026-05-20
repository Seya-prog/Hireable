# Hireable

**Your Career, Supercharged.**

Hireable is a full-stack recruitment platform built with PHP, MySQL, and vanilla JavaScript. It connects job seekers with employers through a modern, warm-organic UI — featuring skill assessments, AI-proctored exams, resume generation, and a complete applicant tracking pipeline.

---

## ✨ Features

### For Job Seekers (Employees)
- **Job Search** — Browse, search, and filter job listings by keyword, location, and type
- **Application Tracking** — Visual pipeline showing application status (Applied → Screening → Interview → Offer)
- **Skill Assessments** — Take timed, AI-proctored assessments with webcam integrity monitoring
- **Resume Generator** — Auto-generate polished resumes from profile data
- **Profile Management** — Manage skills, experience, education, and contact info

### For Employers
- **Dashboard** — Overview of job postings, applicants, interviews, and hiring funnel stats
- **Job Postings** — Create, edit, and manage job listings with required skills and salary ranges
- **Candidate Management** — Review, shortlist, and track applicants through the hiring pipeline
- **Skill Assessments** — Create custom assessments (manual or AI-generated) linked to job postings
- **Assessment Results** — View scores, pass rates, and filter by assessment
- **Interview Scheduling** — Schedule phone, video, or in-person interviews with candidates
- **Interview Feedback** — Rate candidates with structured feedback forms
- **Integrity Review** — Review webcam snapshots and proctoring violation logs

### Shared
- **Authentication** — Secure login/signup with role-based access (employee vs employer)
- **CSRF Protection** — All forms protected against cross-site request forgery
- **Client-Side Filtering** — Reusable search, tab filters, and dropdown filters across all dashboard views
- **Custom Styled Dropdowns** — Native selects replaced with beautiful, themed dropdown components

---

## 🛠 Tech Stack

| Layer | Technology |
|-------|-----------|
| **Backend** | PHP 8+ (vanilla, no framework) |
| **Database** | MySQL 8+ with PDO |
| **Frontend** | HTML5, Vanilla CSS, Vanilla JavaScript |
| **Server** | Apache 2.4+ with mod_rewrite |
| **Fonts** | Google Fonts (Newsreader, Manrope, Pinyon Script) |
| **Icons** | Material Symbols Outlined |

---

## 📁 Project Structure

```
Hireable/
├── backend/
│   ├── controllers/          # Request handlers
│   │   ├── ApplicationController.php
│   │   ├── AssessmentController.php
│   │   ├── AuthController.php
│   │   ├── InterviewController.php
│   │   ├── JobController.php
│   │   ├── ProfileController.php
│   │   └── ResumeController.php
│   ├── helpers/              # Session, CSRF, validation utilities
│   ├── middleware/           # Auth guards and role-based access
│   └── routes/              # Route definitions (auth, jobs, assessments, etc.)
│
├── components/
│   ├── shared/              # Reusable: head.php, toast.php, stat cards
│   ├── employee/            # Employee-specific: sidebar, app cards, skill panels
│   └── employer/            # Employer-specific: sidebar, candidate/job/interview cards
│
├── config/
│   ├── app.php              # URL constants and path definitions
│   ├── database.php         # MySQL connection (PDO)
│   ├── ai.php               # AI/API key configuration
│   └── paths.php            # File path helpers
│
├── core/
│   ├── Router.php           # URL routing engine
│   └── Api.php              # JSON API response handler
│
├── database/
│   ├── schema.sql           # Full database schema
│   ├── migrate.php          # Migration runner
│   ├── seed.php             # Sample data seeder
│   └── repositories/        # Data access layer (PDO queries)
│       ├── ApplicationRepository.php
│       ├── AssessmentRepository.php
│       ├── InterviewRepository.php
│       ├── JobRepository.php
│       └── UserRepository.php
│
├── pages/
│   ├── auth/                # Login, Signup
│   ├── employee/            # Applications, Job Search, Profile, Resume, Assessments
│   ├── employer/            # Dashboard, Jobs, Candidates, Interviews, Assessments
│   └── shared/              # Skill Assessments (both roles)
│
├── public/
│   ├── index.php            # Landing page
│   ├── assets/
│   │   ├── css/             # Stylesheets (global, landing, employer, employee, etc.)
│   │   ├── js/              # Client-side scripts (filters, custom-select, assessments)
│   │   ├── icons/           # Favicon and app icons
│   │   └── images/          # Static images
│   └── .htaccess            # Asset routing
│
├── storage/                 # Uploaded files (resumes, snapshots)
├── .htaccess                # Root URL rewriting rules
└── index.php                # App entry point (redirects to landing)
```

---

## 🚀 Getting Started

### Prerequisites

- **PHP 8.0+** with extensions: `pdo_mysql`, `mbstring`, `json`
- **MySQL 8.0+**
- **Apache 2.4+** with `mod_rewrite` enabled

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-username/hireable.git
   cd hireable
   ```

2. **Configure the database**

   Edit `config/database.php` with your MySQL credentials:
   ```php
   $host = 'localhost';
   $db   = 'hireable';
   $user = 'root';
   $pass = 'your_password';
   ```

3. **Create the database and run migrations**
   ```bash
   mysql -u root -p -e "CREATE DATABASE hireable CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   php database/migrate.php
   ```

4. **Seed sample data** (optional)
   ```bash
   php database/seed.php
   ```

5. **Configure Apache**

   Point your Apache DocumentRoot to the project root, or set up a VirtualHost:
   ```apache
   <VirtualHost *:80>
       DocumentRoot "C:/path/to/Hireable"
       ServerName localhost
       <Directory "C:/path/to/Hireable">
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```

6. **Open in browser**
   ```
   http://localhost/public/index.php
   ```

### Default Test Accounts (after seeding)

| Role | Email | Password |
|------|-------|----------|
| Employee | sara@hireable.com | password123 |
| Employer | techcorp@hireable.com | password123 |

---

## 🔗 URL Routing

The app uses Apache `mod_rewrite` with clean URL patterns:

| Pattern | Handler |
|---------|---------|
| `/action/auth.login` | `AuthController::login()` |
| `/action/auth.signup` | `AuthController::signup()` |
| `/action/employer.jobs.create` | `JobController::create()` |
| `/action/employer.applications.*` | `ApplicationController` |
| `/action/employer.assessments.*` | `AssessmentController` |
| `/action/employer.interviews.*` | `InterviewController` |
| `/api/assessments/*` | JSON API endpoints |

---

## 🎨 Design System

The UI follows a **Warm Organic** aesthetic:

- **Color Palette**: Deep browns (`#170f07`), warm tans (`#f4eedb`), muted golds (`#695d46`)
- **Typography**: Newsreader (serif headings), Manrope (sans-serif body), Pinyon Script (logo accent)
- **Components**: Rounded cards (12px), pill badges, smooth transitions, micro-animations
- **Layout**: Fixed sidebar navigation + scrollable main content area

---

## 📄 License

This project is for educational and portfolio purposes.

---

Built with ☕ and care.
