<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit;
}
require_once __DIR__ . '/../includes/users.php';

// Chạy trên Apache/XAMPP thật. Chỉ tạo và dọn các bản ghi thử có tên ngẫu nhiên.
final class Browser
{
    private CurlHandle $curl;
    public function __construct(private string $baseUrl)
    {
        $this->curl = curl_init();
        curl_setopt_array($this->curl, [CURLOPT_RETURNTRANSFER => true, CURLOPT_HEADER => true,
            CURLOPT_COOKIEFILE => '', CURLOPT_FOLLOWLOCATION => false, CURLOPT_TIMEOUT => 15,
            CURLOPT_NOPROXY => '*']);
    }
    public function request(string $path, ?array $data = null): array
    {
        curl_setopt($this->curl, CURLOPT_URL, $this->baseUrl . $path);
        if ($data === null) {
            curl_setopt($this->curl, CURLOPT_HTTPGET, true);
        } else {
            curl_setopt($this->curl, CURLOPT_POST, true);
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
        }
        $raw = curl_exec($this->curl);
        if ($raw === false) {
            throw new RuntimeException(curl_error($this->curl));
        }
        $length = curl_getinfo($this->curl, CURLINFO_HEADER_SIZE);
        return ['status' => curl_getinfo($this->curl, CURLINFO_RESPONSE_CODE),
            'headers' => substr($raw, 0, $length), 'body' => substr($raw, $length)];
    }
    public function token(string $path): string
    {
        $response = $this->request($path);
        expect($response['status'] === 200, 'GET ' . $path . ' trả về 200');
        if (!preg_match('/name="csrf" value="([a-f0-9]{64})"/', $response['body'], $matches)) {
            throw new RuntimeException('Không thấy CSRF trên ' . $path);
        }
        return $matches[1];
    }
}

$checks = 0;
function expect(bool $condition, string $message): void
{
    global $checks;
    $checks++;
    if (!$condition) {
        throw new RuntimeException('FAIL: ' . $message);
    }
}
function redirected(array $response, string $destination): void
{
    $location = preg_match('/^Location: (.+)$/mi', $response['headers'], $matches) ? trim($matches[1]) : '(không có)';
    expect($response['status'] === 303 && str_contains($response['headers'], 'Location: ' . $destination),
        'Chuyển tới ' . $destination . '; thực tế HTTP ' . $response['status'] . ', Location: ' . $location);
}

$base = rtrim(getenv('TEST_BASE_URL') ?: 'http://localhost/BTTH9', '/') . '/';
$anonymous = new Browser($base);
$member = new Browser($base);
$admin = new Browser($base);
$suffix = bin2hex(random_bytes(4));
$username = 'test_' . $suffix;
$secondUser = 'noimg_' . $suffix;
$adminName = 'adm_' . $suffix;
$password = 'Test-pass@2026';
$changedPassword = 'Changed-pass@2026';
$connection = db();
$seeded = false;
$temporaryFiles = [];
$avatarPaths = [];
$exitCode = 0;
try {
    execute_sql('INSERT INTO admin (useradmin, passadmin) VALUES (?, ?)', [$adminName, md5($password)]);
    $seeded = true;
    foreach (array_merge(['index.php', 'dangky.php', 'dangnhap.php', 'dangnhapQT.php', 'danhsach_sach.php'], array_map(fn ($n) => "bt$n.php", range(1, 10))) as $path) {
        $response = $anonymous->request($path);
        expect($response['status'] === 200 && str_contains($response['body'], '</html>'), 'Trang ' . $path . ' hiển thị đầy đủ');
        expect(!preg_match('/(Fatal error|Warning:|Deprecated:)/i', $response['body']), 'Không lỗi PHP: ' . $path);
    }
    foreach (['config.php', 'config.local.example.php', 'includes/numbers.php', 'database/schema.sql', 'scripts/setup.php', 'docs/extracted/PHAN%202%20-%20php%20va%20SQL.txt', 'tests/run.php', 'reference/giao%20dien_new/giao%20dien/header.php'] as $path) {
        expect($anonymous->request($path)['status'] === 403, 'Chặn truy cập nội bộ: ' . $path);
    }
    redirected($anonymous->request('trangtt_canhan.php'), 'dangnhap.php');
    foreach (['quantri.php', 'sua.php?user=x', 'xoa.php?user=x', 'thuchien_sua.php'] as $path) {
        redirected($anonymous->request($path), 'dangnhapQT.php');
    }
    expect($anonymous->request('xlydangky.php')['status'] === 405, 'Đăng ký chỉ nhận POST');
    expect($anonymous->request('xlydangky.php', [])['status'] === 403, 'Đăng ký yêu cầu CSRF');

    foreach ([['admin', 'Btth9@2026', true], ['admin2', 'Admin2@2026', true],
        ['user01', 'User01@2026', false], ['user02', 'User02@2026', false]] as [$name, $plain, $isAdmin]) {
        $sample = new Browser($base);
        $loginForm = $isAdmin ? 'dangnhapQT.php' : 'dangnhap.php';
        $handler = $isAdmin ? 'xuly_dangnhapQT.php' : 'xuly_dangnhap.php';
        $token = $sample->token($loginForm);
        redirected($sample->request($handler, ['csrf' => $token, 'tendn' => $name, 'matkhau' => $plain]), $isAdmin ? 'quantri.php' : 'trangtt_canhan.php');
    }
    $catalog = $anonymous->request('danhsach_sach.php');
    $bookCount = (int) mysqli_fetch_row(mysqli_query($connection, 'SELECT COUNT(*) FROM sach'))[0];
    expect(substr_count($catalog['body'], 'class="book"') === $bookCount, 'Danh mục hiển thị đủ sách trong CSDL');
    $categoryCodes = array_column(mysqli_fetch_all(mysqli_query($connection, 'SELECT maloai FROM loai'), MYSQLI_ASSOC), 'maloai');
    foreach ($categoryCodes as $categoryCode) {
        $query = execute_sql('SELECT COUNT(*) FROM sach WHERE maloai = ?', [$categoryCode]);
        $count = (int) mysqli_fetch_row(mysqli_stmt_get_result($query))[0];
        $filtered = $anonymous->request('danhsach_sach.php?loai=' . rawurlencode($categoryCode));
        expect($filtered['status'] === 200 && substr_count($filtered['body'], 'class="book"') === $count, 'Lọc sách theo loại ' . $categoryCode);
    }
    expect($anonymous->request('danhsach_sach.php?loai=bad')['status'] === 404, 'Chặn loại sách không tồn tại');
    $coverPaths = array_column(mysqli_fetch_all(mysqli_query($connection, 'SELECT hinhsach FROM sach'), MYSQLI_ASSOC), 'hinhsach');
    foreach ($coverPaths as $coverPath) {
        $cover = $anonymous->request($coverPath);
        expect($cover['status'] === 200 && str_contains($cover['headers'], 'image/jpeg'), 'Đường dẫn ảnh bìa dùng được: ' . $coverPath);
    }

    $mathCases = [
        [1, ['a' => '9', 'b' => '4', 'pheptinh' => '%'], '9 % 4 = 1'],
        [1, ['a' => '9', 'b' => '0', 'pheptinh' => '/'], 'Không thể chia cho 0'],
        [2, ['a' => '0', 'b' => '0'], 'vô số nghiệm'],
        [3, ['a' => '2', 'b' => '3', 'c' => '-5'], 'x₁ = 1; x₂ = -2.5'],
        [4, ['hinh' => 'tron', 'r' => '1'], 'Diện tích = 3.14159265359'],
        [4, ['hinh' => 'tamgiac', 'day' => '3', 'chieucao' => '4'], 'Diện tích = 6'],
        [4, ['hinh' => 'chunhat', 'dai' => '3', 'rong' => '4'], 'Diện tích = 12'],
        [4, ['hinh' => 'tron', 'r' => '-1'], 'Bán kính phải lớn hơn 0'],
        [5, ['a' => '3', 'b' => '4', 'c' => '5'], 'tam giác vuông'],
        [6, ['thang' => '2', 'nam' => '2024'], 'có 29 ngày'],
        [6, ['thang' => '13', 'nam' => '2026'], 'Không có tháng này'],
        [8, ['n' => '10'], '10 × 10'],
        [8, ['n' => '11'], 'phải từ 1 đến 10'],
        [9, ['n' => '97'], '97 là số nguyên tố'],
        [9, ['n' => '1'], '1 không phải là số nguyên tố'],
    ];
    foreach ($mathCases as [$exercise, $data, $expected]) {
        $data['csrf'] = $anonymous->token("bt$exercise.php");
        $response = $anonymous->request("bt$exercise.php", $data);
        expect($response['status'] === 200 && str_contains($response['body'], $expected), 'Kết quả HTTP bài ' . $exercise . ': ' . $expected);
    }
    expect(substr_count($anonymous->request('bt7.php')['body'], '<caption>') === 9, 'Đủ 9 bảng cửu chương 2–10');
    expect(str_contains($anonymous->request('bt10.php')['body'], date('l jS \o\f F Y')), 'Ngày hiện tại theo múi giờ Việt Nam');

    $jpeg = __DIR__ . '/../reference/hinhanh/hinhanh/139075.jpg';
    expect(is_file($jpeg), 'Có ảnh mẫu trong tài liệu đã giải nén');
    $registration = ['tendn' => $username, 'matkhau' => $password, 'hoten' => 'Sinh viên thử nghiệm', 'gioitinh' => '0', 'quocgia' => 'vn'];
    $token = $member->token('dangky.php');
    redirected($member->request('xlydangky.php', $registration + ['csrf' => $token, 'hinhanh' => new CURLFile($jpeg, 'image/jpeg', 'avatar.php')]), 'trangtt_canhan.php');
    $user = find_user($username);
    expect((bool) $user && hash_equals(md5($password), $user['password']), 'CSDL lưu mật khẩu MD5 theo ảnh');
    expect((int) $user['gioitinh'] === 0, 'Đăng ký giới tính Nam lưu BIT(2) bằng 0');
    expect((bool) preg_match('~^storage/avatars/[a-f0-9]{32}\.jpg$~', $user['hinhdaidien']), 'Lưu đường dẫn ảnh ngẫu nhiên, bỏ tên file PHP');
    $avatarPaths[] = $user['hinhdaidien'];
    expect(is_file(avatar_disk_path($user['hinhdaidien'])), 'Ảnh tồn tại trên đĩa');
    expect($member->request($user['hinhdaidien'])['status'] === 403, 'Chặn truy cập trực tiếp thư mục upload');
    $image = $member->request('avatar.php?user=' . $username);
    expect($image['status'] === 200 && str_contains($image['headers'], 'image/jpeg') && hash('sha256', $image['body']) === hash_file('sha256', $jpeg), 'Hiển thị đúng dữ liệu ảnh');
    expect(str_contains($member->request('trangtt_canhan.php')['body'], 'Sinh viên thử nghiệm'), 'Tự đăng nhập sau đăng ký');
    redirected($member->request('quantri.php'), 'dangnhapQT.php');
    redirected($member->request('thuchien_sua.php', ['tendn' => $username, 'hoten' => 'Tấn công']), 'dangnhapQT.php');
    expect(find_user($username)['hoten'] === 'Sinh viên thử nghiệm', 'Thành viên không sửa được thông tin qua trang quản trị');

    $token = $anonymous->token('dangky.php');
    redirected($anonymous->request('xlydangky.php', $registration + ['csrf' => $token]), 'dangky.php');
    expect(str_contains($anonymous->request('dangky.php')['body'], 'đã tồn tại'), 'Thông báo trùng tên đăng nhập');
    $badImage = tempnam(sys_get_temp_dir(), 'btth9');
    file_put_contents($badImage, '<?php echo "invalid image";');
    $temporaryFiles[] = $badImage;
    $invalid = array_replace($registration, ['tendn' => $secondUser]);
    redirected($anonymous->request('xlydangky.php', $invalid + ['csrf' => $token, 'hinhanh' => new CURLFile($badImage, 'image/png', 'bad.png')]), 'dangky.php');
    expect(!find_user($secondUser), 'Ảnh giả không tạo tài khoản');
    expect(str_contains($anonymous->request('dangky.php')['body'], 'ảnh JPG, PNG, GIF hoặc WebP hợp lệ'), 'Thông báo ảnh giả');
    $oversized = tempnam(sys_get_temp_dir(), 'btth9');
    file_put_contents($oversized, str_repeat('a', 1000001));
    $temporaryFiles[] = $oversized;
    redirected($anonymous->request('xlydangky.php', $invalid + ['csrf' => $token, 'hinhanh' => new CURLFile($oversized, 'image/jpeg', 'large.jpg')]), 'dangky.php');
    expect(!find_user($secondUser), 'Ảnh vượt 1 MB không tạo tài khoản');
    foreach (['gioitinh' => '8', 'quocgia' => 'invalid', 'hoten' => ''] as $field => $value) {
        redirected($anonymous->request('xlydangky.php', array_replace($invalid, [$field => $value, 'csrf' => $token])), 'dangky.php');
        expect(!find_user($secondUser), 'Chặn dữ liệu sai: ' . $field);
    }
    redirected($anonymous->request('xlydangky.php', $invalid + ['csrf' => $token]), 'trangtt_canhan.php');
    expect(find_user($secondUser)['hinhdaidien'] === '', 'Đăng ký không ảnh dùng ảnh mặc định');

    $token = $member->token('trangtt_canhan.php');
    redirected($member->request('thoat.php', ['csrf' => $token]), 'dangnhap.php');
    redirected($member->request('trangtt_canhan.php'), 'dangnhap.php');
    $token = $member->token('dangnhap.php');
    redirected($member->request('xuly_dangnhap.php', ['csrf' => $token, 'tendn[]' => $username, 'matkhau' => $password]), 'dangnhap.php');
    redirected($member->request('xuly_dangnhap.php', ['csrf' => $token, 'tendn' => "' OR 1=1 --", 'matkhau' => $password]), 'dangnhap.php');
    redirected($member->request('xuly_dangnhap.php', ['csrf' => $token, 'tendn' => $username, 'matkhau' => 'wrong-pass']), 'dangnhap.php');
    expect(str_contains($member->request('dangnhap.php')['body'], 'Sai tên đăng nhập'), 'Sai mật khẩu có thông báo');
    redirected($member->request('xuly_dangnhap.php', ['csrf' => $token, 'tendn' => $username, 'matkhau' => $password]), 'trangtt_canhan.php');

    $token = $admin->token('dangnhapQT.php');
    redirected($admin->request('xuly_dangnhapQT.php', ['csrf' => $token, 'tendn[]' => $adminName, 'matkhau' => $password]), 'dangnhapQT.php');
    redirected($admin->request('xuly_dangnhapQT.php', ['csrf' => $token, 'tendn' => $username, 'matkhau' => $password]), 'dangnhapQT.php');
    redirected($admin->request('xuly_dangnhapQT.php', ['csrf' => $token, 'tendn' => $adminName, 'matkhau' => $password]), 'quantri.php');
    $token = $admin->token('quantri.php');
    expect(str_contains($admin->request('quantri.php')['body'], $username), 'Quản trị xem danh sách người dùng');
    expect($admin->request('sua.php?user=not_found_' . $suffix)['status'] === 404, 'Sửa người không tồn tại trả về 404');
    $edit = ['csrf' => $token, 'tendn' => $username, 'matkhau' => '', 'hoten' => '<script>alert(1)</script>', 'gioitinh' => '1', 'quocgia' => 'jp', 'hinhcu' => '../../config.php'];
    redirected($admin->request('thuchien_sua.php', $edit), 'quantri.php');
    expect(find_user($username)['hinhdaidien'] === $user['hinhdaidien'], 'Không đổi ảnh thì giữ ảnh từ CSDL, bỏ qua hinhcu giả');
    expect(password_matches($password, find_user($username)['password']), 'Để trống mật khẩu khi sửa thì giữ mật khẩu');
    expect((int) find_user($username)['gioitinh'] === 1, 'Đổi giới tính Nữ lưu BIT(2) bằng 1');
    $profile = $member->request('trangtt_canhan.php')['body'];
    expect(str_contains($profile, '&lt;script&gt;alert(1)&lt;/script&gt;') && !str_contains($profile, '<script>'), 'Mã HTML trong họ tên được escape');
    expect(str_contains($profile, 'Japan'), 'Cập nhật quốc gia');
    expect($admin->request('thuchien_sua.php', array_replace($edit, ['csrf' => 'wrong']))['status'] === 403, 'CSRF bảo vệ sửa');
    $updated = array_replace($edit, ['matkhau' => $changedPassword, 'hoten' => 'Thành viên đã sửa', 'hinh' => new CURLFile($jpeg, 'image/jpeg', 'new.jpg')]);
    redirected($admin->request('thuchien_sua.php', $updated), 'quantri.php');
    $newUser = find_user($username);
    $avatarPaths[] = $newUser['hinhdaidien'];
    expect($newUser['hinhdaidien'] !== $user['hinhdaidien'] && !is_file(avatar_disk_path($user['hinhdaidien'])), 'Đổi ảnh xóa file cũ');
    expect(hash_equals(md5($changedPassword), $newUser['password']), 'Cập nhật mật khẩu mới bằng MD5');
    $tokenMember = $member->token('trangtt_canhan.php');
    redirected($member->request('thoat.php', ['csrf' => $tokenMember]), 'dangnhap.php');
    $tokenMember = $member->token('dangnhap.php');
    redirected($member->request('xuly_dangnhap.php', ['csrf' => $tokenMember, 'tendn' => $username, 'matkhau' => $password]), 'dangnhap.php');
    redirected($member->request('xuly_dangnhap.php', ['csrf' => $tokenMember, 'tendn' => $username, 'matkhau' => $changedPassword]), 'trangtt_canhan.php');

    expect($admin->request('xoa.php?user=' . $username)['status'] === 200 && (bool) find_user($username), 'GET xóa chỉ hiển thị xác nhận');
    expect($admin->request('xoa.php', ['user' => $username, 'csrf' => 'wrong'])['status'] === 403, 'CSRF bảo vệ xóa');
    expect((bool) find_user($username), 'CSRF sai không xóa dữ liệu');
    redirected($admin->request('xoa.php', ['user' => $username, 'csrf' => $token]), 'quantri.php');
    expect(!find_user($username) && !is_file(avatar_disk_path($newUser['hinhdaidien'])), 'Xóa tài khoản và ảnh');
    redirected($member->request('trangtt_canhan.php'), 'dangnhap.php');
    expect($admin->request('thoatQT.php')['status'] === 200, 'GET thoát quản trị là trang xác nhận');
    expect($admin->request('quantri.php')['status'] === 200, 'GET thoát chưa hủy quyền quản trị');
    redirected($admin->request('thoatQT.php', ['csrf' => $token]), 'dangnhapQT.php');
    redirected($admin->request('quantri.php'), 'dangnhapQT.php');
    echo "PASS: $checks kiểm tra HTTP trên XAMPP (PHP + MySQL + upload + phân quyền).\n";
} catch (Throwable $exception) {
    fwrite(STDERR, 'Sau ' . $checks . ' kiểm tra: ' . $exception->getMessage() . "\n");
    $exitCode = 1;
} finally {
    foreach ([$username, $secondUser] as $name) {
        $remaining = find_user($name);
        if ($remaining) {
            remove_avatar($remaining['hinhdaidien']);
            execute_sql('DELETE FROM nguoi_dung WHERE username = ?', [$name]);
        }
    }
    foreach ($avatarPaths as $path) { remove_avatar($path); }
    if ($seeded) {
        execute_sql('DELETE FROM admin WHERE useradmin = ?', [$adminName]);
    }
    foreach ($temporaryFiles as $path) { if (is_file($path)) { unlink($path); } }
}
exit($exitCode);
