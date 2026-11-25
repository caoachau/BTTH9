<?php
require_once __DIR__ . '/includes/users.php';
verify_post();

// BƯỚC 1: nhận hai ô từ form dangnhapQT.php.
$tenDangNhap = $_POST['tendn'] ?? '';
$matKhau = $_POST['matkhau'] ?? '';

// BƯỚC 2: kiểm tra dữ liệu nhập.
if (!is_string($tenDangNhap) || !is_string($matKhau)) {
    flash('Sai tên đăng nhập hoặc mật khẩu.', 'error');
    redirect('dangnhapQT.php');
}
$tenDangNhap = trim($tenDangNhap);
if ($tenDangNhap === '' || $matKhau === '' || strlen($tenDangNhap) > 20 || strlen($matKhau) > 72) {
    flash('Sai tên đăng nhập hoặc mật khẩu.', 'error');
    redirect('dangnhapQT.php');
}

// BƯỚC 3: tìm trong bảng admin. Tên cột theo hai ảnh yêu cầu của giáo viên.
$sql = 'SELECT * FROM admin WHERE useradmin = ?';
$truyVan = mysqli_prepare($kn, $sql);
mysqli_stmt_bind_param($truyVan, 's', $tenDangNhap);
mysqli_stmt_execute($truyVan);
$ketQua = mysqli_stmt_get_result($truyVan);
$quanTri = mysqli_fetch_assoc($ketQua);

// BƯỚC 4: kiểm tra tài khoản và mật khẩu.
if (!$quanTri || !password_matches($matKhau, $quanTri['passadmin'])) {
    flash('Sai tên đăng nhập hoặc mật khẩu.', 'error');
    redirect('dangnhapQT.php');
}

// BƯỚC 5: lưu session quản trị rồi chuyển vào trang quantri.php.
session_regenerate_id(true);
$_SESSION['nguoiquantri'] = $quanTri['useradmin'];
unset($_SESSION['csrf']);
flash('Đăng nhập thành công.');
redirect('quantri.php');
