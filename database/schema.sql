-- ============================================
-- Hireable Platform - Full Database Schema
-- Run: php database/migrate.php
-- WARNING: This DROPS all tables and recreates them
-- ============================================

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS assessment_snapshots;
DROP TABLE IF EXISTS assessment_locks;
DROP TABLE IF EXISTS assessment_answers;
DROP TABLE IF EXISTS assessment_attempts;
DROP TABLE IF EXISTS assessment_questions;
DROP TABLE IF EXISTS assessments;
DROP TABLE IF EXISTS interview_feedback;
DROP TABLE IF EXISTS interviews;
DROP TABLE IF EXISTS applications;
DROP TABLE IF EXISTS jobs;
DROP TABLE IF EXISTS skills;
DROP TABLE IF EXISTS work_experience;
DROP TABLE IF EXISTS education;
DROP TABLE IF EXISTS certifications;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- USERS (Both employees and employers)
-- ============================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('employee','employer') NOT NULL DEFAULT 'employee',
    phone VARCHAR(20),
    country VARCHAR(100),
    city VARCHAR(100),
    
    -- Employee profile fields
    headline VARCHAR(255),
    bio TEXT,
    date_of_birth DATE,
    gender ENUM('male','female','other','prefer_not_to_say'),
    linkedin_url VARCHAR(500),
    portfolio_url VARCHAR(500),
    github_url VARCHAR(500),
    resume_path VARCHAR(500),
    profile_photo VARCHAR(500),
    years_of_experience INT DEFAULT 0,
    current_salary DECIMAL(12,2),
    expected_salary DECIMAL(12,2),
    availability ENUM('immediate','2_weeks','1_month','3_months','not_looking') DEFAULT 'immediate',
    job_preference ENUM('full-time','part-time','contract','remote','any') DEFAULT 'any',
    
    -- Employer profile fields
    company_name VARCHAR(255),
    company_industry VARCHAR(100),
    company_size ENUM('1-10','11-50','51-200','201-500','500+'),
    company_website VARCHAR(500),
    company_logo VARCHAR(500),
    company_description TEXT,
    company_location VARCHAR(255),
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    
    INDEX idx_role (role),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- WORK EXPERIENCE (Employee profile)
-- ============================================
CREATE TABLE work_experience (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    job_title VARCHAR(255) NOT NULL,
    company VARCHAR(255) NOT NULL,
    location VARCHAR(150),
    start_date DATE NOT NULL,
    end_date DATE,
    is_current TINYINT(1) DEFAULT 0,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- EDUCATION (Employee profile)
-- ============================================
CREATE TABLE education (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    institution VARCHAR(255) NOT NULL,
    degree VARCHAR(255) NOT NULL,
    field_of_study VARCHAR(255),
    start_date DATE,
    end_date DATE,
    grade VARCHAR(50),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- CERTIFICATIONS (Employee profile)
-- ============================================
CREATE TABLE certifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    issuing_organization VARCHAR(255),
    issue_date DATE,
    expiry_date DATE,
    credential_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- SKILLS (Employee profile)
-- ============================================
CREATE TABLE skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    skill_name VARCHAR(100) NOT NULL,
    proficiency ENUM('beginner','intermediate','advanced','expert') DEFAULT 'intermediate',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_skill (user_id, skill_name),
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- JOBS (Employer creates)
-- ============================================
CREATE TABLE jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employer_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    department VARCHAR(100),
    location VARCHAR(150),
    job_type ENUM('full-time','part-time','contract','remote') DEFAULT 'full-time',
    experience_level ENUM('entry','mid','senior','executive','director') DEFAULT 'mid',
    salary_min DECIMAL(12,2),
    salary_max DECIMAL(12,2),
    description TEXT,
    requirements TEXT,
    responsibilities TEXT,
    benefits TEXT,
    skills_required TEXT,
    application_deadline DATE,
    status ENUM('draft','active','closed') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employer_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_employer (employer_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- APPLICATIONS (Employee applies to Job)
-- ============================================
CREATE TABLE applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT NOT NULL,
    employee_id INT NOT NULL,
    cover_letter TEXT,
    resume_path VARCHAR(500),
    status ENUM('applied','screening','interview','offer','rejected','withdrawn') DEFAULT 'applied',
    notes TEXT,
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_application (job_id, employee_id),
    INDEX idx_employee (employee_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- ASSESSMENTS
-- ============================================
CREATE TABLE assessments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employer_id INT NOT NULL,
    job_id INT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    difficulty ENUM('beginner','intermediate','advanced','expert') DEFAULT 'intermediate',
    time_limit_minutes INT DEFAULT 45,
    passing_score INT DEFAULT 70,
    status ENUM('draft','active','archived') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE SET NULL,
    INDEX idx_employer (employer_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE assessment_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    assessment_id INT NOT NULL,
    question_type ENUM('multiple_choice','code','open_ended') DEFAULT 'multiple_choice',
    question_text TEXT NOT NULL,
    options JSON,
    correct_answer TEXT,
    points INT DEFAULT 10,
    sort_order INT DEFAULT 0,
    FOREIGN KEY (assessment_id) REFERENCES assessments(id) ON DELETE CASCADE,
    INDEX idx_assessment (assessment_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE assessment_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    assessment_id INT NOT NULL,
    employee_id INT NOT NULL,
    score DECIMAL(5,2),
    total_points INT,
    time_taken_minutes INT,
    status ENUM('in_progress','completed','expired') DEFAULT 'in_progress',
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,

    -- Security: Server-side timer
    deadline_at TIMESTAMP NULL,

    -- Security: Question randomization
    question_order JSON NULL,

    -- Security: Device binding
    ip_address VARCHAR(45),
    session_id VARCHAR(128),
    browser_fingerprint VARCHAR(64),
    device_token VARCHAR(64),

    -- Security: Geo-positioning
    geo_lat DECIMAL(10,7) NULL,
    geo_lng DECIMAL(10,7) NULL,

    -- Proctoring: Violation counters
    tab_switches INT DEFAULT 0,
    fullscreen_exits INT DEFAULT 0,
    face_absence_count INT DEFAULT 0,
    head_violations INT DEFAULT 0,
    multiple_faces_count INT DEFAULT 0,
    phone_detections INT DEFAULT 0,
    dead_zone_flags INT DEFAULT 0,
    behavioral_flags INT DEFAULT 0,
    mouse_distance_total INT DEFAULT 0,

    -- Proctoring: Integrity
    integrity_score INT DEFAULT 100,
    flagged TINYINT(1) DEFAULT 0,
    violation_log JSON NULL,

    -- Proctoring: Webcam
    reference_photo_path VARCHAR(500) NULL,

    FOREIGN KEY (assessment_id) REFERENCES assessments(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_employee (employee_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE assessment_answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    attempt_id INT NOT NULL,
    question_id INT NOT NULL,
    answer_text TEXT,
    is_correct TINYINT(1) DEFAULT 0,
    points_earned INT DEFAULT 0,
    answered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (attempt_id) REFERENCES assessment_attempts(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES assessment_questions(id) ON DELETE CASCADE,
    UNIQUE KEY unique_answer (attempt_id, question_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- ASSESSMENT SNAPSHOTS (Webcam proctoring)
-- ============================================
CREATE TABLE assessment_snapshots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    attempt_id INT NOT NULL,
    photo_path VARCHAR(500) NOT NULL,
    faces_detected INT DEFAULT 1,
    head_yaw DECIMAL(5,2) NULL,
    head_pitch DECIMAL(5,2) NULL,
    flag_type ENUM('clean','face_absent','multiple_faces','looking_away','phone_detected','object_detected') DEFAULT 'clean',
    detected_objects JSON NULL,
    captured_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (attempt_id) REFERENCES assessment_attempts(id) ON DELETE CASCADE,
    INDEX idx_attempt (attempt_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- ASSESSMENT LOCKS (Cross-device prevention)
-- ============================================
CREATE TABLE assessment_locks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    attempt_id INT NOT NULL,
    session_id VARCHAR(128) NOT NULL,
    device_token VARCHAR(64) NOT NULL,
    ip_address VARCHAR(45),
    locked_until TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (attempt_id) REFERENCES assessment_attempts(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_lock (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- INTERVIEWS
-- ============================================
CREATE TABLE interviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    application_id INT NOT NULL,
    employer_id INT NOT NULL,
    employee_id INT NOT NULL,
    scheduled_date DATE NOT NULL,
    start_time TIME NOT NULL,
    duration_minutes INT DEFAULT 60,
    interview_type ENUM('phone','video_zoom','video_meet','in_person') DEFAULT 'video_zoom',
    meeting_link VARCHAR(500),
    location VARCHAR(255),
    panel_members VARCHAR(500),
    notes_for_candidate TEXT,
    status ENUM('scheduled','completed','cancelled','no_show') DEFAULT 'scheduled',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
    FOREIGN KEY (employer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_date (scheduled_date),
    INDEX idx_employer (employer_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE interview_feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    interview_id INT NOT NULL,
    reviewer_id INT NOT NULL,
    overall_rating INT,
    technical_rating INT,
    communication_rating INT,
    problem_solving_rating INT,
    culture_fit_rating INT,
    strengths TEXT,
    improvements TEXT,
    additional_notes TEXT,
    recommendation ENUM('strong_yes','yes','maybe','no') DEFAULT 'maybe',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (interview_id) REFERENCES interviews(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewer_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_feedback (interview_id, reviewer_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
