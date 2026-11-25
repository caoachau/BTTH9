<?php
require_once __DIR__ . '/includes/numbers.php';

// Bài 6: tính số ngày của tháng, có xét năm nhuận.
$result = null;
$error = null;
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    verify_post();
    try {
        $month = read_integer($_POST, 'thang', 'Tháng', -1000000, 1000000);
        $year = read_integer($_POST, 'nam', 'Năm', 1, 9999);
        if ($month < 1 || $month > 12) {
            throw new InvalidArgumentException('Không có tháng này hoặc năm không hợp lệ.');
        }
        if ($month === 2) {
            $leapYear = $year % 400 === 0 || ($year % 4 === 0 && $year % 100 !== 0);
            $days = $leapYear ? 29 : 28;
        } elseif (in_array($month, [4, 6, 9, 11], true)) {
            $days = 30;
        } else {
            $days = 31;
        }
        $result = "Tháng $month năm $year có $days ngày.";
    } catch (InvalidArgumentException $exception) {
        $error = $exception->getMessage();
    }
}

$title = 'Bài 6 — Số ngày trong tháng';
require __DIR__ . '/header.php';
?>
<h1><?= e($title) ?></h1>
<?php if ($error !== null): ?><p class="notice error" role="alert"><?= e($error) ?></p><?php endif; ?>
<form method="post">
    <?php csrf_field(); ?>
    <label for="thang">Tháng (1–12)</label>
    <input id="thang" type="number" step="1" name="thang" value="<?= e(old_input('thang')) ?>" required>
    <label for="nam">Năm</label>
    <input id="nam" type="number" step="1" name="nam" value="<?= e(old_input('nam', date('Y'))) ?>" required>
    <p class="hint">Nhập năm để xác định đúng số ngày của tháng 2 trong năm nhuận.</p>
    <div class="actions"><button type="submit">Xem kết quả</button><a href="bt6.php">Làm lại</a></div>
</form>
<?php if ($result !== null): ?><div class="result" role="status"><?= e($result) ?></div><?php endif; ?>
<?php require __DIR__ . '/footer.php'; ?>
