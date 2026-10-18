<?php
class DataRepository
{
    private ?PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo;
    }

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

    public function getAlumni(array $filters = [], ?array $viewer = null, bool $includeUnverified = false): array
    {
        if ($this->pdo) {
            return $this->queryAlumni($filters, $viewer, $includeUnverified);
        }

        $alumni = [
            [
                'id' => 1,
                'profile_id' => 2,
                'name' => 'Imran Hasan',
                'email' => 'alumni@aiub.edu',
                'department' => 'Computer Science',
                'graduation_year' => 2019,
                'company' => 'TechNova Limited',
                'company_id' => 1,
                'job_title' => 'Senior Software Engineer',
                'industry' => 'Software',
                'email_contact' => false,
                'email_visibility' => 'disabled',
                'verified' => true,
                'verification_status' => 'verified',
                'visibility' => 'public',
                'bio' => 'Career mentor for backend development and software engineering careers.',
                'skills' => ['System Design', 'Backend Engineering', 'Leadership'],
            ],
            [
                'id' => 2,
                'profile_id' => 4,
                'name' => 'Nadia Rahman (Demo)',
                'email' => 'nadia.rahman@example.test',
                'department' => 'Business Administration',
                'graduation_year' => 2018,
                'company' => 'Rupali Ventures',
                'company_id' => 2,
                'job_title' => 'Business Analyst',
                'industry' => 'Consulting',
                'email_contact' => false,
                'email_visibility' => 'disabled',
                'verified' => true,
                'verification_status' => 'verified',
                'visibility' => 'public',
                'bio' => 'Supports students exploring business analytics, consulting, and strategy paths.',
                'skills' => ['Business Strategy', 'Analytics', 'Stakeholder Management'],
            ],
            [
                'id' => 3,
                'profile_id' => 5,
                'name' => 'Farah Jahan (Demo)',
                'email' => 'farah.jahan@example.test',
                'department' => 'EEE',
                'graduation_year' => 2020,
                'company' => 'SmartGrid Systems',
                'company_id' => 3,
                'job_title' => 'Embedded Systems Engineer',
                'industry' => 'Electronics',
                'email_contact' => false,
                'email_visibility' => 'disabled',
                'verified' => false,
                'verification_status' => 'pending',
                'visibility' => 'public',
                'bio' => 'Open to mentoring students with interest in embedded and electronics projects.',
                'skills' => ['Embedded Systems', 'Automation', 'IoT'],
            ],
        ];

        foreach ($alumni as &$person) {
            $overrides = $_SESSION['demo_alumni'][$person['profile_id']] ?? [];
            $person = array_replace($person, $overrides);
            $company = $_SESSION['demo_companies'][$person['company_id']] ?? [];
            if ($company && ($company['status'] ?? 'active') === 'active') {
                $person['company'] = $company['name'] ?? $person['company'];
                $person['industry'] = $company['industry'] ?? $person['industry'];
            }
            $person['verified'] = $person['verification_status'] === 'verified';
            $person['email_contact'] = $this->canShowEmail($person, $viewer);
            if (!$person['email_contact']) {
                $person['email'] = '';
            }
        }
        unset($person);

        if (!$includeUnverified) {
            $alumni = array_values(array_filter($alumni, function (array $person) use ($viewer): bool {
                return $person['verified']
                    && ($person['visibility'] === 'public'
                        || ($person['visibility'] === 'logged_in' && $viewer !== null));
            }));
        }

        foreach (['name' => 'name', 'department' => 'department', 'company' => 'company', 'industry' => 'industry', 'job_title' => 'job_title'] as $filter => $field) {
            $value = trim((string) ($filters[$filter] ?? ''));
            if ($value !== '') {
                $alumni = array_values(array_filter($alumni, static fn (array $person): bool => stripos((string) $person[$field], $value) !== false));
            }
        }
        $year = trim((string) ($filters['graduation_year'] ?? ''));
        if ($year !== '') {
            $alumni = array_values(array_filter($alumni, static fn (array $person): bool => (string) $person['graduation_year'] === $year));
        }

        return $alumni;
    }

    public function getAlumniPage(array $filters, ?array $viewer, int $page, int $pageSize = 12): array
    {
        $pageSize = max(1, min(50, $pageSize));
        $page = max(1, $page);
        $items = $this->pdo
            ? $this->queryAlumni($filters, $viewer, false, $pageSize, ($page - 1) * $pageSize)
            : array_slice($this->getAlumni($filters, $viewer), ($page - 1) * $pageSize, $pageSize);
        $total = $this->pdo ? $this->countAlumni($filters, $viewer) : count($this->getAlumni($filters, $viewer));

        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'pages' => max(1, (int) ceil($total / $pageSize)),
        ];
    }

    public function getAlumniReviewQueue(): array
    {
        if ($this->pdo) {
            return $this->queryAlumni([], null, true, 100);
        }

        return array_slice(array_values(array_filter(
            $this->getAlumni([], null, true),
            static fn (array $person): bool => in_array($person['verification_status'], ['pending', 'rejected'], true)
        )), 0, 100);
    }

    public function reviewAlumni(int $profileId, string $status, string $note, int $actorId): bool
    {
        if (!in_array($status, ['verified', 'rejected', 'pending'], true) || $profileId < 1) {
            return false;
        }

        if ($this->pdo) {
            $this->pdo->beginTransaction();
            try {
                $statement = $this->pdo->prepare(
                    'UPDATE profiles SET verification_status = :status, review_note = :note, verified_at = :verified_at, verified_by = :actor_id WHERE id = :profile_id AND user_id IN (SELECT id FROM users WHERE role = "alumni")'
                );
                $statement->execute([
                    ':status' => $status,
                    ':note' => $note === '' ? null : $note,
                    ':verified_at' => $status === 'verified' ? date('Y-m-d H:i:s') : null,
                    ':actor_id' => $actorId,
                    ':profile_id' => $profileId,
                ]);
                if ($statement->rowCount() !== 1) {
                    $this->pdo->rollBack();
                    return false;
                }
                $this->writeAudit($actorId, 'alumni_' . $status, 'profile', $profileId, ['review_note' => $note]);
                $this->pdo->commit();
                return true;
            } catch (Throwable $error) {
                if ($this->pdo->inTransaction()) {
                    $this->pdo->rollBack();
                }
                throw $error;
            }
        }

        $match = false;
        foreach ($this->getAlumni([], null, true) as $person) {
            if ((int) $person['profile_id'] === $profileId) {
                $_SESSION['demo_alumni'][$profileId]['verification_status'] = $status;
                $_SESSION['demo_alumni'][$profileId]['verified'] = $status === 'verified';
                $_SESSION['demo_alumni'][$profileId]['review_note'] = $note;
                $match = true;
                break;
            }
        }
        if ($match) {
            $_SESSION['demo_audit'][] = [
                'actor_id' => $actorId,
                'action' => 'alumni_' . $status,
                'target_type' => 'profile',
                'target_id' => $profileId,
                'created_at' => date('Y-m-d H:i:s'),
            ];
        }
        return $match;
    }

    public function saveProfilePrivacy(int $userId, string $visibility, string $emailVisibility): bool
    {
        if (!in_array($visibility, ['public', 'logged_in', 'private'], true)
            || !in_array($emailVisibility, ['public', 'logged_in', 'disabled'], true)) {
            return false;
        }

        if ($this->pdo) {
            $exists = $this->pdo->prepare(
                "SELECT 1 FROM profiles p JOIN users u ON u.id = p.user_id WHERE p.user_id = :user_id AND u.role = 'alumni' LIMIT 1"
            );
            $exists->execute([':user_id' => $userId]);
            if (!$exists->fetchColumn()) {
                return false;
            }
            $statement = $this->pdo->prepare(
                'UPDATE profiles SET visibility = :visibility, email_contact_visibility = :email_visibility, email_contact = :email_contact WHERE user_id = :user_id AND EXISTS (SELECT 1 FROM users WHERE id = :user_id_check AND role = "alumni")'
            );
            $statement->execute([
                ':visibility' => $visibility,
                ':email_visibility' => $emailVisibility,
                ':email_contact' => $emailVisibility === 'disabled' ? 'no' : 'yes',
                ':user_id' => $userId,
                ':user_id_check' => $userId,
            ]);
            return true;
        }

        foreach ($this->getAlumni([], null, true) as $person) {
            if ((int) $person['id'] === $userId) {
                $_SESSION['demo_alumni'][$person['profile_id']]['visibility'] = $visibility;
                $_SESSION['demo_alumni'][$person['profile_id']]['email_visibility'] = $emailVisibility;
                return true;
            }
        }
        return false;
    }

    public function getProfilePrivacy(int $userId): array
    {
        $defaults = ['visibility' => 'public', 'email_contact_visibility' => 'disabled'];
        if ($this->pdo) {
            $statement = $this->pdo->prepare(
                "SELECT p.visibility, p.email_contact_visibility FROM profiles p JOIN users u ON u.id = p.user_id WHERE p.user_id = :user_id AND u.role = 'alumni' LIMIT 1"
            );
            $statement->execute([':user_id' => $userId]);
            return $statement->fetch() ?: $defaults;
        }
        foreach ($this->getAlumni([], null, true) as $person) {
            if ((int) $person['id'] === $userId) {
                $preferences = $_SESSION['demo_alumni'][$person['profile_id']] ?? [];
                return [
                    'visibility' => $preferences['visibility'] ?? $person['visibility'],
                    'email_contact_visibility' => $preferences['email_visibility'] ?? $person['email_visibility'],
                ];
            }
        }
        return $defaults;
    }

    public function hasAlumniProfile(int $userId): bool
    {
        if ($this->pdo) {
            $statement = $this->pdo->prepare(
                "SELECT 1 FROM profiles p JOIN users u ON u.id = p.user_id WHERE p.user_id = :user_id AND u.role = 'alumni' LIMIT 1"
            );
            $statement->execute([':user_id' => $userId]);
            return (bool) $statement->fetchColumn();
        }
        foreach ($this->getAlumni([], null, true) as $person) {
            if ((int) $person['id'] === $userId) {
                return true;
            }
        }
        return false;
    }

    public function getCompaniesPage(array $filters, ?array $viewer, int $page, int $pageSize = 12): array
    {
        $pageSize = max(1, min(50, $pageSize));
        $page = max(1, $page);
        if ($this->pdo) {
            [$where, $params] = $this->companyFilterSql($filters);
            $count = $this->pdo->prepare("SELECT COUNT(*) FROM companies c WHERE c.status = 'active' {$where}");
            $count->execute($params);
            $total = (int) $count->fetchColumn();
            $items = $this->queryCompanies($filters, $viewer, $pageSize, ($page - 1) * $pageSize);
        } else {
            $items = $this->getCompanies($filters, $viewer);
            $total = count($items);
            $items = array_slice($items, ($page - 1) * $pageSize, $pageSize);
        }

        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'pages' => max(1, (int) ceil($total / $pageSize)),
        ];
    }

    public function getCompanies(array $filters = [], ?array $viewer = null): array
    {
        if ($this->pdo) {
            return $this->queryCompanies($filters, $viewer);
        }

        $companies = [
            1 => ['id' => 1, 'name' => 'TechNova Limited', 'industry' => 'Software', 'website' => '', 'location' => 'Dhaka', 'description' => 'Demo company profile.', 'alumni_count' => 0],
            2 => ['id' => 2, 'name' => 'Rupali Ventures', 'industry' => 'Consulting', 'website' => '', 'location' => 'Dhaka', 'description' => 'Demo company profile.', 'alumni_count' => 0],
            3 => ['id' => 3, 'name' => 'SmartGrid Systems', 'industry' => 'Electronics', 'website' => '', 'location' => 'Dhaka', 'description' => 'Demo company profile.', 'alumni_count' => 0],
        ];
        foreach ($_SESSION['demo_companies'] ?? [] as $id => $record) {
            if (($record['status'] ?? '') === 'merged') {
                unset($companies[$id]);
                continue;
            }
            $companies[$id] = array_replace($companies[$id] ?? [
                'id' => (int) $id,
                'name' => '',
                'industry' => '',
                'website' => '',
                'location' => '',
                'description' => '',
                'alumni_count' => 0,
            ], $record);
        }
        foreach ($this->getAlumni([], $viewer) as $person) {
            $id = (int) $person['company_id'];
            if ($id > 0 && isset($companies[$id])) {
                $companies[$id]['alumni_count']++;
            }
        }
        foreach ($filters as $field => $value) {
            if (!in_array($field, ['keyword', 'industry', 'location'], true) || trim((string) $value) === '') {
                continue;
            }
            $companies = array_filter($companies, static function (array $company) use ($field, $value): bool {
                $fields = $field === 'keyword' ? ['name', 'industry', 'description'] : [$field];
                foreach ($fields as $companyField) {
                    if (stripos((string) ($company[$companyField] ?? ''), trim((string) $value)) !== false) {
                        return true;
                    }
                }
                return false;
            });
        }
        return array_values($companies);
    }

    public function getCompany(int $companyId, ?array $viewer): ?array
    {
        foreach ($this->getCompanies([], $viewer) as $company) {
            if ((int) $company['id'] === $companyId) {
                return $company;
            }
        }
        return null;
    }

    public function getCompanyAlumniPage(int $companyId, ?array $viewer, int $page, int $pageSize = 12): array
    {
        $pageSize = max(1, min(50, $pageSize));
        $page = max(1, $page);
        if ($this->pdo) {
            $items = $this->queryAlumni([], $viewer, false, $pageSize, ($page - 1) * $pageSize, $companyId);
            $total = $this->countAlumni([], $viewer, $companyId);
        } else {
            $items = array_values(array_filter(
                $this->getAlumni([], $viewer),
                static fn (array $person): bool => (int) $person['company_id'] === $companyId
            ));
            $total = count($items);
            $items = array_slice($items, ($page - 1) * $pageSize, $pageSize);
        }
        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'pages' => max(1, (int) ceil($total / $pageSize)),
        ];
    }

    public function saveCompany(array $input, int $actorId): bool
    {
        $idValue = $input['id'] ?? 0;
        $id = is_scalar($idValue) ? (int) $idValue : -1;
        $name = $this->inputString($input, 'name');
        $industry = $this->inputString($input, 'industry');
        $website = $this->inputString($input, 'website');
        $location = $this->inputString($input, 'location');
        $description = $this->inputString($input, 'description');
        if ($id < 0 || $name === ''
            || $this->characterCount($name) > 200
            || $this->characterCount($industry) > 100
            || $this->characterCount($website) > 255
            || $this->characterCount($location) > 150
            || $this->characterCount($description) > 16000) {
            return false;
        }

        if ($this->pdo) {
            $duplicateSql = 'SELECT id FROM companies WHERE status = "active" AND LOWER(TRIM(name)) = LOWER(TRIM(:name)) AND LOWER(TRIM(COALESCE(location, ""))) = LOWER(TRIM(:location))';
            if ($id > 0) {
                $duplicateSql .= ' AND id <> :id';
            }
            $duplicateSql .= ' LIMIT 1';
            $duplicate = $this->pdo->prepare($duplicateSql);
            $duplicateParams = [':name' => $name, ':location' => $location];
            if ($id > 0) {
                $duplicateParams[':id'] = $id;
            }
            $duplicate->execute($duplicateParams);
            if ($duplicate->fetchColumn()) {
                return false;
            }
            $this->pdo->beginTransaction();
            try {
                if ($id === 0) {
                    $statement = $this->pdo->prepare(
                        'INSERT INTO companies (name, industry, website, location, description) VALUES (:name, :industry, :website, :location, :description)'
                    );
                    $statement->execute([
                        ':name' => $name,
                        ':industry' => $industry,
                        ':website' => $website === '' ? null : $website,
                        ':location' => $location === '' ? null : $location,
                        ':description' => $description === '' ? null : $description,
                    ]);
                    $id = (int) $this->pdo->lastInsertId();
                    $this->writeAudit($actorId, 'company_created', 'company', $id, ['name' => $name]);
                } else {
                    $statement = $this->pdo->prepare(
                        'UPDATE companies SET name = :name, industry = :industry, website = :website, location = :location, description = :description WHERE id = :id AND status = "active"'
                    );
                    $statement->execute([
                        ':name' => $name,
                        ':industry' => $industry,
                        ':website' => $website === '' ? null : $website,
                        ':location' => $location === '' ? null : $location,
                        ':description' => $description === '' ? null : $description,
                        ':id' => $id,
                    ]);
                    if ($statement->rowCount() !== 1) {
                        $exists = $this->pdo->prepare('SELECT 1 FROM companies WHERE id = :id AND status = "active"');
                        $exists->execute([':id' => $id]);
                        if (!$exists->fetchColumn()) {
                            $this->pdo->rollBack();
                            return false;
                        }
                    }
                    $this->writeAudit($actorId, 'company_updated', 'company', $id, ['name' => $name]);
                }
                $this->pdo->commit();
                return true;
            } catch (Throwable $error) {
                if ($this->pdo->inTransaction()) {
                    $this->pdo->rollBack();
                }
                throw $error;
            }
        }

        $companyExists = false;
        foreach ($this->getCompanies([], null) as $company) {
            if ((int) $company['id'] === $id) {
                $companyExists = true;
            }
            if ((int) $company['id'] === $id && ($company['status'] ?? 'active') !== 'active') {
                return false;
            }
            $sameCompany = strcasecmp(trim((string) $company['name']), $name) === 0
                && strcasecmp(trim((string) ($company['location'] ?? '')), $location) === 0;
            if ($sameCompany && ($id === 0 || (int) $company['id'] !== $id)) {
                return false;
            }
        }
        if ($id > 0 && !$companyExists) {
            return false;
        }
        if ($id === 0) {
            $companyIds = array_merge([0, 1, 2, 3], array_map('intval', array_keys($_SESSION['demo_companies'] ?? [])));
            $id = max($companyIds) + 1;
        }
        $_SESSION['demo_companies'][$id] ??= [];
        $_SESSION['demo_companies'][$id] = array_replace($_SESSION['demo_companies'][$id], [
            'name' => $name,
            'industry' => $industry,
            'website' => $website,
            'location' => $location,
            'description' => $description,
        ]);
        $this->recordDemoAudit($actorId, isset($input['id']) && (int) $input['id'] > 0 ? 'company_updated' : 'company_created', 'company', $id);
        return true;
    }

    public function mergeCompanies(int $sourceId, int $targetId, int $actorId): bool
    {
        if ($sourceId < 1 || $targetId < 1 || $sourceId === $targetId) {
            return false;
        }
        if ($this->pdo) {
            $valid = $this->pdo->prepare(
                'SELECT id, name, COALESCE(location, "") AS location FROM companies WHERE id IN (:source, :target) AND status = "active"'
            );
            $valid->execute([':source' => $sourceId, ':target' => $targetId]);
            $records = $valid->fetchAll();
            if (count($records) !== 2
                || strcasecmp(trim($records[0]['name']), trim($records[1]['name'])) !== 0
                || strcasecmp(trim($records[0]['location']), trim($records[1]['location'])) !== 0) {
                return false;
            }
            $this->pdo->beginTransaction();
            try {
                $this->pdo->prepare('UPDATE profiles SET company_id = :target WHERE company_id = :source')
                    ->execute([':target' => $targetId, ':source' => $sourceId]);
                $this->pdo->prepare('UPDATE opportunities SET company_id = :target WHERE company_id = :source')
                    ->execute([':target' => $targetId, ':source' => $sourceId]);
                $statement = $this->pdo->prepare('UPDATE companies SET status = "merged", merged_into_id = :target WHERE id = :source AND status = "active"');
                $statement->execute([':target' => $targetId, ':source' => $sourceId]);
                if ($statement->rowCount() !== 1) {
                    $this->pdo->rollBack();
                    return false;
                }
                $this->writeAudit($actorId, 'company_merged', 'company', $sourceId, ['merged_into_id' => $targetId]);
                $this->pdo->commit();
                return true;
            } catch (Throwable $error) {
                if ($this->pdo->inTransaction()) {
                    $this->pdo->rollBack();
                }
                throw $error;
            }
        }
        $source = $this->getCompany($sourceId, null);
        $target = $this->getCompany($targetId, null);
        if (!$source || !$target
            || strcasecmp(trim($source['name']), trim($target['name'])) !== 0
            || strcasecmp(trim((string) $source['location']), trim((string) $target['location'])) !== 0) {
            return false;
        }
        foreach ($this->getAlumni([], null, true) as $person) {
            if ((int) $person['company_id'] === $sourceId) {
                $_SESSION['demo_alumni'][$person['profile_id']]['company_id'] = $targetId;
                $_SESSION['demo_alumni'][$person['profile_id']]['company'] = $target['name'];
                $_SESSION['demo_alumni'][$person['profile_id']]['industry'] = $target['industry'];
            }
        }
        foreach ($this->getOpportunities() as $opportunity) {
            if ((int) ($opportunity['company_id'] ?? 0) === $sourceId) {
                $_SESSION['demo_opportunity_company'][$opportunity['id']] = $targetId;
            }
        }
        $_SESSION['demo_companies'][$sourceId]['merged_into_id'] = $targetId;
        $_SESSION['demo_companies'][$sourceId]['status'] = 'merged';
        $this->recordDemoAudit($actorId, 'company_merged', 'company', $sourceId);
        return true;
    }

    public function updateOpportunityStatus(int $opportunityId, string $status, int $actorId, ?int $companyId = null): bool
    {
        if (!in_array($status, ['draft', 'published', 'closed', 'archived'], true)
            || $opportunityId < 1
            || ($companyId !== null && $companyId < 0)) {
            return false;
        }
        if ($this->pdo) {
            if ($status === 'published') {
                $validDeadline = $this->pdo->prepare('SELECT 1 FROM opportunities WHERE id = :id AND deadline >= CURRENT_DATE');
                $validDeadline->execute([':id' => $opportunityId]);
                if (!$validDeadline->fetchColumn()) {
                    return false;
                }
            }
            if ($companyId !== null && $companyId > 0) {
                $validCompany = $this->pdo->prepare('SELECT 1 FROM companies WHERE id = :id AND status = "active"');
                $validCompany->execute([':id' => $companyId]);
                if (!$validCompany->fetchColumn()) {
                    return false;
                }
            }
            $this->pdo->beginTransaction();
            try {
                $statement = $this->pdo->prepare('UPDATE opportunities SET status = :status, company_id = :company_id WHERE id = :id');
                $statement->execute([
                    ':status' => $status,
                    ':company_id' => $companyId && $companyId > 0 ? $companyId : null,
                    ':id' => $opportunityId,
                ]);
                if ($statement->rowCount() !== 1) {
                    $exists = $this->pdo->prepare('SELECT 1 FROM opportunities WHERE id = :id');
                    $exists->execute([':id' => $opportunityId]);
                    if (!$exists->fetchColumn()) {
                        $this->pdo->rollBack();
                        return false;
                    }
                }
                if ($companyId !== null) {
                    $this->writeAudit(
                        $actorId,
                        $companyId > 0 ? 'opportunity_company_linked' : 'opportunity_company_unlinked',
                        'opportunity',
                        $opportunityId,
                        $companyId > 0 ? ['company_id' => $companyId] : []
                    );
                }
                $this->writeAudit($actorId, 'opportunity_' . $status, 'opportunity', $opportunityId);
                $this->pdo->commit();
                return true;
            } catch (Throwable $error) {
                if ($this->pdo->inTransaction()) {
                    $this->pdo->rollBack();
                }
                throw $error;
            }
        }
        if ($companyId && $companyId > 0) {
            $companyExists = false;
            foreach ($this->getCompanies([], null) as $company) {
                if ((int) $company['id'] === $companyId) {
                    $companyExists = true;
                    break;
                }
            }
            if (!$companyExists) {
                return false;
            }
        }
        $foundOpportunity = false;
        foreach ($this->getOpportunities() as $opportunity) {
            if ((int) $opportunity['id'] === $opportunityId) {
                $foundOpportunity = true;
                if ($status === 'published' && $opportunity['deadline'] < date('Y-m-d')) {
                    return false;
                }
                break;
            }
        }
        if (!$foundOpportunity) {
            return false;
        }
        $_SESSION['demo_opportunity_status'][$opportunityId] = $status;
        if ($companyId !== null) {
            $_SESSION['demo_opportunity_company'][$opportunityId] = $companyId;
        }
        $this->recordDemoAudit($actorId, 'opportunity_' . $status, 'opportunity', $opportunityId);
        return true;
    }

    public function getAuditLog(int $limit = 50): array
    {
        if (!$this->pdo) {
            return array_slice(array_reverse($_SESSION['demo_audit'] ?? []), 0, max(1, min(100, $limit)));
        }
        $statement = $this->pdo->prepare(
            'SELECT l.id, l.actor_id, u.name AS actor_name, l.action, l.target_type, l.target_id, l.details, l.created_at FROM admin_audit_log l LEFT JOIN users u ON u.id = l.actor_id ORDER BY l.created_at DESC, l.id DESC LIMIT :limit'
        );
        $statement->bindValue(':limit', max(1, min(100, $limit)), PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll();
    }

    public function submitProfileReport(int $profileId, int $reporterId, string $reason, string $details): bool
    {
        if ($profileId < 1 || $reporterId < 1 || $reason === '') {
            return false;
        }
        if ($this->pdo) {
            $statement = $this->pdo->prepare(
                "INSERT INTO profile_reports (profile_id, reporter_id, reason, details) SELECT p.id, :reporter_id, :reason, :details FROM profiles p JOIN users u ON u.id = p.user_id WHERE p.id = :profile_id AND u.id <> :reporter_check AND u.role = 'alumni' AND p.verification_status = 'verified' AND p.visibility IN ('public', 'logged_in')"
            );
            $statement->execute([
                ':profile_id' => $profileId,
                ':reporter_id' => $reporterId,
                ':reporter_check' => $reporterId,
                ':reason' => $reason,
                ':details' => $details === '' ? null : $details,
            ]);
            return $statement->rowCount() === 1;
        }
        $reportable = false;
        foreach ($this->getAlumni([], $_SESSION['user'] ?? null) as $person) {
            if ((int) $person['profile_id'] === $profileId && (int) $person['id'] !== $reporterId) {
                $reportable = true;
                break;
            }
        }
        if (!$reportable) {
            return false;
        }
        $_SESSION['demo_reports'][] = [
            'id' => count($_SESSION['demo_reports'] ?? []) + 1,
            'profile_id' => $profileId,
            'reporter_id' => $reporterId,
            'reason' => $reason,
            'details' => $details,
            'status' => 'open',
            'created_at' => date('Y-m-d H:i:s'),
        ];
        return true;
    }

    public function getOpenProfileReports(): array
    {
        if ($this->pdo) {
            return $this->pdo->query(
                "SELECT r.id, r.profile_id, r.reason, r.details, r.created_at, p.full_name, u.name AS reporter_name FROM profile_reports r JOIN profiles p ON p.id = r.profile_id JOIN users u ON u.id = r.reporter_id WHERE r.status = 'open' ORDER BY r.created_at ASC LIMIT 100"
            )->fetchAll();
        }
        return array_values(array_filter(
            $_SESSION['demo_reports'] ?? [],
            static fn (array $report): bool => $report['status'] === 'open'
        ));
    }

    public function reviewProfileReport(int $reportId, string $status, int $actorId): bool
    {
        if ($reportId < 1 || !in_array($status, ['reviewed', 'dismissed'], true)) {
            return false;
        }
        if ($this->pdo) {
            $this->pdo->beginTransaction();
            try {
                $statement = $this->pdo->prepare(
                    'UPDATE profile_reports SET status = :status, reviewed_by = :actor_id, reviewed_at = CURRENT_TIMESTAMP WHERE id = :id AND status = "open"'
                );
                $statement->execute([':status' => $status, ':actor_id' => $actorId, ':id' => $reportId]);
                if ($statement->rowCount() !== 1) {
                    $this->pdo->rollBack();
                    return false;
                }
                $this->writeAudit($actorId, 'profile_report_' . $status, 'profile_report', $reportId);
                $this->pdo->commit();
                return true;
            } catch (Throwable $error) {
                if ($this->pdo->inTransaction()) {
                    $this->pdo->rollBack();
                }
                throw $error;
            }
        }
        foreach ($_SESSION['demo_reports'] ?? [] as &$report) {
            if ((int) $report['id'] === $reportId && $report['status'] === 'open') {
                $report['status'] = $status;
                $report['reviewed_by'] = $actorId;
                $this->recordDemoAudit($actorId, 'profile_report_' . $status, 'profile_report', $reportId);
                unset($report);
                return true;
            }
        }
        unset($report);
        return false;
    }

    public function getDemoStats(): array
    {
        if ($this->pdo) {
            $count = fn (string $sql): int => (int) $this->pdo->query($sql)->fetchColumn();
            return [
                'student_profiles' => $count("SELECT COUNT(*) FROM profiles p JOIN users u ON u.id = p.user_id WHERE u.role = 'student' AND u.status = 'active'"),
                'alumni_profiles' => $count("SELECT COUNT(*) FROM profiles p JOIN users u ON u.id = p.user_id WHERE u.role = 'alumni' AND u.status = 'active'"),
                'active_students' => $count("SELECT COUNT(*) FROM users WHERE role = 'student' AND status = 'active'"),
                'verified_alumni' => $count("SELECT COUNT(*) FROM profiles p JOIN users u ON u.id = p.user_id WHERE u.role = 'alumni' AND u.status = 'active' AND p.verification_status = 'verified' AND p.visibility = 'public'"),
                'published_opportunities' => $count("SELECT COUNT(*) FROM opportunities WHERE status = 'published' AND deadline >= CURRENT_DATE"),
                'draft_opportunities' => $count("SELECT COUNT(*) FROM opportunities WHERE status = 'draft'"),
                'saved_roles' => 0,
                'mentor_contacts' => 0,
                'recent_activity' => 0,
            ];
        }
        $publishedOpportunities = array_values(array_filter($this->getOpportunities(), static fn (array $opportunity): bool => $opportunity['status'] === 'published'));
        $draftOpportunities = array_values(array_filter($this->getOpportunities(), static fn (array $opportunity): bool => $opportunity['status'] === 'draft'));
        $studentCount = count(array_filter($this->getUsers(), static fn (array $user): bool => $user['role'] === 'student' && $user['status'] === 'active'));

        return [
            'student_profiles' => $studentCount,
            'alumni_profiles' => count($this->getAlumni()),
            'active_students' => $studentCount,
            'verified_alumni' => count(array_filter($this->getAlumni(), static fn (array $alumni): bool => $alumni['verified'])),
            'published_opportunities' => count($publishedOpportunities),
            'draft_opportunities' => count($draftOpportunities),
            'saved_roles' => 6,
            'mentor_contacts' => 5,
            'recent_activity' => 3,
        ];
    }

    public function isPersistent(): bool
    {
        return $this->pdo !== null;
    }

    public function getOpportunities(): array
    {
        if ($this->pdo) {
            $this->closeExpiredOpportunities();
            $statement = $this->pdo->query(
                'SELECT o.id, o.title, COALESCE(c.name, o.company) AS company, o.type, o.department, o.location, o.remote_mode AS remote, o.deadline, o.published_at, o.description, o.application_url, o.status, o.company_id, c.name AS company_name FROM opportunities o LEFT JOIN companies c ON c.id = o.company_id ORDER BY o.published_at DESC, o.id DESC'
            );
            return $statement->fetchAll();
        }

        $opportunities = [
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
                'company_id' => 2,
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
                'company_id' => 3,
            ],
        ];
        foreach ($opportunities as &$opportunity) {
            if ($opportunity['status'] === 'published' && $opportunity['deadline'] < date('Y-m-d')) {
                $_SESSION['demo_opportunity_status'][$opportunity['id']] = 'closed';
                $opportunity['status'] = 'closed';
                $this->recordDemoAudit(null, 'opportunity_auto_closed', 'opportunity', $opportunity['id']);
            }
            if (isset($_SESSION['demo_opportunity_status'][$opportunity['id']])) {
                $opportunity['status'] = $_SESSION['demo_opportunity_status'][$opportunity['id']];
            }
            if (isset($_SESSION['demo_opportunity_company'][$opportunity['id']])) {
                $company = $this->getCompany((int) $_SESSION['demo_opportunity_company'][$opportunity['id']], null);
                $opportunity['company_id'] = (int) $_SESSION['demo_opportunity_company'][$opportunity['id']];
                if ($company) {
                    $opportunity['company'] = $company['name'];
                }
            }
        }
        unset($opportunity);
        return $opportunities;
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

    private function queryAlumni(array $filters, ?array $viewer, bool $includeUnverified, ?int $limit = null, int $offset = 0, ?int $companyId = null): array
    {
        [$where, $params] = $this->alumniFilterSql($filters, $viewer, $includeUnverified);
        if ($companyId !== null) {
            $where .= ' AND p.company_id = :company_id';
            $params[':company_id'] = $companyId;
        }
        $sql = 'SELECT p.id AS profile_id, u.id, u.name, u.email, p.department, p.graduation_year, '
            . 'COALESCE(c.name, p.current_company, "") AS company, p.company_id, p.job_title, '
            . 'COALESCE(c.industry, p.industry, "") AS industry, '
            . 'CASE WHEN p.email_contact_visibility = "public" OR (p.email_contact_visibility = "logged_in" AND :viewer_email IS NOT NULL) THEN u.email ELSE NULL END AS contact_email, '
            . 'p.email_contact_visibility AS email_visibility, p.verification_status, p.visibility, p.bio, p.skills '
            . "FROM profiles p JOIN users u ON u.id = p.user_id LEFT JOIN companies c ON c.id = p.company_id WHERE u.role = 'alumni' AND u.status = 'active' {$where} "
            . 'ORDER BY p.graduation_year DESC, p.full_name ASC';
        if ($limit !== null) {
            $sql .= ' LIMIT :limit OFFSET :offset';
        }
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':viewer_email', $viewer ? $viewer['email'] : null, $viewer ? PDO::PARAM_STR : PDO::PARAM_NULL);
        foreach ($params as $key => $value) {
            $statement->bindValue($key, $value);
        }
        if ($limit !== null) {
            $statement->bindValue(':limit', max(1, min(50, $limit)), PDO::PARAM_INT);
            $statement->bindValue(':offset', max(0, $offset), PDO::PARAM_INT);
        }
        $statement->execute();
        $people = [];
        foreach ($statement->fetchAll() as $row) {
            $row['verified'] = $row['verification_status'] === 'verified';
            $row['email_contact'] = $row['contact_email'] !== null;
            $row['email'] = $row['contact_email'] ?? '';
            unset($row['contact_email']);
            $row['skills'] = array_values(array_filter(array_map('trim', explode(',', (string) ($row['skills'] ?? '')))));
            $people[] = $row;
        }
        return $people;
    }

    private function countAlumni(array $filters, ?array $viewer, ?int $companyId = null): int
    {
        [$where, $params] = $this->alumniFilterSql($filters, $viewer, false);
        if ($companyId !== null) {
            $where .= ' AND p.company_id = :company_id';
            $params[':company_id'] = $companyId;
        }
        $statement = $this->pdo->prepare(
            "SELECT COUNT(*) FROM profiles p JOIN users u ON u.id = p.user_id LEFT JOIN companies c ON c.id = p.company_id WHERE u.role = 'alumni' AND u.status = 'active' {$where}"
        );
        foreach ($params as $key => $value) {
            $statement->bindValue($key, $value);
        }
        $statement->execute();
        return (int) $statement->fetchColumn();
    }

    private function alumniFilterSql(array $filters, ?array $viewer, bool $includeUnverified): array
    {
        $clauses = [];
        $params = [];
        if ($includeUnverified) {
            $clauses[] = "p.verification_status IN ('pending', 'rejected')";
        } else {
            $clauses[] = "p.verification_status = 'verified'";
            $clauses[] = "p.visibility <> 'private'";
            if (!$viewer) {
                $clauses[] = "p.visibility = 'public'";
            }
        }
        $fields = [
            'name' => 'p.full_name',
            'department' => 'p.department',
            'company' => 'COALESCE(c.name, p.current_company, "")',
            'industry' => 'COALESCE(c.industry, p.industry, "")',
            'job_title' => 'p.job_title',
        ];
        foreach ($fields as $key => $field) {
            $value = trim((string) ($filters[$key] ?? ''));
            if ($value !== '') {
                $parameter = ':filter_' . $key;
                $clauses[] = "{$field} LIKE {$parameter}";
                $params[$parameter] = '%' . $value . '%';
            }
        }
        $year = trim((string) ($filters['graduation_year'] ?? ''));
        if ($year !== '' && ctype_digit($year)) {
            $clauses[] = 'p.graduation_year = :filter_graduation_year';
            $params[':filter_graduation_year'] = (int) $year;
        }
        return [' AND ' . implode(' AND ', $clauses), $params];
    }

    private function companyFilterSql(array $filters): array
    {
        $clauses = [];
        $params = [];
        foreach (['keyword' => ['c.name', 'c.industry', 'c.description'], 'industry' => ['c.industry'], 'location' => ['c.location']] as $key => $columns) {
            $value = trim((string) ($filters[$key] ?? ''));
            if ($value === '') {
                continue;
            }
            $searches = [];
            foreach ($columns as $index => $column) {
                $parameter = ':company_' . $key . '_' . $index;
                $searches[] = "{$column} LIKE {$parameter}";
                $params[$parameter] = '%' . $value . '%';
            }
            $clauses[] = '(' . implode(' OR ', $searches) . ')';
        }
        return [$clauses ? ' AND ' . implode(' AND ', $clauses) : '', $params];
    }

    private function queryCompanies(array $filters, ?array $viewer, ?int $limit = null, int $offset = 0): array
    {
        [$where, $params] = $this->companyFilterSql($filters);
        $visibleClause = $viewer
            ? "p.visibility IN ('public', 'logged_in')"
            : "p.visibility = 'public'";
        $sql = 'SELECT c.id, c.name, c.industry, c.website, c.location, c.description, '
            . "COUNT(DISTINCT p.id) AS alumni_count FROM companies c LEFT JOIN profiles p ON p.company_id = c.id AND p.verification_status = 'verified' AND {$visibleClause} AND p.user_id IN (SELECT id FROM users WHERE role = 'alumni' AND status = 'active') "
            . "WHERE c.status = 'active' {$where} GROUP BY c.id, c.name, c.industry, c.website, c.location, c.description ORDER BY c.name ASC";
        if ($limit !== null) {
            $sql .= ' LIMIT :limit OFFSET :offset';
        }
        $statement = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $statement->bindValue($key, $value);
        }
        if ($limit !== null) {
            $statement->bindValue(':limit', max(1, min(50, $limit)), PDO::PARAM_INT);
            $statement->bindValue(':offset', max(0, $offset), PDO::PARAM_INT);
        }
        $statement->execute();
        return $statement->fetchAll();
    }

    private function canShowEmail(array $person, ?array $viewer): bool
    {
        $visibility = $person['email_visibility'] ?? 'disabled';
        return $visibility === 'public' || ($visibility === 'logged_in' && $viewer !== null);
    }

    private function inputString(array $input, string $key): string
    {
        return isset($input[$key]) && is_string($input[$key]) ? trim($input[$key]) : '';
    }

    private function characterCount(string $value): int
    {
        $count = preg_match_all('/./us', $value);
        return $count === false ? PHP_INT_MAX : $count;
    }

    private function writeAudit(?int $actorId, string $action, string $targetType, ?int $targetId, array $details = []): void
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO admin_audit_log (actor_id, action, target_type, target_id, details) VALUES (:actor_id, :action, :target_type, :target_id, :details)'
        );
        $statement->execute([
            ':actor_id' => $actorId,
            ':action' => $action,
            ':target_type' => $targetType,
            ':target_id' => $targetId,
            ':details' => $details ? json_encode($details, JSON_THROW_ON_ERROR) : null,
        ]);
    }

    private function recordDemoAudit(?int $actorId, string $action, string $targetType, int $targetId): void
    {
        $_SESSION['demo_audit'][] = [
            'actor_id' => $actorId,
            'action' => $action,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'created_at' => date('Y-m-d H:i:s'),
        ];
    }

    private function closeExpiredOpportunities(): void
    {
        $expired = $this->pdo->query(
            "SELECT id FROM opportunities WHERE status = 'published' AND deadline < CURRENT_DATE"
        )->fetchAll(PDO::FETCH_COLUMN);
        if (!$expired) {
            return;
        }
        $this->pdo->beginTransaction();
        try {
            $update = $this->pdo->prepare("UPDATE opportunities SET status = 'closed' WHERE id = :id AND status = 'published'");
            foreach ($expired as $id) {
                $update->execute([':id' => (int) $id]);
                if ($update->rowCount() === 1) {
                    $this->writeAudit(null, 'opportunity_auto_closed', 'opportunity', (int) $id);
                }
            }
            $this->pdo->commit();
        } catch (Throwable $error) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $error;
        }
    }
}
