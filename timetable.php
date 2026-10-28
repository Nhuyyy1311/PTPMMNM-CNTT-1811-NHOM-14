<?php
require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Thời khóa biểu';

// 1. XỬ LÝ XÓA LỊCH HỌC (ĐẶT LÊN TRÊN CÙNG ĐỂ NHẬN DIỆN CHÍNH XÁC)
if (isset($_GET['action']) && $_GET['action'] === 'delete' && !empty($_GET['id'])) {
    $deleteId = (int)$_GET['id'];
    if ($deleteId > 0) {
        $stmt = $pdo->prepare("DELETE FROM schedule_items WHERE id = ?");
        $stmt->execute([$deleteId]);
        flash('Đã xóa buổi học khỏi thời khóa biểu thành công!', 'success');
        redirect(BASE_URL . '/timetable.php');
    }
}

// 2. XỬ LÝ THÊM MỚI LỊCH HỌC
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_schedule'])) {
    $lesson_id = (int)($_POST['lesson_id'] ?? 0);
    $room_id   = (int)($_POST['room_id'] ?? 0);
    $slot_id   = (int)($_POST['slot_id'] ?? 0);

    if ($lesson_id > 0 && $room_id > 0 && $slot_id > 0) {
        $versionStmt = $pdo->query("SELECT id FROM schedule_versions ORDER BY id ASC LIMIT 1");
        $version = $versionStmt->fetch();

        if (!$version) {
            $pdo->exec("INSERT INTO schedule_versions () VALUES ()");
            $version_id = (int)$pdo->lastInsertId();
            if ($version_id <= 0) { $version_id = 1; }
        } else {
            $version_id = (int)$version['id'];
        }

        $check = $pdo->prepare("SELECT COUNT(*) FROM schedule_items WHERE version_id = ? AND room_id = ? AND slot_id = ?");
        $check->execute([$version_id, $room_id, $slot_id]);
        
        if ($check->fetchColumn() > 0) {
            flash('Lỗi: Phòng học này đã có lớp sử dụng vào khung giờ/thứ đã chọn!', 'danger');
        } else {
            $ins = $pdo->prepare("INSERT INTO schedule_items (version_id, lesson_id, room_id, slot_id) VALUES (?, ?, ?, ?)");
            $ins->execute([$version_id, $lesson_id, $room_id, $slot_id]);
            flash('Thêm lịch học mới thành công!', 'success');
            redirect(BASE_URL . '/timetable.php');
        }
    } else {
        flash('Vui lòng chọn đầy đủ thông tin!', 'danger');
    }
}

$lessonsList = $pdo->query("
    SELECT l.id, l.subject_name, c.code AS class_code, t.full_name AS teacher_name 
    FROM lessons l
    LEFT JOIN classes c ON c.id = l.class_id
    LEFT JOIN teachers t ON t.id = l.teacher_id
")->fetchAll();

$roomsList = $pdo->query("SELECT id, code FROM rooms ORDER BY code ASC")->fetchAll();
$slotsList = $pdo->query("SELECT id, day_of_week, slot_no, start_time, end_time FROM time_slots ORDER BY day_of_week ASC, slot_no ASC")->fetchAll();

$schedules = $pdo->query("
    SELECT s.id, l.subject_name, t.full_name AS teacher_name,
           c.code AS class_code, r.code AS room_code,
           sl.day_of_week, sl.slot_no AS slot_number,
           sl.start_time, sl.end_time
    FROM schedule_items s
    JOIN lessons l ON l.id = s.lesson_id
    LEFT JOIN teachers t ON t.id = l.teacher_id
    LEFT JOIN classes c ON c.id = l.class_id
    LEFT JOIN rooms r ON r.id = s.room_id
    LEFT JOIN time_slots sl ON sl.id = s.slot_id
    ORDER BY sl.day_of_week ASC, sl.slot_no ASC
")->fetchAll();

$days = [
    1 => 'Thứ 2',
    2 => 'Thứ 3',
    3 => 'Thứ 4',
    4 => 'Thứ 5',
    5 => 'Thứ 6',
    6 => 'Thứ 7',
    7 => 'Chủ nhật'
];

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-heading">
    <div>
        <h1>Thời khóa biểu hệ thống</h1>
        <p>Quản lý, thêm mới và xóa lịch học chi tiết cho toàn trường.</p>
    </div>
</div>

<!-- FORM THÊM LỊCH HỌC MỚI -->
<div class="panel" style="margin-bottom: 24px;">
    <div class="panel-heading">
        <div>
            <h2>+ Phân công / Thêm lịch học mới</h2>
            <p>Chọn buổi học, phòng học và khung giờ cần xếp lịch.</p>
        </div>
    </div>

    <form method="POST" action="" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-top: 10px;">
        <input type="hidden" name="add_schedule" value="1">
        
        <div>
            <label style="display:block; margin-bottom: 6px; font-weight: 500; font-size: 14px;">Môn / Lớp học:</label>
            <select name="lesson_id" required style="width:100%; padding: 10px; border-radius: 6px; background: #111827; color: #fff; border: 1px solid #374151;">
                <option value="">-- Chọn bài giảng/lớp --</option>
                <?php foreach ($lessonsList as $les): ?>
                    <option value="<?= $les['id'] ?>">
                        <?= e($les['subject_name']) ?> (Lớp: <?= e($les['class_code']) ?> - GV: <?= e($les['teacher_name']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label style="display:block; margin-bottom: 6px; font-weight: 500; font-size: 14px;">Phòng học:</label>
            <select name="room_id" required style="width:100%; padding: 10px; border-radius: 6px; background: #111827; color: #fff; border: 1px solid #374151;">
                <option value="">-- Chọn phòng --</option>
                <?php foreach ($roomsList as $rm): ?>
                    <option value="<?= $rm['id'] ?>">Phòng: <?= e($rm['code']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label style="display:block; margin-bottom: 6px; font-weight: 500; font-size: 14px;">Thời gian (Thứ & Ca):</label>
            <select name="slot_id" required style="width:100%; padding: 10px; border-radius: 6px; background: #111827; color: #fff; border: 1px solid #374151;">
                <option value="">-- Chọn thứ & khung giờ --</option>
                <?php foreach ($slotsList as $sl): ?>
                    <option value="<?= $sl['id'] ?>">
                        <?= $days[(int)$sl['day_of_week']] ?? 'Thứ '.$sl['day_of_week'] ?> 
                        (Ca <?= $sl['slot_no'] ?>: <?= substr($sl['start_time'],0,5) ?> - <?= substr($sl['end_time'],0,5) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="display: flex; align-items: flex-end;">
            <button type="submit" class="button" style="width: 100%; padding: 11px; cursor: pointer;">
                + Xếp lịch ngay
            </button>
        </div>
    </form>
</div>

<!-- BẢNG DANH SÁCH THỜI KHÓA BIỂU -->
<div class="panel">
    <div class="panel-heading">
        <div>
            <h2>Lịch học chi tiết đã phân công</h2>
            <p>Tổng số buổi đã xếp: <strong><?= count($schedules) ?></strong> buổi</p>
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Thứ</th>
                    <th>Khung giờ</th>
                    <th>Môn học</th>
                    <th>Lớp</th>
                    <th>Giảng viên</th>
                    <th>Phòng học</th>
                    <th style="text-align: center;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$schedules): ?>
                <tr>
                    <td colspan="7" class="empty-cell">
                        Chưa có thời khóa biểu nào được lập. Hãy chọn ở form trên để xếp lịch!
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($schedules as $row): ?>
                    <tr>
                        <td><strong><?= e($days[(int)$row['day_of_week']] ?? 'Thứ ' . $row['day_of_week']) ?></strong></td>
                        <td>
                            <?= e(substr($row['start_time'], 0, 5)) ?>
                            -
                            <?= e(substr($row['end_time'], 0, 5)) ?>
                        </td>
                        <td><strong><?= e($row['subject_name']) ?></strong></td>
                        <td><?= e($row['class_code']) ?></td>
                        <td><?= e($row['teacher_name']) ?></td>
                        <td><span class="badge"><?= e($row['room_code']) ?></span></td>
                        <td style="text-align: center;">
                            <a href="<?= BASE_URL ?>/timetable.php?action=delete&id=<?= $row['id'] ?>" 
                               onclick="return confirm('Bạn có chắc chắn muốn xóa buổi học này khỏi thời khóa biểu?');"
                               style="color: #ef4444; font-weight: 600; text-decoration: none; padding: 4px 10px; border: 1px solid #ef4444; border-radius: 4px; font-size: 13px;">
                                ✕ Xóa
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>