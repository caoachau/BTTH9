<?php
require_once __DIR__ . '/includes/bootstrap.php';

// Kết thúc phiên quản trị khi nhận biểu mẫu POST hợp lệ.
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    verify_post();
    unset($_SESSION['nguoiquantri'], $_SESSION['csrf']);
    session_regenerate_id(true);
    flash('Bạn đã thoát tài khoản quản trị.');
    redirect('dangnhapQT.php');
}

$title = 'Đăng xuất quản trị';
require __DIR__ . '/header.php';
?>
<h1><?= e($title) ?></h1>
<p>Xác nhận kết thúc phiên đăng nhập hiện tại.</p>
<form method="post">
    <?php csrf_field(); ?>
    <button type="submit">Đăng xuất</button>
</form>
<?php require __DIR__ . '/footer.php'; ?>
