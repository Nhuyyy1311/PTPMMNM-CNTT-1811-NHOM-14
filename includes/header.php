<?php
$pageTitle = $pageTitle ?? 'Hệ thống xếp thời khóa biểu';
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | Smart Timetable</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/style.css?v=3">
</head>
<body>
<div class="app-layout">
    <aside class="sidebar">
        <a class="brand" href="<?= BASE_URL ?>/index.php">
            <span class="brand-icon">ST</span>
            <span>
                <strong>Smart Timetable</strong>
                <small>Quản lý đào tạo</small>
            </span>
        </a>

        <div class="menu-label">TỔNG QUAN</div>

        <a class="menu-item <?= $currentPage === 'index.php' ? 'active' : '' ?>"
           href="<?= BASE_URL ?>/index.php">
            <span>Dashboard</span>
        </a>

        <div class="menu-label">DỮ LIỆU ĐÀO TẠO</div>

        <a class="menu-item <?= $currentPage === 'teachers.php' ? 'active' : '' ?>"
           href="<?= BASE_URL ?>/teachers.php">
            <span>Giảng viên</span>
        </a>

        <a class="menu-item <?= $currentPage === 'classes.php' ? 'active' : '' ?>"
           href="<?= BASE_URL ?>/classes.php">
            <span>Lớp học</span>
        </a>

        <a class="menu-item <?= $currentPage === 'rooms.php' ? 'active' : '' ?>"
           href="<?= BASE_URL ?>/rooms.php">
            <span>Phòng học</span>
        </a>

        <a class="menu-item <?= $currentPage === 'schedule.php' ? 'active' : '' ?>"
           href="<?= BASE_URL ?>/schedule.php">
            <span>Buổi học</span>
        </a>

        <a class="menu-item <?= $currentPage === 'time_slots.php' ? 'active' : '' ?>"
           href="<?= BASE_URL ?>/time_slots.php">
            <span>Khung giờ học</span>
        </a>

        <div class="menu-label">LẬP LỊCH</div>

        <a class="menu-item <?= $currentPage === 'timetable.php' ? 'active' : '' ?>"
           href="<?= BASE_URL ?>/timetable.php">
            <span>Thời khóa biểu</span>
        </a>

        <div class="sidebar-bottom">
            <div class="status-dot"></div>
            <div>
                <strong>Hệ thống xếp lịch</strong>
                <small>PHP + MySQL</small>
            </div>
        </div>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div>
                <span class="breadcrumb">Trang chủ</span>
                <span class="breadcrumb-separator">/</span>
                <strong><?= e($pageTitle) ?></strong>
            </div>
            <div class="user-profile">
                <span class="avatar">AD</span>
                <div>
                    <strong>Quản trị viên</strong>
                    <small>Quản lý thời khóa biểu</small>
                </div>
            </div>
        </header>

        <div class="content-area">
            <?php if ($notice = getFlash()): ?>
                <div class="alert alert-<?= e($notice['type']) ?>">
                    <?= e($notice['message']) ?>
                </div>
            <?php endif; ?>