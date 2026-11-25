<?php
declare(strict_types=1);

// Cập nhật schema của bản Phần 2 trước khi có hai ảnh bổ sung.
// Chỉ thay kiểu dữ liệu/đổi tên cột; giữ nguyên bản ghi và mật khẩu hiện có.
function upgrade_part2_schema(mysqli $connection): void
{
    $result = mysqli_query($connection, 'SHOW COLUMNS FROM admin');
    $columns = array_column(mysqli_fetch_all($result, MYSQLI_ASSOC), 'Field');
    if (in_array('user_admin', $columns, true) && !in_array('useradmin', $columns, true)) {
        mysqli_query($connection, 'ALTER TABLE admin CHANGE COLUMN user_admin useradmin VARCHAR(20) NOT NULL');
    }
    if (in_array('pass_admin', $columns, true) && !in_array('passadmin', $columns, true)) {
        mysqli_query($connection, 'ALTER TABLE admin CHANGE COLUMN pass_admin passadmin TEXT NOT NULL');
    }
    mysqli_query($connection, 'ALTER TABLE admin MODIFY COLUMN passadmin TEXT NOT NULL');
    mysqli_query($connection, 'ALTER TABLE nguoi_dung MODIFY COLUMN password TEXT NOT NULL, MODIFY COLUMN gioitinh BIT(2) NOT NULL, MODIFY COLUMN hinhdaidien TEXT NOT NULL');
}
