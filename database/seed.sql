-- Dữ liệu thực hành theo ảnh: 2 người dùng, 2 quản trị, 5 loại, 10 sách.
-- Mật khẩu chọn Function MD5 khi nhập trong phpMyAdmin tương đương MD5() dưới đây.
-- Không ghi đè các tài khoản hoặc loại sách đã tồn tại.
INSERT IGNORE INTO nguoi_dung (username, password, hoten, gioitinh, quocgia, hinhdaidien) VALUES
('user01', MD5('User01@2026'), 'Nguyễn Văn An', 0, 'vn', ''),
('user02', MD5('User02@2026'), 'Trần Thị Bình', 1, 'vn', '');

INSERT IGNORE INTO admin (useradmin, passadmin) VALUES
('admin', MD5('Btth9@2026')),
('admin2', MD5('Admin2@2026'));

INSERT IGNORE INTO loai (maloai, tenloai) VALUES
('ML001', 'Công nghệ thông tin'),
('ML002', 'Kinh tế'),
('ML003', 'Du lịch'),
('ML004', 'Văn học'),
('ML005', 'Phát triển bản thân');

-- Không truyền idsach: MySQL tự tăng theo yêu cầu của ảnh.
-- Giá, số lượng, mô tả và NXB dưới đây là dữ liệu mẫu cho bài thực hành.
-- Khi chạy lại, bỏ qua sách có đường dẫn bìa đã nhập.
INSERT INTO sach (tensach, hinhsach, mota, gia, soluong, maloai, nxb)
SELECT mau.tensach, mau.hinhsach, mau.mota, mau.gia, mau.soluong, mau.maloai, mau.nxb
FROM (
    SELECT 'Vòng quanh thế giới - Các nước Đông Âu' AS tensach,
        'hinhanh/136452.jpg' AS hinhsach, 'Tìm hiểu các điểm đến tại Đông Âu. Dữ liệu thực hành.' AS mota,
        65000 AS gia, 20 AS soluong, 'ML003' AS maloai, 'NXB Tổng hợp' AS nxb
    UNION ALL SELECT 'Du lịch thế giới - Châu Á', 'hinhanh/139075.jpg', 'Khám phá các điểm đến tại châu Á. Dữ liệu thực hành.', 75000, 15, 'ML003', 'NXB Tổng hợp'
    UNION ALL SELECT 'Du lịch thế giới - Châu Âu', 'hinhanh/139076.jpg', 'Khám phá các điểm đến tại châu Âu. Dữ liệu thực hành.', 85000, 12, 'ML003', 'NXB Tổng hợp'
    UNION ALL SELECT 'Các phương pháp thi công xây dựng', 'hinhanh/143801.jpg', 'Tham khảo phương pháp tổ chức thi công và hoạt động xây dựng. Dữ liệu thực hành.', 120000, 10, 'ML002', 'NXB Xây dựng'
    UNION ALL SELECT 'Mộng đời bất tuyệt', 'hinhanh/144220.jpg', 'Tác phẩm văn học của Nguyễn Tường Bách. Dữ liệu thực hành.', 68000, 18, 'ML004', 'NXB Văn học'
    UNION ALL SELECT 'Tự học lập trình C# thông qua các ví dụ', 'hinhanh/144337.jpg', 'Làm quen lập trình C# qua các ví dụ. Dữ liệu thực hành.', 95000, 25, 'ML001', 'NXB Thống kê'
    UNION ALL SELECT 'Tự học sửa chữa Windows XP', 'hinhanh/144343.jpg', 'Tài liệu tham khảo thao tác và sửa lỗi Windows XP. Dữ liệu thực hành.', 55000, 8, 'ML001', 'NXB Thống kê'
    UNION ALL SELECT 'Vượt qua thử thách', 'hinhanh/144349.jpg', 'Sách tham khảo về phát triển bản thân. Dữ liệu thực hành.', 72000, 16, 'ML005', 'NXB Trẻ'
    UNION ALL SELECT 'Tự học JavaScript', 'hinhanh/144571.jpg', 'Làm quen ngôn ngữ JavaScript. Dữ liệu thực hành.', 89000, 22, 'ML001', 'NXB Thống kê'
    UNION ALL SELECT 'Kỹ thuật số - Thực hành ứng dụng', 'hinhanh/73900.jpg', 'Tài liệu kỹ thuật số và thực hành ứng dụng. Dữ liệu thực hành.', 110000, 14, 'ML001', 'NXB Khoa học và Kỹ thuật'
) AS mau
WHERE NOT EXISTS (SELECT 1 FROM sach AS s WHERE s.hinhsach = mau.hinhsach);
