<?php
require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Quản lý Buổi học';

// Xử lý Xóa
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM lessons WHERE id = ?");
    $stmt->execute([$id]);
    flash('Đã xóa buổi học thành công!', 'success');
    redirect(BASE_URL . '/schedule.php');
}

// Xử lý Thêm mới
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_lesson'])) {
    $subject_name = trim($_POST['subject_name'] ?? '');
    $class_id     = (int)($_POST['class_id'] ?? 0);
    $teacher_id   = (int)($_POST['teacher_id'] ?? 0);

    if ($subject_name !== '' && $class_id > 0 && $teacher_id > 0) {
        $stmt = $pdo->prepare("INSERT INTO lessons (subject_name, class_id, teacher_id) VALUES (?, ?, ?)");
        $stmt->execute([$subject_name, $class_id, $teacher_id]);
        flash('Thêm buổi học mới thành công!', 'success');
        redirect(BASE_URL . '/schedule.php');
    } else {
        flash('Vui lòng nhập đầy đủ Tên môn, chọn Lớp và Giảng viên!', 'danger');
    }
}

$classesList = $pdo->query("SELECT id, code FROM classes ORDER BY code ASC")->fetchAll();
$teachersList = $pdo->query("SELECT id, full_name FROM teachers ORDER BY full_name ASC")->fetchAll();

$lessons = $pdo->query(
    "SELECT l.*, t.full_name AS teacher_name, c.code AS class_code
     FROM lessons l
     LEFT JOIN teachers t ON t.id = l.teacher_id
     LEFT JOIN classes c ON c.id = l.class_id
     ORDER BY l.id DESC"
)->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-heading">
    <div>
        <h1>Quản lý Buổi học</h1>
        <p>Phân công môn học, giảng viên và lớp học cần xếp lịch.</p>
    </div>
</div>

<!-- Form Thêm Buổi học -->
<div class="panel" style="margin-bottom: 24px;">
    <div class="panel-heading">
        <div><h2>+ Khai báo buổi học mới</h2></div>
    </div>
    <form method="POST" action="" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-top: 10px;">
        <input type="hidden" name="add_lesson" value="1">
        
        <div>
            <label style="display:block; margin-bottom: 6px; font-weight: 500; font-size: 14px;">Tên môn học:</label>
            <input type="text" name="subject_name" required placeholder="VD: Lập trình PHP" style="width:100%; padding: 10px; border-radius: 6px; background: #111827; color: #fff; border: 1px solid #374151;">
        </div>

        <div>
            <label style="display:block; margin-bottom: 6px; font-weight: 500; font-size: 14px;">Lớp học:</label>
            <select name="class_id" required style="width:100%; padding: 10px; border-radius: 6px; background: #111827; color: #fff; border: 1px solid #374151;">
                <option value="">-- Chọn lớp học --</option>
                <?php foreach ($classesList as $cls): ?>
                    <option value="<?= $cls['id'] ?>"><?= e($cls['code']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label style="display:block; margin-bottom: 6px; font-weight: 500; font-size: 14px;">Giảng viên:</label>
            <select name="teacher_id" required style="width:100%; padding: 10px; border-radius: 6px; background: #111827; color: #fff; border: 1px solid #374151;">
                <option value="">-- Chọn giảng viên --</option>
                <?php foreach ($teachersList as $tch): ?>
                    <option value="<?= $tch['id'] ?>"><?= e($tch['full_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="display: flex; align-items: flex-end;">
            <button type="submit" class="button" style="width: 100%; padding: 11px; cursor: pointer;">+ Thêm buổi học</button>
        </div>
    </form>
</div>

<!-- Bảng danh sách -->
<div class="panel">
    <div class="panel-heading">
        <div><h2>Danh sách buổi học (<?= count($lessons) ?>)</h2></div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Tên môn học</th>
                    <th>Lớp học</th>
                    <th>Giảng viên</th>
                    <th style="text-align: center;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$lessons): ?>
                <tr><td colspan="5" class="empty-cell">Chưa có dữ liệu buổi học.</td></tr>
            <?php else: ?>
                <?php foreach ($lessons as $index => $row): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><strong><?= e($row['subject_name'] ?? 'N/A') ?></strong></td>
                        <td><span class="badge"><?= e($row['class_code'] ?? 'N/A') ?></span></td>
                        <td><?= e($row['teacher_name'] ?? 'Chưa phân công') ?></td>
                        <td style="text-align: center;">
                            <a href="<?= BASE_URL ?>/schedule.php?action=delete&id=<?= $row['id'] ?>" 
                               onclick="return confirm('Bạn có chắc muốn xóa buổi học này?');"
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