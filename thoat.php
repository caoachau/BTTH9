<?php
require_once __DIR__ . '/includes/bootstrap.php';

// POST từ nút Đăng xuất sẽ kết thúc phiên thành viên.
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    verify_post();
    unset($_SESSION['nguoidung'], $_SESSION['csrf']);
    session_regenerate_id(true);
    flash('Bạn đã đăng xuất.');
    redirect('dangnhap.php');
}

// Khi truy cập bằng liên kết GET, hiển thị nút xác nhận trước khi đăng xuất.
$title = 'Đăng xuất thành viên';
require __DIR__ . '/header.php';
?>
<h1><?= e($title) ?></h1>
<p>Xác nhận kết thúc phiên đăng nhập hiện tại.</p>
<form method="post">
    <?php csrf_field(); ?>
    <button type="submit">Đăng xuất</button>
</form>
<?php require __DIR__ . '/footer.php'; ?>
