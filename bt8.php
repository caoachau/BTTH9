<?php
require_once __DIR__ . '/includes/numbers.php';

// Bài 8: nhận một số nguyên 1–10 rồi in bảng cửu chương tương ứng.
$selectedTable = null;
$error = null;
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    verify_post();
    try {
        $selectedTable = read_integer($_POST, 'n', 'Số cần xem', 1, 10);
    } catch (InvalidArgumentException $exception) {
        $error = $exception->getMessage();
    }
}

$title = 'Bài 8 — Bảng cửu chương tùy chọn';
require __DIR__ . '/header.php';
?>
<h1><?= e($title) ?></h1>
<?php if ($error !== null): ?><p class="notice error" role="alert"><?= e($error) ?></p><?php endif; ?>
<form method="post">
    <?php csrf_field(); ?>
    <label for="n">Nhập số nguyên dương từ 1 đến 10</label>
    <input id="n" type="number" step="1" name="n" value="<?= e(old_input('n')) ?>" required>
    <div class="actions"><button type="submit">Xem kết quả</button><a href="bt8.php">Làm lại</a></div>
</form>
<?php if ($selectedTable !== null): ?>
    <table>
        <caption>Bảng cửu chương <?= $selectedTable ?></caption>
        <tbody>
        <?php for ($i = 1; $i <= 10; $i++): ?>
            <tr><td><?= $selectedTable ?> × <?= $i ?></td><td>= <?= $selectedTable * $i ?></td></tr>
        <?php endfor; ?>
        </tbody>
    </table>
<?php endif; ?>
<?php require __DIR__ . '/footer.php'; ?>
