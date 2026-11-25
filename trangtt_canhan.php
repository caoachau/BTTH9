<?php
require_once __DIR__ . '/includes/users.php';
$user = require_member();
$title = 'Thông tin cá nhân';
require __DIR__ . '/header.php';
?>
<h1>Thông tin của bạn</h1>
<img class="avatar" src="avatar.php?user=<?= rawurlencode($user['username']) ?>" alt="Ảnh đại diện của <?= e($user['hoten']) ?>">
<dl>
    <dt>Tên đăng nhập</dt><dd><?= e($user['username']) ?></dd>
    <dt>Họ và tên</dt><dd><?= e($user['hoten']) ?></dd>
    <dt>Giới tính</dt><dd><?= (int) $user['gioitinh'] === 0 ? 'Nam' : 'Nữ' ?></dd>
    <dt>Quốc gia</dt><dd><?= e(countries()[$user['quocgia']] ?? $user['quocgia']) ?></dd>
</dl>
<form action="thoat.php" method="post"><?php csrf_field(); ?><button type="submit">Đăng xuất</button></form>
<?php require __DIR__ . '/footer.php'; ?>
