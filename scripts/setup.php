<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Chỉ chạy file này từ dòng lệnh.');
}

function execute_sql_batch(mysqli $connection, string $sql): void
{
    mysqli_multi_query($connection, $sql);
    do {
        $result = mysqli_store_result($connection);
        if ($result !== false) {
            mysqli_free_result($result);
        }
    } while (mysqli_more_results($connection) && mysqli_next_result($connection));
}

try {
    $options = getopt('', ['admin:', 'password:', 'seed-demo', 'database:']);
    $username = $options['admin'] ?? 'admin';
    if (!is_string($username) || !preg_match('/^[a-zA-Z0-9_]{3,20}$/D', $username)) {
        throw new RuntimeException('Tên quản trị phải gồm 3–20 chữ cái không dấu, chữ số hoặc dấu gạch dưới.');
    }
    $config = require __DIR__ . '/../config.php';
    if (isset($options['database'])) {
        $config['db_name'] = $options['database'];
    }
    if (!preg_match('/^[a-zA-Z0-9_]+$/D', $config['db_name'])) {
        throw new RuntimeException('Tên cơ sở dữ liệu chỉ được chứa chữ cái, chữ số và dấu gạch dưới.');
    }
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $server = mysqli_connect($config['db_host'], $config['db_user'], $config['db_password'], '', (int) $config['db_port']);
    mysqli_set_charset($server, 'utf8mb4');
    mysqli_query($server, 'CREATE DATABASE IF NOT EXISTS `' . $config['db_name'] . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    mysqli_select_db($server, $config['db_name']);
    $schema = file_get_contents(__DIR__ . '/../database/schema.sql');
    if ($schema === false) {
        throw new RuntimeException('Không đọc được database/schema.sql.');
    }
    execute_sql_batch($server, $schema);
    require_once __DIR__ . '/upgrade-schema.php';
    upgrade_part2_schema($server);
    if (array_key_exists('seed-demo', $options)) {
        $seed = file_get_contents(__DIR__ . '/../database/seed.sql');
        if ($seed === false) {
            throw new RuntimeException('Không đọc được database/seed.sql.');
        }
        execute_sql_batch($server, $seed);
        // Chuyển hash cũ của tài khoản mẫu chỉ khi khớp đúng mật khẩu mẫu.
        // Mật khẩu do người dùng đã thay đổi sẽ được giữ nguyên.
        foreach ([['admin', 'useradmin', 'passadmin', 'admin', 'Btth9@2026'],
            ['admin', 'useradmin', 'passadmin', 'admin2', 'Admin2@2026'],
            ['nguoi_dung', 'username', 'password', 'user01', 'User01@2026'],
            ['nguoi_dung', 'username', 'password', 'user02', 'User02@2026']] as [$table, $key, $column, $name, $plain]) {
            $existing = mysqli_prepare($server, "SELECT $column FROM $table WHERE $key = ?");
            mysqli_stmt_bind_param($existing, 's', $name);
            mysqli_stmt_execute($existing);
            $row = mysqli_fetch_row(mysqli_stmt_get_result($existing));
            $stored = $row[0] ?? null;
            if (is_string($stored) && password_verify($plain, $stored)) {
                $hash = md5($plain);
                $update = mysqli_prepare($server, "UPDATE $table SET $column = ? WHERE $key = ?");
                mysqli_stmt_bind_param($update, 'ss', $hash, $name);
                mysqli_stmt_execute($update);
            }
        }
        echo "Đã nhập dữ liệu mẫu: 2 người dùng, 2 quản trị, 5 loại, 10 sách (bỏ qua dữ liệu đã tồn tại).\n";
        echo "Xem danh mục: http://localhost/BTTH9/danhsach_sach.php\n";
        exit(0);
    }
    $statement = mysqli_prepare($server, 'SELECT useradmin FROM admin WHERE useradmin = ?');
    mysqli_stmt_bind_param($statement, 's', $username);
    mysqli_stmt_execute($statement);
    if (mysqli_fetch_assoc(mysqli_stmt_get_result($statement))) {
        echo "CSDL đã sẵn sàng. Tài khoản quản trị $username đã tồn tại, giữ nguyên mật khẩu.\n";
        exit(0);
    }
    $password = $options['password'] ?? null;
    if ($password === null) {
        fwrite(STDOUT, "Nhập mật khẩu quản trị (6–72 byte; ký tự nhập sẽ hiển thị): ");
        $line = fgets(STDIN);
        $password = $line === false ? '' : rtrim($line, "\r\n");
    }
    if (!is_string($password) || strlen($password) < 6 || strlen($password) > 72) {
        throw new RuntimeException('Mật khẩu quản trị phải dài từ 6 đến 72 byte.');
    }
    $hash = md5($password);
    $statement = mysqli_prepare($server, 'INSERT INTO admin (useradmin, passadmin) VALUES (?, ?)');
    mysqli_stmt_bind_param($statement, 'ss', $username, $hash);
    mysqli_stmt_execute($statement);
    echo "Đã khởi tạo CSDL {$config['db_name']} và tạo quản trị $username.\n";
    echo "Mở http://localhost/BTTH9/ và đăng nhập quản trị bằng mật khẩu vừa chọn.\n";
} catch (Throwable $exception) {
    fwrite(STDERR, 'Khởi tạo thất bại: ' . $exception->getMessage() . "\n");
    exit(1);
}
