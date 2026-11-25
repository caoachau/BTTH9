<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit;
}
require_once __DIR__ . '/../includes/users.php';
$checks = 0;
function require_check(bool $valid, string $message): void
{
    global $checks;
    $checks++;
    if (!$valid) {
        fwrite(STDERR, "FAIL: $message\n");
        exit(1);
    }
}

$expected = [
    'nguoi_dung' => ['username' => 'varchar(20)', 'password' => 'text', 'hoten' => 'varchar(50)', 'gioitinh' => 'bit(2)', 'quocgia' => 'varchar(30)', 'hinhdaidien' => 'text'],
    'admin' => ['useradmin' => 'varchar(20)', 'passadmin' => 'text'],
    'loai' => ['maloai' => 'varchar(5)', 'tenloai' => 'varchar(100)'],
    'sach' => ['idsach' => 'int', 'tensach' => 'varchar(100)', 'hinhsach' => 'text', 'mota' => 'text', 'gia' => 'int', 'soluong' => 'int', 'maloai' => 'varchar(5)', 'nxb' => 'varchar(100)'],
];
foreach ($expected as $table => $columns) {
    $actual = mysqli_fetch_all(mysqli_query($kn, 'SHOW COLUMNS FROM `' . $table . '`'), MYSQLI_ASSOC);
    $byName = array_column($actual, null, 'Field');
    require_check(count($actual) === count($columns), 'Đúng số cột bảng ' . $table);
    foreach ($columns as $column => $type) {
        $actualType = $byName[$column]['Type'] ?? '';
        require_check($type === 'int' ? (bool) preg_match('/^int(?:\(\d+\))?$/', $actualType) : $actualType === $type, "$table.$column đúng kiểu $type");
    }
    $primary = match ($table) { 'nguoi_dung' => 'username', 'admin' => 'useradmin', 'loai' => 'maloai', 'sach' => 'idsach' };
    require_check(($byName[$primary]['Key'] ?? '') === 'PRI', 'Khóa chính ' . $table);
    if ($table === 'sach') {
        require_check(str_contains($byName['idsach']['Extra'], 'auto_increment'), 'idsach tự động tăng');
    }
}
$fkResult = mysqli_query($kn, "SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sach' AND COLUMN_NAME = 'maloai' AND REFERENCED_TABLE_NAME = 'loai' AND REFERENCED_COLUMN_NAME = 'maloai'");
$fk = mysqli_fetch_row($fkResult)[0];
require_check((int) $fk === 1, 'Khóa ngoại sach.maloai → loai.maloai');
$expectedCategories = ['ML001' => 'Công nghệ thông tin', 'ML002' => 'Kinh tế', 'ML003' => 'Du lịch', 'ML004' => 'Văn học', 'ML005' => 'Phát triển bản thân'];
$categoryRows = mysqli_fetch_all(mysqli_query($kn, 'SELECT maloai, tenloai FROM loai'), MYSQLI_ASSOC);
$actualCategories = array_column($categoryRows, 'tenloai', 'maloai');
foreach ($expectedCategories as $code => $name) {
    require_check(($actualCategories[$code] ?? null) === $name, 'Loại sách ' . $code . ': ' . $name);
}
foreach ([['user01', 'User01@2026'], ['user02', 'User02@2026']] as [$username, $password]) {
    $user = find_user($username);
    require_check((bool) $user && hash_equals(md5($password), $user['password']), 'MD5 người dùng mẫu ' . $username);
}
foreach ([['admin', 'Btth9@2026'], ['admin2', 'Admin2@2026']] as [$username, $password]) {
    $statement = execute_sql('SELECT passadmin FROM admin WHERE useradmin = ?', [$username]);
    $row = mysqli_fetch_row(mysqli_stmt_get_result($statement));
    require_check(($row[0] ?? null) === md5($password), 'MD5 quản trị mẫu ' . $username);
}
$covers = ['136452', '139075', '139076', '143801', '144220', '144337', '144343', '144349', '144571', '73900'];
foreach ($covers as $cover) {
    $path = 'hinhanh/' . $cover . '.jpg';
    $statement = execute_sql('SELECT COUNT(*) FROM sach WHERE hinhsach = ? AND idsach > 0 AND gia >= 0 AND soluong >= 0', [$path]);
    $row = mysqli_fetch_row(mysqli_stmt_get_result($statement));
    require_check((int) $row[0] === 1, 'Sách mẫu với ảnh ' . $path);
    require_check(is_file(__DIR__ . '/../' . $path), 'Có file ảnh ' . $path);
}
echo "PASS: $checks kiểm tra schema và dữ liệu theo hai ảnh Phần 2.\n";
