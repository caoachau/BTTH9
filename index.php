<?php
require_once __DIR__ . '/includes/bootstrap.php';
$title = 'Trang chủ';
require __DIR__ . '/header.php';
?>
<h1>Bài thực hành PHP</h1>
<p>Chọn bài trong menu để bắt đầu thực hành.</p>
<h2>Phần 1 · PHP căn bản</h2>
<p>10 bài tập về phép tính, phương trình, hình học, vòng lặp, số nguyên tố và ngày tháng.</p>
<p><a class="button" href="bt1.php">Bắt đầu bài 1</a></p>
<h2>Phần 2 · PHP và MySQL</h2>
<p>Đăng ký tài khoản, tải ảnh đại diện, đăng nhập, xem thông tin cá nhân và quản lý thành viên.</p>
<p><a href="dangky.php">Đăng ký thành viên</a> · <a href="dangnhapQT.php">Đăng nhập quản trị</a></p>
<h2>Danh mục sách</h2>
<p>Xem 10 cuốn sách mẫu thuộc 5 loại sách theo yêu cầu Phần 2.</p>
<p><a href="danhsach_sach.php">Xem danh sách sách</a></p>
<?php require __DIR__ . '/footer.php'; ?>
