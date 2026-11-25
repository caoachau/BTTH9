<?php
require_once __DIR__ . '/includes/users.php';
$values = $_SESSION['old_registration'] ?? [];
unset($_SESSION['old_registration']);
$title = 'Đăng ký thành viên';
require __DIR__ . '/header.php';
?>
<h1>Đăng ký thành viên</h1>
<form action="xlydangky.php" method="post" enctype="multipart/form-data">
    <?php csrf_field(); ?>
    <label for="username">Tên đăng nhập</label>
    <input id="username" name="tendn" value="<?= e($values['tendn'] ?? '') ?>" minlength="3" maxlength="20" pattern="[a-zA-Z0-9_]{3,20}" autocomplete="username" required>
    <p class="hint">3–20 chữ cái không dấu, chữ số hoặc dấu gạch dưới.</p>
    <label for="password">Mật khẩu</label>
    <input id="password" name="matkhau" type="password" minlength="6" maxlength="72" autocomplete="new-password" required>
    <label for="fullname">Họ và tên</label>
    <input id="fullname" name="hoten" value="<?= e($values['hoten'] ?? '') ?>" maxlength="50" autocomplete="name" required>
    <fieldset>
        <legend>Giới tính</legend>
        <label class="inline"><input type="radio" name="gioitinh" value="0" <?= (string) ($values['gioitinh'] ?? '0') === '0' ? 'checked' : '' ?> required>Nam</label>
        <label class="inline"><input type="radio" name="gioitinh" value="1" <?= (string) ($values['gioitinh'] ?? '0') === '1' ? 'checked' : '' ?> required>Nữ</label>
    </fieldset>
    <label for="country">Quốc gia</label>
    <select id="country" name="quocgia">
        <?php foreach (countries() as $code => $label): ?>
            <option value="<?= e($code) ?>" <?= ($values['quocgia'] ?? 'vn') === $code ? 'selected' : '' ?>><?= e($label) ?></option>
        <?php endforeach; ?>
    </select>
    <label for="avatar">Ảnh đại diện (không bắt buộc)</label>
    <input type="hidden" name="MAX_FILE_SIZE" value="1000000">
    <input id="avatar" name="hinhanh" type="file" accept="image/jpeg,image/png,image/gif,image/webp">
    <p class="hint">JPG, PNG, GIF hoặc WebP, tối đa 1.000.000 byte và 4096 × 4096 pixel.</p>
    <div class="actions"><button type="submit">Đăng ký</button><button type="reset" class="secondary">Làm lại</button></div>
</form>
<p>Đã có tài khoản? <a href="dangnhap.php">Đăng nhập</a></p>
<?php require __DIR__ . '/footer.php'; ?>
