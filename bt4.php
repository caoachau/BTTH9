<?php
require_once __DIR__ . '/includes/numbers.php';

// BƯỚC 1: ban đầu chưa có kết quả và chưa có lỗi.
$ketQua = '';
$loi = '';

// BƯỚC 2: chỉ tính khi người dùng bấm nút gửi form bằng POST.
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    verify_post(); // Kiểm tra form; hàm hỗ trợ nằm trong includes/bootstrap.php.
    $hinh = $_POST['hinh'] ?? '';

    // BƯỚC 3: chọn công thức theo giá trị của radio name="hinh".
    if ($hinh === 'tron') {
        $banKinh = $_POST['r'] ?? '';

        // is_numeric() kiểm tra người dùng đã nhập một số hay chưa.
        if (!is_numeric($banKinh)) {
            $loi = 'Bán kính phải là một số hợp lệ.';
        } elseif ($banKinh <= 0) {
            $loi = 'Bán kính phải lớn hơn 0.';
        } elseif ($banKinh > 1000000000000) {
            $loi = 'Bán kính không được vượt quá 10¹².';
        } else {
            $banKinh = (float) $banKinh;
            $dienTich = pi() * $banKinh * $banKinh; // S = π × r × r.
        }
    } elseif ($hinh === 'tamgiac') {
        $day = $_POST['day'] ?? '';
        $chieuCao = $_POST['chieucao'] ?? '';

        if (!is_numeric($day) || !is_numeric($chieuCao)) {
            $loi = 'Đáy và chiều cao phải là số hợp lệ.';
        } elseif ($day <= 0 || $chieuCao <= 0) {
            $loi = 'Đáy và chiều cao phải lớn hơn 0.';
        } elseif ($day > 1000000000000 || $chieuCao > 1000000000000) {
            $loi = 'Kích thước không được vượt quá 10¹².';
        } else {
            $day = (float) $day;
            $chieuCao = (float) $chieuCao;
            $dienTich = $day * $chieuCao / 2; // S = đáy × chiều cao / 2.
        }
    } elseif ($hinh === 'chunhat') {
        $chieuDai = $_POST['dai'] ?? '';
        $chieuRong = $_POST['rong'] ?? '';

        if (!is_numeric($chieuDai) || !is_numeric($chieuRong)) {
            $loi = 'Chiều dài và chiều rộng phải là số hợp lệ.';
        } elseif ($chieuDai <= 0 || $chieuRong <= 0) {
            $loi = 'Chiều dài và chiều rộng phải lớn hơn 0.';
        } elseif ($chieuDai > 1000000000000 || $chieuRong > 1000000000000) {
            $loi = 'Kích thước không được vượt quá 10¹².';
        } else {
            $chieuDai = (float) $chieuDai;
            $chieuRong = (float) $chieuRong;
            $dienTich = $chieuDai * $chieuRong; // S = dài × rộng.
        }
    } else {
        $loi = 'Hãy chọn một hình hợp lệ.';
    }

    // BƯỚC 4: không có lỗi thì tạo câu thông báo để in bên dưới form.
    if ($loi === '') {
        $ketQua = 'Diện tích = ' . number_text($dienTich) . ' (đơn vị vuông).';
    }
}

$title = 'Bài 4 — Tính diện tích';
require __DIR__ . '/header.php';
?>
<h1><?= e($title) ?></h1>
<?php if ($loi !== ''): ?><p class="notice error" role="alert"><?= e($loi) ?></p><?php endif; ?>
<form method="post">
    <?php csrf_field(); ?>
    <fieldset>
        <legend>Chọn hình cần tính</legend>
        <label class="inline"><input type="radio" name="hinh" value="tron" <?= old_input('hinh', 'tron') === 'tron' ? 'checked' : '' ?>>Hình tròn</label>
        <label class="inline"><input type="radio" name="hinh" value="tamgiac" <?= old_input('hinh', 'tron') === 'tamgiac' ? 'checked' : '' ?>>Tam giác</label>
        <label class="inline"><input type="radio" name="hinh" value="chunhat" <?= old_input('hinh', 'tron') === 'chunhat' ? 'checked' : '' ?>>Chữ nhật</label>
    </fieldset>
    <p class="hint">Chỉ cần điền kích thước của hình đã chọn. Dùng cùng một đơn vị đo.</p>
    <label for="r">Hình tròn — bán kính r</label>
    <input id="r" type="number" step="any" name="r" value="<?= e(old_input('r')) ?>">
    <label for="day">Tam giác — đáy</label>
    <input id="day" type="number" step="any" name="day" value="<?= e(old_input('day')) ?>">
    <label for="chieucao">Tam giác — chiều cao</label>
    <input id="chieucao" type="number" step="any" name="chieucao" value="<?= e(old_input('chieucao')) ?>">
    <label for="dai">Chữ nhật — chiều dài</label>
    <input id="dai" type="number" step="any" name="dai" value="<?= e(old_input('dai')) ?>">
    <label for="rong">Chữ nhật — chiều rộng</label>
    <input id="rong" type="number" step="any" name="rong" value="<?= e(old_input('rong')) ?>">
    <div class="actions"><button type="submit">Xem kết quả</button><a href="bt4.php">Làm lại</a></div>
</form>
<!-- BƯỚC 5: hiển thị kết quả đã tính ở phần PHP phía trên. -->
<?php if ($ketQua !== ''): ?><div class="result" role="status"><?= e($ketQua) ?></div><?php endif; ?>
<?php require __DIR__ . '/footer.php'; ?>
