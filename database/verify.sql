-- Chi doc du lieu de doi chieu voi hai anh yeu cau Phan 2.
USE online_shop;

-- 1. Ten CSDL va cong MySQL dang xem.
SELECT DATABASE() AS ten_csdl, @@port AS cong_mysql;

-- 2. Du bon bang.
SHOW TABLES;

-- 3. Ten cot, kieu du lieu, khoa chinh, AUTO_INCREMENT.
SELECT TABLE_NAME AS bang, COLUMN_NAME AS cot, COLUMN_TYPE AS kieu_du_lieu,
       COLUMN_KEY AS khoa, EXTRA AS bo_sung
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
ORDER BY TABLE_NAME, ORDINAL_POSITION;

-- 4. Khoa ngoai sach.maloai -> loai.maloai.
SELECT TABLE_NAME AS bang, COLUMN_NAME AS cot, CONSTRAINT_NAME AS ten_rang_buoc,
       REFERENCED_TABLE_NAME AS bang_tham_chieu,
       REFERENCED_COLUMN_NAME AS cot_tham_chieu
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = DATABASE() AND REFERENCED_TABLE_NAME IS NOT NULL;

-- 5. So dong thuc te. nguoi_dung co the tang khi dang ky them thanh vien.
SELECT 'nguoi_dung' AS bang, COUNT(*) AS so_dong FROM nguoi_dung
UNION ALL SELECT 'admin', COUNT(*) FROM admin
UNION ALL SELECT 'loai', COUNT(*) FROM loai
UNION ALL SELECT 'sach', COUNT(*) FROM sach;

-- 6. Hai nguoi dung mau van co mat, ke ca khi da dang ky them tai khoan.
SELECT username, hoten FROM nguoi_dung
WHERE username IN ('user01', 'user02') ORDER BY username;

-- 7. Dung nam ma loai va ten loai theo anh.
SELECT maloai, tenloai FROM loai ORDER BY maloai;

-- 8. Muoi sach, ma tu tang va duong dan anh trong thu muc hinhanh.
SELECT idsach, tensach, hinhsach, mota, gia, soluong, maloai, nxb
FROM sach ORDER BY idsach;

-- 9. Ket qua dung_md5 = 1 nghia la khop MD5 cua mat khau mau.
SELECT username, CHAR_LENGTH(password) AS do_dai_md5,
       password = CASE username
           WHEN 'user01' THEN MD5('User01@2026')
           WHEN 'user02' THEN MD5('User02@2026')
       END AS dung_md5
FROM nguoi_dung WHERE username IN ('user01', 'user02') ORDER BY username;

SELECT useradmin, CHAR_LENGTH(passadmin) AS do_dai_md5,
       passadmin = CASE useradmin
           WHEN 'admin' THEN MD5('Btth9@2026')
           WHEN 'admin2' THEN MD5('Admin2@2026')
       END AS dung_md5
FROM admin WHERE useradmin IN ('admin', 'admin2') ORDER BY useradmin;
