<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';

// Ba bước giống mẫu: kết nối MySQL, chọn CSDL, đặt bảng mã tiếng Việt.
$config = require __DIR__ . '/config.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$kn = mysqli_connect($config['db_host'], $config['db_user'], $config['db_password'], '', (int) $config['db_port']);
$csdl = mysqli_select_db($kn, $config['db_name']);
mysqli_set_charset($kn, 'utf8mb4');
