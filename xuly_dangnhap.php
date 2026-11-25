<?php
require_once __DIR__ . '/includes/users.php';
verify_post(); // Chỉ nhận dữ liệu gửi từ form hợp lệ.

// BƯỚC 1: nhận hai ô có name="tendn" và name="matkhau" trong dangnhap.php.
$tenDangNhap = $_POST['tendn'] ?? '';
$matKhau = $_POST['matkhau'] ?? '';

// BƯỚC 2: kiểm tra dữ liệu trước khi truy vấn.
if (!is_string($tenDangNhap) || !is_string($matKhau)) {
    flash('Sai tên đăng nhập hoặc mật khẩu.', 'error');
    redirect('dangnhap.php');
}
$tenDangNhap = trim($tenDangNhap);
if ($tenDangNhap === '' || $matKhau === '' || strlen($tenDangNhap) > 20 || strlen($matKhau) > 72) {
    flash('Sai tên đăng nhập hoặc mật khẩu.', 'error');
    redirect('dangnhap.php');
}

// BƯỚC 3: tìm dòng có username bằng tên vừa nhập.
// Dấu ? nhận giá trị $tenDangNhap ở execute(), không nối dữ liệu vào câu SQL.
$sql = 'SELECT * FROM nguoi_dung WHERE username = ?'; // * là lấy tất cả các cột.
$truyVan = mysqli_prepare($kn, $sql);
mysqli_stmt_bind_param($truyVan, 's', $tenDangNhap); // s: giá trị kiểu chuỗi.
mysqli_stmt_execute($truyVan);
$ketQua = mysqli_stmt_get_result($truyVan);
$nguoiDung = mysqli_fetch_assoc($ketQua); // Lấy một dòng theo tên cột.

// BƯỚC 4: không có tài khoản hoặc mật khẩu không khớp thì quay lại đăng nhập.
// password_matches() so sánh MD5; hàm nằm trong includes/users.php.
if (!$nguoiDung || !password_matches($matKhau, $nguoiDung['password'])) {
    flash('Sai tên đăng nhập hoặc mật khẩu.', 'error');
    redirect('dangnhap.php');
}

// BƯỚC 5: session ghi nhớ ai đã đăng nhập, giống $_SESSION trong code mẫu.
session_regenerate_id(true);
$_SESSION['nguoidung'] = $nguoiDung['username'];
unset($_SESSION['csrf']);
flash('Đăng nhập thành công.');
redirect('trangtt_canhan.php');
