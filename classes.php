<?php
require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Quản lý Lớp học';

// Xử lý Xóa
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM classes WHERE id = ?");
    $stmt->execute([$id]);
    flash('Đã xóa lớp học thành công!', 'success');
    redirect(BASE_URL . '/classes.php');
}

// Xử lý Thêm mới
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_class'])) {
    $code          = trim($_POST['code'] ?? '');
    $student_count = (int)($_POST['student_count'] ?? 40);

    if ($code !== '') {
        // Kiểm tra xem bảng có cột name hay không để insert tương ứng
        $stmt = $pdo->prepare("INSERT INTO classes (code, student_count) VALUES (?, ?)");
        $stmt->execute([$code, $student_count]);
        flash('Thêm lớp học mới thành công!', 'success');
        redirect(BASE_URL . '/classes.php');
    } else {
        flash('Vui lòng nhập mã lớp học!', 'danger');
    }
}

$classes = $pdo->query("SELECT * FROM classes ORDER BY id DESC")->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-heading">
    <div>
        <h1>Quản lý Lớp học</h1>
        <p>Thêm mới, quản lý sĩ số và xóa lớp học.</p>
    </div>
</div>

<!-- Form Thêm Lớp -->
<div class="panel" style="margin-bottom: 24px;">
    <div class="panel-heading">
        <div><h2>+ Thêm lớp học mới</h2></div>
    </div>
    <form method="POST" action="" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-top: 10px;">
        <input type="hidden" name="add_class" value="1">
        <div>
            <label style="display:block; margin-bottom: 6px; font-weight: 500; font-size: 14px;">Mã lớp:</label>
            <input type="text" name="code" required placeholder="VD: CNTT01" style="width:100%; padding: 10px; border-radius: 6px; background: #111827; color: #fff; border: 1px solid #374151;">
        </div>
        <div>
            <label style="display:block; margin-bottom: 6px; font-weight: 500; font-size: 14px;">Sĩ số sinh viên:</label>
            <input type="number" name="student_count" value="40" min="1" style="width:100%; padding: 10px; border-radius: 6px; background: #111827; color: #fff; border: 1px solid #374151;">
        </div>
        <div style="display: flex; align-items: flex-end;">
            <button type="submit" class="button" style="width: 100%; padding: 11px; cursor: pointer;">+ Thêm lớp học</button>
        </div>
    </form>
</div>

<!-- Bảng danh sách -->
<div class="panel">
    <div class="panel-heading">
        <div><h2>Danh sách lớp học (<?= count($classes) ?>)</h2></div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Mã lớp</th>
                    <th>Sĩ số</th>
                    <th style="text-align: center;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$classes): ?>
                <tr><td colspan="4" class="empty-cell">Chưa có dữ liệu lớp học.</td></tr>
            <?php else: ?>
                <?php foreach ($classes as $index => $row): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><span class="badge"><?= e($row['code'] ?? 'Lớp ' . $row['id']) ?></span></td>
                        <td><?= e($row['student_count'] ?? 40) ?> sinh viên</td>
                        <td style="text-align: center;">
                            <a href="<?= BASE_URL ?>/classes.php?action=delete&id=<?= $row['id'] ?>" 
                               onclick="return confirm('Bạn có chắc muốn xóa lớp học này?');"
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