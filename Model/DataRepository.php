<?php
class DataRepository
{
    public function getUsers(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'Sadia Ahmed',
                'email' => 'student@aiub.edu',
                'password_hash' => password_hash('student123', PASSWORD_DEFAULT),
                'role' => 'student',
                'status' => 'active',
                'department' => 'Computer Science',
            ],
            [
                'id' => 2,
                'name' => 'Imran Hasan',
                'email' => 'alumni@aiub.edu',
                'password_hash' => password_hash('alumni123', PASSWORD_DEFAULT),
                'role' => 'alumni',
                'status' => 'active',
                'department' => 'Computer Science',
            ],
            [
                'id' => 3,
                'name' => 'Rashid Karim',
                'email' => 'admin@aiub.edu',
                'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
                'role' => 'admin',
                'status' => 'active',
                'department' => 'Administration',
            ],
        ];
    }

    public function getProfiles(): array
    {
        return [
            [
                'id' => 1,
                'user_id' => 1,
                'full_name' => 'Sadia Ahmed',
                'student_id' => '19-39146-1',
                'department' => 'Computer Science',
                'degree' => 'BSc in Computer Science',
                'graduation_year' => 2026,
                'phone' => '+8801700001111',
                'location' => 'Dhaka',
                'skills' => 'PHP, JavaScript, UI/UX, SQL',
                'bio' => 'Final-year Computer Science student seeking internship opportunities in product and web development.',
                'portfolio_url' => 'https://example.com/sadia',
                'linkedin_url' => 'https://linkedin.com/in/sadia',
                'visibility' => 'public',
            ],
            [
                'id' => 2,
                'user_id' => 2,
                'full_name' => 'Imran Hasan',
                'student_id' => null,
                'department' => 'Computer Science',
                'degree' => 'BSc in Computer Science',
                'graduation_year' => 2019,
                'phone' => '+8801711112222',
                'location' => 'Chattogram',
                'skills' => 'System Design, Backend Engineering, Team Leadership',
                'bio' => 'Worked with software teams across fintech and SaaS products, and I support students through career guidance.',
                'portfolio_url' => 'https://example.com/imran',
                'linkedin_url' => 'https://linkedin.com/in/imran',
                'current_company' => 'TechNova Limited',
                'job_title' => 'Senior Software Engineer',
                'industry' => 'Software',
                'email_contact' => 'yes',
                'visibility' => 'public',
            ],
        ];
    }

    public function getAlumni(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'Imran Hasan',
                'email' => 'alumni@aiub.edu',
                'department' => 'Computer Science',
                'graduation_year' => 2019,
                'company' => 'TechNova Limited',
                'job_title' => 'Senior Software Engineer',
                'industry' => 'Software',
                'email_contact' => true,
                'verified' => true,
                'bio' => 'Career mentor for backend development and software engineering careers.',
                'skills' => ['System Design', 'Backend Engineering', 'Leadership'],
            ],
            [
                'id' => 2,
                'name' => 'Nadia Rahman',
                'email' => 'alumni@aiub.edu',
                'department' => 'Business Administration',
                'graduation_year' => 2018,
                'company' => 'Rupali Ventures',
                'job_title' => 'Business Analyst',
                'industry' => 'Consulting',
                'email_contact' => false,
                'verified' => true,
                'bio' => 'Supports students exploring business analytics, consulting, and strategy paths.',
                'skills' => ['Business Strategy', 'Analytics', 'Stakeholder Management'],
            ],
            [
                'id' => 3,
                'name' => 'Farah Jahan',
                'email' => 'alumni@aiub.edu',
                'department' => 'EEE',
                'graduation_year' => 2020,
                'company' => 'SmartGrid Systems',
                'job_title' => 'Embedded Systems Engineer',
                'industry' => 'Electronics',
                'email_contact' => true,
                'verified' => false,
                'bio' => 'Open to mentoring students with interest in embedded and electronics projects.',
                'skills' => ['Embedded Systems', 'Automation', 'IoT'],
            ],
        ];
    }

    public function getDemoStats(): array
    {
        $publishedOpportunities = array_values(array_filter($this->getOpportunities(), static fn (array $opportunity): bool => $opportunity['status'] === 'published'));
        $draftOpportunities = array_values(array_filter($this->getOpportunities(), static fn (array $opportunity): bool => $opportunity['status'] === 'draft'));

        return [
            'student_profiles' => count($this->getProfiles()),
            'alumni_profiles' => count($this->getAlumni()),
            'active_students' => 1200,
            'verified_alumni' => count(array_filter($this->getAlumni(), static fn (array $alumni): bool => $alumni['verified'])),
            'published_opportunities' => count($publishedOpportunities),
            'draft_opportunities' => count($draftOpportunities),
            'saved_roles' => 6,
            'mentor_contacts' => 5,
            'recent_activity' => 3,
        ];
    }

    public function getOpportunities(): array
    {
        return [
            [
                'id' => 1,
                'title' => 'Frontend Developer Intern',
                'company' => 'PixelCraft Studio',
                'type' => 'Internship',
                'department' => 'Computer Science',
                'location' => 'Dhaka',
                'remote' => 'Hybrid',
                'deadline' => '2026-11-15',
                'published_at' => '2026-10-01',
                'description' => 'Design and implement user-facing web pages, work within a UI/UX-focused team, and contribute to responsive front-end features.',
                'application_url' => 'https://example.com/jobs/frontend-intern',
                'status' => 'published',
            ],
            [
                'id' => 2,
                'title' => 'Junior Software Engineer',
                'company' => 'BlueWave Technologies',
                'type' => 'Job',
                'department' => 'Computer Science',
                'location' => 'Remote',
                'remote' => 'Remote',
                'deadline' => '2026-11-22',
                'published_at' => '2026-10-03',
                'description' => 'Build and support web applications using PHP, JavaScript, and MySQL. Collaboration with product and QA teams is essential.',
                'application_url' => 'https://example.com/jobs/junior-engineer',
                'status' => 'published',
            ],
            [
                'id' => 3,
                'title' => 'Business Analyst Intern',
                'company' => 'Rupali Ventures',
                'type' => 'Internship',
                'department' => 'Business Administration',
                'location' => 'Chattogram',
                'remote' => 'On-site',
                'deadline' => '2026-10-28',
                'published_at' => '2026-09-25',
                'description' => 'Assist with market research, process analysis, and business reports for digital transformation initiatives.',
                'application_url' => 'https://example.com/jobs/business-analyst-intern',
                'status' => 'published',
            ],
            [
                'id' => 4,
                'title' => 'Embedded Systems Trainee',
                'company' => 'SmartGrid Systems',
                'type' => 'Internship',
                'department' => 'EEE',
                'location' => 'Dhaka',
                'remote' => 'Hybrid',
                'deadline' => '2026-11-30',
                'published_at' => '2026-10-10',
                'description' => 'Support hardware testing, firmware validation, and IoT prototypes in a collaborative engineering environment.',
                'application_url' => 'https://example.com/jobs/embedded-systems-trainee',
                'status' => 'draft',
            ],
        ];
    }

    public function findUserByEmail(string $email): ?array
    {
        foreach ($this->getUsers() as $user) {
            if (strcasecmp($user['email'], $email) === 0) {
                return $user;
            }
        }

        return null;
    }
}
