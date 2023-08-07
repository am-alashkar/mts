-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 07, 2023 at 07:39 PM
-- Server version: 10.4.25-MariaDB
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mts`
--

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `phone` varchar(256) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `email` text DEFAULT NULL,
  `phone2` varchar(256) DEFAULT NULL,
  `phone3` varchar(256) DEFAULT NULL,
  `phone4` varchar(256) DEFAULT NULL,
  `phone5` varchar(256) DEFAULT NULL,
  `phone6` varchar(256) DEFAULT NULL,
  `city` varchar(256) DEFAULT NULL,
  `old_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `id` int(10) UNSIGNED NOT NULL,
  `file_name` text DEFAULT NULL,
  `location` text DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `m_date` bigint(20) DEFAULT NULL,
  `file_format` text DEFAULT NULL,
  `n_p` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `selectable` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`, `selectable`) VALUES
(1, '{{gr_admins}}', 0),
(3, '{{gr_staff}}', 1);

-- --------------------------------------------------------

--
-- Stand-in structure for view `last_m_log`
-- (See below for the actual view)
--
CREATE TABLE `last_m_log` (
`id` bigint(20) unsigned
,`m_id` bigint(20) unsigned
,`user_id` bigint(20) unsigned
,`report` text
,`log_enter_date` datetime
,`prev_stat` tinyint(4)
,`new_stat` tinyint(4)
);

-- --------------------------------------------------------

--
-- Table structure for table `maintenance`
--

CREATE TABLE `maintenance` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `device` text NOT NULL,
  `type` text NOT NULL,
  `sn` varchar(256) DEFAULT NULL,
  `msn` varchar(256) DEFAULT NULL,
  `enter_date` datetime DEFAULT NULL,
  `customer` int(11) DEFAULT NULL,
  `added_by` bigint(11) UNSIGNED DEFAULT NULL,
  `adds` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `stat` int(2) NOT NULL,
  `out_date` datetime DEFAULT NULL,
  `out_by` bigint(20) UNSIGNED DEFAULT NULL,
  `out_notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `login` varchar(256) NOT NULL,
  `name` text NOT NULL,
  `password` text NOT NULL,
  `email` varchar(512) DEFAULT NULL,
  `info` longtext DEFAULT NULL,
  `sex` tinyint(1) DEFAULT NULL,
  `group_id` int(10) UNSIGNED DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT NULL,
  `last_seen` datetime DEFAULT NULL,
  `disabled` tinyint(1) DEFAULT NULL,
  `disable_note` text DEFAULT NULL,
  `last_try` bigint(20) DEFAULT NULL,
  `register_date` datetime DEFAULT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `lang_name` varchar(16) DEFAULT NULL,
  `timezone` varchar(255) DEFAULT NULL,
  `price_group` tinyint(2) DEFAULT NULL,
  `balance` int(11) DEFAULT NULL,
  `credit` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `login`, `name`, `password`, `email`, `info`, `sex`, `group_id`, `deleted`, `last_seen`, `disabled`, `disable_note`, `last_try`, `register_date`, `parent_id`, `lang_name`, `timezone`, `price_group`, `balance`, `credit`) VALUES
(1, 'admin', 'إدارة الموقع', '$2y$10$jOpm79FPJlQDHywHqWKbc.POA7gtYCyY77fuSwy6hYc2kgeeb9dcO', NULL, NULL, 1, 1, NULL, '2022-01-27 00:32:55', NULL, NULL, 1691427944, '2022-01-27 00:32:55', NULL, 'ar', NULL, NULL, 598985, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `m_log`
--

CREATE TABLE `m_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `m_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `report` text NOT NULL,
  `log_enter_date` datetime NOT NULL,
  `prev_stat` tinyint(4) NOT NULL,
  `new_stat` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `group_id` int(10) UNSIGNED DEFAULT NULL,
  `script` varchar(256) DEFAULT NULL,
  `needs` varchar(256) DEFAULT NULL,
  `only_group` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`user_id`, `group_id`, `script`, `needs`, `only_group`) VALUES
(1, NULL, 'administration', NULL, NULL);

-- --------------------------------------------------------

--
-- Stand-in structure for view `search_table`
-- (See below for the actual view)
--
CREATE TABLE `search_table` (
`id` bigint(20) unsigned
,`device` text
,`type` text
,`sn` varchar(256)
,`msn` varchar(256)
,`enter_date` datetime
,`customer` int(11)
,`added_by` bigint(11) unsigned
,`adds` text
,`notes` text
,`description` text
,`stat` int(2)
,`out_date` datetime
,`out_by` bigint(20) unsigned
,`out_notes` text
,`customer_name` text
,`customer_phone` varchar(256)
,`customer_phone2` varchar(256)
,`customer_phone3` varchar(256)
,`customer_phone4` varchar(256)
,`customer_phone5` varchar(256)
,`customer_phone6` varchar(256)
,`customer_city` varchar(256)
,`customer_email` text
,`customer_address` text
);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sid` text NOT NULL,
  `last_active` datetime DEFAULT NULL,
  `info` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `sid`, `last_active`, `info`) VALUES
(1, 1, '3937d2e36eb038586520d7008d99242e066a0bc8', '2023-08-07 20:39:28', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `var_name` text NOT NULL,
  `var_value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`var_name`, `var_value`) VALUES
('website_title', 'Myne'),
('force_login', '0'),
('nshortdate', 'Y-m-d'),
('nshorttime12', 'h:i A'),
('nshorttime24', 'H:i'),
('nlongdate', 'Y-m-d'),
('shortdate', 'Y-m-d'),
('nlongtime12', 'h:i A'),
('nlongtime24', 'H:i:s');

-- --------------------------------------------------------

--
-- Stand-in structure for view `still_there_maintenance`
-- (See below for the actual view)
--
CREATE TABLE `still_there_maintenance` (
`id` bigint(20) unsigned
,`device` text
,`type` text
,`sn` varchar(256)
,`msn` varchar(256)
,`enter_date` datetime
,`customer` int(11)
,`added_by` bigint(11) unsigned
,`adds` text
,`notes` text
,`description` text
,`stat` int(2)
,`out_date` datetime
,`out_by` bigint(20) unsigned
,`out_notes` text
,`customer_name` text
,`customer_phone` varchar(256)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `stm`
-- (See below for the actual view)
--
CREATE TABLE `stm` (
`id` bigint(20) unsigned
,`device` text
,`type` text
,`sn` varchar(256)
,`msn` varchar(256)
,`enter_date` datetime
,`customer_name` text
,`stat` int(2)
,`lml_id` mediumtext
,`report` mediumtext
);

-- --------------------------------------------------------

--
-- Structure for view `last_m_log`
--
DROP TABLE IF EXISTS `last_m_log`;

CREATE ALGORITHM=TEMPTABLE DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `last_m_log`  AS SELECT `m_log`.`id` AS `id`, `m_log`.`m_id` AS `m_id`, `m_log`.`user_id` AS `user_id`, `m_log`.`report` AS `report`, `m_log`.`log_enter_date` AS `log_enter_date`, `m_log`.`prev_stat` AS `prev_stat`, `m_log`.`new_stat` AS `new_stat` FROM `m_log` GROUP BY `m_log`.`m_id`  ;

-- --------------------------------------------------------

--
-- Structure for view `search_table`
--
DROP TABLE IF EXISTS `search_table`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `search_table`  AS SELECT `ms`.`id` AS `id`, `ms`.`device` AS `device`, `ms`.`type` AS `type`, `ms`.`sn` AS `sn`, `ms`.`msn` AS `msn`, `ms`.`enter_date` AS `enter_date`, `ms`.`customer` AS `customer`, `ms`.`added_by` AS `added_by`, `ms`.`adds` AS `adds`, `ms`.`notes` AS `notes`, `ms`.`description` AS `description`, `ms`.`stat` AS `stat`, `ms`.`out_date` AS `out_date`, `ms`.`out_by` AS `out_by`, `ms`.`out_notes` AS `out_notes`, `cs`.`name` AS `customer_name`, `cs`.`phone` AS `customer_phone`, `cs`.`phone2` AS `customer_phone2`, `cs`.`phone3` AS `customer_phone3`, `cs`.`phone4` AS `customer_phone4`, `cs`.`phone5` AS `customer_phone5`, `cs`.`phone6` AS `customer_phone6`, `cs`.`city` AS `customer_city`, `cs`.`email` AS `customer_email`, `cs`.`address` AS `customer_address` FROM (`maintenance` `ms` left join `customers` `cs` on(`cs`.`id` = `ms`.`customer`))  ;

-- --------------------------------------------------------

--
-- Structure for view `still_there_maintenance`
--
DROP TABLE IF EXISTS `still_there_maintenance`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `still_there_maintenance`  AS SELECT `ms`.`id` AS `id`, `ms`.`device` AS `device`, `ms`.`type` AS `type`, `ms`.`sn` AS `sn`, `ms`.`msn` AS `msn`, `ms`.`enter_date` AS `enter_date`, `ms`.`customer` AS `customer`, `ms`.`added_by` AS `added_by`, `ms`.`adds` AS `adds`, `ms`.`notes` AS `notes`, `ms`.`description` AS `description`, `ms`.`stat` AS `stat`, `ms`.`out_date` AS `out_date`, `ms`.`out_by` AS `out_by`, `ms`.`out_notes` AS `out_notes`, `cs`.`name` AS `customer_name`, `cs`.`phone` AS `customer_phone` FROM (`maintenance` `ms` left join `customers` `cs` on(`cs`.`id` = `ms`.`customer`)) WHERE `ms`.`stat` <> 33  ;

-- --------------------------------------------------------

--
-- Structure for view `stm`
--
DROP TABLE IF EXISTS `stm`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `stm`  AS SELECT `stm`.`id` AS `id`, `stm`.`device` AS `device`, `stm`.`type` AS `type`, `stm`.`sn` AS `sn`, `stm`.`msn` AS `msn`, `stm`.`enter_date` AS `enter_date`, `stm`.`customer_name` AS `customer_name`, `stm`.`stat` AS `stat`, group_concat(`lml`.`id` order by `lml`.`id` DESC separator ',' limit 1) AS `lml_id`, group_concat(`lml`.`report` order by `lml`.`id` DESC separator ',' limit 1) AS `report` FROM (`still_there_maintenance` `stm` left join `m_log` `lml` on(`stm`.`id` = `lml`.`m_id`)) GROUP BY `stm`.`id``id`  ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `maintenance`
--
ALTER TABLE `maintenance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer` (`customer`),
  ADD KEY `added_by` (`added_by`),
  ADD KEY `out_by` (`out_by`),
  ADD KEY `sn` (`sn`),
  ADD KEY `msn` (`msn`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `login` (`login`),
  ADD KEY `disabled` (`disabled`),
  ADD KEY `deleted` (`deleted`),
  ADD KEY `email` (`email`);

--
-- Indexes for table `m_log`
--
ALTER TABLE `m_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `m_id` (`m_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD UNIQUE KEY `user_id` (`user_id`,`group_id`,`script`),
  ADD KEY `group_id` (`group_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD UNIQUE KEY `var_name` (`var_name`) USING HASH;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `maintenance`
--
ALTER TABLE `maintenance`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `m_log`
--
ALTER TABLE `m_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `maintenance`
--
ALTER TABLE `maintenance`
  ADD CONSTRAINT `maintenance_ibfk_1` FOREIGN KEY (`customer`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `maintenance_ibfk_2` FOREIGN KEY (`added_by`) REFERENCES `members` (`id`),
  ADD CONSTRAINT `maintenance_ibfk_3` FOREIGN KEY (`out_by`) REFERENCES `members` (`id`);

--
-- Constraints for table `members`
--
ALTER TABLE `members`
  ADD CONSTRAINT `members_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`),
  ADD CONSTRAINT `members_ibfk_3` FOREIGN KEY (`parent_id`) REFERENCES `members` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `m_log`
--
ALTER TABLE `m_log`
  ADD CONSTRAINT `m_log_ibfk_1` FOREIGN KEY (`m_id`) REFERENCES `maintenance` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `m_log_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `members` (`id`);

--
-- Constraints for table `permissions`
--
ALTER TABLE `permissions`
  ADD CONSTRAINT `permissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `members` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `permissions_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `members` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
