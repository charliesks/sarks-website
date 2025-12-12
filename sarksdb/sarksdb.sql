-- =====================================================
-- Safe SQL Initialization for sarksdb
-- MySQL 8.0 compatible
-- NO REAL CREDENTIALS
-- NO PII
-- =====================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

SET NAMES utf8mb4;

-- -----------------------------------------------------
-- Database
-- -----------------------------------------------------
CREATE DATABASE IF NOT EXISTS `sarksdb`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `sarksdb`;

-- -----------------------------------------------------
-- Table: admin
-- NOTE: No default admin user created
-- Admins MUST be created at runtime
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `admin` (
  `adId` INT NOT NULL AUTO_INCREMENT,
  `aUserName` VARCHAR(40) NOT NULL UNIQUE,
  `aPassword` VARCHAR(255) NOT NULL,
  `aMobile` VARCHAR(40),
  PRIMARY KEY (`adId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Table: customer
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `customer` (
  `cuId` INT NOT NULL AUTO_INCREMENT,
  `cuEmail` VARCHAR(255) NOT NULL UNIQUE,
  `cuMobile` VARCHAR(40),
  `cuAddress` VARCHAR(255),
  `cuName` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`cuId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Table: customerlogin
-- NOTE: Passwords must be hashed by the application
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `customerlogin` (
  `cuId` INT NOT NULL,
  `cuUserName` VARCHAR(40) NOT NULL UNIQUE,
  `cuPassword` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`cuId`),
  CONSTRAINT fk_customerlogin_customer
    FOREIGN KEY (`cuId`)
    REFERENCES `customer`(`cuId`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Table: products
-- SAFE to seed
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `products` (
  `pdtId` INT NOT NULL AUTO_INCREMENT,
  `pdtName` TEXT NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`pdtId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `products` (`pdtName`, `price`) VALUES
('Bronze Protection', 0.00),
('Silver Protection', 25.00),
('Gold Protection', 50.00);

-- -----------------------------------------------------
-- Table: productsorder
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `productsorder` (
  `orderId` INT NOT NULL AUTO_INCREMENT,
  `pdtId` INT NOT NULL,
  `pdtquantity` INT NOT NULL,
  `pdtprice` DECIMAL(10,2) NOT NULL,
  `totalprice` DECIMAL(10,2) NOT NULL,
  `ordercusname` VARCHAR(100) NOT NULL,
  `orderphone` VARCHAR(20) NOT NULL,
  `orderaddress` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`orderId`),
  CONSTRAINT fk_productsorder_product
    FOREIGN KEY (`pdtId`)
    REFERENCES `products`(`pdtId`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;
