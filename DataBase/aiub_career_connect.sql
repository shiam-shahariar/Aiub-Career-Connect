CREATE DATABASE IF NOT EXISTS aiub_career_connect;
USE aiub_career_connect;

CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
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
    job_title VARCHAR(150) DEFAULT NULL,
    industry VARCHAR(100) DEFAULT NULL,
    email_contact ENUM('yes', 'no') DEFAULT 'no',
    visibility ENUM('public', 'logged_in', 'private') DEFAULT 'public',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
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
    status ENUM('draft', 'published', 'archived') NOT NULL DEFAULT 'draft',
    created_by INT,
    FOREIGN KEY (created_by) REFERENCES users(id)
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

INSERT INTO departments (name) VALUES
('Computer Science'),
('Business Administration'),
('EEE');

INSERT INTO users (name, email, password_hash, role, status, department_id) VALUES
('Sadia Ahmed', 'student@aiub.edu', '$2y$10$EJ8U0cDT3m6xK7m/5IYDoeh4V3A6R3P3s8XgS9zP8mQGJvEJgI8IK', 'student', 'active', 1),
('Imran Hasan', 'alumni@aiub.edu', '$2y$10$P5.MxV4XGQ2C2xW7L4FJbeE7wKJfW/OuShM9K8nhfQVC8h4sx7Z.6', 'alumni', 'active', 1),
('Rashid Karim', 'admin@aiub.edu', '$2y$10$3/9dUDQ1TfQFvP.bS6o62el4Z4nP9YgwejQK1hK57EoYafS5iHk0O', 'admin', 'active', 1);

-- The demo passwords are intentionally kept in sync with the app's sample credentials.
-- student123 / alumni123 / admin123
