<?php
session_start();

require __DIR__ . '/Model/DataRepository.php';
require __DIR__ . '/Model/Database.php';
require __DIR__ . '/Controller/AuthController.php';
require __DIR__ . '/Controller/OpportunityController.php';

$repository = new DataRepository();
$auth = new AuthController($repository);
$opportunityController = new OpportunityController($repository);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'login') {
        $result = $auth->login($_POST);
        $_SESSION['flashMessage'] = $result['message'];
        $_SESSION['flashSuccess'] = $result['success'];
        if ($result['success']) {
            header('Location: index.php?page=dashboard');
            exit;
        }
    }

    if ($action === 'register') {
        $result = $auth->register($_POST);
        $_SESSION['flashMessage'] = $result['message'];
        $_SESSION['flashSuccess'] = $result['success'];
        if ($result['success']) {
            header('Location: index.php?page=dashboard');
            exit;
        }
    }

    if ($action === 'save_profile') {
        $user = $auth->currentUser();
        if ($user) {
            $_SESSION['flashMessage'] = 'Demo profile update saved for this session only. Connect the form to a real database in production.';
            $_SESSION['flashSuccess'] = true;
            header('Location: index.php?page=profile');
            exit;
        }

        $_SESSION['flashMessage'] = 'Please log in to save your profile.';
        $_SESSION['flashSuccess'] = false;
        header('Location: index.php?page=login');
        exit;
    }

    if ($action === 'logout') {
        $auth->logout();
        $_SESSION['flashMessage'] = 'You have been logged out.';
        $_SESSION['flashSuccess'] = true;
        $_SESSION['flashOverlay'] = true;
        header('Location: index.php?page=home');
        exit;
    }
}

if (isset($_GET['ajax']) && $_GET['ajax'] === '1' && ($_GET['page'] ?? '') === 'opportunities') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($opportunityController->search([
        'keyword' => $_GET['keyword'] ?? '',
        'type' => $_GET['type'] ?? '',
        'department' => $_GET['department'] ?? '',
    ]));
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
];

if (!in_array($page, $allowedPages, true)) {
    $page = 'home';
}

require __DIR__ . '/View/' . $page . '.php';
