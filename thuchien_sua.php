<?php
require_once __DIR__ . '/includes/users.php';
require_admin();
verify_post();
// BƯỚC 1: nhận form sua.php và lấy thông tin đang lưu của người cần sửa.
$newAvatar = '';
try {
    $data = validate_user($_POST, true);
    $current = find_user($data['username']);
    if (!$current) {
        abort_page(404, 'Không tìm thấy người dùng cần sửa.');
    }

    // BƯỚC 2: ô mật khẩu mới bỏ trống thì giữ mật khẩu hiện tại.
    if ($data['password'] !== '') {
        $matKhauLuu = md5($data['password']);
    } else {
        $matKhauLuu = $current['password'];
    }

    // BƯỚC 3: không chọn ảnh mới thì giữ đường dẫn ảnh hiện tại.
    $newAvatar = store_avatar('hinh');
    if ($newAvatar !== '') {
        $hinhLuu = $newAvatar;
    } else {
        $hinhLuu = $current['hinhdaidien'];
    }

    // BƯỚC 4: UPDATE thay các giá trị của đúng dòng username được chọn.
    $sql = 'UPDATE nguoi_dung SET password = ?, hoten = ?, gioitinh = ?, quocgia = ?, hinhdaidien = ? WHERE username = ?';
    $truyVan = mysqli_prepare($kn, $sql);
    mysqli_stmt_bind_param($truyVan, 'ssisss', $matKhauLuu,
        $data['hoten'], $data['gioitinh'], $data['quocgia'], $hinhLuu, $data['username']);
    mysqli_stmt_execute($truyVan);
} catch (Throwable $exception) {
    remove_avatar($newAvatar);
    if ($exception instanceof InvalidArgumentException) {
        $_SESSION['old_edit'] = old_user_input($_POST);
        flash($exception->getMessage(), 'error');
        $username = $_SESSION['old_edit']['tendn'];
        redirect('sua.php?user=' . rawurlencode($username));
    }
    throw $exception;
}
if ($newAvatar !== '') {
    remove_avatar($current['hinhdaidien']);
}
flash('Đã cập nhật thông tin người dùng.');
// BƯỚC 5: quay lại danh sách sau khi lưu thành công.
redirect('quantri.php');
