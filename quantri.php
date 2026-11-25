<?php
require_once __DIR__ . '/includes/users.php';
require_admin();
$ketQua = mysqli_query($kn, 'SELECT username, hoten, gioitinh, quocgia FROM nguoi_dung ORDER BY username');
$users = mysqli_fetch_all($ketQua, MYSQLI_ASSOC);
$title = 'Quản lý người dùng';
require __DIR__ . '/header.php';
?>
<h1>Thông tin người dùng</h1>
<p>Quản trị: <strong><?= e($_SESSION['nguoiquantri']) ?></strong> · <?= count($users) ?> thành viên</p>
<?php if (!$users): ?><p>Chưa có thành viên. <a href="dangky.php">Đăng ký thành viên đầu tiên</a>.</p>
<?php else: ?>
<div class="table-wrap"><table>
    <thead><tr><th>Tên đăng nhập</th><th>Mật khẩu</th><th>Họ và tên</th><th>Giới tính</th><th>Quốc gia</th><th>Ảnh</th><th>Sửa</th><th>Xóa</th></tr></thead>
    <tbody>
    <?php foreach ($users as $user): ?>
        <tr>
            <td><?= e($user['username']) ?></td><td><small>Đã mã hóa</small></td>
            <td><?= e($user['hoten']) ?></td><td><?= (int) $user['gioitinh'] === 0 ? 'Nam' : 'Nữ' ?></td>
            <td><?= e(countries()[$user['quocgia']] ?? $user['quocgia']) ?></td>
            <td><img class="avatar small" src="avatar.php?user=<?= rawurlencode($user['username']) ?>" alt="Ảnh của <?= e($user['hoten']) ?>"></td>
            <td><a href="sua.php?user=<?= rawurlencode($user['username']) ?>">Sửa</a></td>
            <td><a href="xoa.php?user=<?= rawurlencode($user['username']) ?>">Xóa</a></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table></div>
<?php endif; ?>
<?php require __DIR__ . '/footer.php'; ?>
