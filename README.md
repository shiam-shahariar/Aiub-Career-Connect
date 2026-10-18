# Aiub-Career-Connect

AIUB Career Connect - Increment 1

# Project goal:
This prototype covers the core features for the AIUB Career Connect platform in Increment 1 as described in Project-Documentation.txt.

# Included in this build:
- AIUB-inspired landing page with responsive navigation and device preview buttons
- Student, Alumni, and Admin user roles
- Login and registration flow
- Dashboard views and editable profile mockup
- Alumni directory and internship/job listing pages
- Opportunity detail pages
- Admin dashboard for management overview
- JSON AJAX filtering for opportunities on the listing page
- MySQL schema in DataBase/aiub_career_connect.sql

# Project folder structure:
AIUB-Career-Connect/
├── Assets/
├── Controller/
├── DataBase/
├── Design/
├── Javascript/
├── Model/
├── View/
├── index.php
├── README.txt

# How to run locally:
1. Place this folder in your local web server root (for example htdocs in XAMPP).
2. Start Apache and MySQL from XAMPP.
3. Import DataBase/aiub_career_connect.sql into your MySQL database.
4. Open http://localhost/AIUB-Career-Connect/index.php in your browser.
5. Use the demo accounts:
   - Student: student@aiub.edu / student123
   - Alumni: alumni@aiub.edu / alumni123
   - Admin: admin@aiub.edu / admin123

# Important notes:
- The application is built to follow the documentation's MVC-inspired structure.
- The UI uses AIUB-style branding and responsive design rules from the project documentation.
- The first increment focuses on prototype functionality and usability rather than full production-grade database integration.
- The login, registration, dashboard statistics, and profile save actions are demo-friendly and should be  connected to a real MySQL-backed data layer in a production build.

