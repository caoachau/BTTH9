<?php
require_once __DIR__ . '/includes/bootstrap.php';
$title = 'Đăng nhập quản trị';
require __DIR__ . '/header.php';
?>
<h1><?= e($title) ?></h1>
<form action="xuly_dangnhapQT.php" method="post">
    <?php csrf_field(); ?>
    <label for="login-username">Tên đăng nhập</label>
    <input id="login-username" name="tendn" maxlength="20" autocomplete="username" required>
    <label for="login-password">Mật khẩu</label>
    <input id="login-password" name="matkhau" type="password" autocomplete="current-password" required>
    <div class="actions">
        <button type="submit">Đăng nhập</button>
        <button type="reset" class="secondary">Làm lại</button>
    </div>
</form>
<?php require __DIR__ . '/footer.php'; ?>
