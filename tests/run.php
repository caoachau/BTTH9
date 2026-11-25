<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit;
}
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/numbers.php';

$checks = 0;
function check(bool $condition, string $message): void
{
    global $checks;
    $checks++;
    if (!$condition) {
        fwrite(STDERR, "FAIL: $message\n");
        exit(1);
    }
}
function rejects(callable $callback, string $message): void
{
    try {
        $callback();
        check(false, $message);
    } catch (InvalidArgumentException) {
        check(true, $message);
    }
}

// Chạy chính file bài tập và đọc kết quả HTML, không duy trì bản sao thuật toán.
function render_exercise(int $exercise, ?array $formData = null): string
{
    $_SERVER['REQUEST_METHOD'] = $formData === null ? 'GET' : 'POST';
    $_SERVER['SCRIPT_NAME'] = '/BTTH9/bt' . $exercise . '.php';
    $_POST = $formData ?? [];
    if ($formData !== null) {
        $_POST['csrf'] = csrf_token();
    }
    ob_start();
    try {
        require __DIR__ . '/../bt' . $exercise . '.php';
        return (string) ob_get_contents();
    } finally {
        ob_end_clean();
    }
}

$cases = [
    [1, ['a' => '9', 'b' => '4', 'pheptinh' => '+'], '9 + 4 = 13'],
    [1, ['a' => '9', 'b' => '4', 'pheptinh' => '-'], '9 - 4 = 5'],
    [1, ['a' => '9', 'b' => '4', 'pheptinh' => '*'], '9 * 4 = 36'],
    [1, ['a' => '9', 'b' => '4', 'pheptinh' => '/'], '9 / 4 = 2.25'],
    [1, ['a' => '9', 'b' => '4', 'pheptinh' => '%'], '9 % 4 = 1'],
    [1, ['a' => '1', 'b' => '0', 'pheptinh' => '/'], 'Không thể chia cho 0'],
    [1, ['a' => '1', 'b' => '0', 'pheptinh' => '%'], 'Không thể chia cho 0'],
    [1, ['a' => '2.5', 'b' => '2', 'pheptinh' => '%'], 'yêu cầu hai số nguyên'],
    [1, ['a' => '1', 'b' => '2', 'pheptinh' => '^'], 'Phép toán không hợp lệ'],
    [2, ['a' => '2', 'b' => '-4'], 'x = 2.'],
    [2, ['a' => '0', 'b' => '0'], 'vô số nghiệm'],
    [2, ['a' => '0', 'b' => '3'], 'vô nghiệm'],
    [3, ['a' => '2', 'b' => '3', 'c' => '-5'], 'x₁ = 1; x₂ = -2.5'],
    [3, ['a' => '1', 'b' => '2', 'c' => '1'], 'nghiệm kép x = -1'],
    [3, ['a' => '1', 'b' => '0', 'c' => '1'], 'vô nghiệm'],
    [3, ['a' => '0', 'b' => '2', 'c' => '-4'], 'x = 2.'],
    [3, ['a' => '0', 'b' => '0', 'c' => '0'], 'vô số nghiệm'],
    [3, ['a' => '0', 'b' => '0', 'c' => '1'], 'vô nghiệm'],
    [3, ['a' => '1', 'b' => '2', 'c' => '0'], 'x₁ = 0; x₂ = -2'],
    [4, ['hinh' => 'tron', 'r' => '1'], 'Diện tích = 3.14159265359'],
    [4, ['hinh' => 'tamgiac', 'day' => '3', 'chieucao' => '4'], 'Diện tích = 6'],
    [4, ['hinh' => 'chunhat', 'dai' => '3', 'rong' => '4'], 'Diện tích = 12'],
    [4, ['hinh' => 'tron', 'r' => '-1'], 'Bán kính phải lớn hơn 0'],
    [4, ['hinh' => 'tron', 'r' => ['1']], 'Bán kính phải là một số hợp lệ'],
    [4, ['hinh' => 'tron', 'r' => '1e999'], 'Bán kính không được vượt quá'],
    [4, ['hinh' => 'tamgiac', 'day' => '3', 'chieucao' => '0'], 'Đáy và chiều cao phải lớn hơn 0'],
    [4, ['hinh' => 'chunhat', 'dai' => '1000000000001', 'rong' => '2'], 'Kích thước không được vượt quá'],
    [4, ['hinh' => 'tron', 'r' => '1', 'day' => ['sai']], 'Diện tích = 3.14159265359'],
    [4, ['hinh' => 'khac'], 'Hãy chọn một hình hợp lệ'],
    [5, ['a' => '3', 'b' => '4', 'c' => '5'], 'tam giác vuông.'],
    [5, ['a' => '2', 'b' => '2', 'c' => '2'], 'tam giác đều.'],
    [5, ['a' => '2', 'b' => '2', 'c' => '3'], 'tam giác cân.'],
    [5, ['a' => '1', 'b' => '1', 'c' => (string) sqrt(2)], 'tam giác vuông cân.'],
    [5, ['a' => '4', 'b' => '5', 'c' => '6'], 'tam giác thường.'],
    [5, ['a' => '1', 'b' => '2', 'c' => '3'], 'không tạo thành một tam giác'],
    [5, ['a' => '-1', 'b' => '2', 'c' => '2'], 'không tạo thành một tam giác'],
    [6, ['thang' => '2', 'nam' => '2024'], 'có 29 ngày'],
    [6, ['thang' => '2', 'nam' => '1900'], 'có 28 ngày'],
    [6, ['thang' => '2', 'nam' => '2000'], 'có 29 ngày'],
    [6, ['thang' => '4', 'nam' => '2026'], 'có 30 ngày'],
    [6, ['thang' => '12', 'nam' => '2026'], 'có 31 ngày'],
    [6, ['thang' => '0', 'nam' => '2026'], 'Không có tháng này'],
    [6, ['thang' => '13', 'nam' => '2026'], 'Không có tháng này'],
    [8, ['n' => '1'], '1 × 10'],
    [8, ['n' => '10'], '10 × 10'],
    [8, ['n' => '11'], 'phải từ 1 đến 10'],
    [8, ['n' => '2.5'], 'phải là số nguyên'],
];
foreach ($cases as [$exercise, $data, $expected]) {
    check(str_contains(render_exercise($exercise, $data), $expected), 'Bài ' . $exercise . ': ' . $expected);
}
foreach ([-5, 0, 1, 4, 9, 25, 100] as $n) {
    check(str_contains(render_exercise(9, ['n' => (string) $n]), "$n không phải là số nguyên tố."), "$n không nguyên tố");
}
foreach ([2, 3, 5, 97, 104729] as $n) {
    check(str_contains(render_exercise(9, ['n' => (string) $n]), "$n là số nguyên tố."), "$n nguyên tố");
}
check(substr_count(render_exercise(7), '<caption>') === 9, 'Đủ bảng cửu chương từ 2 đến 10');
check(str_contains(render_exercise(10), date('l jS \o\f F Y')), 'Ngày tiếng Anh theo múi giờ Việt Nam');
check(str_contains(render_exercise(2, ['a' => '<script>alert(1)</script>', 'b' => '1']), '&lt;script&gt;'), 'Escape dữ liệu nhập khi hiển thị lại form');
rejects(fn () => read_number(['a' => ''], 'a', 'a'), 'Chặn số rỗng');
rejects(fn () => read_number(['a' => '1e999'], 'a', 'a'), 'Chặn số vô hạn');
rejects(fn () => number_text(INF), 'Không xuất nghiệm vô hạn do tràn số');
rejects(fn () => read_number(['a' => ['2']], 'a', 'a'), 'Chặn tham số mảng');
rejects(fn () => read_integer(['n' => '2.5'], 'n', 'n', 1, 10), 'Chặn số thập phân');
rejects(fn () => read_integer(['n' => '11'], 'n', 'n', 1, 10), 'Bảng cửu chương tối đa 10');
check(read_integer(['n' => '01'], 'n', 'n', 1, 10) === 1, 'Chấp nhận số 0 ở đầu');
echo "PASS: $checks kiểm tra trực tiếp các file bt1.php–bt10.php và dữ liệu đầu vào.\n";
