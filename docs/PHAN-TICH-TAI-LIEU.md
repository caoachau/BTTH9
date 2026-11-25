# Phân tích tài liệu và phạm vi triển khai

## Thứ tự thực hiện

Yêu cầu của người dùng: repo chỉ dùng PHP thuần, làm Phần 1 và Phần 2 trước, phân loại các file còn lại và để bài mở rộng/dự án khác cho giai đoạn sau. Môi trường chạy là XAMPP.

Người dùng bổ sung hai ảnh yêu cầu Phần 2: tên cột/kiểu dữ liệu, bốn bảng và dữ liệu mẫu MD5. Các yêu cầu cụ thể trong hai ảnh được áp dụng cho Phần 2. Hướng dẫn cài WAMP 2.0i, lời gọi `mysql_*` và ví dụ nối chuỗi SQL trong PDF được thay bằng XAMPP, các hàm `mysqli_*` và truy vấn có tham số.

## Đối chiếu sáu file

| Tài liệu | Nội dung đã xác minh | Quan hệ với repo | Trạng thái |
| --- | --- | --- | --- |
| `PHAN 1 - php.doc` | 10 bài PHP căn bản; ảnh mẫu giao diện xanh, bố cục ba cột | Phần nền tảng | Đã thực hiện đủ 10 bài |
| `PHAN 2 - php va SQL.pdf` | 13 trang, 15 bước: tạo bảng, kết nối, đăng ký, tải ảnh, đăng nhập, hồ sơ, quản trị, sửa/xóa, đăng xuất | Dùng lại giao diện Phần 1, bổ sung CSDL và session | Đã thực hiện |
| `giao dien_new.zip` | `header.php`, `footer.php`, `css.css`, banner và ảnh sách; menu có BT1–BT10 và PHP–MySQL | Liên quan trực tiếp cả hai phần; đồng thời dự kiến cả các bài bổ sung | Đã giải nén vào `reference/giao dien_new`; dùng banner và dựng lại layout hợp lệ, liên kết hoạt động |
| `hinhanh.zip` | Bìa sách, icon và banner; nhiều ảnh trùng với ZIP giao diện | Dùng trực tiếp cho 10 sách mẫu trong hai ảnh bổ sung Phần 2 | Đã giải nén vào `reference/hinhanh`; sao chép 10 bìa vào thư mục `hinhanh` ở root và lưu đường dẫn vào CSDL |
| `BT quan ly ban sach VERSION 6.pdf` | 8 trang dạng ảnh, đọc trực tiếp sau khi mở bằng mật khẩu được cung cấp; giỏ hàng, chi tiết giỏ, mua sách, tồn kho, hủy và quản trị duyệt đơn | Phần mở rộng hệ thống thành viên của Phần 2; không phải ứng dụng tách biệt hoàn toàn | Đã phân tích; chưa triển khai theo thứ tự ưu tiên của người dùng |
| `battapthem_chattructuyen_download_phienbanmoi-ver2.pdf` | File có mã hóa, mật khẩu được cung cấp không mở được; người dùng xác nhận không có mật khẩu khác | Tên file và menu ZIP gợi ý bài chat và tải sản phẩm dùng chung thành viên, nhưng chưa xác minh nội dung chi tiết | Chờ bản có thể đọc; không tự suy diễn chức năng hoặc báo đã hoàn thành |

## Chi tiết Phần 1

- BT1 có cả phép `%` dù ảnh chụp ví dụ chỉ có bốn nút. Chặn chia/lấy dư cho 0; `%` yêu cầu số nguyên.
- BT2 xét đủ một nghiệm, vô nghiệm và vô số nghiệm.
- BT3 xét cả a = 0, nghiệm kép, hai nghiệm và vô nghiệm thực. Ví dụ trong phần chữ của đề `a = 2, b = 3, c = -5` cho `x₁ = 1`, `x₂ = -2.5`; ảnh ví dụ có dấu không nhất quán nên dùng phương trình trong phần chữ.
- BT4 dùng radio chọn hình, tính theo bán kính / đáy và chiều cao / chiều dài và chiều rộng. Chỉ kiểm tra kích thước của hình đang chọn.
- BT5 kiểm tra cạnh dương, bất đẳng thức tam giác; phân loại đều, cân, vuông, vuông cân và thường.
- BT6 kiểm tra tháng 1–12 và cho nhập năm để phân biệt 28/29 ngày tháng 2.
- BT7 xuất bảng 2–10; BT8 nhận số nguyên dương 1–10.
- BT9 coi số nhỏ hơn 2 là không nguyên tố và giới hạn kích thước đầu vào.
- BT10 dùng định dạng tiếng Anh có hậu tố ngày và múi giờ `Asia/Ho_Chi_Minh`.

## Chi tiết Phần 2

| Bước trong PDF | File/chức năng triển khai |
| --- | --- |
| 1 | `database/schema.sql`: bảng `nguoi_dung`; bảng `admin`, `loai`, `sach` theo hai ảnh bổ sung |
| 2 | `ketnoi.php`: `mysqli_connect()` → `mysqli_select_db()` → `mysqli_set_charset()`, theo trình tự trong mẫu |
| 3–4 | `dangky.php`, `xlydangky.php`: kiểm tra dữ liệu, tên trùng, tải ảnh và đăng nhập sau đăng ký |
| 5 | `trangtt_canhan.php`: chỉ người đã đăng nhập được xem hồ sơ của mình |
| 6–8 | `thoat.php`, `dangnhap.php`, `xuly_dangnhap.php` |
| 9–10 | `dangnhapQT.php`, `xuly_dangnhapQT.php`: tài khoản quản trị tách khỏi thành viên |
| 11 | `quantri.php`: danh sách thành viên, liên kết sửa/xóa; không công khai mật khẩu |
| 12–13 | `sua.php`, `thuchien_sua.php`: giữ tên đăng nhập, sửa thông tin, tùy chọn đổi mật khẩu/ảnh |
| 14 | `xoa.php`: kiểm tra quản trị, xác nhận, POST có CSRF, xóa tài khoản và ảnh |
| 15 | `thoatQT.php`: kết thúc phiên quản trị |

Các điều chỉnh cần thiết: `password`/`passadmin` có kiểu `TEXT`, mật khẩu dùng MD5 theo hai ảnh; bảng `admin` có đúng hai cột `useradmin`, `passadmin`; dùng InnoDB và utf8mb4; quốc gia thống nhất mã `vn/cn/jp/k`; dùng đúng session quản trị; redirect có dừng xử lý; không nhận đường dẫn ảnh cũ từ input ẩn. Thêm công cụ CLI `scripts/setup.php` để khởi tạo CSDL và dữ liệu mẫu, không mở trang tạo admin cho khách.

## Hai ảnh yêu cầu bổ sung Phần 2

Ảnh gốc được giữ tại [ảnh 1](../reference/phan2-yeucau-1.jpg) và [ảnh 2](../reference/phan2-yeucau-2.jpg).

| Yêu cầu trên bảng | Cách thực hiện |
| --- | --- |
| Tạo `online_shop`; `nguoi_dung` theo PDF, đổi `password` sang `TEXT` | `scripts/setup.php` tạo CSDL; schema giữ cột theo PDF, dùng `password TEXT` |
| `loai`: `maloai VARCHAR(5)` khóa chính, `tenloai VARCHAR(100)` | Định nghĩa trong `database/schema.sql` |
| `sach`: `idsach INT` khóa chính tự tăng; `tensach VARCHAR(100)`; `hinhsach`, `mota` kiểu `TEXT`; `gia`, `soluong` kiểu `INT`; `maloai VARCHAR(5)` khóa ngoại; `nxb VARCHAR(100)` | Định nghĩa đầy đủ với FK `sach.maloai` → `loai.maloai` |
| `admin`: `useradmin VARCHAR(20)` khóa chính; `passadmin TEXT` | Dùng đúng tên cột trong schema và các trang xử lý đăng nhập/quản trị |
| 2 người dùng, 2 quản trị viên, mật khẩu chọn Function MD5 | `database/seed.sql` có `user01`, `user02`, `admin`, `admin2`, dùng hàm SQL `MD5()` |
| 5 loại `ML001`–`ML005` | Công nghệ thông tin; Kinh tế; Du lịch; Văn học; Phát triển bản thân |
| 10 sách, không nhập `idsach` | Seed chèn 10 sách, để AUTO_INCREMENT sinh mã |
| `hinhsach` chỉ lưu đường dẫn tới ảnh trong thư mục `hinhanh` | Lưu dạng `hinhanh/136452.jpg`, ảnh nằm cạnh `index.php` trong thư mục `hinhanh` |

`danhsach_sach.php` hiển thị dữ liệu để kiểm tra trực quan, có lọc theo loại. Giá, số lượng, NXB và mô tả là giá trị minh họa vì ảnh yêu cầu không cung cấp danh sách cụ thể. Script cập nhật schema cũ bằng ALTER/đổi tên cột, giữ bản ghi; seed chạy lại không thêm trùng sách hoặc đặt lại mật khẩu đã thay đổi. Các hash cũ vẫn đăng nhập được; các lần ghi mật khẩu hiện tại dùng MD5.

Các mục BT11 biển số đẹp, chat, download, đọc/ghi file và bài giáo trình xuất hiện trong ZIP giao diện nhưng không nằm trong Phần 1 hoặc Phần 2. Menu đang chạy chỉ có các bài đã thực hiện.

## Giai đoạn sau: bán sách

Đề bán sách cho thấy cần dùng lại bảng tài khoản, bổ sung dữ liệu sách, `giohang`, `chitiet_giohang`, chức năng mua, xem/hủy giỏ hàng chưa duyệt, danh sách giỏ chờ và duyệt đơn bằng quản trị. `hinhanh.zip` là tài nguyên bìa sách tương ứng.

Hai ảnh mới đã bổ sung schema `sach` và `loai` cho Phần 2. Đề bán sách cũ dùng một số tên cột khác như `id_sach`, `ten_sach`, `so_luong`, `tac_gia`; khi làm giai đoạn sau cần ánh xạ sang `idsach`, `tensach`, `soluong` hoặc bổ sung trường còn thiếu theo chức năng. Luồng mua hàng và cập nhật tồn kho cần transaction; bản thiết kế sau sẽ tránh tạo/xóa bảng tạm riêng theo tên từng người dùng như ví dụ cũ.

## Ghi nhận môi trường XAMPP hiện tại

Đã cấu hình đường dẫn `F:\xampp\htdocs\BTTH9` trỏ về repo, Apache cổng 80. Tạo CSDL `online_shop`; các CSDL `shopbansach`, `shopbansach_symfony` và CSDL khác có sẵn không được sửa bởi phần triển khai này.

Bộ dữ liệu cũ của XAMPP từng có lỗi InnoDB nên trước đây repo dùng bộ dữ liệu `F:\xampp\mysql-btth9-data` ở cổng 3307 bằng script. Hiện đã cấu hình **`F:\xampp\mysql\bin\my.ini`** để XAMPP Control Panel dùng chính bộ dữ liệu đang hoạt động này tại cổng mặc định **3306**. `config.local.php` và phpMyAdmin cùng kết nối cổng 3306. Đã xuất SQL sao lưu và đối chiếu dữ liệu không đổi trước/sau khi chuyển. Thư mục dữ liệu cũ không bị thay thế; các CSDL khác ở đó chưa được nhập vào máy chủ hiện tại.

Đã kiểm tra cài mới trên bộ dữ liệu riêng, nhập mẫu lặp lại không tăng số bản ghi, đăng nhập 4 tài khoản mẫu và toàn bộ luồng đăng ký/upload/sửa/xóa. Có kiểm tra giới tính 0/1 khi gắn tham số số nguyên vào cột `BIT(2)`, đáp ứng chế độ SQL nghiêm ngặt của bản cài mới.
