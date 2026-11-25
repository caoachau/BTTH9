<?php
require_once __DIR__ . '/includes/numbers.php';

// Bài 5: kiểm tra điều kiện ba cạnh và phân loại tam giác.
$result = null;
$error = null;
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    verify_post();
    try {
        $a = read_number($_POST, 'a', 'Cạnh a');
        $b = read_number($_POST, 'b', 'Cạnh b');
        $c = read_number($_POST, 'c', 'Cạnh c');
        // Ba cạnh phải dương và tổng hai cạnh bất kỳ phải lớn hơn cạnh còn lại.
        if ($a <= 0 || $b <= 0 || $c <= 0 || $a + $b <= $c || $a + $c <= $b || $b + $c <= $a) {
            $result = 'Ba cạnh không tạo thành một tam giác.';
        } else {
            // abs() lấy trị tuyệt đối. Sai số nhỏ giúp so sánh các số thập phân.
            $saiSo = 0.0000000001 * max($a, $b, $c);
            $abBangNhau = abs($a - $b) <= $saiSo;
            $acBangNhau = abs($a - $c) <= $saiSo;
            $bcBangNhau = abs($b - $c) <= $saiSo;
            $tamGiacCan = $abBangNhau || $acBangNhau || $bcBangNhau;

            // Định lý Pythagore: bình phương cạnh huyền bằng tổng hai bình phương còn lại.
            $aBinhPhuong = $a * $a;
            $bBinhPhuong = $b * $b;
            $cBinhPhuong = $c * $c;
            $saiSoBinhPhuong = 0.0000000001 * max($aBinhPhuong, $bBinhPhuong, $cBinhPhuong);
            $tamGiacVuong = abs($aBinhPhuong + $bBinhPhuong - $cBinhPhuong) <= $saiSoBinhPhuong
                || abs($aBinhPhuong + $cBinhPhuong - $bBinhPhuong) <= $saiSoBinhPhuong
                || abs($bBinhPhuong + $cBinhPhuong - $aBinhPhuong) <= $saiSoBinhPhuong;

            if ($abBangNhau && $acBangNhau && $bcBangNhau) {
                $result = 'Đây là tam giác đều.';
            } elseif ($tamGiacVuong && $tamGiacCan) {
                $result = 'Đây là tam giác vuông cân.';
            } elseif ($tamGiacVuong) {
                $result = 'Đây là tam giác vuông.';
            } elseif ($tamGiacCan) {
                $result = 'Đây là tam giác cân.';
            } else {
                $result = 'Đây là tam giác thường.';
            }
        }
    } catch (InvalidArgumentException $exception) {
        $error = $exception->getMessage();
    }
}

$title = 'Bài 5 — Phân loại tam giác';
require __DIR__ . '/header.php';
?>
<h1><?= e($title) ?></h1>
<?php if ($error !== null): ?><p class="notice error" role="alert"><?= e($error) ?></p><?php endif; ?>
<form method="post">
    <?php csrf_field(); ?>
    <label for="a">Cạnh a</label>
    <input id="a" type="number" step="any" name="a" value="<?= e(old_input('a')) ?>" required>
    <label for="b">Cạnh b</label>
    <input id="b" type="number" step="any" name="b" value="<?= e(old_input('b')) ?>" required>
    <label for="c">Cạnh c</label>
    <input id="c" type="number" step="any" name="c" value="<?= e(old_input('c')) ?>" required>
    <div class="actions"><button type="submit">Xem kết quả</button><a href="bt5.php">Làm lại</a></div>
</form>
<?php if ($result !== null): ?><div class="result" role="status"><?= e($result) ?></div><?php endif; ?>
<?php require __DIR__ . '/footer.php'; ?>
