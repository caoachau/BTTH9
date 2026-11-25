<?php
require_once __DIR__ . '/includes/numbers.php';

// Bài 9: số nguyên tố là số >= 2 chỉ chia hết cho 1 và chính nó.
$result = null;
$error = null;
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    verify_post();
    try {
        $n = read_integer($_POST, 'n', 'Số cần kiểm tra', -1000000000000, 1000000000000);
        if ($n < 2) {
            $prime = false;
        } else {
            $prime = true;
            // Thử các ước từ 2 đến căn bậc hai của n (tức là i × i <= n).
            for ($i = 2; $i * $i <= $n; $i++) {
                if ($n % $i === 0) {
                    $prime = false;
                    break;
                }
            }
        }
        if ($prime) {
            $result = "$n là số nguyên tố.";
        } else {
            $result = "$n không phải là số nguyên tố.";
        }
    } catch (InvalidArgumentException $exception) {
        $error = $exception->getMessage();
    }
}

$title = 'Bài 9 — Kiểm tra số nguyên tố';
require __DIR__ . '/header.php';
?>
<h1><?= e($title) ?></h1>
<?php if ($error !== null): ?><p class="notice error" role="alert"><?= e($error) ?></p><?php endif; ?>
<form method="post">
    <?php csrf_field(); ?>
    <label for="n">Nhập một số nguyên</label>
    <input id="n" type="number" step="1" name="n" value="<?= e(old_input('n')) ?>" required>
    <p class="hint">Hỗ trợ số nguyên có giá trị tuyệt đối không quá 10¹².</p>
    <div class="actions"><button type="submit">Xem kết quả</button><a href="bt9.php">Làm lại</a></div>
</form>
<?php if ($result !== null): ?><div class="result" role="status"><?= e($result) ?></div><?php endif; ?>
<?php require __DIR__ . '/footer.php'; ?>
