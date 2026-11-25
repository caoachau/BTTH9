<?php
declare(strict_types=1);
// Hàm dùng chung cho các trang tài khoản. Form, SQL đăng nhập và các bước
// đăng ký/sửa/xóa nằm trong các file PHP tương ứng ở thư mục gốc.
require_once __DIR__ . '/database.php';

function countries(): array
{
    return ['vn' => 'Việt Nam', 'cn' => 'China', 'jp' => 'Japan', 'k' => 'Khác'];
}

function password_matches(string $plain, string $stored): bool
{
    if (preg_match('/^[a-f0-9]{32}$/iD', $stored)) {
        return hash_equals(strtolower($stored), md5($plain));
    }
    // Giữ khả năng đăng nhập cho tài khoản đã tạo trước khi bổ sung đề MD5.
    return password_verify($plain, $stored);
}

function find_user(string $username): array|false
{
    $statement = execute_sql('SELECT * FROM nguoi_dung WHERE username = ?', [$username]);
    $result = mysqli_stmt_get_result($statement);
    return mysqli_fetch_assoc($result) ?: false;
}

function require_member(): array
{
    $username = $_SESSION['nguoidung'] ?? null;
    $user = is_string($username) ? find_user($username) : false;
    if (!$user) {
        unset($_SESSION['nguoidung']);
        flash('Hãy đăng nhập để xem thông tin cá nhân.', 'error');
        redirect('dangnhap.php');
    }
    return $user;
}

function require_admin(): void
{
    $username = $_SESSION['nguoiquantri'] ?? null;
    if (is_string($username)) {
        $statement = execute_sql('SELECT useradmin FROM admin WHERE useradmin = ?', [$username]);
        $result = mysqli_stmt_get_result($statement);
        if (mysqli_fetch_assoc($result)) {
            return;
        }
    }
    unset($_SESSION['nguoiquantri']);
    flash('Bạn cần đăng nhập bằng tài khoản quản trị.', 'error');
    redirect('dangnhapQT.php');
}

function validate_user(array $source, bool $editing = false): array
{
    $username = trim(input_text($source, 'tendn'));
    $password = input_text($source, 'matkhau');
    $name = trim(input_text($source, 'hoten'));
    $gender = input_text($source, 'gioitinh');
    $country = input_text($source, 'quocgia');
    if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/D', $username)) {
        throw new InvalidArgumentException('Tên đăng nhập gồm 3–20 chữ cái không dấu, chữ số hoặc dấu gạch dưới.');
    }
    if ((!$editing || $password !== '') && (strlen($password) < 6 || strlen($password) > 72)) {
        throw new InvalidArgumentException('Mật khẩu phải dài từ 6 đến 72 byte (ký tự có dấu có thể chiếm nhiều byte).');
    }
    if ($name === '' || mb_strlen($name, 'UTF-8') > 50) {
        throw new InvalidArgumentException('Họ tên không được để trống và tối đa 50 ký tự.');
    }
    if (!in_array($gender, ['0', '1'], true)) {
        throw new InvalidArgumentException('Hãy chọn giới tính hợp lệ.');
    }
    if (!array_key_exists($country, countries())) {
        throw new InvalidArgumentException('Hãy chọn quốc gia hợp lệ.');
    }
    return ['username' => $username, 'password' => $password, 'hoten' => $name,
        'gioitinh' => (int) $gender, 'quocgia' => $country];
}

function old_user_input(array $source): array
{
    $values = [];
    foreach (['tendn', 'hoten', 'gioitinh', 'quocgia'] as $key) {
        $values[$key] = isset($source[$key]) && is_string($source[$key]) ? $source[$key] : '';
    }
    return $values;
}

function avatar_disk_path(string $relativePath): ?string
{
    if (!preg_match('~^storage/avatars/[a-f0-9]{32}\.(jpg|png|gif|webp)$~D', $relativePath)) {
        return null;
    }
    return __DIR__ . '/../' . $relativePath;
}

function remove_avatar(string $relativePath): void
{
    $path = avatar_disk_path($relativePath);
    if ($path !== null && is_file($path)) {
        if (!unlink($path)) {
            error_log('Không thể xóa ảnh đại diện: ' . $relativePath);
        }
    }
}

function store_avatar(string $field): string
{
    $file = $_FILES[$field] ?? null;
    if ($file === null) {
        return '';
    }
    if (!is_array($file) || !isset($file['error']) || !is_int($file['error'])) {
        throw new InvalidArgumentException('Dữ liệu ảnh tải lên không hợp lệ.');
    }
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return '';
    }
    if (in_array($file['error'], [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)) {
        throw new InvalidArgumentException('Ảnh đại diện không được vượt quá 1.000.000 byte.');
    }
    if ($file['error'] !== UPLOAD_ERR_OK || !isset($file['tmp_name']) || !is_string($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        throw new InvalidArgumentException('Tải ảnh chưa thành công. Hãy chọn lại ảnh.');
    }
    $size = filesize($file['tmp_name']);
    if ($size === false || $size < 1 || $size > 1000000) {
        throw new InvalidArgumentException('Ảnh đại diện không được vượt quá 1.000.000 byte.');
    }
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    $formats = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
    $imageInfo = @getimagesize($file['tmp_name']);
    if (!is_string($mime) || !isset($formats[$mime]) || !$imageInfo || ($imageInfo['mime'] ?? '') !== $mime) {
        throw new InvalidArgumentException('Chỉ chấp nhận ảnh JPG, PNG, GIF hoặc WebP hợp lệ.');
    }
    if ($imageInfo[0] > 4096 || $imageInfo[1] > 4096) {
        throw new InvalidArgumentException('Kích thước ảnh tối đa là 4096 × 4096 pixel.');
    }
    $directory = __DIR__ . '/../storage/avatars';
    if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
        throw new RuntimeException('Không tạo được thư mục ảnh.');
    }
    $relativePath = 'storage/avatars/' . bin2hex(random_bytes(16)) . '.' . $formats[$mime];
    if (!move_uploaded_file($file['tmp_name'], __DIR__ . '/../' . $relativePath)) {
        throw new RuntimeException('Không lưu được ảnh. Kiểm tra quyền ghi storage/avatars.');
    }
    return $relativePath;
}
