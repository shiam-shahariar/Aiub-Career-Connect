<?php
class Database
{
    public function getConnection(): ?PDO
    {
        if (!class_exists('PDO')) {
            return null;
        }

        $host = getenv('DB_HOST') ?: 'localhost';
        $db = getenv('DB_NAME') ?: 'aiub_career_connect';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') ?: '';

        try {
            $pdo = new PDO("mysql:host={$host};dbname={$db};charset=utf8mb4", $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            return $pdo;
        } catch (PDOException $e) {
            error_log('AIUB Career Connect database connection failed: ' . $e->getMessage());
            return null;
        }
    }
}
