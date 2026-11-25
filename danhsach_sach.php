<?php
require_once __DIR__ . '/includes/database.php';
try {
    $selectedCategory = input_text($_GET, 'loai');
} catch (InvalidArgumentException $exception) {
    abort_page(400, $exception->getMessage());
}
$ketQuaLoai = mysqli_query($kn, 'SELECT maloai, tenloai FROM loai ORDER BY maloai');
$categories = mysqli_fetch_all($ketQuaLoai, MYSQLI_ASSOC);
if ($selectedCategory !== '' && !in_array($selectedCategory, array_column($categories, 'maloai'), true)) {
    abort_page(404, 'Không tìm thấy loại sách này.');
}
$sql = 'SELECT s.*, l.tenloai FROM sach s JOIN loai l ON l.maloai = s.maloai'
    . ($selectedCategory !== '' ? ' WHERE s.maloai = ?' : '') . ' ORDER BY s.idsach';
$statement = execute_sql($sql, $selectedCategory !== '' ? [$selectedCategory] : []);
$ketQuaSach = mysqli_stmt_get_result($statement);
$books = mysqli_fetch_all($ketQuaSach, MYSQLI_ASSOC);
$title = 'Danh sách sách';
require __DIR__ . '/header.php';
?>
<h1>Danh sách sách</h1>
<form method="get">
    <label for="book-category">Loại sách</label>
    <select id="book-category" name="loai">
        <option value="">Tất cả loại sách</option>
        <?php foreach ($categories as $category): ?>
            <option value="<?= e($category['maloai']) ?>" <?= $selectedCategory === $category['maloai'] ? 'selected' : '' ?>><?= e($category['maloai'] . ' — ' . $category['tenloai']) ?></option>
        <?php endforeach; ?>
    </select>
    <div class="actions"><button type="submit">Lọc sách</button></div>
</form>
<p><?= count($books) ?> cuốn sách</p>
<?php if (!$books): ?><p>Chưa có sách trong loại này.</p><?php endif; ?>
<div class="book-list">
<?php foreach ($books as $book): ?>
    <article class="book">
        <?php if (preg_match('~^hinhanh/[a-zA-Z0-9_-]+\.(jpg|png|gif|webp)$~D', $book['hinhsach'])): ?>
            <img src="<?= e($book['hinhsach']) ?>" width="80" height="110" alt="Bìa sách <?= e($book['tensach']) ?>" loading="lazy">
        <?php endif; ?>
        <div>
            <h2><?= e($book['tensach']) ?></h2>
            <p><strong><?= number_format((int) $book['gia'], 0, ',', '.') ?> đ</strong> · Còn <?= (int) $book['soluong'] ?> cuốn</p>
            <p class="hint">Mã sách: <?= (int) $book['idsach'] ?> · <?= e($book['tenloai']) ?> · <?= e($book['nxb']) ?></p>
            <p><?= e($book['mota']) ?></p>
        </div>
    </article>
<?php endforeach; ?>
</div>
<?php require __DIR__ . '/footer.php'; ?>
