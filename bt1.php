<?php
require_once __DIR__ . '/includes/numbers.php';

// Bài 1: nhận hai số, chọn phép tính rồi trả kết quả ngay trên trang.
$result = null;
$error = null;
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    verify_post();
    try {
        $a = read_number($_POST, 'a', 'Số a');
        $b = read_number($_POST, 'b', 'Số b');
        $operator = input_text($_POST, 'pheptinh');

        if (($operator === '/' || $operator === '%') && $b == 0) {
            throw new InvalidArgumentException('Không thể chia cho 0.');
        }
        if ($operator === '%' && (floor($a) !== $a || floor($b) !== $b)) {
            throw new InvalidArgumentException('Phép chia lấy dư yêu cầu hai số nguyên.');
        }

        switch ($operator) {
            case '+': $answer = $a + $b; break;
            case '-': $answer = $a - $b; break;
            case '*': $answer = $a * $b; break;
            case '/': $answer = $a / $b; break;
            case '%': $answer = (int) $a % (int) $b; break;
            default: throw new InvalidArgumentException('Phép toán không hợp lệ.');
        }
        $result = number_text($a) . ' ' . $operator . ' ' . number_text($b) . ' = ' . number_text($answer);
    } catch (InvalidArgumentException $exception) {
        $error = $exception->getMessage();
    } catch (ArithmeticError) {
        $error = 'Không thể tính với độ chính xác hiện tại. Hãy dùng hệ số có độ lớn gần nhau hơn.';
    }
}

$title = 'Bài 1 — Chương trình máy tính';
require __DIR__ . '/header.php';
?>
<h1><?= e($title) ?></h1>
<?php if ($error !== null): ?><p class="notice error" role="alert"><?= e($error) ?></p><?php endif; ?>
<form method="post">
    <?php csrf_field(); ?>
    <label for="a">Nhập a</label>
    <input id="a" type="number" step="any" name="a" value="<?= e(old_input('a')) ?>" required>
    <label for="b">Nhập b</label>
    <input id="b" type="number" step="any" name="b" value="<?= e(old_input('b')) ?>" required>
    <div class="actions">
        <button type="submit" name="pheptinh" value="+">+</button>
        <button type="submit" name="pheptinh" value="-">−</button>
        <button type="submit" name="pheptinh" value="*">×</button>
        <button type="submit" name="pheptinh" value="/">÷</button>
        <button type="submit" name="pheptinh" value="%">%</button>
        <a href="bt1.php">Làm lại</a>
    </div>
</form>
<?php if ($result !== null): ?><div class="result" role="status"><?= e($result) ?></div><?php endif; ?>
<?php require __DIR__ . '/footer.php'; ?>
