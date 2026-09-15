<?php
declare(strict_types=1);

/**
 * Крок 2. Підключення до бази даних через PDO.
 * Повертає готовий об'єкт $pdo для використання в решті скриптів.
 */

$dsn = 'mysql:host=localhost;dbname=practicum4;charset=utf8mb4';
$dbUser = 'root';
$dbPassword = '';

try {
    $pdo = new PDO($dsn, $dbUser, $dbPassword, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('Помилка підключення до бази даних: ' . htmlspecialchars($e->getMessage()));
}