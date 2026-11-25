        </main>
        <aside class="sidebar account">
            <h2>Tài khoản</h2>
            <?php if (isset($_SESSION['nguoidung'])): ?>
                <p>Xin chào <strong><?= e($_SESSION['nguoidung']) ?></strong></p>
                <a href="trangtt_canhan.php">Thông tin của bạn</a>
                <form action="thoat.php" method="post"><?php csrf_field(); ?><button type="submit">Đăng xuất thành viên</button></form>
            <?php else: ?>
                <form action="xuly_dangnhap.php" method="post">
                    <?php csrf_field(); ?>
                    <label for="side-user">Tên đăng nhập</label>
                    <input id="side-user" name="tendn" maxlength="20" autocomplete="username" required>
                    <label for="side-password">Mật khẩu</label>
                    <input id="side-password" name="matkhau" type="password" autocomplete="current-password" required>
                    <button type="submit">Đăng nhập</button>
                </form>
                <a href="dangky.php">Đăng ký thành viên</a>
            <?php endif; ?>
            <?php if (isset($_SESSION['nguoiquantri'])): ?>
                <p>Quản trị: <strong><?= e($_SESSION['nguoiquantri']) ?></strong></p>
                <form action="thoatQT.php" method="post"><?php csrf_field(); ?><button type="submit">Đăng xuất quản trị</button></form>
            <?php endif; ?>
        </aside>
    </div>
    <footer class="site-footer">
        <small>sachhay.com — TrucMaiPham</small>
    </footer>
</div>
</body>
</html>
