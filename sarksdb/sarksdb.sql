-- Fixed SQL Dump for sarksdb
-- Optimized for MySQL 8.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- Database: `sarksdb`
CREATE DATABASE IF NOT EXISTS `sarksdb` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `sarksdb`;

-- Table structure for `admin`
CREATE TABLE `admin` (
  `adId` int(11) NOT NULL AUTO_INCREMENT,
  `aUserName` varchar(40) NOT NULL,
  `aPassword` varchar(255) NOT NULL,
  `aMobile` varchar(40) NOT NULL,
  PRIMARY KEY (`adId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `admin` (`adId`, `aUserName`, `aPassword`, `aMobile`) VALUES
(1, 'admin', SHA2('123', 256), '');

-- Table structure for `customer`
CREATE TABLE `customer` (
  `cuId` int(11) NOT NULL AUTO_INCREMENT,
  `cuEmail` varchar(40) NOT NULL,
  `cuMobile` varchar(40) NOT NULL,
  `cuAddress` varchar(40) NOT NULL,
  `cuName` varchar(40) NOT NULL,
  PRIMARY KEY (`cuId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `customer` (`cuId`, `cuEmail`, `cuMobile`, `cuAddress`, `cuName`) VALUES
(1, 'aa@ff.com', '1234', 'aaa', 'asd'),
(2, 'aw@ff.com', '1234', 'asd', 'aaa'),
(3, 'asd@sdf.com', '01712020202', 'sffd', 'dgdfg');

-- Table structure for `customerlogin`
CREATE TABLE `customerlogin` (
  `cuId` int(11) NOT NULL AUTO_INCREMENT,
  `cuUserName` varchar(40) NOT NULL UNIQUE,
  `cuPassword` varchar(255) NOT NULL,
  PRIMARY KEY (`cuId`),
  FOREIGN KEY (`cuId`) REFERENCES `customer`(`cuId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `customerlogin` (`cuId`, `cuUserName`, `cuPassword`) VALUES
(1, 'asd', SHA2('12345', 256)),
(2, 'aaa', SHA2('12345', 256)),
(3, 'dgdfg', SHA2('12345', 256));

-- Table structure for `products`
CREATE TABLE `products` (
  `pdtId` int(11) NOT NULL AUTO_INCREMENT,
  `pdtName` TEXT NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`pdtId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `products` (`pdtId`, `pdtName`, `price`) VALUES
(1, 'Bronze Protection', 0.00),
(2, 'Silver Protection', 25.00),
(3, 'Gold Protection', 50.00);

-- Table structure for `productsorder`
CREATE TABLE `productsorder` (
  `orderId` int(11) NOT NULL AUTO_INCREMENT,
  `pdtId` int(11) NOT NULL,
  `pdtquantity` int(11) NOT NULL,
  `pdtprice` DECIMAL(10,2) NOT NULL,
  `totalprice` DECIMAL(10,2) NOT NULL,
  `ordercusname` varchar(50) NOT NULL,
  `orderphone` varchar(15) NOT NULL,
  `orderaddress` varchar(50) NOT NULL,
  PRIMARY KEY (`orderId`),
  FOREIGN KEY (`pdtId`) REFERENCES `products`(`pdtId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `productsorder` (`orderId`, `pdtId`, `pdtquantity`, `pdtprice`, `totalprice`, `ordercusname`, `orderphone`, `orderaddress`) VALUES
(1, 1, 0, 0.00, 0.00, 'sdsf', '01597498', 'jdeideh'),
(2, 2, 2, 25.00, 50.00, 'sf', '02597498', 'zahle'),
(3, 3, 1, 50.00, 50.00, 'sf', '03597498', 'beirut');

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
