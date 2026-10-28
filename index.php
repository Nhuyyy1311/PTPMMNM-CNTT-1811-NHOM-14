<?php
require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Tổng quan hệ thống';

$stats = [
    [
        'label' => 'Giảng viên',
        'value' => countTable($pdo, 'teachers'),
        'icon' => '♙',
        'class' => 'icon-purple',
        'caption' => 'Danh sách giảng viên'
    ],
    [
        'label' => 'Lớp học',
        'value' => countTable($pdo, 'classes'),
        'icon' => '▤',
        'class' => 'icon-green',
        'caption' => 'Các lớp cần xếp lịch'
    ],
    [
        'label' => 'Phòng học',
        'value' => countTable($pdo, 'rooms'),
        'icon' => '⌂',
        'class' => 'icon-orange',
        'caption' => 'Phòng học khả dụng'
    ],
    [
        'label' => 'Buổi học',
        'value' => countTable($pdo, 'lessons'),
        'icon' => '◷',
        'class' => 'icon-blue',
        'caption' => 'Tổng buổi học cần bố trí'
    ]
];

$totalLessons = countTable($pdo, 'lessons');
$totalScheduled = (int)$pdo->query(
    "SELECT COUNT(DISTINCT lesson_id) FROM schedule_items"
)->fetchColumn();

$pending = max(0, $totalLessons - $totalScheduled);

$latestSchedule = $pdo->query(
    "SELECT s.id, l.subject_name, t.full_name,
            c.code AS class_code, r.code AS room_code,
            sl.day_of_week, sl.slot_no AS slot_number,
            sl.start_time, sl.end_time
     FROM schedule_items s
     JOIN lessons l ON l.id = s.lesson_id
     JOIN teachers t ON t.id = l.teacher_id
     JOIN classes c ON c.id = l.class_id
     JOIN rooms r ON r.id = s.room_id
     JOIN time_slots sl ON sl.id = s.slot_id
     ORDER BY sl.day_of_week, sl.slot_no
     LIMIT 8"
)->fetchAll();

$days = [
    1 => 'Thứ 2',
    2 => 'Thứ 3',
    3 => 'Thứ 4',
    4 => 'Thứ 5',
    5 => 'Thứ 6',
    6 => 'Thứ 7'
];

require __DIR__ . '/includes/header.php';
?>

<div class="page-heading">
    <div>
        <h1>Tổng quan hệ thống</h1>
        <p>
            Quản lý dữ liệu và xếp thời khóa biểu theo
            ràng buộc giảng viên, lớp học và phòng học.
        </p>
    </div>
    <a href="<?= BASE_URL ?>/timetable.php" class="button">
        + Xem thời khóa biểu
    </a>
</div>

<div class="stats-grid">
    <?php foreach ($stats as $stat): ?>
        <div class="stat-card">
            <div class="stat-top">
                <span><?= e($stat['label']) ?></span>
                <span class="stat-icon <?= e($stat['class']) ?>">
                    <?= e($stat['icon']) ?>
                </span>
            </div>
            <div class="stat-number">
                <?= number_format($stat['value']) ?>
            </div>
            <div class="stat-caption">
                <?= e($stat['caption']) ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="panel">
    <div class="panel-heading">
        <div>
            <h2>Tiến độ xếp thời khóa biểu</h2>
            <p>Thống kê dựa trên dữ liệu hiện có trong hệ thống.</p>
        </div>
        <span class="badge">
            <?= $totalLessons > 0
                ? round(($totalScheduled / $totalLessons) * 100) . '%'
                : '0%' ?>
        </span>
    </div>

    <div style="height:10px;background:#edf0f6;border-radius:20px;overflow:hidden">
        <div style="height:100%;width:<?= $totalLessons > 0
            ? min(100, ($totalScheduled / $totalLessons) * 100)
            : 0 ?>%;background:#4f46e5;border-radius:20px">
        </div>
    </div>

    <div style="display:flex;justify-content:space-between;gap:10px;margin-top:12px;flex-wrap:wrap">
        <span>Đã xếp: <strong><?= $totalScheduled ?></strong> buổi</span>
        <span>Chưa xếp: <strong><?= $pending ?></strong> buổi</span>
    </div>
</div>

<div class="panel">
    <div class="panel-heading">
        <div>
            <h2>Chức năng hệ thống</h2>
            <p>Truy cập nhanh các chức năng chính của đề tài.</p>
        </div>
    </div>

    <div class="quick-grid">
        <a class="quick-card" href="<?= BASE_URL ?>/teachers.php">
            <div class="quick-icon">♙</div>
            <h3>Quản lý giảng viên</h3>
            <p>Thêm, sửa, xóa và quản lý danh sách giảng viên.</p>
            <span class="quick-link">Quản lý ngay →</span>
        </a>

        <a class="quick-card" href="<?= BASE_URL ?>/classes.php">
            <div class="quick-icon">▤</div>
            <h3>Quản lý lớp học</h3>
            <p>Quản lý mã lớp, tên lớp, số lượng sinh viên.</p>
            <span class="quick-link">Quản lý ngay →</span>
        </a>

        <a class="quick-card" href="<?= BASE_URL ?>/rooms.php">
            <div class="quick-icon">⌂</div>
            <h3>Quản lý phòng học</h3>
            <p>Quản lý sức chứa và loại phòng học.</p>
            <span class="quick-link">Quản lý ngay →</span>
        </a>

        <a class="quick-card" href="<?= BASE_URL ?>/schedule.php">
            <div class="quick-icon">◷</div>
            <h3>Quản lý buổi học</h3>
            <p>Khai báo môn học, lớp học, giảng viên và điều kiện.</p>
            <span class="quick-link">Quản lý ngay →</span>
        </a>

        <a class="quick-card" href="<?= BASE_URL ?>/timetable.php">
            <div class="quick-icon">▦</div>
            <h3>Thời khóa biểu</h3>
            <p>Xem lịch học, phòng học và khung giờ được phân công.</p>
            <span class="quick-link">Xem lịch học →</span>
        </a>

        <a class="quick-card" href="<?= BASE_URL ?>/timetable.php">
            <div class="quick-icon">⚙</div>
            <h3>Xếp lịch tự động</h3>
            <p>Tìm phương án phân công và kiểm tra các ràng buộc.</p>
            <span class="quick-link">Đi đến lập lịch →</span>
        </a>
    </div>
</div>

<div class="panel">
    <div class="panel-heading">
        <div>
            <h2>Lịch học đã được phân công</h2>
            <p>Tối đa 8 buổi học gần đầu thời khóa biểu.</p>
        </div>
        <a href="<?= BASE_URL ?>/timetable.php" class="button button-secondary">
            Xem tất cả
        </a>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Môn học</th>
                    <th>Lớp</th>
                    <th>Giảng viên</th>
                    <th>Phòng</th>
                    <th>Thứ</th>
                    <th>Khung giờ</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$latestSchedule): ?>
                <tr>
                    <td colspan="6" class="empty-cell">
                        Chưa có lịch học. Hãy nhập dữ liệu và thực hiện xếp lịch.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($latestSchedule as $row): ?>
                    <tr>
                        <td><strong><?= e($row['subject_name']) ?></strong></td>
                        <td><?= e($row['class_code']) ?></td>
                        <td><?= e($row['full_name']) ?></td>
                        <td><span class="badge"><?= e($row['room_code']) ?></span></td>
                        <td><?= e($days[(int)$row['day_of_week']] ?? 'Khác') ?></td>
                        <td>
                            <?= e(substr($row['start_time'], 0, 5)) ?>
                            -
                            <?= e(substr($row['end_time'], 0, 5)) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>