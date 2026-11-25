<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';

// Các hàm nhập/xuất số dùng chung. Thuật toán của từng bài nằm trong bt*.php.
function read_number(array $values, string $name, string $label): float
{
    $raw = trim(input_text($values, $name));
    if ($raw === '' || !is_numeric($raw)) {
        throw new InvalidArgumentException("$label phải là một số hợp lệ.");
    }
    $number = (float) $raw;
    if (!is_finite($number) || abs($number) > 1.0e12) {
        throw new InvalidArgumentException("$label phải nằm trong khoảng -10¹² đến 10¹².");
    }
    return $number;
}

function read_integer(array $values, string $name, string $label, int $min, int $max): int
{
    $raw = trim(input_text($values, $name));
    if (!preg_match('/^[+-]?\d+$/D', $raw)) {
        throw new InvalidArgumentException("$label phải là số nguyên.");
    }
    $number = read_number($values, $name, $label);
    if ($number < $min || $number > $max) {
        throw new InvalidArgumentException("$label phải từ $min đến $max.");
    }
    return (int) $number;
}

function number_text(float|int $number): string
{
    if (!is_finite((float) $number)) {
        throw new InvalidArgumentException('Kết quả vượt quá khả năng biểu diễn số. Hãy dùng hệ số có độ lớn gần nhau hơn.');
    }
    return $number == 0 ? '0' : sprintf('%.12g', $number);
}

function old_input(string $name, string $default = ''): string
{
    $value = $_POST[$name] ?? $default;
    return is_string($value) ? $value : '';
}
