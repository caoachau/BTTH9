<?php
declare(strict_types=1);

// Khởi tạo session và các hàm hỗ trợ chung; không chứa xử lý của từng bài.

date_default_timezone_set('Asia/Ho_Chi_Minh');
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    ]);
    session_start();
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function input_text(array $source, string $key, string $default = ''): string
{
    $value = $source[$key] ?? $default;
    if (!is_string($value)) {
        throw new InvalidArgumentException('Dữ liệu gửi lên không hợp lệ.');
    }
    return $value;
}

function redirect(string $path): never
{
    header('Location: ' . $path, true, 303);
    exit;
}

function flash(string $message, string $type = 'success'): void
{
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

function csrf_token(): string
{
    return $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
}

function csrf_field(): void
{
    echo '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function verify_post(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        header('Allow: POST');
        abort_page(405, 'Trang này chỉ nhận biểu mẫu POST.');
    }
    $token = $_POST['csrf'] ?? null;
    if (!is_string($token) || !hash_equals(csrf_token(), $token)) {
        abort_page(403, 'Phiên biểu mẫu không hợp lệ. Hãy tải lại trang và thử lại.');
    }
}

function abort_page(int $status, string $message): never
{
    http_response_code($status);
    $title = 'Không thể thực hiện yêu cầu';
    require __DIR__ . '/../header.php';
    echo '<h1>' . e($title) . '</h1><p role="alert">' . e($message) . '</p>';
    echo '<p><a href="index.php">Về trang chủ</a></p>';
    require __DIR__ . '/../footer.php';
    exit;
}

set_exception_handler(static function (Throwable $exception): void {
    error_log((string) $exception);
    if (PHP_SAPI === 'cli') {
        fwrite(STDERR, 'Lỗi: ' . $exception->getMessage() . "\n");
        exit(1);
    }
    abort_page(503, 'Không thể truy cập dữ liệu. Hãy bật MySQL trong XAMPP, kiểm tra config.local.php và khởi tạo CSDL theo README.md.');
});
