
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin` (
  `useradmin` varchar(20) NOT NULL,
  `passadmin` text NOT NULL,
  PRIMARY KEY (`useradmin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `admin` WRITE;
/*!40000 ALTER TABLE `admin` DISABLE KEYS */;
INSERT INTO `admin` VALUES ('admin','2dbd9c3dd7974c71e843fdad7727333f'),('admin2','69a639053f9f215ee10d1bf1fb68f1cf'),('admin20','dd4b21e9ef71e1291183a46b913ae6f2'),('admin3','d27d320c27c3033b7883347d8beca317'),('admin6','bae5e3208a3c700e3db642b6631e95b9');
/*!40000 ALTER TABLE `admin` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `loai`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `loai` (
  `maloai` varchar(5) NOT NULL,
  `tenloai` varchar(100) NOT NULL,
  PRIMARY KEY (`maloai`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `loai` WRITE;
/*!40000 ALTER TABLE `loai` DISABLE KEYS */;
INSERT INTO `loai` VALUES ('ML001','Công nghệ thông tin'),('ML002','Kinh tế'),('ML003','Du lịch'),('ML004','Văn học'),('ML005','Phát triển bản thân');
/*!40000 ALTER TABLE `loai` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `nguoi_dung`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nguoi_dung` (
  `username` varchar(20) NOT NULL,
  `password` text NOT NULL,
  `hoten` varchar(50) NOT NULL,
  `gioitinh` bit(2) NOT NULL,
  `quocgia` varchar(30) NOT NULL,
  `hinhdaidien` text NOT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `nguoi_dung` WRITE;
/*!40000 ALTER TABLE `nguoi_dung` DISABLE KEYS */;
INSERT INTO `nguoi_dung` VALUES ('Chau','c1f68ec06b490b3ecb4066b1b13a9ee9','Chau Cao','\0','vn','storage/avatars/ff28968e27bb3e41ae41f1821ad00dfb.png'),('user01','fc97f92fbba55d0c3ef4716953900a1a','Nguyễn Văn An','\0','vn',''),('user02','b3d29fd5dc67030ba484bba6e90d6817','Trần Thị Bình','','vn','');
/*!40000 ALTER TABLE `nguoi_dung` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `sach`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sach` (
  `idsach` int(11) NOT NULL AUTO_INCREMENT,
  `tensach` varchar(100) NOT NULL,
  `hinhsach` text NOT NULL,
  `mota` text NOT NULL,
  `gia` int(11) NOT NULL,
  `soluong` int(11) NOT NULL,
  `maloai` varchar(5) NOT NULL,
  `nxb` varchar(100) NOT NULL,
  PRIMARY KEY (`idsach`),
  KEY `fk_sach_loai` (`maloai`),
  CONSTRAINT `fk_sach_loai` FOREIGN KEY (`maloai`) REFERENCES `loai` (`maloai`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `sach` WRITE;
/*!40000 ALTER TABLE `sach` DISABLE KEYS */;
INSERT INTO `sach` VALUES (1,'Vòng quanh thế giới - Các nước Đông Âu','hinhanh/136452.jpg','Tìm hiểu các điểm đến tại Đông Âu. Dữ liệu thực hành.',65000,20,'ML003','NXB Tổng hợp'),(2,'Du lịch thế giới - Châu Á','hinhanh/139075.jpg','Khám phá các điểm đến tại châu Á. Dữ liệu thực hành.',75000,15,'ML003','NXB Tổng hợp'),(3,'Du lịch thế giới - Châu Âu','hinhanh/139076.jpg','Khám phá các điểm đến tại châu Âu. Dữ liệu thực hành.',85000,12,'ML003','NXB Tổng hợp'),(4,'Các phương pháp thi công xây dựng','hinhanh/143801.jpg','Tham khảo phương pháp tổ chức thi công và hoạt động xây dựng. Dữ liệu thực hành.',120000,10,'ML002','NXB Xây dựng'),(5,'Mộng đời bất tuyệt','hinhanh/144220.jpg','Tác phẩm văn học của Nguyễn Tường Bách. Dữ liệu thực hành.',68000,18,'ML004','NXB Văn học'),(6,'Tự học lập trình C# thông qua các ví dụ','hinhanh/144337.jpg','Làm quen lập trình C# qua các ví dụ. Dữ liệu thực hành.',95000,25,'ML001','NXB Thống kê'),(7,'Tự học sửa chữa Windows XP','hinhanh/144343.jpg','Tài liệu tham khảo thao tác và sửa lỗi Windows XP. Dữ liệu thực hành.',55000,8,'ML001','NXB Thống kê'),(8,'Vượt qua thử thách','hinhanh/144349.jpg','Sách tham khảo về phát triển bản thân. Dữ liệu thực hành.',72000,16,'ML005','NXB Trẻ'),(9,'Tự học JavaScript','hinhanh/144571.jpg','Làm quen ngôn ngữ JavaScript. Dữ liệu thực hành.',89000,22,'ML001','NXB Thống kê'),(10,'Kỹ thuật số - Thực hành ứng dụng','hinhanh/73900.jpg','Tài liệu kỹ thuật số và thực hành ứng dụng. Dữ liệu thực hành.',110000,14,'ML001','NXB Khoa học và Kỹ thuật');
/*!40000 ALTER TABLE `sach` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

