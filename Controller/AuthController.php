<?php
class AuthController
{
    private DataRepository $repository;

    public function __construct(DataRepository $repository)
    {
        $this->repository = $repository;
    }

    public function currentUser(): ?array
    {
        if (!isset($_SESSION['user'])) {
            return null;
        }

        return $_SESSION['user'];
    }

    public function login(array $post): array
    {
        $email = trim((string) ($post['email'] ?? ''));
        $password = (string) ($post['password'] ?? '');

        if ($email === '' || $password === '') {
            return ['success' => false, 'message' => 'Email and password are required.'];
        }

        $user = $this->repository->findUserByEmail($email);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            return ['success' => false, 'message' => 'Invalid email or password.'];
        }

        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'department' => $user['department'],
        ];

        return ['success' => true, 'message' => 'Welcome back, ' . $user['name'] . '!'];
    }

    public function register(array $post): array
    {
        $name = trim((string) ($post['name'] ?? ''));
        $email = trim((string) ($post['email'] ?? ''));
        $role = trim((string) ($post['role'] ?? 'student'));
        $password = (string) ($post['password'] ?? '');
        $confirm = (string) ($post['confirm_password'] ?? '');

        if ($name === '' || $email === '' || $password === '' || $confirm === '') {
            return ['success' => false, 'message' => 'Please complete all fields.'];
        }

        if ($password !== $confirm) {
            return ['success' => false, 'message' => 'Passwords do not match.'];
        }

        if (!in_array($role, ['student', 'alumni'], true)) {
            return ['success' => false, 'message' => 'Invalid account type selected.'];
        }

        if ($this->repository->findUserByEmail($email)) {
            return ['success' => false, 'message' => 'An account with this email already exists.'];
        }

        $newUser = [
            'id' => 99,
            'name' => $name,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'role' => $role,
            'status' => 'active',
            'department' => 'General',
        ];

        $_SESSION['user'] = [
            'id' => $newUser['id'],
            'name' => $newUser['name'],
            'email' => $newUser['email'],
            'role' => $newUser['role'],
            'department' => $newUser['department'],
        ];

        return ['success' => true, 'message' => 'Your account has been created successfully.'];
    }

    public function logout(): void
    {
        unset($_SESSION['user']);
    }
}
