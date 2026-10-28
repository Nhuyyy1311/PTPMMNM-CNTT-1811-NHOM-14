<?php
require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Quản lý Phòng học';

// 1. XỬ LÝ XÓA PHÒNG HỌC (ĐẶT LÊN TRÊN CÙNG ĐỂ NHẬN DIỆN CHÍNH XÁC)
if (isset($_GET['action']) && $_GET['action'] === 'delete' && !empty($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($id > 0) {
        $stmt = $pdo->prepare("DELETE FROM rooms WHERE id = ?");
        $stmt->execute([$id]);
        flash('Đã xóa phòng học thành công!', 'success');
        redirect(BASE_URL . '/rooms.php');
    }
}

// 2. XỬ LÝ THÊM MỚI PHÒNG HỌC
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_room'])) {
    $code     = trim($_POST['code'] ?? '');
    $capacity = (int)($_POST['capacity'] ?? 50);

    if ($code !== '') {
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM rooms WHERE code = ?");
        $checkStmt->execute([$code]);
        
        if ($checkStmt->fetchColumn() > 0) {
            flash('Lỗi: Mã phòng "' . e($code) . '" đã tồn tại trong hệ thống!', 'danger');
        } else {
            $stmt = $pdo->prepare("INSERT INTO rooms (code, capacity) VALUES (?, ?)");
            $stmt->execute([$code, $capacity]);
            flash('Thêm phòng học mới thành công!', 'success');
            redirect(BASE_URL . '/rooms.php');
        }
    } else {
        flash('Vui lòng nhập mã phòng học!', 'danger');
    }
}

$rooms = $pdo->query("SELECT * FROM rooms ORDER BY id DESC")->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-heading">
    <div>
        <h1>Quản lý Phòng học</h1>
        <p>Thêm mới, quản lý sức chứa và xóa phòng học.</p>
    </div>
</div>

<!-- Form Thêm Phòng -->
<div class="panel" style="margin-bottom: 24px;">
    <div class="panel-heading">
        <div><h2>+ Thêm phòng học mới</h2></div>
    </div>
    <form method="POST" action="" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-top: 10px;">
        <input type="hidden" name="add_room" value="1">
        <div>
            <label style="display:block; margin-bottom: 6px; font-weight: 500; font-size: 14px;">Mã phòng:</label>
            <input type="text" name="code" required placeholder="VD: P101" style="width:100%; padding: 10px; border-radius: 6px; background: #111827; color: #fff; border: 1px solid #374151;">
        </div>
        <div>
            <label style="display:block; margin-bottom: 6px; font-weight: 500; font-size: 14px;">Sức chứa (chỗ ngồi):</label>
            <input type="number" name="capacity" value="50" min="10" style="width:100%; padding: 10px; border-radius: 6px; background: #111827; color: #fff; border: 1px solid #374151;">
        </div>
        <div style="display: flex; align-items: flex-end;">
            <button type="submit" class="button" style="width: 100%; padding: 11px; cursor: pointer;">+ Thêm phòng học</button>
        </div>
    </form>
</div>

<!-- Bảng danh sách -->
<div class="panel">
    <div class="panel-heading">
        <div><h2>Danh sách phòng học (<?= count($rooms) ?>)</h2></div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Mã phòng</th>
                    <th>Sức chứa</th>
                    <th style="text-align: center;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$rooms): ?>
                <tr><td colspan="4" class="empty-cell">Chưa có dữ liệu phòng học.</td></tr>
            <?php else: ?>
                <?php foreach ($rooms as $index => $row): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><span class="badge"><?= e($row['code'] ?? 'P' . $row['id']) ?></span></td>
                        <td><?= e($row['capacity'] ?? 50) ?> chỗ ngồi</td>
                        <td style="text-align: center;">
                            <a href="<?= BASE_URL ?>/rooms.php?action=delete&id=<?= $row['id'] ?>" 
                               onclick="return confirm('Bạn có chắc muốn xóa phòng học này?');"
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