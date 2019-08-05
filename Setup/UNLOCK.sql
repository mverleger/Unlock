-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 05, 2019 at 04:58 PM
-- Server version: 5.7.26
-- PHP Version: 7.0.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `CRC`
--

-- --------------------------------------------------------

--
-- Table structure for table `CRC_AssignmentTree`
--

CREATE TABLE `CRC_AssignmentTree` (
  `TreeID` int(11) NOT NULL,
  `CourseID` int(11) NOT NULL,
  `AssignmentID` int(11) NOT NULL,
  `Grouping` int(11) NOT NULL,
  `DisplayLevel` int(11) NOT NULL,
  `Position` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `CRC_AssignmentTreeLabels`
--

CREATE TABLE `CRC_AssignmentTreeLabels` (
  `CourseID` int(11) NOT NULL,
  `Grouping` int(11) NOT NULL,
  `Location` text NOT NULL,
  `Label` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `CRC_GroupCache`
--

CREATE TABLE `CRC_GroupCache` (
  `CourseID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `GroupID` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `CRC_GroupNames`
--

CREATE TABLE `CRC_GroupNames` (
  `CourseID` int(11) NOT NULL,
  `ID` int(11) NOT NULL,
  `Name` varchar(250) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `CRC_Leaderboard`
--

CREATE TABLE `CRC_Leaderboard` (
  `CourseID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `Points` int(11) NOT NULL,
  `UpdateTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `CRC_Leaderboard_Columns`
--

CREATE TABLE `CRC_Leaderboard_Columns` (
  `CourseID` int(11) NOT NULL,
  `AssignmentID` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `CRC_NameCache`
--

CREATE TABLE `CRC_NameCache` (
  `CourseID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `Name` varchar(250) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `CRC_OutcomeAssignments`
--

CREATE TABLE `CRC_OutcomeAssignments` (
  `CourseID` int(11) NOT NULL,
  `AssignmentID` int(11) NOT NULL,
  `OutcomeID` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
  `TargetAssignmentID` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `CRC_OutcomeGroups`
--

CREATE TABLE `CRC_OutcomeGroups` (
  `OutcomeID` int(11) NOT NULL,
  `CourseID` int(11) NOT NULL,
  `Outcome` varchar(250) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `CRC_ProgressBarSettings`
--

CREATE TABLE `CRC_ProgressBarSettings` (
  `CourseID` int(11) NOT NULL,
  `PurpleSize` double NOT NULL,
  `PurplePoints` double NOT NULL,
  `PurpleLabel` text NOT NULL,
  `WhiteSize` double NOT NULL,
  `WhitePoints` double NOT NULL,
  `WhiteLabel` text NOT NULL,
  `BlueSize` double NOT NULL,
  `BluePoints` double NOT NULL,
  `BlueLabel` text NOT NULL,
  `GoldSize` double NOT NULL,
  `GoldPoints` double NOT NULL,
  `GoldLabel` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lti2_consumer`
--

CREATE TABLE `lti2_consumer` (
  `consumer_pk` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `consumer_key256` varchar(256) NOT NULL,
  `consumer_key` text,
  `secret` varchar(1024) NOT NULL,
  `lti_version` varchar(10) DEFAULT NULL,
  `consumer_name` varchar(255) DEFAULT NULL,
  `consumer_version` varchar(255) DEFAULT NULL,
  `consumer_guid` varchar(1024) DEFAULT NULL,
  `profile` text,
  `tool_proxy` text,
  `settings` text,
  `protected` tinyint(1) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `enable_from` datetime DEFAULT NULL,
  `enable_until` datetime DEFAULT NULL,
  `last_access` date DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `lti2_context`
--

CREATE TABLE `lti2_context` (
  `context_pk` int(11) NOT NULL,
  `consumer_pk` int(11) NOT NULL,
  `lti_context_id` varchar(255) NOT NULL,
  `settings` text,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `lti2_nonce`
--

CREATE TABLE `lti2_nonce` (
  `consumer_pk` int(11) NOT NULL,
  `value` varchar(64) NOT NULL,
  `expires` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `lti2_resource_link`
--

CREATE TABLE `lti2_resource_link` (
  `resource_link_pk` int(11) NOT NULL,
  `context_pk` int(11) DEFAULT NULL,
  `consumer_pk` int(11) DEFAULT NULL,
  `lti_resource_link_id` varchar(255) NOT NULL,
  `settings` text,
  `primary_resource_link_pk` int(11) DEFAULT NULL,
  `share_approved` tinyint(1) DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `lti2_share_key`
--

CREATE TABLE `lti2_share_key` (
  `share_key_id` varchar(32) NOT NULL,
  `resource_link_pk` int(11) NOT NULL,
  `auto_approve` tinyint(1) NOT NULL,
  `expires` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `lti2_tool_proxy`
--

CREATE TABLE `lti2_tool_proxy` (
  `tool_proxy_pk` int(11) NOT NULL,
  `tool_proxy_id` varchar(32) NOT NULL,
  `consumer_pk` int(11) NOT NULL,
  `tool_proxy` text NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `lti2_user_result`
--

CREATE TABLE `lti2_user_result` (
  `user_pk` int(11) NOT NULL,
  `resource_link_pk` int(11) NOT NULL,
  `lti_user_id` varchar(255) NOT NULL,
  `lti_result_sourcedid` varchar(1024) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `LTI_Keys`
--

CREATE TABLE `LTI_Keys` (
  `UserID` varchar(250) NOT NULL,
  `AccessToken` varchar(250) DEFAULT NULL,
  `RefreshToken` varchar(250) DEFAULT NULL,
  `ExpirationDate` bigint(20) DEFAULT NULL,
  `Refreshable` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `CRC_AssignmentTree`
--
ALTER TABLE `CRC_AssignmentTree`
  ADD PRIMARY KEY (`TreeID`);

--
-- Indexes for table `CRC_Leaderboard`
--
ALTER TABLE `CRC_Leaderboard`
  ADD UNIQUE KEY `CourseID` (`CourseID`,`UserID`);

--
-- Indexes for table `CRC_NameCache`
--
ALTER TABLE `CRC_NameCache`
  ADD UNIQUE KEY `CourseID` (`CourseID`,`UserID`);

--
-- Indexes for table `CRC_OutcomeGroups`
--
ALTER TABLE `CRC_OutcomeGroups`
  ADD PRIMARY KEY (`OutcomeID`);

--
-- Indexes for table `CRC_ProgressBarSettings`
--
ALTER TABLE `CRC_ProgressBarSettings`
  ADD UNIQUE KEY `CourseID` (`CourseID`);

--
-- Indexes for table `lti2_consumer`
--
ALTER TABLE `lti2_consumer`
  ADD PRIMARY KEY (`consumer_pk`),
  ADD UNIQUE KEY `lti2_consumer_consumer_key_UNIQUE` (`consumer_key256`);

--
-- Indexes for table `lti2_context`
--
ALTER TABLE `lti2_context`
  ADD PRIMARY KEY (`context_pk`),
  ADD KEY `lti2_context_consumer_id_IDX` (`consumer_pk`);

--
-- Indexes for table `lti2_nonce`
--
ALTER TABLE `lti2_nonce`
  ADD PRIMARY KEY (`consumer_pk`,`value`);

--
-- Indexes for table `lti2_resource_link`
--
ALTER TABLE `lti2_resource_link`
  ADD PRIMARY KEY (`resource_link_pk`),
  ADD KEY `lti2_resource_link_lti2_resource_link_FK1` (`primary_resource_link_pk`),
  ADD KEY `lti2_resource_link_consumer_pk_IDX` (`consumer_pk`),
  ADD KEY `lti2_resource_link_context_pk_IDX` (`context_pk`);

--
-- Indexes for table `lti2_share_key`
--
ALTER TABLE `lti2_share_key`
  ADD PRIMARY KEY (`share_key_id`),
  ADD KEY `lti2_share_key_resource_link_pk_IDX` (`resource_link_pk`);

--
-- Indexes for table `lti2_tool_proxy`
--
ALTER TABLE `lti2_tool_proxy`
  ADD PRIMARY KEY (`tool_proxy_pk`),
  ADD UNIQUE KEY `lti2_tool_proxy_tool_proxy_id_UNIQUE` (`tool_proxy_id`),
  ADD KEY `lti2_tool_proxy_consumer_id_IDX` (`consumer_pk`);

--
-- Indexes for table `lti2_user_result`
--
ALTER TABLE `lti2_user_result`
  ADD PRIMARY KEY (`user_pk`),
  ADD KEY `lti2_user_result_resource_link_pk_IDX` (`resource_link_pk`);

--
-- Indexes for table `LTI_Keys`
--
ALTER TABLE `LTI_Keys`
  ADD UNIQUE KEY `UserID` (`UserID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `CRC_AssignmentTree`
--
ALTER TABLE `CRC_AssignmentTree`
  MODIFY `TreeID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `CRC_OutcomeGroups`
--
ALTER TABLE `CRC_OutcomeGroups`
  MODIFY `OutcomeID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lti2_consumer`
--
ALTER TABLE `lti2_consumer`
  MODIFY `consumer_pk` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lti2_context`
--
ALTER TABLE `lti2_context`
  MODIFY `context_pk` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lti2_resource_link`
--
ALTER TABLE `lti2_resource_link`
  MODIFY `resource_link_pk` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lti2_tool_proxy`
--
ALTER TABLE `lti2_tool_proxy`
  MODIFY `tool_proxy_pk` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lti2_user_result`
--
ALTER TABLE `lti2_user_result`
  MODIFY `user_pk` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `lti2_context`
--
ALTER TABLE `lti2_context`
  ADD CONSTRAINT `lti2_context_lti2_consumer_FK1` FOREIGN KEY (`consumer_pk`) REFERENCES `lti2_consumer` (`consumer_pk`);

--
-- Constraints for table `lti2_nonce`
--
ALTER TABLE `lti2_nonce`
  ADD CONSTRAINT `lti2_nonce_lti2_consumer_FK1` FOREIGN KEY (`consumer_pk`) REFERENCES `lti2_consumer` (`consumer_pk`);

--
-- Constraints for table `lti2_resource_link`
--
ALTER TABLE `lti2_resource_link`
  ADD CONSTRAINT `lti2_resource_link_lti2_context_FK1` FOREIGN KEY (`context_pk`) REFERENCES `lti2_context` (`context_pk`),
  ADD CONSTRAINT `lti2_resource_link_lti2_resource_link_FK1` FOREIGN KEY (`primary_resource_link_pk`) REFERENCES `lti2_resource_link` (`resource_link_pk`);

--
-- Constraints for table `lti2_share_key`
--
ALTER TABLE `lti2_share_key`
  ADD CONSTRAINT `lti2_share_key_lti2_resource_link_FK1` FOREIGN KEY (`resource_link_pk`) REFERENCES `lti2_resource_link` (`resource_link_pk`);

--
-- Constraints for table `lti2_tool_proxy`
--
ALTER TABLE `lti2_tool_proxy`
  ADD CONSTRAINT `lti2_tool_proxy_lti2_consumer_FK1` FOREIGN KEY (`consumer_pk`) REFERENCES `lti2_consumer` (`consumer_pk`);

--
-- Constraints for table `lti2_user_result`
--
ALTER TABLE `lti2_user_result`
  ADD CONSTRAINT `lti2_user_result_lti2_resource_link_FK1` FOREIGN KEY (`resource_link_pk`) REFERENCES `lti2_resource_link` (`resource_link_pk`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
