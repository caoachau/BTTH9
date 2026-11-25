<?php
require_once __DIR__ . '/includes/bootstrap.php';
$exerciseTitles = [1 => 'Máy tính', 'Phương trình bậc I', 'Phương trình bậc II', 'Tính diện tích',
    'Phân loại tam giác', 'Số ngày trong tháng', 'Bảng cửu chương 2–10', 'Bảng cửu chương tùy chọn',
    'Kiểm tra số nguyên tố', 'Ngày tháng hiện tại'];
$currentPage = basename($_SERVER['SCRIPT_NAME'] ?? '');
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Bài thực hành PHP') ?> · BTTH9</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="site">
    <header class="banner"><a href="index.php" aria-label="Về trang chủ"><img src="assets/banner.gif" alt="Tri thức — cùng bạn đi tìm tri thức" width="1000" height="200"></a></header>
    <div class="topbar"><a href="index.php">Trang chủ</a><span>Cùng bạn đi tìm tri thức!</span></div>
    <div class="columns">
        <nav class="sidebar" aria-label="Danh sách bài tập">
            <h2>Bài tập PHP căn bản</h2>
            <?php foreach ($exerciseTitles as $number => $label): ?>
                <a href="bt<?= $number ?>.php" <?= $currentPage === "bt$number.php" ? 'aria-current="page"' : '' ?>>BT<?= $number ?> — <?= e($label) ?></a>
            <?php endforeach; ?>
            <h2>Bài tập PHP–MySQL</h2>
            <a href="dangky.php">Đăng ký thành viên</a>
            <a href="dangnhap.php">Đăng nhập thành viên</a>
            <a href="trangtt_canhan.php">Thông tin cá nhân</a>
            <a href="dangnhapQT.php">Đăng nhập quản trị</a>
            <a href="quantri.php">Quản lý người dùng</a>
            <a href="danhsach_sach.php">Danh sách sách</a>
        </nav>
        <main id="content">
            <?php if (isset($_SESSION['flash'])): $notice = $_SESSION['flash']; unset($_SESSION['flash']); ?>
                <div class="notice <?= e($notice['type']) ?>" role="status"><?= e($notice['message']) ?></div>
            <?php endif; ?>
