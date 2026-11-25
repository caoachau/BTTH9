<?php
require_once __DIR__ . '/includes/bootstrap.php';

// Bài 10: date() dùng múi giờ Việt Nam đã được đặt trong bootstrap.php.
// l: tên thứ, jS: ngày kèm hậu tố (1st, 2nd...), F: tên tháng, Y: năm.
$today = date('l jS \o\f F Y');
$title = 'Bài 10 — Ngày tháng hiện tại';
require __DIR__ . '/header.php';
?>
<h1><?= e($title) ?></h1>
<p>Hôm nay theo giờ Việt Nam:</p>
<div class="result"><time datetime="<?= date('Y-m-d') ?>" lang="en"><?= e($today) ?></time></div>
<?php require __DIR__ . '/footer.php'; ?>
