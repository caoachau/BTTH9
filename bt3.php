<?php
require_once __DIR__ . '/includes/numbers.php';

// Bài 3: giải phương trình ax² + bx + c = 0.
$result = null;
$error = null;
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    verify_post();
    try {
        $a = read_number($_POST, 'a', 'Hệ số a');
        $b = read_number($_POST, 'b', 'Hệ số b');
        $c = read_number($_POST, 'c', 'Hệ số c');

        if ($a == 0) {
            // a = 0 thì phương trình trở thành bx + c = 0.
            $result = 'Hệ số a = 0, chuyển về phương trình bậc I. ';
            if ($b == 0) {
                $result .= $c == 0 ? 'Phương trình có vô số nghiệm.' : 'Phương trình vô nghiệm.';
            } else {
                $result .= 'Phương trình có nghiệm x = ' . number_text(-$c / $b) . '.';
            }
        } else {
            // Công thức đã học: delta = b² - 4ac.
            $delta = $b * $b - 4 * $a * $c;

            if ($delta < 0) {
                $result = 'Phương trình vô nghiệm trong tập số thực.';
            } elseif ($delta == 0) {
                $x = -$b / (2 * $a);
                $result = 'Phương trình có nghiệm kép x = ' . number_text($x) . '.';
            } else {
                // sqrt() là căn bậc hai. Tính hai nghiệm theo công thức thông thường.
                $x1 = (-$b + sqrt($delta)) / (2 * $a);
                $x2 = (-$b - sqrt($delta)) / (2 * $a);
                $result = 'Phương trình có hai nghiệm phân biệt: x₁ = ' . number_text($x1)
                    . '; x₂ = ' . number_text($x2) . '.';
            }
        }
    } catch (InvalidArgumentException $exception) {
        $error = $exception->getMessage();
    } catch (ArithmeticError) {
        $error = 'Không thể tính với độ chính xác hiện tại. Hãy dùng hệ số có độ lớn gần nhau hơn.';
    }
}

$title = 'Bài 3 — Giải phương trình bậc II';
require __DIR__ . '/header.php';
?>
<h1><?= e($title) ?></h1>
<p>ax² + bx + c = 0</p>
<?php if ($error !== null): ?><p class="notice error" role="alert"><?= e($error) ?></p><?php endif; ?>
<form method="post">
    <?php csrf_field(); ?>
    <label for="a">Nhập a</label>
    <input id="a" type="number" step="any" name="a" value="<?= e(old_input('a')) ?>" required>
    <label for="b">Nhập b</label>
    <input id="b" type="number" step="any" name="b" value="<?= e(old_input('b')) ?>" required>
    <label for="c">Nhập c</label>
    <input id="c" type="number" step="any" name="c" value="<?= e(old_input('c')) ?>" required>
    <div class="actions"><button type="submit">Giải</button><a href="bt3.php">Làm lại</a></div>
</form>
<?php if ($result !== null): ?><div class="result" role="status"><?= e($result) ?></div><?php endif; ?>
<?php require __DIR__ . '/footer.php'; ?>
