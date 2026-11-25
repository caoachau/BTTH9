# BTTH9 — PHP thuần trên XAMPP

Đã thực hiện **Phần 1** (10 bài PHP căn bản) và **Phần 2** theo PDF và **hai ảnh yêu cầu bổ sung**: tài khoản, ảnh đại diện, quản trị người dùng, loại sách và 10 sách mẫu. Ứng dụng dùng PHP, HTML/CSS và MySQL/MariaDB; không cần framework, Composer, Node.js hoặc bước build.

## Code của từng bài nằm ở đâu?

Nếu mới học PHP, đọc [hướng dẫn bắt đầu và giải thích từng bước](docs/HOC-TU-DAU.md). Nên bắt đầu với `bt2.php`, `bt4.php`, sau đó đọc cặp `dangnhap.php` → `xuly_dangnhap.php`.

Mở trực tiếp các file PHP ở thư mục gốc. Mỗi bài có phần xử lý PHP ở đầu file, tiếp theo là HTML/form và phần xuất kết quả. Các file này chứa code thực hiện bài, không chuyển sang một file xử lý tổng theo số bài.

| File | Nội dung code |
| --- | --- |
| `bt1.php`–`bt10.php` | Thuật toán, nhận dữ liệu và giao diện của từng bài Phần 1 |
| `dangky.php` → `xlydangky.php` | Form đăng ký → kiểm tra, lưu thành viên/ảnh và chuyển sang hồ sơ |
| `dangnhap.php` → `xuly_dangnhap.php` | Form đăng nhập → truy vấn bảng `nguoi_dung`, kiểm tra mật khẩu, lưu session |
| `dangnhapQT.php` → `xuly_dangnhapQT.php` | Form đăng nhập quản trị → truy vấn bảng `admin`, kiểm tra mật khẩu, lưu session quản trị |
| `quantri.php`, `sua.php`, `thuchien_sua.php`, `xoa.php` | Danh sách thành viên, form sửa, lệnh cập nhật, xác nhận và lệnh xóa |
| `thoat.php`, `thoatQT.php` | Xử lý đăng xuất và form xác nhận của từng vai trò |
| `trangtt_canhan.php`, `danhsach_sach.php` | Trang hồ sơ thành viên và danh mục/lọc sách |
| `ketnoi.php`, `config.php`, `config.local.php` | Kết nối mysqli và thông tin CSDL |

Các phần dùng chung còn lại:

- `header.php`, `footer.php`: khung giao diện và menu.
- `includes/bootstrap.php`: session, thông báo, mã CSRF và hàm xuất HTML an toàn.
- `includes/database.php`: hàm chạy SQL dùng chung; `ketnoi.php` chỉ có phần kết nối CSDL.
- `includes/numbers.php`: kiểm tra dữ liệu số, định dạng kết quả và giữ giá trị vừa nhập; thuật toán nằm trong từng `bt*.php`.
- `includes/users.php`: các hàm được nhiều trang tài khoản sử dụng, như kiểm tra quyền/dữ liệu, đọc người dùng, kiểm tra mật khẩu và lưu/xóa ảnh.
- `database/`: SQL tạo bảng, dữ liệu mẫu và truy vấn kiểm chứng.
- `scripts/`: công cụ khởi tạo/cập nhật CSDL và hỗ trợ MySQL. Không cần đọc các file này để hiểu code bài tập.
- `tests/`: các chương trình kiểm tra, không phải trang web của bài.

## Chạy trên máy hiện tại

1. Mở XAMPP Control Panel tại `F:\xampp\xampp-control.exe`.
2. Bấm **Start** ở **Apache** và **MySQL**. Nếu đang hiện **Stop** thì dịch vụ đã bật. Không cần chạy lệnh PowerShell; CSDL và dữ liệu đã có sẵn.
3. Mở **http://localhost/BTTH9/**.
4. Đăng nhập quản trị tại `dangnhapQT.php`, thành viên tại `dangnhap.php` bằng các tài khoản mẫu bên dưới.
5. Mở `danhsach_sach.php` để xem 10 sách, lọc theo 5 loại và kiểm tra ảnh bìa.
6. Tạo thêm tài khoản tại `dangky.php` để thử tải ảnh đại diện và xem thông tin cá nhân.

| Vai trò | Tên đăng nhập | Mật khẩu mẫu |
| --- | --- | --- |
| Người dùng | `user01` | `User01@2026` |
| Người dùng | `user02` | `User02@2026` |
| Quản trị | `admin` | `Btth9@2026` |
| Quản trị | `admin2` | `Admin2@2026` |

`F:\xampp\htdocs\BTTH9` là junction trỏ đến thư mục repo này. Sửa code trong repo là cập nhật ngay trang trên localhost. CSDL của bài là `online_shop`, MySQL/MariaDB đi kèm XAMPP chạy tại **127.0.0.1:3306**, `root`, mật khẩu rỗng; cấu hình trong `config.local.php`.

Bộ dữ liệu cũ tại `F:\xampp\mysql\data` từng báo lỗi InnoDB. Hiện file **`F:\xampp\mysql\bin\my.ini`** đã được cấu hình để nút MySQL trong Control Panel dùng bộ dữ liệu đang hoạt động tại **`F:\xampp\mysql-btth9-data`**, cổng mặc định **3306**. CSDL và tài khoản được giữ nguyên; dữ liệu cũ vẫn nằm ở thư mục cũ. Các CSDL khác trong thư mục cũ sẽ chưa xuất hiện trên máy chủ này. Cấu hình trước khi đổi và bản xuất SQL của `online_shop` được lưu trong `.runtime/xampp-panel-*`.

## Xem và kiểm chứng bằng phpMyAdmin

Mở **http://localhost/phpmyadmin/index.php?route=/database/structure&server=2&db=online_shop**. Chọn máy chủ **BTTH9 - online_shop (3306)** và CSDL **online_shop**. Dùng tab **Structure / Cấu trúc** để xem tên cột, kiểu dữ liệu, khóa và tự tăng; dùng **Browse / Duyệt** để xem dữ liệu mẫu. Xem [hướng dẫn đối chiếu từng yêu cầu](docs/KIEM-CHUNG-PHAN-2.md).

Cũng có thể mở đường dẫn ngắn **http://localhost/phpmyadmin/**. Nếu trang đã mở trước khi sửa lỗi tương thích PHP 8.4, bấm **Ctrl + F5** để tải lại JavaScript và các nút thao tác.

File [database/verify.sql](database/verify.sql) có các truy vấn chỉ đọc để xác nhận tên CSDL/cổng, bốn bảng, khóa ngoại, số dòng, loại sách, đường dẫn ảnh và MD5. Chép từng truy vấn vào tab **SQL** để chạy. Nếu đăng ký thêm thành viên thì tổng số người dùng tăng; hai tài khoản mẫu `user01`, `user02` vẫn được giữ.

## Cài lại trên XAMPP khác

Yêu cầu PHP **8.1 trở lên**, MySQL/MariaDB và các extension `mysqli`, `mbstring`, `fileinfo`. XAMPP hiện tại đã có đủ; `curl` dùng cho bài kiểm tra tích hợp.

1. Đặt repo trong `<XAMPP>\htdocs\BTTH9`, hoặc tạo junction từ đường dẫn đó đến repo.
2. Bật Apache và MySQL trong XAMPP.
3. Với MySQL XAMPP bình thường, sao chép `config.local.example.php` thành `config.local.php` để dùng cổng 3306, hoặc sửa theo cấu hình máy. Các biến môi trường `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` cũng được hỗ trợ; `config.local.php` có ưu tiên cao hơn.
4. Mở PowerShell tại repo và chạy:

   ```powershell
   & 'F:\xampp\php\php.exe' scripts/setup.php --seed-demo
   ```

   Đổi đường dẫn PHP theo nơi cài XAMPP. Script tạo CSDL, **4 bảng**, **2 người dùng, 2 quản trị, 5 loại, 10 sách** theo ảnh. Chạy lại bỏ qua dữ liệu mẫu đã có, không nhân đôi sách và không đặt lại mật khẩu đã đổi. Script cũng cập nhật tên cột/kiểu dữ liệu của bản Phần 2 trước khi bổ sung hai ảnh.

5. Mở `http://localhost/BTTH9/`.

Cách nhập SQL thủ công trên CSDL mới: tạo `online_shop` với collation `utf8mb4_unicode_ci` trong phpMyAdmin, chọn CSDL đó rồi import lần lượt **`database/schema.sql`** và **`database/seed.sql`**. Các câu `MD5('mật khẩu')` trong seed tương đương chọn Function MD5 khi nhập trên phpMyAdmin. Với CSDL của bản cũ đã chạy, dùng script để đổi cột trước khi nhập mẫu.

Nếu chỉ muốn tạo một quản trị với mật khẩu tự chọn, chạy `php scripts/setup.php --admin=ten_quan_tri` rồi nhập mật khẩu khi được hỏi; ký tự nhập sẽ hiển thị trong cửa sổ dòng lệnh. Không có trang đăng ký quản trị công khai.

Có thể truyền `--database=ten_csdl_rieng` cho công cụ setup khi cần kiểm thử cài đặt trong một CSDL khác; ứng dụng vẫn đọc cấu hình riêng ở `config.php`/`config.local.php`.

`scripts/xampp-mysql.php` và `config.xampp-btth9.example.php` là công cụ dự phòng cho trường hợp cần tách bộ dữ liệu, không cần dùng khi bật MySQL bằng XAMPP Control Panel. Không chép cấu hình ví dụ cổng 3307 đè lên cấu hình máy hiện tại đang dùng cổng 3306. Script nhận ra cấu hình Control Panel trỏ tới dữ liệu BTTH9 và dùng đúng cổng đó.

Phần 1 chạy được ngay cả khi MySQL chưa bật. Phần 2 cần khởi tạo CSDL trước. Thư mục `storage/avatars` cần quyền ghi; ảnh được đọc qua `avatar.php`, truy cập trực tiếp vào thư mục lưu trữ bị chặn.

## Các bài đã làm

| Phần 1 | File |
| --- | --- |
| Máy tính cộng, trừ, nhân, chia, lấy dư | `bt1.php` |
| Phương trình bậc I, kể cả a = 0 | `bt2.php` |
| Phương trình bậc II và các trường hợp suy biến | `bt3.php` |
| Diện tích hình tròn, tam giác, chữ nhật; chọn bằng radio | `bt4.php` |
| Kiểm tra và phân loại tam giác | `bt5.php` |
| Số ngày trong tháng, có xử lý năm nhuận | `bt6.php` |
| Bảng cửu chương từ 2 đến 10 | `bt7.php` |
| Bảng cửu chương theo số nguyên dương ≤ 10 | `bt8.php` |
| Kiểm tra số nguyên tố | `bt9.php` |
| Ngày hiện tại bằng tiếng Anh, múi giờ Việt Nam | `bt10.php` |

Phần 2 giữ tên file theo đề: `ketnoi.php`, `dangky.php`, `xlydangky.php`, `trangtt_canhan.php`, `thoat.php`, `dangnhap.php`, `xuly_dangnhap.php`, `dangnhapQT.php`, `xuly_dangnhapQT.php`, `quantri.php`, `sua.php`, `thuchien_sua.php`, `xoa.php`, `thoatQT.php`.

Theo yêu cầu trong ảnh, **`nguoi_dung.password` và `admin.passadmin` có kiểu `TEXT`**, tài khoản mẫu và các lần đăng ký/đổi mật khẩu lưu **MD5**. Đăng nhập vẫn đọc được hash của tài khoản đã tạo ở bản trước. Code dùng `mysqli_connect`, `mysqli_select_db`, `mysqli_query`, `mysqli_fetch_assoc` và prepared statement của mysqli cho các giá trị người dùng nhập. Các thao tác thay đổi dữ liệu kiểm tra CSRF và quyền đăng nhập. Trang quản trị không xuất mật khẩu hoặc chuỗi hash. Khi sửa, để trống mật khẩu/không chọn ảnh mới sẽ giữ dữ liệu hiện tại. Xóa cần xác nhận bằng POST và xóa cả file ảnh liên quan.

Các bảng trong CSDL:

| Bảng | Cột và quan hệ theo ảnh |
| --- | --- |
| `nguoi_dung` | Các cột theo PDF; `password` đổi sang `TEXT` |
| `admin` | `useradmin VARCHAR(20)` khóa chính, `passadmin TEXT` |
| `loai` | `maloai VARCHAR(5)` khóa chính, `tenloai VARCHAR(100)` |
| `sach` | `idsach INT AUTO_INCREMENT` khóa chính; `tensach VARCHAR(100)`, `hinhsach TEXT`, `mota TEXT`, `gia INT`, `soluong INT`, `maloai VARCHAR(5)` khóa ngoại tới `loai`, `nxb VARCHAR(100)` |

Năm loại: `ML001` Công nghệ thông tin, `ML002` Kinh tế, `ML003` Du lịch, `ML004` Văn học, `ML005` Phát triển bản thân. `idsach` do MySQL tự tăng, không nhập trong dữ liệu mẫu. `hinhsach` chỉ lưu đường dẫn tương đối như **`hinhanh/144571.jpg`**. File ảnh nằm trong thư mục `hinhanh` cùng cấp `index.php`, đúng ví dụ trên bảng. Giá, số lượng, mô tả và NXB là dữ liệu minh họa cho bài thực hành.

Ảnh đại diện là tùy chọn, định dạng JPG/PNG/GIF/WebP, tối đa **1.000.000 byte**, tối đa **4096 × 4096 pixel**. Tên file trên máy chủ được tạo ngẫu nhiên; CSDL chỉ lưu đường dẫn. Bài tính toán nhận số có giá trị tuyệt đối tối đa `10^12` để tránh yêu cầu xử lý quá lớn.

## Quan hệ với các tài liệu còn lại

Xem [phân tích đầy đủ](docs/PHAN-TICH-TAI-LIEU.md). `giao dien_new.zip` phục vụ trực tiếp Phần 1–2. `hinhanh.zip` được dùng cho dữ liệu sách Phần 2 theo hai ảnh mới. Đề bán sách mở rộng thành giỏ hàng và duyệt đơn, để triển khai sau. PDF chat/download hiện chưa đọc được vì mật khẩu đã cung cấp không mở được file này.

## Kiểm tra

```powershell
& 'F:\xampp\php\php.exe' tests/run.php
& 'F:\xampp\php\php.exe' tests/requirements.php
& 'F:\xampp\php\php.exe' tests/integration.php
```

`tests/run.php` chạy trực tiếp các file `bt1.php`–`bt10.php` với dữ liệu kiểm tra và đọc kết quả HTML, không cần MySQL. `tests/requirements.php` đối chiếu tên cột, kiểu dữ liệu, khóa chính/ngoại, MD5, 5 loại và 10 ảnh sách với hai ảnh yêu cầu. `tests/integration.php` kiểm tra HTTP, đăng nhập cả 4 tài khoản mẫu, danh mục/lọc sách, ảnh bìa, session, upload, phân quyền và sửa/xóa. Hai bài kiểm tra sau cần nhập dữ liệu mẫu và giữ mật khẩu mẫu; bài HTTP tạo tài khoản thử ngẫu nhiên và tự dọn chúng sau khi chạy. Có thể đặt `TEST_BASE_URL` nếu URL khác `http://localhost/BTTH9/`.

Các thư mục `includes`, `database`, `scripts`, `tests`, `docs`, `reference`, `storage` được chặn HTTP bằng `.htaccess` trên Apache. Chạy qua XAMPP để có đầy đủ cấu hình này.
