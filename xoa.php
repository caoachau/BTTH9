<?php
require_once __DIR__ . '/includes/users.php';
require_admin();
$isPost = ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
if ($isPost) {
    verify_post();
}
try {
    $username = input_text($isPost ? $_POST : $_GET, 'user');
} catch (InvalidArgumentException $exception) {
    abort_page(400, $exception->getMessage());
}
$user = find_user($username);
if (!$user) {
    abort_page(404, 'Không tìm thấy người dùng cần xóa.');
}
if ($isPost) {
    $statement = mysqli_prepare($kn, 'DELETE FROM nguoi_dung WHERE username = ?');
    mysqli_stmt_bind_param($statement, 's', $user['username']);
    mysqli_stmt_execute($statement);
    remove_avatar($user['hinhdaidien']);
    if (($_SESSION['nguoidung'] ?? null) === $user['username']) {
        unset($_SESSION['nguoidung']);
    }
    flash('Đã xóa người dùng ' . $user['username'] . '.');
    redirect('quantri.php');
}
$title = 'Xác nhận xóa người dùng';
require __DIR__ . '/header.php';
?>
<h1>Xác nhận xóa người dùng</h1>
<p>Bạn đang xóa tài khoản <strong><?= e($user['username']) ?></strong> của <?= e($user['hoten']) ?>. Ảnh đại diện cũng sẽ được xóa.</p>
<form method="post">
    <?php csrf_field(); ?>
    <input type="hidden" name="user" value="<?= e($user['username']) ?>">
    <div class="actions"><button type="submit" class="danger">Xóa người dùng</button><a href="quantri.php">Hủy bỏ</a></div>
</form>
<?php require __DIR__ . '/footer.php'; ?>
