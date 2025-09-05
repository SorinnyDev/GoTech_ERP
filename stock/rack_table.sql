-- --------------------------------------------------------
-- 호스트:                          127.0.0.1
-- 서버 버전:                        10.4.32-MariaDB - mariadb.org binary distribution
-- 서버 OS:                        Win64
-- HeidiSQL 버전:                  12.11.0.7065
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- wave38_gotech 데이터베이스 구조 내보내기
CREATE DATABASE IF NOT EXISTS `wave38_gotech` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `wave38_gotech`;

-- 테이블 wave38_gotech.g5_rack_expired 구조 내보내기
CREATE TABLE IF NOT EXISTS `g5_rack_expired` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `warehouse` int(11) DEFAULT NULL,
  `rack_id` int(11) NOT NULL DEFAULT 0,
  `stock` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `sales3_id` int(11) DEFAULT NULL,
  `expired_date` date DEFAULT NULL,
  `reg_datetime` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `g5_rack_expired_id_index` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=213 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 내보낼 데이터가 선택되어 있지 않습니다.

-- 테이블 wave38_gotech.g5_rack_stock 구조 내보내기
CREATE TABLE IF NOT EXISTS `g5_rack_stock` (
  `seq` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `wr_warehouse` varchar(50) NOT NULL DEFAULT '',
  `wr_rack` varchar(50) NOT NULL DEFAULT '',
  `wr_stock` int(11) NOT NULL DEFAULT 0,
  `wr_product_id` int(11) NOT NULL DEFAULT 0,
  `wr_sales1_id` int(11) DEFAULT 0 COMMENT 'g5_sales0_list -> seq',
  `wr_sales2_id` int(11) DEFAULT 0 COMMENT 'g5_sales1_list -> seq',
  `wr_sales3_id` int(11) NOT NULL DEFAULT 0 COMMENT '출고등록 seq',
  `wr_sales4_id` int(11) DEFAULT 0 COMMENT 'g5_sales2_list -> seq',
  `wr_mb_id` varchar(50) NOT NULL DEFAULT '',
  `wr_expired_date` date DEFAULT NULL,
  `wr_datetime` datetime NOT NULL,
  `wr_move_log` text NOT NULL,
  PRIMARY KEY (`seq`),
  KEY `wr_warehouse` (`wr_warehouse`),
  KEY `wr_product_id` (`wr_product_id`),
  KEY `wr_rack` (`wr_rack`),
  KEY `wr_sales3_id` (`wr_sales3_id`)
) ENGINE=InnoDB AUTO_INCREMENT=368801 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- 내보낼 데이터가 선택되어 있지 않습니다.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
