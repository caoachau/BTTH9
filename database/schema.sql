-- Chọn CSDL online_shop trong phpMyAdmin trước khi nhập file này.
-- Kiểu dữ liệu và tên cột theo hai ảnh yêu cầu Phần 2.
CREATE TABLE IF NOT EXISTS nguoi_dung (
    username VARCHAR(20) NOT NULL,
    password TEXT NOT NULL,
    hoten VARCHAR(50) NOT NULL,
    gioitinh BIT(2) NOT NULL,
    quocgia VARCHAR(30) NOT NULL,
    hinhdaidien TEXT NOT NULL,
    PRIMARY KEY (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS admin (
    useradmin VARCHAR(20) NOT NULL,
    passadmin TEXT NOT NULL,
    PRIMARY KEY (useradmin)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS loai (
    maloai VARCHAR(5) NOT NULL,
    tenloai VARCHAR(100) NOT NULL,
    PRIMARY KEY (maloai)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sach (
    idsach INT NOT NULL AUTO_INCREMENT,
    tensach VARCHAR(100) NOT NULL,
    hinhsach TEXT NOT NULL,
    mota TEXT NOT NULL,
    gia INT NOT NULL,
    soluong INT NOT NULL,
    maloai VARCHAR(5) NOT NULL,
    nxb VARCHAR(100) NOT NULL,
    PRIMARY KEY (idsach),
    CONSTRAINT fk_sach_loai FOREIGN KEY (maloai) REFERENCES loai(maloai)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
