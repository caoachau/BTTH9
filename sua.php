<?php
require_once __DIR__ . '/includes/users.php';
require_admin();
try {
    $username = input_text($_GET, 'user');
} catch (InvalidArgumentException $exception) {
    abort_page(400, $exception->getMessage());
}
$user = find_user($username);
if (!$user) {
    abort_page(404, 'Không tìm thấy người dùng cần sửa.');
}
$values = ['tendn' => $user['username'], 'hoten' => $user['hoten'], 'gioitinh' => $user['gioitinh'], 'quocgia' => $user['quocgia']];
$old = $_SESSION['old_edit'] ?? [];
unset($_SESSION['old_edit']);
if (($old['tendn'] ?? null) === $user['username']) {
    $values = array_replace($values, $old);
}
$title = 'Sửa thông tin người dùng';
require __DIR__ . '/header.php';
?>
<h1>Sửa thông tin người dùng</h1>
<form action="thuchien_sua.php" method="post" enctype="multipart/form-data">
    <?php csrf_field(); ?>
    <label for="username">Tên đăng nhập</label>
    <input id="username" name="tendn" value="<?= e($values['tendn']) ?>" maxlength="20" autocomplete="off" readonly required>
    <label for="password">Mật khẩu mới (để trống để giữ nguyên)</label>
    <input id="password" name="matkhau" type="password" minlength="6" maxlength="72" autocomplete="new-password">
    <label for="fullname">Họ và tên</label>
    <input id="fullname" name="hoten" value="<?= e($values['hoten']) ?>" maxlength="50" autocomplete="name" required>
    <fieldset>
        <legend>Giới tính</legend>
        <label class="inline"><input type="radio" name="gioitinh" value="0" <?= (string) $values['gioitinh'] === '0' ? 'checked' : '' ?> required>Nam</label>
        <label class="inline"><input type="radio" name="gioitinh" value="1" <?= (string) $values['gioitinh'] === '1' ? 'checked' : '' ?> required>Nữ</label>
    </fieldset>
    <label for="country">Quốc gia</label>
    <select id="country" name="quocgia">
        <?php foreach (countries() as $code => $label): ?>
            <option value="<?= e($code) ?>" <?= $values['quocgia'] === $code ? 'selected' : '' ?>><?= e($label) ?></option>
        <?php endforeach; ?>
    </select>
    <p>Ảnh hiện tại</p>
    <img class="avatar" src="avatar.php?user=<?= rawurlencode($values['tendn']) ?>" alt="Ảnh đại diện hiện tại">
    <label for="avatar">Ảnh mới (không chọn để giữ ảnh hiện tại)</label>
    <input type="hidden" name="MAX_FILE_SIZE" value="1000000">
    <input id="avatar" name="hinh" type="file" accept="image/jpeg,image/png,image/gif,image/webp">
    <p class="hint">JPG, PNG, GIF hoặc WebP, tối đa 1.000.000 byte và 4096 × 4096 pixel.</p>
    <div class="actions"><button type="submit">Lưu thay đổi</button><a href="quantri.php">Hủy bỏ</a></div>
</form>
<?php require __DIR__ . '/footer.php'; ?>
