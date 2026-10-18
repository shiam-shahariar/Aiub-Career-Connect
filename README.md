# Aiub-Career-Connect

AIUB Career Connect - Increment 2

Project goal:
This prototype covers the core features in Increment 1 and the verified alumni and company network in Increment 2 as described in Project-Documentation.txt.

Included in this build:
- AIUB-inspired landing page with responsive navigation and device preview buttons
- Student, Alumni, and Admin user roles
- Login and registration flow
- Dashboard views and editable profile mockup
- Alumni directory and internship/job listing pages
- Opportunity detail pages
- Admin dashboard for management overview
- JSON AJAX filtering for opportunities on the listing page
- Searchable and paginated alumni and company directories
- Alumni verification queue, profile visibility preferences, and contact consent
- Company profile management, duplicate merging, and verified alumni counts
- Profile reporting, administrator audit history, and opportunity lifecycle status controls
- MySQL schema in DataBase/aiub_career_connect.sql and Increment 1 upgrade script in DataBase/increment_2_upgrade.sql

Project folder structure:
AIUB Career Connect/
├── Assets/
├── Controller/
├── DataBase/
├── Design/
├── Javascript/
├── Model/
├── View/
├── index.php
├── README.txt

How to run locally:
1. Place this folder in your local web server root (for example htdocs in XAMPP).
2. Start Apache and MySQL from XAMPP.
3. For a fresh database, import DataBase/aiub_career_connect.sql into MySQL.
   If upgrading an existing Increment 1 database, import DataBase/increment_2_upgrade.sql once instead.
4. Configure DB_HOST, DB_NAME, DB_USER, and DB_PASS in the PHP environment if your local MySQL settings differ from the defaults (localhost, aiub_career_connect, root, and an empty password).
5. Open http://localhost/AIUB-Career-Connect/index.php in your browser.
6. Use the demo accounts:
   - Student: student@aiub.edu / student123
   - Alumni: alumni@aiub.edu / alumni123
   - Admin: admin@aiub.edu / admin123

Important notes:
- The application is built to follow the documentation's MVC-inspired structure.
- The UI uses AIUB-style branding and responsive design rules from the project documentation.
- Alumni verification, company management, reports, opportunity lifecycle changes, and audit records persist when MySQL is configured.
- When MySQL is unavailable, directory and administration changes use session-only demo data. The Admin page indicates when this mode is active.
- Login, registration, the general profile editor, and some dashboard activity metrics remain demo features; they are not complete production account-management flows.
- Demo alumni records named with "(Demo)" or using the reserved example.test domain are sample data, not real people or contact addresses.
- Company duplicates are identified by a case-insensitive, trimmed company name and location; administrators can merge duplicate records into an active canonical company.
- Only verified, non-private alumni profiles are included in directory and company counts. Logged-in visibility and contact visibility are applied according to the viewer's authentication and the alumnus's preferences.


