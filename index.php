<?php
session_start();

require __DIR__ . '/Model/DataRepository.php';
require __DIR__ . '/Model/Database.php';
require __DIR__ . '/Controller/AuthController.php';
require __DIR__ . '/Controller/OpportunityController.php';

$database = new Database();
$repository = new DataRepository($database->getConnection());
$auth = new AuthController($repository);
$opportunityController = new OpportunityController($repository);
$_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));
$postString = static function (string $key): string {
    return isset($_POST[$key]) && is_string($_POST[$key]) ? trim($_POST[$key]) : '';
};
$postInt = static function (string $key) use ($postString): int {
    $value = filter_var($postString($key), FILTER_VALIDATE_INT);
    return $value === false ? 0 : $value;
};

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $csrfActions = [
        'login',
        'register',
        'save_profile',
        'logout',
        'review_alumni',
        'save_profile_privacy',
        'save_company',
        'merge_companies',
        'update_opportunity_status',
        'submit_profile_report',
        'review_profile_report',
    ];
    $submittedToken = isset($_POST['csrf_token']) && is_string($_POST['csrf_token'])
        ? $_POST['csrf_token']
        : '';
    if (in_array($action, $csrfActions, true)
        && !hash_equals($_SESSION['csrf_token'], $submittedToken)) {
        http_response_code(403);
        exit('Invalid request token. Please reload the page and try again.');
    }

    $currentUser = $auth->currentUser();

    if ($action === 'review_alumni') {
        if (!$currentUser || $currentUser['role'] !== 'admin') {
            http_response_code(403);
            exit('Administrator access is required.');
        }
        $note = $postString('review_note');
        $noteLength = preg_match_all('/./us', $note);
        if ($noteLength === false || $noteLength > 2000) {
            $success = false;
            $message = 'Review notes cannot exceed 2,000 characters.';
        } else {
            $success = $repository->reviewAlumni(
                $postInt('profile_id'),
                $postString('status'),
                $note,
                (int) $currentUser['id']
            );
            $message = $success ? 'Alumni verification review saved.' : 'The profile could not be reviewed.';
        }
        $_SESSION['flashMessage'] = $message;
        $_SESSION['flashSuccess'] = $success;
        header('Location: index.php?page=admin');
        exit;
    }

    if ($action === 'save_profile_privacy') {
        if (!$currentUser || $currentUser['role'] !== 'alumni') {
            http_response_code(403);
            exit('Alumni access is required.');
        }
        $success = $repository->saveProfilePrivacy(
            (int) $currentUser['id'],
            $postString('visibility'),
            $postString('email_visibility')
        );
        $_SESSION['flashMessage'] = $success
            ? 'Your profile visibility settings have been updated.'
            : 'The profile visibility settings could not be updated.';
        $_SESSION['flashSuccess'] = $success;
        header('Location: index.php?page=profile');
        exit;
    }

    if ($action === 'save_company' || $action === 'merge_companies') {
        if (!$currentUser || $currentUser['role'] !== 'admin') {
            http_response_code(403);
            exit('Administrator access is required.');
        }
        if ($action === 'save_company') {
            $website = $postString('website');
            $validWebsite = $website === ''
                || (filter_var($website, FILTER_VALIDATE_URL) && in_array(strtolower((string) parse_url($website, PHP_URL_SCHEME)), ['http', 'https'], true));
            $success = $validWebsite && $repository->saveCompany($_POST, (int) $currentUser['id']);
            $message = $success
                ? 'Company details saved.'
                : ($validWebsite ? 'Company details could not be saved. Check for a duplicate name and location.' : 'Enter a valid HTTP or HTTPS company website.');
        } else {
            $success = $repository->mergeCompanies(
                $postInt('source_company_id'),
                $postInt('target_company_id'),
                (int) $currentUser['id']
            );
            $message = $success
                ? 'Company records merged.'
                : 'The records could not be merged. Select two different active records with the same normalized name and location.';
        }
        $_SESSION['flashMessage'] = $message;
        $_SESSION['flashSuccess'] = $success;
        header('Location: index.php?page=admin');
        exit;
    }

    if ($action === 'update_opportunity_status') {
        if (!$currentUser || $currentUser['role'] !== 'admin') {
            http_response_code(403);
            exit('Administrator access is required.');
        }
        $success = $repository->updateOpportunityStatus(
            $postInt('opportunity_id'),
            $postString('status'),
            (int) $currentUser['id'],
            $postInt('company_id')
        );
        $_SESSION['flashMessage'] = $success ? 'Opportunity status updated.' : 'The opportunity status could not be updated.';
        $_SESSION['flashSuccess'] = $success;
        header('Location: index.php?page=admin');
        exit;
    }

    if ($action === 'submit_profile_report') {
        if (!$currentUser) {
            http_response_code(403);
            exit('Please log in to report a profile.');
        }
        $reason = $postString('reason');
        $details = $postString('details');
        $reasonLength = preg_match_all('/./us', $reason);
        $detailsLength = preg_match_all('/./us', $details);
        $success = $reasonLength !== false && $reasonLength <= 100
            && $detailsLength !== false && $detailsLength <= 2000
            && $repository->submitProfileReport(
                $postInt('profile_id'),
                (int) $currentUser['id'],
                $reason,
                $details
            );
        $_SESSION['flashMessage'] = $success ? 'The profile report was sent to administrators.' : 'The profile report could not be submitted.';
        $_SESSION['flashSuccess'] = $success;
        header('Location: index.php?page=alumni');
        exit;
    }

    if ($action === 'review_profile_report') {
        if (!$currentUser || $currentUser['role'] !== 'admin') {
            http_response_code(403);
            exit('Administrator access is required.');
        }
        $success = $repository->reviewProfileReport(
            $postInt('report_id'),
            $postString('status'),
            (int) $currentUser['id']
        );
        $_SESSION['flashMessage'] = $success ? 'Profile report review saved.' : 'The profile report could not be reviewed.';
        $_SESSION['flashSuccess'] = $success;
        header('Location: index.php?page=admin');
        exit;
    }

    if ($action === 'login') {
        $result = $auth->login($_POST);
        $_SESSION['flashMessage'] = $result['message'];
        $_SESSION['flashSuccess'] = $result['success'];
        if ($result['success']) {
            session_regenerate_id(true);
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            header('Location: index.php?page=dashboard');
            exit;
        }
    }

    if ($action === 'register') {
        $result = $auth->register($_POST);
        $_SESSION['flashMessage'] = $result['message'];
        $_SESSION['flashSuccess'] = $result['success'];
        if ($result['success']) {
            session_regenerate_id(true);
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            header('Location: index.php?page=dashboard');
            exit;
        }
    }

    if ($action === 'save_profile') {
        if (!$currentUser) {
            $_SESSION['flashMessage'] = 'Please log in to save your profile.';
            $_SESSION['flashSuccess'] = false;
            header('Location: index.php?page=login');
            exit;
        }

        $profile = [
            'full_name' => $postString('full_name'),
            'email' => $postString('email'),
            'department' => $postString('department'),
            'graduation_year' => $postString('graduation_year'),
            'company' => $postString('company'),
            'skills' => $postString('skills'),
            'bio' => $postString('bio'),
        ];
        $lengths = array_map(
            static fn (string $value): int|false => preg_match_all('/./us', $value),
            $profile
        );
        $validProfile = $profile['full_name'] !== ''
            && filter_var($profile['email'], FILTER_VALIDATE_EMAIL) !== false
            && $lengths['full_name'] !== false && $lengths['full_name'] <= 150
            && $lengths['email'] !== false && $lengths['email'] <= 180
            && $lengths['department'] !== false && $lengths['department'] <= 100
            && $lengths['graduation_year'] !== false
            && ($profile['graduation_year'] === '' || preg_match('/^\d{4}$/', $profile['graduation_year']) === 1)
            && $lengths['company'] !== false && $lengths['company'] <= 150
            && $lengths['skills'] !== false && $lengths['skills'] <= 2000
            && $lengths['bio'] !== false && $lengths['bio'] <= 5000;
        if ($validProfile) {
            $_SESSION['demo_profiles'][(int) $currentUser['id']] = $profile;
            $_SESSION['user']['name'] = $profile['full_name'];
            $_SESSION['user']['email'] = $profile['email'];
            $_SESSION['user']['department'] = $profile['department'];
            $_SESSION['flashMessage'] = 'Demo profile changes saved for this session only.';
            $_SESSION['flashSuccess'] = true;
        } else {
            $_SESSION['flashMessage'] = 'Profile changes were not saved. Check the required fields and length limits.';
            $_SESSION['flashSuccess'] = false;
        }
        header('Location: index.php?page=profile');
        exit;
    }

    if ($action === 'logout') {
        $auth->logout();
        session_regenerate_id(true);
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['flashMessage'] = 'You have been logged out.';
        $_SESSION['flashSuccess'] = true;
        $_SESSION['flashOverlay'] = true;
        header('Location: index.php?page=home');
        exit;
    }
}

if (isset($_GET['ajax']) && $_GET['ajax'] === '1' && ($_GET['page'] ?? '') === 'opportunities') {
    header('Content-Type: application/json; charset=utf-8');
    try {
        echo json_encode($opportunityController->search([
            'keyword' => isset($_GET['keyword']) && is_string($_GET['keyword']) ? $_GET['keyword'] : '',
            'type' => isset($_GET['type']) && is_string($_GET['type']) ? $_GET['type'] : '',
            'department' => isset($_GET['department']) && is_string($_GET['department']) ? $_GET['department'] : '',
        ]), JSON_THROW_ON_ERROR);
    } catch (PDOException $error) {
        error_log('AIUB Career Connect opportunity search failed: ' . $error->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Opportunity search is temporarily unavailable.']);
    } catch (JsonException $error) {
        error_log('AIUB Career Connect opportunity search response encoding failed: ' . $error->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Opportunity search response could not be encoded.']);
    }
    exit;
}

$page = $_GET['page'] ?? 'home';
$currentUser = $auth->currentUser();

$allowedPages = [
    'home',
    'register',
    'login',
    'dashboard',
    'profile',
    'alumni',
    'opportunities',
    'opportunity-detail',
    'admin',
    'companies',
    'company-detail',
];

if (!in_array($page, $allowedPages, true)) {
    $page = 'home';
}

require __DIR__ . '/View/' . $page . '.php';
