<?php
declare(strict_types=1);
$testDatabase = getenv('BTTH9_MYSQLI_TEST_DATABASE');
if (!is_string($testDatabase) || !preg_match('/^btth9_mysqli_test_[a-f0-9]{8}$/D', $testDatabase)) {
    throw new RuntimeException('Unexpected test database name.');
}
require_once __DIR__ . '/../ketnoi.php';
mysqli_select_db($kn, $testDatabase);
require __DIR__ . '/../tests/requirements.php';
foreach (['nguoi_dung' => 2, 'admin' => 2, 'loai' => 5, 'sach' => 10] as $table => $expected) {
    $count = (int) mysqli_fetch_row(mysqli_query($kn, 'SELECT COUNT(*) FROM ' . $table))[0];
    if ($count !== $expected) {
        throw new RuntimeException('Unexpected seed count for ' . $table);
    }
}
echo "Fresh mysqli install and demo counts OK.\n";
