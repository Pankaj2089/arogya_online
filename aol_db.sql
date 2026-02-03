-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 03, 2026 at 07:01 AM
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
-- Database: `aol_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bed_distributions`
--

CREATE TABLE `bed_distributions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `department_id` bigint(20) UNSIGNED NOT NULL,
  `gender` enum('M','F') DEFAULT NULL,
  `bed_no` varchar(50) NOT NULL,
  `bed_status` enum('available','occupied','reserved','maintenance') NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bed_distributions`
--

INSERT INTO `bed_distributions` (`id`, `department_id`, `gender`, `bed_no`, `bed_status`, `status`, `created_date`) VALUES
(1, 2, 'M', 'A-123', 'available', 1, '2026-02-03 04:29:19');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `status`, `created_date`) VALUES
(1, 'OPD', 1, '2026-02-01 02:52:58'),
(2, 'LAB', 1, '2026-02-01 02:53:12');

-- --------------------------------------------------------

--
-- Table structure for table `designations`
--

CREATE TABLE `designations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `diseases`
--

CREATE TABLE `diseases` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `dept_id` bigint(20) UNSIGNED NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `diseases`
--

INSERT INTO `diseases` (`id`, `name`, `dept_id`, `status`, `created_date`) VALUES
(1, 'Cardiovascular', 2, 1, '2026-02-03 05:44:33'),
(2, 'Cancers', 2, 1, '2026-02-03 05:46:55'),
(3, 'Diabetes', 2, 1, '2026-02-03 05:47:08'),
(4, 'HIV/AIDS', 2, 1, '2026-02-03 05:47:23');

-- --------------------------------------------------------

--
-- Table structure for table `financial_years`
--

CREATE TABLE `financial_years` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `opd_number` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `financial_years`
--

INSERT INTO `financial_years` (`id`, `name`, `opd_number`, `created_date`, `status`) VALUES
(1, '2025', 1, '2026-02-01 02:48:19', 0),
(2, '2026', 4, '2026-02-01 02:49:58', 1);

-- --------------------------------------------------------

--
-- Table structure for table `ipd_registration`
--

CREATE TABLE `ipd_registration` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `opd_registration_id` bigint(20) UNSIGNED NOT NULL,
  `ipd_number` varchar(50) DEFAULT NULL,
  `patient_name` varchar(255) DEFAULT NULL,
  `patient_age` int(10) UNSIGNED DEFAULT NULL,
  `patient_age_unit` varchar(20) DEFAULT NULL,
  `gender` varchar(50) DEFAULT NULL,
  `opd_number` varchar(255) DEFAULT NULL,
  `hid_number` varchar(255) DEFAULT NULL,
  `dept_id` bigint(20) UNSIGNED DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `date` date NOT NULL,
  `time` varchar(20) DEFAULT NULL,
  `fath_husb_name` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `diagnosis` text DEFAULT NULL,
  `bed_distribution_id` bigint(20) UNSIGNED DEFAULT NULL,
  `admit_by_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `discharge_date` date DEFAULT NULL,
  `discharge_dept_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ipd_registration`
--

INSERT INTO `ipd_registration` (`id`, `opd_registration_id`, `ipd_number`, `patient_name`, `patient_age`, `patient_age_unit`, `gender`, `opd_number`, `hid_number`, `dept_id`, `category`, `date`, `time`, `fath_husb_name`, `address`, `diagnosis`, `bed_distribution_id`, `admit_by_user_id`, `amount`, `discharge_date`, `discharge_dept_id`, `created_date`) VALUES
(1, 6, '20260001', 'Pankaj', 36, 'Days', 'Male', '2026000003', '2026-P-0001', 2, 'GENERAL', '2026-02-03', NULL, 'Test', '#a123 Main Road', 'Test', 1, 6, NULL, '2026-02-03', 2, '2026-02-03 04:47:56');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2025_02_01_100000_create_financial_years_table', 1),
(2, '2025_02_01_100001_create_departments_table', 2),
(3, '2025_02_01_100002_create_designations_table', 3),
(4, '2025_02_01_100003_create_diseases_table', 4),
(5, '2025_02_01_100004_create_bed_distributions_table', 5),
(6, '2025_02_01_100005_create_doctor_profiles_table', 6),
(7, '2025_02_01_100006_add_dept_id_to_users_table', 7),
(8, '2025_02_01_100007_create_opd_registration_table', 8),
(9, '2025_02_01_100008_add_register_type_to_opd_registration_table', 9),
(10, '2025_02_01_100009_remove_unique_from_hid_number_opd_registration', 10),
(11, '2025_02_02_100010_create_ipd_registration_table', 11),
(12, '2025_02_02_100011_change_bed_no_to_varchar_in_bed_distributions', 12),
(13, '2025_02_02_100012_add_patient_details_to_ipd_registration_table', 13),
(14, '2025_02_02_100013_add_ipd_number_to_ipd_registration_table', 14),
(15, '2025_02_02_100014_add_discharge_fields_to_ipd_registration_table', 15),
(16, '2025_02_02_100015_add_disease_id_to_opd_registration_table', 16);

-- --------------------------------------------------------

--
-- Table structure for table `opd_registration`
--

CREATE TABLE `opd_registration` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `financial_year_id` bigint(20) UNSIGNED NOT NULL,
  `patient_name` varchar(255) NOT NULL,
  `fath_husb_name` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `date` date NOT NULL,
  `patient_age` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `patient_age_unit` varchar(20) NOT NULL DEFAULT 'Years',
  `gender` varchar(20) DEFAULT NULL,
  `dept_id` bigint(20) UNSIGNED DEFAULT NULL,
  `register_type` varchar(50) NOT NULL DEFAULT 'New',
  `opd_number` varchar(255) NOT NULL,
  `hid_number` varchar(255) NOT NULL,
  `disease_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `opd_registration`
--

INSERT INTO `opd_registration` (`id`, `financial_year_id`, `patient_name`, `fath_husb_name`, `address`, `date`, `patient_age`, `patient_age_unit`, `gender`, `dept_id`, `register_type`, `opd_number`, `hid_number`, `disease_id`, `created_date`) VALUES
(1, 2, 'Pankaj', 'Test', '#a123 Main Road', '2026-02-01', 35, 'Years', 'Male', 2, 'New', '2026000001', '2026-P-0001', 2, '2026-02-01 03:34:30'),
(4, 2, 'Pankaj', 'Test', '#a123 Main Road', '2026-02-01', 35, 'Years', 'Male', 1, 'OLD', '2026000002', '2026-P-0001', NULL, '2026-02-01 03:56:12'),
(6, 2, 'Pankaj', 'Test', '#a123 Main Road', '2026-02-01', 36, 'Days', 'Male', 2, 'OLD', '2026000003', '2026-P-0001', 1, '2026-02-01 03:59:35');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `admin_email` varchar(255) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `business_address` varchar(255) DEFAULT NULL,
  `mobile` varchar(255) DEFAULT NULL,
  `whatsapp` varchar(50) DEFAULT NULL,
  `footer_content` varchar(255) DEFAULT NULL,
  `footer_info` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `admin_email`, `company_name`, `business_address`, `mobile`, `whatsapp`, `footer_content`, `footer_info`, `logo`, `created_at`, `updated_at`) VALUES
(1, 'ecommerce@gmail.com', 'Vigore Travel', 'WRF2+M8F, Kanti Nagar, \r\nSindhi Camp, Jaipur, Rajasthan 302032', '+91- 1234567890', '+91 1234567890', 'Copyright © 2025 admin.com', '1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. INDIA', '1737978390379765911.jpg', '2023-04-26 18:10:27', '2025-01-27 17:16:30');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `dept_id` bigint(20) UNSIGNED DEFAULT NULL,
  `slug` text DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'Admin' COMMENT 'Admin/Operator/Doctor',
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` text DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `dob` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(200) DEFAULT NULL,
  `country` varchar(200) DEFAULT NULL,
  `zipcode` varchar(255) DEFAULT NULL,
  `mobile` varchar(255) DEFAULT NULL,
  `emergency_contact_name` varchar(255) DEFAULT NULL,
  `emergency_contact_number` varchar(255) DEFAULT NULL,
  `emergency_contact_relation_id` int(11) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `govt_id_no` varchar(255) DEFAULT NULL,
  `govt_id_file` varchar(255) DEFAULT NULL,
  `is_govet_id_verified` tinyint(4) NOT NULL DEFAULT 2 COMMENT '2=No,1=Yes',
  `billing_details` text DEFAULT NULL,
  `language` varchar(128) DEFAULT NULL,
  `tax_exempt` tinyint(4) DEFAULT 2 COMMENT '1=YES, 2-NO',
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `is_delete` tinyint(4) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `dept_id`, `slug`, `type`, `name`, `email`, `password`, `gender`, `dob`, `address`, `city`, `state`, `country`, `zipcode`, `mobile`, `emergency_contact_name`, `emergency_contact_number`, `emergency_contact_relation_id`, `photo`, `govt_id_no`, `govt_id_file`, `is_govet_id_verified`, `billing_details`, `language`, `tax_exempt`, `status`, `is_delete`, `last_login`, `created_at`, `updated_at`) VALUES
(1, NULL, NULL, 'Admin', 'AOL', 'admin@admin.com', '$2y$10$pELPhkKV6ICwHbR3KIsEAuMXNsadcqG0Kb52fAt/y5pitP3K21h9S', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1111111111', NULL, NULL, NULL, '1733121446.png', NULL, NULL, 2, NULL, NULL, 2, 1, NULL, NULL, '2022-12-07 08:11:31', '2026-02-01 07:48:51'),
(6, 2, NULL, 'Doctor', 'AOL Doctor', 'aol_docotr@admin.com', '$2y$10$pELPhkKV6ICwHbR3KIsEAuMXNsadcqG0Kb52fAt/y5pitP3K21h9S', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1111111111', NULL, NULL, NULL, '1733121446.png', NULL, NULL, 2, NULL, NULL, 2, 1, NULL, NULL, '2022-12-07 08:11:31', '2026-02-03 10:53:57'),
(7, 2, NULL, 'Doctor', 'Pankaj', 'pankaj@gmail.com', '$2y$10$Cy1QZ1Hev0szqYs2i0tVEubxz7r4jaEhDYxeMJc8RwamMWFLsSeV2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '9571997180', NULL, NULL, NULL, NULL, NULL, NULL, 2, NULL, NULL, 2, 1, NULL, NULL, '2026-02-03 10:53:48', '2026-02-03 10:53:48');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bed_distributions`
--
ALTER TABLE `bed_distributions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bed_distributions_department_id_foreign` (`department_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `departments_name_unique` (`name`);

--
-- Indexes for table `designations`
--
ALTER TABLE `designations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `designations_name_unique` (`name`);

--
-- Indexes for table `diseases`
--
ALTER TABLE `diseases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `diseases_dept_id_foreign` (`dept_id`);

--
-- Indexes for table `financial_years`
--
ALTER TABLE `financial_years`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `financial_years_name_unique` (`name`);

--
-- Indexes for table `ipd_registration`
--
ALTER TABLE `ipd_registration`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ipd_registration_opd_registration_id_foreign` (`opd_registration_id`),
  ADD KEY `ipd_registration_bed_distribution_id_foreign` (`bed_distribution_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `opd_registration`
--
ALTER TABLE `opd_registration`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `opd_registration_opd_number_unique` (`opd_number`),
  ADD KEY `opd_registration_financial_year_id_foreign` (`financial_year_id`),
  ADD KEY `opd_registration_dept_id_foreign` (`dept_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `users_dept_id_foreign` (`dept_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bed_distributions`
--
ALTER TABLE `bed_distributions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `designations`
--
ALTER TABLE `designations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `diseases`
--
ALTER TABLE `diseases`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `financial_years`
--
ALTER TABLE `financial_years`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ipd_registration`
--
ALTER TABLE `ipd_registration`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `opd_registration`
--
ALTER TABLE `opd_registration`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bed_distributions`
--
ALTER TABLE `bed_distributions`
  ADD CONSTRAINT `bed_distributions_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `diseases`
--
ALTER TABLE `diseases`
  ADD CONSTRAINT `diseases_dept_id_foreign` FOREIGN KEY (`dept_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ipd_registration`
--
ALTER TABLE `ipd_registration`
  ADD CONSTRAINT `ipd_registration_bed_distribution_id_foreign` FOREIGN KEY (`bed_distribution_id`) REFERENCES `bed_distributions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ipd_registration_opd_registration_id_foreign` FOREIGN KEY (`opd_registration_id`) REFERENCES `opd_registration` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `opd_registration`
--
ALTER TABLE `opd_registration`
  ADD CONSTRAINT `opd_registration_dept_id_foreign` FOREIGN KEY (`dept_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `opd_registration_financial_year_id_foreign` FOREIGN KEY (`financial_year_id`) REFERENCES `financial_years` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_dept_id_foreign` FOREIGN KEY (`dept_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
