-- Web-Based Blood Bank System - Production Database Schema
-- Compatible with MySQL 5.7+, MySQL 8.0+, MariaDB 10.3+, Aiven, Railway, TiDB

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- Table: hospitals
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `hospitals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hname` varchar(100) NOT NULL,
  `hemail` varchar(100) NOT NULL,
  `hpassword` varchar(255) NOT NULL,
  `hphone` varchar(100) NOT NULL,
  `hcity` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hemail` (`hemail`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: receivers
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `receivers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rname` varchar(100) NOT NULL,
  `remail` varchar(100) NOT NULL,
  `rpassword` varchar(255) NOT NULL,
  `rphone` varchar(100) NOT NULL,
  `rbg` varchar(10) NOT NULL,
  `rcity` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `remail` (`remail`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: bloodinfo (Hospital Stock Samples)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `bloodinfo` (
  `bid` int(11) NOT NULL AUTO_INCREMENT,
  `hid` int(11) NOT NULL,
  `bg` varchar(10) NOT NULL,
  PRIMARY KEY (`bid`),
  KEY `hid` (`hid`),
  CONSTRAINT `bloodinfo_ibfk_1` FOREIGN KEY (`hid`) REFERENCES `hospitals` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: bloodrequest (Blood Requests between Receivers and Hospitals)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `bloodrequest` (
  `reqid` int(11) NOT NULL AUTO_INCREMENT,
  `hid` int(11) NOT NULL,
  `rid` int(11) NOT NULL,
  `bg` varchar(11) NOT NULL,
  `status` varchar(100) NOT NULL DEFAULT 'Pending',
  PRIMARY KEY (`reqid`),
  KEY `hid` (`hid`),
  KEY `rid` (`rid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
