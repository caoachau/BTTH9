# Đọc code từ đâu nếu mới học PHP?

## Bắt đầu với ba file này

1. **`bt2.php`**: ít ô nhập, chỉ dùng `if/else` để giải `ax + b = 0`.
2. **`bt4.php`**: có biến tiếng Việt và chú thích BƯỚC 1–5; đọc một nhánh hình tròn trước, rồi đọc tam giác và chữ nhật.
3. **`dangnhap.php` → `xuly_dangnhap.php`**: xem form gửi dữ liệu đi đâu và PHP đọc dữ liệu đó để đăng nhập như thế nào.

Mỗi lần học một file. Bạn chưa cần đọc `scripts/setup.php`, `scripts/xampp-mysql.php` hay các file `tests/` để hiểu bài tập; đó là công cụ cài đặt và kiểm tra.

## Bài 4: đọc từ form đến kết quả

Ở cuối `bt4.php`, ô nhập bán kính có:

```html
<input name="r" type="number">
```

`name="r"` là tên của dữ liệu được gửi đi. Form dùng `method="post"`, vì vậy PHP lấy dữ liệu bằng:

```php
$banKinh = $_POST['r'] ?? '';
```

Đọc dòng này là: lấy ô có tên `r`, đưa vào biến `$banKinh`; nếu chưa có dữ liệu thì dùng chuỗi rỗng `''`.

Radio chọn hình tròn có `name="hinh" value="tron"`. Khi bạn chọn nó, PHP nhận:

```php
$hinh = $_POST['hinh'] ?? '';
```

Sau đó chọn công thức:

```php
if ($hinh === 'tron') {
    // Tính diện tích hình tròn.
} elseif ($hinh === 'tamgiac') {
    // Tính diện tích tam giác.
} elseif ($hinh === 'chunhat') {
    // Tính diện tích chữ nhật.
}
```

Trong nhánh hình tròn, phần tính chính là:

```php
$dienTich = pi() * $banKinh * $banKinh;
```

Ví dụ nhập bán kính `2` thì tính `π × 2 × 2`, kết quả xấp xỉ `12.5663706144`. Các dòng `is_numeric()` và kiểm tra lớn hơn 0 nằm trước công thức để tránh tính khi ô nhập sai.

Kết quả được đưa vào `$ketQua`, rồi in bên dưới form. Trong `bt4.php`, bạn có thể tìm lần lượt các chữ **BƯỚC 1**, **BƯỚC 2**… để đọc theo thứ tự.

## Một số ký hiệu thường gặp

| Code | Cách hiểu |
| --- | --- |
| `$banKinh = 2;` | Gán số 2 vào biến `$banKinh` |
| `$_POST['r']` | Dữ liệu của ô có `name="r"` khi gửi form bằng POST |
| `?? ''` | Nếu dữ liệu chưa có hoặc là null thì dùng chuỗi rỗng |
| `if (...) { ... }` | Nếu điều kiện đúng thì làm phần trong ngoặc nhọn |
| `else` | Làm khi điều kiện trước không đúng |
| `===` | So sánh cả giá trị và kiểu dữ liệu |
| `||` | Hoặc: chỉ cần một điều kiện đúng |
| `&&` | Và: các điều kiện đều phải đúng |
| `!` | Phủ định; ví dụ `!is_numeric($a)` nghĩa là `$a` không phải số |
| `.` | Nối chuỗi, như `'Xin chào ' . $ten` |
| `(float)` | Chuyển giá trị sang số có phần thập phân |
| `<?= e($ketQua) ?>` | In kết quả ra HTML; `e()` giúp văn bản được hiển thị an toàn |

Trong các bài khác, `$result` nghĩa là kết quả, `$error` nghĩa là lỗi. Bài 4 dùng tên `$ketQua`, `$loi` để bạn dễ bắt đầu.

## Đăng nhập: năm bước giống luồng của code mẫu

Trong `dangnhap.php`:

```html
<form action="xuly_dangnhap.php" method="post">
```

Nghĩa là: bấm Đăng nhập sẽ gửi dữ liệu sang `xuly_dangnhap.php`.

File xử lý đã được chú thích theo năm bước:

1. Lấy tên đăng nhập và mật khẩu bằng `$_POST`.
2. Kiểm tra ô nhập có hợp lệ không.
3. Tìm người dùng trong bảng `nguoi_dung` bằng `SELECT`.
4. Kiểm tra mật khẩu có khớp không.
5. Lưu tên người dùng vào session và chuyển sang `trangtt_canhan.php`.

Các dòng mysqli cần hiểu ở bước 3:

```php
$sql = 'SELECT * FROM nguoi_dung WHERE username = ?';
$truyVan = mysqli_prepare($kn, $sql);
mysqli_stmt_bind_param($truyVan, 's', $tenDangNhap);
mysqli_stmt_execute($truyVan);
$ketQua = mysqli_stmt_get_result($truyVan);
$nguoiDung = mysqli_fetch_assoc($ketQua);
```

- `mysqli_prepare()`: chuẩn bị câu SQL. Dấu `?` là vị trí sẽ nhận tên đăng nhập.
- `mysqli_stmt_bind_param()`: đưa `$tenDangNhap` vào dấu `?`; chữ `s` nghĩa là dữ liệu chuỗi, chữ `i` dùng cho số nguyên.
- `mysqli_stmt_execute()`: chạy truy vấn đã chuẩn bị.
- `mysqli_stmt_get_result()`: lấy tập kết quả của truy vấn.
- `mysqli_fetch_assoc()`: lấy một dòng theo tên cột. Đọc tên bằng `$nguoiDung['username']`, đọc mật khẩu đã lưu bằng `$nguoiDung['password']`. Không tìm thấy dòng nào thì kết quả là `null`.

Sau khi đăng nhập đúng:

```php
$_SESSION['nguoidung'] = $nguoiDung['username'];
```

Session giúp các trang tiếp theo biết ai đang đăng nhập. File `thoat.php` xóa giá trị này khi đăng xuất.

## Vì sao không chép nguyên các hàm trong PDF Phần 2?

Luồng và tên trang theo tài liệu, nhưng những hàm `mysql_connect()`, `mysql_query()`, `mysql_fetch_array()` đã không còn trong PHP của XAMPP đang dùng. Bản này dùng `mysqli` để viết gần mẫu và chạy được trên PHP hiện tại. `mysqli` là phần mở rộng của PHP, không phải framework.

| Trong code mẫu cũ | Trong repo này |
| --- | --- |
| `mysql_connect(...)` | `mysqli_connect(...)` |
| `mysql_select_db('online_shop', $kn)` | `mysqli_select_db($kn, 'online_shop')` — đổi thứ tự tham số |
| `mysql_query(...)` | `mysqli_query($kn, $sql)`; với dữ liệu nhập từ form dùng `mysqli_prepare()` và gắn tham số |
| `mysql_fetch_array(...)` | `mysqli_fetch_assoc(...)` để lấy một dòng theo tên cột |
| `$_POST[...]` | Vẫn nhận dữ liệu từ form bằng `$_POST` |
| `$_SESSION[...]` | Vẫn ghi nhớ người đăng nhập bằng session |
| Mật khẩu MD5 theo hai ảnh | Vẫn dùng `md5()`; tên cột và kiểu `TEXT` theo ảnh |

Phần 1 trong tài liệu chủ yếu là đề bài và ảnh giao diện. Các thuật toán nằm trực tiếp trong `bt1.php`–`bt10.php`; bài 3 dùng delta và công thức nghiệm thông thường.

## Phân biệt online_shop, tên bảng và dấu *

```php
mysqli_select_db($kn, 'online_shop');
$ketQua = mysqli_query($kn, 'SELECT * FROM nguoi_dung');
```

- `online_shop`: tên cơ sở dữ liệu cần chọn.
- `nguoi_dung`: tên bảng trong cơ sở dữ liệu đó.
- `*`: lấy tất cả các cột của bảng, ví dụ `username`, `password`, `hoten`…

Không thay tên CSDL `online_shop` bằng `*`. Bạn chỉ có thể viết `SELECT *` thay cho việc liệt kê từng cột trong câu SELECT. Trong `ketnoi.php`, `$config['db_name']` đang mang giá trị `online_shop` đọc từ file cấu hình.

## Các hàm hỗ trợ có thể đọc sau

| Hàm | Mục đích |
| --- | --- |
| `read_number()` / `read_integer()` | Lấy dữ liệu và kiểm tra số ở các bài khác |
| `old_input()` | Giữ lại giá trị trong ô sau khi bấm tính |
| `number_text()` | Định dạng số trước khi in ra |
| `csrf_field()` / `verify_post()` | Kiểm tra biểu mẫu gửi về đúng phiên đang dùng |
| `flash()` / `redirect()` | Lưu thông báo và chuyển sang trang khác |
| `validate_user()` | Kiểm tra các ô đăng ký/sửa người dùng |
| `store_avatar()` | Kiểm tra, lưu file ảnh và trả về đường dẫn ảnh |
| `password_matches()` | Kiểm tra mật khẩu với dữ liệu đã lưu |

`try { ... } catch (...) { ... }` nghĩa là: chạy các bước trong `try`; nếu có lỗi thì xử lý ở `catch`. Ví dụ đăng ký trùng tên sẽ quay lại form kèm thông báo. Khi mới đọc, hãy theo dõi luồng đăng ký thành công từ BƯỚC 1–5 trước, rồi xem cách thông báo lỗi.
