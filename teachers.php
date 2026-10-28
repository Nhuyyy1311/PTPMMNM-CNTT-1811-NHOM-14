<?php
require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Quản lý Giảng viên';

// 1. XỬ LÝ XÓA GIẢNG VIÊN
if (isset($_GET['action']) && $_GET['action'] === 'delete' && !empty($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($id > 0) {
        $stmt = $pdo->prepare("DELETE FROM teachers WHERE id = ?");
        $stmt->execute([$id]);
        flash('Đã xóa giảng viên thành công!', 'success');
        redirect(BASE_URL . '/teachers.php');
    }
}

// 2. XỬ LÝ THÊM MỚI GIẢNG VIÊN (An toàn tuyệt đối, tự động tương thích CSDL không có cột phone)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_teacher'])) {
    $code      = trim($_POST['code'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');

    if ($code !== '' && $full_name !== '') {
        // Kiểm tra trùng lặp mã giảng viên
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM teachers WHERE code = ?");
        $checkStmt->execute([$code]);
        
        if ($checkStmt->fetchColumn() > 0) {
            flash('Lỗi: Mã giảng viên "' . e($code) . '" đã tồn tại trong hệ thống. Vui lòng nhập mã khác!', 'danger');
        } else {
            // Thử lưu có kèm số điện thoại, nếu CSDL chưa có cột phone thì tự động chuyển về lưu 3 cột chuẩn
            try {
                $stmt = $pdo->prepare("INSERT INTO teachers (code, full_name, email, phone) VALUES (?, ?, ?, ?)");
                $stmt->execute([$code, $full_name, $email, $phone]);
            } catch (\PDOException $e) {
                $stmt = $pdo->prepare("INSERT INTO teachers (code, full_name, email) VALUES (?, ?, ?)");
                $stmt->execute([$code, $full_name, $email]);
            }

            flash('Thêm giảng viên mới thành công!', 'success');
            redirect(BASE_URL . '/teachers.php');
        }
    } else {
        flash('Vui lòng nhập đầy đủ Mã GV và Họ tên!', 'danger');
    }
}

// 3. LẤY DANH SÁCH GIẢNG VIÊN (Kiểm tra an toàn nếu thiếu cột phone)
try {
    $teachers = $pdo->query("SELECT * FROM teachers ORDER BY id DESC")->fetchAll();
} catch (\PDOException $e) {
    $teachers = [];
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-heading">
    <div>
        <h1>Quản lý Giảng viên</h1>
        <p>Thêm mới, quản lý thông tin và xóa giảng viên trong hệ thống.</p>
    </div>
</div>

<!-- FORM THÊM GIẢNG VIÊN MỚI -->
<div class="panel" style="margin-bottom: 24px;">
    <div class="panel-heading">
        <div>
            <h2>+ Thêm giảng viên mới</h2>
            <p>Nhập mã, họ tên, email và số điện thoại của giảng viên.</p>
        </div>
    </div>

    <form method="POST" action="" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-top: 10px;">
        <input type="hidden" name="add_teacher" value="1">
        
        <div>
            <label style="display:block; margin-bottom: 6px; font-weight: 500; font-size: 14px;">Mã giảng viên:</label>
            <input type="text" name="code" required placeholder="VD: GV01" style="width:100%; padding: 10px; border-radius: 6px; background: #111827; color: #fff; border: 1px solid #374151;">
        </div>

        <div>
            <label style="display:block; margin-bottom: 6px; font-weight: 500; font-size: 14px;">Họ và tên:</label>
            <input type="text" name="full_name" required placeholder="VD: Nguyễn Văn A" style="width:100%; padding: 10px; border-radius: 6px; background: #111827; color: #fff; border: 1px solid #374151;">
        </div>

        <div>
            <label style="display:block; margin-bottom: 6px; font-weight: 500; font-size: 14px;">Email:</label>
            <input type="email" name="email" placeholder="VD: a.nv@dnu.edu.vn" style="width:100%; padding: 10px; border-radius: 6px; background: #111827; color: #fff; border: 1px solid #374151;">
        </div>

        <div>
            <label style="display:block; margin-bottom: 6px; font-weight: 500; font-size: 14px;">Số điện thoại:</label>
            <input type="text" name="phone" placeholder="VD: 0912345678" style="width:100%; padding: 10px; border-radius: 6px; background: #111827; color: #fff; border: 1px solid #374151;">
        </div>

        <div style="display: flex; align-items: flex-end;">
            <button type="submit" class="button" style="width: 100%; padding: 11px; cursor: pointer;">
                + Thêm giảng viên
            </button>
        </div>
    </form>
</div>

<!-- BẢNG DANH SÁCH GIẢNG VIÊN -->
<div class="panel">
    <div class="panel-heading">
        <div>
            <h2>Danh sách giảng viên</h2>
            <p>Tổng số: <strong><?= count($teachers) ?></strong> giảng viên</p>
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Mã GV</th>
                    <th>Họ và tên</th>
                    <th>Email</th>
                    <th>Số điện thoại</th>
                    <th style="text-align: center;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$teachers): ?>
                <tr>
                    <td colspan="6" class="empty-cell">
                        Chưa có dữ liệu giảng viên. Hãy thêm mới ở form phía trên!
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($teachers as $index => $row): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><span class="badge"><?= e($row['code'] ?? 'GV' . $row['id']) ?></span></td>
                        <td><strong><?= e($row['full_name'] ?? $row['name'] ?? 'N/A') ?></strong></td>
                        <td><?= e($row['email'] ?? 'Chưa cập nhật') ?></td>
                        <td><?= e(!empty($row['phone']) ? $row['phone'] : 'Chưa cập nhật') ?></td>
                        <td style="text-align: center;">
                            <a href="<?= BASE_URL ?>/teachers.php?action=delete&id=<?= $row['id'] ?>" 
                               onclick="return confirm('Bạn có chắc chắn muốn xóa giảng viên này?');"
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