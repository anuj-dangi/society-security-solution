-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 03, 2025 at 04:33 PM
-- Server version: 8.0.43
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `society_security`
--

-- --------------------------------------------------------

--
-- Table structure for table `building_table`
--

DROP TABLE IF EXISTS `building_table`;
CREATE TABLE IF NOT EXISTS `building_table` (
  `building_id` int NOT NULL AUTO_INCREMENT,
  `building_name` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `total_flats` int NOT NULL,
  PRIMARY KEY (`building_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `building_table`
--

INSERT INTO `building_table` (`building_id`, `building_name`, `address`, `total_flats`) VALUES
(1, 'Sunrise Apartments', 'Sector 12, Hyderabad', 20),
(2, 'Ocean View Towers', 'Coastal Road, Vizag', 15),
(3, 'Greenwood Residency', 'Green Park, Bangalore', 18),
(4, 'Mount abu', 'Block B', 12);

-- --------------------------------------------------------

--
-- Table structure for table `flat_details`
--

DROP TABLE IF EXISTS `flat_details`;
CREATE TABLE IF NOT EXISTS `flat_details` (
  `flat_id` int NOT NULL AUTO_INCREMENT,
  `building_id` int DEFAULT NULL,
  `flat_no` varchar(10) NOT NULL,
  `flat_area` float DEFAULT NULL,
  `ownership_status` enum('Owner','Tenant') DEFAULT NULL,
  PRIMARY KEY (`flat_id`),
  UNIQUE KEY `flat_no` (`flat_no`),
  KEY `building_id` (`building_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `flat_details`
--

INSERT INTO `flat_details` (`flat_id`, `building_id`, `flat_no`, `flat_area`, `ownership_status`) VALUES
(1, 1, 'S-101', 1200, 'Owner'),
(2, 1, 'S-102', 1100, 'Owner'),
(3, 2, 'O-201', 1000, 'Tenant'),
(4, 3, 'G-301', 1300, 'Owner');

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_details`
--

DROP TABLE IF EXISTS `maintenance_details`;
CREATE TABLE IF NOT EXISTS `maintenance_details` (
  `maintenance_id` int NOT NULL AUTO_INCREMENT,
  `flat_id` int DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `description` text,
  `due_status` enum('Paid','Due') DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `payment_mode` enum('Cash','Online','Cheque') DEFAULT NULL,
  PRIMARY KEY (`maintenance_id`),
  KEY `flat_id` (`flat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `maintenance_details`
--

INSERT INTO `maintenance_details` (`maintenance_id`, `flat_id`, `amount`, `description`, `due_status`, `payment_date`, `payment_mode`) VALUES
(1, 1, 1200.00, NULL, 'Paid', '2025-09-01', 'Online'),
(2, 2, 1100.00, NULL, 'Paid', NULL, 'Cash'),
(3, 3, 1000.00, NULL, 'Due', '2025-09-10', 'Online'),
(4, 4, 1500.00, 'Plumber bathroom knob fix', 'Paid', '2025-10-27', 'Online');

-- --------------------------------------------------------

--
-- Table structure for table `normal_visitor`
--

DROP TABLE IF EXISTS `normal_visitor`;
CREATE TABLE IF NOT EXISTS `normal_visitor` (
  `visitor_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `building_name` varchar(100) NOT NULL,
  `flat_no` varchar(10) NOT NULL,
  `phone_no` varchar(15) NOT NULL,
  `purpose` varchar(225) NOT NULL,
  `status` enum('Pending','Approved','Denied') NOT NULL,
  `visit_time` datetime NOT NULL,
  PRIMARY KEY (`visitor_id`),
  KEY `flat_no` (`flat_no`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `normal_visitor`
--

INSERT INTO `normal_visitor` (`visitor_id`, `name`, `building_name`, `flat_no`, `phone_no`, `purpose`, `status`, `visit_time`) VALUES
(1, 'Amit Verma', 'Sunrise Apartments', 'S-101', '9998887776', 'Delivery', 'Approved', '2025-10-09 10:00:00'),
(2, 'Neha Sharma', 'Sunrise Apartments', 'S-102', '8889997775', 'Friend Visit', 'Pending', '2025-10-09 11:30:00'),
(3, 'Rahul Singh', 'Ocean View Towers', 'O-201', '7778889990', 'Maintenance', 'Approved', '2025-10-10 09:00:00'),
(4, 'Priya Nair', 'Greenwood Residency', 'G-301', '6665554443', 'Delivery', 'Approved', '2025-10-10 11:00:00'),
(24, 'asdf', 'Sunrise Apartments', 'S-102', '9876543210', 'Friend', 'Pending', '2025-10-27 17:49:09'),
(25, 'asdf', 'Sunrise Apartments', 'S-102', '9876543210', 'Friend', 'Pending', '2025-10-27 17:49:26'),
(26, 'asdf', 'Greenwood Residency', 'G-301', '9876543210', 'Friend', 'Approved', '2025-10-27 17:52:54'),
(27, 'asdf', 'Greenwood Residency', 'G-301', '9876543210', 'Friend', 'Denied', '2025-10-27 18:07:19'),
(28, 'asdf', '', 'G-301', '9876543210', 'Friend asdf', 'Approved', '2025-10-27 18:10:01'),
(29, 'asdf', '', 'G-301', '9876543210', 'Friend asdf adsf', 'Approved', '2025-10-27 18:15:39'),
(30, 'sffadfytjry', 'Greenwood Residency', 'G-301', '9876543210', 'asd', 'Denied', '2025-10-27 18:23:15'),
(31, 'sffadfytjry', 'Greenwood Residency', 'G-301', '9876543210', 'asd', 'Approved', '2025-10-27 18:41:20'),
(32, 'Chand kumar', 'Ocean View Towers', 'O-201', '7894561123', 'Friend', 'Approved', '2025-10-27 18:49:05'),
(33, 'ajay', 'Greenwood Residency', 'G-301', '1234567890', 'Freind', 'Approved', '2025-10-28 09:49:13'),
(34, '649', 'Greenwood Residency', 'G-301', '1234567890', 'asdf', 'Approved', '2025-10-28 10:08:46'),
(35, 'anuj', 'Greenwood Residency', 'G-301', '7894561230', 'family', 'Pending', '2025-10-28 10:13:28'),
(36, 'anuj', 'Greenwood Residency', 'G-301', '7894561230', 'family', 'Pending', '2025-10-28 10:14:19'),
(37, 'anuj', 'Greenwood Residency', 'G-301', '7894561230', 'family', 'Pending', '2025-10-28 10:14:20'),
(38, 'anuj', 'Greenwood Residency', 'G-301', '7894561230', 'family', 'Pending', '2025-10-28 10:14:26'),
(39, 'anuj', 'Greenwood Residency', 'G-301', '7894561230', 'family', 'Pending', '2025-10-28 10:14:37'),
(40, 'anuj', 'Greenwood Residency', 'G-301', '7894561230', 'family', 'Pending', '2025-10-28 10:14:38'),
(41, 'anuj', 'Greenwood Residency', 'G-301', '7894561230', 'family', 'Approved', '2025-10-28 10:14:44'),
(42, 'anuj', 'Greenwood Residency', 'G-301', '7894561230', 'family', 'Pending', '2025-10-28 10:15:14');

-- --------------------------------------------------------

--
-- Table structure for table `regular_vendors`
--

DROP TABLE IF EXISTS `regular_vendors`;
CREATE TABLE IF NOT EXISTS `regular_vendors` (
  `vendor_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `work_type` varchar(50) DEFAULT NULL,
  `security_code` varchar(20) DEFAULT NULL,
  `flat_id` int DEFAULT NULL,
  `check_in` datetime DEFAULT NULL,
  `check_out` datetime DEFAULT NULL,
  PRIMARY KEY (`vendor_id`),
  KEY `flat_id` (`flat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `regular_vendors`
--

INSERT INTO `regular_vendors` (`vendor_id`, `name`, `work_type`, `security_code`, `flat_id`, `check_in`, `check_out`) VALUES
(1, 'Ramesh', 'Electrician', 'SEC123', 1, '2025-10-09 09:30:00', '2025-10-09 10:30:00'),
(2, 'Suresh', 'Cleaner', 'SEC124', 2, '2025-10-09 11:00:00', '2025-10-09 12:00:00'),
(3, 'Ramesh', 'Electrician', 'SEC123', 1, '2025-10-27 16:52:31', '2025-10-27 16:52:36'),
(4, 'Anuj Dangi', 'Plumber', 'SEC125', 4, '2025-10-27 16:53:28', '2025-10-27 16:53:32');

-- --------------------------------------------------------

--
-- Table structure for table `resident_details`
--

DROP TABLE IF EXISTS `resident_details`;
CREATE TABLE IF NOT EXISTS `resident_details` (
  `resident_id` int NOT NULL AUTO_INCREMENT,
  `flat_id` int DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `phone_no` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('Admin','Supervisor','Resident') DEFAULT NULL,
  PRIMARY KEY (`resident_id`),
  KEY `flat_id` (`flat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `resident_details`
--

INSERT INTO `resident_details` (`resident_id`, `flat_id`, `name`, `phone_no`, `email`, `username`, `password`, `role`) VALUES
(1, 1, 'Admin User', '9999999999', 'admin@society.com', 'admin', 'admin123', 'Admin'),
(2, 2, 'Supervisor User', '8888888888', 'supervisor@society.com', 'supervisor', 'super123', 'Supervisor'),
(3, 3, 'Ravi Kumar', '7777777777', 'ravi@society.com', 'ravi', 'ravi123', 'Resident'),
(4, 4, 'Neha Sharma', '6666666666', 'neha@society.com', 'neha', 'neha123', 'Resident'),
(6, 3, 'wife', '7854961234', 'wife@gmail.com', 'wife', 'wife123', 'Resident'),
(7, 3, 'son', '7894561123', '23mcce15@uohy.ac.in', 'son', 'Son@1234', 'Resident');

-- --------------------------------------------------------

--
-- Table structure for table `staff_details`
--

DROP TABLE IF EXISTS `staff_details`;
CREATE TABLE IF NOT EXISTS `staff_details` (
  `staff_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `role` enum('Security','Cleaning','Gardener','Temple','Clubhouse','Other') NOT NULL,
  `phone_no` varchar(15) DEFAULT NULL,
  `shift_time` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `added_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`staff_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `staff_details`
--

INSERT INTO `staff_details` (`staff_id`, `name`, `role`, `phone_no`, `shift_time`, `email`, `added_on`) VALUES
(1, 'Ramesh Kumar', 'Security', '9876543210', '6 AM - 2 PM', 'ramesh.security@example.com', '2025-10-25 19:48:08'),
(2, 'Sita Devi', 'Cleaning', '9123456780', '7 AM - 3 PM', 'sita.cleaning@example.com', '2025-10-25 19:48:08'),
(3, 'Vikram Singh', 'Gardener', '9988776655', '5 AM - 1 PM', 'vikram.gardener@example.com', '2025-10-25 19:48:08'),
(4, 'Anita Sharma', 'Temple', '9012345678', '8 AM - 12 PM', 'anita.temple@example.com', '2025-10-25 19:48:08'),
(5, 'Rajesh Patel', 'Clubhouse', '9876123456', '10 AM - 6 PM', 'rajesh.clubhouse@example.com', '2025-10-25 19:48:08'),
(6, 'Sunita Reddy', 'Cleaning', '9123456700', '2 PM - 10 PM', 'sunita.cleaning@example.com', '2025-10-25 19:48:08'),
(7, 'Manoj Verma', 'Security', '9988776600', '2 PM - 10 PM', 'manoj.security@example.com', '2025-10-25 19:48:08'),
(8, 'Priya Joshi', 'Gardener', '9012345600', '6 AM - 2 PM', 'priya.gardener@example.com', '2025-10-25 19:48:08'),
(9, 'Deepak Kumar', 'Clubhouse', '9876501234', '6 PM - 10 PM', 'deepak.clubhouse@example.com', '2025-10-25 19:48:08');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `flat_details`
--
ALTER TABLE `flat_details`
  ADD CONSTRAINT `flat_details_ibfk_1` FOREIGN KEY (`building_id`) REFERENCES `building_table` (`building_id`) ON DELETE CASCADE;

--
-- Constraints for table `maintenance_details`
--
ALTER TABLE `maintenance_details`
  ADD CONSTRAINT `maintenance_details_ibfk_1` FOREIGN KEY (`flat_id`) REFERENCES `flat_details` (`flat_id`) ON DELETE CASCADE;

--
-- Constraints for table `normal_visitor`
--
ALTER TABLE `normal_visitor`
  ADD CONSTRAINT `normal_visitor_ibfk_1` FOREIGN KEY (`flat_no`) REFERENCES `flat_details` (`flat_no`) ON DELETE CASCADE;

--
-- Constraints for table `regular_vendors`
--
ALTER TABLE `regular_vendors`
  ADD CONSTRAINT `regular_vendors_ibfk_1` FOREIGN KEY (`flat_id`) REFERENCES `flat_details` (`flat_id`) ON DELETE CASCADE;

--
-- Constraints for table `resident_details`
--
ALTER TABLE `resident_details`
  ADD CONSTRAINT `resident_details_ibfk_1` FOREIGN KEY (`flat_id`) REFERENCES `flat_details` (`flat_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
