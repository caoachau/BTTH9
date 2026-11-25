# Kiểm chứng Phần 2 trong phpMyAdmin

## Mở đúng CSDL của bài

Mở **http://localhost/phpmyadmin/index.php?route=/database/structure&server=2&db=online_shop**.

Trên máy hiện tại, phpMyAdmin đã chọn mặc định máy chủ **BTTH9 - online_shop (3306)**, dùng MySQL do XAMPP Control Panel quản lý. Chọn **online_shop** trong cây thư mục bên trái. Nếu trang còn hiển thị cổng 3307, tải lại trang.

phpMyAdmin kết nối MySQL bằng `root`, mật khẩu rỗng; đây là tài khoản kết nối CSDL. `admin`, `admin2`, `user01`, `user02` là tài khoản của ứng dụng PHP, đăng nhập trên trang BTTH9.

Nếu chưa kết nối được, mở XAMPP Control Panel và bấm **Start** ở **Apache** và **MySQL**. Không cần chạy script PowerShell.

## Đối chiếu từng yêu cầu trong hai ảnh

| Yêu cầu | Xem ở đâu trong phpMyAdmin | Kết quả cần thấy |
| --- | --- | --- |
| Tên CSDL | Cây bên trái và thanh đường dẫn phía trên | `online_shop` |
| Bốn bảng | Chọn CSDL → **Structure / Cấu trúc** | `nguoi_dung`, `admin`, `loai`, `sach` |
| Cột tài khoản theo PDF | `nguoi_dung` → **Structure** | `username VARCHAR(20)` khóa chính; `password TEXT`; `hoten VARCHAR(50)`; `gioitinh BIT(2)`; `quocgia VARCHAR(30)`; `hinhdaidien TEXT` |
| Bảng quản trị theo ảnh | `admin` → **Structure** | `useradmin VARCHAR(20)` khóa chính, `passadmin TEXT` |
| Loại sách | `loai` → **Structure** | `maloai VARCHAR(5)` khóa chính, `tenloai VARCHAR(100)` |
| Tám cột sách | `sach` → **Structure** | `idsach INT`, `tensach VARCHAR(100)`, `hinhsach TEXT`, `mota TEXT`, `gia INT`, `soluong INT`, `maloai VARCHAR(5)`, `nxb VARCHAR(100)` |
| Mã sách tự tăng | Dòng `idsach` trong Structure | Khóa chính và ô **A_I** được chọn / Extra ghi `AUTO_INCREMENT` |
| Khóa ngoại | `sach` → **Structure → Relation view / Xem quan hệ**, hoặc tab SQL chạy `SHOW CREATE TABLE sach;` | `fk_sach_loai`: `sach.maloai` tham chiếu `loai.maloai` |
| Hai người dùng mẫu | `nguoi_dung` → **Browse / Duyệt** | `user01`, `user02` |
| Hai quản trị viên | `admin` → **Browse** | `admin`, `admin2` |
| Năm loại sách | `loai` → **Browse** | `ML001` Công nghệ thông tin; `ML002` Kinh tế; `ML003` Du lịch; `ML004` Văn học; `ML005` Phát triển bản thân |
| Mười sách | `sach` → **Browse** | 10 dòng mẫu với tên sách, giá, số lượng, loại và NXB |
| Chỉ lưu đường dẫn ảnh | Cột `hinhsach` trong bảng `sach` | Ví dụ `hinhanh/144571.jpg`; không lưu dữ liệu ảnh vào CSDL |
| Mật khẩu MD5 | Tab **SQL**, dùng truy vấn MD5 ở cuối file kiểm chứng | `do_dai_md5 = 32`, `dung_md5 = 1` cho bốn tài khoản mẫu |

MySQL có thể hiển thị `INT(11)` trong Structure; đây là kiểu `INT` trong yêu cầu. Số 11 là độ rộng hiển thị của phiên bản MariaDB này.

## Chạy các truy vấn kiểm chứng

Mở file [database/verify.sql](../database/verify.sql), chép nội dung vào tab **SQL** của CSDL `online_shop` rồi nhấn **Go / Thực hiện**. Toàn bộ câu lệnh chỉ đọc dữ liệu và cấu trúc. Có thể chạy từng phần được đánh số để dễ đối chiếu.

Hai câu đầu để xác nhận đúng kết nối:

```sql
USE online_shop;
SELECT DATABASE() AS ten_csdl, @@port AS cong_mysql;
SHOW TABLES;
```

Kết quả trên máy này: `online_shop`, cổng `3306`, bốn bảng như trên.

Đếm số dòng chính xác:

```sql
SELECT 'nguoi_dung' AS bang, COUNT(*) AS so_dong FROM nguoi_dung
UNION ALL SELECT 'admin', COUNT(*) FROM admin
UNION ALL SELECT 'loai', COUNT(*) FROM loai
UNION ALL SELECT 'sach', COUNT(*) FROM sach;
```

Lúc kiểm tra hiện tại, kết quả là **3 người dùng, 2 quản trị, 5 loại, 10 sách**. Hai người dùng mẫu `user01`, `user02` vẫn có đủ; tài khoản `Chau` được đăng ký thêm sau đó và được giữ nguyên. Số người dùng sẽ tiếp tục tăng khi thực hành chức năng đăng ký.

Ví dụ xác nhận mật khẩu mẫu của `user01` đúng MD5:

```sql
SELECT username, CHAR_LENGTH(password) AS do_dai_md5,
       password = MD5('User01@2026') AS dung_md5
FROM nguoi_dung WHERE username = 'user01';
```

Kết quả cần thấy: `user01`, `32`, `1`.

## Vì sao thêm bằng phpMyAdmin mà mật khẩu vẫn hiện nguyên văn?

Cột kiểu `TEXT` chỉ lưu giá trị được đưa vào, không tự chạy MD5. Khi thêm tài khoản trực tiếp trong phpMyAdmin, code của trang đăng ký PHP không được chạy.

- Với bảng `admin`, chọn **Insert / Chèn** hoặc **Edit / Sửa**; tại dòng `passadmin`, chọn **Function = MD5**, rồi nhập mật khẩu gốc vào **Value / Giá trị** và nhấn **Go / Thực hiện**.
- Nếu gõ SQL trực tiếp, dùng ví dụ `INSERT INTO admin (useradmin, passadmin) VALUES ('admin_moi', MD5('MatKhau@2026'));`.
- Khi đăng ký trên `dangky.php`, PHP đã gọi `md5()` trước `INSERT`, nhưng tài khoản được thêm vào bảng **`nguoi_dung`**, không phải `admin`.

Chỉ chọn MD5 khi ô giá trị đang là mật khẩu gốc. Nếu đã là chuỗi MD5 thì không hash lại lần thứ hai. Khi đăng nhập, nhập mật khẩu gốc, không nhập chuỗi MD5.

Ảnh thực tế có thể mở tại **http://localhost/BTTH9/hinhanh/144571.jpg**. Danh sách sách có ảnh, lọc theo loại ở **http://localhost/BTTH9/danhsach_sach.php**. Đăng ký, đăng nhập thành viên, đăng nhập quản trị và sửa/xóa được kiểm tra trên ứng dụng; phpMyAdmin dùng để quan sát các bản ghi trước/sau thao tác.

## Cấu hình phpMyAdmin trên máy này

Kết nối thứ hai đã được thêm vào `F:\xampp\phpMyAdmin\config.inc.php`, giữ kết nối XAMPP cũ tại 3306. Bản cấu hình trước thay đổi được sao lưu trong `.runtime/phpmyadmin-config.before-btth9.php.bak` của repo.

Phiên bản phpMyAdmin cài sẵn phát thông báo deprecated trên PHP 8.4. Hai điểm vào `F:\xampp\phpMyAdmin\index.php` và `F:\xampp\phpMyAdmin\js\messages.php` được bổ sung `error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED)` trước khi tải thư viện để chỉ bỏ qua nhóm thông báo deprecated; lỗi và cảnh báo khác vẫn được giữ. File `js/messages.php` sinh JavaScript: nếu thông báo PHP bị chèn vào phản hồi thì trình duyệt báo `Unexpected token '<'` / `Messages is not defined` và các nút phpMyAdmin không hoạt động.

Bản trước thay đổi được sao lưu tại `.runtime/phpmyadmin-index.before-php84.php.bak` và `.runtime/phpmyadmin-messages.before-php84.php.bak`. Cấu hình báo lỗi của ứng dụng BTTH9 không thay đổi. Sau khi sửa, cần **Ctrl + F5** trên trang phpMyAdmin để bỏ file JavaScript cũ đã được cache. Có thể mở đường dẫn ngắn **http://localhost/phpmyadmin/**, chọn **BTTH9 - online_shop (3306)** rồi chọn CSDL `online_shop`; đường dẫn ngắn không cần tham số có dấu `&`.
