🏫 Smart Campus Management System
📋 Overview
A comprehensive web-based platform designed to digitize and streamline campus operations, communication, and student engagement. This system replaces traditional manual processes with an integrated digital solution for educational institutions.

✨ Features
👨‍🏫 Admin Features
📢 Digital Notice Board - Create, edit, and manage campus notices

📅 Event Management - Organize events with online registration system

⚖️ Complaint Management - Track and resolve student complaints (Pending → In Process → Resolved)

🔐 Access Control - Manage student permissions and access rules

📊 Dashboard - Comprehensive overview of campus activities

👨‍🎓 Student Features
🏠 Personal Dashboard - Centralized view of all campus information

📰 Notice Viewer - Real-time access to important announcements

🎫 Event Registration - Easy sign-up for campus events

📝 Complaint Submission - Submit grievances with status tracking

🔔 Status Updates - Real-time tracking of complaint resolutions

🎨 System Features
📱 Fully Responsive - Works seamlessly on desktop, tablet, and mobile

🌙 Dark/Light Mode - User-toggleable themes for better accessibility

🔐 Secure Authentication - Role-based access control (Admin/Student)

⚡ Fast Performance - Optimized database queries and caching

📱 Cross-Browser Compatible - Works on all modern browsers

🏗️ Technology Stack
Backend
PHP - Server-side scripting language

MySQL - Relational database management system

Apache - Web server

Frontend
HTML5 - Markup language

CSS3 - Styling with responsive design

JavaScript - Client-side interactivity

Bootstrap - Responsive framework (optional)

Development Tools
XAMPP/WAMP - Local development environment

phpMyAdmin - Database management

Git - Version control


🚀 Installation Guide
Prerequisites
XAMPP or WAMP installed

PHP 7.4 or higher

MySQL 5.7 or higher

Web browser (Chrome, Firefox, Edge)

Step-by-Step Installation
Clone the Repository

bash
git clone https://github.com/yourusername/smart-campus-system.git
cd smart-campus-system
Setup Database

Open phpMyAdmin (http://localhost/phpmyadmin)

Create new database: smart_campus_db

Import database/schema.sql

Optional: Import database/dummy-data.sql for sample data

Configure Database Connection
Edit includes/config.php:

php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'smart_campus_db');
Move Project to htdocs/www

bash
# For XAMPP on Windows
copy smart-campus-system C:\xampp\htdocs\

# For Linux
sudo cp -r smart-campus-system /opt/lampp/htdocs/
Start Services

Start Apache

Start MySQL

Access the Application

text
http://localhost/smart-campus-system
👥 Default Login Credentials
Admin Account
Username: admin@campus.edu

Password: admin123

Student Account
Username: student@campus.edu

Password: student123

Note: Change passwords immediately after first login

🗃️ Database Schema
Main Tables
users - User authentication and profiles

notices - Campus announcements and notices

events - Event details and schedules

complaints - Student complaints and resolution tracking

registrations - Event registrations

access_rules - Permission management

ER Diagram
sql
users (1) → (n) notices
users (1) → (n) complaints  
users (1) → (n) events
users (n) → (n) events (through registrations)
🔧 Configuration
1. Email Configuration (For Notifications)
Edit includes/email-config.php:

php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-password');
2. Session Configuration
Edit includes/session-manager.php:

php
// Session timeout (in seconds)
ini_set('session.gc_maxlifetime', 3600);
session_set_cookie_params(3600);
3. Security Settings
php
// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Disable for production
// error_reporting(0);
// ini_set('display_errors', 0);
📖 Usage Guide
For Administrators
Login with admin credentials

Post Notices from the dashboard

Create Events and manage registrations

Review Complaints and update status

Manage Access rules for students

For Students
Login with student credentials

View Notices on dashboard

Register for upcoming events

Submit Complaints through the portal

Track Status of submitted complaints

🔒 Security Features
SQL Injection Prevention - Prepared statements

XSS Protection - Input sanitization and output escaping

CSRF Protection - Token-based validation

Password Hashing - bcrypt algorithm

Session Security - Regeneration and timeout

Input Validation - Server-side validation

🧪 Testing
Manual Testing Checklist
User authentication and authorization

Notice creation and display

Event registration system

Complaint submission and tracking

Responsive design on all devices

Dark/Light mode toggle

Form validation and error handling

Database operations

Automated Testing
Run PHPUnit tests:

bash
composer install
./vendor/bin/phpunit tests/
📊 Performance Optimization
Database Indexing

sql
CREATE INDEX idx_user_email ON users(email);
CREATE INDEX idx_notice_date ON notices(created_at);
Query Optimization

Use LIMIT for pagination

Implement caching for frequent queries

Optimize JOIN operations

Frontend Optimization

Minify CSS and JavaScript

Optimize images

Implement lazy loading

🤝 Contributing
We welcome contributions! Please follow these steps:

Fork the repository

Create a feature branch

bash
git checkout -b feature/amazing-feature
Commit your changes

bash
git commit -m 'Add some amazing feature'
Push to the branch

bash
git push origin feature/amazing-feature
Open a Pull Request

Code Guidelines
Follow PSR-12 coding standards

Write meaningful commit messages

Add comments for complex logic

Update documentation accordingly

🐛 Troubleshooting
Common Issues
Database Connection Failed

text
Error: Access denied for user 'root'@'localhost'
Solution: Check database credentials in config.php

Session Not Working

text
Warning: session_start(): Cannot start session...
Solution: Ensure no output before session_start()

Page Not Found (404)

text
404 Not Found
Solution: Check .htaccess file and Apache rewrite module



php
define('DEBUG_MODE', true);

📈 Future Enhancements
Planned Features
Mobile Application (React Native)

Real-time Chat System

Attendance Management

Online Fee Payment

Library Management

Course Registration

Faculty Evaluation System

SMS/Email Notifications

Technical Improvements
REST API Development

Docker Containerization

Unit Test Coverage

Performance Monitoring

CDN Integration

Progressive Web App (PWA)

📚 Documentation
API Documentation

Database Schema

User Manual

Admin Guide

Deployment Guide

👨‍💻 Developer Setup
Environment Variables
Create .env file:

env
DB_HOST=localhost
DB_NAME=smart_campus_db
DB_USER=root
DB_PASS=
APP_ENV=development
APP_URL=http://localhost:8080
Development Server
bash
# Using PHP built-in server
php -S localhost:8000 -t public/

# With specific port
php -S 0.0.0.0:8080
Database Migrations
bash
# Run migrations
php database/migrate.php

# Seed database
php database/seed.php
📄 License
This project is licensed under the MIT License - see the LICENSE file for details.

🙏 Acknowledgments
University professors for guidance

Web Programming course instructors

Open source community for tools and libraries

Contributors and testers

📞 Support
For support, email: support@campus.edu or create an issue in the GitHub repository.

🌐 Live Demo
Check out the live demo: [https://smart-campus.demo.edu](https://smartcampusuaf.wuaze.com/)
