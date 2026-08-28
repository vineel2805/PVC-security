-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 24, 2026 at 09:05 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pvc`
--

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `brandid` varchar(10) NOT NULL,
  `brandname` varchar(255) NOT NULL,
  `imagelink` varchar(255) DEFAULT '',
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `display_status` tinyint(1) NOT NULL DEFAULT 1,
  `display_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`brandid`, `brandname`, `imagelink`, `status`, `created_at`, `display_status`, `display_order`) VALUES
('B01', 'ANWIZ', '../uploads/brands/brand_b01_1785335792.jpg', 'Active', '2026-06-27 17:30:44', 1, 1),
('B02', 'D-LINK', 'assets/img/CATEGORY BANNERS/D-LINK.svg', 'Active', '2026-06-27 17:30:44', 1, 2),
('B023', 'CONNECT', '../uploads/brands/brand_b023_1784001975.png', 'Active', '2026-07-01 09:35:54', 1, 0),
('B024', 'OX', '../uploads/brands/brand_b024_1784002011.png', 'Active', '2026-07-01 09:54:02', 1, 0),
('B025', 'SECURE MAX', '../uploads/brands/brand_b025_1784002036.png', 'Active', '2026-07-03 04:32:51', 1, 0),
('B026', 'SMART', '../uploads/brands/brand_b026_1785335863.jpg', 'Active', '2026-07-12 17:29:18', 1, 0),
('B027', 'OTHERS', '../uploads/brands/brand_b027_1785335856.jpg', 'Active', '2026-07-24 09:34:59', 1, 0),
('B028', 'GEONIX', '../uploads/brands/brand_b028_1786640476.png', 'Active', '2026-07-29 15:01:44', 1, 0),
('B029', 'EZVIZ', '../uploads/brands/brand_b029_1786690377.png', 'Active', '2026-07-29 15:11:49', 1, 0),
('B03', 'CP PLUS\r\n', 'assets/img/CATEGORY BANNERS/CP PLUS.svg', 'Active', '2026-06-27 17:30:44', 1, 3),
('B030', 'TOSHIBA', '../uploads/brands/brand_b030_1786640420.png', 'Active', '2026-07-30 15:01:52', 1, 0),
('B031', 'SEAGATE', '../uploads/brands/brand_b031_1786640441.png', 'Active', '2026-07-30 15:01:59', 1, 0),
('B032', 'WESTERN DIGITAL', '../uploads/brands/brand_b032_1786640448.png', 'Active', '2026-07-30 15:04:56', 1, 0),
('B033', 'DAICHI', '../uploads/brands/brand_b033_1786640493.png', 'Active', '2026-07-30 16:14:48', 1, 0),
('B04', 'ERD', 'assets/img/CATEGORY BANNERS/EDR.svg', 'Active', '2026-06-27 17:30:44', 1, 0),
('B05', 'COFE', 'assets/img/CATEGORY BANNERS/COFE.svg', 'Active', '2026-06-27 17:30:44', 1, 0),
('B06', 'FINOLEX', 'assets/img/CATEGORY BANNERS/FINOLEX.svg', 'Inactive', '2026-06-27 17:30:44', 1, 0),
('B07', 'LPCARE', 'assets/img/CATEGORY BANNERS/LPCARE.svg', 'Active', '2026-06-27 17:30:44', 1, 0),
('B08', 'MAXXION', 'assets/img/CATEGORY BANNERS/MAXXION.svg', 'Active', '2026-06-27 17:30:44', 1, 0),
('B09', 'PRAMA', '../uploads/brands/brand_b09_1784480174.png', 'Active', '2026-06-27 17:30:44', 1, 0),
('B10', 'SMART PRO', '../uploads/brands/brand_b10_1785335898.jpg', 'Active', '2026-06-27 17:30:44', 1, 0),
('B11', 'YADON', '../uploads/brands/brand_b11_1786690439.png', 'Active', '2026-06-27 17:30:44', 1, 0),
('B12', 'VGURAD', '../uploads/brands/brand_b12_1786640601.png', 'Active', '2026-06-27 17:30:44', 1, 0),
('B13', 'VOLTAIC', '../uploads/brands/brand_b13_1786640592.png', 'Active', '2026-06-27 17:30:44', 1, 0),
('B15', 'DAHUA', 'assets/img/CATEGORY BANNERS/DAHUA.svg', 'Active', '2026-06-27 17:30:44', 1, 0),
('B16', 'TP-LINK\r\n', 'assets/img/CATEGORY BANNERS/TP LINK.svg', 'Active', '2026-06-27 17:30:44', 1, 0),
('B18', 'HIKVISION', 'assets/img/CATEGORY BANNERS/HIKVISON.svg', 'Active', '2026-06-27 17:30:44', 1, 0),
('B19', 'ZEBRONICS', 'assets/img/CATEGORY BANNERS/ZEBRONICS.svg', 'Active', '2026-06-27 17:30:44', 1, 0),
('B20', 'TRUEVIEW', 'assets/img/CATEGORY BANNERS/TRUEVIEW.svg', 'Active', '2026-06-27 17:30:44', 1, 0),
('B21', 'SECUREYE', 'assets/img/CATEGORY BANNERS/SECUREYE.svg', 'Active', '2026-06-27 17:30:44', 1, 0),
('B22', 'SECURUS', 'assets/img/CATEGORY BANNERS/SECRUS.svg', 'Active', '2026-06-27 17:30:44', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `cid` varchar(20) NOT NULL,
  `cname` varchar(255) NOT NULL,
  `cimage` varchar(255) DEFAULT '',
  `brandid` varchar(10) NOT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `display_status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`cid`, `cname`, `cimage`, `brandid`, `status`, `created_at`, `display_status`) VALUES
('B01-C09', 'WI-FI MODEL\'S', '../uploads/category/cat_b01-c09_1786025413.png', 'B01', 'Active', '2026-07-01 10:11:55', 1),
('B01-C11', '4G CAMERA MODELS', '../uploads/category/cat_b01-c11_1786025491.jpg', 'B01', 'Active', '2026-07-01 13:53:22', 1),
('B01-C12', 'IP CAMERAS', '../uploads/category/cat_b01-c12_1786026318.jpg', 'B01', 'Active', '2026-07-19 12:08:03', 1),
('B01-C13', 'SOLAR CAMERAS', 'uploads/product_Img/SOLAR CAMERAS.png', 'B01', 'Active', '2026-07-19 12:27:47', 1),
('B01-C14', 'TRUECLOUD  & ESEECLOUD app supported', '../uploads/category/cat_b01-c14_1785336678.png', 'B01', 'Active', '2026-07-27 15:34:04', 1),
('B02-C01', 'SMPS', '../uploads/category/cat_b02-c01_1785333249.jpg', 'B02', 'Active', '2026-07-21 14:14:42', 1),
('B02-C02', 'ROUTERS', '../uploads/category/cat_b02-c02_1784646722.png', 'B02', 'Active', '2026-07-21 14:47:53', 1),
('B023-C01', 'TRUECLOUD  & ESEECLOUD app supported', '../uploads/category/cat_b023-c01_1785336686.png', 'B023', 'Active', '2026-07-01 09:45:17', 1),
('B023-C02', 'WI-FI MODEL\'S', '../uploads/category/cat_b023-c02_1786025425.png', 'B023', 'Active', '2026-07-01 10:03:21', 1),
('B023-C03', '4G CAMERA MODELS', '../uploads/category/cat_b023-c03_1786025502.jpg', 'B023', 'Active', '2026-07-14 04:34:29', 1),
('B024-C01', 'TRUECLOUD  & ESEECLOUD app supported', '../uploads/category/cat_b024-c01_1785336692.png', 'B024', 'Active', '2026-07-01 09:54:21', 1),
('B024-C02', 'WI-FI MODEL\'S', '../uploads/category/cat_b024-c02_1786025432.png', 'B024', 'Active', '2026-07-01 10:43:42', 1),
('B025-C01', '4G CAMERA MODELS', '../uploads/category/cat_b025-c01_1786025508.jpg', 'B025', 'Active', '2026-07-03 04:36:44', 1),
('B026-C01', '4G CAMERA MODELS', '../uploads/category/cat_b026-c01_1786025512.jpg', 'B026', 'Active', '2026-07-27 13:52:24', 1),
('B026-C02', 'TRUECLOUD  & ESEECLOUD app supported', '../uploads/category/cat_b026-c02_1785336697.png', 'B026', 'Active', '2026-07-27 13:54:27', 1),
('B026-C03', 'SOLAR CAMERAS', 'uploads/product_Img/SOLAR CAMERAS.png', 'B026', 'Active', '2026-07-27 13:58:05', 1),
('B026-C04', 'WI-FI MODEL\'S', '../uploads/category/cat_b026-c04_1786025439.png', 'B026', 'Active', '2026-07-27 14:41:48', 1),
('B027-C01', 'ACCESSORIES', '../uploads/category/cat_b027-c01_1784993740.png', 'B027', 'Active', '2026-07-25 15:27:45', 1),
('B027-C03', 'STANDS', '../uploads/category/cat_b027-c03_1785335899.jpg', 'B027', 'Active', '2026-07-26 12:43:33', 1),
('B027-C04', 'CONVERTERS', '../uploads/category/cat_b21-c01_1784990802.png', 'B027', 'Active', '2026-07-27 13:03:29', 0),
('B027-C05', 'HDMI EXTENDERS', '../uploads/category/cat_c053_1784991107.png', 'B027', 'Active', '2026-07-27 13:05:06', 0),
('B027-C06', 'SPIKE/POWER SOCKETS', '../uploads/category/cat_b027-c06_1785427821.png', 'B027', 'Active', '2026-07-27 13:16:35', 1),
('B027-C07', 'BNC', '../uploads/category/cat_b027-c07_1786023936.png', 'B027', 'Active', '2026-07-27 13:17:49', 0),
('B027-C08', 'HDMI CABLES', '../uploads/category/cat_b027-c08_1785427599.png', 'B027', 'Active', '2026-07-29 14:09:50', 1),
('B027-C09', 'MONITORS', 'uploads/product_Img/MONITORS.png', 'B027', 'Active', '2026-07-30 14:08:35', 1),
('B027-C10', 'RACKS', 'uploads/product_Img/RACKS.png', 'B027', 'Active', '2026-07-30 14:28:37', 1),
('B027-C11', 'HDD HARD DISK', '../uploads/category/cat_b027-c11_1785427095.jpg', 'B027', 'Active', '2026-07-30 15:37:04', 1),
('B027-C12', 'SD CARDS', '../uploads/category/cat_b027-c12_1785429310.jpg', 'B027', 'Active', '2026-07-30 16:24:06', 1),
('B028-C01', 'SMPS', '../uploads/category/cat_b02-c01_1785333249.jpg', 'B028', 'Active', '2026-07-29 15:02:24', 1),
('B028-C02', 'SD CARDS', '../uploads/category/cat_b027-c12_1785429310.jpg', 'B028', 'Active', '2026-08-02 11:14:21', 1),
('B029-C01', 'SMART LOCK', '../uploads/category/cat_b029-c01_1785340878.jpg', 'B029', 'Active', '2026-07-29 15:12:35', 1),
('B029-C02', 'IP CAMERAS', '../uploads/category/cat_b029-c02_1786026328.jpg', 'B029', 'Active', '2026-07-29 15:18:14', 1),
('B03-C08', 'WI-FI MODEL\'S', '../uploads/category/cat_b03-c08_1785342516.jpg', 'B03', 'Active', '2026-07-01 10:11:45', 1),
('B030-C01', 'HDD HARD DISK', '../uploads/category/cat_b030-c01_1785427100.jpg', 'B030', 'Active', '2026-07-30 15:03:13', 1),
('B031-C01', 'HDD HARD DISK', '../uploads/category/cat_b031-c01_1785427107.jpg', 'B031', 'Active', '2026-07-30 15:02:28', 1),
('B032-C01', 'HDD HARD DISK', '../uploads/category/cat_b032-c01_1785427113.jpg', 'B032', 'Active', '2026-07-30 15:05:19', 1),
('B033-C01', 'SD CARDS', '../uploads/category/cat_b033-c01_1785429314.jpg', 'B033', 'Active', '2026-07-30 16:23:02', 1),
('B033-C02', 'SOLAR CAMERAS', 'uploads/product_Img/SOLAR CAMERAS.png', 'B033', 'Active', '2026-08-06 13:26:01', 1),
('B033-C03', 'WI-FI MODEL\'S', '../uploads/category/cat_b033-c03_1786025448.png', 'B033', 'Active', '2026-08-06 13:44:35', 1),
('B05-C04', 'WI-FI MODEL\'S', '../uploads/category/cat_b05-c04_1786025455.png', 'B05', 'Active', '2026-07-01 13:46:27', 1),
('B05-C23', '4G CAMERA MODELS', '../uploads/category/cat_b05-c23_1786026099.jpg', 'B05', 'Active', '2026-07-27 15:31:05', 1),
('B10-C05', 'TRUECLOUD  & ESEECLOUD app supported', '../uploads/category/cat_b10-c05_1785336701.png', 'B10', 'Active', '2026-07-01 09:35:17', 1),
('B10-C06', 'WI-FI MODEL\'S', '../uploads/category/cat_b10-c06_1786025463.png', 'B10', 'Active', '2026-07-01 10:12:08', 1),
('B10-C07', '4G CAMERA MODELS', '../uploads/category/cat_b10-c07_1786025519.jpg', 'B10', 'Active', '2026-07-01 13:45:38', 1),
('B10-C56', 'ACCESSORIES', '../uploads/category/cat_b027-c01_1784993740.png', 'B10', 'Active', '2026-07-27 13:19:52', 1),
('B13-C30', 'ACCESSORIES', '../uploads/category/cat_b027-c01_1784993740.png', 'B13', 'Active', '2026-07-27 13:27:28', 1),
('B18-C52', 'MONITORS', 'uploads/product_Img/MONITORS.png', 'B18', 'Active', '2026-07-30 13:47:22', 1),
('B18-C53', 'BIOMETRIC', '../uploads/category/cat_b18-c53_1785427490.jpg', 'B18', 'Active', '2026-07-30 14:15:53', 1),
('B18-C54', 'SD CARDS', '../uploads/category/cat_b18-c54_1785429318.jpg', 'B18', 'Active', '2026-07-30 16:14:37', 1),
('B20-C01', 'ROUTERS', '../uploads/category/cat_b20-c01_1784646743.png', 'B20', 'Active', '2026-07-21 15:05:11', 1),
('B21-C01', 'CONVERTERS', '../uploads/category/cat_b21-c01_1784990802.png', 'B21', 'Active', '2026-07-21 14:07:16', 0),
('B21-C68', 'ACCESSORIES', '../uploads/category/cat_b027-c01_1784993740.png', 'B21', 'Active', '2026-07-27 13:23:41', 1),
('B21-C69', 'SD CARDS', '../uploads/category/cat_b21-c69_1785429323.jpg', 'B21', 'Active', '2026-07-30 16:25:05', 1),
('C002', 'NVR', 'uploads/product_Img/NVR.png', 'B01', 'Active', '2026-06-27 17:30:44', 1),
('C003', 'POE', 'uploads/product_Img/POE.png', 'B01', 'Active', '2026-06-27 17:30:44', 1),
('C004', 'CABLES', '../uploads/category/cat_c004_1786029156.jpg', 'B01', 'Active', '2026-06-27 17:30:44', 1),
('C005', 'RACKS', 'uploads/product_Img/RACKS.png', 'B01', 'Active', '2026-06-27 17:30:44', 1),
('C006', 'SMPS', '../uploads/category/cat_c006_1785333262.jpg', 'B01', 'Active', '2026-06-27 17:30:44', 1),
('C007', 'ADAPTERS', 'uploads/product_Img/ADAPTERS.png', 'B01', 'Active', '2026-06-27 17:30:44', 1),
('C008', 'RACKS', 'uploads/product_Img/RACKS.png', 'B02', 'Active', '2026-06-27 17:30:44', 1),
('C009', 'CABLES', '../uploads/category/cat_c009_1786029160.jpg', 'B02', 'Active', '2026-06-27 17:30:44', 1),
('C010', 'ACCESSORIES', '../uploads/category/cat_c010_1784993752.png', 'B02', 'Active', '2026-06-27 17:30:44', 1),
('C011', 'POE', 'uploads/product_Img/POE.png', 'B02', 'Active', '2026-06-27 17:30:44', 1),
('C013', 'SWITCHES', 'uploads/product_Img/SWITCHES.png', 'B02', 'Active', '2026-06-27 17:30:44', 1),
('C014', 'SMPS/POWER SUPPLY', 'uploads/product_Img/SMPS or POWER SUPPLY.png', 'B02', 'Active', '2026-06-27 17:30:44', 1),
('C015', 'DVR', 'uploads/product_Img/DVR.png', 'B03', 'Active', '2026-06-27 17:30:44', 1),
('C016', 'NVR', 'uploads/product_Img/NVR.png', 'B03', 'Active', '2026-06-27 17:30:44', 1),
('C017', 'HD CAMERAS', 'uploads/product_Img/HD CAMERAS.png', 'B03', 'Active', '2026-06-27 17:30:44', 1),
('C018', 'IP CAMERAS', '../uploads/category/cat_c018_1786026335.jpg', 'B03', 'Active', '2026-06-27 17:30:44', 1),
('C020', 'POE', 'uploads/product_Img/POE.png', 'B03', 'Active', '2026-06-27 17:30:44', 1),
('C021', 'CABLES', '../uploads/category/cat_c021_1786029164.jpg', 'B03', 'Active', '2026-06-27 17:30:44', 1),
('C022', 'SOLAR CAMERAS', 'uploads/product_Img/SOLAR CAMERAS.png', 'B05', 'Active', '2026-06-27 17:30:44', 1),
('C024', 'ADAPTERS', 'uploads/product_Img/ADAPTERS.png', 'B04', 'Active', '2026-06-27 17:30:44', 1),
('C025', 'STABILIZER', '../uploads/category/cat_c025_1785339793.jpg', 'B12', 'Active', '2026-06-27 17:30:44', 1),
('C026', 'UPS', '../uploads/category/cat_c026_1785339738.jpg', 'B12', 'Active', '2026-06-27 17:30:44', 1),
('C027', 'CABLES', '../uploads/category/cat_c027_1786029168.jpg', 'B13', 'Active', '2026-06-27 17:30:44', 1),
('C028', 'RACKS', 'uploads/product_Img/RACKS.png', 'B13', 'Active', '2026-06-27 17:30:44', 1),
('C029', 'BNC', '../uploads/category/cat_c029_1785340903.png', 'B13', 'Active', '2026-06-27 17:30:44', 0),
('C032', 'DVR', 'uploads/product_Img/DVR.png', 'B15', 'Active', '2026-06-27 17:30:44', 1),
('C033', 'NVR', 'uploads/product_Img/NVR.png', 'B15', 'Active', '2026-06-27 17:30:44', 1),
('C034', 'HD CAMERAS', 'uploads/product_Img/HD CAMERAS.png', 'B15', 'Active', '2026-06-27 17:30:44', 1),
('C035', 'NVR', 'uploads/product_Img/NVR.png', 'B16', 'Active', '2026-06-27 17:30:44', 1),
('C036', 'POE', 'uploads/product_Img/POE.png', 'B16', 'Active', '2026-06-27 17:30:44', 1),
('C037', 'SWITCHES', 'uploads/product_Img/SWITCHES.png', 'B16', 'Active', '2026-06-27 17:30:44', 1),
('C038', 'ACCESS POINTS', '../uploads/category/cat_c038_1785066664.png', 'B16', 'Active', '2026-06-27 17:30:44', 1),
('C039', 'ROUTERS', '../uploads/category/cat_c039_1785340917.png', 'B16', 'Active', '2026-06-27 17:30:44', 1),
('C040', 'MONITORS', 'uploads/product_Img/MONITORS.png', 'B07', 'Active', '2026-06-27 17:30:44', 1),
('C041', 'MOUSES', 'uploads/product_Img/MOUSES.png', 'B07', 'Active', '2026-06-27 17:30:44', 1),
('C047', 'PTZ CAMERAS', '../uploads/category/cat_c047_1785332508.jpg', 'B09', 'Active', '2026-06-27 17:30:44', 1),
('C048', 'DVR', 'uploads/product_Img/DVR.png', 'B18', 'Active', '2026-06-27 17:30:44', 1),
('C049', 'NVR', 'uploads/product_Img/NVR.png', 'B18', 'Active', '2026-06-27 17:30:44', 1),
('C050', 'HD CAMERAS', 'uploads/product_Img/HD CAMERAS.png', 'B18', 'Active', '2026-06-27 17:30:44', 1),
('C051', 'CABLES', '../uploads/category/cat_c051_1786029176.jpg', 'B18', 'Active', '2026-06-27 17:30:44', 1),
('C052', 'HDMI CABLES', '../uploads/category/cat_c052_1784990955.png', 'B10', 'Active', '2026-06-27 17:30:44', 1),
('C053', 'HDMI EXTENDERS', '../uploads/category/cat_c053_1784991107.png', 'B10', 'Active', '2026-06-27 17:30:44', 0),
('C054', 'CONVERTERS', '../uploads/category/cat_c054_1784990816.png', 'B10', 'Active', '2026-06-27 17:30:44', 0),
('C055', 'LAN TESTER', '../uploads/category/cat_c055_1784991195.png', 'B027', 'Active', '2026-06-27 17:30:44', 1),
('C056', 'MONITORS', 'uploads/product_Img/MONITORS.png', 'B19', 'Active', '2026-06-27 17:30:44', 1),
('C057', 'SMPS', '../uploads/category/cat_c057_1785333271.jpg', 'B19', 'Active', '2026-06-27 17:30:44', 1),
('C058', 'CABLES', '../uploads/category/cat_c058_1786029181.jpg', 'B19', 'Active', '2026-06-27 17:30:44', 1),
('C059', 'UPS', '../uploads/category/cat_c059_1785339921.webp', 'B19', 'Active', '2026-06-27 17:30:44', 1),
('C065', 'POE', 'uploads/product_Img/POE.png', 'B21', 'Active', '2026-06-27 17:30:44', 1),
('C066', 'MEDIA CONVERTERS', 'uploads/product_Img/MEDIA CONVERTERS.png', 'B21', 'Active', '2026-06-27 17:30:44', 1),
('C067', 'MEMORY CARDS', 'uploads/product_Img/MEMORY CARDS.png', 'B21', 'Active', '2026-06-27 17:30:44', 0),
('C069', 'NVR', 'uploads/product_Img/NVR.png', 'B22', 'Active', '2026-06-27 17:30:44', 1),
('C070', 'DVR', 'uploads/product_Img/DVR.png', 'B22', 'Active', '2026-06-27 17:30:44', 1),
('C071', 'IP CAMERAS', '../uploads/category/cat_c071_1786026340.jpg', 'B22', 'Active', '2026-06-27 17:30:44', 1),
('C072', 'HD CAMERAS', 'uploads/product_Img/HD CAMERAS.png', 'B22', 'Active', '2026-06-27 17:30:44', 1),
('C073', 'PTZ CAMERAS', '../uploads/category/cat_c073_1785331752.jpg', 'B22', 'Active', '2026-06-27 17:30:44', 1),
('C074', 'POE', 'uploads/product_Img/POE.png', 'B11', 'Active', '2026-06-27 17:30:44', 1),
('C075', 'UPS', '../uploads/category/cat_c075_1784991326.png', 'B11', 'Active', '2026-06-27 17:30:44', 1);

-- --------------------------------------------------------

--
-- Table structure for table `hero_slides`
--

CREATE TABLE `hero_slides` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `desktop_image` varchar(255) NOT NULL,
  `mobile_image` varchar(255) DEFAULT NULL,
  `button_text` varchar(100) DEFAULT NULL,
  `button_link` varchar(255) DEFAULT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hero_slides`
--

INSERT INTO `hero_slides` (`id`, `title`, `subtitle`, `description`, `desktop_image`, `mobile_image`, `button_text`, `button_link`, `display_order`, `status`, `created_at`, `updated_at`) VALUES
(6, '', '', '', 'uploads/slides/slide_1787324113_6427.jpg', 'uploads/slides/slide_1787324113_5779.jpg', '', '', 0, 1, '2026-08-21 14:55:13', '2026-08-21 14:55:13');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `pid` varchar(30) NOT NULL,
  `pname` varchar(255) NOT NULL,
  `pdescription` text DEFAULT NULL,
  `pcat` varchar(20) NOT NULL,
  `brandid` varchar(10) NOT NULL,
  `pimage` varchar(255) DEFAULT '',
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `display_status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`pid`, `pname`, `pdescription`, `pcat`, `brandid`, `pimage`, `status`, `created_at`, `display_status`) VALUES
('B01-C09-P01', 'TRIBLE LENS Wi-Fi OUTDOOR CCTV CAMERA', '', 'B01-C09', 'B01', '../uploads/products/prod_b01-c09-p01_1784455145.jpeg', 'Active', '2026-07-01 10:15:55', 1),
('B01-C09-P02', 'WI-FI 3MP ROBO', '', 'B01-C09', 'B01', '../uploads/products/prod_b01-c09-p02_1784454967.jpeg', 'Active', '2026-07-01 10:42:05', 1),
('B01-C12-P01', 'ANWIZ-ALM5MB2', 'ANWIZ 5MP IP CAMERA', 'B01-C12', 'B01', '../uploads/products/prod_b01-c12-p01_1784549841.jpeg', 'Active', '2026-07-20 12:17:21', 1),
('B01-C12-P02', 'ANWIZ-ALM5MD2', 'ANWIZ 5MP IP DOME CAMERA', 'B01-C12', 'B01', '../uploads/products/prod_b01-c12-p02_1784549889.jpeg', 'Active', '2026-07-20 12:18:09', 1),
('B01-C12-P03', 'ANWIZ B5 (AN-PREM-5MB5)', 'ANWIZ 5MP BULLET CAMERA', 'B01-C12', 'B01', '../uploads/products/prod_b01-c12-p03_1784549976.jpeg', 'Active', '2026-07-20 12:19:36', 1),
('B01-C12-P04', 'ANWIZ D5 (AN-PREM-5MD5)', 'ANWIZ 5MP IP DOME CAMERA', 'B01-C12', 'B01', '../uploads/products/prod_b01-c12-p04_1784551983.jpeg', 'Active', '2026-07-20 12:53:03', 1),
('B01-C12-P05', 'ANWIZ-PROM-5MB4', 'ANWIZ 5MP IP BULLET CAMERA', 'B01-C12', 'B01', '../uploads/products/prod_b01-c12-p05_1784552075.jpeg', 'Active', '2026-07-20 12:54:35', 1),
('B01-C12-P06', 'ANWIZ-PROM-5MD4', 'ANWIZ 5MP IP DOME CAMERA', 'B01-C12', 'B01', '../uploads/products/prod_b01-c12-p06_1784552187.jpeg', 'Active', '2026-07-20 12:56:27', 1),
('B01-C12-P07', 'ANWIZ-ALP5MB1', 'ANWIZ 5MP IP BULLET CAMERA', 'B01-C12', 'B01', '../uploads/products/prod_b01-c12-p07_1784552246.jpeg', 'Active', '2026-07-20 12:57:26', 1),
('B01-C12-P08', 'ANWIZ PROJ-MOTMVD6 DOME', 'ANWIZ 5MP IP BULLET CAMERA', 'B01-C12', 'B01', '../uploads/products/prod_b01-c12-p08_1784552311.jpeg', 'Active', '2026-07-20 12:58:31', 1),
('B01-C12-P09', 'ANWIZ-ALP5MD1', 'ANWIZ 5MP IP DOME CAMERA', 'B01-C12', 'B01', '../uploads/products/prod_b01-c12-p09_1784552522.jpeg', 'Active', '2026-07-20 13:02:02', 1),
('B01-C13-P01', 'ANWIZ SOLAR 4G CAMERA', '', 'B01-C13', 'B01', '../uploads/products/prod_b01-c13-p01_1785336764.jpeg', 'Inactive', '2026-07-27 15:14:02', 1),
('B01-C14-P01', 'ANWIZ WI-FI ROBO', '', 'B01-C14', 'B01', '../uploads/products/prod_b01-c14-p01_1785331872.jpeg', 'Inactive', '2026-07-27 15:34:04', 1),
('B02-C01-P01', 'D-LINK 4CH SMPS (DPS-F1805)', '', 'B02-C01', 'B02', '../uploads/products/prod_b02-c01-p01_1784644630.jpg', 'Active', '2026-07-21 14:15:25', 1),
('B02-C02-P01', 'D LINK 4G ROUTER N300 WIFI (G403C)', '', 'B02-C02', 'B02', '../uploads/products/prod_b02-c02-p01_1784645317.png', 'Active', '2026-07-21 14:48:37', 1),
('B02-C02-P02', 'D-LINK N300 ROUTER (DIR-650IN)', '', 'B02-C02', 'B02', '../uploads/products/prod_b02-c02-p02_1784645377.jpg', 'Active', '2026-07-21 14:49:37', 1),
('B023-C01-P01', 'WI-FI TRIPLE LENS', '', 'B023-C01', 'B023', '../uploads/products/prod_b023-c01-p01_1784640359.jpeg', 'Inactive', '2026-07-01 09:46:49', 1),
('B023-C01-P03', 'WI-FI BASED CCTV CAMERA', '', 'B023-C01', 'B023', '../uploads/products/prod_b023-c01-p03_1784640335.jpeg', 'Inactive', '2026-07-01 09:49:16', 1),
('B023-C01-P04', 'CONNECT 4G LINKAGE CAMERA', '', 'B023-C01', 'B023', '../uploads/products/prod_b023-c01-p04_1784641559.jpeg', 'Inactive', '2026-07-01 09:50:51', 1),
('B023-C01-P06', 'CONNECT SOLAR 4G CAMERA', '', 'B023-C01', 'B023', '../uploads/products/prod_b023-c01-p06_1784640280.jpeg', 'Inactive', '2026-07-14 05:02:54', 1),
('B023-C02-P01', 'WI-FI BASED CCTV CAMERA', '', 'B023-C02', 'B023', '../uploads/products/prod_b023-c02-p01_1784640348.jpeg', 'Inactive', '2026-07-01 10:04:26', 1),
('B023-C02-P02', 'CONNECT WI-FI TRIPLE LENS', '', 'B023-C02', 'B023', '../uploads/products/prod_b023-c02-p02_1784641403.jpeg', 'Inactive', '2026-07-01 10:04:58', 1),
('B023-C03-P01', 'CONNECT 4G LINKAGE CAMERA', '', 'B023-C03', 'B023', '../uploads/products/prod_b023-c03-p01_1785332040.jpeg', 'Inactive', '2026-07-14 04:35:23', 1),
('B023-C03-P02', 'CONNECT SOLAR 4G CAMERA', '', 'B023-C03', 'B023', '../uploads/products/prod_b023-c03-p02_1784004361.jpeg', 'Inactive', '2026-07-14 04:46:01', 1),
('B024-C01-P02', 'OX 3MP SINGLE LENS PT', '', 'B024-C01', 'B024', '../uploads/products/prod_b024-c01-p02_1784644914.jpeg', 'Inactive', '2026-07-14 05:04:04', 1),
('B024-C02-P01', 'OX 3MP SINGLE LENS PT', '', 'B024-C02', 'B024', '../uploads/products/prod_b024-c02-p01_1784645026.jpeg', 'Inactive', '2026-07-01 10:44:05', 1),
('B024-C02-P02', 'OX 3MP SMART WIFI BULLET CAMERA', '', 'B024-C02', 'B024', '../uploads/products/prod_b024-c02-p02_1786027594.jpeg', 'Active', '2026-07-29 15:15:56', 1),
('B025-C01-P01', '4G PLUS WIFI OUTDOOR CCTV CAMERA', '', 'B025-C01', 'B025', '../uploads/products/prod_b025-c01-p01_1784469343.jpeg', 'Inactive', '2026-07-03 04:43:40', 1),
('B025-C01-P02', '4G DUAL LENS PT CAMERA', '', 'B025-C01', 'B025', '../uploads/products/prod_b025-c01-p02_1786022633.jpeg', 'Inactive', '2026-07-29 15:07:20', 1),
('B026-C01-P01', 'SMART 4G BULLET 3MP', '', 'B026-C01', 'B026', '../uploads/products/prod_b026-c01-p01_1785332224.jpeg', 'Inactive', '2026-07-14 04:30:20', 1),
('B026-C01-P02', 'SMART 4G DOME 3MP', '', 'B026-C01', 'B026', '../uploads/products/prod_b026-c01-p02_1785332198.jpeg', 'Inactive', '2026-07-14 04:28:39', 1),
('B026-C01-P03', 'SMART SINGLE LENS 4G', '', 'B026-C01', 'B026', '../uploads/products/prod_b026-c01-p03_1785332210.jpeg', 'Inactive', '2026-07-14 04:32:52', 1),
('B026-C01-P04', 'SMART 4G TRIPLE LENS', '', 'B026-C01', 'B026', '../uploads/products/prod_b10-c07-p05_1784641718.jpeg', 'Inactive', '2026-07-01 13:51:58', 1),
('B026-C02-P02', 'SMART 4G BULLET 3MP', '', 'B026-C02', 'B026', '../uploads/products/prod_b026-c02-p02_1785332191.jpeg', 'Inactive', '2026-07-01 09:37:54', 1),
('B026-C02-P03', 'SMART 4G TRIPLE LENS', '', 'B026-C02', 'B026', '../uploads/products/prod_b10-c05-p01_1784641708.jpeg', 'Inactive', '2026-07-01 09:36:49', 1),
('B026-C02-P04', 'SMART DOME 3MP', '', 'B026-C02', 'B026', '../uploads/products/prod_b10-c05-p05_1784640954.jpeg', 'Inactive', '2026-07-01 09:41:35', 1),
('B026-C02-P05', 'SMART SINGLE LENS 4G', '', 'B026-C02', 'B026', '../uploads/products/prod_b10-c05-p04_1784645602.jpeg', 'Inactive', '2026-07-01 09:39:49', 1),
('B026-C02-P06', 'SMART WI-FI 3mp BULLET CAMERA', '', 'B026-C02', 'B026', '../uploads/products/prod_b026-c02-p06_1785332278.jpeg', 'Inactive', '2026-07-03 11:57:15', 1),
('B026-C02-P07', 'TRIPLE LENS SOLAR 4G', '', 'B026-C02', 'B026', '../uploads/products/prod_b10-c05-p03_1784640970.jpeg', 'Inactive', '2026-07-01 09:38:51', 1),
('B026-C03-P01', 'SMART SOLAR 4G LINKAGE CAMERA', '', 'B026-C03', 'B026', '../uploads/products/prod_b10-c07-p11_1784642343.jpeg', 'Inactive', '2026-07-14 04:44:54', 1),
('B026-C03-P02', 'TRIPLE LENS SOLAR 4G', '', 'B026-C03', 'B026', '../uploads/products/prod_b026-c03-p02_1785331187.jpeg', 'Inactive', '2026-07-27 14:46:44', 1),
('B026-C04-P01', 'SMART WI-FI 3mp BULLET CAMERA', '', 'B026-C04', 'B026', '../uploads/products/prod_b026-c04-p01_1785332306.jpeg', 'Inactive', '2026-07-27 14:41:48', 1),
('B026-C04-P02', 'SMART WI-FI 3mp DOME CAMERA', '', 'B026-C04', 'B026', '../uploads/products/prod_b026-c04-p02_1785332321.jpeg', 'Inactive', '2026-07-27 14:43:35', 1),
('B027-C01-P01', 'CR 1220 Cell With Soldering Tabs', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p01_1784993307.png', 'Active', '2026-07-25 15:28:27', 1),
('B027-C01-P02', 'TARA Multiplug', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p02_1784993529.jpg', 'Active', '2026-07-25 15:32:09', 1),
('B027-C01-P03', '100 mm SS Ring Adjustable', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p03_1784993842.png', 'Active', '2026-07-25 15:37:22', 1),
('B027-C01-P04', '200 mm SS Ring Adjustable', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p04_1784993866.png', 'Active', '2026-07-25 15:37:46', 1),
('B027-C01-P05', '400 mm SS Ring Adjustable', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p05_1784993888.png', 'Active', '2026-07-25 15:38:08', 1),
('B027-C01-P06', 'Multifunctional Wire Stripper Crimper Cable Cutter Pliers', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p06_1784993968.jpg', 'Active', '2026-07-25 15:39:28', 1),
('B027-C01-P07', '2 MP AHD Pinhole Camera With Mic', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p07_1784994188.jpg', 'Active', '2026-07-25 15:43:08', 1),
('B027-C01-P08', 'Copper Wired DC', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p08_1784994437.png', 'Active', '2026-07-25 15:47:17', 1),
('B027-C01-P09', 'Copper Wired DC (Heavy)', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p09_1784994661.png', 'Active', '2026-07-25 15:51:01', 1),
('B027-C01-P10', 'Video Balun 8 MP', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p10_1784994730.webp', 'Active', '2026-07-25 15:52:10', 1),
('B027-C01-P11', 'PV Video Balun', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p11_1784994795.jpg', 'Active', '2026-07-25 15:53:15', 1),
('B027-C01-P12', 'DC Female', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p12_1784994888.jpg', 'Active', '2026-07-25 15:54:48', 1),
('B027-C01-P13', 'Lan Jointer', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p13_1784994940.webp', 'Active', '2026-07-25 15:55:40', 1),
('B027-C01-P14', 'Rj45 Lan Splitter', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p14_1784995010.webp', 'Active', '2026-07-25 15:56:50', 1),
('B027-C01-P15', 'HDMI Jointer L Type', 'HDMI Jointer L Type adapter', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p15_1784995085.webp', 'Active', '2026-07-25 15:58:05', 1),
('B027-C01-P16', 'HDMI Jointer I Type', 'HDMI Jointer I Type adpater', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p16_1784995152.jpg', 'Active', '2026-07-25 15:59:12', 1),
('B027-C01-P17', 'POE Injector', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p17_1784995246.webp', 'Active', '2026-07-25 16:00:46', 1),
('B027-C01-P18', 'POE Multiplexer', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p18_1784995310.png', 'Active', '2026-07-25 16:01:50', 1),
('B027-C01-P20', 'POE Extender 1+2', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p20_1784995429.webp', 'Active', '2026-07-25 16:03:49', 1),
('B027-C01-P22', 'EMI Lock 600 LBS', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p22_1785065307.webp', 'Active', '2026-07-26 11:28:27', 1),
('B027-C01-P23', 'D LINK LAN Tester LAN+BNC', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p23_1785065398.webp', 'Active', '2026-07-26 11:29:58', 1),
('B027-C01-P24', 'HDMI Splitter 1×4', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p24_1785066159.png', 'Active', '2026-07-26 11:42:39', 1),
('B027-C01-P25', 'Wire Stripper Cutter Yellow', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p25_1785066240.webp', 'Active', '2026-07-26 11:44:00', 1),
('B027-C01-P27', 'HDMI Splitter 1×2', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p27_1785066395.webp', 'Active', '2026-07-26 11:46:35', 1),
('B027-C01-P28', 'HDMI Switch 1×3', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p28_1785066442.webp', 'Active', '2026-07-26 11:47:22', 1),
('B027-C01-P29', 'HDMI Switch 1×5', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p29_1785066775.png', 'Active', '2026-07-26 11:52:55', 1),
('B027-C01-P30', 'Wire Tracker', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p30_1785066908.webp', 'Active', '2026-07-26 11:55:08', 1),
('B027-C01-P31', 'Double Nail Cable Clip 10 mm', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p31_1785068268.webp', 'Active', '2026-07-26 11:58:51', 1),
('B027-C01-P32', 'Cable Tag Tie 150 x 4.8 mm (Pack of 100)', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p32_1785068565.png', 'Active', '2026-07-26 11:59:53', 1),
('B027-C01-P33', 'Cable Tag Tie 100 x 4.8 mm (Pack of 100)', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p33_1785067220.png', 'Active', '2026-07-26 12:00:20', 1),
('B027-C01-P34', '5 pc CR 1220 Cell', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p34_1785067500.png', 'Active', '2026-07-26 12:05:00', 1),
('B027-C01-P35', '5 pc CR 2032 Cell', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p35_1785067560.png', 'Active', '2026-07-26 12:06:00', 1),
('B027-C01-P36', 'RJ45 BOOTS', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p36_1785070024.jpg', 'Active', '2026-07-26 12:47:04', 1),
('B027-C01-P37', '5×5 Diamond Junction Box', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p37_1785071278.png', 'Active', '2026-07-26 13:07:58', 1),
('B027-C01-P38', '5 x 5 Outdoor Junction Box', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p38_1785072905.png', 'Active', '2026-07-26 13:35:05', 1),
('B027-C01-P39', 'VGA 1.5 Meter Cable Heavy', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p39_1785337228.jpg', 'Active', '2026-07-27 12:23:07', 1),
('B027-C01-P40', 'VGA 3 Meter Cable Heavy', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p40_1785337244.jpg', 'Active', '2026-07-27 12:23:46', 1),
('B027-C01-P41', 'VGA 5 METER Cable Heavy', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p41_1785337249.jpg', 'Active', '2026-07-27 12:24:12', 1),
('B027-C01-P42', 'VGA 20 Meter  Cable Heavy', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p42_1785337234.jpg', 'Active', '2026-07-27 12:25:01', 1),
('B027-C01-P43', '1.5 Meter Desktop Power Cable', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p43_1785331226.webp', 'Active', '2026-07-27 12:28:11', 1),
('B027-C01-P46', 'Sata Power Cable', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p46_1785337189.webp', 'Active', '2026-07-27 12:31:23', 1),
('B027-C01-P47', 'CCTV Mic', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p47_1785332002.jpg', 'Active', '2026-07-27 12:32:38', 1),
('B027-C01-P48', 'Cable Tag Tie 200 x 4.8 mm (Pack of 100)', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p48_1785331991.png', 'Active', '2026-07-27 12:36:25', 1),
('B027-C01-P49', 'Cable Clip 12 mm', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p49_1785331975.webp', 'Active', '2026-07-27 12:37:27', 1),
('B027-C01-P50', 'Cable Clip  6mm', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p50_1785331963.webp', 'Active', '2026-07-27 12:37:55', 1),
('B027-C01-P51', 'Cable Clip 8 mm', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p51_1785331982.webp', 'Active', '2026-07-27 12:38:21', 1),
('B027-C01-P52', 'Cable Clip 10 mm', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p52_1785331969.webp', 'Active', '2026-07-27 12:38:37', 1),
('B027-C01-P53', 'Electric Tape Box', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p53_1785332075.png', 'Active', '2026-07-27 12:58:09', 1),
('B027-C01-P54', 'USB to DC 12 Inch Long', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p54_1785337217.png', 'Active', '2026-07-27 13:02:44', 1),
('B027-C01-P55', 'Transparent Rj45 Boot', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p55_1785331243.jpg', 'Active', '2026-07-27 13:11:24', 1),
('B027-C01-P56', 'IP POE Cable', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p56_1785337176.png', 'Active', '2026-07-27 13:15:38', 1),
('B027-C01-P57', 'USB 3.0 to HDMI', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p57_1785337208.jpg', 'Active', '2026-07-27 13:03:29', 1),
('B027-C01-P58', 'Wired BNC (WHITE)', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p26_1785066308.webp', 'Active', '2026-07-26 11:45:08', 1),
('B027-C01-P59', 'HDMI Extender 30 Meter', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p59_1785332104.jpg', 'Active', '2026-07-27 12:29:41', 1),
('B027-C01-P60', 'HDMI KVM Extender 200 Meter', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p60_1785337159.webp', 'Active', '2026-07-27 12:30:25', 1),
('B027-C01-P61', 'BNC SCREW TYPE', '', 'B027-C01', 'B027', '../uploads/products/prod_p-b08-019_1784468515.jpg', 'Active', '2026-06-27 20:58:25', 1),
('B027-C01-P62', 'CABEL MANAGER', '', 'B027-C01', 'B027', '../uploads/products/prod_p-b08-017_1784468526.png', 'Active', '2026-06-27 20:58:25', 1),
('B027-C01-P63', 'CABINET LOCK (MX-CDL)', '', 'B027-C01', 'B027', '../uploads/products/prod_p-b08-016_1784468534.png', 'Active', '2026-06-27 20:58:25', 1),
('B027-C01-P64', 'CRIMPING TOOL HEAVY', '', 'B027-C01', 'B027', '../uploads/products/prod_p-b08-020_1784468582.jpg', 'Active', '2026-06-27 20:58:25', 1),
('B027-C01-P65', 'DC SCREW TYPE', '', 'B027-C01', 'B027', '../uploads/products/prod_p-b08-018_1784468620.jpg', 'Active', '2026-06-27 20:58:25', 1),
('B027-C01-P66', 'GIGA POE EXTENDER WATER PROOF 90W (MX-PE13G-OD9W)', '', 'B027-C01', 'B027', '../uploads/products/prod_p-b08-002_1784468694.jpg', 'Active', '2026-06-27 20:58:25', 1),
('B027-C01-P67', 'JUNCTION BOX', '', 'B027-C01', 'B027', '../uploads/products/prod_p-b08-024_1784468868.jpg', 'Active', '2026-06-27 20:58:25', 1),
('B027-C01-P68', 'GIGA POE EXTENDER WATER PROOF 30W (MX-PE13G-OD3W)', '', 'B027-C01', 'B027', '../uploads/products/prod_p-b08-001_1784468742.jpg', 'Active', '2026-06-27 20:58:25', 1),
('B027-C01-P69', 'PUNCH DOWN TOOL', '', 'B027-C01', 'B027', '../uploads/products/prod_p-b08-025_1784468954.jpg', 'Active', '2026-06-27 20:58:25', 1),
('B027-C01-P70', 'ROUND BOX HEAVY (MX-HBRS)', '', 'B027-C01', 'B027', '../uploads/products/prod_p-b08-021_1784469003.jpg', 'Active', '2026-06-27 20:58:25', 1),
('B027-C01-P71', 'ROUND BOX HEAVY 5XS (MX-HBR)', '', 'B027-C01', 'B027', '../uploads/products/prod_p-b08-022_1784469032.jpg', 'Active', '2026-06-27 20:58:25', 1),
('B027-C01-P72', 'SQUARE BOX HEAVY 5X5', '', 'B027-C01', 'B027', '../uploads/products/prod_p-b08-023_1784469060.jpg', 'Active', '2026-06-27 20:58:25', 1),
('B027-C01-P73', 'RACK FAN', '', 'B027-C01', 'B027', '../uploads/products/prod_p-b08-027_1784469146.jpg', 'Active', '2026-06-27 20:58:25', 1),
('B027-C01-P74', 'WIRE BNC (HEAVY QUALITY)', '', 'B027-C01', 'B027', '../uploads/products/prod_p-b08-026_1784469112.jpg', 'Active', '2026-06-27 20:58:25', 1),
('B027-C01-P75', 'BLACK SCREWS', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p75_1785340385.jpg', 'Active', '2026-07-29 15:53:05', 1),
('B027-C01-P76', 'TARA MALE FEMALE PLUG', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p76_1785340597.jpg', 'Active', '2026-07-29 15:56:37', 1),
('B027-C01-P77', 'BATTERY CELL', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p77_1785342020.jpg', 'Active', '2026-07-29 16:20:20', 1),
('B027-C01-P78', 'BATTERY CELL KP CR2032 3V 19E', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p78_1785342118.jpg', 'Active', '2026-07-29 16:21:58', 1),
('B027-C01-P79', 'BATTERY SOLAR CELL', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p79_1785342399.jpg', 'Active', '2026-07-29 16:26:39', 1),
('B027-C01-P80', 'SOLAR PANEL', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p80_1785342477.jpg', 'Active', '2026-07-29 16:27:57', 1),
('B027-C01-P81', 'Fiber Optic Patch Cable', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p81_1785419199.jpg', 'Active', '2026-07-30 13:46:39', 1),
('B027-C01-P82', 'USB TO C-TYPE DELUXE DIGITAL CABLE', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p82_1785419274.webp', 'Active', '2026-07-30 13:47:54', 1),
('B027-C01-P83', 'USB RJ45 EXTENSION ADAPTER UPTO 150FT', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p83_1785419304.jpg', 'Active', '2026-07-30 13:48:24', 1),
('B027-C01-P84', 'PUNCHDOWN TOOL', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p84_1785419334.jpg', 'Active', '2026-07-30 13:48:54', 1),
('B027-C01-P85', 'TYPE-C USB HUB 4 IN 1', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p85_1785419457.jpg', 'Active', '2026-07-30 13:50:57', 1),
('B027-C01-P86', 'FIBRE BOX', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p86_1785419789.jpg', 'Active', '2026-07-30 13:56:29', 1),
('B027-C01-P87', 'SATA DATA CABLE', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p87_1785420177.jpg', 'Active', '2026-07-30 14:02:57', 1),
('B027-C01-P88', 'CRYSTAL INTERCOMM', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p88_1785421938.jpg', 'Active', '2026-07-30 14:32:18', 1),
('B027-C01-P89', 'PVC RACK', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p89_1785423046.jpg', 'Active', '2026-07-30 14:50:46', 1),
('B027-C01-P90', '14-26 INCH MONITOR STAND', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p90_1785423407.jpg', 'Active', '2026-07-30 14:56:47', 1),
('B027-C01-P91', 'HDTV VIDEO CAPTURE 4K 30HZ', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p91_1785423675.jpg', 'Active', '2026-07-30 15:01:15', 1),
('B027-C01-P92', 'UTP VIDEO BALUN 8 PORT', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p92_1785425207.jpg', 'Active', '2026-07-30 15:26:47', 1),
('B027-C01-P93', 'UTP VIDEO BALUN 4 PORT', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p93_1785426194.webp', 'Active', '2026-07-30 15:43:14', 1),
('B027-C01-P94', 'MODULAR CRIMPING TOOL', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p94_1785426681.jpg', 'Active', '2026-07-30 15:51:21', 1),
('B027-C01-P95', 'MULTI FUNCTIONAL MODULAR PLUG CRIMPER', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p95_1785426843.jpg', 'Active', '2026-07-30 15:54:03', 1),
('B027-C01-P96', 'WIRE STRIPPER', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p96_1785428978.jpg', 'Active', '2026-07-30 16:29:38', 1),
('B027-C01-P97', 'WIRED BNC (BLACK)', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p97_1785613287.webp', 'Active', '2026-08-01 19:41:27', 1),
('B027-C01-P98', 'RJ45 WATERPROOF ETHERNET CONNECTOR', '', 'B027-C01', 'B027', '../uploads/products/prod_b027-c01-p98_1786022184.png', 'Active', '2026-08-06 13:16:24', 1),
('B027-C03-P01', 'Metal Bullet Stand 10 Inch', '', 'B027-C03', 'B027', '../uploads/products/prod_b027-c03-p01_1785069813.jpg', 'Active', '2026-07-26 12:43:33', 1),
('B027-C03-P02', 'Metal Bullet Stand 12 Inch', '', 'B027-C03', 'B027', '../uploads/products/prod_b027-c03-p02_1785069903.jpg', 'Active', '2026-07-26 12:45:03', 1),
('B027-C03-P03', 'Adjustable Stand I Type 100CM - 200CM (MX-IS1020)', '', 'B027-C03', 'B027', '../uploads/products/prod_c043-p04_1785331265.jpg', 'Active', '2026-07-27 12:46:20', 1),
('B027-C03-P04', 'Adjustable Stand I Type 30CM - 60CM (MX-IS3060)', '', 'B027-C03', 'B027', '../uploads/products/prod_c043-p01_1785331275.jpg', 'Active', '2026-07-27 12:43:52', 1),
('B027-C03-P05', 'Adjustable Stand I Type 60CM - 120CM (MX-IS6012)', '', 'B027-C03', 'B027', '../uploads/products/prod_c043-p02_1785331285.jpg', 'Active', '2026-07-27 12:44:28', 1),
('B027-C03-P06', 'ALUMINIUM SMALL STAND 12CM (MX-AS05A)', '', 'B027-C03', 'B027', '../uploads/products/prod_p-b08-006_1784468457.jpg', 'Active', '2026-06-27 20:58:25', 1),
('B027-C03-P07', 'Adjustable Stand I Type 70CM - 150CM (MX-IS7515)', '', 'B027-C03', 'B027', '../uploads/products/prod_c043-p03_1785331299.jpg', 'Active', '2026-07-27 12:45:08', 1),
('B027-C03-P08', 'DUAL SIDE POLE STAND (MX-PTS)', '', 'B027-C03', 'B027', '../uploads/products/prod_p-b08-004_1784468631.jpg', 'Active', '2026-06-27 20:58:25', 1),
('B027-C03-P09', 'EZVIZ ROBO STAND (MX-ERS)', '', 'B027-C03', 'B027', '../uploads/products/prod_p-b08-003_1784468245.png', 'Active', '2026-06-27 20:58:25', 1),
('B027-C03-P10', 'METAL DOME STAND (MX-MDS401)', '', 'B027-C03', 'B027', '../uploads/products/prod_p-b08-005_1784468906.jpg', 'Active', '2026-06-27 20:58:25', 1),
('B027-C03-P11', 'ROBO STAND 401', '', 'B027-C03', 'B027', '../uploads/products/prod_b027-c03-p11_1785420674.jpg', 'Active', '2026-07-30 14:11:14', 1),
('B027-C03-P12', 'ROBO STAND 402', '', 'B027-C03', 'B027', '../uploads/products/prod_b027-c03-p12_1785420702.jpg', 'Active', '2026-07-30 14:11:42', 1),
('B027-C03-P13', 'L-Type Telescopic Stand for CCTV Camera', '', 'B027-C03', 'B027', '../uploads/products/prod_b027-c03-p13_1785422614.webp', 'Active', '2026-07-30 14:43:34', 1),
('B027-C03-P14', 'WHITE METAL L PIPE STAND 1ft', '', 'B027-C03', 'B027', '../uploads/products/prod_b027-c03-p14_1785422634.jpg', 'Active', '2026-07-30 14:43:54', 1),
('B027-C03-P15', 'ADJUSTABLE ALUMINIUM STAND 2ft', '', 'B027-C03', 'B027', '../uploads/products/prod_b027-c03-p15_1785422684.jpg', 'Active', '2026-07-30 14:44:44', 1),
('B027-C03-P16', 'ADJUSTABLE ALUMINIUM STAND 4FT', '', 'B027-C03', 'B027', '../uploads/products/prod_b027-c03-p16_1785422718.jpg', 'Active', '2026-07-30 14:45:18', 1),
('B027-C06-P01', 'SPIKE 1.5MTRS', '', 'B027-C06', 'B027', '../uploads/products/prod_p-b04-011_1784464787.jpg', 'Active', '2026-06-27 20:58:25', 1),
('B027-C06-P02', 'SPIKE 3MTR', '', 'B027-C06', 'B027', '../uploads/products/prod_p-b04-012_1784464845.jpg', 'Active', '2026-06-27 20:58:25', 1),
('B027-C06-P03', 'SPIKE 5MTR', '', 'B027-C06', 'B027', '../uploads/products/prod_p-b04-013_1784464853.jpg', 'Active', '2026-06-27 20:58:25', 1),
('B027-C06-P04', '3PIN POWER SOCKET', '', 'B027-C06', 'B027', '../uploads/products/prod_p-b08-014_1784468438.jpg', 'Active', '2026-06-27 20:58:25', 1),
('B027-C06-P05', '6SOCKET POWER', '', 'B027-C06', 'B027', '../uploads/products/prod_p-b08-015_1784468449.jpg', 'Active', '2026-06-27 20:58:25', 1),
('B027-C08-P01', 'HDMI 4K 10MTR', '', 'B027-C08', 'B027', '../uploads/products/prod_p-b08-010_1784468791.jpg', 'Active', '2026-06-27 20:58:25', 1),
('B027-C08-P02', 'HDMI 4K 20MTR', '', 'B027-C08', 'B027', '../uploads/products/prod_b027-c08-p02_1785337129.jpg', 'Active', '2026-06-27 20:58:25', 1),
('B027-C08-P03', 'HDMI 4K 3MTR', '', 'B027-C08', 'B027', '../uploads/products/prod_b027-c08-p03_1785337139.jpg', 'Active', '2026-06-27 20:58:25', 1),
('B027-C08-P04', 'HDMI 4K 30MTR', '', 'B027-C08', 'B027', '../uploads/products/prod_b027-c08-p04_1785337134.jpg', 'Active', '2026-06-27 20:58:25', 1),
('B027-C08-P05', 'HDMI 4K 5MTR', '', 'B027-C08', 'B027', '../uploads/products/prod_b027-c08-p05_1785337143.jpg', 'Active', '2026-06-27 20:58:25', 1),
('B027-C08-P06', 'HDMI 4K 15MTR', '', 'B027-C08', 'B027', '../uploads/products/prod_b027-c08-p06_1785337125.jpg', 'Active', '2026-06-27 20:58:25', 1),
('B027-C08-P07', 'HDMI 4K 1.5MTR', '', 'B027-C08', 'B027', '../uploads/products/prod_p-b08-007_1784468771.jpg', 'Active', '2026-06-27 20:58:25', 1),
('B027-C09-P01', '7 INCH HD TFT COLOUR MONITOR', '', 'B027-C09', 'B027', '../uploads/products/prod_b027-c09-p01_1785420739.jpg', 'Active', '2026-07-30 14:08:35', 1),
('B027-C10-P01', 'OUTDOOR METAL RACK BIG', '', 'B027-C10', 'B027', '../uploads/products/prod_b027-c10-p01_1785421717.jpg', 'Active', '2026-07-30 14:28:37', 1),
('B027-C10-P02', 'OUTDOOR METAL RACK SMALL', '', 'B027-C10', 'B027', '../uploads/products/prod_b027-c10-p02_1785421743.jpg', 'Active', '2026-07-30 14:29:03', 1),
('B027-C11-P01', '500GB HARDDISK HDD', '', 'B027-C11', 'B027', '../uploads/products/prod_b027-c11-p01_1785425824.png', 'Active', '2026-07-30 15:37:04', 1),
('B027-C11-P02', '1TB HARDDISK HDD', '', 'B027-C11', 'B027', '../uploads/products/prod_b027-c11-p02_1785425857.png', 'Active', '2026-07-30 15:37:37', 1),
('B027-C11-P03', '2TB HARDDISK HDD', '', 'B027-C11', 'B027', '../uploads/products/prod_b027-c11-p03_1785425877.png', 'Active', '2026-07-30 15:37:57', 1),
('B027-C11-P04', '3TB HARDDISK HDD', '', 'B027-C11', 'B027', '../uploads/products/prod_b027-c11-p04_1785425897.png', 'Active', '2026-07-30 15:38:17', 1),
('B027-C11-P05', '4TB HARDDISK HDD', '', 'B027-C11', 'B027', '../uploads/products/prod_b027-c11-p05_1785425918.png', 'Active', '2026-07-30 15:38:38', 1),
('B027-C12-P01', 'ZEBION 256GB SD CARD', '', 'B027-C12', 'B027', '../uploads/products/prod_b027-c12-p01_1785428646.jpg', 'Active', '2026-07-30 16:24:06', 1),
('B028-C01-P01', 'GEONIX 4CH SMPS', '', 'B028-C01', 'B028', '../uploads/products/prod_b028-c01-p01_1785338866.webp', 'Active', '2026-07-29 15:02:24', 1),
('B028-C02-P01', 'GEONIX 64GB SD CARD', '', 'B028-C02', 'B028', '../uploads/products/prod_b028-c02-p01_1786027255.webp', 'Active', '2026-08-02 11:14:21', 1),
('B029-C01-P01', 'EZVIZ SMART LOCK (DL03 PRO)', '', 'B029-C01', 'B029', '../uploads/products/prod_b029-c01-p01_1785338789.jpg', 'Active', '2026-07-29 15:14:42', 1),
('B029-C02-P01', 'EZVIZ 3MP BULLET CAMERA(CS-H5)', '', 'B029-C02', 'B029', '../uploads/products/prod_b029-c02-p01_1785338558.webp', 'Active', '2026-07-29 15:18:14', 1),
('B03-C08-P04', 'E34Q 3MP', '', 'B03-C08', 'B03', '../uploads/products/prod_b03-c08-p04_1784005138.jpeg', 'Active', '2026-07-14 04:58:58', 1),
('B03-C08-P05', 'E48Q 4MP', '', 'B03-C08', 'B03', '../uploads/products/prod_b03-c08-p05_1784005160.jpeg', 'Active', '2026-07-14 04:59:20', 1),
('B03-C08-P06', 'E28Q 2mp', '', 'B03-C08', 'B03', '../uploads/products/prod_b03-c08-p06_1784005182.jpeg', 'Active', '2026-07-14 04:59:42', 1),
('B030-C01-P01', 'TOSHIBA 2TB HDD HARDDISK', '', 'B030-C01', 'B030', '../uploads/products/prod_b030-c01-p01_1785423793.jpg', 'Active', '2026-07-30 15:03:13', 1),
('B031-C01-P01', 'SEAGATE 1 TB  HDD HARDDISK', '', 'B031-C01', 'B031', '../uploads/products/prod_b031-c01-p01_1785423773.jpg', 'Active', '2026-07-30 15:02:53', 1),
('B032-C01-P01', 'WD 4TB HARDDISK HDD', '', 'B032-C01', 'B032', '../uploads/products/prod_b032-c01-p01_1785423919.webp', 'Active', '2026-07-30 15:05:19', 1),
('B033-C01-P01', 'DAICHI 32GB SD CARD', '', 'B033-C01', 'B033', '../uploads/products/prod_b033-c01-p01_1785428582.jpg', 'Active', '2026-07-30 16:23:02', 1),
('B033-C01-P02', 'DAICHI 64GB SD CARD', '', 'B033-C01', 'B033', '../uploads/products/prod_b033-c01-p02_1785428605.jpg', 'Active', '2026-07-30 16:23:25', 1),
('B033-C02-P01', 'DAICHI SOLAR 4G DUAL LENS CAMERA', '', 'B033-C02', 'B033', '../uploads/products/prod_b033-c02-p01_1786022761.jpeg', 'Active', '2026-08-06 13:26:01', 1),
('B033-C03-P01', 'DAICHI 3MP WIFI-LINKAGE CAMERA', '', 'B033-C03', 'B033', '../uploads/products/prod_b033-c03-p01_1786023875.jpeg', 'Active', '2026-08-06 13:44:35', 1),
('B05-C04-P01', 'COFE WI FI BULLET -3MP', 'COFE WI-FI 3MP BULLET CAMERA', 'B05-C04', 'B05', '../uploads/products/prod_b05-c04-p01_1784640536.jpeg', 'Active', '2026-07-21 13:28:56', 1),
('B05-C04-P02', 'COFE WI FI DOME -3MP', 'COFE WIFI 3MP DOME CAMERA', 'B05-C04', 'B05', '../uploads/products/prod_b05-c04-p02_1784640592.jpeg', 'Active', '2026-07-21 13:29:52', 1),
('B05-C23-P01', 'COFE 4G 3MP BULLET CAMERA', '', 'B05-C23', 'B05', '../uploads/products/prod_b05-c23-p01_1785332031.jpeg', 'Active', '2026-07-27 15:31:05', 1),
('B10-C56-P01', 'LAN TESTER', '', 'B10-C56', 'B10', '../uploads/products/prod_p-b10-009_1784553620.jpg', 'Active', '2026-06-27 20:58:25', 1),
('B10-C56-P02', '120MTR HDMI EXTENDER', '', 'B10-C56', 'B10', '../uploads/products/prod_p-b10-004_1784807231.jpg', 'Active', '2026-06-27 20:58:25', 1),
('B10-C56-P03', '120MTR KVM EXTENDER', '', 'B10-C56', 'B10', '../uploads/products/prod_p-b10-006_1784807284.webp', 'Active', '2026-06-27 20:58:25', 1),
('B10-C56-P04', '60MTR EXTENDER', '', 'B10-C56', 'B10', '../uploads/products/prod_p-b10-003_1784553280.jpg', 'Active', '2026-06-27 20:58:25', 1),
('B10-C56-P05', '60MTR KVM EXTENDER', '', 'B10-C56', 'B10', '../uploads/products/prod_p-b10-005_1784553306.webp', 'Active', '2026-06-27 20:58:25', 1),
('B10-C56-P06', 'HDMI TO VGA CONVERTER', '', 'B10-C56', 'B10', '../uploads/products/prod_p-b10-008_1784553500.webp', 'Active', '2026-06-27 20:58:25', 1),
('B10-C56-P07', 'VGA TO HDMI CONVERTER', '', 'B10-C56', 'B10', '../uploads/products/prod_p-b10-007_1784553665.webp', 'Active', '2026-06-27 20:58:25', 1),
('B10-C56-P08', 'HDMI SP 5MTR', '', 'B10-C56', 'B10', '../uploads/products/prod_p-b10-002_1784553477.jpg', 'Active', '2026-06-27 20:58:25', 1),
('B10-C56-P09', 'HDMI SP 3MTR', '', 'B10-C56', 'B10', '../uploads/products/prod_p-b10-001_1784553364.jpg', 'Active', '2026-06-27 20:58:25', 1),
('B10-C56-P10', 'USB ETHERNET ADAPTER (SP-K408)', '', 'B10-C56', 'B10', '../uploads/products/prod_b10-c56-p10_1785426464.jpg', 'Active', '2026-07-30 15:47:44', 1),
('B13-C30-P01', 'WIRELESS BNC', '', 'B13-C30', 'B13', '../uploads/products/prod_p-b13-005_1784471220.jpg', 'Active', '2026-06-27 20:58:25', 1),
('B18-C52-P01', 'Hikvision DS-D5022QE-D 22-inch Monitor', '', 'B18-C52', 'B18', '../uploads/products/prod_b18-c52-p01_1785419242.jpg', 'Active', '2026-07-30 13:47:22', 1),
('B18-C53-P01', 'HIKVISION BIOMETRIC BS-K1T321MFWX', '', 'B18-C53', 'B18', '../uploads/products/prod_b18-c53-p01_1785420973.jpg', 'Active', '2026-07-30 14:16:13', 1),
('B18-C54-P01', 'HIKVISION 256GB SD CARD', '', 'B18-C54', 'B18', '../uploads/products/prod_b18-c54-p01_1785428671.png', 'Active', '2026-07-30 16:24:31', 1),
('B18-C54-P02', 'HIKVISION 128GB  SD CARD', '', 'B18-C54', 'B18', '../uploads/products/prod_b18-c54-p02_1785428686.jpg', 'Active', '2026-07-30 16:24:46', 1),
('B18-C54-P03', 'HIKVISION 64GB  SD CARD', '', 'B18-C54', 'B18', '../uploads/products/prod_b18-c54-p03_1785428804.jpg', 'Active', '2026-07-30 16:26:44', 1),
('B20-C01-P01', 'TRUEVIEW 4G ROUTER', '', 'B20-C01', 'B20', '../uploads/products/prod_b20-c01-p01_1785332158.jpg', 'Active', '2026-07-21 15:05:56', 1),
('B21-C68-P01', 'HDMI TO VGA', 'HDMI TO VGA converter', 'B21-C68', 'B21', '../uploads/products/prod_b21-c01-p01_1784642929.jpg', 'Active', '2026-07-21 14:08:49', 1),
('B21-C68-P02', 'SECUREYE MEDIA CONVERTER 10/100 PAIR (SMP-S-SMSF-FE)', '', 'B21-C68', 'B21', '../uploads/products/prod_p-b21-011_1784469716.jpg', 'Active', '2026-06-27 20:58:25', 1),
('B21-C68-P03', 'SECUREYE MEDIA CONVERTER 10/100/1000 GIGA (SMP-S-SMSF-GE)', '', 'B21-C68', 'B21', '../uploads/products/prod_p-b21-012_1784469746.jpg', 'Active', '2026-06-27 20:58:25', 1),
('B21-C68-P06', 'SECUREYE WIRELESS N USB ADAPTER FALCON WIFI 300', '', 'B21-C68', 'B21', '../uploads/products/prod_b027-c01-p85_1785419364.jpg', 'Active', '2026-07-30 13:49:24', 1),
('B21-C69-P01', 'SECUREYE 128 GB MEMORY CARD (SD CARD)', '', 'B21-C69', 'B21', '../uploads/products/prod_p-b21-014_1784469514.jpg', 'Active', '2026-06-27 20:58:25', 1),
('B21-C69-P02', 'SECUREYE 64 GB MEMORY CARD(SD CARD)', '', 'B21-C69', 'B21', '../uploads/products/prod_p-b21-013_1784469658.jpg', 'Active', '2026-06-27 20:58:25', 1),
('C013-P01', 'D-LINK 8-Port Gigabit Easy Desktop Switch DGS-1008A', '', 'C013', 'B02', '../uploads/products/prod_c013-p01_1785421342.png', 'Active', '2026-07-30 14:22:22', 1),
('C025-P01', 'VGUARD STABILIZER (VG100 SB)', '', 'C025', 'B12', '../uploads/products/prod_c025-p01_1785340249.jpg', 'Active', '2026-07-29 15:47:38', 1),
('C025-P02', 'VGUARD STABILIZER (VG150 SB)', '', 'C025', 'B12', '../uploads/products/prod_c025-p02_1785340219.jpg', 'Active', '2026-07-29 15:48:31', 1),
('C050-P01', 'HIKVISION 2MP DOME COLOR + AUDIO (DS-2CE70DF3T-PTS)', '', 'C050', 'B18', '../uploads/products/prod_c050-p01_1785338916.webp', 'Active', '2026-07-29 15:11:05', 1),
('C065-P01', 'SECUREYE 4 PORT POE SWITCH (S-4FE2UE-L-NB)', '', 'C065', 'B21', '../uploads/products/prod_c065-p01_1786019849.png', 'Active', '2026-08-06 12:37:29', 1),
('C065-P02', 'SECUREYE 8 PORT POE (S-8FE2UE-L-NB)', '', 'C065', 'B21', '../uploads/products/prod_c065-p02_1786020142.jpg', 'Active', '2026-08-06 12:42:22', 1),
('C065-P03', 'SECUREYE 4 PORT GIGA POE SWITCH (S-4GE2UG1UF-L-NB)', '', 'C065', 'B21', '../uploads/products/prod_c065-p03_1786020536.jpg', 'Active', '2026-08-06 12:48:56', 1),
('C075-P01', 'YADON LITHIUM-X UPS', '', 'C075', 'B11', '../uploads/products/prod_c075-p01_1785427342.jpeg', 'Active', '2026-07-30 16:02:22', 1),
('CPP001', 'CP PLUS 4 CH DVR 2MP (CP-UVR-0401E1-IC2)', '', 'C015', 'B03', '../uploads/products/prod_cpp001_1784450645.jpg', 'Active', '2026-06-27 20:01:16', 1),
('CPP002', 'CP PLUS 8 CH DVR 2MP(CP-UVR-0801E1-IC2)', '', 'C015', 'B03', '../uploads/products/prod_cpp002_1784450727.jpg', 'Active', '2026-06-27 20:01:16', 1),
('CPP003', 'CP PLUS 16 CH DVR 2MP (CP-UVR-1601E1-IC2)', '', 'C015', 'B03', '../uploads/products/prod_cpp003_1784444696.jpg', 'Active', '2026-06-27 20:01:16', 1),
('CPP004', 'CP PLUS 4 CH DVR 5MP (CP-UVR-0401F1-IC)', '', 'C015', 'B03', '../uploads/products/prod_cpp004_1784450666.jpg', 'Active', '2026-06-27 20:01:16', 1),
('CPP005', 'CP PLUS 8 CH DVR 5MP (CP-UVR-0801F1-IC2)', '', 'C015', 'B03', '../uploads/products/prod_cpp005_1784450744.jpg', 'Active', '2026-06-27 20:01:16', 1),
('CPP006', 'CP PLUS 16 CH DVR 5MP', '', 'C015', 'B03', '../uploads/products/prod_cpp006_1784450323.jpg', 'Active', '2026-06-27 20:01:16', 1),
('CPP007', 'CP PLUS 32 CH DVR 5MP', '', 'C015', 'B03', '../uploads/products/prod_cpp007_1784450592.jpg', 'Active', '2026-06-27 20:01:16', 1),
('CPP008', 'CP PLUS 4 CH NVR (CP-UNR-104F1)', '', 'C016', 'B03', '../uploads/products/prod_cpp008_1784450683.png', 'Active', '2026-06-27 20:01:16', 1),
('CPP009', 'CP PLUS 8 CH NVR (CP-UNR-108F1)', '', 'C016', 'B03', '../uploads/products/prod_cpp009_1784450760.png', 'Active', '2026-06-27 20:01:16', 1),
('CPP010', 'CP PLUS 16 CH NVR (CP-UNR-4K2161-V4)', '', 'C016', 'B03', '../uploads/products/prod_cpp010_1784450345.jpg', 'Active', '2026-06-27 20:01:16', 1),
('CPP011', 'CP PLUS 32 CH NVR (CP-UNR-4K4322-V4)', '', 'C016', 'B03', '../uploads/products/prod_cpp011_1784450619.jpg', 'Active', '2026-06-27 20:01:16', 1),
('CPP012', 'CP PLUS 64 CH NVR', '', 'C016', 'B03', '../uploads/products/prod_cpp012_1784450708.jpg', 'Active', '2026-06-27 20:01:16', 1),
('CPP013', 'CP PLUS 2.4MP BULLET B/W + AUDIO (CP-URC-TC24PL3C)', '', 'C017', 'B03', '../uploads/products/prod_cpp013_1784456230.jpg', 'Active', '2026-06-27 20:01:16', 1),
('CPP014', 'CP PLUS 2.4MP DOME B/W + AUDIO (CP-URC-DC24PL3C)', '', 'C017', 'B03', '../uploads/products/prod_cpp014_1784450454.jpg', 'Active', '2026-06-27 20:01:16', 1),
('CPP015', 'CP PLUS 2.4MP BULLET ILLUMAX + AUDIO (CP-URC-TC24PL3C-L-V2)', '', 'C017', 'B03', '../uploads/products/prod_cpp015_1784456443.jpg', 'Active', '2026-06-27 20:01:16', 1),
('CPP016', 'CP PLUS 2.4MP DOME ILLUMAX + AUDIO (CP-URC-DC24PL3C-L-V2)', '', 'C017', 'B03', '../uploads/products/prod_cpp016_1784450472.jpg', 'Active', '2026-06-27 20:01:16', 1),
('CPP017', 'CP PLUS 2.4MP BULLET COLOR + AUDIO (CP-GPC-TA24PL2C-SE-V2)', '', 'C017', 'B03', '../uploads/products/prod_cpp017_1784456369.jpg', 'Active', '2026-06-27 20:01:16', 1),
('CPP018', 'CP PLUS 2MP DOME COLOR + AUDIO (CP-GPC-DA24PL2C-SE-V2)', '', 'C017', 'B03', '../uploads/products/prod_cpp018_1784450504.jpg', 'Active', '2026-06-27 20:01:16', 1),
('CPP019', 'CP PLUS 2MP BULLET COLOR + 2 WAY AUDIO (CP-HD-UMC-TA24L3-L)', '', 'C017', 'B03', '../uploads/products/prod_cpp019_1784456531.png', 'Active', '2026-06-27 20:01:16', 1),
('CPP020', 'CP PLUS 2MP DOME COLOR + 2 WAY AUDIO (CP-HD-UMC-DA24L3-L)', '', 'C017', 'B03', '../uploads/products/prod_cpp020_1784450490.webp', 'Active', '2026-06-27 20:01:16', 1),
('CPP021', 'CP PLUS 5MP BULLET B/W + AUDIO (CP-USC-TC51PL3C-0360)', '', 'C017', 'B03', '../uploads/products/prod_cpp021_1784457574.png', 'Active', '2026-06-27 20:01:16', 1),
('CPP022', 'CP PLUS 5MP DOME B/W + AUDIO (CP-USC-DC51PL3C-0360)', '', 'C017', 'B03', '../uploads/products/prod_cpp022_1784457634.png', 'Active', '2026-06-27 20:01:16', 1),
('CPP023', 'CP PLUS 5MP BULLET ILLUMAX + AUDIO (CP-URC-TC51PL3C-L-0360)', '', 'C017', 'B03', '../uploads/products/prod_cpp023_1784457610.jpg', 'Active', '2026-06-27 20:01:16', 1),
('CPP024', 'CP PLUS 5MP DOME ILLUMAX + AUDIO (CP-URC-DC51PL3C-L-0360)', '', 'C017', 'B03', '../uploads/products/prod_cpp024_1784457645.png', 'Active', '2026-06-27 20:01:16', 1),
('CPP025', 'CP PLUS 2MP IP ILLUMAX BULLET C+A- (CP-UNC-TA21L3C-LQ)', '', 'C018', 'B03', '../uploads/products/prod_cpp025_1784457093.png', 'Active', '2026-06-27 20:01:16', 1),
('CPP026', 'CP PLUS 4MP IP IR DOME (CP-UNC-DA41PL3-0360)', '', 'C018', 'B03', '../uploads/products/prod_cpp026_1784457532.png', 'Active', '2026-06-27 20:01:16', 1),
('CPP027', 'CP PLUS 2MP IP B/W DOME(CP-UNC-DA21L3C-Q-0280/0360)', '', 'C018', 'B03', '../uploads/products/prod_cpp027_1784457048.png', 'Active', '2026-06-27 20:01:16', 1),
('CPP028', 'CP PLUS 2MP IP B/W BULLET (CP-UNC-TA21L3C-Q)', '', 'C018', 'B03', '../uploads/products/prod_cpp028_1784456953.png', 'Active', '2026-06-27 20:01:16', 1),
('CPP029', 'CP PLUS 2MP IP ILLUMAX DOME (CP-UNC-DA21L3C-LQ)', '', 'C018', 'B03', '../uploads/products/prod_cpp029_1784457113.webp', 'Active', '2026-06-27 20:01:16', 1),
('CPP030', 'CP PLUS 2MP IP ILLUMAX BULLET (CP-UNC-TA21L3C-LQ)', '', 'C018', 'B03', '../uploads/products/prod_cpp030_1784457060.jpg', 'Active', '2026-06-27 20:01:16', 1),
('CPP031', 'CP PLUS 2MP IP 2-WAY AUDIO DOME (CP-UNC-DA21L3B-LQ)', '', 'C018', 'B03', '../uploads/products/prod_cpp031_1784456723.png', 'Active', '2026-06-27 20:01:16', 1),
('CPP032', 'CP PLUS 2MP IP 2-WAY AUDIO BULLET (CP-UNC-TA21L3B-LQ)', '', 'C018', 'B03', '../uploads/products/prod_cpp032_1784456804.jpg', 'Active', '2026-06-27 20:01:16', 1),
('CPP033', 'CP PLUS 2MP IP B/W 6MM LENS BULLET (CP-UNC-TA21L6C-Q-0600)', '', 'C018', 'B03', '../uploads/products/prod_cpp033_1784456878.jpg', 'Active', '2026-06-27 20:01:16', 1),
('CPP034', 'CP PLUS 4MP IP B/W DOME (CP-UNC-DA41L3C-D-Q)', '', 'C018', 'B03', '../uploads/products/prod_cpp034_1784457323.png', 'Active', '2026-06-27 20:01:16', 1),
('CPP035', 'CP PLUS 4MP IP B/W BULLET (CP-UNC-TA41L3C-D-Q)', '', 'C018', 'B03', '../uploads/products/prod_cpp035_1784457310.png', 'Active', '2026-06-27 20:01:16', 1),
('CPP036', 'CP PLUS 4MP IP ILLUMAX DOME (CP-UNC-DA41L3C-D-LQ)', '', 'C018', 'B03', '../uploads/products/prod_cpp036_1784457487.png', 'Active', '2026-06-27 20:01:16', 1),
('CPP037', 'CP PLUS 4MP IP ILLUMAX BULLET (CP-UNC-TA41L3C-D-LQ)', '', 'C018', 'B03', '../uploads/products/prod_cpp037_1784457384.jpg', 'Active', '2026-06-27 20:01:16', 1),
('CPP038', 'CP PLUS 4MP IP 2-WAY AUDIO DOME (CP-UNC-DA41L3B-D-LQ)', '', 'C018', 'B03', '../uploads/products/prod_cpp038_1784457178.webp', 'Active', '2026-06-27 20:01:16', 1),
('CPP039', 'CP PLUS 4MP IP 2-WAY AUDIO BULLET (CP-UNC-TA41L3B-D-LQ)', '', 'C018', 'B03', '../uploads/products/prod_cpp039_1784457167.jpg', 'Active', '2026-06-27 20:01:16', 1),
('CPP040', 'CP PLUS 4MP IP B/W 6MM LENS BULLET (CP-UNC-TA41L6C-D-Q-0600)', '', 'C018', 'B03', '../uploads/products/prod_cpp040_1784457286.jpg', 'Active', '2026-06-27 20:01:16', 1),
('CPP041', 'CP PLUS 6MP IP ILLUMAX DOME (CP-UNC-DA61L3C-LQ)', '', 'C018', 'B03', '../uploads/products/prod_cpp041_1784457720.jpg', 'Active', '2026-06-27 20:01:16', 1),
('CPP042', 'CP PLUS 6MP IP ILLUMAX BULLET (CP-UNC-TA61L3C-LQ)', '', 'C018', 'B03', '../uploads/products/prod_cpp042_1784457686.jpg', 'Active', '2026-06-27 20:01:16', 1),
('CPP043', 'CP PLUS 8MP IP ILLUMAX DOME (CP-UNC-DA81L3C-LQ)', '', 'C018', 'B03', '../uploads/products/prod_cpp043_1784457753.png', 'Active', '2026-06-27 20:01:16', 1),
('CPP044', 'CP PLUS 8MP IP ILLUMAX BULLET (CP-UNC-TA81L3C-LQ)', '', 'C018', 'B03', '../uploads/products/prod_cpp044_1784457743.jpg', 'Active', '2026-06-27 20:01:16', 1),
('CPP047', '90MTR INDOOR CABLE', '', 'C021', 'B03', '../uploads/products/prod_cpp047_1784450277.jpg', 'Active', '2026-06-27 20:01:16', 1),
('CPP048', '180MTR INDOOR CABLE', '', 'C021', 'B03', '../uploads/products/prod_cpp048_1784450216.jpg', 'Active', '2026-06-27 20:01:16', 1),
('CPP049', '90MTR OUTDOOR CABLE', '', 'C021', 'B03', '../uploads/products/prod_cpp049_1784450292.jpg', 'Active', '2026-06-27 20:01:16', 1),
('CPP050', '180MTR OUTDOOR CABLE', '', 'C021', 'B03', '../uploads/products/prod_cpp050_1784450232.jpg', 'Active', '2026-06-27 20:01:16', 1),
('CPP051', '305 CAT6 CABLE CCA', '', 'C021', 'B03', '../uploads/products/prod_cpp051_1784450250.webp', 'Active', '2026-06-27 20:01:16', 1),
('CPP052', '305 CAT6 CABLE COPPER', '', 'C021', 'B03', '../uploads/products/prod_cpp052_1784450265.webp', 'Active', '2026-06-27 20:01:16', 1),
('DLK001', 'D LINK 2U MINI RACK (NWR-3535)', '', 'C008', 'B02', '../uploads/products/prod_dlk001_1784457887.jpg', 'Active', '2026-06-27 20:11:59', 1),
('DLK002', 'D LINK 2U RACK', '', 'C008', 'B02', '../uploads/products/prod_dlk002_1784457949.png', 'Active', '2026-06-27 20:11:59', 1),
('DLK003', 'D LINK 4U RACK UNLOADED', '', 'C008', 'B02', '../uploads/products/prod_dlk003_1784458711.jpg', 'Active', '2026-06-27 20:11:59', 1),
('DLK004', 'D LINK 4U BIG RACK ONLY SPIKE', '', 'C008', 'B02', '../uploads/products/prod_dlk004_1784458703.jpg', 'Active', '2026-06-27 20:11:59', 1),
('DLK005', 'D LINK 6U RACK (SPIKE&FAN)', '', 'C008', 'B02', '../uploads/products/prod_dlk005_1784458793.png', 'Active', '2026-06-27 20:11:59', 1),
('DLK006', 'D LINK 9U RACK', '', 'C008', 'B02', '../uploads/products/prod_dlk006_1784459112.png', 'Active', '2026-06-27 20:11:59', 1),
('DLK007', 'D LINK 12U RACK', '', 'C008', 'B02', '../uploads/products/prod_dlk007_1784457845.png', 'Active', '2026-06-27 20:11:59', 1),
('DLK008', 'D LINK RACK TRAY', '', 'C008', 'B02', '../uploads/products/prod_dlk008_1784459215.png', 'Active', '2026-06-27 20:11:59', 1),
('DLK009', 'D LINK 3+1 180MTRS STANDARD CABLE', '', 'C009', 'B02', '../uploads/products/prod_dlk009_1784457962.png', 'Active', '2026-06-27 20:11:59', 1),
('DLK010', 'D LINK 3+1 90 MTRS OUTDOOR CABLE', '', 'C009', 'B02', '../uploads/products/prod_dlk010_1784458146.png', 'Active', '2026-06-27 20:11:59', 1),
('DLK011', 'D LINK 3+1 90MTRS STANDARD CABLE', '', 'C009', 'B02', '../uploads/products/prod_dlk011_1784458245.jpg', 'Active', '2026-06-27 20:11:59', 1),
('DLK012', 'D LINK CAT6 100 MTRS', '', 'C009', 'B02', '../uploads/products/prod_dlk012_1784459026.jpg', 'Active', '2026-06-27 20:11:59', 1),
('DLK013', 'D LINK CAT6 305MTR CABLE', '', 'C009', 'B02', '../uploads/products/prod_dlk013_1784458996.jpg', 'Active', '2026-06-27 20:11:59', 1),
('DLK014', 'D LINK CAT6 305MTR CABLE OUTDOOR', '', 'C009', 'B02', '../uploads/products/prod_dlk014_1784459011.jpg', 'Active', '2026-06-27 20:11:59', 1),
('DLK015', 'DLINK CAT6 NEWW', '', 'C009', 'B02', '../uploads/products/prod_dlk015_1784459592.jpg', 'Active', '2026-06-27 20:11:59', 1),
('DLK016', 'D LINK RJ45 PINS', '', 'C010', 'B02', '../uploads/products/prod_dlk016_1784459229.jpg', 'Active', '2026-06-27 20:11:59', 1),
('DLK017', 'D LINK SPIKE', '', 'C010', 'B02', '../uploads/products/prod_dlk017_1784459414.jpg', 'Active', '2026-06-27 20:11:59', 1),
('DLK018', 'DLINK BACK BOX', '', 'C010', 'B02', '../uploads/products/prod_dlk018_1784459538.jpg', 'Active', '2026-06-27 20:11:59', 1),
('DLK019', 'DLINK FACEPLATE DUAL', '', 'C010', 'B02', '../uploads/products/prod_dlk019_1784459644.png', 'Active', '2026-06-27 20:11:59', 1),
('DLK020', 'DLINK FACEPLATE SINGLE', '', 'C010', 'B02', '../uploads/products/prod_dlk020_1784459867.jpg', 'Active', '2026-06-27 20:11:59', 1),
('DLK021', 'DLINK KEYSTONE', '', 'C010', 'B02', '../uploads/products/prod_dlk021_1784459915.jpg', 'Active', '2026-06-27 20:11:59', 1),
('DLK022', 'DLINK PATCHCARD 1MTR', '', 'C010', 'B02', '../uploads/products/prod_dlk022_1784460007.png', 'Active', '2026-06-27 20:11:59', 1),
('DLK023', 'DLINK PATCHCARD 2MTR', '', 'C010', 'B02', '../uploads/products/prod_dlk023_1784460016.png', 'Active', '2026-06-27 20:11:59', 1),
('DLK024', 'D LINK 4 PORT POE (DES-F1006P-E)', '', 'C011', 'B02', '../uploads/products/prod_dlk024_1784458284.png', 'Active', '2026-06-27 20:11:59', 1),
('DLK025', 'D LINK 8PORT POE (DES-F1010P-E)', '', 'C011', 'B02', '../uploads/products/prod_dlk025_1784459079.png', 'Active', '2026-06-27 20:11:59', 1),
('DLK026', 'D LINK 16 PORT POE SWITCH (DGS-F1018P-E)', '', 'C011', 'B02', '../uploads/products/prod_dlk026_1784457859.jpg', 'Active', '2026-06-27 20:11:59', 1),
('DLK029', 'D LINK 5 PORT DESKTOP SWITCH (DES-1005C)', '', 'C013', 'B02', '../uploads/products/prod_dlk029_1784458758.png', 'Active', '2026-06-27 20:11:59', 1),
('DLK030', 'D LINK 8 PORT DESKTOP SWITCH (DES-1008C)', '', 'C013', 'B02', '../uploads/products/prod_dlk030_1784458859.png', 'Active', '2026-06-27 20:11:59', 1),
('P-B04-001', 'ERD 12V-1AMP PLUGIN', '', 'C024', 'B04', '../uploads/products/prod_p-b04-001_1784464517.webp', 'Active', '2026-06-27 20:58:25', 1),
('P-B04-002', 'ERD 12V-2AMP PLUGIN', '', 'C024', 'B04', '../uploads/products/prod_p-b04-002_1784464546.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B04-003', 'ERD 12V-3AMP PLUGIN', '', 'C024', 'B04', '../uploads/products/prod_p-b04-003_1784464574.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B04-004', 'ERD 12V-3AMP DESKTOP SWITCH', '', 'C024', 'B04', '../uploads/products/prod_p-b04-004_1784464557.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B04-005', 'ERD 12V - 5AMP ADAPTOR', '', 'C024', 'B04', '../uploads/products/prod_p-b04-005_1784464506.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B04-006', 'ERD 16CH SMPS', '', 'C024', 'B04', '../uploads/products/prod_p-b04-006_1784464644.jpeg', 'Active', '2026-06-27 20:58:25', 1),
('P-B04-007', 'ERD 48V-1.25AMP POE ADAPTER', '', 'C024', 'B04', '../uploads/products/prod_p-b04-007_1784464655.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B04-008', 'ERD 4CH SMPS', '', 'C024', 'B04', '../uploads/products/prod_p-b04-008_1784464693.webp', 'Active', '2026-06-27 20:58:25', 1),
('P-B04-009', 'ERD 5V 1 AMP PLUGIN', '', 'C024', 'B04', '../uploads/products/prod_p-b04-009_1784464774.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B04-010', 'ERD 8CH SMPS', '', 'C024', 'B04', '../uploads/products/prod_p-b04-010_1784464707.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B05-001', 'COFE DUAL LENS 3+3 SOLAR CAMERA (CF-4G-PTSL24-A-DL)', '', 'C022', 'B05', '../uploads/products/prod_p-b05-001_1784455497.jpg', 'Active', '2026-06-27 20:58:25', 0),
('P-B07-001', 'LAPCARE 19\' MONITOR (HDMI&VGA) (LM195WDH)', '', 'C040', 'B07', '../uploads/products/prod_p-b07-001_1784468160.webp', 'Active', '2026-06-27 20:58:25', 1),
('P-B07-002', 'LAPCARE WIRELESS MOUSE', '', 'C041', 'B07', '../uploads/products/prod_p-b07-002_1784468168.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B09-001', '4MP PTZ 30X ZOOM 250MTRS', '', 'C047', 'B09', '../uploads/products/prod_p-b09-001_1784469287.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B11-001', '4 PORT POE & 2UP LINK SWITCH WITH UPS (Y100-4P2UP/B)', '', 'C074', 'B11', '../uploads/products/prod_p-b11-001_1785425770.webp', 'Active', '2026-06-27 20:58:25', 1),
('P-B11-002', '8 PORT POE & 2UP LINK SWITCH WITH UPS (Y100-8P2UP/B)', '', 'C074', 'B11', '../uploads/products/prod_p-b11-002_1784471346.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B11-003', 'YADON 4PORT POE SWITCH WITH (BACKUP)', '', 'C074', 'B11', '../uploads/products/prod_p-b11-003_1784471367.jpg', 'Active', '2026-06-27 20:58:25', 1);
INSERT INTO `products` (`pid`, `pname`, `pdescription`, `pcat`, `brandid`, `pimage`, `status`, `created_at`, `display_status`) VALUES
('P-B11-004', 'YADON 8PORT POE SWITCH WITH (BACKUP)', '', 'C074', 'B11', '../uploads/products/prod_p-b11-004_1784471385.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B11-005', 'YADON 12V UPS', '', 'C075', 'B11', '../uploads/products/prod_p-b11-005_1784471252.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B12-001', 'VGUARD STABILIZER (VG50 SB)', '', 'C025', 'B12', '../uploads/products/prod_p-b12-001_1784471107.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B12-002', 'VGUARD UPS', '', 'C026', 'B12', '../uploads/products/prod_p-b12-002_1784471117.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B13-001', 'VOLTAIC CAT 6 305 OUTDOOR LDPE', '', 'C027', 'B13', '../uploads/products/prod_p-b13-001_1784471165.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B13-002', 'VOLTAIC CAT6 OUTDOOR 305 KATVISION', '', 'C027', 'B13', '../uploads/products/prod_p-b13-002_1784471175.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B13-003', '2U RACK', '', 'C028', 'B13', '../uploads/products/prod_p-b13-003_1784471147.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B13-004', '4U RACK', '', 'C028', 'B13', '../uploads/products/prod_p-b13-004_1784471155.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B15-001', 'DAHUA 4 CH DVR 2MP (DH-XVR4B04-I)', '', 'C032', 'B15', '../uploads/products/prod_p-b15-001_1784460733.webp', 'Active', '2026-06-27 20:58:25', 1),
('P-B15-002', 'DAHUA 8 CH DVR 2MP (DH-XVR4B08-I)', '', 'C032', 'B15', '../uploads/products/prod_p-b15-002_1784460973.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B15-003', 'DAHUA 16 CH DVR 2MP (DH-XVR4B16-I)', '', 'C032', 'B15', '../uploads/products/prod_p-b15-003_1784460219.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B15-004', 'DAHUA 4 CH DVR 5MP (DH-XVR4B04H-I)', '', 'C032', 'B15', '../uploads/products/prod_p-b15-004_1784460747.webp', 'Active', '2026-06-27 20:58:25', 1),
('P-B15-005', 'DAHUA 8 CH DVR 5MP (DH-XVR4B08H-I)', '', 'C032', 'B15', '../uploads/products/prod_p-b15-005_1784461010.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B15-006', 'DAHUA 16 CH DVR 5MP (DH-XVR4B16H-I)', '', 'C032', 'B15', '../uploads/products/prod_p-b15-006_1784460228.webp', 'Active', '2026-06-27 20:58:25', 1),
('P-B15-007', 'DAHUA 4 CH 4K DVR (DH-XVR5104HS-4KL-I3)', '', 'C032', 'B15', '../uploads/products/prod_p-b15-007_1784460722.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B15-008', 'DAHUA 8 CH 4K DVR (DH-XVR5116H-4KL-I3)', '', 'C032', 'B15', '../uploads/products/prod_p-b15-008_1784460936.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B15-009', 'DAHUA 4 CH NVR (DHI-NVR1104HS-S3/H)', '', 'C033', 'B15', '../uploads/products/prod_p-b15-009_1784460760.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B15-010', 'DAHUA 8 CH NVR (DHI-NVR1108HS-S3/H)', '', 'C033', 'B15', '../uploads/products/prod_p-b15-010_1784461043.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B15-011', 'DAHUA 16 CH 4K NVR (DHI-NVR2116HS-4KS3)', '', 'C033', 'B15', '../uploads/products/prod_p-b15-011_1784460205.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B15-012', 'DAHUA 32 CH 4K NVR (DHI-NVR4232-4KS3)', '', 'C033', 'B15', '../uploads/products/prod_p-b15-012_1784460617.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B15-013', 'DAHUA 2MP BULLET B/W + AUDIO (DH-HAC-B1A21P-A)', '', 'C034', 'B15', '../uploads/products/prod_p-b15-013_1784460354.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B15-014', 'DAHUA 2MP DOME B/W + AUDIO (DH-HAC-T1A21P)', '', 'C034', 'B15', '../uploads/products/prod_p-b15-014_1784460532.webp', 'Active', '2026-06-27 20:58:25', 1),
('P-B15-015', 'DAHUA 2MP BULLET SMART DUAL LIGHT + AUDIO (DH-HAC-B1A21P-U-IL-A)', '', 'C034', 'B15', '../uploads/products/prod_p-b15-015_1784460518.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B15-016', 'DAHUA 2MP DOME SMART DUAL LIGHT + AUDIO (DH-HAC-T1A21P-U-IL-A)', '', 'C034', 'B15', '../uploads/products/prod_p-b15-016_1784460567.webp', 'Active', '2026-06-27 20:58:25', 1),
('P-B15-017', 'DAHUA 2MP BULLET COLOR + AUDIO (DH-HAC-HFW1209CLP-A-LED)', '', 'C034', 'B15', '../uploads/products/prod_p-b15-017_1784460362.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B15-018', 'DAHUA 2MP DOME COLOR + AUDIO (DH-HAC-HDW1209CLQP-A-LED)', '', 'C034', 'B15', '../uploads/products/prod_p-b15-018_1784460556.webp', 'Active', '2026-06-27 20:58:25', 1),
('P-B15-019', 'DAHUA 5MP BULLET B/W + AUDIO (DH-HAC-B1A51P)', '', 'C034', 'B15', '../uploads/products/prod_p-b15-019_1784460829.webp', 'Active', '2026-06-27 20:58:25', 1),
('P-B15-020', 'DAHUA 5MP DOME B/W + AUDIO (DH-HAC-HFW1501CMP)', '', 'C034', 'B15', '../uploads/products/prod_p-b15-020_1784460898.webp', 'Active', '2026-06-27 20:58:25', 1),
('P-B15-021', 'DAHUA 5MP BULLET COLOR + AUDIO (DH-HAC-HFW1509CLP-A-LED)', '', 'C034', 'B15', '../uploads/products/prod_p-b15-021_1784460847.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B15-022', 'DAHUA 5MP DOME COLOR + AUDIO (DH-HAC-HDW1509CLQP-A-LED)', '', 'C034', 'B15', '../uploads/products/prod_p-b15-022_1784460912.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B16-001', 'TP LINK 4 CH NVR (NVR1004H)', '', 'C035', 'B16', '../uploads/products/prod_p-b16-001_1784470431.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B16-002', 'TP LINK 8 CH NVR (NVR1008H)', '', 'C035', 'B16', '../uploads/products/prod_p-b16-002_1784470596.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B16-003', 'TP LINK 16 CH NVR 2 SATA(NVR2016H)', '', 'C035', 'B16', '../uploads/products/prod_p-b16-003_1784470197.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B16-004', 'TP LINK 32 CH NVR 4 SATA (NVR4032H)', '', 'C035', 'B16', '../uploads/products/prod_p-b16-004_1784470422.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B16-005', 'TP LINK 4+2 POE 10/100 (LS106LP)', '', 'C036', 'B16', '../uploads/products/prod_p-b16-005_1784470491.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B16-006', 'TP LINK 4+2 POE 10/100 (LS106P)', '', 'C036', 'B16', '../uploads/products/prod_p-b16-006_1784470519.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B16-007', 'TP LINK 4+1 FULL GIGA POE 10/100/1000 (LS105GP)', '', 'C036', 'B16', '../uploads/products/prod_p-b16-007_1784470465.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B16-008', 'TP LINK 8+2 POE 10/100 (LS-110P)', '', 'C036', 'B16', '../uploads/products/prod_p-b16-008_1784470738.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B16-009', 'TP LINK 8+2 UPLINK GIGA POE (LS1210P)', '', 'C036', 'B16', '../uploads/products/prod_p-b16-009_1784470767.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B16-010', 'TP LINK 8+1+1 SFP FULL GIGA POE (SG1210P)', '', 'C036', 'B16', '../uploads/products/prod_p-b16-010_1784470664.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B16-011', 'TP LINK 16PORT GIGA POE (SG1218MPE)', '', 'C036', 'B16', '../uploads/products/prod_p-b16-011_1784470293.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B16-012', 'TP LINK 18PORT GIGA POE RACKMOUNT (SG1218MP)', '', 'C036', 'B16', '../uploads/products/prod_p-b16-012_1784470314.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B16-013', 'TP LINK 24PORT GIGA POE (SG1428PE)', '', 'C036', 'B16', '../uploads/products/prod_p-b16-013_1784470408.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B16-014', 'TP LINK POE ADAPTER (POE2412)', '', 'C036', 'B16', '../uploads/products/prod_p-b16-014_1784470962.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B16-015', 'TP LINK 16+2UPLINK GIGA POE (SL1218MP)', '', 'C036', 'B16', '../uploads/products/prod_p-b16-015_1784470273.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B16-016', 'TP LINK 16PORT GIGA POE (SG1218MPE) + 2 SFP', '', 'C036', 'B16', '../uploads/products/prod_p-b16-016_1784470304.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B16-017', 'TP LINK 24 PORT POE FULL GIGA +2 UPLINK (SG2428P)', '', 'C036', 'B16', '../uploads/products/prod_p-b16-017_1784470372.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B16-018', 'TP LINK 5 PORT GIGA SWITCH (SG1005D)', '', 'C037', 'B16', '../uploads/products/prod_p-b16-018_1784470559.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B16-019', 'TP LINK 8 PORT GIGA SWITCH (SG1008D)', '', 'C037', 'B16', '../uploads/products/prod_p-b16-019_1784470607.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B16-020', 'TP LINK 8 PORT GIGA SWITCH (SG108E)', '', 'C037', 'B16', '../uploads/products/prod_p-b16-020_1784470635.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B16-021', 'TP LINK 16 PORT GIGA SWITCH (SG1016D)', '', 'C037', 'B16', '../uploads/products/prod_p-b16-021_1784470234.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B16-022', 'TP-LINK 16PORT GIGA SWITCH (SG116E)', '', 'C037', 'B16', '../uploads/products/prod_p-b16-022_1784471014.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B16-023', 'TP LINK 24 PORT GIGA SWITCH (SG1024DE)', '', 'C037', 'B16', '../uploads/products/prod_p-b16-023_1784470349.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B16-024', 'TP LINK ACCESS POINT SEALING(EAP115) AC1350', '', 'C038', 'B16', '../uploads/products/prod_p-b16-024_1784470938.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B16-025', 'TP ACCESS POINT SEALING (EAP225)', '', 'C038', 'B16', '../uploads/products/prod_p-b16-025_1784470092.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B16-026', 'TP LINK ACCESS POINT (EAP610) AX1800', '', 'C038', 'B16', '../uploads/products/prod_p-b16-026_1784470905.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B16-027', 'TP LINK ACCESS POINT (CPE210)', '', 'C038', 'B16', '../uploads/products/prod_p-b16-027_1784470813.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B16-028', 'TP LINK ACCESS POINT 300MBPS (CPE510)', '', 'C038', 'B16', '../uploads/products/prod_p-b16-028_1784470913.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B16-029', 'TP LINK ACCESS POINT (CPE610)', '', 'C038', 'B16', '../uploads/products/prod_p-b16-029_1784470842.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B16-030', 'TP LINK ACCESS POINT (CPE710)', '', 'C038', 'B16', '../uploads/products/prod_p-b16-030_1784470877.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B16-031', 'TP LINK VPN GIGABIT VPN ROUTER (ER605)', '', 'C039', 'B16', '../uploads/products/prod_p-b16-031_1784470990.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B16-032', 'TPL N300 ROUTER (WR850N)', '', 'C039', 'B16', '../uploads/products/prod_p-b16-032_1784471033.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B16-033', 'TP LINK AC1200 (ARCHER C6)', '', 'C039', 'B16', '../uploads/products/prod_p-b16-033_1784470787.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B18-001', 'HIKVISION 4 CH DVR 2MP (DS-7104HGHI-M1/T)', '', 'C048', 'B18', '../uploads/products/prod_p-b18-001_1784465975.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B18-002', 'HIKVISION 8 CH DVR 2MP (DS-7108HGHI-M1/T)', '', 'C048', 'B18', '../uploads/products/prod_p-b18-002_1784467983.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B18-003', 'HIKVISION 16 CH DVR 2MP (DS-7116HGHI-K1)', '', 'C048', 'B18', '../uploads/products/prod_p-b18-003_1784465079.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B18-004', 'HIKVISION 4 CH DVR 5MP (IDS-7104HQHI-M1/S)', '', 'C048', 'B18', '../uploads/products/prod_p-b18-004_1784466007.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B18-005', 'HIKVISION 8 CH DVR 5MP (IDS-7108HQHI-M1/T)', '', 'C048', 'B18', '../uploads/products/prod_p-b18-005_1784468012.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B18-006', 'HIKVISION 16 CH DVR 5MP (IDS-7116HQHI-M1/S)', '', 'C048', 'B18', '../uploads/products/prod_p-b18-006_1784465153.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B18-007', 'HIKVISION 32 CH DVR 5MP', '', 'C048', 'B18', '../uploads/products/prod_p-b18-007_1784465864.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B18-008', 'HIKVISION 4 CH NVR Q1/M (DS-7104NI-Q1/M)', '', 'C049', 'B18', '../uploads/products/prod_p-b18-008_1784466092.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B18-009', 'HIKVISION 8 CH NVR Q1/M (DS-7108NI-Q1/M)', '', 'C049', 'B18', '../uploads/products/prod_p-b18-009_1784468102.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B18-010', 'HIKVISION 16 CH NVR Q1/M (DS-7116NI-Q1/M)', '', 'C049', 'B18', '../uploads/products/prod_p-b18-010_1784465322.webp', 'Active', '2026-06-27 20:58:25', 1),
('P-B18-011', 'HIKVISION 4 CH NVR Q1 (DS-7604NI-Q1)', '', 'C049', 'B18', '../uploads/products/prod_p-b18-011_1784466059.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B18-012', 'HIKVISION 8 CH NVR Q1 (DS-7608NI-Q1)', '', 'C049', 'B18', '../uploads/products/prod_p-b18-012_1784468069.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B18-013', 'HIKVISION 16 CH NVR Q1 (DS-7616NI-Q1)', '', 'C049', 'B18', '../uploads/products/prod_p-b18-013_1784465268.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B18-014', 'HIKVISION 4 CH NVR K1 (DS-7604NXI-K1)', '', 'C049', 'B18', '../uploads/products/prod_p-b18-014_1784466034.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B18-015', 'HIKVISION 8 CH NVR K1 (DS-7608NXI-K1)', '', 'C049', 'B18', '../uploads/products/prod_p-b18-015_1784468039.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B18-016', 'HIKVISION 16 CH NVR K1 (DS-7616NXI-K1)', '', 'C049', 'B18', '../uploads/products/prod_p-b18-016_1784465202.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B18-017', 'HIKVISION 16 CH NVR K2 (DS-7616NXI-K2)', '', 'C049', 'B18', '../uploads/products/prod_p-b18-017_1784465230.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B18-018', 'HIKVISION 32 CH NVR K2 (DS-7632NXI-K2)', '', 'C049', 'B18', '../uploads/products/prod_p-b18-018_1784465897.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B18-019', 'HIKVISION 32 CH NVR K4', '', 'C049', 'B18', '../uploads/products/prod_p-b18-019_1784465940.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B18-020', 'HIKVISION 64 CH NVR K4', '', 'C049', 'B18', '../uploads/products/prod_p-b18-020_1784466376.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B18-021', 'HIKVISION 64 CH NVR K8', '', 'C049', 'B18', '../uploads/products/prod_p-b18-021_1784467824.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B18-022', 'HIKVISION 2MP BULLET B/W + AUDIO (DS-2CE16D0T-ITPFS)', '', 'C050', 'B18', '../uploads/products/prod_p-b18-022_1784465361.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B18-023', 'HIKVISION 2MP DOME B/W + AUDIO (DS-2CE76D0T-ITPFS)', '', 'C050', 'B18', '../uploads/products/prod_p-b18-023_1784465489.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B18-024', 'HIKVISION 2MP BULLET HYBRID + AUDIO (DS-2CE16D0T-LPFS)', '', 'C050', 'B18', '../uploads/products/prod_p-b18-024_1784465455.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B18-025', 'HIKVISION 2MP DOME HYBRID + AUDIO (DS-2CE76D0T-LPFS)', '', 'C050', 'B18', '../uploads/products/prod_p-b18-025_1784465841.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B18-026', 'HIKVISION 2MP BULLET COLOR + AUDIO (DS-2CE10DF0T-PFS)', '', 'C050', 'B18', '../uploads/products/prod_p-b18-026_1784465428.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B18-027', 'HIKVISION 2MP DOME COLOR + AUDIO (DS-2CE70DF0T-PFS)', '', 'C050', 'B18', '../uploads/products/prod_p-b18-027_1784465807.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B18-028', 'HIKVISION 2MP BULLET COLOR + 2 WAY AUDIO (DS-2CE16D0T-LPTS)', '', 'C050', 'B18', '../uploads/products/prod_p-b18-028_1784465403.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B18-029', 'HIKVISION 2MP DOME COLOR + 2 WAY AUDIO (DS-2CE70D0T-PTLTS)', '', 'C050', 'B18', '../uploads/products/prod_p-b18-029_1784465511.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B18-030', 'HIKVISION 5MP BULLET B/W + AUDIO (DS-2CE16H0T-ITPFS)', '', 'C050', 'B18', '../uploads/products/prod_p-b18-030_1784466125.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B18-031', 'HIKVISION 5MP DOME B/W + AUDIO (DS-2CE76H0T-ITPFS)', '', 'C050', 'B18', '../uploads/products/prod_p-b18-031_1784466266.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B18-032', 'HIKVISION 5MP BULLET HYBRID + AUDIO (DS-2CE16K0T-LPFS)', '', 'C050', 'B18', '../uploads/products/prod_p-b18-032_1784466238.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B18-033', 'HIKVISION 5MP DOME HYBRID + AUDIO (DS-2CE76K0T-LPFS)', '', 'C050', 'B18', '../uploads/products/prod_p-b18-033_1784466339.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B18-034', 'HIKVISION 5MP BULLET COLOR + AUDIO (DS-2CE10KF0T-PFS)', '', 'C050', 'B18', '../uploads/products/prod_p-b18-034_1784466207.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B18-035', 'HIKVISION 5MP DOME COLOR + AUDIO (DS-2CE70KF0T-PFS)', '', 'C050', 'B18', '../uploads/products/prod_p-b18-035_1784466317.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B18-036', 'HIKVISION 5MP BULLET COLOR + 2 WAY AUDIO', '', 'C050', 'B18', '../uploads/products/prod_p-b18-036_1784466173.webp', 'Active', '2026-06-27 20:58:25', 1),
('P-B18-037', 'HIKVISION 5MP DOME COLOR + 2 WAY AUDIO', '', 'C050', 'B18', '../uploads/products/prod_p-b18-037_1784466295.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B18-038', '180MTR 3+1 OUTDOOR CABLE', '', 'C051', 'B18', '../uploads/products/prod_p-b18-038_1784465062.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B19-001', 'ZEBRONICS 15 INCH MONITOR', '', 'C056', 'B19', '../uploads/products/prod_p-b19-001_1784471410.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B19-002', 'ZEBRONICS 17 INCH MONITOR', '', 'C056', 'B19', '../uploads/products/prod_p-b19-002_1784471515.webp', 'Active', '2026-06-27 20:58:25', 1),
('P-B19-003', 'ZEBRONICS 19 INCH MONITOR', '', 'C056', 'B19', '../uploads/products/prod_p-b19-003_1784471527.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B19-004', 'ZEBRONICS 22 INCH MONITOR', '', 'C056', 'B19', '../uploads/products/prod_p-b19-004_1784471555.webp', 'Active', '2026-06-27 20:58:25', 1),
('P-B19-005', 'ZEBRONICS 4 CH SMPS', '', 'C057', 'B19', '../uploads/products/prod_p-b19-005_1784471640.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B19-006', 'ZEBRONICS 8 CH SMPS', '', 'C057', 'B19', '../uploads/products/prod_p-b19-006_1784471659.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B19-007', 'ZEBRONICS 16 CH SMPS', '', 'C057', 'B19', '../uploads/products/prod_p-b19-007_1784471440.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B19-008', 'ZEBRONICS 3+1 CABLE', '', 'C058', 'B19', '../uploads/products/prod_p-b19-008_1784471607.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B19-009', 'ZEBRONICS CAT6 INDOOR', '', 'C058', 'B19', '../uploads/products/prod_p-b19-009_1784471692.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B19-010', 'ZEBRONICS UPS', '', 'C059', 'B19', '../uploads/products/prod_p-b19-010_1784471720.webp', 'Active', '2026-06-27 20:58:25', 1),
('P-B21-008', 'SECUREYE 8PORT GIGA 8+2+2SF (SSP-8GE-2UG-2UF-NB)', '', 'C065', 'B21', '../uploads/products/prod_p-b21-008_1784469689.webp', 'Active', '2026-06-27 20:58:25', 1),
('P-B21-009', 'SECUREYE 16 PORT POE+2UP GIGA+1SFC (16FE-2UG-1UF-300W-NB)', '', 'C065', 'B21', '../uploads/products/prod_p-b21-009_1784469635.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B21-010', 'SECUREYE 16 PORT POE FULLGIGA+2GIGA+1SFC (16GE-2UG-1UF-300W-NB)', '', 'C065', 'B21', '../uploads/products/prod_p-b21-010_1784469566.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B22-001', 'SECURUS 6CH NVR (SS-N8006RVA-H1-M5)', '', 'C069', 'B22', '../uploads/products/prod_p-b22-001_1784469946.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B22-002', 'SECURUS 9CH NVR(SS-N8009RVA-H1-M5)', '', 'C069', 'B22', '../uploads/products/prod_p-b22-002_1784469966.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B22-003', 'SECURUS 16CH NVR (SS-N8016RVA-H1-M5)', '', 'C069', 'B22', '../uploads/products/prod_p-b22-003_1784469787.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B22-004', 'SECURUS 16CH NVR 2SATA (SS-N8016RV-H2-M5)', '', 'C069', 'B22', '../uploads/products/prod_p-b22-004_1784469801.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B22-005', 'SECURUS 32CH NVR (SS-N8032RV-H2-M5)', '', 'C069', 'B22', '../uploads/products/prod_p-b22-005_1784469842.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B22-006', 'SECURUS 4CH DVR(SS-8041-TPHD-M1(SF))', '', 'C070', 'B22', '../uploads/products/prod_p-b22-006_1784469899.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B22-007', 'SECURUS 8CH DVR (SS-8081-TPHD-M1(SF))', '', 'C070', 'B22', '../uploads/products/prod_p-b22-007_1784469954.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B22-008', 'SECURUS 3MP IP C+A BULLET (SS-NC20L2XP-HSUTL-M3(S))', '', 'C071', 'B22', '../uploads/products/prod_p-b22-008_1784469879.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B22-009', 'SECURUS 3MP IP C+A DOME(SS-NC15DXLP-HSUTL-M3)', '', 'C071', 'B22', '../uploads/products/prod_p-b22-009_1784469889.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B22-010', 'SECURUS 3MP IP 4 CABEL(NC15DXLP-CSF-M3(S) 4 CABEL)', '', 'C071', 'B22', '../uploads/products/prod_p-b22-010_1784469857.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B22-011', 'SECURUS 3MP IP C+2WAY DOME(NC1DXLP-CSUTL-M3(SM)SL)', '', 'C071', 'B22', '../uploads/products/prod_p-b22-011_1784469869.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B22-012', 'SECURUS 5MP IP BULLET(NC20L2XP-CSFM(S)(MII))', '', 'C071', 'B22', '../uploads/products/prod_p-b22-012_1784469911.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B22-013', 'SECURUS 5MP IP BULLETS(NC50L5CPXM5(4CABEL))', '', 'C071', 'B22', '../uploads/products/prod_p-b22-013_1784469922.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B22-014', 'SECURUS 5MP IP DOME(NC15DXLP-CSF-M5(S)/2.8MM)', '', 'C071', 'B22', '../uploads/products/prod_p-b22-014_1784469936.png', 'Active', '2026-06-27 20:58:25', 1),
('P-B22-015', 'SECURUS 2.4MP COLOUR+AUDIO BULLET(SS-20L2XP-TPCSD-M2.4(S))', '', 'C072', 'B22', '../uploads/products/prod_p-b22-015_1784469814.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B22-016', 'SECURUS 2.4MP COLOUR+AUDIO DOME(SS-15DXP-TPCSD-M2.4)', '', 'C072', 'B22', '../uploads/products/prod_p-b22-016_1784469828.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P-B22-017', 'SECURUS PTZ 5MP(SS-NE15XSP-M5-4CABEL)', '', 'C073', 'B22', '../uploads/products/prod_p-b22-017_1784469977.jpg', 'Active', '2026-06-27 20:58:25', 1),
('P019', 'ANWIZ 10 CHANNEL NVR', '10 Channel H.265+ smart stream network video management device.', 'C002', 'B01', '../uploads/products/prod_p019_1784461158.jpg', 'Active', '2026-06-27 19:31:53', 0),
('P020', 'ANWIZ 16 CHANNEL NVR', '16 Channel High-performance multi-drive security recording deck.', 'C002', 'B01', '../uploads/products/prod_p020_1784461175.jpg', 'Active', '2026-06-27 19:31:53', 0),
('P021', 'ANWIZ 4+2 NORMAL', 'Standard 4 Port PoE Switch featuring dual uplink termination modules.', 'C003', 'B01', '../uploads/products/prod_p021_1784449832.jpg', 'Active', '2026-06-27 19:31:53', 1),
('P022', 'ANWIZ 8+2 NORMAL', 'Standard 8 Port PoE Switch featuring dual uplink termination modules.', 'C003', 'B01', '../uploads/products/prod_p022_1784449961.jpg', 'Active', '2026-06-27 19:31:53', 1),
('P023', 'ANWIZ 8+2 UPLINK GIGA', '8 Port Fast Ethernet PoE device with dual structural Gigabit uplink networks.', 'C003', 'B01', '../uploads/products/prod_p023_1784449976.jpg', 'Active', '2026-06-27 19:31:53', 1),
('P024', 'ANWIZ 4+1 FULL GIGA', '4 Port Full Gigabit PoE operational switch with a gigabit baseline hub.', 'C003', 'B01', '../uploads/products/prod_p024_1784449809.jpg', 'Active', '2026-06-27 19:31:53', 1),
('P025', 'ANWIZ 8+1 FULL GIGA', '8 Port Full Gigabit PoE operational switch with a gigabit baseline hub.', 'C003', 'B01', '../uploads/products/prod_p025_1784449935.jpg', 'Active', '2026-06-27 19:31:53', 1),
('P026', 'ANWIZ 4+2 FULL GIGA', '4 Port Full Gigabit routing switch with continuous dual giga uplinks.', 'C003', 'B01', '../uploads/products/prod_p026_1784449819.jpg', 'Active', '2026-06-27 19:31:53', 1),
('P027', 'ANWIZ 8+2 FULL GIGA', '8 Port Full Gigabit routing switch with continuous dual giga uplinks.', 'C003', 'B01', '../uploads/products/prod_p027_1784449948.jpg', 'Active', '2026-06-27 19:31:53', 1),
('P028', 'ANWIZ 4+2+2 SFP GIGA', '4 Port PoE system with dual Gigabit uplinks and dual high-speed SFP fiber bays.', 'C003', 'B01', '../uploads/products/prod_p028_1784449865.jpg', 'Active', '2026-06-27 19:31:53', 1),
('P029', 'ANWIZ 8+2+2 SFP GIGA', '8 Port PoE system with dual Gigabit uplinks and dual high-speed SFP fiber bays.', 'C003', 'B01', '../uploads/products/prod_p029_1784449985.jpg', 'Active', '2026-06-27 19:31:53', 1),
('P030', 'ANWIZ 16+2 UPLINK GIGA', '16 Port PoE distribution spine framework with dual gigabit link-up panels.', 'C003', 'B01', '../uploads/products/prod_p030_1784456003.jpg', 'Active', '2026-06-27 19:31:53', 1),
('P031', 'ANWIZ 16+2 FULL GIGA', '16 Port High load industrial full gigabit operational tracking switcher grid.', 'C003', 'B01', '../uploads/products/prod_p031_1784461609.jpg', 'Active', '2026-06-27 19:31:53', 0),
('P032', 'ANWIZ 3+1 BLAK COPPER CABEL', 'Structural 3+1 solid black copper camera connection transmission wire.', 'C004', 'B01', '../uploads/products/prod_p032_1784449763.jpg', 'Active', '2026-06-27 19:31:53', 1),
('P033', 'ANWIZ 3+1 BLAK COPPER PREMIUM CABEL', 'Heavy shielding 3+1 premium grade video audio coaxial copper roll.', 'C004', 'B01', '../uploads/products/prod_p033_1784449785.jpg', 'Active', '2026-06-27 19:31:53', 1),
('P034', 'ANWIZ 3+1 WHITE INDOOR COPPER CABEL', 'Clean aesthetic 3+1 white indoor routing composite system cable.', 'C004', 'B01', '../uploads/products/prod_p034_1784449797.jpg', 'Active', '2026-06-27 19:31:53', 1),
('P035', 'ANWIZ CAT6 BLACK CABEL', 'Standard outer protective jacket CAT6 black network interface roll.', 'C004', 'B01', '../uploads/products/prod_p035_1784449998.jpg', 'Active', '2026-06-27 19:31:53', 1),
('P036', 'ANWIZ CAT6 BLACK COPPER CABEL', 'Pure structural bare copper conductor solid black CAT6 deployment reel.', 'C004', 'B01', '../uploads/products/prod_p036_1784454473.jpg', 'Active', '2026-06-27 19:31:53', 1),
('P037', 'ANWIZ CAT6 INDOOR GREY CABEL', 'Flexible interior framework deployment gray CAT6 structured mesh roll.', 'C004', 'B01', '../uploads/products/prod_p037_1784454631.png', 'Active', '2026-06-27 19:31:53', 1),
('P038', 'ANWIZ CAT6 INDOOR YELLOW COPPER CABEL', 'Solid bare copper high-visibility yellow core CAT6 indoor channel line.', 'C004', 'B01', '../uploads/products/prod_p038_1784454753.png', 'Active', '2026-06-27 19:31:53', 1),
('P039', 'ANWIZ 2U RACK HEAVY', 'Heavy gauge fortified metal enclosure 2U compact system framework cabinet.', 'C005', 'B01', '../uploads/products/prod_p039_1784454292.jpeg', 'Active', '2026-06-27 19:31:53', 1),
('P040', 'ANWIZ 4U RACk 2nd', 'Heavy gauge structural wall mount 4U multi component ventilation locker.', 'C005', 'B01', '../uploads/products/prod_p040_1784449921.jpg', 'Active', '2026-06-27 19:31:53', 1),
('P041', 'ANWIZ 2U RACK 2nd', 'Standard form layout 2U storage box with security locks.', 'C005', 'B01', '../uploads/products/prod_p041_1784449742.jpg', 'Active', '2026-06-27 19:31:53', 1),
('P042', 'ANWIZ 4U RACK XT', 'Extended depth specialized structural layout 4U deployment rack panel.', 'C005', 'B01', '../uploads/products/prod_p042_1784461666.jpg', 'Active', '2026-06-27 19:31:53', 1),
('P043', '4CH SMPS', 'Centralized regulated multi terminal 4 Channel 12V power supply box.', 'C006', 'B01', '../uploads/products/prod_p043_1784449614.png', 'Active', '2026-06-27 19:31:53', 1),
('P044', '8CH SMPS', 'Centralized surge isolated multi terminal 8 Channel 12V power matrix hub.', 'C006', 'B01', '../uploads/products/prod_p044_1784449503.jpg', 'Active', '2026-06-27 19:31:53', 1),
('P045', '16CH SMPS', 'Heavy load industrial distributed multi terminal 16 Channel power system control module.', 'C006', 'B01', '../uploads/products/prod_p045_1784444482.png', 'Active', '2026-06-27 19:31:53', 1),
('P046', '12W 1 AMPS', '1 Ampere structural wall power module converter line.', 'C007', 'B01', '../uploads/products/prod_p046_1784454171.png', 'Active', '2026-06-27 19:31:53', 1),
('P047', '12W 2 AMPS', '2 Ampere regulated constant voltage high execution wall adapter.', 'C007', 'B01', '../uploads/products/prod_p047_1784454180.png', 'Active', '2026-06-27 19:31:53', 1),
('P048', '12W 3 AMPS', '3 Ampere performance stabilizer line configuration adapter module.', 'C007', 'B01', '../uploads/products/prod_p048_1784454199.png', 'Active', '2026-06-27 19:31:53', 1),
('P049', '12W 5 AMPS', 'High output 5 Ampere safety thermal power execution converter brick.', 'C007', 'B01', '../uploads/products/prod_p049_1784454207.png', 'Active', '2026-06-27 19:31:53', 1);

-- --------------------------------------------------------

--
-- Table structure for table `strategic_partners`
--

CREATE TABLE `strategic_partners` (
  `id` int(11) NOT NULL,
  `partner_name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `strategic_partners`
--

INSERT INTO `strategic_partners` (`id`, `partner_name`, `image`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(5, 'Secureye', 'assets/img/brands/Secureye.png', 5, 1, '2026-08-13 17:32:11', '2026-08-13 17:32:11'),
(6, 'TP-Link', 'assets/img/brands/tplink.png', 6, 1, '2026-08-13 17:32:11', '2026-08-13 17:32:11'),
(7, 'D-Link', 'assets/img/brands/dlink.png', 7, 1, '2026-08-13 17:32:11', '2026-08-13 17:32:11'),
(8, 'Prama', 'assets/img/brands/Prama.png', 8, 1, '2026-08-13 17:32:11', '2026-08-13 17:32:11'),
(9, 'Dada', 'assets/img/brands/dada.png', 9, 1, '2026-08-13 17:32:11', '2026-08-13 17:32:11'),
(10, 'Yadon', 'assets/img/brands/yadon.png', 10, 1, '2026-08-13 17:32:11', '2026-08-13 17:32:11'),
(11, 'Seagate', 'assets/img/brands/Seagate.png', 11, 1, '2026-08-13 17:32:11', '2026-08-13 17:32:11'),
(12, 'Western Digital', 'assets/img/brands/Westerndigital.png', 12, 1, '2026-08-13 17:32:11', '2026-08-13 17:32:11'),
(13, 'Toshiba', 'assets/img/brands/Toshiba.png', 13, 1, '2026-08-13 17:32:11', '2026-08-13 17:32:11'),
(14, 'ERD', 'assets/img/brands/erd.png', 14, 1, '2026-08-13 17:32:11', '2026-08-13 17:32:11'),
(15, 'Hikvision', 'uploads/partners/partner_1787208020_9682.png', 0, 1, '2026-08-20 06:40:20', '2026-08-20 06:40:20'),
(16, 'Dhaua', 'uploads/partners/partner_1787208053_7102.png', 0, 1, '2026-08-20 06:40:53', '2026-08-20 06:40:53'),
(17, 'Securus', 'uploads/partners/partner_1787208091_3097.png', 0, 1, '2026-08-20 06:41:31', '2026-08-20 06:41:31'),
(18, 'CP Plus', 'uploads/partners/partner_1787208123_9223.png', 0, 1, '2026-08-20 06:42:03', '2026-08-20 06:42:03');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `profile_image`) VALUES
(1, 'pvcsec', '$2y$10$hEbjA77ShQTy.gXB4ZTuRucjvcn5wxnzYx.9AmDr9Qnfa6VV7N2yu', 'uploads/profiles/avatar_pvcsec_1787491434.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`brandid`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`cid`),
  ADD KEY `idx_brand` (`brandid`);

--
-- Indexes for table `hero_slides`
--
ALTER TABLE `hero_slides`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`pid`),
  ADD KEY `idx_category` (`pcat`),
  ADD KEY `idx_brand` (`brandid`);

--
-- Indexes for table `strategic_partners`
--
ALTER TABLE `strategic_partners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `hero_slides`
--
ALTER TABLE `hero_slides`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `strategic_partners`
--
ALTER TABLE `strategic_partners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `category`
--
ALTER TABLE `category`
  ADD CONSTRAINT `fk_category_brand` FOREIGN KEY (`brandid`) REFERENCES `brands` (`brandid`) ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_product_brand` FOREIGN KEY (`brandid`) REFERENCES `brands` (`brandid`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_product_category` FOREIGN KEY (`pcat`) REFERENCES `category` (`cid`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
