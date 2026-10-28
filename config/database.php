<?php
declare(strict_types=1);

$host = 'localhost';
$dbname = 'xep_thoi_khoa_bieu';
$username = 'root';
$password = '';

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    exit(
        'Không thể kết nối cơ sở dữ liệu. '
        . 'Hãy kiểm tra MySQL và file config/database.php.'
    );
}
