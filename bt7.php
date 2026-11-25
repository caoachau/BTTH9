<?php
require_once __DIR__ . '/includes/bootstrap.php';
$title = 'Bài 7 — Bảng cửu chương từ 2 đến 10';
require __DIR__ . '/header.php';
?>
<h1><?= e($title) ?></h1>
<!-- Vòng lặp ngoài chọn bảng; vòng lặp trong in 10 phép nhân của bảng đó. -->
<div class="tables">
    <?php for ($table = 2; $table <= 10; $table++): ?>
        <table>
            <caption>Bảng cửu chương <?= $table ?></caption>
            <tbody>
            <?php for ($i = 1; $i <= 10; $i++): ?>
                <tr><td><?= $table ?> × <?= $i ?></td><td>= <?= $table * $i ?></td></tr>
            <?php endfor; ?>
            </tbody>
        </table>
    <?php endfor; ?>
</div>
<?php require __DIR__ . '/footer.php'; ?>
