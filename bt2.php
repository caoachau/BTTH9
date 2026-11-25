<?php
require_once __DIR__ . '/includes/numbers.php';

// Bài 2: giải phương trình ax + b = 0.
$result = null;
$error = null;
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    verify_post();
    try {
        $a = read_number($_POST, 'a', 'Hệ số a');
        $b = read_number($_POST, 'b', 'Hệ số b');
        if ($a == 0) {
            if ($b == 0) {
                $result = 'Phương trình có vô số nghiệm.';
            } else {
                $result = 'Phương trình vô nghiệm.';
            }
        } else {
            $x = -$b / $a;
            $result = 'Phương trình có nghiệm x = ' . number_text($x) . '.';
        }
    } catch (InvalidArgumentException $exception) {
        $error = $exception->getMessage();
    } catch (ArithmeticError) {
        $error = 'Không thể tính với độ chính xác hiện tại. Hãy dùng hệ số có độ lớn gần nhau hơn.';
    }
}

$title = 'Bài 2 — Giải phương trình bậc I';
require __DIR__ . '/header.php';
?>
<h1><?= e($title) ?></h1>
<p>ax + b = 0</p>
<?php if ($error !== null): ?><p class="notice error" role="alert"><?= e($error) ?></p><?php endif; ?>
<form method="post">
    <?php csrf_field(); ?>
    <label for="a">Nhập a</label>
    <input id="a" type="number" step="any" name="a" value="<?= e(old_input('a')) ?>" required>
    <label for="b">Nhập b</label>
    <input id="b" type="number" step="any" name="b" value="<?= e(old_input('b')) ?>" required>
    <div class="actions"><button type="submit">Giải</button><a href="bt2.php">Làm lại</a></div>
</form>
<?php if ($result !== null): ?><div class="result" role="status"><?= e($result) ?></div><?php endif; ?>
<?php require __DIR__ . '/footer.php'; ?>
