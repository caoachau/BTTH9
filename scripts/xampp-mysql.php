<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli' || PHP_OS_FAMILY !== 'Windows') {
    http_response_code(403);
    exit("Script này dùng cho XAMPP trên Windows.\n");
}

function ps_literal(string $value): string
{
    return "'" . str_replace("'", "''", $value) . "'";
}

function run_process(array $command): void
{
    $process = proc_open($command, [0 => STDIN, 1 => STDOUT, 2 => STDERR], $pipes,
        null, null, ['bypass_shell' => true]);
    if (!is_resource($process) || proc_close($process) !== 0) {
        throw new RuntimeException('Không thực hiện được: ' . $command[0]);
    }
}

function normalized_path(string $path): string
{
    return strtolower(rtrim(str_replace('\\', '/', $path), '/'));
}

function local_instance(int $port): ?mysqli
{
    try {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $connection = mysqli_init();
        mysqli_options($connection, MYSQLI_OPT_CONNECT_TIMEOUT, 1);
        mysqli_real_connect($connection, '127.0.0.1', 'root', '', '', $port);
        mysqli_set_charset($connection, 'utf8mb4');
        return $connection;
    } catch (mysqli_sql_exception) {
        return null;
    }
}

try {
    $action = $argv[1] ?? 'start';
    if (!in_array($action, ['start', 'stop', 'status'], true)) {
        throw new RuntimeException('Cách dùng: php scripts/xampp-mysql.php start|stop|status [thu-muc-XAMPP]');
    }
    $xampp = realpath($argv[2] ?? dirname(dirname(PHP_BINARY)));
    if ($xampp === false) {
        throw new RuntimeException('Không tìm thấy thư mục XAMPP.');
    }
    $bin = $xampp . '/mysql/bin';
    $data = $xampp . '/mysql-btth9-data';
    $marker = $data . '/.btth9-instance';
    $iniPath = $data . '/my.ini';
    $port = 3307;
    // Nếu Control Panel đã dùng dữ liệu BTTH9 thì dùng đúng cấu hình đó.
    // Không mở thêm máy chủ thứ hai trên cùng bộ dữ liệu.
    $panelIniPath = $bin . '/my.ini';
    $panelIni = is_file($panelIniPath) ? parse_ini_file($panelIniPath, true, INI_SCANNER_RAW) : [];
    $panelManaged = normalized_path((string) ($panelIni['mysqld']['datadir'] ?? '')) === normalized_path($data);
    if ($panelManaged) {
        $iniPath = $panelIniPath;
        $port = (int) ($panelIni['mysqld']['port'] ?? 3306);
    }
    $connection = local_instance($port);
    if ($connection) {
        $actualData = (string) mysqli_fetch_row(mysqli_query($connection, 'SELECT @@datadir'))[0];
        if (!is_file($marker) || normalized_path($actualData) !== normalized_path($data)) {
            throw new RuntimeException("Cổng $port đang được dùng bởi MySQL khác. Không thay đổi dịch vụ đó.");
        }
        if ($action === 'stop') {
            run_process([$bin . '/mysqladmin.exe', '--host=127.0.0.1', '--port=' . $port, '--user=root', 'shutdown']);
            echo "Đã dừng MySQL XAMPP dùng cho BTTH9.\n";
        } else {
            echo "MySQL BTTH9 đang chạy: 127.0.0.1:$port; dữ liệu: $data\n";
        }
        exit(0);
    }
    if ($action !== 'start') {
        echo "MySQL riêng của BTTH9 chưa chạy.\n";
        exit($action === 'status' ? 1 : 0);
    }
    if (!is_file($bin . '/mysqld.exe') || !is_file($bin . '/mysql_install_db.exe')) {
        throw new RuntimeException('Không tìm thấy MariaDB đi kèm XAMPP tại ' . $bin);
    }
    // Chỉ khởi tạo thư mục mới/đang trống, không chạm mysql/data của XAMPP.
    if (!is_file($marker)) {
        if (is_dir($data) && count(scandir($data) ?: []) > 2) {
            throw new RuntimeException('Thư mục dữ liệu đã có nội dung và không thuộc BTTH9: ' . $data);
        }
        if (!is_dir($data) && !mkdir($data, 0775, true)) {
            throw new RuntimeException('Không tạo được ' . $data);
        }
        run_process([$bin . '/mysql_install_db.exe', '--datadir=' . $data, '--port=' . $port]);
        if (file_put_contents($marker, "BTTH9 isolated XAMPP MariaDB\n") === false) {
            throw new RuntimeException('Không ghi được dấu nhận diện MySQL BTTH9.');
        }
    }
    $mysqlBase = str_replace('\\', '/', $xampp . '/mysql');
    $mysqlData = str_replace('\\', '/', $data);
    $ini = "[mysqld]\nbasedir=\"$mysqlBase\"\ndatadir=\"$mysqlData\"\nport=3307\nbind-address=127.0.0.1\n"
        . "character-set-server=utf8mb4\ncollation-server=utf8mb4_unicode_ci\nmax-allowed-packet=16M\n"
        . "innodb-buffer-pool-size=64M\nlog-error=\"$mysqlData/mysql-error.log\"\npid-file=\"$mysqlData/mysql.pid\"\n";
    if (!$panelManaged && file_put_contents($iniPath, $ini) === false) {
        throw new RuntimeException('Không ghi được cấu hình MySQL BTTH9.');
    }
    $powerShell = (getenv('SystemRoot') ?: 'C:/Windows') . '/System32/WindowsPowerShell/v1.0/powershell.exe';
    $ps = 'Start-Process -FilePath ' . ps_literal($bin . '/mysqld.exe')
        . ' -ArgumentList @(' . ps_literal('--defaults-file="' . $iniPath . '"') . ",'--standalone')"
        . ' -WorkingDirectory ' . ps_literal($bin) . ' -WindowStyle Hidden'
        . ' -RedirectStandardOutput ' . ps_literal($data . '/mysql-start.stdout.log')
        . ' -RedirectStandardError ' . ps_literal($data . '/mysql-start.stderr.log');
    run_process([$powerShell, '-NoProfile', '-NonInteractive', '-Command', $ps]);
    for ($attempt = 0; $attempt < 25; $attempt++) {
        usleep(200000);
        $connection = local_instance($port);
        if ($connection) {
            $actualData = (string) mysqli_fetch_row(mysqli_query($connection, 'SELECT @@datadir'))[0];
            if (normalized_path($actualData) !== normalized_path($data)) {
                throw new RuntimeException("Dịch vụ trên cổng $port không dùng thư mục dữ liệu BTTH9.");
            }
            echo "Đã bật MariaDB của XAMPP cho BTTH9 tại 127.0.0.1:$port.\n";
            echo "Dữ liệu riêng: $data\n";
            exit(0);
        }
    }
    throw new RuntimeException('MySQL BTTH9 chưa sẵn sàng. Kiểm tra ' . $data . '/mysql-error.log');
} catch (Throwable $exception) {
    fwrite(STDERR, $exception->getMessage() . "\n");
    exit(1);
}
