<?php
declare(strict_types=1);
require_once __DIR__ . '/../ketnoi.php';

// Các hàm chạy SQL dùng chung; ketnoi.php chỉ phụ trách kết nối.
function db(): mysqli
{
    global $kn;
    return $kn;
}

function execute_sql(string $sql, array $parameters): mysqli_stmt
{
    $statement = mysqli_prepare(db(), $sql);
    $values = array_values($parameters);
    if ($values !== []) {
        $types = '';
        foreach ($values as $value) {
            if (is_int($value)) {
                $types .= 'i';
            } elseif (is_float($value)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
        }
        mysqli_stmt_bind_param($statement, $types, ...$values);
    }
    mysqli_stmt_execute($statement);
    return $statement;
}
