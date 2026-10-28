<?php
require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Quản lý Khung giờ học';

// 1. XỬ LÝ XÓA KHUNG GIỜ
if (isset($_GET['action']) && $_GET['action'] === 'delete' && !empty($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($id > 0) {
        $stmt = $pdo->prepare("DELETE FROM time_slots WHERE id = ?");
        $stmt->execute([$id]);
        flash('Đã xóa khung giờ học thành công!', 'success');
        redirect(BASE_URL . '/time_slots.php');
    }
}

// 2. XỬ LÝ THÊM MỚI KHUNG GIỜ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_slot'])) {
    $day_of_week = (int)($_POST['day_of_week'] ?? 1);
    $slot_no     = (int)($_POST['slot_no'] ?? 1);
    $start_time  = trim($_POST['start_time'] ?? '');
    $end_time    = trim($_POST['end_time'] ?? '');

    if ($start_time !== '' && $end_time !== '') {
        $stmt = $pdo->prepare("INSERT INTO time_slots (day_of_week, slot_no, start_time, end_time) VALUES (?, ?, ?, ?)");
        $stmt->execute([$day_of_week, $slot_no, $start_time, $end_time]);
        
        flash('Thêm khung giờ học mới thành công!', 'success');
        redirect(BASE_URL . '/time_slots.php');
    } else {
        flash('Vui lòng nhập đầy đủ thời gian bắt đầu và kết thúc!', 'danger');
    }
}

// 3. LẤY DANH SÁCH KHUNG GIỜ
$slots = $pdo->query("SELECT * FROM time_slots ORDER BY day_of_week ASC, slot_no ASC")->fetchAll();

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
        <h1>Quản lý Khung giờ học</h1>
        <p>Thiết lập thứ trong tuần, số ca/tiết học, giờ bắt đầu và kết thúc.</p>
    </div>
</div>

<!-- FORM THÊM KHUNG GIỜ MỚI -->
<div class="panel" style="margin-bottom: 24px;">
    <div class="panel-heading">
        <div>
            <h2>+ Thêm khung giờ học mới</h2>
            <p>Chọn ngày, ca học và nhập khoảng thời gian (VD: 07:30).</p>
        </div>
    </div>

    <form method="POST" action="" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-top: 10px;">
        <input type="hidden" name="add_slot" value="1">
        
        <div>
            <label style="display:block; margin-bottom: 6px; font-weight: 500; font-size: 14px;">Thứ trong tuần:</label>
            <select name="day_of_week" required style="width:100%; padding: 10px; border-radius: 6px; background: #111827; color: #fff; border: 1px solid #374151;">
                <?php foreach ($days as $key => $name): ?>
                    <option value="<?= $key ?>"><?= $name ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label style="display:block; margin-bottom: 6px; font-weight: 500; font-size: 14px;">Ca / Tiết số:</label>
            <input type="number" name="slot_no" value="1" min="1" max="10" required style="width:100%; padding: 10px; border-radius: 6px; background: #111827; color: #fff; border: 1px solid #374151;">
        </div>

        <div>
            <label style="display:block; margin-bottom: 6px; font-weight: 500; font-size: 14px;">Giờ bắt đầu:</label>
            <input type="text" name="start_time" placeholder="VD: 07:30" required style="width:100%; padding: 10px; border-radius: 6px; background: #111827; color: #fff; border: 1px solid #374151;">
        </div>

        <div>
            <label style="display:block; margin-bottom: 6px; font-weight: 500; font-size: 14px;">Giờ kết thúc:</label>
            <input type="text" name="end_time" placeholder="VD: 11:30" required style="width:100%; padding: 10px; border-radius: 6px; background: #111827; color: #fff; border: 1px solid #374151;">
        </div>

        <div style="display: flex; align-items: flex-end;">
            <button type="submit" class="button" style="width: 100%; padding: 11px; cursor: pointer;">
                + Thêm khung giờ
            </button>
        </div>
    </form>
</div>

<!-- BẢNG DANH SÁCH KHUNG GIỜ -->
<div class="panel">
    <div class="panel-heading">
        <div>
            <h2>Danh sách khung giờ học</h2>
            <p>Tổng số: <strong><?= count($slots) ?></strong> khung giờ</p>
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Thứ</th>
                    <th>Ca / Tiết</th>
                    <th>Giờ bắt đầu</th>
                    <th>Giờ kết thúc</th>
                    <th style="text-align: center;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$slots): ?>
                <tr>
                    <td colspan="6" class="empty-cell">
                        Chưa có khung giờ học nào được tạo. Hãy thêm mới ở form phía trên!
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($slots as $index => $row): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><strong><?= e($days[(int)$row['day_of_week']] ?? 'Thứ ' . $row['day_of_week']) ?></strong></td>
                        <td><span class="badge">Ca <?= e($row['slot_no']) ?></span></td>
                        <td><?= e(substr($row['start_time'], 0, 5)) ?></td>
                        <td><?= e(substr($row['end_time'], 0, 5)) ?></td>
                        <td style="text-align: center;">
                            <a href="<?= BASE_URL ?>/time_slots.php?action=delete&id=<?= $row['id'] ?>" 
                               onclick="return confirm('Bạn có chắc chắn muốn xóa khung giờ này?');"
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