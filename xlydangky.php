<?php
require_once __DIR__ . '/includes/users.php';
verify_post();
// Đọc các BƯỚC 1–5 để theo dõi luồng đăng ký giống tài liệu Phần 2.
$hinhMoi = '';
try {
    // BƯỚC 1: nhận và kiểm tra các ô của dangky.php.
    $nguoiDung = validate_user($_POST);

    // BƯỚC 2: tên đăng nhập không được trùng người đã có trong CSDL.
    if (find_user($nguoiDung['username'])) {
        throw new InvalidArgumentException('Tên đăng nhập đã tồn tại. Hãy chọn tên khác.');
    }

    // BƯỚC 3: lưu file ảnh; hàm trả về đường dẫn để đưa vào CSDL.
    $hinhMoi = store_avatar('hinhanh');

    // BƯỚC 4: thêm dòng người dùng. Sáu giá trị tương ứng sáu dấu ?.
    $sql = 'INSERT INTO nguoi_dung (username, password, hoten, gioitinh, quocgia, hinhdaidien) VALUES (?, ?, ?, ?, ?, ?)';
    $giaTri = [
        $nguoiDung['username'],
        md5($nguoiDung['password']),
        $nguoiDung['hoten'],
        $nguoiDung['gioitinh'],
        $nguoiDung['quocgia'],
        $hinhMoi,
    ];
    execute_sql($sql, $giaTri); // Hàm chạy SQL trong includes/database.php.
} catch (Throwable $exception) {
    // Nếu có lỗi thì dọn ảnh vừa tải và trả thông báo về form đăng ký.
    remove_avatar($hinhMoi);
    if ($exception instanceof InvalidArgumentException || ($exception instanceof mysqli_sql_exception && $exception->getCode() === 1062)) {
        $_SESSION['old_registration'] = old_user_input($_POST);
        flash($exception instanceof InvalidArgumentException ? $exception->getMessage() : 'Tên đăng nhập đã tồn tại. Hãy chọn tên khác.', 'error');
        redirect('dangky.php');
    }
    throw $exception;
}

// BƯỚC 5: đăng ký xong thì lưu session và chuyển sang thông tin cá nhân.
session_regenerate_id(true);
$_SESSION['nguoidung'] = $nguoiDung['username'];
unset($_SESSION['old_registration'], $_SESSION['csrf']);
flash('Đăng ký thành công. Đây là thông tin của bạn.');
redirect('trangtt_canhan.php');
