<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Asia/Ho_Chi_Minh');

define('BASE_URL', '/XepThoiKhoaBieu');

require_once __DIR__ . '/../config/database.php';

function e(mixed $value): string
{
    return htmlspecialchars(
        (string)($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function flash(string $message, string $type = 'success'): void
{
    $_SESSION['flash'] = [
        'message' => $message,
        'type'    => $type
    ];
}

function getFlash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $message = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $message;
}

function countTable(PDO $pdo, string $table): int
{
    $allowed = [
        'teachers',
        'classes',
        'rooms',
        'lessons',
        'schedules',
        'slots'
    ];

    if (!in_array($table, $allowed, true)) {
        return 0;
    }

    return (int)$pdo->query(
        "SELECT COUNT(*) FROM `$table`"
    )->fetchColumn();
}