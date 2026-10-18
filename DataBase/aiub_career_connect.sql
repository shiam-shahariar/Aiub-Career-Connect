CREATE DATABASE IF NOT EXISTS aiub_career_connect;
USE aiub_career_connect;

CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    industry VARCHAR(100) NOT NULL DEFAULT '',
    website VARCHAR(255) DEFAULT NULL,
    location VARCHAR(150) DEFAULT NULL,
    description TEXT,
    status ENUM('active', 'inactive', 'merged') NOT NULL DEFAULT 'active',
    merged_into_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (merged_into_id) REFERENCES companies(id),
    INDEX idx_companies_name_location (name, location),
    INDEX idx_companies_status (status)
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(180) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('student', 'alumni', 'admin') NOT NULL,
    status ENUM('active', 'pending', 'inactive') NOT NULL DEFAULT 'active',
    department_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id)
);

CREATE TABLE profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    full_name VARCHAR(150) NOT NULL,
    student_id VARCHAR(50) DEFAULT NULL,
    department VARCHAR(100) NOT NULL,
    degree VARCHAR(180) NOT NULL,
    graduation_year INT DEFAULT NULL,
    phone VARCHAR(30) DEFAULT NULL,
    location VARCHAR(100) DEFAULT NULL,
    skills TEXT,
    bio TEXT,
    portfolio_url VARCHAR(255) DEFAULT NULL,
    linkedin_url VARCHAR(255) DEFAULT NULL,
    current_company VARCHAR(150) DEFAULT NULL,
    company_id INT DEFAULT NULL,
    job_title VARCHAR(150) DEFAULT NULL,
    industry VARCHAR(100) DEFAULT NULL,
    email_contact ENUM('yes', 'no') DEFAULT 'no',
    email_contact_visibility ENUM('public', 'logged_in', 'disabled') NOT NULL DEFAULT 'disabled',
    visibility ENUM('public', 'logged_in', 'private') DEFAULT 'public',
    verification_status ENUM('pending', 'verified', 'rejected') NOT NULL DEFAULT 'pending',
    review_note TEXT DEFAULT NULL,
    verified_at TIMESTAMP NULL DEFAULT NULL,
    verified_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (verified_by) REFERENCES users(id),
    INDEX idx_profiles_alumni_review (verification_status, updated_at),
    INDEX idx_profiles_directory (department, graduation_year, visibility),
    INDEX idx_profiles_job (industry, job_title),
    INDEX idx_profiles_company_visibility (company_id, verification_status, visibility)
);

CREATE TABLE opportunities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    company VARCHAR(150) NOT NULL,
    type ENUM('Internship', 'Job') NOT NULL,
    department VARCHAR(100) NOT NULL,
    location VARCHAR(120) NOT NULL,
    remote_mode VARCHAR(50) NOT NULL,
    deadline DATE NOT NULL,
    published_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    description TEXT NOT NULL,
    application_url VARCHAR(255) NOT NULL,
    status ENUM('draft', 'published', 'closed', 'archived') NOT NULL DEFAULT 'draft',
    company_id INT DEFAULT NULL,
    created_by INT,
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (company_id) REFERENCES companies(id),
    INDEX idx_opportunities_status_deadline (status, deadline)
);

CREATE TABLE alumni_directory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    company_name VARCHAR(150) DEFAULT NULL,
    job_title VARCHAR(150) DEFAULT NULL,
    industry VARCHAR(100) DEFAULT NULL,
    verification_status ENUM('pending', 'verified', 'rejected') NOT NULL DEFAULT 'pending',
    email_contact_visible ENUM('public', 'logged_in', 'disabled') NOT NULL DEFAULT 'disabled',
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE admin_audit_log (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    actor_id INT DEFAULT NULL,
    action VARCHAR(80) NOT NULL,
    target_type VARCHAR(40) NOT NULL,
    target_id INT DEFAULT NULL,
    details JSON DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (actor_id) REFERENCES users(id),
    INDEX idx_audit_target (target_type, target_id, created_at)
);

CREATE TABLE profile_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profile_id INT NOT NULL,
    reporter_id INT NOT NULL,
    reason VARCHAR(100) NOT NULL,
    details TEXT DEFAULT NULL,
    status ENUM('open', 'reviewed', 'dismissed') NOT NULL DEFAULT 'open',
    reviewed_by INT DEFAULT NULL,
    reviewed_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (profile_id) REFERENCES profiles(id),
    FOREIGN KEY (reporter_id) REFERENCES users(id),
    FOREIGN KEY (reviewed_by) REFERENCES users(id),
    INDEX idx_reports_status (status, created_at)
);

INSERT INTO departments (name) VALUES
('Computer Science'),
('Business Administration'),
('EEE');

INSERT INTO users (name, email, password_hash, role, status, department_id) VALUES
('Sadia Ahmed', 'student@aiub.edu', '$2y$10$cgIeowUZ5AQIG.53qdCjc.WLKqGpzskp2Nmy7Dmuth4GBnsQ8FGoS', 'student', 'active', 1),
('Imran Hasan', 'alumni@aiub.edu', '$2y$10$IX2PJI9ZSAKaVgeL57cd.em7uzYxUpj.Ajyq6kRbH0kRTlhRvHrza', 'alumni', 'active', 1),
('Rashid Karim', 'admin@aiub.edu', '$2y$10$qfngcj55NVtL5ZnoOcEmB.UlEvtsO9G2JXgUlTru1HGpTd2XW3xum', 'admin', 'active', 1);

INSERT INTO users (name, email, password_hash, role, status, department_id) VALUES
('Nadia Rahman (Demo)', 'nadia.rahman@example.test', '!directory-demo-account', 'alumni', 'active', 2),
('Farah Jahan (Demo)', 'farah.jahan@example.test', '!directory-demo-account', 'alumni', 'active', 3);

INSERT INTO companies (id, name, industry, website, location, description) VALUES
(1, 'TechNova Limited', 'Software', NULL, 'Dhaka', 'Demo company record for the alumni directory.'),
(2, 'Rupali Ventures', 'Consulting', NULL, 'Dhaka', 'Demo company record for the alumni directory.'),
(3, 'SmartGrid Systems', 'Electronics', NULL, 'Dhaka', 'Demo company record for the alumni directory.');

INSERT INTO profiles (
    user_id, full_name, department, degree, graduation_year, skills, bio,
    current_company, company_id, job_title, industry, email_contact,
    email_contact_visibility, visibility, verification_status, verified_at, verified_by
) VALUES
(2, 'Imran Hasan', 'Computer Science', 'BSc in Computer Science', 2019,
 'System Design, Backend Engineering, Leadership',
 'Demo alumni profile for demonstrating verified directory listings.',
 'TechNova Limited', 1, 'Senior Software Engineer', 'Software', 'no',
 'disabled', 'public', 'verified', CURRENT_TIMESTAMP, 3),
(4, 'Nadia Rahman (Demo)', 'Business Administration', 'BBA', 2018,
 'Business Strategy, Analytics, Stakeholder Management',
 'Demo alumni profile for demonstrating company directory counts.',
 'Rupali Ventures', 2, 'Business Analyst', 'Consulting', 'no',
 'disabled', 'public', 'verified', CURRENT_TIMESTAMP, 3),
(5, 'Farah Jahan (Demo)', 'EEE', 'BSc in Electrical and Electronic Engineering', 2020,
 'Embedded Systems, Automation, IoT',
 'Demo alumni profile awaiting administrator review.',
 'SmartGrid Systems', 3, 'Embedded Systems Engineer', 'Electronics', 'no',
 'disabled', 'public', 'pending', NULL, NULL);

INSERT INTO alumni_directory (user_id, company_name, job_title, industry, verification_status, email_contact_visible) VALUES
(2, 'TechNova Limited', 'Senior Software Engineer', 'Software', 'verified', 'disabled'),
(4, 'Rupali Ventures', 'Business Analyst', 'Consulting', 'verified', 'disabled'),
(5, 'SmartGrid Systems', 'Embedded Systems Engineer', 'Electronics', 'pending', 'disabled');

INSERT INTO opportunities (
    title, company, type, department, location, remote_mode, deadline, published_at,
    description, application_url, status, company_id, created_by
) VALUES
('Frontend Developer Intern', 'PixelCraft Studio', 'Internship', 'Computer Science', 'Dhaka', 'Hybrid', '2026-11-15', '2026-10-01', 'Design and implement user-facing web pages, work within a UI/UX-focused team, and contribute to responsive front-end features.', 'https://example.com/jobs/frontend-intern', 'published', NULL, 3),
('Junior Software Engineer', 'BlueWave Technologies', 'Job', 'Computer Science', 'Remote', 'Remote', '2026-11-22', '2026-10-03', 'Build and support web applications using PHP, JavaScript, and MySQL. Collaboration with product and QA teams is essential.', 'https://example.com/jobs/junior-engineer', 'published', NULL, 3),
('Business Analyst Intern', 'Rupali Ventures', 'Internship', 'Business Administration', 'Chattogram', 'On-site', '2026-10-28', '2026-09-25', 'Assist with market research, process analysis, and business reports for digital transformation initiatives.', 'https://example.com/jobs/business-analyst-intern', 'published', 2, 3),
('Embedded Systems Trainee', 'SmartGrid Systems', 'Internship', 'EEE', 'Dhaka', 'Hybrid', '2026-11-30', '2026-10-10', 'Support hardware testing, firmware validation, and IoT prototypes in a collaborative engineering environment.', 'https://example.com/jobs/embedded-systems-trainee', 'draft', 3, 3);

-- The demo passwords are intentionally kept in sync with the app's sample credentials.
-- student123 / alumni123 / admin123
