/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.8.2-MariaDB, for osx10.20 (arm64)
--
-- Host: 127.0.0.1    Database: studip_6_0
-- ------------------------------------------------------
-- Server version	11.8.2-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `Institute`
--

DROP TABLE IF EXISTS `Institute`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Institute` (
  `Institut_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `Name` varchar(255) NOT NULL DEFAULT '',
  `fakultaets_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `Strasse` varchar(255) NOT NULL DEFAULT '',
  `Plz` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT 'http://www.studip.de',
  `telefon` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `fax` varchar(255) NOT NULL DEFAULT '',
  `type` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  `lit_plugin_name` varchar(255) DEFAULT NULL,
  `srienabled` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `lock_rule` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`Institut_id`),
  KEY `fakultaets_id` (`fakultaets_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `abschluss`
--

DROP TABLE IF EXISTS `abschluss`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `abschluss` (
  `abschluss_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `name_kurz` varchar(50) DEFAULT NULL,
  `beschreibung` text DEFAULT NULL,
  `author_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `editor_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned DEFAULT NULL,
  `chdate` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`abschluss_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `activities`
--

DROP TABLE IF EXISTS `activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `activities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_id` varchar(255) NOT NULL,
  `context` enum('system','course','institute','user') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `context_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `provider` varchar(255) NOT NULL,
  `actor_type` varchar(255) NOT NULL,
  `actor_id` varchar(255) NOT NULL,
  `verb` enum('answered','attempted','attended','completed','created','deleted','edited','experienced','failed','imported','interacted','passed','shared','sent','voided') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'experienced',
  `content` text DEFAULT NULL,
  `object_type` varchar(255) NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `context_id` (`context_id`),
  KEY `mkdate` (`mkdate`),
  KEY `object_id` (`object_id`(32)),
  KEY `context_query` (`context`,`context_id`,`mkdate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `admission_condition`
--

DROP TABLE IF EXISTS `admission_condition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `admission_condition` (
  `rule_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `filter_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `conditiongroup_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`rule_id`,`filter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `admission_conditiongroup`
--

DROP TABLE IF EXISTS `admission_conditiongroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `admission_conditiongroup` (
  `conditiongroup_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `quota` int(11) NOT NULL,
  PRIMARY KEY (`conditiongroup_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `admission_seminar_user`
--

DROP TABLE IF EXISTS `admission_seminar_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `admission_seminar_user` (
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `seminar_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `status` enum('awaiting','accepted') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  `comment` varchar(255) NOT NULL DEFAULT '',
  `visible` enum('yes','no','unknown') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'unknown',
  PRIMARY KEY (`user_id`,`seminar_id`),
  KEY `seminar_id` (`seminar_id`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `admissionfactor`
--

DROP TABLE IF EXISTS `admissionfactor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `admissionfactor` (
  `list_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `name` varchar(255) NOT NULL,
  `factor` float NOT NULL DEFAULT 1,
  `owner_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`list_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `admissionrule_compat`
--

DROP TABLE IF EXISTS `admissionrule_compat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `admissionrule_compat` (
  `rule_type` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `compat_rule_type` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`rule_type`,`compat_rule_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `admissionrules`
--

DROP TABLE IF EXISTS `admissionrules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `admissionrules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ruletype` varchar(255) NOT NULL,
  `active` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `path` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ruletype` (`ruletype`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `archiv`
--

DROP TABLE IF EXISTS `archiv`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `archiv` (
  `seminar_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `untertitel` varchar(255) NOT NULL DEFAULT '',
  `beschreibung` text NOT NULL,
  `start_time` int(10) unsigned NOT NULL DEFAULT 0,
  `semester` varchar(16) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `heimat_inst_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `institute` varchar(255) NOT NULL DEFAULT '',
  `dozenten` varchar(255) NOT NULL DEFAULT '',
  `fakultaet` varchar(255) NOT NULL DEFAULT '',
  `dump` mediumtext NOT NULL,
  `archiv_file_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `archiv_protected_file_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `forumdump` longtext NOT NULL,
  `wikidump` longtext DEFAULT NULL,
  `studienbereiche` text NOT NULL,
  `VeranstaltungsNummer` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`seminar_id`),
  KEY `heimat_inst_id` (`heimat_inst_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `archiv_user`
--

DROP TABLE IF EXISTS `archiv_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `archiv_user` (
  `seminar_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `status` enum('user','autor','tutor','dozent') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'user',
  PRIMARY KEY (`seminar_id`,`user_id`),
  KEY `user_id` (`user_id`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `auth_extern`
--

DROP TABLE IF EXISTS `auth_extern`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `auth_extern` (
  `studip_user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `external_user_id` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `external_user_name` varchar(64) NOT NULL DEFAULT '',
  `external_user_password` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `external_user_token` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `external_user_token_valid_until` int(11) NOT NULL DEFAULT 0,
  `external_user_category` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `external_user_system_type` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `external_user_type` smallint(6) NOT NULL DEFAULT 0,
  PRIMARY KEY (`studip_user_id`,`external_user_system_type`,`external_user_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `auth_user_md5`
--

DROP TABLE IF EXISTS `auth_user_md5`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `auth_user_md5` (
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `username` varchar(64) NOT NULL DEFAULT '',
  `password` varbinary(64) NOT NULL DEFAULT '',
  `perms` enum('user','autor','tutor','dozent','admin','root') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'user',
  `Vorname` varchar(64) NOT NULL DEFAULT '',
  `Nachname` varchar(64) NOT NULL DEFAULT '',
  `Email` varchar(256) NOT NULL DEFAULT '',
  `validation_key` varchar(10) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `auth_plugin` varchar(64) DEFAULT 'standard',
  `locked` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `lock_comment` varchar(255) DEFAULT NULL,
  `locked_by` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `visible` enum('global','always','yes','unknown','no','never') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'unknown',
  `matriculation_number` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `k_username` (`username`),
  KEY `perms` (`perms`),
  KEY `matriculation_number` (`matriculation_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `auto_insert_sem`
--

DROP TABLE IF EXISTS `auto_insert_sem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `auto_insert_sem` (
  `seminar_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `status` enum('autor','tutor','dozent') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'autor',
  `domain_id` varchar(45) NOT NULL DEFAULT '',
  PRIMARY KEY (`seminar_id`,`status`,`domain_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `auto_insert_user`
--

DROP TABLE IF EXISTS `auto_insert_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `auto_insert_user` (
  `seminar_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`seminar_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `aux_lock_rules`
--

DROP TABLE IF EXISTS `aux_lock_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `aux_lock_rules` (
  `lock_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `attributes` text NOT NULL,
  `sorting` text NOT NULL,
  `mkdate` int(10) unsigned DEFAULT NULL,
  `chdate` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`lock_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `banner_ads`
--

DROP TABLE IF EXISTS `banner_ads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `banner_ads` (
  `ad_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `banner_path` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) DEFAULT NULL,
  `alttext` varchar(255) DEFAULT NULL,
  `target_type` enum('url','seminar','inst','user','none') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'url',
  `target` varchar(255) NOT NULL DEFAULT '',
  `startdate` int(10) unsigned NOT NULL DEFAULT 0,
  `enddate` int(10) unsigned NOT NULL DEFAULT 0,
  `priority` int(10) unsigned NOT NULL DEFAULT 0,
  `views` int(10) unsigned NOT NULL DEFAULT 0,
  `clicks` int(10) unsigned NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`ad_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `banner_roles`
--

DROP TABLE IF EXISTS `banner_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `banner_roles` (
  `ad_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `roleid` int(11) NOT NULL,
  PRIMARY KEY (`ad_id`,`roleid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `blubber_comments`
--

DROP TABLE IF EXISTS `blubber_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `blubber_comments` (
  `comment_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `thread_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `external_contact` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `content` text DEFAULT NULL,
  `network` varchar(64) DEFAULT NULL,
  `chdate` int(10) unsigned DEFAULT NULL,
  `mkdate` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`comment_id`),
  KEY `thread_id` (`thread_id`),
  KEY `user_id` (`user_id`),
  KEY `mkdate` (`mkdate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `blubber_events_queue`
--

DROP TABLE IF EXISTS `blubber_events_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `blubber_events_queue` (
  `event_type` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `item_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`event_type`,`item_id`,`mkdate`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `blubber_mentions`
--

DROP TABLE IF EXISTS `blubber_mentions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `blubber_mentions` (
  `mention_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `thread_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `external_contact` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`mention_id`),
  UNIQUE KEY `unique_users_per_topic` (`thread_id`,`user_id`,`external_contact`),
  KEY `topic_id` (`thread_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `blubber_tags`
--

DROP TABLE IF EXISTS `blubber_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `blubber_tags` (
  `topic_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `tag` varchar(128) NOT NULL,
  PRIMARY KEY (`topic_id`,`tag`),
  KEY `tag` (`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `blubber_threads`
--

DROP TABLE IF EXISTS `blubber_threads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `blubber_threads` (
  `thread_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `context_type` enum('public','private','course','institute') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'public',
  `context_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `external_contact` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `content` text DEFAULT NULL,
  `display_class` varchar(64) DEFAULT NULL,
  `visible_in_stream` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `commentable` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `metadata` text DEFAULT NULL,
  `chdate` int(10) unsigned DEFAULT NULL,
  `mkdate` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`thread_id`),
  KEY `context_type` (`context_type`),
  KEY `context_id` (`context_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `blubber_threads_followstates`
--

DROP TABLE IF EXISTS `blubber_threads_followstates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `blubber_threads_followstates` (
  `thread_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `state` enum('followed','unfollowed') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'unfollowed',
  `mkdate` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`thread_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `cache_key` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `content` mediumblob NOT NULL,
  `expires` int(10) unsigned NOT NULL,
  PRIMARY KEY (`cache_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cache_operations`
--

DROP TABLE IF EXISTS `cache_operations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_operations` (
  `cache_key` varchar(256) NOT NULL DEFAULT '',
  `operation` char(6) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `parameters` text NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`cache_key`(200),`operation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cache_types`
--

DROP TABLE IF EXISTS `cache_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_types` (
  `cache_id` int(11) NOT NULL AUTO_INCREMENT,
  `class_name` varchar(255) NOT NULL,
  `chdate` int(11) DEFAULT NULL,
  `mkdate` int(11) DEFAULT NULL,
  PRIMARY KEY (`cache_id`),
  UNIQUE KEY `class_name` (`class_name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `calendar_date_assignments`
--

DROP TABLE IF EXISTS `calendar_date_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `calendar_date_assignments` (
  `range_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `calendar_date_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(11) NOT NULL DEFAULT 0,
  `chdate` int(11) NOT NULL DEFAULT 0,
  `participation` enum('','ACCEPTED','DECLINED','ACKNOWLEDGED') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`range_id`,`calendar_date_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `calendar_date_exceptions`
--

DROP TABLE IF EXISTS `calendar_date_exceptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `calendar_date_exceptions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `calendar_date_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `date` date NOT NULL,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `calendar_date_id` (`calendar_date_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `calendar_dates`
--

DROP TABLE IF EXISTS `calendar_dates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `calendar_dates` (
  `id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `author_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `editor_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `unique_id` varchar(255) NOT NULL,
  `begin` int(11) NOT NULL DEFAULT 0,
  `end` int(11) NOT NULL DEFAULT 0,
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text DEFAULT NULL,
  `access` enum('PUBLIC','PRIVATE','CONFIDENTIAL') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'PRIVATE',
  `user_category` varchar(64) DEFAULT '',
  `category` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `location` varchar(255) DEFAULT '',
  `interval` tinyint(4) DEFAULT 0,
  `offset` tinyint(4) DEFAULT 0,
  `days` varchar(7) DEFAULT '',
  `month` tinyint(3) unsigned DEFAULT NULL,
  `repetition_type` enum('SINGLE','DAILY','WEEKLY','MONTHLY','YEARLY') DEFAULT 'SINGLE',
  `number_of_dates` smallint(5) unsigned NOT NULL DEFAULT 1,
  `repetition_end` bigint(20) NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  `import_date` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_id` (`unique_id`),
  KEY `autor_id` (`author_id`),
  KEY `repetition_type` (`repetition_type`,`repetition_end`),
  KEY `begin` (`begin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `captcha_challenges`
--

DROP TABLE IF EXISTS `captcha_challenges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `captcha_challenges` (
  `challenge_id` int(11) NOT NULL AUTO_INCREMENT,
  `salt` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `number` int(11) unsigned NOT NULL,
  `mkdate` int(11) unsigned NOT NULL,
  PRIMARY KEY (`challenge_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `clipboard_items`
--

DROP TABLE IF EXISTS `clipboard_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `clipboard_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clipboard_id` int(11) NOT NULL,
  `range_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `range_type` varchar(64) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'SimpleORMap',
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `clipboard_id` (`clipboard_id`),
  KEY `range` (`range_id`,`range_type`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `clipboards`
--

DROP TABLE IF EXISTS `clipboards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `clipboards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `name` varchar(256) NOT NULL DEFAULT '',
  `handler` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'Clipboard',
  `allowed_item_class` varchar(64) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'StudipItem',
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `colour_values`
--

DROP TABLE IF EXISTS `colour_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `colour_values` (
  `colour_id` varchar(128) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `description` varchar(256) NOT NULL DEFAULT '',
  `value` varchar(8) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'ffffffff',
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`colour_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `comments` (
  `comment_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `object_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`comment_id`),
  KEY `object_id` (`object_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `conditionaladmissions`
--

DROP TABLE IF EXISTS `conditionaladmissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `conditionaladmissions` (
  `rule_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `message` text DEFAULT NULL,
  `start_time` int(10) unsigned NOT NULL DEFAULT 0,
  `end_time` int(10) unsigned NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `conditions_stopped` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`rule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `config`
--

DROP TABLE IF EXISTS `config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `config` (
  `field` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `value` text NOT NULL,
  `type` enum('boolean','integer','string','array','i18n') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'string',
  `range` enum('global','range','user','course','institute') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'global',
  `section` varchar(255) NOT NULL DEFAULT '',
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  `description` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`field`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `config_values`
--

DROP TABLE IF EXISTS `config_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `config_values` (
  `field` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `range_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `value` text NOT NULL,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  `comment` text NOT NULL,
  PRIMARY KEY (`field`,`range_id`),
  KEY `field` (`field`,`value`(10)),
  KEY `range_id` (`range_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `consultation_blocks`
--

DROP TABLE IF EXISTS `consultation_blocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `consultation_blocks` (
  `block_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `range_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `range_type` enum('user','course','institute') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `start` int(10) unsigned NOT NULL,
  `end` int(10) unsigned NOT NULL,
  `room` varchar(128) NOT NULL,
  `calendar_events` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT 'Create events for slots',
  `show_participants` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `require_reason` enum('no','optional','yes') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'optional',
  `mail_to_tutors` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `confirmation_text` text DEFAULT NULL,
  `note` text NOT NULL,
  `size` tinyint(3) unsigned NOT NULL DEFAULT 1 COMMENT 'How many people may book a slot',
  `lock_time` int(10) unsigned DEFAULT NULL,
  `consecutive` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`block_id`),
  KEY `range` (`range_id`,`range_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `consultation_bookings`
--

DROP TABLE IF EXISTS `consultation_bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `consultation_bookings` (
  `booking_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `slot_id` int(10) unsigned NOT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `reason` text DEFAULT NULL,
  `student_event_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`booking_id`),
  KEY `block_id` (`slot_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `consultation_events`
--

DROP TABLE IF EXISTS `consultation_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `consultation_events` (
  `slot_id` int(10) unsigned NOT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `event_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`slot_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `consultation_responsibilities`
--

DROP TABLE IF EXISTS `consultation_responsibilities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `consultation_responsibilities` (
  `block_id` int(10) unsigned NOT NULL,
  `range_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `range_type` enum('user','institute','statusgroup') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`block_id`,`range_id`,`range_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `consultation_slots`
--

DROP TABLE IF EXISTS `consultation_slots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `consultation_slots` (
  `slot_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `block_id` int(10) unsigned NOT NULL,
  `previous_slot_id` int(11) unsigned DEFAULT NULL,
  `start_time` int(10) unsigned NOT NULL,
  `end_time` int(10) unsigned NOT NULL,
  `note` text NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`slot_id`),
  KEY `block_id` (`block_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contact`
--

DROP TABLE IF EXISTS `contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact` (
  `owner_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `mkdate` int(11) NOT NULL DEFAULT 0,
  `chdate` int(11) NOT NULL DEFAULT 0,
  `calendar_permissions` enum('','READ','WRITE') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`owner_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contact_group_items`
--

DROP TABLE IF EXISTS `contact_group_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact_group_items` (
  `group_id` bigint(20) unsigned NOT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`group_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contact_groups`
--

DROP TABLE IF EXISTS `contact_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact_groups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `owner_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `content_terms_of_use_entries`
--

DROP TABLE IF EXISTS `content_terms_of_use_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `content_terms_of_use_entries` (
  `id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `name` varchar(255) NOT NULL,
  `position` int(10) unsigned NOT NULL,
  `description` text NOT NULL,
  `student_description` text NOT NULL,
  `download_condition` tinyint(4) NOT NULL,
  `icon` varchar(128) NOT NULL DEFAULT '',
  `is_default` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `coursememberadmissions`
--

DROP TABLE IF EXISTS `coursememberadmissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `coursememberadmissions` (
  `rule_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `start_time` int(10) unsigned NOT NULL DEFAULT 0,
  `end_time` int(10) unsigned NOT NULL DEFAULT 0,
  `courses` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `modus` tinyint(1) NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`rule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `courseset_factorlist`
--

DROP TABLE IF EXISTS `courseset_factorlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `courseset_factorlist` (
  `set_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `factorlist_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`set_id`,`factorlist_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `courseset_institute`
--

DROP TABLE IF EXISTS `courseset_institute`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `courseset_institute` (
  `set_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `institute_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned DEFAULT NULL,
  `chdate` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`set_id`,`institute_id`),
  KEY `institute_id` (`institute_id`,`set_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `courseset_rule`
--

DROP TABLE IF EXISTS `courseset_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `courseset_rule` (
  `set_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `rule_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `mkdate` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`set_id`,`rule_id`),
  KEY `type` (`set_id`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `coursesets`
--

DROP TABLE IF EXISTS `coursesets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `coursesets` (
  `set_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `name` varchar(255) NOT NULL,
  `infotext` text NOT NULL,
  `algorithm` varchar(255) NOT NULL,
  `algorithm_run` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `private` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`set_id`),
  KEY `set_user` (`user_id`,`set_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `coursewizardsteps`
--

DROP TABLE IF EXISTS `coursewizardsteps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `coursewizardsteps` (
  `id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `name` varchar(255) NOT NULL,
  `classname` varchar(255) NOT NULL,
  `number` tinyint(1) NOT NULL,
  `enabled` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `classname` (`classname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cronjobs_logs`
--

DROP TABLE IF EXISTS `cronjobs_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cronjobs_logs` (
  `log_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `schedule_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `scheduled` int(10) unsigned NOT NULL,
  `executed` int(10) unsigned NOT NULL,
  `exception` text DEFAULT NULL,
  `output` text DEFAULT NULL,
  `duration` float NOT NULL,
  PRIMARY KEY (`log_id`),
  KEY `schedule_id` (`schedule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cronjobs_schedules`
--

DROP TABLE IF EXISTS `cronjobs_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cronjobs_schedules` (
  `schedule_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `task_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `active` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `title` varchar(255) DEFAULT NULL,
  `description` varchar(4096) DEFAULT NULL,
  `parameters` text DEFAULT NULL,
  `minute` tinyint(4) DEFAULT NULL,
  `hour` tinyint(4) DEFAULT NULL,
  `day` tinyint(4) DEFAULT NULL,
  `month` tinyint(4) DEFAULT NULL,
  `day_of_week` tinyint(3) unsigned DEFAULT NULL,
  `next_execution` int(10) unsigned NOT NULL DEFAULT 0,
  `last_execution` int(10) unsigned DEFAULT NULL,
  `last_result` text DEFAULT NULL,
  `execution_count` bigint(20) unsigned NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`schedule_id`),
  KEY `task_id` (`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cronjobs_tasks`
--

DROP TABLE IF EXISTS `cronjobs_tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cronjobs_tasks` (
  `task_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `filename` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `active` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `execution_count` bigint(20) unsigned NOT NULL DEFAULT 0,
  `assigned_count` int(10) unsigned NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned DEFAULT NULL,
  `chdate` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_block_comments`
--

DROP TABLE IF EXISTS `cw_block_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cw_block_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `block_id` int(11) NOT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `comment` mediumtext NOT NULL,
  `mkdate` int(11) NOT NULL,
  `chdate` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_block_id` (`block_id`),
  KEY `index_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_block_feedbacks`
--

DROP TABLE IF EXISTS `cw_block_feedbacks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cw_block_feedbacks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `block_id` int(11) NOT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `feedback` mediumtext NOT NULL,
  `mkdate` int(11) NOT NULL,
  `chdate` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_block_id` (`block_id`),
  KEY `index_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_blocks`
--

DROP TABLE IF EXISTS `cw_blocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cw_blocks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `container_id` int(11) NOT NULL,
  `owner_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `editor_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `edit_blocker_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `position` int(11) NOT NULL,
  `block_type` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `visible` tinyint(1) NOT NULL,
  `commentable` tinyint(1) NOT NULL,
  `payload` mediumtext NOT NULL,
  `mkdate` int(11) NOT NULL,
  `chdate` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_container_id` (`container_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_bookmarks`
--

DROP TABLE IF EXISTS `cw_bookmarks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cw_bookmarks` (
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `element_id` int(11) NOT NULL,
  `mkdate` int(11) NOT NULL,
  `chdate` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`element_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_certificates`
--

DROP TABLE IF EXISTS `cw_certificates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cw_certificates` (
  `id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `course_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `unit_id` int(11) NOT NULL,
  `fileref_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `mkdate` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_user_id` (`user_id`),
  KEY `index_unit_id` (`unit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_clipboards`
--

DROP TABLE IF EXISTS `cw_clipboards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cw_clipboards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` mediumtext NOT NULL,
  `block_id` int(11) DEFAULT NULL,
  `container_id` int(11) DEFAULT NULL,
  `structural_element_id` int(11) DEFAULT NULL,
  `object_type` enum('courseware-structural-elements','courseware-containers','courseware-blocks') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `object_kind` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `backup` mediumtext NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_containers`
--

DROP TABLE IF EXISTS `cw_containers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cw_containers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `structural_element_id` int(11) NOT NULL,
  `owner_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `editor_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `edit_blocker_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `position` int(11) NOT NULL,
  `site` int(11) NOT NULL,
  `container_type` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `visible` tinyint(1) NOT NULL,
  `payload` mediumtext NOT NULL,
  `mkdate` int(11) NOT NULL,
  `chdate` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_structural_element_id` (`structural_element_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_peer_review_processes`
--

DROP TABLE IF EXISTS `cw_peer_review_processes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cw_peer_review_processes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `task_group_id` int(11) NOT NULL,
  `owner_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `configuration` mediumtext NOT NULL,
  `review_start` int(11) unsigned NOT NULL,
  `review_end` int(11) unsigned NOT NULL,
  `paired_at` int(11) unsigned DEFAULT NULL,
  `mkdate` int(11) unsigned NOT NULL,
  `chdate` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_task_group_id` (`task_group_id`),
  KEY `index_owner_id` (`owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_peer_reviews`
--

DROP TABLE IF EXISTS `cw_peer_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cw_peer_reviews` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `process_id` int(11) unsigned NOT NULL,
  `task_id` int(11) unsigned NOT NULL,
  `submitter_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `reviewer_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `reviewer_type` enum('autor','group') CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `assessment` text DEFAULT NULL,
  `mkdate` int(11) unsigned NOT NULL,
  `chdate` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_process_id` (`process_id`),
  KEY `index_task_id` (`task_id`),
  KEY `index_submitter_id` (`submitter_id`),
  KEY `index_reviewer_id` (`reviewer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_public_links`
--

DROP TABLE IF EXISTS `cw_public_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cw_public_links` (
  `id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `structural_element_id` int(11) NOT NULL,
  `password` varbinary(64) NOT NULL,
  `expire_date` int(11) NOT NULL,
  `mkdate` int(11) NOT NULL,
  `chdate` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_user_id` (`user_id`),
  KEY `index_structural_element_id` (`structural_element_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_structural_element_comments`
--

DROP TABLE IF EXISTS `cw_structural_element_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cw_structural_element_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `structural_element_id` int(11) NOT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `comment` mediumtext NOT NULL,
  `mkdate` int(11) NOT NULL,
  `chdate` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_structural_element_id` (`structural_element_id`),
  KEY `index_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_structural_element_feedbacks`
--

DROP TABLE IF EXISTS `cw_structural_element_feedbacks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cw_structural_element_feedbacks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `structural_element_id` int(11) NOT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `feedback` mediumtext NOT NULL,
  `mkdate` int(11) NOT NULL,
  `chdate` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_structural_element_id` (`structural_element_id`),
  KEY `index_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_structural_elements`
--

DROP TABLE IF EXISTS `cw_structural_elements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cw_structural_elements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `is_link` tinyint(1) NOT NULL,
  `target_id` int(11) DEFAULT NULL,
  `range_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `range_type` enum('course','user') CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `owner_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `editor_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `edit_blocker_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `position` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `image_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `image_type` enum('FileRef','StockImage') NOT NULL DEFAULT 'FileRef',
  `purpose` enum('content','draft','task','template','oer','other','portfolio') CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `payload` mediumtext NOT NULL,
  `public` tinyint(1) NOT NULL,
  `commentable` tinyint(1) NOT NULL,
  `permission_type` enum('all','users','groups') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'all',
  `visible` enum('always','never','period') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'always',
  `visible_all` tinyint(4) NOT NULL DEFAULT 0,
  `visible_start_date` int(10) unsigned DEFAULT NULL,
  `visible_end_date` int(10) unsigned DEFAULT NULL,
  `writable` enum('always','never','period') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'never',
  `writable_all` tinyint(4) NOT NULL DEFAULT 0,
  `writable_start_date` int(10) unsigned DEFAULT NULL,
  `writable_end_date` int(10) unsigned DEFAULT NULL,
  `visible_approval` text NOT NULL,
  `writable_approval` text NOT NULL,
  `content_approval` text NOT NULL,
  `copy_approval` text NOT NULL,
  `external_relations` text NOT NULL,
  `mkdate` int(11) NOT NULL,
  `chdate` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_parent_id` (`parent_id`),
  KEY `index_range_id` (`range_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_task_feedbacks`
--

DROP TABLE IF EXISTS `cw_task_feedbacks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cw_task_feedbacks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL,
  `lecturer_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `content` mediumtext NOT NULL,
  `mkdate` int(11) NOT NULL,
  `chdate` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_task_id` (`task_id`),
  KEY `index_lecturer_id` (`lecturer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_task_groups`
--

DROP TABLE IF EXISTS `cw_task_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cw_task_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `seminar_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `lecturer_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `target_id` int(11) NOT NULL,
  `task_template_id` int(11) NOT NULL,
  `solver_may_add_blocks` tinyint(1) NOT NULL,
  `title` varchar(255) NOT NULL,
  `start_date` int(11) NOT NULL,
  `end_date` int(11) NOT NULL,
  `mkdate` int(11) NOT NULL,
  `chdate` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_seminar_id` (`seminar_id`),
  KEY `index_lecturer_id` (`lecturer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_tasks`
--

DROP TABLE IF EXISTS `cw_tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cw_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_group_id` int(11) NOT NULL,
  `structural_element_id` int(11) NOT NULL,
  `solver_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `solver_type` enum('autor','group') CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `submitted` tinyint(1) NOT NULL,
  `renewal` enum('pending','granted','declined') CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `renewal_date` int(11) NOT NULL,
  `visible` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `feedback_id` int(11) DEFAULT NULL,
  `mkdate` int(11) NOT NULL,
  `chdate` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_task_group_id` (`task_group_id`),
  KEY `index_structural_element_id` (`structural_element_id`),
  KEY `index_solver_id` (`solver_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_templates`
--

DROP TABLE IF EXISTS `cw_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cw_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `purpose` enum('content','template','oer','portfolio','draft','other') CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `structure` mediumtext NOT NULL,
  `mkdate` int(11) NOT NULL,
  `chdate` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_units`
--

DROP TABLE IF EXISTS `cw_units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cw_units` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `range_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `range_type` enum('course','user') CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `structural_element_id` int(11) NOT NULL,
  `content_type` enum('courseware') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `position` int(11) DEFAULT NULL,
  `public` tinyint(4) NOT NULL DEFAULT 1,
  `creator_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `permission_scope` enum('unit','structural_element') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'unit',
  `permission_type` enum('all','users','groups') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'all',
  `visible` enum('always','never','period') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'always',
  `visible_all` tinyint(4) NOT NULL DEFAULT 0,
  `visible_start_date` int(10) unsigned DEFAULT NULL,
  `visible_end_date` int(10) unsigned DEFAULT NULL,
  `writable` enum('always','never','period') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'never',
  `writable_all` tinyint(4) NOT NULL DEFAULT 0,
  `writable_start_date` int(10) unsigned DEFAULT NULL,
  `writable_end_date` int(10) unsigned DEFAULT NULL,
  `visible_approval` text NOT NULL,
  `writable_approval` text NOT NULL,
  `config` text NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_range_id` (`range_id`),
  KEY `index_structural_element_id` (`structural_element_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_user_data_fields`
--

DROP TABLE IF EXISTS `cw_user_data_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cw_user_data_fields` (
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `block_id` int(11) NOT NULL,
  `payload` text NOT NULL,
  `mkdate` int(11) NOT NULL,
  `chdate` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`block_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cw_user_progresses`
--

DROP TABLE IF EXISTS `cw_user_progresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cw_user_progresses` (
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `block_id` int(11) NOT NULL,
  `grade` float NOT NULL,
  `mkdate` int(11) NOT NULL,
  `chdate` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`block_id`),
  KEY `block_id` (`block_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `datafields`
--

DROP TABLE IF EXISTS `datafields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `datafields` (
  `datafield_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `name` varchar(255) DEFAULT NULL,
  `object_type` enum('sem','inst','user','userinstrole','usersemdata','roleinstdata','moduldeskriptor','modulteildeskriptor','studycourse') DEFAULT NULL,
  `object_class` varchar(255) DEFAULT NULL,
  `edit_perms` enum('user','autor','tutor','dozent','admin','root') CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `view_perms` enum('all','user','autor','tutor','dozent','admin','root') CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `institut_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `priority` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned DEFAULT NULL,
  `chdate` int(10) unsigned DEFAULT NULL,
  `type` enum('bool','textline','textlinei18n','textarea','textareai18n','textmarkup','textmarkupi18n','selectbox','date','time','email','phone','radio','combo','link','selectboxmultiple') NOT NULL DEFAULT 'textline',
  `typeparam` text NOT NULL,
  `is_required` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `default_value` text DEFAULT NULL,
  `is_userfilter` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `description` text NOT NULL,
  `system` tinyint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`datafield_id`),
  KEY `object_type` (`object_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `datafields_entries`
--

DROP TABLE IF EXISTS `datafields_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `datafields_entries` (
  `datafield_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `range_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `content` text DEFAULT NULL,
  `mkdate` int(10) unsigned DEFAULT NULL,
  `chdate` int(10) unsigned DEFAULT NULL,
  `sec_range_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `lang` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`datafield_id`,`range_id`,`sec_range_id`,`lang`) USING BTREE,
  KEY `range_id` (`range_id`,`datafield_id`),
  KEY `datafield_id_2` (`datafield_id`,`sec_range_id`),
  KEY `datafields_contents` (`datafield_id`,`content`(32))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `deputies`
--

DROP TABLE IF EXISTS `deputies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `deputies` (
  `range_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `gruppe` tinyint(4) NOT NULL DEFAULT 0,
  `notification` int(11) NOT NULL DEFAULT 0,
  `edit_about` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned DEFAULT NULL,
  `chdate` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`range_id`,`user_id`),
  KEY `user_id` (`user_id`,`range_id`,`edit_about`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `etask_assignment_attempts`
--

DROP TABLE IF EXISTS `etask_assignment_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `etask_assignment_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `assignment_id` int(11) NOT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `start` int(10) unsigned DEFAULT NULL,
  `end` int(10) unsigned DEFAULT NULL,
  `ip_address` varchar(39) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `options` text DEFAULT NULL,
  `mkdate` int(10) unsigned DEFAULT NULL,
  `chdate` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `assignment_id` (`assignment_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `etask_assignment_ranges`
--

DROP TABLE IF EXISTS `etask_assignment_ranges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `etask_assignment_ranges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `assignment_id` int(11) NOT NULL,
  `range_type` enum('course','global','group','institute','user') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `range_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `options` text NOT NULL,
  `mkdate` int(10) unsigned DEFAULT NULL,
  `chdate` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `assignment_id` (`assignment_id`,`range_type`,`range_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `etask_assignments`
--

DROP TABLE IF EXISTS `etask_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `etask_assignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `test_id` int(11) NOT NULL,
  `range_type` enum('course','global','group','institute','user') CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `range_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `type` varchar(64) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `start` int(10) unsigned DEFAULT NULL,
  `end` int(10) unsigned DEFAULT NULL,
  `active` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `weight` float NOT NULL DEFAULT 0,
  `block_id` int(11) DEFAULT NULL,
  `options` text NOT NULL,
  `mkdate` int(10) unsigned DEFAULT NULL,
  `chdate` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `test_id` (`test_id`),
  KEY `range_id` (`range_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `etask_blocks`
--

DROP TABLE IF EXISTS `etask_blocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `etask_blocks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `range_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `group_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `visible` tinyint(4) NOT NULL DEFAULT 1,
  `weight` float DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `range_id` (`range_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `etask_group_members`
--

DROP TABLE IF EXISTS `etask_group_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `etask_group_members` (
  `group_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `start` int(10) unsigned NOT NULL,
  `end` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`group_id`,`user_id`,`start`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `etask_responses`
--

DROP TABLE IF EXISTS `etask_responses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `etask_responses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `assignment_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `response` mediumtext NOT NULL,
  `student_comment` text DEFAULT NULL,
  `ip_address` varchar(39) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `state` tinyint(1) DEFAULT NULL,
  `points` float DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `commented_solution` text DEFAULT NULL,
  `grader_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  `options` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `assignment_id` (`assignment_id`,`task_id`,`user_id`),
  KEY `user_id` (`user_id`),
  KEY `task_id` (`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `etask_task_tags`
--

DROP TABLE IF EXISTS `etask_task_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `etask_task_tags` (
  `task_id` int(11) NOT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `tag` varchar(64) NOT NULL,
  PRIMARY KEY (`task_id`,`user_id`,`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `etask_tasks`
--

DROP TABLE IF EXISTS `etask_tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `etask_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(64) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` mediumtext NOT NULL,
  `task` mediumtext NOT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  `options` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `etask_test_tags`
--

DROP TABLE IF EXISTS `etask_test_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `etask_test_tags` (
  `test_id` int(11) NOT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `tag` varchar(64) NOT NULL,
  PRIMARY KEY (`test_id`,`user_id`,`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `etask_test_tasks`
--

DROP TABLE IF EXISTS `etask_test_tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `etask_test_tasks` (
  `test_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  `part` int(11) NOT NULL DEFAULT 0,
  `points` float DEFAULT NULL,
  `options` text NOT NULL,
  `mkdate` int(10) unsigned DEFAULT NULL,
  `chdate` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`test_id`,`task_id`),
  KEY `task_id` (`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `etask_tests`
--

DROP TABLE IF EXISTS `etask_tests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `etask_tests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` mediumtext NOT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  `options` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ex_termine`
--

DROP TABLE IF EXISTS `ex_termine`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ex_termine` (
  `termin_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `range_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `autor_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `content` varchar(255) NOT NULL DEFAULT '',
  `date` int(10) unsigned NOT NULL DEFAULT 0,
  `end_time` int(10) unsigned NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  `date_typ` tinyint(4) NOT NULL DEFAULT 0,
  `raum` varchar(255) DEFAULT NULL,
  `metadate_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `resource_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`termin_id`),
  KEY `range_id` (`range_id`,`date`),
  KEY `metadate_id` (`metadate_id`,`date`),
  KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `extern_pages_configs`
--

DROP TABLE IF EXISTS `extern_pages_configs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `extern_pages_configs` (
  `config_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `range_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `type` varchar(50) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` tinytext NOT NULL,
  `conf` text NOT NULL,
  `template` mediumtext NOT NULL,
  `author_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `editor_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`config_id`),
  KEY `range_id` (`range_id`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `external_users`
--

DROP TABLE IF EXISTS `external_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `external_users` (
  `external_contact_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `foreign_id` varchar(256) DEFAULT NULL,
  `host_id` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `contact_type` varchar(16) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'anonymous',
  `name` varchar(256) NOT NULL,
  `avatar_url` varchar(256) DEFAULT NULL,
  `data` text DEFAULT NULL,
  `chdate` int(10) unsigned NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`external_contact_id`),
  KEY `mail_identifier` (`foreign_id`),
  KEY `contact_type` (`contact_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fach`
--

DROP TABLE IF EXISTS `fach`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fach` (
  `fach_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL,
  `name_kurz` varchar(50) DEFAULT NULL,
  `beschreibung` text DEFAULT NULL,
  `schlagworte` text DEFAULT NULL,
  `author_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `editor_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`fach_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `feedback`
--

DROP TABLE IF EXISTS `feedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `range_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `range_type` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `course_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `question` text NOT NULL,
  `description` text NOT NULL,
  `mode` int(10) unsigned NOT NULL,
  `results_visible` tinyint(3) unsigned NOT NULL,
  `commentable` tinyint(3) unsigned NOT NULL,
  `anonymous_entries` tinyint(1) NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `idx_range` (`range_id`,`range_type`),
  KEY `course_id` (`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `feedback_entries`
--

DROP TABLE IF EXISTS `feedback_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `feedback_entries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `feedback_id` int(10) unsigned NOT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `comment` text NOT NULL,
  `rating` tinyint(3) unsigned NOT NULL,
  `anonymous` tinyint(1) NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `feedback_id` (`feedback_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `file_refs`
--

DROP TABLE IF EXISTS `file_refs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `file_refs` (
  `id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `file_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `folder_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `downloads` int(10) unsigned NOT NULL DEFAULT 0,
  `description` text NOT NULL,
  `content_terms_of_use_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `file_id` (`file_id`),
  KEY `folder_id` (`folder_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `files`
--

DROP TABLE IF EXISTS `files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `files` (
  `id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mime_type` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL,
  `filetype` varchar(64) DEFAULT 'StandardFile',
  `size` int(10) unsigned NOT NULL,
  `metadata` text DEFAULT NULL,
  `author_name` varchar(100) NOT NULL DEFAULT '',
  `is_accessible` tinyint(1) DEFAULT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `files_search_attributes`
--

DROP TABLE IF EXISTS `files_search_attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `files_search_attributes` (
  `id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `file_ref_user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `file_ref_mkdate` int(10) unsigned NOT NULL,
  `file_ref_chdate` int(10) unsigned NOT NULL,
  `folder_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `folder_range_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `folder_range_type` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `folder_type` varchar(255) NOT NULL,
  `course_status` tinyint(3) unsigned DEFAULT NULL,
  `semester_start` int(10) unsigned DEFAULT NULL,
  `semester_end` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `folder_range_id` (`folder_range_id`),
  KEY `folder_range_type` (`folder_range_type`),
  KEY `semester_start` (`semester_start`),
  KEY `semester_end` (`semester_end`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `files_search_index`
--

DROP TABLE IF EXISTS `files_search_index`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `files_search_index` (
  `FTS_DOC_ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `file_ref_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `text` text NOT NULL,
  `relevance` float NOT NULL,
  PRIMARY KEY (`FTS_DOC_ID`),
  KEY `file_ref_id` (`file_ref_id`),
  FULLTEXT KEY `text` (`text`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `folders`
--

DROP TABLE IF EXISTS `folders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `folders` (
  `id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `parent_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `range_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `range_type` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `folder_type` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `data_content` text NOT NULL,
  `description` text NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `range_id` (`range_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forum_abo_users`
--

DROP TABLE IF EXISTS `forum_abo_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `forum_abo_users` (
  `topic_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  PRIMARY KEY (`topic_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forum_categories`
--

DROP TABLE IF EXISTS `forum_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `forum_categories` (
  `category_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `seminar_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `entry_name` varchar(255) NOT NULL,
  `pos` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`category_id`),
  KEY `seminar_id` (`seminar_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forum_categories_entries`
--

DROP TABLE IF EXISTS `forum_categories_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `forum_categories_entries` (
  `category_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `topic_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `pos` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`category_id`,`topic_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forum_entries`
--

DROP TABLE IF EXISTS `forum_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `forum_entries` (
  `topic_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `seminar_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `name` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `area` tinyint(4) NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned NOT NULL,
  `latest_chdate` int(10) unsigned DEFAULT NULL,
  `chdate` int(10) unsigned NOT NULL,
  `author` varchar(255) NOT NULL,
  `author_host` varchar(255) NOT NULL,
  `lft` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  `depth` int(11) NOT NULL,
  `anonymous` tinyint(4) NOT NULL DEFAULT 0,
  `closed` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `sticky` tinyint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`topic_id`),
  KEY `seminar_id` (`seminar_id`,`lft`),
  KEY `seminar_id_2` (`seminar_id`,`rgt`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forum_entries_issues`
--

DROP TABLE IF EXISTS `forum_entries_issues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `forum_entries_issues` (
  `topic_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `issue_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  PRIMARY KEY (`topic_id`,`issue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forum_favorites`
--

DROP TABLE IF EXISTS `forum_favorites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `forum_favorites` (
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `topic_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  PRIMARY KEY (`user_id`,`topic_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forum_likes`
--

DROP TABLE IF EXISTS `forum_likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `forum_likes` (
  `topic_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  PRIMARY KEY (`topic_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forum_visits`
--

DROP TABLE IF EXISTS `forum_visits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `forum_visits` (
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `seminar_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `visitdate` int(10) unsigned NOT NULL,
  `last_visitdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`seminar_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `global_resource_locks`
--

DROP TABLE IF EXISTS `global_resource_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `global_resource_locks` (
  `lock_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `begin` int(10) unsigned NOT NULL DEFAULT 0,
  `end` int(10) unsigned NOT NULL DEFAULT 0,
  `type` varchar(15) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`lock_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `grading_definitions`
--

DROP TABLE IF EXISTS `grading_definitions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `grading_definitions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `item` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `tool` varchar(64) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `category` varchar(255) NOT NULL,
  `position` int(11) NOT NULL DEFAULT 0,
  `weight` float unsigned NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `course_id` (`course_id`),
  KEY `tool` (`tool`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `grading_instances`
--

DROP TABLE IF EXISTS `grading_instances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `grading_instances` (
  `definition_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `rawgrade` decimal(6,5) unsigned NOT NULL,
  `feedback` varchar(255) DEFAULT NULL,
  `passed` tinyint(4) NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`definition_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `help_content`
--

DROP TABLE IF EXISTS `help_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `help_content` (
  `global_content_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `content_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `language` char(2) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'de',
  `content` text NOT NULL,
  `route` varchar(255) NOT NULL,
  `studip_version` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `position` tinyint(4) NOT NULL DEFAULT 1,
  `custom` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `visible` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `author_email` varchar(255) NOT NULL,
  `installation_id` varchar(255) NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  `comment` text DEFAULT NULL,
  PRIMARY KEY (`content_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `help_tour_audiences`
--

DROP TABLE IF EXISTS `help_tour_audiences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `help_tour_audiences` (
  `tour_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `range_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `type` enum('inst','sem','studiengang','abschluss','userdomain','tour') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned DEFAULT NULL,
  `chdate` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`tour_id`,`range_id`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `help_tour_settings`
--

DROP TABLE IF EXISTS `help_tour_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `help_tour_settings` (
  `tour_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `active` tinyint(3) unsigned NOT NULL,
  `access` enum('standard','link','autostart','autostart_once') CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `mkdate` int(10) unsigned DEFAULT NULL,
  `chdate` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`tour_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `help_tour_steps`
--

DROP TABLE IF EXISTS `help_tour_steps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `help_tour_steps` (
  `tour_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `step` tinyint(4) NOT NULL DEFAULT 1,
  `title` varchar(255) NOT NULL DEFAULT '',
  `tip` text NOT NULL,
  `orientation` enum('T','TL','TR','L','LT','LB','B','BL','BR','R','RT','RB') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'B',
  `interactive` tinyint(3) unsigned NOT NULL,
  `css_selector` varchar(255) NOT NULL,
  `route` varchar(255) NOT NULL DEFAULT '',
  `action_prev` varchar(255) NOT NULL,
  `action_next` varchar(255) NOT NULL,
  `author_email` varchar(255) NOT NULL,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`tour_id`,`step`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `help_tour_user`
--

DROP TABLE IF EXISTS `help_tour_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `help_tour_user` (
  `tour_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `step_nr` int(11) NOT NULL,
  `completed` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned DEFAULT NULL,
  `chdate` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`tour_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `help_tours`
--

DROP TABLE IF EXISTS `help_tours`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `help_tours` (
  `global_tour_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `tour_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `type` enum('tour','wizard') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `roles` varchar(255) NOT NULL,
  `version` int(10) unsigned NOT NULL DEFAULT 1,
  `language` char(2) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'de',
  `studip_version` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `installation_id` varchar(255) NOT NULL DEFAULT 'demo-installation',
  `author_email` varchar(255) NOT NULL,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`tour_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `i18n`
--

DROP TABLE IF EXISTS `i18n`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `i18n` (
  `object_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `table` varchar(64) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `field` varchar(128) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `lang` varchar(5) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `value` text DEFAULT NULL,
  PRIMARY KEY (`object_id`,`table`,`field`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `institute_plan_columns`
--

DROP TABLE IF EXISTS `institute_plan_columns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `institute_plan_columns` (
  `range_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `column` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `visible` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`range_id`,`column`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kategorien`
--

DROP TABLE IF EXISTS `kategorien`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `kategorien` (
  `kategorie_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `range_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  `priority` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`kategorie_id`),
  KEY `priority` (`priority`),
  KEY `range_id` (`range_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `keyrings`
--

DROP TABLE IF EXISTS `keyrings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `keyrings` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `range_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `range_type` varchar(16) NOT NULL,
  `public_key` blob NOT NULL,
  `private_key` blob NOT NULL DEFAULT '',
  `passphrase` varchar(512) NOT NULL DEFAULT '',
  `mkdate` int(11) NOT NULL DEFAULT 0,
  `chdate` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `range_id` (`range_id`,`range_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `licenses`
--

DROP TABLE IF EXISTS `licenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `licenses` (
  `identifier` varchar(64) NOT NULL COMMENT 'According to SPDX standard if able.',
  `name` varchar(128) DEFAULT NULL,
  `link` varchar(256) DEFAULT NULL,
  `default` tinyint(1) DEFAULT 0,
  `description` text DEFAULT NULL,
  `twillo_licensekey` varchar(16) DEFAULT NULL,
  `twillo_cclicenseversion` varchar(8) DEFAULT NULL,
  `chdate` int(11) DEFAULT NULL,
  `mkdate` int(11) DEFAULT NULL,
  PRIMARY KEY (`identifier`),
  KEY `default` (`default`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `limitedadmissions`
--

DROP TABLE IF EXISTS `limitedadmissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `limitedadmissions` (
  `rule_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `message` text NOT NULL,
  `start_time` int(10) unsigned NOT NULL DEFAULT 0,
  `end_time` int(10) unsigned NOT NULL DEFAULT 0,
  `maxnumber` int(11) NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`rule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lock_rules`
--

DROP TABLE IF EXISTS `lock_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `lock_rules` (
  `lock_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `permission` enum('autor','tutor','dozent','admin','root') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'dozent',
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `attributes` text NOT NULL,
  `object_type` enum('sem','inst','user') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'sem',
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `mkdate` int(10) unsigned DEFAULT NULL,
  `chdate` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`lock_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lockedadmissions`
--

DROP TABLE IF EXISTS `lockedadmissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `lockedadmissions` (
  `rule_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `message` text NOT NULL,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`rule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log_actions`
--

DROP TABLE IF EXISTS `log_actions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `log_actions` (
  `action_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `name` varchar(128) NOT NULL DEFAULT '',
  `description` varchar(64) DEFAULT NULL,
  `info_template` text DEFAULT NULL,
  `active` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `expires` int(10) unsigned NOT NULL DEFAULT 0,
  `filename` varchar(255) DEFAULT NULL,
  `class` varchar(255) DEFAULT NULL,
  `type` enum('core','plugin','file') CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `mkdate` int(10) unsigned DEFAULT NULL,
  `chdate` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`action_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log_events`
--

DROP TABLE IF EXISTS `log_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `log_events` (
  `event_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `action_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `affected_range_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `coaffected_range_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `info` text DEFAULT NULL,
  `dbg_info` text DEFAULT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`event_id`),
  KEY `action_id` (`action_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `login_faq`
--

DROP TABLE IF EXISTS `login_faq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `login_faq` (
  `faq_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`faq_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `loginbackgrounds`
--

DROP TABLE IF EXISTS `loginbackgrounds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `loginbackgrounds` (
  `background_id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  `mobile` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `desktop` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `in_release` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned DEFAULT NULL,
  `chdate` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`background_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lti_deployments`
--

DROP TABLE IF EXISTS `lti_deployments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `lti_deployments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tool_id` int(11) NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  `purpose` enum('general','deep_linking') NOT NULL DEFAULT 'general',
  `name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lti_grade`
--

DROP TABLE IF EXISTS `lti_grade`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `lti_grade` (
  `link_id` int(11) NOT NULL DEFAULT 0,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `score` float NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`link_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lti_resource_links`
--

DROP TABLE IF EXISTS `lti_resource_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `lti_resource_links` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `deployment_id` int(11) NOT NULL,
  `course_id` char(32) NOT NULL,
  `position` int(11) NOT NULL DEFAULT 0,
  `mkdate` int(11) NOT NULL DEFAULT 0,
  `chdate` int(11) NOT NULL DEFAULT 0,
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text DEFAULT NULL,
  `launch_url` varchar(255) NOT NULL DEFAULT '',
  `options` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `deployment_id` (`deployment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lti_tool_privacy_settings`
--

DROP TABLE IF EXISTS `lti_tool_privacy_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `lti_tool_privacy_settings` (
  `tool_id` int(11) NOT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `accepted` tinyint(1) NOT NULL DEFAULT 0,
  `allowed_optional_fields` varchar(256) NOT NULL DEFAULT '',
  `mkdate` int(11) NOT NULL DEFAULT 0,
  `chdate` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`tool_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lti_tools`
--

DROP TABLE IF EXISTS `lti_tools`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `lti_tools` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `launch_url` varchar(255) NOT NULL DEFAULT '',
  `consumer_key` varchar(255) NOT NULL DEFAULT '',
  `consumer_secret` varchar(255) NOT NULL DEFAULT '',
  `custom_parameters` text NOT NULL,
  `allow_custom_url` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `deep_linking` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `send_lis_person` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  `oauth_signature_method` varchar(10) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'sha1',
  `lti_version` varchar(8) NOT NULL DEFAULT '1.3a',
  `range_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `oidc_init_url` varchar(255) NOT NULL DEFAULT '',
  `oauth2_client_id` int(11) DEFAULT NULL,
  `jwks_url` varchar(255) NOT NULL DEFAULT '',
  `jwks_key_id` varchar(255) NOT NULL DEFAULT '',
  `deep_linking_url` varchar(255) NOT NULL DEFAULT '',
  `terms_of_use_url` varchar(255) NOT NULL DEFAULT '',
  `privacy_policy_url` varchar(255) NOT NULL DEFAULT '',
  `data_protection_notes` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mail_queue_entries`
--

DROP TABLE IF EXISTS `mail_queue_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mail_queue_entries` (
  `mail_queue_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mail` mediumtext NOT NULL,
  `message_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `tries` int(10) unsigned NOT NULL,
  `last_try` int(10) unsigned NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`mail_queue_id`),
  KEY `message_id` (`message_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `massmail_filter`
--

DROP TABLE IF EXISTS `massmail_filter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `massmail_filter` (
  `message_id` int(11) NOT NULL,
  `filter_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`message_id`,`filter_id`),
  KEY `filter_id` (`filter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `massmail_markers`
--

DROP TABLE IF EXISTS `massmail_markers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `massmail_markers` (
  `marker_id` int(11) NOT NULL AUTO_INCREMENT,
  `marker` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('text','database','function','token') CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `description` text DEFAULT NULL,
  `root_only` tinyint(1) unsigned DEFAULT 0,
  `replacement` text DEFAULT NULL,
  `replacement_female` text DEFAULT NULL,
  `replacement_unknown` text DEFAULT NULL,
  `position` tinyint(1) unsigned DEFAULT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`marker_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `massmail_messages`
--

DROP TABLE IF EXISTS `massmail_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `massmail_messages` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `author_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `send_at_date` int(11) DEFAULT NULL,
  `target` enum('all','students','employees','lecturers','courses','usernames') CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `config` longtext DEFAULT NULL,
  `exclude_users` longtext DEFAULT NULL,
  `cc` text DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `folder_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `is_template` tinyint(1) NOT NULL DEFAULT 0,
  `locked` tinyint(1) NOT NULL DEFAULT 0,
  `sent` tinyint(1) NOT NULL DEFAULT 0,
  `protected` tinyint(1) NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`message_id`),
  KEY `author_id` (`author_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `massmail_permission_degree`
--

DROP TABLE IF EXISTS `massmail_permission_degree`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `massmail_permission_degree` (
  `permission_id` int(11) NOT NULL,
  `degree_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`degree_id`),
  KEY `degree_id` (`degree_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `massmail_permission_institute`
--

DROP TABLE IF EXISTS `massmail_permission_institute`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `massmail_permission_institute` (
  `permission_id` int(11) NOT NULL,
  `institute_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`institute_id`),
  KEY `institute_id` (`institute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `massmail_permission_subject`
--

DROP TABLE IF EXISTS `massmail_permission_subject`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `massmail_permission_subject` (
  `permission_id` int(11) NOT NULL,
  `subject_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`subject_id`),
  KEY `subject_id` (`subject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `massmail_permissions`
--

DROP TABLE IF EXISTS `massmail_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `massmail_permissions` (
  `permission_id` int(11) NOT NULL AUTO_INCREMENT,
  `institute_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `min_perm` enum('admin','dozent','tutor','autor') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'admin',
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`),
  UNIQUE KEY `institute_id` (`institute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `massmail_tokens`
--

DROP TABLE IF EXISTS `massmail_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `massmail_tokens` (
  `token_id` int(11) NOT NULL AUTO_INCREMENT,
  `message_id` int(11) NOT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `token` varchar(1024) NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`token_id`),
  KEY `message_id` (`message_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `media_cache`
--

DROP TABLE IF EXISTS `media_cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `media_cache` (
  `id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `type` varchar(64) NOT NULL,
  `chdate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `expires` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `message`
--

DROP TABLE IF EXISTS `message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `message` (
  `message_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `autor_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `show_adressees` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `priority` enum('normal','high') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'normal',
  PRIMARY KEY (`message_id`),
  KEY `autor_id` (`autor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `message_tags`
--

DROP TABLE IF EXISTS `message_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `message_tags` (
  `message_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `tag` varchar(64) NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`message_id`,`user_id`,`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `message_user`
--

DROP TABLE IF EXISTS `message_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `message_user` (
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `message_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `readed` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `deleted` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `snd_rec` enum('rec','snd') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'rec',
  `answered` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`message_id`,`snd_rec`,`user_id`),
  KEY `user_id` (`user_id`,`snd_rec`,`deleted`,`readed`,`mkdate`),
  KEY `user_id_2` (`user_id`,`snd_rec`,`deleted`,`mkdate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mvv_abschl_kategorie`
--

DROP TABLE IF EXISTS `mvv_abschl_kategorie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mvv_abschl_kategorie` (
  `kategorie_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `name` varchar(255) NOT NULL,
  `name_kurz` varchar(50) DEFAULT NULL,
  `beschreibung` text DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  `author_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `editor_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`kategorie_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mvv_abschl_zuord`
--

DROP TABLE IF EXISTS `mvv_abschl_zuord`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mvv_abschl_zuord` (
  `abschluss_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `kategorie_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `position` int(11) NOT NULL DEFAULT 9999,
  `author_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `editor_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`abschluss_id`),
  KEY `kategorie_id` (`kategorie_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mvv_aufbaustudiengang`
--

DROP TABLE IF EXISTS `mvv_aufbaustudiengang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mvv_aufbaustudiengang` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `grund_stg_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `aufbau_stg_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `typ` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `kommentar` text DEFAULT NULL,
  `author_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `editor_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `grund_stg_id` (`grund_stg_id`,`aufbau_stg_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mvv_contacts`
--

DROP TABLE IF EXISTS `mvv_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mvv_contacts` (
  `contact_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `contact_status` enum('intern','extern','institution') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `alt_mail` varchar(255) NOT NULL,
  `author_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `editor_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`contact_id`),
  KEY `contact_status` (`contact_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mvv_contacts_ranges`
--

DROP TABLE IF EXISTS `mvv_contacts_ranges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mvv_contacts_ranges` (
  `contact_range_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `contact_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `range_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `range_type` enum('Modul','Studiengang','StudiengangTeil') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `type` varchar(32) NOT NULL,
  `category` varchar(32) NOT NULL,
  `position` int(11) DEFAULT NULL,
  `author_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `editor_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`contact_range_id`),
  KEY `range_id` (`range_id`),
  KEY `range_type` (`range_type`),
  KEY `type` (`type`),
  KEY `category_range` (`category`,`range_id`),
  KEY `contact_id` (`contact_id`,`range_id`,`category`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mvv_extern_contacts`
--

DROP TABLE IF EXISTS `mvv_extern_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mvv_extern_contacts` (
  `extern_contact_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `name` varchar(255) NOT NULL,
  `vorname` varchar(255) DEFAULT NULL,
  `homepage` varchar(255) NOT NULL,
  `mail` varchar(255) NOT NULL,
  `tel` varchar(255) NOT NULL,
  `author_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `editor_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`extern_contact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mvv_fach_inst`
--

DROP TABLE IF EXISTS `mvv_fach_inst`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mvv_fach_inst` (
  `fach_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `institut_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `position` int(11) NOT NULL,
  `author_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `editor_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`fach_id`,`institut_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mvv_files`
--

DROP TABLE IF EXISTS `mvv_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mvv_files` (
  `mvvfile_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `year` int(11) DEFAULT NULL,
  `type` varchar(32) DEFAULT NULL,
  `category` text DEFAULT NULL,
  `tags` text DEFAULT NULL,
  `extern_visible` tinyint(3) unsigned DEFAULT NULL,
  `author_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `editor_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`mvvfile_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mvv_files_filerefs`
--

DROP TABLE IF EXISTS `mvv_files_filerefs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mvv_files_filerefs` (
  `mvvfile_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `file_language` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `name` varchar(1000) NOT NULL,
  `fileref_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `author_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `editor_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`mvvfile_id`,`file_language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mvv_files_ranges`
--

DROP TABLE IF EXISTS `mvv_files_ranges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mvv_files_ranges` (
  `mvvfile_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `range_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `range_type` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  `author_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `editor_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`mvvfile_id`,`range_id`),
  KEY `range_id` (`range_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mvv_lvgruppe`
--

DROP TABLE IF EXISTS `mvv_lvgruppe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mvv_lvgruppe` (
  `lvgruppe_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `name` varchar(250) NOT NULL,
  `alttext` tinytext DEFAULT NULL,
  `author_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `editor_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`lvgruppe_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mvv_lvgruppe_modulteil`
--

DROP TABLE IF EXISTS `mvv_lvgruppe_modulteil`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mvv_lvgruppe_modulteil` (
  `lvgruppe_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `modulteil_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `position` int(11) NOT NULL DEFAULT 9999,
  `fn_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `author_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `editor_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`lvgruppe_id`,`modulteil_id`),
  KEY `fn_id` (`fn_id`),
  KEY `modulteil_id` (`modulteil_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mvv_lvgruppe_seminar`
--

DROP TABLE IF EXISTS `mvv_lvgruppe_seminar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mvv_lvgruppe_seminar` (
  `lvgruppe_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `seminar_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `author_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `editor_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`lvgruppe_id`,`seminar_id`),
  KEY `seminar_id` (`seminar_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mvv_modul`
--

DROP TABLE IF EXISTS `mvv_modul`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mvv_modul` (
  `modul_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `quelle` varchar(120) DEFAULT NULL,
  `variante` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `flexnow_modul` varchar(250) DEFAULT NULL,
  `code` varchar(250) DEFAULT NULL,
  `start` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `end` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `beschlussdatum` int(10) unsigned DEFAULT NULL,
  `fassung_nr` int(11) DEFAULT NULL,
  `fassung_typ` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `version` varchar(120) NOT NULL DEFAULT '1',
  `dauer` varchar(50) DEFAULT NULL,
  `kapazitaet` varchar(50) NOT NULL DEFAULT '',
  `kp` double(5,2) DEFAULT NULL,
  `wl_selbst` int(11) DEFAULT NULL,
  `wl_pruef` int(11) DEFAULT NULL,
  `pruef_ebene` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `faktor_note` varchar(10) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '1',
  `stat` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `kommentar_status` text DEFAULT NULL,
  `verantwortlich` tinytext DEFAULT NULL,
  `original_language` varchar(10) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'de_DE',
  `author_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `editor_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`modul_id`),
  KEY `stat` (`stat`),
  KEY `flexnow_modul` (`flexnow_modul`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mvv_modul_deskriptor`
--

DROP TABLE IF EXISTS `mvv_modul_deskriptor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mvv_modul_deskriptor` (
  `deskriptor_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `modul_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `verantwortlich` tinytext DEFAULT NULL,
  `bezeichnung` tinytext DEFAULT NULL,
  `voraussetzung` text DEFAULT NULL,
  `kompetenzziele` text DEFAULT NULL,
  `inhalte` text DEFAULT NULL,
  `literatur` text DEFAULT NULL,
  `links` text DEFAULT NULL,
  `kommentar` text DEFAULT NULL,
  `turnus` tinytext DEFAULT NULL,
  `kommentar_kapazitaet` text DEFAULT NULL,
  `kommentar_sws` text DEFAULT NULL,
  `kommentar_wl_selbst` text DEFAULT NULL,
  `kommentar_wl_pruef` text DEFAULT NULL,
  `kommentar_note` text DEFAULT NULL,
  `pruef_vorleistung` text DEFAULT NULL,
  `pruef_leistung` text DEFAULT NULL,
  `pruef_wiederholung` text DEFAULT NULL,
  `ersatztext` text DEFAULT NULL,
  `author_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `editor_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`deskriptor_id`),
  UNIQUE KEY `modul_id` (`modul_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mvv_modul_inst`
--

DROP TABLE IF EXISTS `mvv_modul_inst`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mvv_modul_inst` (
  `modul_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `institut_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `gruppe` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `position` int(11) NOT NULL DEFAULT 9999,
  `author_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `editor_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`modul_id`,`institut_id`),
  KEY `institut_id` (`institut_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mvv_modul_language`
--

DROP TABLE IF EXISTS `mvv_modul_language`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mvv_modul_language` (
  `modul_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `lang` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `position` int(11) NOT NULL DEFAULT 9999,
  `author_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `editor_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`modul_id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mvv_modulteil`
--

DROP TABLE IF EXISTS `mvv_modulteil`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mvv_modulteil` (
  `modulteil_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `modul_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `position` int(11) NOT NULL DEFAULT 9999,
  `flexnow_modul` varchar(250) DEFAULT NULL,
  `nummer` varchar(20) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `num_bezeichnung` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `lernlehrform` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `semester` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `kapazitaet` varchar(50) DEFAULT NULL,
  `kp` double(5,2) DEFAULT NULL,
  `sws` int(11) DEFAULT NULL,
  `wl_praesenz` int(11) DEFAULT NULL,
  `wl_bereitung` int(11) DEFAULT NULL,
  `wl_selbst` int(11) DEFAULT NULL,
  `wl_pruef` int(11) DEFAULT NULL,
  `anteil_note` int(11) DEFAULT NULL,
  `ausgleichbar` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `pflicht` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `author_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `editor_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`modulteil_id`),
  KEY `modul_id` (`modul_id`),
  KEY `flexnow_modul` (`flexnow_modul`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mvv_modulteil_deskriptor`
--

DROP TABLE IF EXISTS `mvv_modulteil_deskriptor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mvv_modulteil_deskriptor` (
  `deskriptor_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `modulteil_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `bezeichnung` tinytext NOT NULL,
  `voraussetzung` text DEFAULT NULL,
  `kommentar` text DEFAULT NULL,
  `kommentar_kapazitaet` text DEFAULT NULL,
  `kommentar_wl_praesenz` text DEFAULT NULL,
  `kommentar_wl_bereitung` text DEFAULT NULL,
  `kommentar_wl_selbst` text DEFAULT NULL,
  `kommentar_wl_pruef` text DEFAULT NULL,
  `pruef_vorleistung` text DEFAULT NULL,
  `pruef_leistung` text DEFAULT NULL,
  `kommentar_pflicht` text DEFAULT NULL,
  `author_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `editor_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`deskriptor_id`),
  KEY `modulteil_id` (`modulteil_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mvv_modulteil_language`
--

DROP TABLE IF EXISTS `mvv_modulteil_language`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mvv_modulteil_language` (
  `modulteil_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `lang` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `position` int(11) NOT NULL DEFAULT 9999,
  `author_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `editor_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`modulteil_id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mvv_modulteil_stgteilabschnitt`
--

DROP TABLE IF EXISTS `mvv_modulteil_stgteilabschnitt`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mvv_modulteil_stgteilabschnitt` (
  `modulteil_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `abschnitt_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `fachsemester` int(11) NOT NULL,
  `differenzierung` varchar(100) NOT NULL,
  `author_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `editor_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`modulteil_id`,`abschnitt_id`,`fachsemester`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mvv_ovl_conflicts`
--

DROP TABLE IF EXISTS `mvv_ovl_conflicts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mvv_ovl_conflicts` (
  `conflict_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `selection_id` int(11) NOT NULL,
  `base_abschnitt_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `base_modulteil_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `base_course_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `base_metadate_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `comp_abschnitt_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `comp_modulteil_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `comp_course_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `comp_metadate_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned DEFAULT NULL,
  `chdate` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`conflict_id`),
  KEY `selection_id` (`selection_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mvv_ovl_excludes`
--

DROP TABLE IF EXISTS `mvv_ovl_excludes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mvv_ovl_excludes` (
  `selection_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `course_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned DEFAULT NULL,
  `chdate` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`selection_id`,`course_id`),
  KEY `course_id` (`course_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mvv_ovl_selections`
--

DROP TABLE IF EXISTS `mvv_ovl_selections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mvv_ovl_selections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `selection_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `semester_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `base_version_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `comp_version_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `fachsems` varchar(100) NOT NULL DEFAULT '',
  `semtypes` varchar(100) NOT NULL DEFAULT '',
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `show_excluded` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `selection_id` (`selection_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mvv_stg_stgteil`
--

DROP TABLE IF EXISTS `mvv_stg_stgteil`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mvv_stg_stgteil` (
  `studiengang_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `stgteil_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `stgteil_bez_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `position` int(11) NOT NULL,
  `author_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `editor_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`studiengang_id`,`stgteil_id`,`stgteil_bez_id`),
  KEY `stgteil_id` (`stgteil_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mvv_stgteil`
--

DROP TABLE IF EXISTS `mvv_stgteil`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mvv_stgteil` (
  `stgteil_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `fach_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `kp` varchar(50) DEFAULT NULL,
  `semester` int(11) DEFAULT NULL,
  `zusatz` varchar(200) NOT NULL,
  `author_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `editor_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`stgteil_id`),
  KEY `fach_id` (`fach_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mvv_stgteil_bez`
--

DROP TABLE IF EXISTS `mvv_stgteil_bez`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mvv_stgteil_bez` (
  `stgteil_bez_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `name` varchar(100) NOT NULL,
  `name_kurz` varchar(20) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `position` int(11) NOT NULL DEFAULT 9999,
  `author_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `editor_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`stgteil_bez_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mvv_stgteilabschnitt`
--

DROP TABLE IF EXISTS `mvv_stgteilabschnitt`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mvv_stgteilabschnitt` (
  `abschnitt_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `version_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `position` int(11) NOT NULL DEFAULT 9999,
  `name` varchar(200) NOT NULL,
  `kommentar` varchar(200) DEFAULT NULL,
  `kp` double(5,2) DEFAULT NULL,
  `ueberschrift` tinytext DEFAULT NULL,
  `author_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `editor_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`abschnitt_id`),
  KEY `version_id` (`version_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mvv_stgteilabschnitt_modul`
--

DROP TABLE IF EXISTS `mvv_stgteilabschnitt_modul`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mvv_stgteilabschnitt_modul` (
  `abschnitt_modul_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `abschnitt_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `modul_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `flexnow_modul` varchar(250) DEFAULT NULL,
  `modulcode` varchar(250) DEFAULT NULL,
  `position` int(11) NOT NULL DEFAULT 9999,
  `bezeichnung` varchar(250) DEFAULT NULL,
  `author_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `editor_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`abschnitt_modul_id`),
  UNIQUE KEY `abschnitt_id` (`abschnitt_id`,`modul_id`) USING BTREE,
  KEY `flexnow_modul` (`flexnow_modul`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mvv_stgteilversion`
--

DROP TABLE IF EXISTS `mvv_stgteilversion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mvv_stgteilversion` (
  `version_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `stgteil_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `start_sem` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `end_sem` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `code` varchar(100) DEFAULT NULL,
  `beschlussdatum` int(10) unsigned DEFAULT NULL,
  `fassung_nr` int(11) DEFAULT NULL,
  `fassung_typ` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `beschreibung` text DEFAULT NULL,
  `stat` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `kommentar_status` text DEFAULT NULL,
  `author_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `editor_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`version_id`),
  KEY `stgteil_id` (`stgteil_id`),
  KEY `stat` (`stat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mvv_studiengang`
--

DROP TABLE IF EXISTS `mvv_studiengang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mvv_studiengang` (
  `studiengang_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `abschluss_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `typ` enum('einfach','mehrfach') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `name` varchar(255) NOT NULL,
  `name_kurz` varchar(50) DEFAULT NULL,
  `beschreibung` text DEFAULT NULL,
  `institut_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `start` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `end` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `beschlussdatum` int(10) unsigned DEFAULT NULL,
  `fassung_nr` int(11) DEFAULT NULL,
  `fassung_typ` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `stat` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `kommentar_status` text DEFAULT NULL,
  `schlagworte` text DEFAULT NULL,
  `studienzeit` tinyint(3) unsigned DEFAULT NULL,
  `studienplaetze` int(10) unsigned DEFAULT NULL,
  `abschlussgrad` varchar(32) DEFAULT NULL,
  `enroll` varchar(50) DEFAULT NULL,
  `author_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `editor_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`studiengang_id`),
  KEY `abschluss_id` (`abschluss_id`),
  KEY `institut_id` (`institut_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mvv_studycourse_language`
--

DROP TABLE IF EXISTS `mvv_studycourse_language`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mvv_studycourse_language` (
  `studiengang_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `lang` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `position` int(11) NOT NULL DEFAULT 9999,
  `author_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `editor_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`studiengang_id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mvv_studycourse_type`
--

DROP TABLE IF EXISTS `mvv_studycourse_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mvv_studycourse_type` (
  `studiengang_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `type` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `author_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `editor_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`studiengang_id`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `news` (
  `news_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `topic` varchar(255) NOT NULL DEFAULT '',
  `body` text NOT NULL,
  `author` varchar(255) NOT NULL DEFAULT '',
  `date` int(10) unsigned NOT NULL DEFAULT 0,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `expire` int(10) unsigned NOT NULL DEFAULT 0,
  `allow_comments` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `prio` tinyint(4) NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate_uid` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`news_id`),
  KEY `date` (`date`),
  KEY `chdate` (`chdate`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `news_range`
--

DROP TABLE IF EXISTS `news_range`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `news_range` (
  `news_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `range_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `mkdate` int(10) unsigned DEFAULT NULL,
  `chdate` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`news_id`,`range_id`),
  KEY `range_id` (`range_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `news_roles`
--

DROP TABLE IF EXISTS `news_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `news_roles` (
  `news_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `roleid` int(11) NOT NULL,
  PRIMARY KEY (`news_id`,`roleid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `news_rss_range`
--

DROP TABLE IF EXISTS `news_rss_range`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `news_rss_range` (
  `range_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `rss_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `range_type` enum('user','sem','inst','global') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'user',
  PRIMARY KEY (`range_id`),
  KEY `rss_id` (`rss_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oauth2_access_tokens`
--

DROP TABLE IF EXISTS `oauth2_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth2_access_tokens` (
  `id` varchar(100) NOT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `scopes` text DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL DEFAULT 0,
  `expires_at` int(11) DEFAULT NULL,
  `mkdate` int(11) NOT NULL,
  `chdate` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oauth2_auth_codes`
--

DROP TABLE IF EXISTS `oauth2_auth_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth2_auth_codes` (
  `id` varchar(100) NOT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `scopes` text DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL DEFAULT 0,
  `expires_at` int(11) DEFAULT NULL,
  `mkdate` int(11) NOT NULL,
  `chdate` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oauth2_clients`
--

DROP TABLE IF EXISTS `oauth2_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth2_clients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `secret` varchar(100) DEFAULT NULL,
  `redirect` text NOT NULL,
  `revoked` tinyint(1) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `owner` varchar(255) DEFAULT NULL,
  `homepage` varchar(255) DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `mkdate` int(11) NOT NULL,
  `chdate` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oauth2_refresh_tokens`
--

DROP TABLE IF EXISTS `oauth2_refresh_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth2_refresh_tokens` (
  `id` varchar(100) NOT NULL,
  `access_token_id` varchar(100) NOT NULL,
  `revoked` tinyint(1) NOT NULL DEFAULT 0,
  `expires_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `access_token_id` (`access_token_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `object_contentmodules`
--

DROP TABLE IF EXISTS `object_contentmodules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `object_contentmodules` (
  `object_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `module_id` varchar(255) NOT NULL DEFAULT '',
  `system_type` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `module_type` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`object_id`,`module_id`,`system_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `object_user_visits`
--

DROP TABLE IF EXISTS `object_user_visits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `object_user_visits` (
  `object_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `plugin_id` int(11) NOT NULL,
  `visitdate` int(10) unsigned NOT NULL DEFAULT 0,
  `last_visitdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`object_id`,`user_id`,`plugin_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `object_views`
--

DROP TABLE IF EXISTS `object_views`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `object_views` (
  `object_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `views` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`object_id`),
  KEY `views` (`views`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oer_abo`
--

DROP TABLE IF EXISTS `oer_abo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `oer_abo` (
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `material_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  UNIQUE KEY `user_id` (`user_id`,`material_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oer_comments`
--

DROP TABLE IF EXISTS `oer_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `oer_comments` (
  `comment_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `review_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `foreign_comment_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `comment` text NOT NULL,
  `host_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `chdate` bigint(20) NOT NULL,
  `mkdate` bigint(20) NOT NULL,
  PRIMARY KEY (`comment_id`),
  KEY `review_id` (`review_id`),
  KEY `foreign_comment_id` (`foreign_comment_id`),
  KEY `host_id` (`host_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oer_downloadcounter`
--

DROP TABLE IF EXISTS `oer_downloadcounter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `oer_downloadcounter` (
  `counter_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `material_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `longitude` double DEFAULT NULL,
  `latitude` double DEFAULT NULL,
  `mkdate` int(11) DEFAULT NULL,
  PRIMARY KEY (`counter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oer_hosts`
--

DROP TABLE IF EXISTS `oer_hosts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `oer_hosts` (
  `host_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `sorm_class` varchar(50) NOT NULL DEFAULT 'OERHost',
  `name` varchar(64) NOT NULL,
  `url` varchar(200) NOT NULL,
  `public_key` text NOT NULL,
  `private_key` text DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT 1,
  `index_server` tinyint(4) NOT NULL DEFAULT 0,
  `allowed_as_index_server` tinyint(4) NOT NULL DEFAULT 1,
  `last_updated` bigint(20) NOT NULL,
  `chdate` bigint(20) NOT NULL,
  `mkdate` bigint(20) NOT NULL,
  PRIMARY KEY (`host_id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oer_material`
--

DROP TABLE IF EXISTS `oer_material`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `oer_material` (
  `material_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `foreign_material_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `host_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `name` varchar(64) NOT NULL,
  `category` varchar(64) NOT NULL DEFAULT '',
  `draft` tinyint(1) NOT NULL DEFAULT 0,
  `filename` varchar(64) NOT NULL,
  `short_description` varchar(100) DEFAULT NULL,
  `description` text NOT NULL,
  `difficulty_start` tinyint(4) NOT NULL DEFAULT 1,
  `difficulty_end` tinyint(4) NOT NULL DEFAULT 12,
  `player_url` varchar(256) DEFAULT NULL,
  `tool` varchar(128) DEFAULT NULL,
  `content_type` varchar(256) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `front_image_content_type` varchar(64) DEFAULT NULL,
  `structure` text DEFAULT NULL,
  `rating` double DEFAULT NULL,
  `license_identifier` varchar(64) NOT NULL DEFAULT 'CC BY SA 3.0',
  `uri` varchar(1000) NOT NULL DEFAULT '',
  `uri_hash` char(32) NOT NULL DEFAULT '',
  `published_id_on_twillo` varchar(50) DEFAULT NULL,
  `source_url` varchar(256) DEFAULT NULL,
  `data` text DEFAULT NULL,
  `chdate` bigint(20) NOT NULL,
  `mkdate` int(11) NOT NULL,
  PRIMARY KEY (`material_id`),
  KEY `host_id` (`host_id`),
  KEY `category` (`category`),
  KEY `foreign_material_id` (`foreign_material_id`),
  KEY `license_identifier` (`license_identifier`),
  KEY `uri_hash` (`uri_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oer_material_users`
--

DROP TABLE IF EXISTS `oer_material_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `oer_material_users` (
  `material_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `external_contact` int(11) NOT NULL DEFAULT 0,
  `position` int(11) NOT NULL DEFAULT 1,
  `chdate` int(11) NOT NULL,
  `mkdate` int(11) NOT NULL,
  PRIMARY KEY (`material_id`,`user_id`,`external_contact`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oer_post_upload`
--

DROP TABLE IF EXISTS `oer_post_upload`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `oer_post_upload` (
  `file_ref_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `reminder_date` int(10) unsigned DEFAULT NULL,
  `mkdate` int(11) NOT NULL,
  `chdate` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`file_ref_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oer_reviews`
--

DROP TABLE IF EXISTS `oer_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `oer_reviews` (
  `review_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `material_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `foreign_review_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `host_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `rating` int(11) NOT NULL,
  `review` text NOT NULL,
  `chdate` int(11) NOT NULL,
  `mkdate` int(11) NOT NULL,
  PRIMARY KEY (`review_id`),
  UNIQUE KEY `unique_users` (`user_id`,`host_id`,`material_id`),
  KEY `material_id` (`material_id`),
  KEY `foreign_review_id` (`foreign_review_id`),
  KEY `user_id` (`user_id`),
  KEY `host_id` (`host_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oer_tags`
--

DROP TABLE IF EXISTS `oer_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `oer_tags` (
  `tag_hash` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`tag_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oer_tags_material`
--

DROP TABLE IF EXISTS `oer_tags_material`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `oer_tags_material` (
  `material_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `tag_hash` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  UNIQUE KEY `unique_tags` (`material_id`,`tag_hash`),
  KEY `tag_hash` (`tag_hash`),
  KEY `material_id` (`material_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oer_user`
--

DROP TABLE IF EXISTS `oer_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `oer_user` (
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `foreign_user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `host_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `name` varchar(100) NOT NULL,
  `avatar` varchar(256) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `chdate` int(11) NOT NULL,
  `mkdate` int(11) NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `unique_users` (`foreign_user_id`,`host_id`),
  KEY `foreign_user_id` (`foreign_user_id`),
  KEY `host_id` (`host_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `opengraphdata`
--

DROP TABLE IF EXISTS `opengraphdata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `opengraphdata` (
  `opengraph_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hash` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `url` varchar(1000) NOT NULL,
  `is_opengraph` tinyint(3) unsigned DEFAULT NULL,
  `title` text DEFAULT NULL,
  `image` varchar(1024) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `type` varchar(64) DEFAULT NULL,
  `data` text NOT NULL,
  `last_update` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`opengraph_id`),
  UNIQUE KEY `hash` (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `participantrestrictedadmissions`
--

DROP TABLE IF EXISTS `participantrestrictedadmissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `participantrestrictedadmissions` (
  `rule_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `distribution_time` int(10) unsigned NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`rule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `passwordadmissions`
--

DROP TABLE IF EXISTS `passwordadmissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `passwordadmissions` (
  `rule_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `message` text DEFAULT NULL,
  `start_time` int(10) unsigned NOT NULL DEFAULT 0,
  `end_time` int(10) unsigned NOT NULL DEFAULT 0,
  `password` varchar(255) DEFAULT NULL,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`rule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `personal_notifications`
--

DROP TABLE IF EXISTS `personal_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_notifications` (
  `personal_notification_id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(512) NOT NULL DEFAULT '',
  `text` text NOT NULL,
  `avatar` varchar(256) NOT NULL DEFAULT '',
  `dialog` tinyint(4) NOT NULL DEFAULT 0,
  `html_id` varchar(64) NOT NULL DEFAULT '',
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`personal_notification_id`),
  KEY `html_id` (`html_id`),
  KEY `url` (`url`(256))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `personal_notifications_user`
--

DROP TABLE IF EXISTS `personal_notifications_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_notifications_user` (
  `personal_notification_id` int(10) unsigned NOT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `seen` tinyint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`personal_notification_id`,`user_id`),
  KEY `user_id` (`user_id`,`seen`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `plugin_assets`
--

DROP TABLE IF EXISTS `plugin_assets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `plugin_assets` (
  `asset_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `plugin_id` int(10) unsigned NOT NULL,
  `type` enum('css','js') CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `filename` varchar(255) NOT NULL DEFAULT '',
  `storagename` varchar(255) NOT NULL DEFAULT '',
  `size` int(10) unsigned DEFAULT NULL,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`asset_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `plugins`
--

DROP TABLE IF EXISTS `plugins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `plugins` (
  `pluginid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pluginclassname` varchar(255) NOT NULL DEFAULT '',
  `pluginpath` varchar(255) NOT NULL DEFAULT '',
  `pluginname` varchar(45) NOT NULL DEFAULT '',
  `plugintype` text NOT NULL,
  `enabled` enum('yes','no') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'no',
  `navigationpos` int(10) unsigned NOT NULL DEFAULT 0,
  `dependentonid` int(10) unsigned DEFAULT NULL,
  `automatic_update_url` varchar(256) DEFAULT NULL,
  `automatic_update_secret` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `description` text DEFAULT NULL,
  `description_mode` enum('add','override_description','replace_all') DEFAULT 'add',
  `highlight_until` int(10) unsigned DEFAULT NULL,
  `highlight_text` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`pluginid`),
  KEY `highlight_until` (`highlight_until`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `plugins_activated`
--

DROP TABLE IF EXISTS `plugins_activated`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `plugins_activated` (
  `pluginid` int(10) unsigned NOT NULL DEFAULT 0,
  `range_type` enum('user') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'user',
  `range_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `state` tinyint(3) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`pluginid`,`range_type`,`range_id`),
  KEY `range` (`range_id`,`range_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prefadmission_condition`
--

DROP TABLE IF EXISTS `prefadmission_condition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `prefadmission_condition` (
  `rule_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `condition_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `chance` int(11) NOT NULL DEFAULT 1,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`rule_id`,`condition_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prefadmissions`
--

DROP TABLE IF EXISTS `prefadmissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `prefadmissions` (
  `rule_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `favor_semester` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`rule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `priorities`
--

DROP TABLE IF EXISTS `priorities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `priorities` (
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `set_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `seminar_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `priority` int(11) NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`,`set_id`,`seminar_id`),
  KEY `user_rule_priority` (`user_id`,`priority`,`set_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `questionnaire_anonymous_answers`
--

DROP TABLE IF EXISTS `questionnaire_anonymous_answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `questionnaire_anonymous_answers` (
  `anonymous_answer_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `questionnaire_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`anonymous_answer_id`),
  UNIQUE KEY `questionnaire_id_user_id` (`questionnaire_id`,`user_id`),
  KEY `questionnaire_id` (`questionnaire_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `questionnaire_answers`
--

DROP TABLE IF EXISTS `questionnaire_answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `questionnaire_answers` (
  `answer_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `question_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `answerdata` text NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`answer_id`),
  KEY `question_id` (`question_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `questionnaire_assignments`
--

DROP TABLE IF EXISTS `questionnaire_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `questionnaire_assignments` (
  `assignment_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `questionnaire_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `range_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `range_type` varchar(64) NOT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`assignment_id`),
  KEY `questionnaire_id` (`questionnaire_id`),
  KEY `range_id_range_type` (`range_id`,`range_type`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `questionnaire_questions`
--

DROP TABLE IF EXISTS `questionnaire_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `questionnaire_questions` (
  `question_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `questionnaire_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `questiontype` varchar(64) NOT NULL DEFAULT '',
  `internal_name` varchar(128) DEFAULT NULL,
  `questiondata` text NOT NULL,
  `position` int(11) NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`question_id`),
  KEY `questionnaire_id` (`questionnaire_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `questionnaires`
--

DROP TABLE IF EXISTS `questionnaires`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `questionnaires` (
  `questionnaire_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `title` varchar(128) NOT NULL,
  `description` text DEFAULT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `startdate` int(10) unsigned DEFAULT NULL,
  `stopdate` int(10) unsigned DEFAULT NULL,
  `visible` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `anonymous` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `resultvisibility` enum('always','never','afterending','afterparticipation') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'always',
  `editanswers` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `copyable` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`questionnaire_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `range_tree`
--

DROP TABLE IF EXISTS `range_tree`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `range_tree` (
  `item_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `parent_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `level` int(11) NOT NULL DEFAULT 0,
  `priority` int(11) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL DEFAULT '',
  `studip_object` varchar(10) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `studip_object_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`item_id`),
  KEY `parent_id` (`parent_id`),
  KEY `priority` (`priority`),
  KEY `studip_object_id` (`studip_object_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `resource_booking_intervals`
--

DROP TABLE IF EXISTS `resource_booking_intervals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `resource_booking_intervals` (
  `interval_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `resource_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `booking_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `begin` int(10) unsigned NOT NULL DEFAULT 0,
  `end` int(10) unsigned NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  `takes_place` tinyint(3) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`interval_id`),
  KEY `resource_id` (`resource_id`,`takes_place`,`end`),
  KEY `booking_id` (`booking_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `resource_bookings`
--

DROP TABLE IF EXISTS `resource_bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `resource_bookings` (
  `id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `resource_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `range_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `description` text DEFAULT NULL,
  `begin` int(10) unsigned NOT NULL DEFAULT 0,
  `end` int(10) unsigned NOT NULL DEFAULT 0,
  `repeat_end` int(10) unsigned DEFAULT NULL,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  `internal_comment` text DEFAULT NULL,
  `preparation_time` int(11) NOT NULL DEFAULT 0,
  `booking_type` tinyint(4) NOT NULL DEFAULT 0,
  `booking_user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `repetition_interval` varchar(24) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `weekdays` varchar(7) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `assign_user_id` (`range_id`),
  KEY `resource_id` (`resource_id`,`booking_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `resource_categories`
--

DROP TABLE IF EXISTS `resource_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `resource_categories` (
  `id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `system` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `iconnr` int(11) DEFAULT 1,
  `class_name` varchar(60) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'Resource',
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `resource_category_properties`
--

DROP TABLE IF EXISTS `resource_category_properties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `resource_category_properties` (
  `category_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `property_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `requestable` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `protected` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `system` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `form_text` text DEFAULT NULL,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`category_id`,`property_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `resource_permissions`
--

DROP TABLE IF EXISTS `resource_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `resource_permissions` (
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `resource_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `perms` varchar(10) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`,`resource_id`),
  KEY `resource_id` (`resource_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `resource_properties`
--

DROP TABLE IF EXISTS `resource_properties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `resource_properties` (
  `resource_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `property_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `state` text NOT NULL,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`resource_id`,`property_id`),
  KEY `property_id` (`property_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `resource_property_definitions`
--

DROP TABLE IF EXISTS `resource_property_definitions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `resource_property_definitions` (
  `property_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text DEFAULT NULL,
  `type` enum('bool','text','num','select','user','institute','position','fileref','url') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `options` text NOT NULL,
  `system` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `info_label` tinyint(4) NOT NULL DEFAULT 0,
  `display_name` varchar(512) NOT NULL DEFAULT '',
  `searchable` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `range_search` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `write_permission_level` varchar(16) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'admin-global',
  `property_group_id` int(11) DEFAULT NULL,
  `property_group_pos` tinyint(4) DEFAULT NULL,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`property_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `resource_property_groups`
--

DROP TABLE IF EXISTS `resource_property_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `resource_property_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `position` tinyint(4) NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `resource_request_appointments`
--

DROP TABLE IF EXISTS `resource_request_appointments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `resource_request_appointments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `request_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `appointment_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `resource_request_properties`
--

DROP TABLE IF EXISTS `resource_request_properties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `resource_request_properties` (
  `request_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `property_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `state` text DEFAULT NULL,
  `mkdate` int(10) unsigned DEFAULT NULL,
  `chdate` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`request_id`,`property_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `resource_requests`
--

DROP TABLE IF EXISTS `resource_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `resource_requests` (
  `id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `course_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `termin_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `metadate_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `last_modified_by` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `resource_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `category_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT '',
  `comment` text DEFAULT NULL,
  `reply_comment` text DEFAULT NULL,
  `reply_recipients` enum('requester','lecturer') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'requester',
  `closed` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned DEFAULT NULL,
  `chdate` int(10) unsigned DEFAULT NULL,
  `begin` int(10) unsigned NOT NULL DEFAULT 0,
  `end` int(10) unsigned NOT NULL DEFAULT 0,
  `preparation_time` int(11) NOT NULL DEFAULT 0,
  `marked` tinyint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `termin_id` (`termin_id`),
  KEY `seminar_id` (`course_id`),
  KEY `user_id` (`user_id`),
  KEY `resource_id` (`resource_id`),
  KEY `category_id` (`category_id`),
  KEY `closed` (`closed`,`id`,`resource_id`),
  KEY `metadate_id` (`metadate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `resource_temporary_permissions`
--

DROP TABLE IF EXISTS `resource_temporary_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `resource_temporary_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `resource_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `begin` int(10) unsigned NOT NULL DEFAULT 0,
  `end` int(10) unsigned NOT NULL DEFAULT 0,
  `perms` varchar(10) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `resource_id` (`resource_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `resources`
--

DROP TABLE IF EXISTS `resources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `resources` (
  `id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `parent_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `category_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `level` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text DEFAULT NULL,
  `requestable` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `lockable` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `booking_plan_request` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  `sort_position` tinyint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `roleid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rolename` varchar(80) NOT NULL DEFAULT '',
  `system` enum('y','n') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'n',
  PRIMARY KEY (`roleid`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `roles_plugins`
--

DROP TABLE IF EXISTS `roles_plugins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles_plugins` (
  `roleid` int(10) unsigned NOT NULL DEFAULT 0,
  `pluginid` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`roleid`,`pluginid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `roles_studipperms`
--

DROP TABLE IF EXISTS `roles_studipperms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles_studipperms` (
  `roleid` int(10) unsigned NOT NULL DEFAULT 0,
  `permname` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`roleid`,`permname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `roles_user`
--

DROP TABLE IF EXISTS `roles_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles_user` (
  `roleid` int(10) unsigned NOT NULL DEFAULT 0,
  `userid` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `institut_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`roleid`,`userid`,`institut_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `schedule_courses`
--

DROP TABLE IF EXISTS `schedule_courses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `schedule_courses` (
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `course_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `metadate_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `visible` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `mkdate` int(11) unsigned NOT NULL DEFAULT 0,
  `chdate` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`,`course_id`,`metadate_id`),
  KEY `seminar_id` (`course_id`),
  KEY `metadate_id` (`metadate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `schedule_entries`
--

DROP TABLE IF EXISTS `schedule_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `schedule_entries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `start_time` smallint(6) NOT NULL,
  `end_time` smallint(6) NOT NULL,
  `dow` tinyint(1) NOT NULL,
  `label` varchar(255) NOT NULL DEFAULT '',
  `content` text DEFAULT NULL,
  `colour_id` tinyint(3) NOT NULL DEFAULT 0,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` bigint(10) NOT NULL DEFAULT 0,
  `chdate` bigint(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `schema_version`
--

DROP TABLE IF EXISTS `schema_version`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `schema_version` (
  `domain` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `branch` varchar(64) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '0',
  `version` int(10) unsigned NOT NULL,
  PRIMARY KEY (`domain`,`branch`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scm`
--

DROP TABLE IF EXISTS `scm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `scm` (
  `scm_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `range_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `tab_name` varchar(255) NOT NULL DEFAULT '',
  `content` mediumtext NOT NULL,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  `position` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`scm_id`),
  KEY `chdate` (`chdate`),
  KEY `range_id` (`range_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sem_classes`
--

DROP TABLE IF EXISTS `sem_classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sem_classes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `only_inst_user` tinyint(3) unsigned NOT NULL,
  `default_read_level` int(11) NOT NULL,
  `default_write_level` int(11) NOT NULL,
  `bereiche` tinyint(3) unsigned NOT NULL,
  `module` tinyint(3) unsigned NOT NULL,
  `show_browse` tinyint(3) unsigned NOT NULL,
  `write_access_nobody` tinyint(3) unsigned NOT NULL,
  `topic_create_autor` tinyint(3) unsigned NOT NULL,
  `visible` tinyint(3) unsigned NOT NULL,
  `course_creation_forbidden` tinyint(3) unsigned NOT NULL,
  `modules` text NOT NULL,
  `description` text NOT NULL,
  `create_description` text NOT NULL,
  `studygroup_mode` tinyint(3) unsigned NOT NULL,
  `admission_prelim_default` tinyint(4) NOT NULL DEFAULT 0,
  `admission_type_default` tinyint(4) NOT NULL DEFAULT 0,
  `title_dozent` varchar(64) DEFAULT NULL,
  `title_dozent_plural` varchar(64) DEFAULT NULL,
  `title_tutor` varchar(64) DEFAULT NULL,
  `title_tutor_plural` varchar(64) DEFAULT NULL,
  `title_autor` varchar(64) DEFAULT NULL,
  `title_autor_plural` varchar(64) DEFAULT NULL,
  `show_raumzeit` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `is_group` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `admission_turnout_mandatory` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `unlimited_forbidden` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sem_tree`
--

DROP TABLE IF EXISTS `sem_tree`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sem_tree` (
  `sem_tree_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `parent_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `priority` tinyint(4) NOT NULL DEFAULT 0,
  `info` text NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `studip_object_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `type` tinyint(3) unsigned NOT NULL,
  `mkdate` int(10) unsigned DEFAULT NULL,
  `chdate` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`sem_tree_id`),
  KEY `parent_id` (`parent_id`),
  KEY `priority` (`priority`),
  KEY `studip_object_id` (`studip_object_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sem_types`
--

DROP TABLE IF EXISTS `sem_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sem_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `class` int(11) NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `semester_courses`
--

DROP TABLE IF EXISTS `semester_courses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `semester_courses` (
  `semester_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `course_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `mkdate` int(11) NOT NULL DEFAULT 0,
  `chdate` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`semester_id`,`course_id`),
  KEY `course_id` (`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `semester_data`
--

DROP TABLE IF EXISTS `semester_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `semester_data` (
  `semester_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `semester_token` varchar(10) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `beginn` int(10) unsigned DEFAULT NULL,
  `ende` int(10) unsigned DEFAULT NULL,
  `sem_wechsel` int(10) unsigned DEFAULT NULL,
  `vorles_beginn` int(10) unsigned DEFAULT NULL,
  `vorles_ende` int(10) unsigned DEFAULT NULL,
  `visible` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `external_id` varchar(50) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `mkdate` int(10) unsigned DEFAULT NULL,
  `chdate` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`semester_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `semester_holiday`
--

DROP TABLE IF EXISTS `semester_holiday`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `semester_holiday` (
  `holiday_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `semester_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `beginn` int(10) unsigned DEFAULT NULL,
  `ende` int(10) unsigned NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned DEFAULT NULL,
  `chdate` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`holiday_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `seminar_courseset`
--

DROP TABLE IF EXISTS `seminar_courseset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `seminar_courseset` (
  `set_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `seminar_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`set_id`,`seminar_id`),
  KEY `seminar_id` (`seminar_id`,`set_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `seminar_cycle_dates`
--

DROP TABLE IF EXISTS `seminar_cycle_dates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `seminar_cycle_dates` (
  `metadate_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `seminar_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `weekday` tinyint(3) unsigned NOT NULL,
  `description` varchar(255) NOT NULL DEFAULT '',
  `sws` decimal(2,1) NOT NULL DEFAULT 0.0,
  `cycle` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `week_offset` int(11) NOT NULL DEFAULT 0,
  `end_offset` int(11) DEFAULT NULL,
  `sorter` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`metadate_id`),
  KEY `seminar_id` (`seminar_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `seminar_inst`
--

DROP TABLE IF EXISTS `seminar_inst`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `seminar_inst` (
  `seminar_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `institut_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`seminar_id`,`institut_id`),
  KEY `institut_id` (`institut_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `seminar_sem_tree`
--

DROP TABLE IF EXISTS `seminar_sem_tree`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `seminar_sem_tree` (
  `seminar_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `sem_tree_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`seminar_id`,`sem_tree_id`),
  KEY `sem_tree_id` (`sem_tree_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `seminar_user`
--

DROP TABLE IF EXISTS `seminar_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `seminar_user` (
  `Seminar_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `status` enum('user','autor','tutor','dozent') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'user',
  `position` int(11) NOT NULL DEFAULT 0,
  `gruppe` tinyint(4) NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `comment` varchar(255) NOT NULL DEFAULT '',
  `visible` enum('yes','no','unknown') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'unknown',
  `label` varchar(128) NOT NULL DEFAULT '',
  `bind_calendar` tinyint(3) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`Seminar_id`,`user_id`),
  KEY `status` (`status`,`Seminar_id`),
  KEY `user_id` (`user_id`,`Seminar_id`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `seminar_user_notifications`
--

DROP TABLE IF EXISTS `seminar_user_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `seminar_user_notifications` (
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `seminar_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `notification_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `chdate` int(10) unsigned NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`seminar_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `seminar_userdomains`
--

DROP TABLE IF EXISTS `seminar_userdomains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `seminar_userdomains` (
  `seminar_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `userdomain_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`seminar_id`,`userdomain_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `seminare`
--

DROP TABLE IF EXISTS `seminare`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `seminare` (
  `Seminar_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '0',
  `VeranstaltungsNummer` varchar(100) DEFAULT NULL,
  `Institut_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '0',
  `Name` varchar(255) NOT NULL DEFAULT '',
  `Untertitel` varchar(255) DEFAULT NULL,
  `status` int(10) unsigned NOT NULL DEFAULT 1,
  `Beschreibung` text NOT NULL,
  `Ort` varchar(255) DEFAULT NULL,
  `Sonstiges` text DEFAULT NULL,
  `Lesezugriff` tinyint(4) NOT NULL DEFAULT 0,
  `Schreibzugriff` tinyint(4) NOT NULL DEFAULT 0,
  `art` varchar(255) DEFAULT NULL,
  `teilnehmer` text DEFAULT NULL,
  `vorrausetzungen` text DEFAULT NULL,
  `lernorga` text DEFAULT NULL,
  `leistungsnachweis` text DEFAULT NULL,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  `ects` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `admission_turnout` int(11) DEFAULT NULL,
  `admission_binding` tinyint(4) DEFAULT NULL,
  `admission_prelim` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `admission_prelim_txt` text DEFAULT NULL,
  `admission_disable_waitlist` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `visible` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `showscore` tinyint(4) DEFAULT 0,
  `aux_lock_rule` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `aux_lock_rule_forced` tinyint(4) NOT NULL DEFAULT 0,
  `lock_rule` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `admission_waitlist_max` int(10) unsigned NOT NULL DEFAULT 0,
  `admission_disable_waitlist_move` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `completion` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `parent_course` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `expires` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`Seminar_id`),
  KEY `Institut_id` (`Institut_id`),
  KEY `visible` (`visible`),
  KEY `status` (`status`,`Seminar_id`),
  KEY `parent_course` (`parent_course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `separable_room_parts`
--

DROP TABLE IF EXISTS `separable_room_parts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `separable_room_parts` (
  `separable_room_id` int(11) NOT NULL,
  `room_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`separable_room_id`,`room_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `separable_rooms`
--

DROP TABLE IF EXISTS `separable_rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `separable_rooms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `building_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `name` varchar(256) NOT NULL DEFAULT '',
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `session_data`
--

DROP TABLE IF EXISTS `session_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `session_data` (
  `sid` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `val` mediumblob NOT NULL,
  `changed` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`sid`),
  KEY `changed` (`changed`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `siteinfo_details`
--

DROP TABLE IF EXISTS `siteinfo_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `siteinfo_details` (
  `detail_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `rubric_id` smallint(5) unsigned NOT NULL,
  `position` tinyint(3) unsigned DEFAULT NULL,
  `draft_status` tinyint(4) NOT NULL DEFAULT 0,
  `page_disabled_nobody` tinyint(4) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`detail_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `siteinfo_rubrics`
--

DROP TABLE IF EXISTS `siteinfo_rubrics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `siteinfo_rubrics` (
  `rubric_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `position` tinyint(3) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`rubric_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `statusgruppe_user`
--

DROP TABLE IF EXISTS `statusgruppe_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `statusgruppe_user` (
  `statusgruppe_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `position` int(11) NOT NULL DEFAULT 0,
  `visible` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `inherit` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `mkdate` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`statusgruppe_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `statusgruppen`
--

DROP TABLE IF EXISTS `statusgruppen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `statusgruppen` (
  `statusgruppe_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text DEFAULT NULL,
  `range_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `position` int(11) NOT NULL DEFAULT 0,
  `size` int(11) NOT NULL DEFAULT 0,
  `selfassign` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `selfassign_start` int(10) unsigned NOT NULL DEFAULT 0,
  `selfassign_end` int(10) unsigned NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  `calendar_group` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `name_w` varchar(255) DEFAULT NULL,
  `name_m` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`statusgruppe_id`),
  KEY `range_id` (`range_id`),
  KEY `position` (`position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stock_images`
--

DROP TABLE IF EXISTS `stock_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `license` text NOT NULL,
  `author` varchar(255) NOT NULL,
  `mime_type` varchar(64) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `size` int(10) unsigned NOT NULL,
  `width` int(10) unsigned NOT NULL,
  `height` int(10) unsigned NOT NULL,
  `palette` text NOT NULL,
  `tags` text NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `studygroup_courses`
--

DROP TABLE IF EXISTS `studygroup_courses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `studygroup_courses` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `studygroup_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `course_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `mkdate` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `studygroup_id` (`studygroup_id`,`course_id`),
  KEY `studygroup_id_2` (`studygroup_id`),
  KEY `course_id` (`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `studygroup_courses_proposals`
--

DROP TABLE IF EXISTS `studygroup_courses_proposals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `studygroup_courses_proposals` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `studygroup_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `course_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `proposed_from` enum('course','studygroup') NOT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `mkdate` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `studygroup_id` (`studygroup_id`,`course_id`),
  KEY `course_id` (`course_id`),
  KEY `studygroup_id_2` (`studygroup_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `studygroup_invitations`
--

DROP TABLE IF EXISTS `studygroup_invitations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `studygroup_invitations` (
  `sem_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`sem_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `studygroup_stgteil`
--

DROP TABLE IF EXISTS `studygroup_stgteil`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `studygroup_stgteil` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `studygroup_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `stgteil_id` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `studygroup_id` (`studygroup_id`,`stgteil_id`),
  KEY `studygroup_id_2` (`studygroup_id`),
  KEY `stgteil_id` (`stgteil_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tags` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `active` tinyint(1) DEFAULT 1,
  `chdate` int(11) unsigned DEFAULT NULL,
  `mkdate` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tags_relations`
--

DROP TABLE IF EXISTS `tags_relations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tags_relations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tag_id` int(11) unsigned DEFAULT NULL,
  `range_id` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `range_type` varchar(100) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `mkdate` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tag_id` (`tag_id`),
  KEY `range_id` (`range_id`),
  KEY `range_type` (`range_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `termin_related_groups`
--

DROP TABLE IF EXISTS `termin_related_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `termin_related_groups` (
  `termin_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `statusgruppe_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  PRIMARY KEY (`termin_id`,`statusgruppe_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `termin_related_persons`
--

DROP TABLE IF EXISTS `termin_related_persons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `termin_related_persons` (
  `range_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  PRIMARY KEY (`range_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `termine`
--

DROP TABLE IF EXISTS `termine`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `termine` (
  `termin_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `range_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `autor_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `content` varchar(255) NOT NULL DEFAULT '',
  `date` int(10) unsigned NOT NULL DEFAULT 0,
  `end_time` int(10) unsigned NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  `date_typ` tinyint(4) NOT NULL DEFAULT 0,
  `raum` varchar(255) DEFAULT NULL,
  `metadate_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `number_of_participants` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`termin_id`),
  KEY `metadate_id` (`metadate_id`,`date`),
  KEY `range_id` (`range_id`,`date`),
  KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `termsadmissions`
--

DROP TABLE IF EXISTS `termsadmissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `termsadmissions` (
  `rule_id` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `terms` text NOT NULL,
  `mkdate` int(11) NOT NULL DEFAULT 0,
  `chdate` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`rule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `themen`
--

DROP TABLE IF EXISTS `themen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `themen` (
  `issue_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `seminar_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `author_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `priority` smallint(5) unsigned NOT NULL DEFAULT 0,
  `paper_related` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`issue_id`),
  KEY `seminar_id` (`seminar_id`,`priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `themen_termine`
--

DROP TABLE IF EXISTS `themen_termine`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `themen_termine` (
  `issue_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `termin_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`issue_id`,`termin_id`),
  KEY `termin_id` (`termin_id`,`issue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `timedadmissions`
--

DROP TABLE IF EXISTS `timedadmissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `timedadmissions` (
  `rule_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `message` text NOT NULL,
  `start_time` int(10) unsigned NOT NULL DEFAULT 0,
  `end_time` int(10) unsigned NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`rule_id`),
  KEY `start_time` (`start_time`),
  KEY `end_time` (`end_time`),
  KEY `start_end` (`start_time`,`end_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tools_activated`
--

DROP TABLE IF EXISTS `tools_activated`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tools_activated` (
  `range_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `range_type` enum('course','institute') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `plugin_id` int(10) unsigned NOT NULL,
  `position` tinyint(3) unsigned NOT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`range_id`,`plugin_id`),
  KEY `plugin_id` (`plugin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_factorlist`
--

DROP TABLE IF EXISTS `user_factorlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_factorlist` (
  `list_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `mkdate` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`list_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_info`
--

DROP TABLE IF EXISTS `user_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_info` (
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `hobby` mediumtext NOT NULL,
  `lebenslauf` mediumtext NOT NULL,
  `publi` mediumtext NOT NULL,
  `schwerp` text NOT NULL,
  `Home` varchar(200) NOT NULL DEFAULT '',
  `privatnr` varchar(255) NOT NULL DEFAULT '',
  `privatcell` varchar(255) NOT NULL DEFAULT '',
  `privadr` varchar(64) NOT NULL DEFAULT '',
  `score` int(10) unsigned NOT NULL DEFAULT 0,
  `geschlecht` tinyint(4) NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  `title_front` varchar(64) NOT NULL DEFAULT '',
  `title_rear` varchar(64) NOT NULL DEFAULT '',
  `preferred_language` varchar(20) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `smsforward_copy` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `smsforward_rec` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `email_forward` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `motto` varchar(255) NOT NULL DEFAULT '',
  `lock_rule` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `oercampus_description` text DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `score` (`score`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_inst`
--

DROP TABLE IF EXISTS `user_inst`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_inst` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '0',
  `Institut_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '0',
  `inst_perms` enum('user','autor','tutor','dozent','admin') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'user',
  `sprechzeiten` varchar(200) NOT NULL DEFAULT '',
  `raum` varchar(200) NOT NULL DEFAULT '',
  `Telefon` varchar(255) NOT NULL DEFAULT '',
  `Fax` varchar(255) NOT NULL DEFAULT '',
  `externdefault` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `priority` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `visible` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `mkdate` int(10) unsigned DEFAULT NULL,
  `chdate` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_inst` (`Institut_id`,`user_id`),
  KEY `inst_perms` (`inst_perms`,`Institut_id`),
  KEY `user_id` (`user_id`,`inst_perms`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_online`
--

DROP TABLE IF EXISTS `user_online`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_online` (
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `last_lifesign` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`),
  KEY `last_lifesign` (`last_lifesign`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_studiengang`
--

DROP TABLE IF EXISTS `user_studiengang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_studiengang` (
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `fach_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `semester` tinyint(4) DEFAULT 0,
  `abschluss_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '0',
  `version_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `mkdate` int(10) unsigned DEFAULT NULL,
  `chdate` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_id`,`fach_id`,`abschluss_id`),
  KEY `studiengang_id` (`fach_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_token`
--

DROP TABLE IF EXISTS `user_token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_token` (
  `token` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `expiration` int(10) unsigned NOT NULL,
  `mkdate` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`token`),
  KEY `index_expiration` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_userdomains`
--

DROP TABLE IF EXISTS `user_userdomains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_userdomains` (
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `userdomain_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`user_id`,`userdomain_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_visibility`
--

DROP TABLE IF EXISTS `user_visibility`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_visibility` (
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `online` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `search` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `email` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `homepage` text NOT NULL,
  `default_homepage_visibility` int(11) NOT NULL DEFAULT 0,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_visibility_settings`
--

DROP TABLE IF EXISTS `user_visibility_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_visibility_settings` (
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `visibilityid` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `category` varchar(128) NOT NULL,
  `name` varchar(128) NOT NULL,
  `state` int(11) DEFAULT NULL,
  `plugin` int(11) DEFAULT NULL,
  `identifier` varchar(64) NOT NULL,
  PRIMARY KEY (`visibilityid`),
  KEY `parent_id` (`parent_id`),
  KEY `identifier` (`identifier`),
  KEY `userid` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `userdomains`
--

DROP TABLE IF EXISTS `userdomains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `userdomains` (
  `userdomain_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `restricted_access` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`userdomain_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `userfilter`
--

DROP TABLE IF EXISTS `userfilter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `userfilter` (
  `filter_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `range_id` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `range_type` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`filter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `userfilter_fields`
--

DROP TABLE IF EXISTS `userfilter_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `userfilter_fields` (
  `field_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `filter_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `type` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `value` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `compare_op` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `mkdate` int(10) unsigned NOT NULL DEFAULT 0,
  `chdate` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `userlimits`
--

DROP TABLE IF EXISTS `userlimits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `userlimits` (
  `rule_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `maxnumber` int(11) DEFAULT NULL,
  `mkdate` int(10) unsigned DEFAULT NULL,
  `chdate` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`rule_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_tfa`
--

DROP TABLE IF EXISTS `users_tfa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users_tfa` (
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `secret` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `confirmed` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `type` enum('email','app') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'email',
  `mkdate` int(10) unsigned NOT NULL,
  `chdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_tfa_tokens`
--

DROP TABLE IF EXISTS `users_tfa_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users_tfa_tokens` (
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `token` char(6) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `widget_default`
--

DROP TABLE IF EXISTS `widget_default`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `widget_default` (
  `pluginid` int(11) NOT NULL,
  `col` tinyint(1) NOT NULL DEFAULT 0,
  `position` tinyint(1) NOT NULL DEFAULT 0,
  `perm` enum('user','autor','tutor','dozent','admin','root') CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'autor',
  PRIMARY KEY (`perm`,`pluginid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `widget_user`
--

DROP TABLE IF EXISTS `widget_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `widget_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pluginid` int(11) NOT NULL,
  `position` int(11) NOT NULL DEFAULT 0,
  `range_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `col` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `range_id` (`range_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wiki_links`
--

DROP TABLE IF EXISTS `wiki_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wiki_links` (
  `range_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `from_page_id` int(10) unsigned NOT NULL,
  `to_page_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`range_id`,`to_page_id`,`from_page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wiki_online_editing_users`
--

DROP TABLE IF EXISTS `wiki_online_editing_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wiki_online_editing_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `page_id` int(11) NOT NULL,
  `editing` tinyint(1) NOT NULL DEFAULT 0,
  `editing_request` tinyint(1) NOT NULL DEFAULT 0,
  `chdate` int(11) NOT NULL,
  `mkdate` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_2` (`user_id`,`page_id`),
  KEY `user_id` (`user_id`),
  KEY `page_id` (`page_id`),
  KEY `chdate` (`chdate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wiki_pages`
--

DROP TABLE IF EXISTS `wiki_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wiki_pages` (
  `page_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `range_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `name` varchar(255) NOT NULL,
  `content` mediumtext DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `read_permission` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'all',
  `write_permission` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'all',
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `locked_since` bigint(20) DEFAULT NULL,
  `locked_by_user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `chdate` bigint(20) NOT NULL,
  `mkdate` bigint(20) NOT NULL,
  PRIMARY KEY (`page_id`),
  KEY `read_permission` (`read_permission`),
  KEY `write_permission` (`write_permission`),
  KEY `range_id` (`range_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wiki_versions`
--

DROP TABLE IF EXISTS `wiki_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wiki_versions` (
  `version_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `page_id` int(10) unsigned NOT NULL,
  `name` varchar(128) NOT NULL,
  `content` text DEFAULT NULL,
  `user_id` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `mkdate` bigint(20) NOT NULL,
  PRIMARY KEY (`version_id`),
  KEY `page_id` (`page_id`),
  KEY `mkdate` (`mkdate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2025-08-06  9:18:31
/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.8.2-MariaDB, for osx10.20 (arm64)
--
-- Host: 127.0.0.1    Database: studip_6_0
-- ------------------------------------------------------
-- Server version	11.8.2-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Dumping data for table `Institute`
--

LOCK TABLES `Institute` WRITE;
/*!40000 ALTER TABLE `Institute` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `Institute` VALUES
('110ce78ffefaf1e5f167cd7019b728bf','externe Einrichtung B','ec2e364b28357106c0f8c282733dbe56','','','','','','',1,1156516698,1156516698,'Studip',0,''),
('1535795b0d6ddecac6813f5f6ac47ef2','Test Fakultät','1535795b0d6ddecac6813f5f6ac47ef2','Geismar Landstr. 17b','37083 Göttingen','http://www.studip.de','0551 / 381 985 0','testfakultaet@studip.de','0551 / 381 985 3',1,1156516698,1156516698,'Studip',0,''),
('2560f7c7674942a7dce8eeb238e15d93','Test Einrichtung','1535795b0d6ddecac6813f5f6ac47ef2','','','','','','',1,1156516698,1156516698,'Studip',0,''),
('536249daa596905f433e1f73578019db','Test Lehrstuhl','1535795b0d6ddecac6813f5f6ac47ef2','','','','','','',3,1156516698,1156516698,'Studip',0,''),
('7a4f19a0a2c321ab2b8f7b798881af7c','externe Einrichtung A','ec2e364b28357106c0f8c282733dbe56','','','','','','',1,1156516698,1156516698,'Studip',0,''),
('ec2e364b28357106c0f8c282733dbe56','externe Bildungseinrichtungen','ec2e364b28357106c0f8c282733dbe56','','','','','','',1,1156516698,1156516698,'Studip',0,''),
('f02e2b17bc0e99fc885da6ac4c2532dc','Test Abteilung','1535795b0d6ddecac6813f5f6ac47ef2','','','','','','',4,1156516698,1156516698,'Studip',0,'');
/*!40000 ALTER TABLE `Institute` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `abschluss`
--

LOCK TABLES `abschluss` WRITE;
/*!40000 ALTER TABLE `abschluss` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `abschluss` VALUES
('228234544820cdf75db55b42d1ea3ecc','Bachelor',NULL,'','','',1311416359,1311416359),
('c7f569e815a35cf24a515a0e67928072','Master',NULL,'','','',1311416385,1311416385);
/*!40000 ALTER TABLE `abschluss` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `activities`
--

LOCK TABLES `activities` WRITE;
/*!40000 ALTER TABLE `activities` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `activities` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `admission_condition`
--

LOCK TABLES `admission_condition` WRITE;
/*!40000 ALTER TABLE `admission_condition` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `admission_condition` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `admission_conditiongroup`
--

LOCK TABLES `admission_conditiongroup` WRITE;
/*!40000 ALTER TABLE `admission_conditiongroup` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `admission_conditiongroup` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `admission_seminar_user`
--

LOCK TABLES `admission_seminar_user` WRITE;
/*!40000 ALTER TABLE `admission_seminar_user` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `admission_seminar_user` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `admissionfactor`
--

LOCK TABLES `admissionfactor` WRITE;
/*!40000 ALTER TABLE `admissionfactor` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `admissionfactor` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `admissionrule_compat`
--

LOCK TABLES `admissionrule_compat` WRITE;
/*!40000 ALTER TABLE `admissionrule_compat` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `admissionrule_compat` VALUES
('ConditionalAdmission','ConditionalAdmission',1483462780,1483462780),
('ConditionalAdmission','CourseMemberAdmission',1483462780,1483462780),
('ConditionalAdmission','LimitedAdmission',1483462780,1483462780),
('ConditionalAdmission','ParticipantRestrictedAdmission',1483462780,1483462780),
('ConditionalAdmission','PasswordAdmission',1483462780,1483462780),
('ConditionalAdmission','PreferentialAdmission',1483462780,1483462780),
('ConditionalAdmission','TermsAdmission',1640797278,1640797278),
('ConditionalAdmission','TimedAdmission',1483462780,1483462780),
('CourseMemberAdmission','ConditionalAdmission',1483462780,1483462780),
('CourseMemberAdmission','CourseMemberAdmission',1483462780,1483462780),
('CourseMemberAdmission','LimitedAdmission',1483462780,1483462780),
('CourseMemberAdmission','ParticipantRestrictedAdmission',1483462780,1483462780),
('CourseMemberAdmission','PasswordAdmission',1483462780,1483462780),
('CourseMemberAdmission','PreferentialAdmission',1483462780,1483462780),
('CourseMemberAdmission','TermsAdmission',1640797278,1640797278),
('CourseMemberAdmission','TimedAdmission',1483462780,1483462780),
('LimitedAdmission','ConditionalAdmission',1483462780,1483462780),
('LimitedAdmission','CourseMemberAdmission',1483462780,1483462780),
('LimitedAdmission','ParticipantRestrictedAdmission',1483462780,1483462780),
('LimitedAdmission','PasswordAdmission',1483462780,1483462780),
('LimitedAdmission','PreferentialAdmission',1483462780,1483462780),
('LimitedAdmission','TermsAdmission',1640797278,1640797278),
('LimitedAdmission','TimedAdmission',1483462780,1483462780),
('ParticipantRestrictedAdmission','ConditionalAdmission',1483462780,1483462780),
('ParticipantRestrictedAdmission','CourseMemberAdmission',1483462780,1483462780),
('ParticipantRestrictedAdmission','LimitedAdmission',1483462780,1483462780),
('ParticipantRestrictedAdmission','PreferentialAdmission',1483462780,1483462780),
('ParticipantRestrictedAdmission','TermsAdmission',1640797278,1640797278),
('ParticipantRestrictedAdmission','TimedAdmission',1483462780,1483462780),
('PasswordAdmission','ConditionalAdmission',1483462780,1483462780),
('PasswordAdmission','CourseMemberAdmission',1483462780,1483462780),
('PasswordAdmission','PreferentialAdmission',1483462780,1483462780),
('PasswordAdmission','TimedAdmission',1483462780,1483462780),
('PreferentialAdmission','ConditionalAdmission',1483462780,1483462780),
('PreferentialAdmission','CourseMemberAdmission',1483462780,1483462780),
('PreferentialAdmission','LimitedAdmission',1483462780,1483462780),
('PreferentialAdmission','ParticipantRestrictedAdmission',1483462780,1483462780),
('PreferentialAdmission','PasswordAdmission',1483462780,1483462780),
('PreferentialAdmission','TermsAdmission',1640797278,1640797278),
('PreferentialAdmission','TimedAdmission',1483462780,1483462780),
('TermsAdmission','ConditionalAdmission',1640797278,1640797278),
('TermsAdmission','CourseMemberAdmission',1640797278,1640797278),
('TermsAdmission','LimitedAdmission',1640797278,1640797278),
('TermsAdmission','ParticipantRestrictedAdmission',1640797278,1640797278),
('TermsAdmission','PreferentialAdmission',1640797278,1640797278),
('TermsAdmission','TimedAdmission',1640797278,1640797278),
('TimedAdmission','ConditionalAdmission',1483462780,1483462780),
('TimedAdmission','CourseMemberAdmission',1483462780,1483462780),
('TimedAdmission','LimitedAdmission',1483462780,1483462780),
('TimedAdmission','ParticipantRestrictedAdmission',1483462780,1483462780),
('TimedAdmission','PasswordAdmission',1483462780,1483462780),
('TimedAdmission','PreferentialAdmission',1483462780,1483462780),
('TimedAdmission','TermsAdmission',1640797278,1640797278);
/*!40000 ALTER TABLE `admissionrule_compat` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `admissionrules`
--

LOCK TABLES `admissionrules` WRITE;
/*!40000 ALTER TABLE `admissionrules` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `admissionrules` VALUES
(1,'ConditionalAdmission',1,1388682201,'lib/admissionrules/conditionaladmission'),
(2,'LimitedAdmission',1,1388682201,'lib/admissionrules/limitedadmission'),
(3,'LockedAdmission',1,1388682201,'lib/admissionrules/lockedadmission'),
(4,'PasswordAdmission',1,1388682201,'lib/admissionrules/passwordadmission'),
(5,'TimedAdmission',1,1388682201,'lib/admissionrules/timedadmission'),
(6,'ParticipantRestrictedAdmission',1,1388682201,'lib/admissionrules/participantrestrictedadmission'),
(7,'CourseMemberAdmission',1,1414584420,'lib/admissionrules/coursememberadmission'),
(8,'PreferentialAdmission',1,1465458738,'lib/admissionrules/preferentialadmission'),
(9,'TermsAdmission',1,1640797278,'lib/admissionrules/termsadmission'),
(10,'ConnectedcourseAdmission',1,1754464709,'lib/admissionrules/connectedcourseadmission');
/*!40000 ALTER TABLE `admissionrules` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `archiv`
--

LOCK TABLES `archiv` WRITE;
/*!40000 ALTER TABLE `archiv` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `archiv` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `archiv_user`
--

LOCK TABLES `archiv_user` WRITE;
/*!40000 ALTER TABLE `archiv_user` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `archiv_user` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `auth_extern`
--

LOCK TABLES `auth_extern` WRITE;
/*!40000 ALTER TABLE `auth_extern` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `auth_extern` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `auth_user_md5`
--

LOCK TABLES `auth_user_md5` WRITE;
/*!40000 ALTER TABLE `auth_user_md5` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `auth_user_md5` VALUES
('205f3efb7997a0fc9755da2b535038da','test_dozent','$2a$08$ajIvgEjd17MiiDcFr6msc.xldknH/tTGajUXVhDxDKNJVX0H0iv0i','dozent','Testaccount','Dozent','dozent@studip.de','','standard',0,NULL,NULL,'unknown',NULL),
('2afaa0dce05f0b12a7318075e52879e2','N.N.','','dozent','N.','N.','','','standard',0,NULL,NULL,'never',NULL),
('6235c46eb9e962866ebdceece739ace5','test_admin','$2a$08$svvSma20vIxIR4J5gc0jIu31gws1WibmiQ/HDhCTukFA5GqhscY1G','admin','Testaccount','Admin','admin@studip.de','','standard',0,NULL,NULL,'unknown',NULL),
('76ed43ef286fb55cf9e41beadb484a9f','root@studip','$2a$08$SRoCYxAhWPFVF8V8CO15TOyzr.PpLRfVD9lVWVrmmBw4brkRTE/2G','root','Root','Studip','root@localhost','','standard',0,NULL,NULL,'yes',NULL),
('7e81ec247c151c02ffd479511e24cc03','test_tutor','$2a$08$mGhBl85TPsiItumZ4xjbgOnQ1vqIhLAC9giCfWcFzpkE1jqe4lmby','tutor','Testaccount','Tutor','tutor@studip.de','','standard',0,NULL,NULL,'unknown',NULL),
('e7a0a84b161f3e8c09b4a0a2e8a58147','test_autor','$2a$08$xvbrvPhkcsvkzPZsNh.kceLw2IIwiNJ.1jGOwY3.H/dR2f8PG5X3O','autor','Testaccount','Autor','autor@studip.de','','standard',0,NULL,NULL,'unknown','1234567');
/*!40000 ALTER TABLE `auth_user_md5` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `auto_insert_sem`
--

LOCK TABLES `auto_insert_sem` WRITE;
/*!40000 ALTER TABLE `auto_insert_sem` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `auto_insert_sem` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `auto_insert_user`
--

LOCK TABLES `auto_insert_user` WRITE;
/*!40000 ALTER TABLE `auto_insert_user` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `auto_insert_user` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `aux_lock_rules`
--

LOCK TABLES `aux_lock_rules` WRITE;
/*!40000 ALTER TABLE `aux_lock_rules` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `aux_lock_rules` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `banner_ads`
--

LOCK TABLES `banner_ads` WRITE;
/*!40000 ALTER TABLE `banner_ads` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `banner_ads` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `banner_roles`
--

LOCK TABLES `banner_roles` WRITE;
/*!40000 ALTER TABLE `banner_roles` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `banner_roles` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `blubber_comments`
--

LOCK TABLES `blubber_comments` WRITE;
/*!40000 ALTER TABLE `blubber_comments` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `blubber_comments` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `blubber_events_queue`
--

LOCK TABLES `blubber_events_queue` WRITE;
/*!40000 ALTER TABLE `blubber_events_queue` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `blubber_events_queue` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `blubber_mentions`
--

LOCK TABLES `blubber_mentions` WRITE;
/*!40000 ALTER TABLE `blubber_mentions` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `blubber_mentions` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `blubber_tags`
--

LOCK TABLES `blubber_tags` WRITE;
/*!40000 ALTER TABLE `blubber_tags` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `blubber_tags` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `blubber_threads`
--

LOCK TABLES `blubber_threads` WRITE;
/*!40000 ALTER TABLE `blubber_threads` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `blubber_threads` VALUES
('global','public','','',0,NULL,'BlubberGlobalThread',1,1,NULL,1591717440,1591717440);
/*!40000 ALTER TABLE `blubber_threads` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `blubber_threads_followstates`
--

LOCK TABLES `blubber_threads_followstates` WRITE;
/*!40000 ALTER TABLE `blubber_threads_followstates` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `blubber_threads_followstates` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `cache` VALUES
('DB_SEMESTER_DATA','s:766:\"a:2:{i:0;a:12:{s:11:\"semester_id\";s:32:\"322f640f3f4643ebe514df65f1163eb1\";s:4:\"name\";s:9:\"SoSe 2025\";s:14:\"semester_token\";s:0:\"\";s:6:\"beginn\";s:10:\"1743458400\";s:4:\"ende\";s:10:\"1759269599\";s:11:\"sem_wechsel\";N;s:13:\"vorles_beginn\";s:10:\"1744581600\";s:11:\"vorles_ende\";s:10:\"1752962399\";s:7:\"visible\";s:1:\"1\";s:11:\"external_id\";s:0:\"\";s:6:\"mkdate\";N;s:6:\"chdate\";s:10:\"1754464710\";}i:1;a:12:{s:11:\"semester_id\";s:32:\"4967f0a483e36554b77e3dc47aa58941\";s:4:\"name\";s:14:\"WiSe 2025/2026\";s:14:\"semester_token\";s:0:\"\";s:6:\"beginn\";s:10:\"1759269600\";s:4:\"ende\";s:10:\"1774994399\";s:11:\"sem_wechsel\";N;s:13:\"vorles_beginn\";s:10:\"1760306400\";s:11:\"vorles_ende\";s:10:\"1771109999\";s:7:\"visible\";s:1:\"1\";s:11:\"external_id\";s:0:\"\";s:6:\"mkdate\";N;s:6:\"chdate\";s:10:\"1754464710\";}}\";',1754507911),
('DB_SEM_TYPES_ARRAY','s:1945:\"a:14:{i:0;a:5:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:9:\"Vorlesung\";s:5:\"class\";s:1:\"1\";s:6:\"mkdate\";s:10:\"1366882120\";s:6:\"chdate\";s:10:\"1366882120\";}i:1;a:5:{s:2:\"id\";s:1:\"2\";s:4:\"name\";s:7:\"Seminar\";s:5:\"class\";s:1:\"1\";s:6:\"mkdate\";s:10:\"1366882120\";s:6:\"chdate\";s:10:\"1366882120\";}i:2;a:5:{s:2:\"id\";s:1:\"3\";s:4:\"name\";s:6:\"Übung\";s:5:\"class\";s:1:\"1\";s:6:\"mkdate\";s:10:\"1366882120\";s:6:\"chdate\";s:10:\"1366882120\";}i:3;a:5:{s:2:\"id\";s:1:\"4\";s:4:\"name\";s:9:\"Praktikum\";s:5:\"class\";s:1:\"1\";s:6:\"mkdate\";s:10:\"1366882120\";s:6:\"chdate\";s:10:\"1366882120\";}i:4;a:5:{s:2:\"id\";s:1:\"5\";s:4:\"name\";s:10:\"Colloquium\";s:5:\"class\";s:1:\"1\";s:6:\"mkdate\";s:10:\"1366882120\";s:6:\"chdate\";s:10:\"1366882120\";}i:5;a:5:{s:2:\"id\";s:1:\"6\";s:4:\"name\";s:16:\"Forschungsgruppe\";s:5:\"class\";s:1:\"1\";s:6:\"mkdate\";s:10:\"1366882120\";s:6:\"chdate\";s:10:\"1366882120\";}i:6;a:5:{s:2:\"id\";s:1:\"7\";s:4:\"name\";s:8:\"sonstige\";s:5:\"class\";s:1:\"1\";s:6:\"mkdate\";s:10:\"1366882120\";s:6:\"chdate\";s:10:\"1366882120\";}i:7;a:5:{s:2:\"id\";s:1:\"8\";s:4:\"name\";s:7:\"Gremium\";s:5:\"class\";s:1:\"2\";s:6:\"mkdate\";s:10:\"1366882120\";s:6:\"chdate\";s:10:\"1366882120\";}i:8;a:5:{s:2:\"id\";s:1:\"9\";s:4:\"name\";s:13:\"Projektgruppe\";s:5:\"class\";s:1:\"2\";s:6:\"mkdate\";s:10:\"1366882120\";s:6:\"chdate\";s:10:\"1366882120\";}i:9;a:5:{s:2:\"id\";s:2:\"10\";s:4:\"name\";s:8:\"sonstige\";s:5:\"class\";s:1:\"2\";s:6:\"mkdate\";s:10:\"1366882120\";s:6:\"chdate\";s:10:\"1366882120\";}i:10;a:5:{s:2:\"id\";s:2:\"11\";s:4:\"name\";s:11:\"Kulturforum\";s:5:\"class\";s:1:\"3\";s:6:\"mkdate\";s:10:\"1366882120\";s:6:\"chdate\";s:10:\"1366882120\";}i:11;a:5:{s:2:\"id\";s:2:\"12\";s:4:\"name\";s:19:\"Veranstaltungsboard\";s:5:\"class\";s:1:\"3\";s:6:\"mkdate\";s:10:\"1366882120\";s:6:\"chdate\";s:10:\"1366882120\";}i:12;a:5:{s:2:\"id\";s:2:\"13\";s:4:\"name\";s:8:\"sonstige\";s:5:\"class\";s:1:\"3\";s:6:\"mkdate\";s:10:\"1366882120\";s:6:\"chdate\";s:10:\"1366882120\";}i:13;a:5:{s:2:\"id\";s:2:\"99\";s:4:\"name\";s:13:\"Studiengruppe\";s:5:\"class\";s:2:\"99\";s:6:\"mkdate\";s:10:\"1366882120\";s:6:\"chdate\";s:10:\"1366882120\";}}\";',1754507906),
('DB_TABLE_SCHEMES','s:42220:\"a:26:{s:9:\"user_info\";a:2:{s:9:\"db_fields\";a:22:{s:7:\"user_id\";a:5:{s:4:\"name\";s:7:\"user_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:5:\"hobby\";a:5:{s:4:\"name\";s:5:\"hobby\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";N;s:4:\"type\";s:10:\"mediumtext\";s:5:\"extra\";s:0:\"\";}s:10:\"lebenslauf\";a:5:{s:4:\"name\";s:10:\"lebenslauf\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";N;s:4:\"type\";s:10:\"mediumtext\";s:5:\"extra\";s:0:\"\";}s:5:\"publi\";a:5:{s:4:\"name\";s:5:\"publi\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";N;s:4:\"type\";s:10:\"mediumtext\";s:5:\"extra\";s:0:\"\";}s:7:\"schwerp\";a:5:{s:4:\"name\";s:7:\"schwerp\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";N;s:4:\"type\";s:4:\"text\";s:5:\"extra\";s:0:\"\";}s:4:\"home\";a:5:{s:4:\"name\";s:4:\"Home\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:12:\"varchar(200)\";s:5:\"extra\";s:0:\"\";}s:8:\"privatnr\";a:5:{s:4:\"name\";s:8:\"privatnr\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:12:\"varchar(255)\";s:5:\"extra\";s:0:\"\";}s:10:\"privatcell\";a:5:{s:4:\"name\";s:10:\"privatcell\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:12:\"varchar(255)\";s:5:\"extra\";s:0:\"\";}s:7:\"privadr\";a:5:{s:4:\"name\";s:7:\"privadr\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:11:\"varchar(64)\";s:5:\"extra\";s:0:\"\";}s:5:\"score\";a:5:{s:4:\"name\";s:5:\"score\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:10:\"geschlecht\";a:5:{s:4:\"name\";s:10:\"geschlecht\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:10:\"tinyint(4)\";s:5:\"extra\";s:0:\"\";}s:6:\"mkdate\";a:5:{s:4:\"name\";s:6:\"mkdate\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:6:\"chdate\";a:5:{s:4:\"name\";s:6:\"chdate\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:11:\"title_front\";a:5:{s:4:\"name\";s:11:\"title_front\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:11:\"varchar(64)\";s:5:\"extra\";s:0:\"\";}s:10:\"title_rear\";a:5:{s:4:\"name\";s:10:\"title_rear\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:11:\"varchar(64)\";s:5:\"extra\";s:0:\"\";}s:18:\"preferred_language\";a:5:{s:4:\"name\";s:18:\"preferred_language\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:11:\"varchar(20)\";s:5:\"extra\";s:0:\"\";}s:15:\"smsforward_copy\";a:5:{s:4:\"name\";s:15:\"smsforward_copy\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"1\";s:4:\"type\";s:19:\"tinyint(3) unsigned\";s:5:\"extra\";s:0:\"\";}s:14:\"smsforward_rec\";a:5:{s:4:\"name\";s:14:\"smsforward_rec\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:13:\"email_forward\";a:5:{s:4:\"name\";s:13:\"email_forward\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:19:\"tinyint(3) unsigned\";s:5:\"extra\";s:0:\"\";}s:5:\"motto\";a:5:{s:4:\"name\";s:5:\"motto\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:12:\"varchar(255)\";s:5:\"extra\";s:0:\"\";}s:9:\"lock_rule\";a:5:{s:4:\"name\";s:9:\"lock_rule\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:11:\"varchar(32)\";s:5:\"extra\";s:0:\"\";}s:21:\"oercampus_description\";a:5:{s:4:\"name\";s:21:\"oercampus_description\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:4:\"text\";s:5:\"extra\";s:0:\"\";}}s:2:\"pk\";a:1:{i:0;s:7:\"user_id\";}}s:13:\"auth_user_md5\";a:2:{s:9:\"db_fields\";a:14:{s:7:\"user_id\";a:5:{s:4:\"name\";s:7:\"user_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:8:\"username\";a:5:{s:4:\"name\";s:8:\"username\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:11:\"varchar(64)\";s:5:\"extra\";s:0:\"\";}s:8:\"password\";a:5:{s:4:\"name\";s:8:\"password\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:13:\"varbinary(64)\";s:5:\"extra\";s:0:\"\";}s:5:\"perms\";a:5:{s:4:\"name\";s:5:\"perms\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:4:\"user\";s:4:\"type\";s:52:\"enum(\'user\',\'autor\',\'tutor\',\'dozent\',\'admin\',\'root\')\";s:5:\"extra\";s:0:\"\";}s:7:\"vorname\";a:5:{s:4:\"name\";s:7:\"Vorname\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:11:\"varchar(64)\";s:5:\"extra\";s:0:\"\";}s:8:\"nachname\";a:5:{s:4:\"name\";s:8:\"Nachname\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:11:\"varchar(64)\";s:5:\"extra\";s:0:\"\";}s:5:\"email\";a:5:{s:4:\"name\";s:5:\"Email\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:12:\"varchar(256)\";s:5:\"extra\";s:0:\"\";}s:14:\"validation_key\";a:5:{s:4:\"name\";s:14:\"validation_key\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:11:\"varchar(10)\";s:5:\"extra\";s:0:\"\";}s:11:\"auth_plugin\";a:5:{s:4:\"name\";s:11:\"auth_plugin\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";s:8:\"standard\";s:4:\"type\";s:11:\"varchar(64)\";s:5:\"extra\";s:0:\"\";}s:6:\"locked\";a:5:{s:4:\"name\";s:6:\"locked\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:19:\"tinyint(3) unsigned\";s:5:\"extra\";s:0:\"\";}s:12:\"lock_comment\";a:5:{s:4:\"name\";s:12:\"lock_comment\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:12:\"varchar(255)\";s:5:\"extra\";s:0:\"\";}s:9:\"locked_by\";a:5:{s:4:\"name\";s:9:\"locked_by\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:11:\"varchar(32)\";s:5:\"extra\";s:0:\"\";}s:7:\"visible\";a:5:{s:4:\"name\";s:7:\"visible\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:7:\"unknown\";s:4:\"type\";s:52:\"enum(\'global\',\'always\',\'yes\',\'unknown\',\'no\',\'never\')\";s:5:\"extra\";s:0:\"\";}s:20:\"matriculation_number\";a:5:{s:4:\"name\";s:20:\"matriculation_number\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:12:\"varchar(255)\";s:5:\"extra\";s:0:\"\";}}s:2:\"pk\";a:1:{i:0;s:7:\"user_id\";}}s:11:\"log_actions\";a:2:{s:9:\"db_fields\";a:11:{s:9:\"action_id\";a:5:{s:4:\"name\";s:9:\"action_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:4:\"name\";a:5:{s:4:\"name\";s:4:\"name\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:12:\"varchar(128)\";s:5:\"extra\";s:0:\"\";}s:11:\"description\";a:5:{s:4:\"name\";s:11:\"description\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:11:\"varchar(64)\";s:5:\"extra\";s:0:\"\";}s:13:\"info_template\";a:5:{s:4:\"name\";s:13:\"info_template\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:4:\"text\";s:5:\"extra\";s:0:\"\";}s:6:\"active\";a:5:{s:4:\"name\";s:6:\"active\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"1\";s:4:\"type\";s:19:\"tinyint(3) unsigned\";s:5:\"extra\";s:0:\"\";}s:7:\"expires\";a:5:{s:4:\"name\";s:7:\"expires\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:8:\"filename\";a:5:{s:4:\"name\";s:8:\"filename\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:12:\"varchar(255)\";s:5:\"extra\";s:0:\"\";}s:5:\"class\";a:5:{s:4:\"name\";s:5:\"class\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:12:\"varchar(255)\";s:5:\"extra\";s:0:\"\";}s:4:\"type\";a:5:{s:4:\"name\";s:4:\"type\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:28:\"enum(\'core\',\'plugin\',\'file\')\";s:5:\"extra\";s:0:\"\";}s:6:\"mkdate\";a:5:{s:4:\"name\";s:6:\"mkdate\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:6:\"chdate\";a:5:{s:4:\"name\";s:6:\"chdate\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}}s:2:\"pk\";a:1:{i:0;s:9:\"action_id\";}}s:10:\"log_events\";a:2:{s:9:\"db_fields\";a:8:{s:8:\"event_id\";a:5:{s:4:\"name\";s:8:\"event_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";N;s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:14:\"auto_increment\";}s:7:\"user_id\";a:5:{s:4:\"name\";s:7:\"user_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:9:\"action_id\";a:5:{s:4:\"name\";s:9:\"action_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:17:\"affected_range_id\";a:5:{s:4:\"name\";s:17:\"affected_range_id\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:19:\"coaffected_range_id\";a:5:{s:4:\"name\";s:19:\"coaffected_range_id\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:4:\"info\";a:5:{s:4:\"name\";s:4:\"info\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:4:\"text\";s:5:\"extra\";s:0:\"\";}s:8:\"dbg_info\";a:5:{s:4:\"name\";s:8:\"dbg_info\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:4:\"text\";s:5:\"extra\";s:0:\"\";}s:6:\"mkdate\";a:5:{s:4:\"name\";s:6:\"mkdate\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";N;s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}}s:2:\"pk\";a:1:{i:0;s:8:\"event_id\";}}s:13:\"semester_data\";a:2:{s:9:\"db_fields\";a:12:{s:11:\"semester_id\";a:5:{s:4:\"name\";s:11:\"semester_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:4:\"name\";a:5:{s:4:\"name\";s:4:\"name\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:12:\"varchar(255)\";s:5:\"extra\";s:0:\"\";}s:14:\"semester_token\";a:5:{s:4:\"name\";s:14:\"semester_token\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:11:\"varchar(10)\";s:5:\"extra\";s:0:\"\";}s:6:\"beginn\";a:5:{s:4:\"name\";s:6:\"beginn\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:4:\"ende\";a:5:{s:4:\"name\";s:4:\"ende\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:11:\"sem_wechsel\";a:5:{s:4:\"name\";s:11:\"sem_wechsel\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:13:\"vorles_beginn\";a:5:{s:4:\"name\";s:13:\"vorles_beginn\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:11:\"vorles_ende\";a:5:{s:4:\"name\";s:11:\"vorles_ende\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:7:\"visible\";a:5:{s:4:\"name\";s:7:\"visible\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"1\";s:4:\"type\";s:19:\"tinyint(3) unsigned\";s:5:\"extra\";s:0:\"\";}s:11:\"external_id\";a:5:{s:4:\"name\";s:11:\"external_id\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:11:\"varchar(50)\";s:5:\"extra\";s:0:\"\";}s:6:\"mkdate\";a:5:{s:4:\"name\";s:6:\"mkdate\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:6:\"chdate\";a:5:{s:4:\"name\";s:6:\"chdate\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}}s:2:\"pk\";a:1:{i:0;s:11:\"semester_id\";}}s:16:\"semester_holiday\";a:2:{s:9:\"db_fields\";a:8:{s:10:\"holiday_id\";a:5:{s:4:\"name\";s:10:\"holiday_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:11:\"semester_id\";a:5:{s:4:\"name\";s:11:\"semester_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:4:\"name\";a:5:{s:4:\"name\";s:4:\"name\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:12:\"varchar(255)\";s:5:\"extra\";s:0:\"\";}s:11:\"description\";a:5:{s:4:\"name\";s:11:\"description\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";N;s:4:\"type\";s:4:\"text\";s:5:\"extra\";s:0:\"\";}s:6:\"beginn\";a:5:{s:4:\"name\";s:6:\"beginn\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:4:\"ende\";a:5:{s:4:\"name\";s:4:\"ende\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:6:\"mkdate\";a:5:{s:4:\"name\";s:6:\"mkdate\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:6:\"chdate\";a:5:{s:4:\"name\";s:6:\"chdate\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}}s:2:\"pk\";a:1:{i:0;s:10:\"holiday_id\";}}s:9:\"resources\";a:2:{s:9:\"db_fields\";a:12:{s:2:\"id\";a:5:{s:4:\"name\";s:2:\"id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:9:\"parent_id\";a:5:{s:4:\"name\";s:9:\"parent_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:11:\"category_id\";a:5:{s:4:\"name\";s:11:\"category_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:5:\"level\";a:5:{s:4:\"name\";s:5:\"level\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:7:\"int(11)\";s:5:\"extra\";s:0:\"\";}s:4:\"name\";a:5:{s:4:\"name\";s:4:\"name\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:12:\"varchar(255)\";s:5:\"extra\";s:0:\"\";}s:11:\"description\";a:5:{s:4:\"name\";s:11:\"description\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:4:\"text\";s:5:\"extra\";s:0:\"\";}s:11:\"requestable\";a:5:{s:4:\"name\";s:11:\"requestable\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:19:\"tinyint(3) unsigned\";s:5:\"extra\";s:0:\"\";}s:8:\"lockable\";a:5:{s:4:\"name\";s:8:\"lockable\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"1\";s:4:\"type\";s:19:\"tinyint(3) unsigned\";s:5:\"extra\";s:0:\"\";}s:20:\"booking_plan_request\";a:5:{s:4:\"name\";s:20:\"booking_plan_request\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"1\";s:4:\"type\";s:19:\"tinyint(1) unsigned\";s:5:\"extra\";s:0:\"\";}s:6:\"mkdate\";a:5:{s:4:\"name\";s:6:\"mkdate\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:6:\"chdate\";a:5:{s:4:\"name\";s:6:\"chdate\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:13:\"sort_position\";a:5:{s:4:\"name\";s:13:\"sort_position\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:19:\"tinyint(3) unsigned\";s:5:\"extra\";s:0:\"\";}}s:2:\"pk\";a:1:{i:0;s:2:\"id\";}}s:8:\"seminare\";a:2:{s:9:\"db_fields\";a:34:{s:10:\"seminar_id\";a:5:{s:4:\"name\";s:10:\"Seminar_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:20:\"veranstaltungsnummer\";a:5:{s:4:\"name\";s:20:\"VeranstaltungsNummer\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:12:\"varchar(100)\";s:5:\"extra\";s:0:\"\";}s:11:\"institut_id\";a:5:{s:4:\"name\";s:11:\"Institut_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:4:\"name\";a:5:{s:4:\"name\";s:4:\"Name\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:12:\"varchar(255)\";s:5:\"extra\";s:0:\"\";}s:10:\"untertitel\";a:5:{s:4:\"name\";s:10:\"Untertitel\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:12:\"varchar(255)\";s:5:\"extra\";s:0:\"\";}s:6:\"status\";a:5:{s:4:\"name\";s:6:\"status\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"1\";s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:12:\"beschreibung\";a:5:{s:4:\"name\";s:12:\"Beschreibung\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";N;s:4:\"type\";s:4:\"text\";s:5:\"extra\";s:0:\"\";}s:3:\"ort\";a:5:{s:4:\"name\";s:3:\"Ort\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:12:\"varchar(255)\";s:5:\"extra\";s:0:\"\";}s:9:\"sonstiges\";a:5:{s:4:\"name\";s:9:\"Sonstiges\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:4:\"text\";s:5:\"extra\";s:0:\"\";}s:11:\"lesezugriff\";a:5:{s:4:\"name\";s:11:\"Lesezugriff\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:10:\"tinyint(4)\";s:5:\"extra\";s:0:\"\";}s:14:\"schreibzugriff\";a:5:{s:4:\"name\";s:14:\"Schreibzugriff\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:10:\"tinyint(4)\";s:5:\"extra\";s:0:\"\";}s:3:\"art\";a:5:{s:4:\"name\";s:3:\"art\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:12:\"varchar(255)\";s:5:\"extra\";s:0:\"\";}s:10:\"teilnehmer\";a:5:{s:4:\"name\";s:10:\"teilnehmer\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:4:\"text\";s:5:\"extra\";s:0:\"\";}s:15:\"vorrausetzungen\";a:5:{s:4:\"name\";s:15:\"vorrausetzungen\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:4:\"text\";s:5:\"extra\";s:0:\"\";}s:8:\"lernorga\";a:5:{s:4:\"name\";s:8:\"lernorga\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:4:\"text\";s:5:\"extra\";s:0:\"\";}s:17:\"leistungsnachweis\";a:5:{s:4:\"name\";s:17:\"leistungsnachweis\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:4:\"text\";s:5:\"extra\";s:0:\"\";}s:6:\"mkdate\";a:5:{s:4:\"name\";s:6:\"mkdate\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:6:\"chdate\";a:5:{s:4:\"name\";s:6:\"chdate\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:4:\"ects\";a:5:{s:4:\"name\";s:4:\"ects\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:11:\"varchar(32)\";s:5:\"extra\";s:0:\"\";}s:17:\"admission_turnout\";a:5:{s:4:\"name\";s:17:\"admission_turnout\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:7:\"int(11)\";s:5:\"extra\";s:0:\"\";}s:17:\"admission_binding\";a:5:{s:4:\"name\";s:17:\"admission_binding\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:10:\"tinyint(4)\";s:5:\"extra\";s:0:\"\";}s:16:\"admission_prelim\";a:5:{s:4:\"name\";s:16:\"admission_prelim\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:19:\"tinyint(3) unsigned\";s:5:\"extra\";s:0:\"\";}s:20:\"admission_prelim_txt\";a:5:{s:4:\"name\";s:20:\"admission_prelim_txt\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:4:\"text\";s:5:\"extra\";s:0:\"\";}s:26:\"admission_disable_waitlist\";a:5:{s:4:\"name\";s:26:\"admission_disable_waitlist\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:19:\"tinyint(3) unsigned\";s:5:\"extra\";s:0:\"\";}s:7:\"visible\";a:5:{s:4:\"name\";s:7:\"visible\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"1\";s:4:\"type\";s:19:\"tinyint(3) unsigned\";s:5:\"extra\";s:0:\"\";}s:9:\"showscore\";a:5:{s:4:\"name\";s:9:\"showscore\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:10:\"tinyint(4)\";s:5:\"extra\";s:0:\"\";}s:13:\"aux_lock_rule\";a:5:{s:4:\"name\";s:13:\"aux_lock_rule\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:11:\"varchar(32)\";s:5:\"extra\";s:0:\"\";}s:20:\"aux_lock_rule_forced\";a:5:{s:4:\"name\";s:20:\"aux_lock_rule_forced\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:10:\"tinyint(4)\";s:5:\"extra\";s:0:\"\";}s:9:\"lock_rule\";a:5:{s:4:\"name\";s:9:\"lock_rule\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:11:\"varchar(32)\";s:5:\"extra\";s:0:\"\";}s:22:\"admission_waitlist_max\";a:5:{s:4:\"name\";s:22:\"admission_waitlist_max\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:31:\"admission_disable_waitlist_move\";a:5:{s:4:\"name\";s:31:\"admission_disable_waitlist_move\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:19:\"tinyint(3) unsigned\";s:5:\"extra\";s:0:\"\";}s:10:\"completion\";a:5:{s:4:\"name\";s:10:\"completion\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:19:\"tinyint(3) unsigned\";s:5:\"extra\";s:0:\"\";}s:13:\"parent_course\";a:5:{s:4:\"name\";s:13:\"parent_course\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:7:\"expires\";a:5:{s:4:\"name\";s:7:\"expires\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:16:\"int(11) unsigned\";s:5:\"extra\";s:0:\"\";}}s:2:\"pk\";a:1:{i:0;s:10:\"seminar_id\";}}s:19:\"seminar_cycle_dates\";a:2:{s:9:\"db_fields\";a:13:{s:11:\"metadate_id\";a:5:{s:4:\"name\";s:11:\"metadate_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";N;s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:10:\"seminar_id\";a:5:{s:4:\"name\";s:10:\"seminar_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";N;s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:10:\"start_time\";a:5:{s:4:\"name\";s:10:\"start_time\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";N;s:4:\"type\";s:4:\"time\";s:5:\"extra\";s:0:\"\";}s:8:\"end_time\";a:5:{s:4:\"name\";s:8:\"end_time\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";N;s:4:\"type\";s:4:\"time\";s:5:\"extra\";s:0:\"\";}s:7:\"weekday\";a:5:{s:4:\"name\";s:7:\"weekday\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";N;s:4:\"type\";s:19:\"tinyint(3) unsigned\";s:5:\"extra\";s:0:\"\";}s:11:\"description\";a:5:{s:4:\"name\";s:11:\"description\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:12:\"varchar(255)\";s:5:\"extra\";s:0:\"\";}s:3:\"sws\";a:5:{s:4:\"name\";s:3:\"sws\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:3:\"0.0\";s:4:\"type\";s:12:\"decimal(2,1)\";s:5:\"extra\";s:0:\"\";}s:5:\"cycle\";a:5:{s:4:\"name\";s:5:\"cycle\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:19:\"tinyint(3) unsigned\";s:5:\"extra\";s:0:\"\";}s:11:\"week_offset\";a:5:{s:4:\"name\";s:11:\"week_offset\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:7:\"int(11)\";s:5:\"extra\";s:0:\"\";}s:10:\"end_offset\";a:5:{s:4:\"name\";s:10:\"end_offset\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:7:\"int(11)\";s:5:\"extra\";s:0:\"\";}s:6:\"sorter\";a:5:{s:4:\"name\";s:6:\"sorter\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:19:\"tinyint(3) unsigned\";s:5:\"extra\";s:0:\"\";}s:6:\"mkdate\";a:5:{s:4:\"name\";s:6:\"mkdate\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";N;s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:6:\"chdate\";a:5:{s:4:\"name\";s:6:\"chdate\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";N;s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}}s:2:\"pk\";a:1:{i:0;s:11:\"metadate_id\";}}s:10:\"ex_termine\";a:2:{s:9:\"db_fields\";a:12:{s:9:\"termin_id\";a:5:{s:4:\"name\";s:9:\"termin_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:8:\"range_id\";a:5:{s:4:\"name\";s:8:\"range_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:8:\"autor_id\";a:5:{s:4:\"name\";s:8:\"autor_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:7:\"content\";a:5:{s:4:\"name\";s:7:\"content\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:12:\"varchar(255)\";s:5:\"extra\";s:0:\"\";}s:4:\"date\";a:5:{s:4:\"name\";s:4:\"date\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:8:\"end_time\";a:5:{s:4:\"name\";s:8:\"end_time\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:6:\"mkdate\";a:5:{s:4:\"name\";s:6:\"mkdate\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:6:\"chdate\";a:5:{s:4:\"name\";s:6:\"chdate\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:8:\"date_typ\";a:5:{s:4:\"name\";s:8:\"date_typ\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:10:\"tinyint(4)\";s:5:\"extra\";s:0:\"\";}s:4:\"raum\";a:5:{s:4:\"name\";s:4:\"raum\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:12:\"varchar(255)\";s:5:\"extra\";s:0:\"\";}s:11:\"metadate_id\";a:5:{s:4:\"name\";s:11:\"metadate_id\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:11:\"resource_id\";a:5:{s:4:\"name\";s:11:\"resource_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}}s:2:\"pk\";a:1:{i:0;s:9:\"termin_id\";}}s:7:\"termine\";a:2:{s:9:\"db_fields\";a:12:{s:9:\"termin_id\";a:5:{s:4:\"name\";s:9:\"termin_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:8:\"range_id\";a:5:{s:4:\"name\";s:8:\"range_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:8:\"autor_id\";a:5:{s:4:\"name\";s:8:\"autor_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:7:\"content\";a:5:{s:4:\"name\";s:7:\"content\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:12:\"varchar(255)\";s:5:\"extra\";s:0:\"\";}s:4:\"date\";a:5:{s:4:\"name\";s:4:\"date\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:8:\"end_time\";a:5:{s:4:\"name\";s:8:\"end_time\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:6:\"mkdate\";a:5:{s:4:\"name\";s:6:\"mkdate\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:6:\"chdate\";a:5:{s:4:\"name\";s:6:\"chdate\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:8:\"date_typ\";a:5:{s:4:\"name\";s:8:\"date_typ\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:10:\"tinyint(4)\";s:5:\"extra\";s:0:\"\";}s:4:\"raum\";a:5:{s:4:\"name\";s:4:\"raum\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:12:\"varchar(255)\";s:5:\"extra\";s:0:\"\";}s:11:\"metadate_id\";a:5:{s:4:\"name\";s:11:\"metadate_id\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:22:\"number_of_participants\";a:5:{s:4:\"name\";s:22:\"number_of_participants\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:11:\"smallint(6)\";s:5:\"extra\";s:0:\"\";}}s:2:\"pk\";a:1:{i:0;s:9:\"termin_id\";}}s:16:\"semester_courses\";a:2:{s:9:\"db_fields\";a:4:{s:11:\"semester_id\";a:5:{s:4:\"name\";s:11:\"semester_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:9:\"course_id\";a:5:{s:4:\"name\";s:9:\"course_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:6:\"mkdate\";a:5:{s:4:\"name\";s:6:\"mkdate\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:7:\"int(11)\";s:5:\"extra\";s:0:\"\";}s:6:\"chdate\";a:5:{s:4:\"name\";s:6:\"chdate\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:7:\"int(11)\";s:5:\"extra\";s:0:\"\";}}s:2:\"pk\";a:2:{i:0;s:11:\"semester_id\";i:1;s:9:\"course_id\";}}s:17:\"resource_bookings\";a:2:{s:9:\"db_fields\";a:15:{s:2:\"id\";a:5:{s:4:\"name\";s:2:\"id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:11:\"resource_id\";a:5:{s:4:\"name\";s:11:\"resource_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:8:\"range_id\";a:5:{s:4:\"name\";s:8:\"range_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:11:\"description\";a:5:{s:4:\"name\";s:11:\"description\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:4:\"text\";s:5:\"extra\";s:0:\"\";}s:5:\"begin\";a:5:{s:4:\"name\";s:5:\"begin\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:3:\"end\";a:5:{s:4:\"name\";s:3:\"end\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:10:\"repeat_end\";a:5:{s:4:\"name\";s:10:\"repeat_end\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:6:\"mkdate\";a:5:{s:4:\"name\";s:6:\"mkdate\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:6:\"chdate\";a:5:{s:4:\"name\";s:6:\"chdate\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:16:\"internal_comment\";a:5:{s:4:\"name\";s:16:\"internal_comment\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:4:\"text\";s:5:\"extra\";s:0:\"\";}s:16:\"preparation_time\";a:5:{s:4:\"name\";s:16:\"preparation_time\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:7:\"int(11)\";s:5:\"extra\";s:0:\"\";}s:12:\"booking_type\";a:5:{s:4:\"name\";s:12:\"booking_type\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:10:\"tinyint(4)\";s:5:\"extra\";s:0:\"\";}s:15:\"booking_user_id\";a:5:{s:4:\"name\";s:15:\"booking_user_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:19:\"repetition_interval\";a:5:{s:4:\"name\";s:19:\"repetition_interval\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:11:\"varchar(24)\";s:5:\"extra\";s:0:\"\";}s:8:\"weekdays\";a:5:{s:4:\"name\";s:8:\"weekdays\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:10:\"varchar(7)\";s:5:\"extra\";s:0:\"\";}}s:2:\"pk\";a:1:{i:0;s:2:\"id\";}}s:6:\"themen\";a:2:{s:9:\"db_fields\";a:9:{s:8:\"issue_id\";a:5:{s:4:\"name\";s:8:\"issue_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:10:\"seminar_id\";a:5:{s:4:\"name\";s:10:\"seminar_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:9:\"author_id\";a:5:{s:4:\"name\";s:9:\"author_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:5:\"title\";a:5:{s:4:\"name\";s:5:\"title\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:12:\"varchar(255)\";s:5:\"extra\";s:0:\"\";}s:11:\"description\";a:5:{s:4:\"name\";s:11:\"description\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";N;s:4:\"type\";s:4:\"text\";s:5:\"extra\";s:0:\"\";}s:8:\"priority\";a:5:{s:4:\"name\";s:8:\"priority\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:20:\"smallint(5) unsigned\";s:5:\"extra\";s:0:\"\";}s:13:\"paper_related\";a:5:{s:4:\"name\";s:13:\"paper_related\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:19:\"tinyint(3) unsigned\";s:5:\"extra\";s:0:\"\";}s:6:\"mkdate\";a:5:{s:4:\"name\";s:6:\"mkdate\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:6:\"chdate\";a:5:{s:4:\"name\";s:6:\"chdate\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}}s:2:\"pk\";a:1:{i:0;s:8:\"issue_id\";}}s:14:\"themen_termine\";a:2:{s:9:\"db_fields\";a:2:{s:8:\"issue_id\";a:5:{s:4:\"name\";s:8:\"issue_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:9:\"termin_id\";a:5:{s:4:\"name\";s:9:\"termin_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}}s:2:\"pk\";a:2:{i:0;s:8:\"issue_id\";i:1;s:9:\"termin_id\";}}s:13:\"statusgruppen\";a:2:{s:9:\"db_fields\";a:14:{s:15:\"statusgruppe_id\";a:5:{s:4:\"name\";s:15:\"statusgruppe_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:4:\"name\";a:5:{s:4:\"name\";s:4:\"name\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:12:\"varchar(255)\";s:5:\"extra\";s:0:\"\";}s:11:\"description\";a:5:{s:4:\"name\";s:11:\"description\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:4:\"text\";s:5:\"extra\";s:0:\"\";}s:8:\"range_id\";a:5:{s:4:\"name\";s:8:\"range_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:8:\"position\";a:5:{s:4:\"name\";s:8:\"position\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:7:\"int(11)\";s:5:\"extra\";s:0:\"\";}s:4:\"size\";a:5:{s:4:\"name\";s:4:\"size\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:7:\"int(11)\";s:5:\"extra\";s:0:\"\";}s:10:\"selfassign\";a:5:{s:4:\"name\";s:10:\"selfassign\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:19:\"tinyint(3) unsigned\";s:5:\"extra\";s:0:\"\";}s:16:\"selfassign_start\";a:5:{s:4:\"name\";s:16:\"selfassign_start\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:14:\"selfassign_end\";a:5:{s:4:\"name\";s:14:\"selfassign_end\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:6:\"mkdate\";a:5:{s:4:\"name\";s:6:\"mkdate\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:6:\"chdate\";a:5:{s:4:\"name\";s:6:\"chdate\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:14:\"calendar_group\";a:5:{s:4:\"name\";s:14:\"calendar_group\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:19:\"tinyint(3) unsigned\";s:5:\"extra\";s:0:\"\";}s:6:\"name_w\";a:5:{s:4:\"name\";s:6:\"name_w\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:12:\"varchar(255)\";s:5:\"extra\";s:0:\"\";}s:6:\"name_m\";a:5:{s:4:\"name\";s:6:\"name_m\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:12:\"varchar(255)\";s:5:\"extra\";s:0:\"\";}}s:2:\"pk\";a:1:{i:0;s:15:\"statusgruppe_id\";}}s:21:\"termin_related_groups\";a:2:{s:9:\"db_fields\";a:2:{s:9:\"termin_id\";a:5:{s:4:\"name\";s:9:\"termin_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";N;s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:15:\"statusgruppe_id\";a:5:{s:4:\"name\";s:15:\"statusgruppe_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";N;s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}}s:2:\"pk\";a:2:{i:0;s:9:\"termin_id\";i:1;s:15:\"statusgruppe_id\";}}s:22:\"termin_related_persons\";a:2:{s:9:\"db_fields\";a:2:{s:8:\"range_id\";a:5:{s:4:\"name\";s:8:\"range_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";N;s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:7:\"user_id\";a:5:{s:4:\"name\";s:7:\"user_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";N;s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}}s:2:\"pk\";a:2:{i:0;s:8:\"range_id\";i:1;s:7:\"user_id\";}}s:19:\"resource_categories\";a:2:{s:9:\"db_fields\";a:8:{s:2:\"id\";a:5:{s:4:\"name\";s:2:\"id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:4:\"name\";a:5:{s:4:\"name\";s:4:\"name\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:12:\"varchar(255)\";s:5:\"extra\";s:0:\"\";}s:11:\"description\";a:5:{s:4:\"name\";s:11:\"description\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";N;s:4:\"type\";s:4:\"text\";s:5:\"extra\";s:0:\"\";}s:6:\"system\";a:5:{s:4:\"name\";s:6:\"system\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:19:\"tinyint(3) unsigned\";s:5:\"extra\";s:0:\"\";}s:6:\"iconnr\";a:5:{s:4:\"name\";s:6:\"iconnr\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";s:1:\"1\";s:4:\"type\";s:7:\"int(11)\";s:5:\"extra\";s:0:\"\";}s:10:\"class_name\";a:5:{s:4:\"name\";s:10:\"class_name\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:8:\"Resource\";s:4:\"type\";s:11:\"varchar(60)\";s:5:\"extra\";s:0:\"\";}s:6:\"mkdate\";a:5:{s:4:\"name\";s:6:\"mkdate\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:6:\"chdate\";a:5:{s:4:\"name\";s:6:\"chdate\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}}s:2:\"pk\";a:1:{i:0;s:2:\"id\";}}s:26:\"resource_booking_intervals\";a:2:{s:9:\"db_fields\";a:8:{s:11:\"interval_id\";a:5:{s:4:\"name\";s:11:\"interval_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";N;s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:11:\"resource_id\";a:5:{s:4:\"name\";s:11:\"resource_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:10:\"booking_id\";a:5:{s:4:\"name\";s:10:\"booking_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:5:\"begin\";a:5:{s:4:\"name\";s:5:\"begin\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:3:\"end\";a:5:{s:4:\"name\";s:3:\"end\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:6:\"mkdate\";a:5:{s:4:\"name\";s:6:\"mkdate\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:6:\"chdate\";a:5:{s:4:\"name\";s:6:\"chdate\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:11:\"takes_place\";a:5:{s:4:\"name\";s:11:\"takes_place\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"1\";s:4:\"type\";s:19:\"tinyint(3) unsigned\";s:5:\"extra\";s:0:\"\";}}s:2:\"pk\";a:1:{i:0;s:11:\"interval_id\";}}s:7:\"folders\";a:2:{s:9:\"db_fields\";a:11:{s:2:\"id\";a:5:{s:4:\"name\";s:2:\"id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";N;s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:7:\"user_id\";a:5:{s:4:\"name\";s:7:\"user_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";N;s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:9:\"parent_id\";a:5:{s:4:\"name\";s:9:\"parent_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";N;s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:8:\"range_id\";a:5:{s:4:\"name\";s:8:\"range_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";N;s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:10:\"range_type\";a:5:{s:4:\"name\";s:10:\"range_type\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";N;s:4:\"type\";s:11:\"varchar(32)\";s:5:\"extra\";s:0:\"\";}s:11:\"folder_type\";a:5:{s:4:\"name\";s:11:\"folder_type\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";N;s:4:\"type\";s:12:\"varchar(255)\";s:5:\"extra\";s:0:\"\";}s:4:\"name\";a:5:{s:4:\"name\";s:4:\"name\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";N;s:4:\"type\";s:12:\"varchar(255)\";s:5:\"extra\";s:0:\"\";}s:12:\"data_content\";a:5:{s:4:\"name\";s:12:\"data_content\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";N;s:4:\"type\";s:4:\"text\";s:5:\"extra\";s:0:\"\";}s:11:\"description\";a:5:{s:4:\"name\";s:11:\"description\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";N;s:4:\"type\";s:4:\"text\";s:5:\"extra\";s:0:\"\";}s:6:\"mkdate\";a:5:{s:4:\"name\";s:6:\"mkdate\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";N;s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:6:\"chdate\";a:5:{s:4:\"name\";s:6:\"chdate\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";N;s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}}s:2:\"pk\";a:1:{i:0;s:2:\"id\";}}s:9:\"file_refs\";a:2:{s:9:\"db_fields\";a:10:{s:2:\"id\";a:5:{s:4:\"name\";s:2:\"id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";N;s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:7:\"file_id\";a:5:{s:4:\"name\";s:7:\"file_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";N;s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:9:\"folder_id\";a:5:{s:4:\"name\";s:9:\"folder_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";N;s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:9:\"downloads\";a:5:{s:4:\"name\";s:9:\"downloads\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:11:\"description\";a:5:{s:4:\"name\";s:11:\"description\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";N;s:4:\"type\";s:4:\"text\";s:5:\"extra\";s:0:\"\";}s:23:\"content_terms_of_use_id\";a:5:{s:4:\"name\";s:23:\"content_terms_of_use_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";N;s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:7:\"user_id\";a:5:{s:4:\"name\";s:7:\"user_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:4:\"name\";a:5:{s:4:\"name\";s:4:\"name\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:12:\"varchar(255)\";s:5:\"extra\";s:0:\"\";}s:6:\"mkdate\";a:5:{s:4:\"name\";s:6:\"mkdate\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:6:\"chdate\";a:5:{s:4:\"name\";s:6:\"chdate\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}}s:2:\"pk\";a:1:{i:0;s:2:\"id\";}}s:17:\"resource_requests\";a:2:{s:9:\"db_fields\";a:18:{s:2:\"id\";a:5:{s:4:\"name\";s:2:\"id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:9:\"course_id\";a:5:{s:4:\"name\";s:9:\"course_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:9:\"termin_id\";a:5:{s:4:\"name\";s:9:\"termin_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:11:\"metadate_id\";a:5:{s:4:\"name\";s:11:\"metadate_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:7:\"user_id\";a:5:{s:4:\"name\";s:7:\"user_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:16:\"last_modified_by\";a:5:{s:4:\"name\";s:16:\"last_modified_by\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:11:\"resource_id\";a:5:{s:4:\"name\";s:11:\"resource_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:11:\"category_id\";a:5:{s:4:\"name\";s:11:\"category_id\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:7:\"comment\";a:5:{s:4:\"name\";s:7:\"comment\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:4:\"text\";s:5:\"extra\";s:0:\"\";}s:13:\"reply_comment\";a:5:{s:4:\"name\";s:13:\"reply_comment\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:4:\"text\";s:5:\"extra\";s:0:\"\";}s:16:\"reply_recipients\";a:5:{s:4:\"name\";s:16:\"reply_recipients\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:9:\"requester\";s:4:\"type\";s:28:\"enum(\'requester\',\'lecturer\')\";s:5:\"extra\";s:0:\"\";}s:6:\"closed\";a:5:{s:4:\"name\";s:6:\"closed\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:19:\"tinyint(3) unsigned\";s:5:\"extra\";s:0:\"\";}s:6:\"mkdate\";a:5:{s:4:\"name\";s:6:\"mkdate\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:6:\"chdate\";a:5:{s:4:\"name\";s:6:\"chdate\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:5:\"begin\";a:5:{s:4:\"name\";s:5:\"begin\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:3:\"end\";a:5:{s:4:\"name\";s:3:\"end\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:16:\"preparation_time\";a:5:{s:4:\"name\";s:16:\"preparation_time\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:7:\"int(11)\";s:5:\"extra\";s:0:\"\";}s:6:\"marked\";a:5:{s:4:\"name\";s:6:\"marked\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:19:\"tinyint(3) unsigned\";s:5:\"extra\";s:0:\"\";}}s:2:\"pk\";a:1:{i:0;s:2:\"id\";}}s:27:\"resource_request_properties\";a:2:{s:9:\"db_fields\";a:5:{s:10:\"request_id\";a:5:{s:4:\"name\";s:10:\"request_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:11:\"property_id\";a:5:{s:4:\"name\";s:11:\"property_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:5:\"state\";a:5:{s:4:\"name\";s:5:\"state\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:4:\"text\";s:5:\"extra\";s:0:\"\";}s:6:\"mkdate\";a:5:{s:4:\"name\";s:6:\"mkdate\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:6:\"chdate\";a:5:{s:4:\"name\";s:6:\"chdate\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}}s:2:\"pk\";a:2:{i:0;s:10:\"request_id\";i:1;s:11:\"property_id\";}}s:29:\"resource_request_appointments\";a:2:{s:9:\"db_fields\";a:5:{s:2:\"id\";a:5:{s:4:\"name\";s:2:\"id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";N;s:4:\"type\";s:7:\"int(11)\";s:5:\"extra\";s:14:\"auto_increment\";}s:10:\"request_id\";a:5:{s:4:\"name\";s:10:\"request_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";N;s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:14:\"appointment_id\";a:5:{s:4:\"name\";s:14:\"appointment_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";N;s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:6:\"mkdate\";a:5:{s:4:\"name\";s:6:\"mkdate\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:6:\"chdate\";a:5:{s:4:\"name\";s:6:\"chdate\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}}s:2:\"pk\";a:1:{i:0;s:2:\"id\";}}s:29:\"resource_property_definitions\";a:2:{s:9:\"db_fields\";a:15:{s:11:\"property_id\";a:5:{s:4:\"name\";s:11:\"property_id\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:8:\"char(32)\";s:5:\"extra\";s:0:\"\";}s:4:\"name\";a:5:{s:4:\"name\";s:4:\"name\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:12:\"varchar(255)\";s:5:\"extra\";s:0:\"\";}s:11:\"description\";a:5:{s:4:\"name\";s:11:\"description\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:4:\"text\";s:5:\"extra\";s:0:\"\";}s:4:\"type\";a:5:{s:4:\"name\";s:4:\"type\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";N;s:4:\"type\";s:80:\"enum(\'bool\',\'text\',\'num\',\'select\',\'user\',\'institute\',\'position\',\'fileref\',\'url\')\";s:5:\"extra\";s:0:\"\";}s:7:\"options\";a:5:{s:4:\"name\";s:7:\"options\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";N;s:4:\"type\";s:4:\"text\";s:5:\"extra\";s:0:\"\";}s:6:\"system\";a:5:{s:4:\"name\";s:6:\"system\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:19:\"tinyint(3) unsigned\";s:5:\"extra\";s:0:\"\";}s:10:\"info_label\";a:5:{s:4:\"name\";s:10:\"info_label\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:10:\"tinyint(4)\";s:5:\"extra\";s:0:\"\";}s:12:\"display_name\";a:5:{s:4:\"name\";s:12:\"display_name\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:0:\"\";s:4:\"type\";s:12:\"varchar(512)\";s:5:\"extra\";s:0:\"\";}s:10:\"searchable\";a:5:{s:4:\"name\";s:10:\"searchable\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:19:\"tinyint(3) unsigned\";s:5:\"extra\";s:0:\"\";}s:12:\"range_search\";a:5:{s:4:\"name\";s:12:\"range_search\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:19:\"tinyint(3) unsigned\";s:5:\"extra\";s:0:\"\";}s:22:\"write_permission_level\";a:5:{s:4:\"name\";s:22:\"write_permission_level\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:12:\"admin-global\";s:4:\"type\";s:11:\"varchar(16)\";s:5:\"extra\";s:0:\"\";}s:17:\"property_group_id\";a:5:{s:4:\"name\";s:17:\"property_group_id\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:7:\"int(11)\";s:5:\"extra\";s:0:\"\";}s:18:\"property_group_pos\";a:5:{s:4:\"name\";s:18:\"property_group_pos\";s:4:\"null\";s:3:\"YES\";s:7:\"default\";N;s:4:\"type\";s:10:\"tinyint(4)\";s:5:\"extra\";s:0:\"\";}s:6:\"mkdate\";a:5:{s:4:\"name\";s:6:\"mkdate\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}s:6:\"chdate\";a:5:{s:4:\"name\";s:6:\"chdate\";s:4:\"null\";s:2:\"NO\";s:7:\"default\";s:1:\"0\";s:4:\"type\";s:16:\"int(10) unsigned\";s:5:\"extra\";s:0:\"\";}}s:2:\"pk\";a:1:{i:0;s:11:\"property_id\";}}}\";',1754507911);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `cache_operations`
--

LOCK TABLES `cache_operations` WRITE;
/*!40000 ALTER TABLE `cache_operations` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `cache_operations` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `cache_types`
--

LOCK TABLES `cache_types` WRITE;
/*!40000 ALTER TABLE `cache_types` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `cache_types` VALUES
(1,'Studip\\Cache\\DbCache',1640797278,1640797278),
(2,'Studip\\Cache\\FileCache',1640797278,1640797278),
(3,'Studip\\Cache\\MemcachedCache',1640797278,1640797278),
(4,'Studip\\Cache\\RedisCache',1640797278,1640797278);
/*!40000 ALTER TABLE `cache_types` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `calendar_date_assignments`
--

LOCK TABLES `calendar_date_assignments` WRITE;
/*!40000 ALTER TABLE `calendar_date_assignments` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `calendar_date_assignments` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `calendar_date_exceptions`
--

LOCK TABLES `calendar_date_exceptions` WRITE;
/*!40000 ALTER TABLE `calendar_date_exceptions` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `calendar_date_exceptions` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `calendar_dates`
--

LOCK TABLES `calendar_dates` WRITE;
/*!40000 ALTER TABLE `calendar_dates` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `calendar_dates` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `captcha_challenges`
--

LOCK TABLES `captcha_challenges` WRITE;
/*!40000 ALTER TABLE `captcha_challenges` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `captcha_challenges` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `clipboard_items`
--

LOCK TABLES `clipboard_items` WRITE;
/*!40000 ALTER TABLE `clipboard_items` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `clipboard_items` VALUES
(1,1,'728f1578de643fb08b32b4b8afb2db77','Room',1591715354,1591715354),
(2,1,'b17c4ea6e053f2fffba8a5517fc277b3','Room',1591715356,1591715356),
(3,1,'2f98bf64830043fd98a39fbbe2068678','Room',1591715357,1591715357),
(4,2,'51ad4b7100d3a8a1db61c7b099f052a6','Room',1591715367,1591715367),
(5,2,'a8c03520e8ad9dc90fb2d161ffca7d7b','Room',1591715368,1591715368),
(6,2,'5ead77812be3b601e2f08ed5da4c5630','Room',1591715370,1591715370);
/*!40000 ALTER TABLE `clipboard_items` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `clipboards`
--

LOCK TABLES `clipboards` WRITE;
/*!40000 ALTER TABLE `clipboards` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `clipboards` VALUES
(1,'76ed43ef286fb55cf9e41beadb484a9f','HS','Clipboard','StudipItem',1591715351,1591715351),
(2,'76ed43ef286fb55cf9e41beadb484a9f','SR','Clipboard','StudipItem',1591715364,1591715364);
/*!40000 ALTER TABLE `clipboards` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `colour_values`
--

LOCK TABLES `colour_values` WRITE;
/*!40000 ALTER TABLE `colour_values` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `colour_values` VALUES
('Resources.BookingPlan.Booking.Bg','Die Farbe im Belegungsplan für gewöhnliche Buchungen.','129c94ff',1591630777,1591630777),
('Resources.BookingPlan.Booking.Fg','Die Textfarbe im Belegungsplan für gewöhnliche Buchungen.','ffffffff',1591630777,1591630777),
('Resources.BookingPlan.CourseBooking.Bg','Die Farbe im Belegungsplan für veranstaltungsbezogene Buchungen.','682c8bff',1591630777,1591630777),
('Resources.BookingPlan.CourseBooking.Fg','Die Textfarbe im Belegungsplan für veranstaltungsbezogene Buchungen.','ffffffff',1591630777,1591630777),
('Resources.BookingPlan.CourseBookingWithExceptions.Bg','Die Farbe im Belegungsplan für veranstaltungsbezogene Buchungen mit Ausfallterminen.','a480b9ff',1591630777,1591630777),
('Resources.BookingPlan.CourseBookingWithExceptions.Fg','Die Textfarbe im Belegungsplan für veranstaltungsbezogene Buchungen mit Ausfallterminen.','ffffffff',1591630777,1591630777),
('Resources.BookingPlan.Lock.Bg','Die Farbe im Belegungsplan für Sperrbuchungen.','d60000ff',1591630777,1591630777),
('Resources.BookingPlan.Lock.Fg','Die Textfarbe im Belegungsplan für Sperrbuchungen.','ffffffff',1591630777,1591630777),
('Resources.BookingPlan.PlannedBooking.Bg','Die Farbe im Belegungsplan für geplante Buchungen.','f26e00ff',1591630777,1591630777),
('Resources.BookingPlan.PlannedBooking.Fg','Die Textfarbe im Belegungsplan für geplante Buchungen.','000000ff',1591630777,1591630777),
('Resources.BookingPlan.PreparationTime.Bg','Die Farbe im Belegungsplan für Rüstzeiten.','cf81b0ff',1591630777,1591630777),
('Resources.BookingPlan.PreparationTime.Fg','Die Textfarbe im Belegungsplan für Rüstzeiten.','000000ff',1591630777,1591630777),
('Resources.BookingPlan.Request.Bg','Die Farbe im Belegungsplan für Anfragen.','ffbd33ff',1591630777,1591630777),
('Resources.BookingPlan.Request.Fg','Die Textfarbe im Belegungsplan für Anfragen.','000000ff',1591630777,1591630777),
('Resources.BookingPlan.Reservation.Bg','Die Farbe im Belegungsplan für Reservierungen.','6ead10ff',1591630777,1591630777),
('Resources.BookingPlan.Reservation.Fg','Die Textfarbe im Belegungsplan für Reservierungen.','ffffffff',1591630777,1591630777),
('Resources.BookingPlan.SimpleBookingWithExceptions.Bg','Die Farbe im Belegungsplan für einfache Buchungen mit Wiederholungen, bei denen es Ausfalltermine gibt.','70c3bfff',1591630777,1591630777),
('Resources.BookingPlan.SimpleBookingWithExceptions.Fg','Die Textfarbe im Belegungsplan für einfache Buchungen mit Wiederholungen, bei denen es Ausfalltermine gibt.','ffffffff',1591630777,1591630777);
/*!40000 ALTER TABLE `colour_values` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `conditionaladmissions`
--

LOCK TABLES `conditionaladmissions` WRITE;
/*!40000 ALTER TABLE `conditionaladmissions` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `conditionaladmissions` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `config`
--

LOCK TABLES `config` WRITE;
/*!40000 ALTER TABLE `config` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `config` VALUES
('ACCESSIBILITY_DISCLAIMER_URL','','string','global','accessibility',1698855217,1698855217,'URL der Barrierefreiheitserklärung, die in der Fußleiste verlinkt wird. Wenn Sie den Mustertext im Impressum verwenden, tragen Sie diese URL ein: dispatch.php/siteinfo/show/1/8'),
('ACCESSIBILITY_INFO_TEXT','','i18n','global','accessibility',1686150733,1686150733,'Diese Konfiguration bitte unter Admin -> Standort -> Infotext zu barrierefreien Dateien anpassen!'),
('ACCESSIBILITY_RECEIVER_EMAIL','','array','global','accessibility',1686150733,1686150733,'Die E-Mail-Adressen der Personen, die beim Melden einer Barriere benachrichtigt werden sollen.\n                Beispiel: [\"mailadresse1@server.de\",\"mailadresse2@server.de\"]'),
('ACTION_MENU_THRESHOLD','1','integer','global','global',1669041528,1669041528,'Obergrenze an Einträgen, bis zu der ein Aktionsmenü als Icons dargestellt wird'),
('ADMIN_COURSES_DATAFIELDS_FILTERS','[]','array','user','',1698855217,1698855217,'Für Admins, Roots und DedicatedAdmins können hier die Datenfelder gespeichert werden, nach denen die Veranstaltungen gefiltert werden sollen.'),
('ADMIN_COURSES_SEARCHTEXT','','string','user','',1698855218,1698855218,'Speichert den auf der Veranstaltungsübersicht für Admins eingegebenen Suchtext'),
('ADMIN_COURSES_SHOW_COMPLETE','1','boolean','global','global',1462287310,1462287310,'Definiert, ob auf der Admin-Veranstaltunggseite der Komplett-Status für Veranstaltungen aufgeführt sein soll'),
('ADMIN_COURSES_SIDEBAR_ACTIVE_ELEMENTS','','string','user','',0,0,'Diese Einstellung legt fest, welche Elemente in der Seitenleiste der Veranstaltungsübersicht für Admins sichtbar sind.'),
('ADMIN_COURSES_TEACHERFILTER','','string','user','',1698855218,1698855218,'Der auf der Veranstaltungsübersicht für Admins gewählte Filter auf Lehrende'),
('ADMISSION_PRELIM_COMMENT_ENABLE','1','boolean','global','',1153814966,1153814966,'Schaltet ein oder aus, ob ein Nutzer im Modus \"Vorläufiger Eintrag\" eine Bemerkung hinterlegen kann'),
('AJAX_AUTOCOMPLETE_DISABLED','0','boolean','global','',1293118060,1293118060,'Sollen alle QuickSearches deaktiviertes Autocomplete haben? Wenn es zu Performanceproblemen kommt, kann es sich lohnen, diese Variable auf true zu stellen.'),
('ALLOW_ADMIN_RELATED_INST','0','boolean','global','global',1640797278,1640797278,'Admins beteiligter Einrichtungen haben die gleiche Rechte an Veranstaltungen wie die Heimateinrichtung'),
('ALLOW_ADMIN_USERACCESS','1','boolean','global','permissions',1240427632,1240427632,'Wenn eingeschaltet, dürfen Administratoren sensible persönliche Angaben wie z.B. Passwörter ändern.'),
('ALLOW_CHANGE_EMAIL','1','boolean','global','permissions',1510849314,1510849314,'If true, users are allowed to change their email'),
('ALLOW_CHANGE_NAME','1','boolean','global','permissions',1510849314,1510849314,'If true, users are allowed to change their name'),
('ALLOW_CHANGE_TITLE','1','boolean','global','permissions',1510849314,1510849314,'If true, users are allowed to change their titles'),
('ALLOW_CHANGE_USERNAME','1','boolean','global','permissions',1510849314,1510849314,'If true, users are allowed to change their username'),
('ALLOW_DOZENT_COURSESET_ADMIN','0','boolean','global','coursesets',1403258021,1403258021,'Sollen Lehrende einrichtungsweite Anmeldesets anlegen und bearbeiten dürfen?'),
('ALLOW_DOZENT_DELETE','0','boolean','global','permissions',0,1109946684,'Schaltet ein oder aus, ob eine Lehrperson eigene Veranstaltungen selbst löschen darf oder nicht'),
('ALLOW_DOZENT_VISIBILITY','0','boolean','global','permissions',0,0,'Schaltet ein oder aus, ob eine Lerhrperson eigene Veranstaltungen selbst verstecken darf oder nicht'),
('ALLOW_SELFASSIGN_INSTITUTE','1','boolean','global','permissions',1240427632,1240427632,'Wenn eingeschaltet, dürfen Studenten sich selbst Einrichtungen an denen sie studieren zuordnen.'),
('ALLOW_SELFASSIGN_STUDYCOURSE','1','boolean','global','global',1510849314,1510849314,'If true, students are allowed to set or change their studycourse (studiengang)'),
('AUTO_INSERT_SEM_PARTICIPANTS_VIEW_PERM','tutor','string','global','global',1311411856,1311411856,'Ab welchem Status soll in Veranstaltungen mit automatisch eingetragenen Nutzern der Teilnehmerreiter zu sehen sein?'),
('AUX_RULE_ADMIN_PERM','admin','string','global','permissions',1240427632,1240427632,'mit welchem Status dürfen Zusatzangaben definiert werden (admin, root)'),
('BANNER_ADS_ENABLE','0','boolean','global','modules',1293118059,1293118059,'Schaltet ein oder aus, ob die Bannerwerbung global verfügbar ist.'),
('BANNER_ONLY_SYSTEM_ROLES','1','boolean','global','',1656513810,1656513810,'Über diese Option wird die Auswahl der rollenspezifischen Banner auf Systemrollen begrenzt'),
('BLUBBER_DEFAULT_THREAD','1','string','user','',1591630778,1591630778,'Dieses ist bei dem globalen Blubber-Messenger der vorausgewählte Blubber.'),
('BLUBBER_GLOBAL_MESSENGER_ACTIVATE','1','boolean','global','global',1591630778,1591630778,'Ist Blubber unter Community global aktiv? Blubber in Veranstaltungen wird über das Plugin Blubber aktiviert oder deaktiviert.'),
('BLUBBER_GLOBAL_THREAD_OPTOUT','1','boolean','global','global',1640797278,1640797278,'Gibt an, ob beim globalen Blubber Thread ein Opt-Out-Verfahren genutzt werden soll'),
('CALENDAR_ENABLE','1','boolean','global','calendar',1293118059,1293118059,'Schaltet ein oder aus, ob der Kalender global verfügbar ist.'),
('CALENDAR_GRANT_ALL_INSERT','1','boolean','global','calendar',1462287762,1462287762,'Ermöglicht das Eintragen von Terminen in alle Kalender der Nutzenden, ohne Beachtung des Rechtesystems.'),
('CALENDAR_GROUP_ENABLE','0','boolean','global','calendar',1326799692,1326799692,'Schaltet die Gruppenterminkalender-Funktionen ein.'),
('CALENDAR_SETTINGS','{\"view\":\"week\",\"start\":\"9\",\"end\":\"20\",\"step_day\":\"900\",\"step_week\":\"1800\",\"type_week\":\"LONG\",\"step_week_group\":\"3600\",\"step_day_group\":\"3600\"}','array','user','',1403258015,1403258015,'persönliche Einstellungen des Kalenders'),
('CAPTCHA_KEY','','string','global','',1754464706,1754464706,'Speichert den für Captchas verwendeten Schlüssel (Wert leeren, um einen neuen zu generieren)'),
('CONSULTATION_ALLOW_DOCENTS_RESERVING','1','boolean','global','Terminvergabe',1557244743,1557244743,'Lehrende können sich bei anderen Lehrenden anmelden'),
('CONSULTATION_ENABLED','1','boolean','global','Terminvergabe',1557244743,1557244743,'Schaltet die Sprechstunden global ein'),
('CONSULTATION_EXCLUDE_EXPIRED','1','boolean','user','global',1573236813,1573236813,'Sprechstunden: Sollen abgelaufene Blöcke ausgeblendet werden'),
('CONSULTATION_GARBAGE_COLLECT','0','boolean','range','Terminvergabe',1640797277,1640797277,'Sollen abgelaufene Termine automatisch abgeräumt werden?'),
('CONSULTATION_REQUIRED_PERMISSION','tutor','string','global','Terminvergabe',1557244743,1557244743,'Ab welcher Rechtestufe dürfen Nutzer Sprechstunden anlegen (user, autor, tutor, dozent, admin, root)'),
('CONSULTATION_SEND_MESSAGES','1','boolean','user','Terminvergabe',1557244743,1557244743,'Nachrichten empfangen über Buchungen/Stornierungen'),
('CONSULTATION_SHOW_GROUPED','1','boolean','user','Terminvergabe',1640797277,1640797277,'Sollen die Termine nach Blöcken sortiert angezeigt werden?'),
('CONSULTATION_TAB_TITLE','Terminvergabe','i18n','range','Terminvergabe',1640797277,1640797277,'Der Name des Reiters für die Terminvergabe'),
('CONTENTMODULES_TILED_DISPLAY','1','boolean','user','',1698855218,1698855218,'Bevorzugt ein Nutzer eine Kachelansicht auf der Werkzeugseite in den Veranstaltungen oder lieber eine Tabelle?'),
('CONVERT_IDNA_URL','1','boolean','global','global',1510849314,1510849314,'If true, urls with german \"umlauts\" are converted'),
('COURSEWARE_CERTIFICATES_ENABLE','1','boolean','global','',1716385357,1716385357,'Schaltet Courseware-Zertifikate, -Erinnerungen und -Fortschrittsrücksetzung ein oder aus'),
('COURSEWARE_FAVORITE_BLOCK_TYPES','[]','array','user','',1640797279,1640797279,'In dieser Konfigurationseinstellung können Nutzende ihre Lieblingsblocktypen speichern.'),
('COURSEWARE_LAST_ELEMENT','[]','array','user','',1640797279,1640797279,'In dieser Konfigurationseinstellung werden die zuletzt besuchten Elemente in allen Coursewares abgelegt.'),
('COURSE_ADMIN_NOTICE','','string','course','',1640797279,1640797279,'Admins: Notiz zu einer Veranstaltung'),
('COURSE_CALENDAR_ENABLE','0','boolean','global','calendar',1326799692,1326799692,'Kalender als Inhaltselement in Veranstaltungen.'),
('COURSE_MANAGEMENT_SELECTOR_ORDER_BY','name','string','user','',1686150733,1686150733,'Gibt an, nach welchem Kriterium die Veranstaltungsschnellwauswahl innerhalb der Veranstaltungsverwaltung sortiert werden soll'),
('COURSE_NUMBER_FORMAT','','string','global','global',1510849314,1510849314,'Erlaubt das Eintragen eines regulären Ausdrucks zur Validierung einer Veranstaltungsnummer. Im Kommentarfeld kann ein entsprechender Hilfetext hinterlegt werden.'),
('COURSE_PUBLIC_TOPICS','0','boolean','course','',1543856103,1543856103,'Über diese Option können Sie die Themen einer Veranstaltung öffentlich einsehbar machen.'),
('COURSE_SEARCH_IS_VISIBLE_NOBODY','0','boolean','global','coursesearch',1543856104,1543856104,'Soll die Veranstaltungssuche auch für nobody (ohne Anmeldung) sichtbar sein?'),
('COURSE_SEARCH_NAVIGATION_OPTIONS','{\"courses\":{\"visible\":true,\"target\":\"sidebar\"},\"semtree\":{\"visible\":true,\"target\":\"sidebar\"},\"rangetree\":{\"visible\":true,\"target\":\"sidebar\"},\"module\":{\"visible\":true,\"target\":\"sidebar\"}}','array','global','coursesearch',1543856104,1543856104,'Aktivierung und Reihenfolge der Navigationsoptionen in der Veranstaltungssuche'),
('COURSE_SEARCH_SHOW_ADMISSION_STATE','1','boolean','global','coursesearch',1543856104,1543856104,'Anzeige des Zugangsstatus in der Veranstaltungssuche als Icon.'),
('COURSE_SEM_TREE_CLOSED_LEVELS','[1]','array','global','global',1416496270,1416496270,'Gibt an, welche Ebenen der Studienbereichszuordnung geschlossen bleiben sollen'),
('COURSE_SEM_TREE_DISPLAY','0','boolean','global','global',1416496270,1416496270,'Zeigt den Studienbereichsbaum als Baum an'),
('COURSE_STUDENT_MAILING','0','boolean','course','',1530289048,1530289048,'Über diese Option können Sie Studierenden das Schreiben von Nachrichten an alle anderen Teilnehmer der Veranstaltung erlauben.'),
('CRONJOBS_ENABLE','1','boolean','global','global',1403258015,1403258015,'Schaltet die Cronjobs an'),
('CURRENT_LOGIN_TIMESTAMP','0','integer','user','',1403258015,1403258015,'Zeitstempel des Logins'),
('CUSTOMIZED_HOLIDAYS','[]','array','global','global',1754464707,1754464707,'Speichert die internen Ids von Feiertagen, die als gesetztlich markiert werden sollen'),
('DEFAULT_LANGUAGE','de_DE','string','global','global',1510849314,1510849314,'Which language should we use if we can gather no information from user?'),
('DEFAULT_TIMEZONE','Europe/Berlin','string','global','global',1510849314,1510849314,'What timezone should be used (default: Europe/Berlin)?'),
('DEPUTIES_DEFAULTENTRY_ENABLE','1','boolean','global','deputies',1293118059,1293118059,'Dürfen Lehrende Standardvertretungen festlegen? Diese werden automatisch bei Hinzufügen von Lehrenden als Vertretung in Veranstaltungen eingetragen.'),
('DEPUTIES_EDIT_ABOUT_ENABLE','1','boolean','global','deputies',1293118059,1293118059,'Dürfen Lehrende ihren Standardvertretungen erlauben, ihr Profil zu bearbeiten?'),
('DEPUTIES_ENABLE','1','boolean','global','deputies',1293118059,1293118059,'Legt fest, ob die Funktion Vertretung aktiviert ist.'),
('DISPLAY_DOWNLOAD_COUNTER','always','string','global','files',1591630777,1591630777,'Steuert die Anzeige der Anzahl der Downloads in Dateisichten (\"always\" zeigt die Anzahl immer an, \"flat\" nur in \"Alle Dateien\", jeder andere Wert schaltet die Anzeige komplett aus)'),
('DISPLAY_STGTEILVERSION_USERFILTER','0','boolean','global','coursesets',1591630778,1591630778,'Steuert die Anzeige des Studiengangteil-Version Filters beim Erstellen von bedingten Anmelderegeln.'),
('DOZENT_ALWAYS_VISIBLE','1','boolean','global','privacy',1293118059,1293118059,'Legt fest, ob Personen mit Lehrendenrechten immer global sichtbar sind und das auch nicht selbst ändern können.'),
('DUMMY_TEACHER_ID','2afaa0dce05f0b12a7318075e52879e2','string','global','global',1754464709,1754464709,'ID of user that should be added to course if no teacher is left'),
('EASY_READ_URL','dispatch.php/siteinfo/show/1/9','string','global','accessibility',1716385357,1716385357,'URL zur Seite \"Leichte Sprache\"'),
('EMAIL_DOMAIN_RESTRICTION','','string','global','',1157107088,1157107088,'Beschränkt die gültigkeit von Email-Adressen bei freier Registrierung auf die angegebenen Domains. Komma-separierte Liste von Domains ohne vorangestelltes @.'),
('EMAIL_VISIBILITY_DEFAULT','1','boolean','global','privacy',1326799691,1326799691,'Ist die eigene Emailadresse sichtbar, falls der Nutzer nichts anderes eingestellt hat?'),
('ENABLE_ARCHIVE_SEARCH','0','boolean','global','global',1557244743,1557244743,'Soll es eine Suche in dem alten Archiv geben?'),
('ENABLE_COURSESET_FCFS','1','boolean','global','coursesets',1403258021,1403258021,'Soll first-come-first-served (Windhundverfahren) bei der Anmeldung erlaubt sein?'),
('ENABLE_DESCRIPTION_ENTRY_ON_UPLOAD','1','boolean','global','files',1591630777,1591630777,'Whether to allow adding a description directly after file upload (true) or not (false). Defaults to true.'),
('ENABLE_FREE_ACCESS','0','string','global','global',1510849314,1510849314,'1: courses and institutes with public access are visible without login. courses_only: only courses with public access are visible without login. 0: disable this feature.'),
('ENABLE_NUMBER_OF_PARTICIPANTS','0','','global','global',1754464707,1754464707,'Schaltet die Möglichkeit zum Erfassen der tatsächlichen Teilnehmendenanzahl pro Termin ein.'),
('ENABLE_REQUEST_NEW_PASSWORD_BY_USER','1','boolean','global','permissions',1510849314,1510849314,'If true, users are able to request a new password themselves'),
('ENABLE_SELF_REGISTRATION','1','boolean','global','permissions',1510849314,1510849314,'Should it be possible for an user to register himself'),
('ENABLE_STUDYCOURSE_INFO_PAGE','0','boolean','global','global',1591630777,1591630777,'Shows an icon to open a dialog with studycourse informations in module search if true.'),
('ENTRIES_PER_PAGE','20','integer','global','global',1311411856,1311411856,'Anzahl von Einträgen pro Seite'),
('EXPORT_ENABLE','1','boolean','global','modules',1293118059,1293118059,'Schaltet ein oder aus, ob der Export global verfügbar ist.'),
('EXTERN_ENABLE','1','boolean','global','modules',1293118059,1293118059,'Schaltet ein oder aus, ob die externen Seiten global verfügbar sind.'),
('EXTERN_PAGES_ERROR_MESSAGE','Ein Fehler ist aufgetreten. Die Inhalte können nicht angezeigt werden.','string','global','external_pages',1716385357,1716385357,'Allgemeine Fehlermeldung,die auf der Webseite ausgegeben wird, auf der der Inhalt der externe Seite angezeigt werden soll. Diese Meldung wird ausgegeben, wenn z.B. das Template fehlerhaft ist.'),
('FEEDBACK_ADMIN_PERM','tutor','string','course','',1591630778,1591630778,'Voreinstellung für Berechtigungslevel, um Einstellung zu Feedback-Elementen zu verwalten'),
('FEEDBACK_CREATE_PERM','tutor','string','course','',1591630778,1591630778,'Voreinstellung für Berechtigungslevel, um Feedback-Elemente anzulegen.'),
('FORUM_ANONYMOUS_POSTINGS','0','boolean','global','privacy',1293118059,1293118059,'Legt fest, ob Forenbeiträge anonym verfasst werden dürfen (Root sieht aber immer den Urheber).'),
('FORUM_SETTINGS','{\"neuauf\":false,\"rateallopen\":true,\"showimages\":true,\"sortthemes\":\"last\",\"themeview\":\"mixed\",\"presetview\":\"mixed\",\"shrink\":604800}','array','user','',1403258015,1403258015,'persönliche Einstellungen Forum'),
('GLOBALSEARCH_ASYNC_QUERIES','0','boolean','global','globalsearch',1530289048,1530289048,'Sollen die Suchanfragen asynchron über mysqli gestellt werden? Andernfalls wird PDO verwendet.'),
('GLOBALSEARCH_MAX_RESULT_OF_TYPE','5','integer','global','globalsearch',1530289048,1530289048,'Wie viele Ergebnisse sollen in der globalen Schnellsuche pro Kategorie angezeigt werden?'),
('GLOBALSEARCH_MODULES','{\"GlobalSearchBuzzwords\":{\"order\":1,\"active\":true,\"fulltext\":false},\"GlobalSearchCourses\":{\"order\":3,\"active\":true,\"fulltext\":false},\"GlobalSearchUsers\":{\"order\":4,\"active\":true,\"fulltext\":false},\"GlobalSearchInstitutes\":{\"order\":5,\"active\":true,\"fulltext\":false},\"GlobalSearchFiles\":{\"order\":6,\"active\":true,\"fulltext\":false},\"GlobalSearchCalendar\":{\"order\":7,\"active\":true,\"fulltext\":false},\"GlobalSearchMessages\":{\"order\":8,\"active\":true,\"fulltext\":false},\"GlobalSearchForum\":{\"order\":9,\"active\":true,\"fulltext\":false},\"GlobalSearchResources\":{\"order\":10,\"active\":true,\"fulltext\":false},\"GlobalSearchRoomAssignments\":{\"order\":11,\"active\":true,\"fulltext\":false},\"GlobalSearchModules\":{\"order\":12,\"active\":true,\"fulltext\":false},\"GlobalSearchBlubber\":{\"order\":13,\"active\":true,\"fulltext\":true},\"GlobalSearchCourseware\":{\"order\":14,\"active\":true,\"fulltext\":true},\"GlobalSearchStudygroups\":{\"order\":15,\"active\":true,\"fulltext\":false}}','array','global','globalsearch',1530289048,1530289048,'Aktivierung und Reihenfolge der Module in der globalen Suche'),
('HIDE_STUDYGROUPS_FROM_PROFILE','1','boolean','global','studygroups',1640797277,1640797277,'Sollen Studiengruppen bei der Anzeige der Veranstaltungen auf dem Profil versteckt werden?'),
('HOMEPAGEPLUGIN_DEFAULT_ACTIVATION','0','boolean','global','privacy',1403258014,1403258014,'Sollen neu installierte Homepageplugins automatisch für Benutzer aktiviert sein?'),
('HOMEPAGE_VISIBILITY_DEFAULT','VISIBILITY_STUDIP','string','global','privacy',1293118059,1293118059,'Standardsichtbarkeit für Homepageelemente, falls der Benutzer nichts anderes eingestellt hat. Gültige Werte sind: VISIBILITY_ME, VISIBILITY_BUDDIES, VISIBILITY_DOMAIN, VISIBILITY_STUDIP, VISIBILITY_EXTERN'),
('HTTP_PROXY','','string','global','global',1607702429,1607702429,'externe http Anfragen über proxy'),
('HTTP_PROXY_IGNORE','','string','global','global',1607702429,1607702429,'Kommaseparierte Liste mit Hostnamen, die nicht über Proxy aufgerufen werden sollen'),
('ILIAS_INTERFACE_BASIC_SETTINGS','{\"moduletitle\":\"ILIAS\",\"edit_moduletitle\":false,\"search_active\":true,\"show_offline\":false,\"cache\":true}','array','global','modules',1557244743,1557244743,''),
('ILIAS_INTERFACE_ENABLE','0','boolean','global','modules',1557244743,1557244743,''),
('ILIAS_INTERFACE_MODULETITLE','ILIAS','string','course','modules',1557244743,1557244743,''),
('ILIAS_INTERFACE_SETTINGS','[]','array','global','modules',1557244743,1557244743,''),
('IMPORTANT_SEMNUMBER','1','boolean','global','global',1403258018,1403258018,'Zeigt die Veranstaltungsnummer prominenter in der Suche und auf der Meine Veranstaltungen Seite an'),
('INSTITUTE_COURSE_PLAN_END_HOUR','20:00','string','global','modules',1591630777,1591630777,'The end hour for the default view of the institute course plan.'),
('INSTITUTE_COURSE_PLAN_START_HOUR','08:00','string','global','modules',1591630777,1591630777,'The start hour for the default view of the institute course plan.'),
('INST_FAK_ADMIN_PERMS','none','string','global','permissions',1293118059,1293118059,'\"none\" Fakultätsadmin darf Einrichtungen weder anlegen noch löschen, \"create\" Fakultätsadmin darf Einrichtungen anlegen, aber nicht löschen, \"all\" Fakultätsadmin darf Einrichtungen anlegen und löschen.'),
('JSONAPI_CORS_ORIGIN','[]','array','global','global',1591630777,1591630777,'Diese Einstellung definiert URIs, die mittels CORS auf die JSONAPI zugreifen dürfen.'),
('JSONAPI_DANGEROUS_ROUTES_ALLOWED','0','boolean','global','global',1591630776,1591630776,'Wenn diese Einstellung gesetzt ist, dürfen auch potentiell gefährliche JSONAPI-Routen genutzt werden. (Zum Beispiel dürfen dann root-Nutzer auch andere Nutzer löschen.)'),
('LAST_LOGIN_TIMESTAMP','0','integer','user','',1403258015,1403258015,'Zeitstempel des vorherigen Logins'),
('LIBRARY_ADD_ITEM_ACTION_DESCRIPTION','Sie können digitale Originaldokumente direkt aus der Bibliothek beziehen. Sie erhalten Materialien mit geklärten Rechten und in hochwertiger Qualität. Bei Bedarf kann die Bibliothek zur Bereitstellung eingebunden werden.','string','global','Library',1607702429,1607702429,'Der Beschreibungstext für die Aktion zum Hinzufügen eines Bibliothekseintrags in den Dateibereich.'),
('LITERATURE_ENABLE','0','boolean','global','modules',1293118059,1293118059,'Schaltet ein oder aus, ob die Literaturverwaltung global verfügbar ist.'),
('LOAD_EXTERNAL_MEDIA','deny','string','global','',1293118060,1293118060,'Sollen externe Medien über [img/audio/video] eingebunden werden? deny=nicht erlaubt, allow=erlaubt, proxy=proxy benutzen.'),
('LOCK_RULE_ADMIN_PERM','admin','string','global','permissions',1240427632,1240427632,'mit welchem Status dürfen Sperrebenen angepasst werden (admin, root)'),
('LOGIN_FAQ_TITLE','Hinweise zum Login','i18n','global','Loginseite',1716385357,1716385357,'Überschrift für den FAQ-Bereich auf der Loginseite'),
('LOGIN_FAQ_VISIBILITY','1','boolean','global','Loginseite',1716385357,1716385357,'Soll der FAQ-Bereich auf der Loginseite sichtbar sein?'),
('LOGIN_NEWS_VISIBILITY','1','boolean','global','Loginseite',1754464708,1754464708,'Soll Ankündigungs-Galerie auf der Loginseite sichtbar sein?'),
('LOG_ENABLE','1','boolean','global','modules',1293118059,1293118059,'Schaltet ein oder aus, ob das Log global verfügbar ist.'),
('LTI_ALLOW_TOOL_CONFIG_IN_COURSE','1','boolean','global','LTI',1754464709,1754464709,'Soll es Lehrenden möglich sein, eigene LTI-Tools zu konfigurieren? Wenn nicht, können nur global konfigurierte LTI-Tools in Veranstaltungen angebunden werden.'),
('LTI_DATA_PROTECTION_COURSE_WARNING','','string','course','LTI',1754464709,1754464709,'Eine in einer Veranstaltung angepasste Warnung zur Weitergabe personenbezogener Daten, die angezeigt wird, wenn Personen aus der Veranstaltung in ein LTI-Tool wechseln.'),
('LTI_DATA_PROTECTION_DEFAULT_WARNING','Bitte beachten Sie die Datenschutzhinweise. Wenn Sie zugestimmt haben, werden Ihre Daten weitergegeben.','string','global','LTI',1754464709,1754464709,'Eine Warnung zur Weitergabe personenbezogener Daten, die standardmäßig angezeigt wird, wenn Personen aus einer Veranstaltung in ein LTI-Tool wechseln.'),
('MAILQUEUE_ENABLE','0','boolean','global','global',1403258017,1403258017,'Aktiviert bzw. deaktiviert die Mailqueue'),
('MAILQUEUE_SEND_LIMIT','0','integer','global','global',1462287310,1462287310,'Wieviele Mails soll die Mailqueue maximal auf einmal an den Mailserver schicken. 0 für unendlich viele.'),
('MAIL_AS_HTML','1','boolean','user','',1293118060,1293118060,'Benachrichtigungen werden im HTML-Format versandt'),
('MAIL_NOTIFICATION_ENABLE','1','boolean','global','',1122996278,1122996278,'Informationen über neue Inhalte per email verschicken'),
('MAIL_SUBJECT_PREFIX','[Stud.IP]','string','global','global',1754464707,1754464707,'Stellt dem Titel von per Mail versandten Nachrichten'),
('MAINTENANCE_MODE_ENABLE','0','boolean','global','',1130840930,1130840930,'Schaltet das System in den Wartungsmodus, so dass nur noch Administratoren Zugriff haben'),
('MASSMAIL_GC_DAYS','7','integer','global','MassMail',1754464709,1754464709,'Anzahl Tage, nach denen bereits verschickte Nachrichten aus der Datenbank entfernt werden (0 bedeutet nie)'),
('MASSMAIL_LECTURER_SEM_CATEGORIES','[1]','array','global','MassMail',1754464709,1754464709,'Veranstaltungskategorien, die für die Ermittlung aktiver Lehrender berücksichtigt werden'),
('MAX_SHOW_ADMIN_COURSES','500','integer','global','MeineVeranstaltungen',1754464706,1754464706,'Wie viele Veranstaltungen sollen auf der Admin-Veranstaltungsseite angezeigt werden.'),
('MEDIA_CACHE_LIFETIME','86400','integer','global','global',1510849314,1510849314,'Wieviele Sekunden soll gecached werden?'),
('MEDIA_CACHE_MAX_FILES','3000','integer','global','global',1510849314,1510849314,'Wieviele Dateien sollen maximal gecached werden?'),
('MEDIA_CACHE_MAX_LENGTH','1000000','integer','global','global',1510849314,1510849314,'Maximale Größe von Dateien, die im Media-Cache gecached werden (in Bytes)?'),
('MESSAGE_PRIORITY','0','boolean','global','',1240427632,1240427632,'If enabled, messages of high priority are displayed reddish'),
('MESSAGING_SETTINGS','{\"show_only_buddys\":false,\"delete_messages_after_logout\":false,\"timefilter\":\"30d\",\"opennew\":1,\"logout_markreaded\":false,\"openall\":false,\"addsignature\":false,\"save_snd\":true,\"sms_sig\":\"\",\"send_view\":false,\"confirm_reading\":3,\"send_as_email\":false,\"folder\":{\"in\":[\"dummy\"],\"out\":[\"dummy\"]}}','array','user','',1403258015,1403258015,'persönliche Einstellungen Nachrichtenbereich'),
('MIGRATION_START_TIME','1754464707','string','global','Root-Assistent',1754464707,1754464707,'Speichert die Startzeit (Timestamp) der letzten Migration'),
('MIGRATION_START_VERSION','5.5','string','global','Root-Assistent',1754464707,1754464707,'Speichert die jeweilige Stud.IP-Version beim Start der Migration'),
('MVV_ACCESS_ASSIGN_LVGRUPPEN','admin','string','global','mvv',1483462780,1483462780,'Ab welchem Rechtestatus können Veranstaltungen Modulen (LV-Gruppen) zugeordnet werden. Bei Angabe von fakadmin darf nur dieser Zuordnungen vornehmen.'),
('MVV_ALLOW_CREATE_LVGRUPPEN_INDEPENDENTLY','0','boolean','global','mvv',1573236812,1573236812,'Soll das Anlegen von LV-Gruppen unabhängig von bestehenden Modulteilen auf der Verwaltungsseite für LV-Gruppen möglich sein?'),
('MVV_DEFAULT_LANGUAGE','de_DE','string','global','mvv',1754464709,1754464709,'Code der Inhalts-Sprache, die als Original-Sprache der Deskriptoren für Module und Modulteile vorausgewählt ist.'),
('MVV_OVERLAPPING_SHOW_VERSIONS_INSIDE_MULTIPLE_STUDY_COURSES','0','boolean','global','mvv',1591630777,1591630777,'Zeigt als zweite Auswahl bei Mehrfachstudiengängen nur Versionen der dazugehörigen Teilstudiengänge an.'),
('MVV_TEMPLATE_NAME_ABSCHLUSS','','string','global','mvv',1716385357,1716385357,'Template for degrees. Possible placeholders: {{degree_name}}, {{degree_short_name}}. If empty a default name will be displayed.'),
('MVV_TEMPLATE_NAME_FACHBEREICH','{{faculty_short_name}} - {{name}}','string','global','mvv',1716385357,1716385357,'Template for departments. Possible placeholders: {{department_name}}, {{faculty_short_name}}. Used only if the department is not a faculty. If empty the name of the institution will be displayed.'),
('MVV_TEMPLATE_NAME_MODUL','{{module_name}} ({{semester_validity}})','string','global','mvv',1716385357,1716385357,'Template for modules. Possible placeholders: {{module_code}}, {{module_name}}, {{semester_validity}}'),
('MVV_TEMPLATE_NAME_MODULTEIL','','string','global','mvv',1716385357,1716385357,'Template for module parts. Possible placeholders: {{part_number}}, {{part_number_label}}, {{part_name}}, {{teaching_method}}. If empty a default name will be displayed.'),
('MVV_TEMPLATE_NAME_STGTEILABSCHNITTMODUL','{{module_code}} - {{module_name}} ({{semester_validity}})','string','global','mvv',1716385357,1716385357,'Template for modules displayed in the context of a study course. Possible placeholders: {{module_code}}, {{module_name}}, {{semester_validity}}. If empty a default name will be displayed.'),
('MVV_TEMPLATE_NAME_STGTEILVERSION','{{subject_name}} {{credit_points CP}} {{purpose_addition}}{{, version_ordinal_number}} {{version_type}} {{semester_validity}}','string','global','mvv',1716385357,1716385357,'Template for versions of study courses. Possible placeholders: {{subject_name}}, {{credit_points}}, {{purpose_addition}}, {{version_number}}, {{version_type}}, {{version_ordinal_number}}, {{semester_validity}}.'),
('MVV_TEMPLATE_NAME_STUDIENGANG','{{study_course_name}} ({{degree_category}})','string','global','mvv',1716385357,1716385357,'Template for the name of a study course. Possible placeholders: {{study_course_name}}, {{degree_name}}, {{degree_category}}.'),
('MVV_TEMPLATE_NAME_STUDIENGANGTEIL','{{subject_name}} {{credit_points}} CP {{purpose_addition}}','string','global','mvv',1716385357,1716385357,'Template for parts of a study course. Possible placeholders: {{subject_name}}, {{credit_points}}, {{purpose_addition}}.'),
('MY_COURSES_DEFAULT_CYCLE','last','string','global','MeineVeranstaltungen',1462287310,1462287310,'Standardeinstellung für den Semester-Filter, falls noch keine Auswahl getätigt wurde. (all, future, current, last)'),
('MY_COURSES_ENABLE_ALL_SEMESTERS','1','boolean','global','MeineVeranstaltungen',1416496224,1416496224,'Ermöglicht die Anzeige von allen Semestern unter meine Veranstaltungen.'),
('MY_COURSES_ENABLE_STUDYGROUPS','1','boolean','global','MeineVeranstaltungen',1416496224,1416496224,'Sollen Studiengruppen in einem eigenen Bereich angezeigt werden (Neues Navigationelement in Meine Veranstaltungen)?.'),
('MY_COURSES_FORCE_GROUPING','sem_number','string','global','',1293118059,1293118059,'Legt fest, ob die persönliche Veranstaltungsübersicht systemweit zwangsgruppiert werden soll, wenn keine eigene Gruppierung eingestellt ist. Werte: not_grouped, sem_number, sem_tree_id, sem_status, gruppe, dozent_id.'),
('MY_COURSES_GROUPING','','string','user','',1403258015,1403258015,'Gruppierung der Veranstaltungsübersicht'),
('MY_COURSES_OPEN_GROUPS','[]','array','user','',1403258015,1403258015,'geöffnete Gruppen der Veranstaltungsübersicht'),
('MY_COURSES_SELECTED_CYCLE','','string','user','',1698855218,1698855218,'Das auf der Veranstaltungsübersicht für Admins gewählte Semester'),
('MY_COURSES_SELECTED_STGTEIL','','string','user','',1698855218,1698855218,'Der auf der Veranstaltungsübersicht für Admins gewählte Studiengangsteil'),
('MY_COURSES_TYPE_FILTER','','string','user','',1698855218,1698855218,'Der auf der Veranstaltungsübersicht für Admins gewählte Filter auf Veranstaltungstypen'),
('MY_COURSES_VIEW_SETTINGS','{\"regular\":{\"tiled\":false,\"only_new\":false},\"responsive\":{\"tiled\":true,\"only_new\":false}}','array','user','MeineVeranstaltungen',1698855217,1698855217,'Konfiguration der Ansicht \"Meine Veranstaltungen\"'),
('MY_INSTITUTES_DEFAULT','all','string','user','',1403258015,1403258015,'Standard Einrichtung in der Veranstaltungsübersicht für Admins'),
('MY_INSTITUTES_INCLUDE_CHILDREN','1','boolean','user','',1530289048,1530289048,'Sollen untergeordnete Institute mit angezeigt werden in der Veranstaltungsübersicht für Admins?'),
('NEWS_DISABLE_GARBAGE_COLLECT','1','boolean','global','',1123751948,1123751948,'Schaltet den Garbage-Collect für News ein oder aus'),
('NEWS_DISPLAY','2','integer','global','view',1462287310,1462287310,'Legt fest, wie sich News für Anwender präsentieren. (2 zeigt sowohl Autor als auch Zugriffszahlen an. 1 zeigt nur den Autor an. 0 blendet beides für Benutzer aus.'),
('NEWS_ONLY_SYSTEM_ROLES','1','boolean','global','',1656513810,1656513810,'Über diese Option wird die Auswahl der rollenspezifischen Ankündigungen auf Systemrollen begrenzt'),
('NEWS_RSS_EXPORT_ENABLE','1','boolean','global','',0,0,'Schaltet die Möglichkeit des rss-Export von privaten News global ein oder aus'),
('NEW_INDICATOR_THRESHOLD','90','integer','global','global',1448561064,1448561064,'Gibt an, nach wieviel Tagen ein Eintrag als alt angesehen und nicht mehr rot markiert werden soll (0 angeben, um nur das tatsäcliche Alter) zu betrachten.'),
('NOTIFY_ON_WAITLIST_ADVANCE','1','boolean','global','global',1543856103,1543856103,'Versendet Nachrichten an Teilnehmer bei jeder Änderung der Position auf der Warteliste'),
('OERCAMPUS_ENABLED','1','boolean','global','OERCampus',1640797278,1640797278,'Ist der OER Campus aktiviert?'),
('OERCAMPUS_ENABLE_TWILLO','0','boolean','global','OERCampus',1656513810,1656513810,'Soll der Upload zu twillo.de vom OERCampus möglich sein? Folgen Sie dazu der Installationsanleitung.'),
('OERCAMPUS_TWILLO_APPID','','string','global','OERCampus',1656513810,1656513810,'Welche ID hat dieses Stud.IP, wenn es mit twillo.de kommuniziert?'),
('OERCAMPUS_TWILLO_DFNAAIID_DATAFIELD','','string','global','OERCampus',1656513810,1656513810,'Welches Datenfeld eines Nutzers trägt dessen DFN-AAI-ID?'),
('OER_DISABLE_LICENSE','0','boolean','global','OERCampus',1640797278,1640797278,'Sollen die Lizenzen deaktiviert / nicht angezeigt werden?'),
('OER_ENABLE_POST_UPLOAD','1','boolean','global','OERCampus',1686150733,1686150733,'Post-Upload-Dialog nach Hochladen einer Datei erlauben?'),
('OER_ENABLE_SUGGESTIONS','1','boolean','global','OERCampus',1669041528,1669041528,'Studierendenvorschläge erlauben?'),
('OER_OERSI_ONLY_DOWNLOADABLE','1','boolean','global','OERCampus',1669041528,1669041528,'Should the search in OERSI only find downloadable OERs?'),
('OER_PUBLIC_STATUS','autor','string','global','OERCampus',1640797278,1640797278,'Ab welchem Nutzerstatus (nobody, user, autor, tutor, dozent) darf man den Marktplatz sehen?'),
('ONLINE_NAME_FORMAT','full_rev','string','user','',1153814980,1153814980,'Default-Wert für wer-ist-online Namensformatierung'),
('ONLINE_VISIBILITY_DEFAULT','1','boolean','global','privacy',1326799691,1326799691,'Sind Nutzer sichtbar in der Wer ist online-Liste, falls sie nichts anderes eingestellt haben?'),
('OPENGRAPH_ENABLE','1','boolean','global','global',1403258018,1403258018,'De-/Aktiviert OpenGraph-Informationen und deren Abrufen.'),
('PASSWORD_TOOLTIP_TEXT','','i18n','global','Loginseite',1716385357,1716385357,'Text für den Tooltip des Benutzernamens auf der Loginseite'),
('PDF_LOGO','','string','global','global',1311411856,1311411856,'Geben Sie hier den absoluten Pfad auf Ihrem Server (also ohne http) zu einem Logo an, das bei PDF-Exporten im Kopfbereich verwendet wird.'),
('PERSONAL_DETAILS_INFO_TEXT','Einige Ihrer persönlichen Daten werden nicht in Stud.IP verwaltet und können daher hier nicht geändert werden.','i18n','global','global',1698855217,1698855217,'Der Infotext der unter Profil->Persönliche Angaben->Grunddaten angezeigt wird, wenn man nicht die Standard-Auth nutzt.'),
('PERSONAL_NOTIFICATIONS_ACTIVATED','1','boolean','global','privacy',1403258015,1403258015,'Sollen persönliche Benachrichtigungen aktiviert sein?'),
('PERSONAL_NOTIFICATIONS_DEACTIVATED','0','boolean','user','',1754464706,1754464706,'Deaktiviert die persönlichen Benachrichtigungen'),
('PERSONAL_STARTPAGE','0','integer','user','',1403258015,1403258015,'Persönliche Startseite'),
('PLUGINADMIN_DISPLAY_SETTINGS','{\"plugin_filter\":null,\"core_filter\":\"yes\"}','array','user','',1483462779,1483462779,'Speichert die Darstellungseinstellungen der Pluginadministration'),
('PLUS_SETTINGS','[]','array','user','',1436547919,1436547919,'Nutzer Konfiguration für Plusseite'),
('PREVENT_ROOT_FOLDER_UPLOADS_BY_STUDENTS_IN_COURSES','0','boolean','global','files',1754464707,1754464707,'Studierende können im Dateibereich einer Veranstaltung auf der Ebene des Hauptordners keine Dateien hochladen.'),
('PRIVACY_CONTACT','','string','global','privacy',1543856104,1543856104,'Username der Kontaktperson zum Datenschutz'),
('PRIVACY_PERM','autor','string','global','privacy',1543856104,1543856104,'Rechtestufe zum Datenzugriff'),
('PRIVACY_URL','dispatch.php/siteinfo/show/1/7','string','global','privacy',1543856104,1543856104,'URL zur Datenschutzerklärung'),
('PROFILE_LAST_VISIT','0','integer','user','',1403258015,1403258015,'Zeitstempel des letzten Besuchs der Profilseite'),
('PROPOSED_TEACHER_LABELS','','string','global','global',1326799692,1326799692,'Write a list of comma separated possible labels for teachers and tutor here.'),
('QUESTIONNAIRE_AUTOMATED_DATA_PERM','autor','string','global','global',1754464709,1754464709,'Ab welchem Status (autor, tutor, dozent, admin, root) darf man den Fragetyp Automatik in Fragebögen einbauen?'),
('REPORT_BARRIER_MODE','on','string','global','accessibility',1716385357,1716385357,'Einstellungen zum Formular zu Melden einer Barriere (\"on\" = immer an, \"logged-in\" = nur für angemeldete Personen, \"off\" = ausgeschaltet)'),
('RESOURCES_ADDITIONAL_TEXT_ROOM_EXPORT','','string','global','resources',1656513808,1656513808,'Zusatztext, der beim Seriendruck unter jedem Raumplan angezeigt werden soll'),
('RESOURCES_ALLOW_ROOM_PROPERTY_REQUESTS','1','boolean','global','resources',0,1074780851,'Schaltet in der Ressourcenverwaltung die Möglichkeit, im Rahmen einer Anfrage Raumeigenschaften zu wünschen, ein oder aus'),
('RESOURCES_ALLOW_ROOM_REQUESTS','1','boolean','global','resources',0,1100709567,'Schaltet in der Ressourcenverwaltung das System zum Stellen und Bearbeiten von Raumanfragen ein oder aus'),
('RESOURCES_ALLOW_SINGLE_ASSIGN_PERCENTAGE','50','integer','global','resources',0,1100709567,'Wert (in Prozent), ab dem ein Raum mit Einzelbelegungen (statt Serienbelegungen) gefüllt wird, wenn dieser Anteil an möglichen Belegungen bereits durch andere Belegungen zu Überschneidungen führt'),
('RESOURCES_ALLOW_SINGLE_DATE_GROUPING','5','integer','global','resources',0,1100709567,'Anzahl an Einzeltermine, ab der diese als Gruppe zusammengefasst bearbeitet werden'),
('RESOURCES_BOOKING_PLAN_END_HOUR','21:00','string','global','resources',1591630777,1591630777,'The start hour for the default view of the booking plan.'),
('RESOURCES_BOOKING_PLAN_START_HOUR','07:00','string','global','resources',1591630777,1591630777,'The start hour for the default view of the booking plan.'),
('RESOURCES_CONFIRM_PLAN_DRAG_AND_DROP','0','boolean','user','resources',1656513808,1656513808,'Soll beim Verschieben von Buchungen im Belegungsplan eine Sicherheitsabfrage erscheinen?'),
('RESOURCES_DIRECT_ROOM_REQUESTS_ONLY','0','boolean','global','resources',1591630777,1591630777,'Restricts room requests so that only specific rooms can be requested.'),
('RESOURCES_DISPLAY_CURRENT_REQUESTS_IN_OVERVIEW','1','boolean','global','resources',1591630777,1591630777,'Whether to display the list with current requests in the room management overview (true) or not (false).'),
('RESOURCES_ENABLE','0','boolean','global','',0,0,'Enable the Stud.IP resource management module'),
('RESOURCES_ENABLE_BOOKINGSTATUS_COLORING','1','boolean','global','resources',1686150732,1686150732,'Enable the colored presentation of the room booking status of a date'),
('RESOURCES_EXPORT_BOOKINGTYPES_DEFAULT','[0,1,2]','array','global','resources',1656513808,1656513808,'Standardmäßig zu exportierende Belegungstypen'),
('RESOURCES_MAP_SERVICE_URL','https://www.openstreetmap.org/#map=19/LATITUDE/LONGITUDE','string','global','resources',1591630777,1591630777,'The URL for a map service if you wish to use another service instead of OpenStreetMap. The default is: https://www.openstreetmap.org/#map=17/LATITUDE/LONGITUDE (LATITUDE and LONGITUDE are placeholders!)'),
('RESOURCES_MAX_PREPARATION_TIME','120','integer','global','resources',1591630777,1591630777,'The maximum amount of time that can be used for preparation before the actual booking begins. The value represents minutes, not hours!'),
('RESOURCES_MIN_BOOKING_PERMS','autor','string','global','resources',1591630777,1591630777,'The minimum permission level for global booking rights on a resource.'),
('RESOURCES_MIN_BOOKING_TIME','15','integer','global','resources',1591630777,1591630777,'The minimum amount of minutes for the booking of a resource.'),
('RESOURCES_MIN_REQUEST_PERMISSION','','string','global','resources',1591630777,1591630777,'The minimum permission level for creating \"free\" requests that are not bound to a course.'),
('RESOURCES_ROOM_REQUEST_DEFAULT_SEATS','0','integer','global','resources',1557244742,1557244742,'Vorbelegung der Sitzplatzanzahl einer Raumanfrage, falls der Kurs keine max. Teilnehmerzahl hat'),
('RESOURCES_SHOW_PUBLIC_ROOM_PLANS','0','boolean','global','resources',1591630777,1591630777,'Whether to display the list of available public room plans.'),
('RESTRICTED_USER_MANAGEMENT','1','boolean','global','permissions',1240427632,1240427632,'Schränkt Zugriff auf die globale Nutzerverwaltung auf root ein'),
('SCHEDULE_ENABLE','1','boolean','global','modules',1326799692,1326799692,'Schaltet ein oder aus, ob der Stundenplan global verfügbar ist.'),
('SCHEDULE_SETTINGS','{\"glb_start_time\":8,\"glb_end_time\":19,\"glb_days\":{\"1\":1,\"2\":2,\"3\":3,\"4\":4,\"5\":5,\"6\":6,\"0\":0},\"glb_sem\":null,\"converted\":true}','array','user','',1403258015,1403258015,'persönliche Einstellungen Stundenplan'),
('SCM_ENABLE','1','boolean','global','modules',1293118059,1293118059,'Schaltet ein oder aus, ob freie Informationsseiten global verfügbar sind.'),
('SCORE_ENABLE','1','boolean','global','modules',1403258021,1403258021,'Schaltet ein oder aus, ob die Rangliste und die Score-Funktion global verfügbar sind.'),
('SEARCH_VISIBILITY_DEFAULT','1','boolean','global','privacy',1326799691,1326799691,'Sind Nutzer auffindbar in der Personensuche, falls sie nichts anderes eingestellt haben?'),
('SEMESTER_ADMINISTRATION_ENABLE','1','boolean','global','',1219328498,1219328498,'schaltet die Semesterverwaltung ein oder aus'),
('SEMESTER_TIME_SWITCH','4','integer','global','',1140013696,1140013696,'Anzahl der Wochen vor Semesterende zu dem das vorgewählte Semester umspringt'),
('SEM_CREATE_PERM','dozent','string','global','permissions',1170242930,1170242930,'Bestimmt den globalen Nutzerstatus, ab dem Veranstaltungen angelegt werden dürfen (root,admin,dozent)'),
('SEM_TREE_ALLOW_BRANCH_ASSIGN','0','boolean','global','',1222947575,1222947575,'Diese Option beeinflusst die Möglichkeit, Veranstaltungen entweder nur an die Blätter oder überall in der Veranstaltungshierarchie einhängen zu dürfen.'),
('SEM_TREE_SHOW_EMPTY_AREAS_PERM','user','string','global','permissions',1240427632,1240427632,'Bestimmt den globalen Nutzerstatus, ab dem in der Veranstaltungssuche auch Bereiche angezeigt werden, denen keine Veranstaltungen zugewiesen sind.'),
('SEM_VISIBILITY_PERM','root','string','global','permissions',1170242706,1170242706,'Bestimmt den globalen Nutzerstatus, ab dem versteckte Veranstaltungen in der Suche gefunden werden (root,admin,dozent)'),
('SENDFILE_LINK_MODE','normal','string','global','files',1141212096,1141212096,'Format der Downloadlinks: normal=sendfile.php?parameter=x, old=sendfile.php?/parameter=x, rewrite=download/parameter/file.txt'),
('SHOWSEM_ENABLE','1','boolean','user','',1122461027,1122461027,'Einstellung für Nutzer, ob Semesterangaben in der Übersicht \"Meine Veranstaltung\" nach dem Titel der Veranstaltung gemacht werden; Systemdefault'),
('SHOW_ADRESSEES_LIMIT','20','string','global','global',1530289048,1530289048,'Ab wievielen Adressaten dürfen diese aus datenschutzgründen nicht mehr angezeigt werden in einer empfangenen Nachricht?'),
('SHOW_FOLDER_SIZE','1','boolean','global','files',1686150733,1686150733,'SHOW_FOLDER_SIZE gibt an, ob die Anzahl der Objekte (Dateien und Unterordner) in einem Ordner angezeigt werden sollen.'),
('SHOW_TERMS_ON_FIRST_LOGIN','1','boolean','global','global',1510849314,1510849314,'If true, the user has to accept the terms on his first login (this feature makes only sense, if you use disable ENABLE_SELF_REGISTRATION).'),
('SORT_NEWS_BY_CHDATE','false','boolean','global','view',1557244742,1557244742,'Wenn diese Einstellung gesetzt ist werden Ankündigungen nach ihrem letzten Änderungsdatum statt ihrem Erstellungsdatum sortiert angezeigt.'),
('STUDIP_INSTALLATION_ID','demo-installation','string','global','global',1510849314,1510849314,'Unique identifier for installation'),
('STUDIP_SHORT_NAME','Stud.IP','string','global','global',1436546684,1436546684,'Studip Kurzname'),
('STUDYGROUPS_ENABLE','0','boolean','global','studygroups',1257956185,1293118059,'Schaltet ein oder aus, ob die Studiengruppen global verfügbar sind.'),
('STUDYGROUPS_INVISIBLE_ALLOWED','0','boolean','global','studygroups',1403258018,1403258018,'Ermöglicht unsichtbare Studiengruppen'),
('STUDYGROUP_ACCEPTANCE_TEXT','Die Moderatorinnen und Moderatoren der Studiengruppe können Ihren Aufnahmewunsch bestätigen oder ablehnen. Erst nach Bestätigung erhalten Sie vollen Zugriff auf die Gruppe.','string','global','studygroups',1448561064,1448561064,'Text, der angezeigt wird, wenn man sich in eine zugriffsbeschränkte Studiengruppe eintragen möchte'),
('STUDYGROUP_DEFAULT_INST','','string','global','studygroups',1258042892,1258042892,'Die Standardeinrichtung für Studiengruppen kann hier gesetzt werden.'),
('STUDYGROUP_ON_STGTEIL_ENABLE','1','boolean','global','studygroups',1754464709,1754464709,'Are studygroups allowed to get attached to study course parts?'),
('STUDYGROUP_TERMS','Mir ist bekannt, dass ich die Gruppe nicht zu rechtswidrigen Zwecken nutzen darf. Dazu zählen u.a. Urheberrechtsverletzungen, Beleidigungen und andere Persönlichkeitsdelikte.\n\nIch erkläre mich damit einverstanden, daß Administratorinnen und Administratoren die Inhalte der Gruppe zu Kontrollzwecken einsehen dürfen.','i18n','global','studygroups',1257956185,1257956185,'Hier werden die Nutzungsbedinungen der Studiengruppen hinterlegt.'),
('SYSTEMCACHE','{\"type\": \"Studip\\\\Cache\\\\DbCache\", \"config\": []}','array','global','global',1640797278,1640797278,'Typ und Konfiguration des zu verwendenden Systemcaches'),
('SYSTEM_NOTIFICATIONS_PLACEMENT','topcenter','string','user','',1754464706,1754464706,'Wo sollen Systembenachrichtigungen im Fenster angezeigt werden? Gültige Werte sind \"topcenter\" und \"bottomright\"'),
('TERMS_ACCEPTED','0','boolean','user','',1640797279,1640797279,'Die Nutzungsbedingungen wurden akzeptiert'),
('TERMS_CONFIG','{\"compulsory\":false,\"denial_message\":\"\"}','array','global','global',1607702429,1607702429,'In case the terms are not compulsory, user can deny them.if denial_message is not set, a default text is displayed.'),
('TERMS_OF_USE_URL','dispatch.php/siteinfo/show/1/10','string','global','privacy',1754464709,1754464709,'URL zu den Nutzungsbedingungen'),
('TFA_MAX_TRIES','3','integer','global','Zwei-Faktor-Authentifizierung',1573236813,1573236813,'Maximale Anzahl fehlerhafter Versuche innerhalb eines Zeitraums'),
('TFA_MAX_TRIES_TIMESPAN','300','integer','global','Zwei-Faktor-Authentifizierung',1573236813,1573236813,'Zeitraum in Sekunden, nach dem fehlerhafte Versuche vergessen werden'),
('TFA_PERMS','root','string','global','Zwei-Faktor-Authentifizierung',1573236813,1573236813,'Systemrollen für die die Zwei-Faktor-Authentifizierung aktiviert ist (kommaseparierte Liste, mögliche Werte: autor, tutor, dozent, admin, root)'),
('TFA_TEXT_APP','Richten Sie dafür eine geeignete OTP-Authenticator-App ein. Hier finden Sie eine Liste bekannter und kompatibler Apps:\n- [Authy]https://authy.com/\n- [FreeOTP]https://freeotp.github.io/\n- Google Authenticator: [Android]https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2 oder [iOS]https://apps.apple.com/app/google-authenticator/id388497605\n- [LastPass Authenticator]https://lastpass.com/auth/\n- [Microsoft Authenticator]https://www.microsoft.com/authenticator','i18n','global','Zwei-Faktor-Authentifizierung',1656513808,1656513808,'Text, der als Einleitung beim Einrichten der Zwei-Faktor-Authentisierung via App angezeigt wird'),
('TFA_TEXT_INTRODUCTION','Mittels Zwei-Faktor-Authentifizierung können Sie Ihr Konto schützen, indem bei jedem Login ein Token von Ihnen eingegeben werden muss. Dieses Token erhalten Sie entweder per E-Mail oder können es über eine geeignete Authenticator-App erzeugen lassen.','i18n','global','Zwei-Faktor-Authentifizierung',1656513808,1656513808,'Text, der als Einleitung beim Einrichten der Zwei-Faktor-Authentisierung angezeigt wird'),
('TFA_TRUST_DURATION','30','integer','global','Zwei-Faktor-Authentifizierung',1656513809,1656513809,'Dauer, denen Geräte vertraut werden soll in Tagen (0 für dauerhaftes Vertrauen)'),
('TOURS_ENABLE','1','boolean','global','global',1416496223,1416496223,'Aktiviert die Funktionen zum Anbieten von Touren in Stud.IP'),
('UNI_NAME_CLEAN','Stud.IP','string','global','global',1510849314,1510849314,'Name der Stud.IP-Installation bzw. Hochschule.'),
('UPDATE_NEWS_SEEN','0','boolean','global','Root-Assistent',1754464707,1754464707,'Bestätigung, dass die Update-Neuigkeiten gesehen wurden'),
('USERNAME_REGULAR_EXPRESSION','/^([a-zA-Z0-9_@.-]{4,})$/','string','global','global',1510849314,1510849314,'Regulärer Ausdruck für erlaubte Zeichen in Benutzernamen. Das Kommentarfeld kann genutzt werden, um eine Fehlermeldung anzugeben, die zum Beispiel im Registrierungsformular ausgegeben wird, wenn der Ausdruck nicht erfüllt wird.'),
('USERNAME_TOOLTIP_TEXT','','i18n','global','Loginseite',1716385357,1716385357,'Text für den Tooltip des Benutzernamens auf der Loginseite'),
('USER_HIGH_CONTRAST','0','boolean','user','accessibility',1669041528,1669041528,'Schaltet ein barrierefreies Stylesheet mit hohem Kontrast ein oder aus.'),
('USER_VISIBILITY_CHECK','1','boolean','global','global',1510849314,1510849314,'Enable presentation of visibility decision texts for users after first login. see lib/include/header.php and lib/user_visible.inc.php for further info'),
('USER_VISIBILITY_UNKNOWN','1','boolean','global','privacy',1153815901,1153815901,'Sollen Nutzer mit Sichtbarkeit \"unknown\" wie sichtbare behandelt werden?'),
('VIPS_COURSE_GRADES','[]','array','course','',1754464709,1754464709,'Kursbezogenes Schema zur Notenverteilung in Vips'),
('VIPS_EXAM_RESTRICTIONS','0','boolean','global','',1754464709,1754464709,'Sperrt während einer Klausur andere Bereiche von Stud.IP für die Teilnehmenden'),
('VIPS_EXAM_ROOMS','[]','array','global','',1754464709,1754464709,'Zentral verwaltete IP-Adressen für PC-Räume'),
('VIPS_EXAM_TERMS','','string','global','',1754464709,1754464709,'Teilnahmebedingungen, die vor Beginn einer Klausur zu akzeptieren sind'),
('VIRUSSCAN_HOST','127.0.0.1','string','global','files',1686150733,1686150733,'Host des Virenscanners (wird nur verwendet, falls kein Socket eingetragen ist)'),
('VIRUSSCAN_MAX_STREAMLENGTH','26214400','integer','global','files',1686150733,1686150733,'Maximale Streamlänge in Bytes, die beim Virenscanner erlaubt ist'),
('VIRUSSCAN_ON_UPLOAD','0','boolean','global','files',1686150733,1686150733,'Sollen Dateien beim Upload mit ClamAV auf Viren überprüft werden?'),
('VIRUSSCAN_PORT','3310','integer','global','files',1686150733,1686150733,'Port des Virenscanners (wird nur verwendet, falls kein Socket eingetragen ist)'),
('VIRUSSCAN_SOCKET','/var/run/clamav/clamd.ctl','string','global','files',1686150733,1686150733,'Pfad zum Unix Socket (wird statt TCP verwendet, falls etwas eingetragen ist)'),
('VOTE_ENABLE','1','boolean','global','modules',1293118059,1293118059,'Schaltet ein oder aus, ob die Umfragen global verfügbar sind.'),
('WIKI_CREATE_PERMISSION','all','string','course','',1716385357,1716385357,'Status, den es braucht, um neue Wiki-Seiten anzulegen.'),
('WIKI_ENABLE','1','boolean','global','modules',1293118059,1293118059,'Schaltet ein oder aus, ob das Wiki global verfügbar ist.'),
('WIKI_ENABLE_AUTOSAVE','1','boolean','user','wiki',1754464710,1754464710,'Aktiviert das automatische Speichern im Wiki'),
('WIKI_RENAME_PERMISSION','all','string','course','',1716385357,1716385357,'Status, den es braucht, um Wiki-Seiten umzubenennen.'),
('WIKI_STARTPAGE_ID','','string','range','',1716385357,1716385357,'ID der Wiki-Startseite des Wikis.'),
('ZIP_DOWNLOAD_MAX_FILES','400','integer','global','files',1219328498,1219328498,'Die maximale Anzahl an Dateien, die gezippt heruntergeladen werden kann'),
('ZIP_DOWNLOAD_MAX_SIZE','200','integer','global','files',1219328498,1219328498,'Die maximale Größe aller Dateien, die zusammen in einem Zip heruntergeladen werden kann (in Megabytes).'),
('ZIP_UPLOAD_ENABLE','1','boolean','global','files',1130840930,1130840930,'Ermöglicht es, ein Zip Archiv hochzuladen, welches automatisch entpackt wird'),
('ZIP_UPLOAD_MAX_DIRS','10','integer','global','files',1130840962,1130840962,'Die maximale Anzahl an Verzeichnissen, die bei einem Zipupload automatisch entpackt werden'),
('ZIP_UPLOAD_MAX_FILES','100','integer','global','files',1130840930,1130840930,'Die maximale Anzahl an Dateien, die bei einem Zipupload automatisch entpackt werden');
/*!40000 ALTER TABLE `config` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `config_values`
--

LOCK TABLES `config_values` WRITE;
/*!40000 ALTER TABLE `config_values` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `config_values` VALUES
('RESOURCES_ENABLE','studip','1',1530292001,1530292001,''),
('STUDYGROUPS_ENABLE','studip','1',1268739461,1268739461,'Studiengruppen'),
('STUDYGROUP_DEFAULT_INST','studip','ec2e364b28357106c0f8c282733dbe56',1268739461,1268739461,''),
('STUDYGROUP_TERMS','studip','Mir ist bekannt, dass ich die Gruppe nicht zu rechtswidrigen Zwecken nutzen darf. Dazu zählen u.a. Urheberrechtsverletzungen, Beleidigungen und andere Persönlichkeitsdelikte.\r\n\r\nIch erkläre mich damit einverstanden, dass Administratorinnen und Administratoren die Inhalte der Gruppe zu Kontrollzwecken einsehen dürfen.',1268739461,1268739461,'');
/*!40000 ALTER TABLE `config_values` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `consultation_blocks`
--

LOCK TABLES `consultation_blocks` WRITE;
/*!40000 ALTER TABLE `consultation_blocks` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `consultation_blocks` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `consultation_bookings`
--

LOCK TABLES `consultation_bookings` WRITE;
/*!40000 ALTER TABLE `consultation_bookings` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `consultation_bookings` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `consultation_events`
--

LOCK TABLES `consultation_events` WRITE;
/*!40000 ALTER TABLE `consultation_events` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `consultation_events` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `consultation_responsibilities`
--

LOCK TABLES `consultation_responsibilities` WRITE;
/*!40000 ALTER TABLE `consultation_responsibilities` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `consultation_responsibilities` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `consultation_slots`
--

LOCK TABLES `consultation_slots` WRITE;
/*!40000 ALTER TABLE `consultation_slots` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `consultation_slots` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `contact`
--

LOCK TABLES `contact` WRITE;
/*!40000 ALTER TABLE `contact` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `contact` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `contact_group_items`
--

LOCK TABLES `contact_group_items` WRITE;
/*!40000 ALTER TABLE `contact_group_items` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `contact_group_items` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `contact_groups`
--

LOCK TABLES `contact_groups` WRITE;
/*!40000 ALTER TABLE `contact_groups` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `contact_groups` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `content_terms_of_use_entries`
--

LOCK TABLES `content_terms_of_use_entries` WRITE;
/*!40000 ALTER TABLE `content_terms_of_use_entries` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `content_terms_of_use_entries` VALUES
('FREE_LICENSE','Werk mit freier Lizenz',3,'Werke, die unter einer freien Lizenz veröffentlich wurden, d.h. deren Weitergabe und zumeist auch Veränderung ohne Lizenzkosten gestattet ist, dürfen Sie ohne Einschränkungen für den Unterricht zugänglich machen. \n\nTypische Beispiele sind:\n- Open-Access-Publikationen \n- Open Educational Ressources (OER) \n- Werke unter Creative-Commons-Lizenzen (z.B. Wikipedia-Inhalte) \n\nAchtung: Vergewissern Sie sich im Einzelfall, welche Einschränkungen für die Verbreitung und Veränderung die jeweilige Lizenz ggf. enthält.','Das Dokument unterliegt einer freien Lizenz. Sie dürfen es weitergeben und unter Beachtung der Details der Lizenz (s. Angaben im Dokument) verändern und in eigene Werke übernehmen.',0,'cc',0,1499435049,1499435049),
('NO_LICENSE','Veröffentlichte Werke ohne erworbene Lizenz oder gesonderte Erlaubnis',5,'Veröffentlichte Werke, für die keine Lizenz erworben wurde und für die keine gesonderte Erlaubnis vorliegt, dürfen unter den Erlaubnissen des § 60a UrhG für Unterrichtsteilnehmende zugänglich gemacht werden.\n\nEs muss sich dabei um kleine Teile des Gesamtwerkes handeln (z.B. max.  15% eines Buches oder Bildbandes, 5 Minuten bei Musikstücken oder Filmen, Kinofilme erst nach 2 Jahren). Einzelne Abbildungen, Photos oder Artikel aus wissenschaftlichen Zeitschriften dürfen ganz zugänglich gemacht werden, Artikel aus Zeitungen und anderen Zeitschriften allerdings ebenfalls nur zu 10%.\n\nZum Hintergrund: Diese Regelung gilt wegen der Befristung des § 60a UrhG zunächst bis März 2023, eine Einzelmeldung oder Abrechnung über die Hochschule o.ä. ist nicht erforderlich.','Das Dokument wird zur Nutzung im Rahmen dieser Veranstaltung bereitgestellt. Sie dürfen es für private Zwecke herunterladen und archivieren, nicht jedoch ohne Erlaubnis weitergeben.',0,'60a',0,1544006590,1544006609),
('SELFMADE_NONPUB','Selbst verfasstes, nicht publiziertes Werk',2,'Selbst verfasste Werke dürfen Sie ohne Einschränkungen zugänglich machen, wenn Sie die Verwertungsrechte nicht an einen Verlag abgetreten haben. \nTypische Beispiele sind selbst verfasste:\n - Präsentationsfolien, auch mit Text- und Bildzitaten aus fremden Quellen \n- Übungsaufgaben, Musterlösungen \n- Computer-Programme \n- Literaturlisten, Seminarpläne\n - Vorlesungsskripte \n\nWichtig ist die Beachtung des Zitatrechtes: \nWenn Sie Teile fremder Quellen übernehmen, ist das zulässig, solange diese Teile mit Quelle gekennzeicht werden und Gegenstand einer wissenschaftlichen Auseinandersetzung sind.','Das Dokument wird von den Autor/-innen zur Nutzung im Rahmen dieser Veranstaltung bereitgestellt. Sie dürfen es für private Zwecke herunterladen und archivieren, nicht jedoch ohne Erlaubnis weitergeben. Für darüber hinaus gehende Erlaubnisse (Weitergabe, Veränderung) wenden Sie sich an die Autor/-innen oder beachten Sie die Hinweise im Dokument.',0,'own-license',0,1499435049,1499435049),
('UNDEF_LICENSE','Ungeklärte Lizenz',1,'Bitte geben Sie an, welcher Lizenz das hochgeladene Material unterliegt bzw. auf welcher Grundlage Sie es zugänglich machen. Unterbleibt diese Angabe, wird beim Herunterladen auf den ungeklärten Lizenzstatus hingewiesen.','Diese Datei enthält Material mit einer ungeklärten Lizenz. Zu Fragen der Nutzung und Weitergabe wenden Sie sich an die Person, die diese Datei hochgeladen hat.',0,'question-circle',1,1499435049,1516978561),
('WITH_LICENSE','Nutzungserlaubnis oder Lizenz liegt vor',4,'Wenn Sie urheberrechtlich geschützte Werke zugänglich machen wollen und keine der anderen Kategorien passt, benötigen Sie eine Erlaubnis oder kostenpflichtige Lizenz des Inhabers der Verwertungsrechte. Das ist bei publizierten Werken der Verlag, bei nicht publizierten Werken der Autor. \n\nTypische Beispiele sind: \n- Zustimmung von Kollegen oder Studierenden zur Weitergabe von Skripten, Seminararbeiten, Referatsfolien \n- Zustimmung eines Verlages zur Nutzung von Werkteilen für die Lehre \n- Verlags-Erlaubnis zur Nutzung eigener publizierter Werke für die Lehre \n- Erworbene Lizenz für die Weitergabe in Lehrveranstaltung (eine einzelne erworbene Kopie reicht nicht aus!) \n\nAchtung: Campus- oder Nationallizenzen erlauben es nicht, dass Sie ein Werk erneut hochladen und somit selbst verbreiten. Verlinken Sie in diesem Fall direkt auf das Angebot Ihrer Bibliothek o.ä.','Das Dokument wird zur Nutzung im Rahmen dieser Veranstaltung bereitgestellt. Sie dürfen es für private Zwecke herunterladen und archivieren, nicht jedoch ohne Erlaubnis weitergeben.',0,'license',0,1499435049,1499435049);
/*!40000 ALTER TABLE `content_terms_of_use_entries` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `coursememberadmissions`
--

LOCK TABLES `coursememberadmissions` WRITE;
/*!40000 ALTER TABLE `coursememberadmissions` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `coursememberadmissions` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `courseset_factorlist`
--

LOCK TABLES `courseset_factorlist` WRITE;
/*!40000 ALTER TABLE `courseset_factorlist` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `courseset_factorlist` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `courseset_institute`
--

LOCK TABLES `courseset_institute` WRITE;
/*!40000 ALTER TABLE `courseset_institute` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `courseset_institute` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `courseset_rule`
--

LOCK TABLES `courseset_rule` WRITE;
/*!40000 ALTER TABLE `courseset_rule` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `courseset_rule` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `coursesets`
--

LOCK TABLES `coursesets` WRITE;
/*!40000 ALTER TABLE `coursesets` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `coursesets` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `coursewizardsteps`
--

LOCK TABLES `coursewizardsteps` WRITE;
/*!40000 ALTER TABLE `coursewizardsteps` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `coursewizardsteps` VALUES
('3780ba468183b5ed6d7c32fbd73edb02','Erweiterte Grunddaten','AdvancedBasicDataWizardStep',1,0,1483462779,1483462779),
('59405e754a753a21588d63eac75f0ccd','Studienbereiche','StudyAreasWizardStep',2,1,1448561064,1448561064),
('6a7f6dfa33738438d332a85aaeadf230','LVGruppen','LVGroupsWizardStep',3,1,1483462781,1483462781),
('e455df8d296d7dc46a5a27cb9bcc40b0','Grunddaten','BasicDataWizardStep',1,1,1448561064,1448561064),
('ec7b6671be2d47e03e5863e5e5b75e14','Studienbereich oder LV-Gruppe','StudyAreasLVGroupsCombinedWizardStep',3,0,1607702429,1607702429);
/*!40000 ALTER TABLE `coursewizardsteps` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `cronjobs_logs`
--

LOCK TABLES `cronjobs_logs` WRITE;
/*!40000 ALTER TABLE `cronjobs_logs` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `cronjobs_logs` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `cronjobs_schedules`
--

LOCK TABLES `cronjobs_schedules` WRITE;
/*!40000 ALTER TABLE `cronjobs_schedules` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `cronjobs_schedules` VALUES
('3eb6cd006b1d27ab3dfd812c17d90f38','532b3fe76447dd85e10949a6fc5f3aa8',0,NULL,'','{\"cronjobs\":\"1\",\"cronjobs-success\":\"7\",\"cronjobs-error\":\"14\"}',13,2,NULL,NULL,NULL,0,NULL,NULL,0,1403258015,1403258107),
('5e8536eda6d60e42c1068195b812b021','ca6df41746dbd2077d993d3bfddbf10c',1,NULL,NULL,'[]',0,1,NULL,NULL,NULL,0,NULL,NULL,0,1686150733,1686150733),
('69f3cf620a3ee6a3e77a554163685520','81f150b1a22210a1d6fac70220faa831',1,NULL,NULL,'{\"verbose\":false}',41,1,NULL,NULL,NULL,1686181260,NULL,NULL,0,1686150733,1686150733),
('6eef46d414b104b153402be299e16515','2f2713671892bd9624fc27866cfd4630',0,NULL,'','{\"verbose\":\"1\",\"send_messages\":\"1\"}',-30,NULL,NULL,NULL,NULL,0,NULL,NULL,0,1403258015,1403258130),
('81411d712690ab3a82032439dbcdc8c1','9c4ad2a8fe47d07e61475d25f5e539db',0,NULL,NULL,'[]',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,0,1403258017,1403258017),
('8541eec0e512e4e0caa2a5566dd3633b','5ecaecd21cd6dd3712d3d294de51c776',0,NULL,NULL,'null',45,1,NULL,NULL,NULL,0,NULL,NULL,0,1716385357,1716385357),
('b6e232acce27674e496bd2182aab5aaa','43f9da3d9245d0f01b43f744e0b8cdce',0,NULL,NULL,'null',55,0,NULL,NULL,NULL,1530312900,NULL,NULL,0,1530289049,1530290418),
('b913606c32ac082162658086ead45692','42ae67dba8162012eda99b0a017ac16c',0,NULL,NULL,'[]',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,0,1754464709,1754464709),
('cdf293c6c5ae966d87dc5ee723d9880d','823875ed4a4b2e87baca0e5137243d96',0,NULL,'','{\"verbose\":\"1\"}',33,2,NULL,NULL,NULL,1530318780,NULL,NULL,0,1403258015,1530290419),
('dc849ba21c484ffbb82f7ef9edea3d7d','208619e89a59895771c2967076daf59e',0,NULL,NULL,'[]',-30,NULL,NULL,NULL,NULL,0,NULL,NULL,0,1403258015,1403258015),
('dfd35e23a8256fee930e2e748cd53f1d','3428a64935e8c6a5ab5dcf5bf95fe556',0,NULL,NULL,'null',13,3,NULL,NULL,NULL,1530321180,NULL,NULL,0,1403258015,1530290420),
('e9770da600a3399d4e4731885ba00c00','bc8587149433a9cb90eb91df240275d8',1,NULL,NULL,'null',-15,NULL,NULL,NULL,NULL,1754465400,NULL,NULL,0,1754464709,1754464709),
('f048bf3c13bfdb2a2a17ce867903ca0e','d19f37c382fec524b4fd51b3c5a1ada3',0,NULL,NULL,'[]',7,1,NULL,NULL,NULL,0,NULL,NULL,0,1403258015,1403258015);
/*!40000 ALTER TABLE `cronjobs_schedules` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `cronjobs_tasks`
--

LOCK TABLES `cronjobs_tasks` WRITE;
/*!40000 ALTER TABLE `cronjobs_tasks` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `cronjobs_tasks` VALUES
('208619e89a59895771c2967076daf59e','lib/cronjobs/purge_cache.php','PurgeCacheJob',1,0,0,NULL,NULL),
('2f2713671892bd9624fc27866cfd4630','lib/cronjobs/check_admission.php','CheckAdmissionJob',1,0,0,NULL,NULL),
('3428a64935e8c6a5ab5dcf5bf95fe556','lib/cronjobs/session_gc.php','SessionGcJob',1,0,0,NULL,NULL),
('42ae67dba8162012eda99b0a017ac16c','lib/cronjobs/studygroup_expiration.class.php','StudygroupExpirationJob',1,0,0,NULL,NULL),
('43f9da3d9245d0f01b43f744e0b8cdce','lib/classes/FilesSearch/Cronjob.php','FilesSearch\\Cronjob',1,0,2,NULL,NULL),
('532b3fe76447dd85e10949a6fc5f3aa8','lib/cronjobs/cleanup_log.php','CleanupLogJob',1,0,0,NULL,NULL),
('5ecaecd21cd6dd3712d3d294de51c776','lib/cronjobs/import_ilias_testresults.php','ImportIliasTestresults',1,0,1,1716385357,1716385357),
('81f150b1a22210a1d6fac70220faa831','lib/cronjobs/courseware.php','CoursewareCronjob',1,0,1,1686150733,1686150733),
('823875ed4a4b2e87baca0e5137243d96','lib/cronjobs/garbage_collector.php','GarbageCollectorJob',1,0,0,NULL,NULL),
('9c4ad2a8fe47d07e61475d25f5e539db','lib/cronjobs/send_mail_queue.php','SendMailQueueJob',1,0,0,NULL,NULL),
('bc8587149433a9cb90eb91df240275d8','lib/cronjobs/send_massmails.php','SendMassmailsJob',1,0,1,1754464709,1754464709),
('ca6df41746dbd2077d993d3bfddbf10c','lib/cronjobs/remind_oer_upload.php','RemindOerUpload',1,0,0,NULL,NULL),
('d19f37c382fec524b4fd51b3c5a1ada3','lib/cronjobs/send_mail_notifications.php','SendMailNotificationsJob',1,0,0,NULL,NULL);
/*!40000 ALTER TABLE `cronjobs_tasks` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `cw_block_comments`
--

LOCK TABLES `cw_block_comments` WRITE;
/*!40000 ALTER TABLE `cw_block_comments` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `cw_block_comments` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `cw_block_feedbacks`
--

LOCK TABLES `cw_block_feedbacks` WRITE;
/*!40000 ALTER TABLE `cw_block_feedbacks` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `cw_block_feedbacks` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `cw_blocks`
--

LOCK TABLES `cw_blocks` WRITE;
/*!40000 ALTER TABLE `cw_blocks` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `cw_blocks` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `cw_bookmarks`
--

LOCK TABLES `cw_bookmarks` WRITE;
/*!40000 ALTER TABLE `cw_bookmarks` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `cw_bookmarks` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `cw_certificates`
--

LOCK TABLES `cw_certificates` WRITE;
/*!40000 ALTER TABLE `cw_certificates` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `cw_certificates` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `cw_clipboards`
--

LOCK TABLES `cw_clipboards` WRITE;
/*!40000 ALTER TABLE `cw_clipboards` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `cw_clipboards` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `cw_containers`
--

LOCK TABLES `cw_containers` WRITE;
/*!40000 ALTER TABLE `cw_containers` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `cw_containers` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `cw_peer_review_processes`
--

LOCK TABLES `cw_peer_review_processes` WRITE;
/*!40000 ALTER TABLE `cw_peer_review_processes` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `cw_peer_review_processes` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `cw_peer_reviews`
--

LOCK TABLES `cw_peer_reviews` WRITE;
/*!40000 ALTER TABLE `cw_peer_reviews` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `cw_peer_reviews` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `cw_public_links`
--

LOCK TABLES `cw_public_links` WRITE;
/*!40000 ALTER TABLE `cw_public_links` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `cw_public_links` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `cw_structural_element_comments`
--

LOCK TABLES `cw_structural_element_comments` WRITE;
/*!40000 ALTER TABLE `cw_structural_element_comments` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `cw_structural_element_comments` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `cw_structural_element_feedbacks`
--

LOCK TABLES `cw_structural_element_feedbacks` WRITE;
/*!40000 ALTER TABLE `cw_structural_element_feedbacks` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `cw_structural_element_feedbacks` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `cw_structural_elements`
--

LOCK TABLES `cw_structural_elements` WRITE;
/*!40000 ALTER TABLE `cw_structural_elements` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `cw_structural_elements` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `cw_task_feedbacks`
--

LOCK TABLES `cw_task_feedbacks` WRITE;
/*!40000 ALTER TABLE `cw_task_feedbacks` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `cw_task_feedbacks` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `cw_task_groups`
--

LOCK TABLES `cw_task_groups` WRITE;
/*!40000 ALTER TABLE `cw_task_groups` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `cw_task_groups` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `cw_tasks`
--

LOCK TABLES `cw_tasks` WRITE;
/*!40000 ALTER TABLE `cw_tasks` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `cw_tasks` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `cw_templates`
--

LOCK TABLES `cw_templates` WRITE;
/*!40000 ALTER TABLE `cw_templates` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `cw_templates` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `cw_units`
--

LOCK TABLES `cw_units` WRITE;
/*!40000 ALTER TABLE `cw_units` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `cw_units` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `cw_user_data_fields`
--

LOCK TABLES `cw_user_data_fields` WRITE;
/*!40000 ALTER TABLE `cw_user_data_fields` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `cw_user_data_fields` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `cw_user_progresses`
--

LOCK TABLES `cw_user_progresses` WRITE;
/*!40000 ALTER TABLE `cw_user_progresses` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `cw_user_progresses` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `datafields`
--

LOCK TABLES `datafields` WRITE;
/*!40000 ALTER TABLE `datafields` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `datafields` VALUES
('0c63321a8e93b3ccc927611709248e07','default Planungsfarbe','inst',NULL,'admin','root',NULL,0,NULL,NULL,'textline','',0,NULL,0,'Default Farben im Veranstaltungsplaner',0),
('41cda2be71fe9efd6e28b853fc0681f3','zugeordnete Planungsfarbe','sem',NULL,'admin','root',NULL,0,NULL,NULL,'textline','',0,NULL,0,'Zugewiesene Farbe im Veranstaltungsplaner',0),
('69f6485f3c937766866a03d9d642ecbb','zugeordnete Planungsspalte','sem',NULL,'admin','root',NULL,0,NULL,NULL,'textline','',0,NULL,0,'Gibt die zugeordnete Planungsspalte im Veranstaltungsplan an.',0);
/*!40000 ALTER TABLE `datafields` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `datafields_entries`
--

LOCK TABLES `datafields_entries` WRITE;
/*!40000 ALTER TABLE `datafields_entries` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `datafields_entries` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `deputies`
--

LOCK TABLES `deputies` WRITE;
/*!40000 ALTER TABLE `deputies` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `deputies` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `etask_assignment_attempts`
--

LOCK TABLES `etask_assignment_attempts` WRITE;
/*!40000 ALTER TABLE `etask_assignment_attempts` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `etask_assignment_attempts` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `etask_assignment_ranges`
--

LOCK TABLES `etask_assignment_ranges` WRITE;
/*!40000 ALTER TABLE `etask_assignment_ranges` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `etask_assignment_ranges` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `etask_assignments`
--

LOCK TABLES `etask_assignments` WRITE;
/*!40000 ALTER TABLE `etask_assignments` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `etask_assignments` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `etask_blocks`
--

LOCK TABLES `etask_blocks` WRITE;
/*!40000 ALTER TABLE `etask_blocks` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `etask_blocks` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `etask_group_members`
--

LOCK TABLES `etask_group_members` WRITE;
/*!40000 ALTER TABLE `etask_group_members` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `etask_group_members` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `etask_responses`
--

LOCK TABLES `etask_responses` WRITE;
/*!40000 ALTER TABLE `etask_responses` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `etask_responses` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `etask_task_tags`
--

LOCK TABLES `etask_task_tags` WRITE;
/*!40000 ALTER TABLE `etask_task_tags` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `etask_task_tags` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `etask_tasks`
--

LOCK TABLES `etask_tasks` WRITE;
/*!40000 ALTER TABLE `etask_tasks` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `etask_tasks` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `etask_test_tags`
--

LOCK TABLES `etask_test_tags` WRITE;
/*!40000 ALTER TABLE `etask_test_tags` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `etask_test_tags` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `etask_test_tasks`
--

LOCK TABLES `etask_test_tasks` WRITE;
/*!40000 ALTER TABLE `etask_test_tasks` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `etask_test_tasks` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `etask_tests`
--

LOCK TABLES `etask_tests` WRITE;
/*!40000 ALTER TABLE `etask_tests` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `etask_tests` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `ex_termine`
--

LOCK TABLES `ex_termine` WRITE;
/*!40000 ALTER TABLE `ex_termine` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `ex_termine` VALUES
('bcba86d88c4ada8a4b207955c4ec22fb','a07535cf2f8a72df33c12ddfa4b53dde','cli','',1766995200,1767002400,1754464711,1754464711,1,NULL,'fc3c44f257e448e3cd36a88406a8a9c1',''),
('cf200c5191a9142eb11d8d29ea3fce4b','a07535cf2f8a72df33c12ddfa4b53dde','cli','',1766390400,1766397600,1754464711,1754464711,1,NULL,'fc3c44f257e448e3cd36a88406a8a9c1','');
/*!40000 ALTER TABLE `ex_termine` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `extern_pages_configs`
--

LOCK TABLES `extern_pages_configs` WRITE;
/*!40000 ALTER TABLE `extern_pages_configs` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `extern_pages_configs` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `external_users`
--

LOCK TABLES `external_users` WRITE;
/*!40000 ALTER TABLE `external_users` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `external_users` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `fach`
--

LOCK TABLES `fach` WRITE;
/*!40000 ALTER TABLE `fach` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `fach` VALUES
('6b9ac09535885ca55e29dd011e377c0a','Geschichte',NULL,'',NULL,'','',1311416418,1311416418),
('f981c9b42ca72788a09da4a45794a737','Informatik',NULL,'',NULL,'','',1311416397,1311416397);
/*!40000 ALTER TABLE `fach` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `feedback`
--

LOCK TABLES `feedback` WRITE;
/*!40000 ALTER TABLE `feedback` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `feedback` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `feedback_entries`
--

LOCK TABLES `feedback_entries` WRITE;
/*!40000 ALTER TABLE `feedback_entries` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `feedback_entries` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `file_refs`
--

LOCK TABLES `file_refs` WRITE;
/*!40000 ALTER TABLE `file_refs` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `file_refs` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `files`
--

LOCK TABLES `files` WRITE;
/*!40000 ALTER TABLE `files` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `files` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `files_search_attributes`
--

LOCK TABLES `files_search_attributes` WRITE;
/*!40000 ALTER TABLE `files_search_attributes` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `files_search_attributes` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `files_search_index`
--

LOCK TABLES `files_search_index` WRITE;
/*!40000 ALTER TABLE `files_search_index` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `files_search_index` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `folders`
--

LOCK TABLES `folders` WRITE;
/*!40000 ALTER TABLE `folders` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `folders` VALUES
('3cc9006789bceef5d3ea7bed680790b4','76ed43ef286fb55cf9e41beadb484a9f','','110ce78ffefaf1e5f167cd7019b728bf','institute','RootFolder','externe Einrichtung B','','',1510849315,1510849315),
('694cdcef09c2b8e70a7313b028e36fb6','','3cc9006789bceef5d3ea7bed680790b4','110ce78ffefaf1e5f167cd7019b728bf','institute','StandardFolder','Allgemeiner Dateiordner','','Ablage für allgemeine Ordner und Dokumente der Einrichtung',1156516698,1156516698),
('76b822dcc7f1458ae6e144c3c0fb544e','76ed43ef286fb55cf9e41beadb484a9f','','ec2e364b28357106c0f8c282733dbe56','institute','RootFolder','externe Bildungseinrichtungen','','',1510849315,1510849315),
('8373518141e658ade0ff097fc25b2b2c','76ed43ef286fb55cf9e41beadb484a9f','da3c2c2b4ea4c9781dccbae6eade5721','7cb72dab1bf896a0b55c6aa7a70a3a86','course','CourseDateFolder','00. Klausur am Sa, 26.07.25, 10:00 - 14:00','{\"termin_id\":\"42c1555ea5ee40618f5151472354b9f1\"}','',1754464711,1754464711),
('9082368f7e01b24af15178d0d954f4dc','76ed43ef286fb55cf9e41beadb484a9f','','7a4f19a0a2c321ab2b8f7b798881af7c','institute','RootFolder','externe Einrichtung A','','',1510849315,1510849315),
('ad8dc6a6162fb0fe022af4a62a15e309','76ed43ef286fb55cf9e41beadb484a9f','f7fc5ae64d2c453daa9619a820a6467e','a07535cf2f8a72df33c12ddfa4b53dde','course','HomeworkFolder','Hausaufgaben','{\"permission\":\"3\"}','',1343924873,1343924877),
('b58081c411c76814bc8f78425fb2ab81','','9082368f7e01b24af15178d0d954f4dc','7a4f19a0a2c321ab2b8f7b798881af7c','institute','StandardFolder','Allgemeiner Dateiordner','','Ablage für allgemeine Ordner und Dokumente der Einrichtung',1156516698,1156516698),
('bc63814f56ec1bbbba731e07d0074b45','76ed43ef286fb55cf9e41beadb484a9f','','76ed43ef286fb55cf9e41beadb484a9f','user','RootFolder','','[]','',1543858972,1543858972),
('ca002fbae136b07e4df29e0136e3bd32','76ed43ef286fb55cf9e41beadb484a9f','f7fc5ae64d2c453daa9619a820a6467e','a07535cf2f8a72df33c12ddfa4b53dde','course','StandardFolder','Allgemeiner Dateiordner','','Ablage für allgemeine Ordner und Dokumente der Veranstaltung',1343924407,1343924894),
('ca31d5812954d2f2cf252b8a77a332cd','76ed43ef286fb55cf9e41beadb484a9f','f7fc5ae64d2c453daa9619a820a6467e','a07535cf2f8a72df33c12ddfa4b53dde','course','CourseDateFolder','15. Klausur am Sa, 21.02.26, 10:00 - 14:00','{\"termin_id\":\"30b480d6506c4f2d2becceee29254e46\"}','',1754464711,1754464711),
('da3c2c2b4ea4c9781dccbae6eade5721','76ed43ef286fb55cf9e41beadb484a9f','','7cb72dab1bf896a0b55c6aa7a70a3a86','course','RootFolder','Test Studiengruppe','','',1510849315,1510849315),
('dad53cd0f0d9f36817c3c9c7c124bda3','','76b822dcc7f1458ae6e144c3c0fb544e','ec2e364b28357106c0f8c282733dbe56','institute','StandardFolder','Allgemeiner Dateiordner','','Ablage für allgemeine Ordner und Dokumente der Einrichtung',1156516698,1156516698),
('df122112a21812ff4ffcf1965cb48fc3','76ed43ef286fb55cf9e41beadb484a9f','f7fc5ae64d2c453daa9619a820a6467e','a07535cf2f8a72df33c12ddfa4b53dde','course','CourseGroupFolder','Dateiordner der Gruppe: Studierende','{\"group\":\"2f597139a049a768dbf8345a0a0af3de\"}','Ablage für Ordner und Dokumente dieser Gruppe',1343924860,1343924860),
('f7fc5ae64d2c453daa9619a820a6467e','76ed43ef286fb55cf9e41beadb484a9f','','a07535cf2f8a72df33c12ddfa4b53dde','course','RootFolder','Test Lehrveranstaltung','','',1510849315,1510849315);
/*!40000 ALTER TABLE `folders` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `forum_abo_users`
--

LOCK TABLES `forum_abo_users` WRITE;
/*!40000 ALTER TABLE `forum_abo_users` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `forum_abo_users` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `forum_categories`
--

LOCK TABLES `forum_categories` WRITE;
/*!40000 ALTER TABLE `forum_categories` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `forum_categories` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `forum_categories_entries`
--

LOCK TABLES `forum_categories_entries` WRITE;
/*!40000 ALTER TABLE `forum_categories_entries` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `forum_categories_entries` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `forum_entries`
--

LOCK TABLES `forum_entries` WRITE;
/*!40000 ALTER TABLE `forum_entries` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `forum_entries` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `forum_entries_issues`
--

LOCK TABLES `forum_entries_issues` WRITE;
/*!40000 ALTER TABLE `forum_entries_issues` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `forum_entries_issues` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `forum_favorites`
--

LOCK TABLES `forum_favorites` WRITE;
/*!40000 ALTER TABLE `forum_favorites` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `forum_favorites` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `forum_likes`
--

LOCK TABLES `forum_likes` WRITE;
/*!40000 ALTER TABLE `forum_likes` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `forum_likes` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `forum_visits`
--

LOCK TABLES `forum_visits` WRITE;
/*!40000 ALTER TABLE `forum_visits` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `forum_visits` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `global_resource_locks`
--

LOCK TABLES `global_resource_locks` WRITE;
/*!40000 ALTER TABLE `global_resource_locks` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `global_resource_locks` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `grading_definitions`
--

LOCK TABLES `grading_definitions` WRITE;
/*!40000 ALTER TABLE `grading_definitions` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `grading_definitions` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `grading_instances`
--

LOCK TABLES `grading_instances` WRITE;
/*!40000 ALTER TABLE `grading_instances` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `grading_instances` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `help_content`
--

LOCK TABLES `help_content` WRITE;
/*!40000 ALTER TABLE `help_content` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `help_content` VALUES
('014a2106d384c0ca55d9311597029ca0','014a2106d384c0ca55d9311597029ca0','de','Mit der Ressourcensuche können universitäre Ressourcen wie Räume, Gebäude etc. gefunden werden.','resources.php','3.1',0,0,1,'','',1406641688,0,NULL),
('01ad8998268101ad186babf43dac30a4','01ad8998268101ad186babf43dac30a4','de','In den Standard-Vertretungseinstellungen können Dozierende eine Standard-Vertretung festlegen, die alle Veranstaltungen des Dozierenden verwalten und ändern kann.','dispatch.php/settings/deputies','3.1',0,0,1,'','',1406641688,0,NULL),
('0237ea35a203be81e44c979d82ef5ee6','0237ea35a203be81e44c979d82ef5ee6','en','Archived courses to which the user is assigned appear here. Content can no longer be changed, but stored files can be downloaded as zip files.','dispatch.php/my_courses/archive','4.4',0,0,1,'','',1412942388,0,NULL),
('02b4e3ce7b8fe6b3e6a3586d410a51a1','02b4e3ce7b8fe6b3e6a3586d410a51a1','en','This page shows the study groups to which the user is assigned. Study groups are an easy way to collaborate with fellow students, colleagues and others. Each user can create study groups or search for them. The colour coding can be adjusted individually.','dispatch.php/my_studygroups','4.4',0,0,1,'','',1412942388,0,NULL),
('04457f9a66eab07618fe502d470a9711','04457f9a66eab07618fe502d470a9711','de','In der Übersicht finden sich veranstaltungsbezogene Kurz- und Detail-Informationen, Ankündigungen, Termine und Umfragen.','dispatch.php/course/overview','3.1',0,0,1,'','',1406641688,0,NULL),
('0838a96b5678e2fc26be0ee38ae67619','0838a96b5678e2fc26be0ee38ae67619','en','In DoIT!, lecturers have the ability to set different types of tasks, including file uploads, multiple-choice questions, and peer reviewing. The task processing can be limited in time and can be done in groups.','plugins.php/reloadedplugin/show','4.4',0,0,1,'','',1412942388,0,NULL),
('0ad754cc62d1e86e97c1a28dd68ac40c','0ad754cc62d1e86e97c1a28dd68ac40c','en','Here you can find an overview of the dates that have been booked by students.','plugins.php/homepageterminvergabeplugin/show_bookings','4.4',0,0,1,'','',1412942388,0,NULL),
('0c055cc6ae418a96ff3afa9db13098df','0c055cc6ae418a96ff3afa9db13098df','en','You can use the administration features to change the properties of the course at a later date. Under Actions a simulation of the student\'s view is possible.','dispatch.php/course/management','4.4',0,0,1,'','',1412942388,0,NULL),
('0d83ce036f2870f873446230c0118bb7','0d83ce036f2870f873446230c0118bb7','en','The learning module interface makes it possible to provide study units or tests from external programs such as ILIAS and LON-CAPA in Stud.IP.','dispatch.php/course/elearning/show','4.4',0,0,1,'','',1412942388,0,NULL),
('0e816d9428a3bc8a73fb0042fb2da540','0e816d9428a3bc8a73fb0042fb2da540','en','Here the affiliation to user domains can be viewed, but not changed.','dispatch.php/settings/userdomains','4.4',0,0,1,'','',1412942388,0,NULL),
('1058f03da5b6fc6a5ff3a08c9c1fa5f7','1058f03da5b6fc6a5ff3a08c9c1fa5f7','de','Hier können der Veranstaltung weitere Funktionen hinzugefügt werden.','dispatch.php/course/plus','3.1',0,0,1,'','',1406641688,0,NULL),
('1289e991a93dce5a0b4edd678514325e','1289e991a93dce5a0b4edd678514325e','de','Hier können einzelne Inhaltselemente nachträglich aktiviert oder deaktiviert werden. Aktivierte Inhaltselemente fügen neue Funktionen zu Ihrem Profil oder Ihren Einstellungen hinzu. Diese werden meist als neuer Reiter im Menü erscheinen. Wenn Funktionalitäten nicht benötigt werden, können diese hier deaktiviert werden. Die entsprechenden Menüpunkte werden dann ausgeblendet.','dispatch.php/profilemodules','3.1',0,0,1,'','',1406641688,0,NULL),
('142482b4b06a376b2eb4c91d38559a15','142482b4b06a376b2eb4c91d38559a15','de','Freie Gestaltung von Reiternamen und Inhalten durch Lehrende. Es gibt Raum für eigene Informationen, der Name des Reiters ist frei definierbar. Es können beliebig viele Einträge (\"neue Einträge\") hinzugefügt werden.','dispatch.php/course/scm','4.0',1,0,1,'','',0,0,NULL),
('14b77e9e0b7773c92db9e7344a23fcfc','14b77e9e0b7773c92db9e7344a23fcfc','de','Mit der Personensuche können NutzerInnen gefunden werden, solange deren Privatsphäre-Einstellung dies nicht verhindert. Die Suche kann auf bestimmte Veranstaltungen oder Einrichtungen begrenzt werden.','browse.php','3.1',0,0,1,'','',1406641688,0,NULL),
('164f77ab2cb7d38fd1ea20ed725834fd','164f77ab2cb7d38fd1ea20ed725834fd','de','Hier findet sich eine Übersicht über die Termine, die von Studierenden gebucht wurden.','plugins.php/homepageterminvergabeplugin/show_bookings','3.1',0,0,1,'','',1406641688,0,NULL),
('1804e526c2f6794b877a4b2096eaa67a','1804e526c2f6794b877a4b2096eaa67a','en','Blubber is a mixed version of forum and chat where participants\' posts are displayed in real time. Others can be informed about a post by mentioning them in the post by @username or @\'first name last name\'.','plugins.php/blubber/streams/forum','4.4',0,0,1,'','',1412942388,0,NULL),
('194874212676ced8d45e1883da1ad456','194874212676ced8d45e1883da1ad456','de','Das Forum ist eine textbasierte, zeit- und ortsunabhängige Möglichkeit zum Austausch von Fragen, Meinungen und Erfahrungen. Beiträge können abonniert, exportiert, als Favoriten gekennzeichnet und editiert werden. Über die Navigation links können unterschieldiche Ansichten (z.B. Neue Beiträge seit letztem LogIn) gewählt werden.','dispatch.php/course/forum','3.1',0,0,1,'','',1406641688,0,NULL),
('19c2bc232075602bd39efd4b6623d576','19c2bc232075602bd39efd4b6623d576','de','Mit der Studienbereiche-Funktion kann die Veranstaltung einem Studienbereich zugeordnet werden. Die Bearbeitung kann gesperrt sein, wenn Daten aus anderen Systemen (z.B. LSF/ UniVZ) übernommen werden.','dispatch.php/course/study_areas/show','3.1',0,0,1,'','',1406641688,0,NULL),
('19d47b782ac5c8b8b21bd1f94858a0fa','19d47b782ac5c8b8b21bd1f94858a0fa','de','Mit Zugangsberechtigungen (Anmeldeverfahren) lässt sich z.B. durch Passwörter, Zeitsteuerung und TeilnehmerInnenbeschränkung der Zugang zu einer Veranstaltung regulieren.','dispatch.php/course/admission','3.1',0,0,1,'','',1406641688,0,NULL),
('1c61657979ce22a9af023248a617f6b2','1c61657979ce22a9af023248a617f6b2','de','Die Startseite wird nach dem Einloggen angezeigt und kann an persönliche Bedürfnisse mit Hilfe von Widgets angepasst werden.','dispatch.php/start','3.1',0,0,1,'','',1406641688,0,NULL),
('1cb8fd77427ebc092d751eea95454b0a','1cb8fd77427ebc092d751eea95454b0a','en','Here you can edit reference lists and make them visible in the course (by clicking on the \"eye\").','dispatch.php/literature/edit_list','4.4',0,0,1,'','',1412942388,0,NULL),
('1d1323471cf21637f51284f4e6f2d135','1d1323471cf21637f51284f4e6f2d135','de','Detaillierte Informationen über die Veranstaltung werden angezeigt, wie z.B. die Veranstaltungsnummer, Zuordnungen, Lehrende, Tutorinnen und Tutoren etc. In den Detail-Informationen ist unter Aktionen das Eintragen in eine Veranstaltung möglich.','dispatch.php/course/details','3.1',0,0,1,'','',1406641688,0,NULL),
('1da144f3c6f52af0566c343151a6a6ff','1da144f3c6f52af0566c343151a6a6ff','de','In den Benachrichtigungseinstellungen kann ausgewählt werden, bei welchen Änderungen innerhalb einer Veranstaltung eine Benachrichtigung erfolgen soll.','dispatch.php/settings/notification','3.1',0,0,1,'','',1406641688,0,NULL),
('1dca5b0b83f7bca92ec4add50d34b8c5','1dca5b0b83f7bca92ec4add50d34b8c5','de','Hier können der Studiengruppe Mitglieder hinzugefügt und Nachrichten an diese versendet werden.','dispatch.php/course/studygroup/members','3.1',0,0,1,'','',1406641688,0,NULL),
('1ea099717ceb1b401aedcedc89814d9c','1ea099717ceb1b401aedcedc89814d9c','en','The study diary supports the autonomous studying process of the students and is managed independently by them. Inquiries to the lecturers regarding work steps are possible, certain data can be released individually.','plugins.php/lerntagebuchplugin/overview','4.4',0,0,1,'','',1412942388,0,NULL),
('1f216fe42d879c3fcbb582d67e9ad5a2','1f216fe42d879c3fcbb582d67e9ad5a2','en','Here, appointments can be assigned topics or previously entered topics can be taken over and edited.','dispatch.php/course/topics','4.4',0,0,1,'','',1412942388,0,NULL),
('1f6e2f98affbffb1d12904355e9313e5','1f6e2f98affbffb1d12904355e9313e5','de','Diese Seite zeigt die Einrichtungen an, denen die/der NutzerIn zugeordnet ist.','dispatch.php/my_institutes','3.1',0,0,1,'','',1406641688,0,NULL),
('2075fe42f56207fbd153a810188f1beb','2075fe42f56207fbd153a810188f1beb','en','Configuration of the study diary for students and creation of a study diary for lecturers.','plugins.php/lerntagebuchplugin/admin_settings','4.4',0,0,1,'','',1412942388,0,NULL),
('233564d01b8301ebec7ef2fe918d1290','233564d01b8301ebec7ef2fe918d1290','de','Ansicht über die der/ dem Stud.IP-NutzerIn zugeordneten Einrichtungen.','dispatch.php/settings/statusgruppen','3.1',0,0,1,'','',1406641688,0,NULL),
('245ce01d7a0175ab0b977ae822821e9e','245ce01d7a0175ab0b977ae822821e9e','de','Diese Seite bietet die Möglichkeit Stud.IP-Nutzende in das eigene Adressbuch einzutragen und alle bereits im Adressbuch befindlichen Kontakte aufzulisten.','contact.php','3.1',0,0,1,'','',1406641688,0,NULL),
('25255dc15fd0d6260bc1abd1f10aecc5','25255dc15fd0d6260bc1abd1f10aecc5','de','Individuelle persönliche Angaben, wie bspw. E-Mail-Adresse, können auf dieser Seite verändert und angepasst werden. ','dispatch.php/settings/account','3.1',0,0,1,'','',1406641688,0,NULL),
('260ee12fdc7dccb30eca2cc075ef0096','260ee12fdc7dccb30eca2cc075ef0096','en','The schedule settings offer the possibility to be adapted to your own needs.','dispatch.php/settings/calendar','4.4',0,0,1,'','',1412942388,0,NULL),
('2689cecba24e021f05fcece5e4c96057','2689cecba24e021f05fcece5e4c96057','de','Mit der Evaluationen-Funktion lassen sich Befragungen mit Multiple-Choice, Likert- und Freitextfragen für Veranstaltungen, Studiengruppen, das eigene Profil oder Einrichtungen erstellen. Dabei können auch öffentliche Vorlagen anderer Personen verwendet werden. Es werden alle zukünftigen, laufenden und beendeten Evaluationen angezeigt.','admin_evaluation.php','3.1',0,0,1,'','',1406641688,0,NULL),
('27c4d9837cfb1a9a40c079e16daac902','27c4d9837cfb1a9a40c079e16daac902','en','This page offers the possibility to enter Stud.IP users in your own address book and to list all contacts already in the address book.','contact.php','4.4',0,0,1,'','',1412942388,0,NULL),
('29c3bfa01ddbaaa998094d3ee975a06a','29c3bfa01ddbaaa998094d3ee975a06a','de','Der Ablaufplan zeigt Termine, Themen und Räume der Veranstaltung an. Einzelne Termine können bearbeitet werden, z.B. können Themen zu Terminen hinzugefügt werden.','dispatch.php/course/dates','3.1',0,0,1,'','',1406641688,0,NULL),
('2a389c2472656121a76ca4f3b0e137d4','2a389c2472656121a76ca4f3b0e137d4','en','Here you can upload a profile picture.','dispatch.php/settings/avatar','4.4',0,0,1,'','',1412942388,0,NULL),
('2c55eab1f52d6f7d1021880836906f5b','2c55eab1f52d6f7d1021880836906f5b','de','Hier lassen sich Literaturlisten bearbeiten und in der Veranstaltung sichtbar schalten (mit Klick auf das \"Auge\").','dispatch.php/literature/edit_list.php','3.1',0,0,1,'','',1406641688,0,NULL),
('2f1602394a4e31c2e30706f0a0b3112f','2f1602394a4e31c2e30706f0a0b3112f','en','On this page you can see which contacts are currently online. A message can be sent to these people. Clicking on a person\'s name will take you to their profile.','dispatch.php/online','4.4',0,0,1,'','',1412942388,0,NULL),
('2fcc672d91f2627ab5ca48499e8b1617','2fcc672d91f2627ab5ca48499e8b1617','de','Möglichkeit zur Bereitstellung von Vorlesungsaufzeichnungen und Podcasts für Studierende der Veranstaltung (durch Verlinkung auf die Dateien auf dem Medienserver). ','plugins.php/mediacastsplugin/show','3.1',0,0,1,'','',1406641688,0,NULL),
('3318ee99a062079b463e902348ad520e','3318ee99a062079b463e902348ad520e','en','Here, lecturers can create and display announcements for their courses, institutions, and profile page, with the ability to filter the display.','dispatch.php/news/admin_news','4.4',0,0,1,'','',1412942388,0,NULL),
('357bbf06015b2738aae15837f581a07d','357bbf06015b2738aae15837f581a07d','en','Detailed information about the course, e.g. the course number, assignments, lecturers, tutors, etc. is displayed. In the detail information, you can enter a course under Actions.','dispatch.php/course/details','4.4',0,0,1,'','',1412942388,0,NULL),
('35b1860b95854a2533b6ecfbbf04ab71','35b1860b95854a2533b6ecfbbf04ab71','de','Der Stundenplan besteht aus abonnierten Veranstaltungen, die ein- und ausgeblendet sowie in Darstellungsgröße und -form angepasst werden können.','dispatch.php/calendar/schedule','3.1',0,0,1,'','',1406641688,0,NULL),
('3607d6daea679dcd7003e076fdd1660a','3607d6daea679dcd7003e076fdd1660a','en','The list of participants shows a list of the participants of the course. Additional participants can be added, removed, downgraded, promoted or assigned to self-defined groups by lecturers.','dispatch.php/course/members','4.4',0,0,1,'','',1412942388,0,NULL),
('362a67fff2ef7af8cca9f8e20583c9f2','362a67fff2ef7af8cca9f8e20583c9f2','en','Contacts from the address book can be displayed sorted according to the groups here.','???','3.1',0,0,1,'','',1412942388,0,NULL),
('38d1a86517eb6cc195b2e921270c3035','38d1a86517eb6cc195b2e921270c3035','en','The group calendar provides an overview of course dates and personalized additional dates for that course.','plugins.php/gruppenkalenderplugin/show','4.4',0,0,1,'','',1412942388,0,NULL),
('394a45f94e1d84d3744027a5a69d9e3e','394a45f94e1d84d3744027a5a69d9e3e','de','Auf dieser Seite lässt sich einsehen, welche Kontakte gerade online sind. Diesen Personen kann eine Nachricht geschickt werden. Das Klicken auf den Namen einer Person leitet zu deren Profil weiter.','dispatch.php/online','3.1',0,0,1,'','',1406641688,0,NULL),
('3b7a4c04017fef2984ee029610194f26','3b7a4c04017fef2984ee029610194f26','en','The settings of the messaging system offer the possibility to forward the messages received in Stud.IP to your email address.','dispatch.php/settings/messaging','4.4',0,0,1,'','',1412942388,0,NULL),
('3d040e95a8c29e733a8d5439ee9f5b59','3d040e95a8c29e733a8d5439ee9f5b59','en','The name, function and access restriction of the study group can be edited here.','dispatch.php/course/studygroup/edit','4.4',0,0,1,'','',1412942388,0,NULL),
('4151003175042b71bea3529e5adc5a9e','4151003175042b71bea3529e5adc5a9e','de','Mit der Terminvergabe können Termine für Sprechstunden, Prüfungen usw. angelegt werden, in die sich Studierende selbst eintragen können.','plugins.php/homepageterminvergabeplugin/showadmin','3.1',0,0,1,'','',1406641688,0,NULL),
('42060187921376807f90e52fad5f9822','42060187921376807f90e52fad5f9822','en','With the Surveys and Tests function, you can create (time-controlled) surveys or individual multiple/single-choice questions for courses, study groups or the profile.','admin_vote.php','4.4',0,0,1,'','',1412942388,0,NULL),
('437c83a27473ef8139b47198101067fb','437c83a27473ef8139b47198101067fb','de','Hier erscheinen archivierte Veranstaltungen, denen der Nutzer zugeordnet ist. Inhalte können nicht mehr verändert, jedoch hinterlegte Dateien als zip-Datei heruntergeladen werden.','dispatch.php/my_courses/archive','3.1',0,0,1,'','',1406641688,0,NULL),
('438c4456f85afec29fd9f47c111136c1','438c4456f85afec29fd9f47c111136c1','en','This page shows the institutions that the user is assigned to.','dispatch.php/my_institutes','4.4',0,0,1,'','',1412942388,0,NULL),
('43df8e33145c25eb6d941e4e845ada24','43df8e33145c25eb6d941e4e845ada24','en','In the notification settings you can select which changes within a course you want to be notified for.','dispatch.php/settings/notification','4.4',0,0,1,'','',1412942388,0,NULL),
('440e50f7fcc825368aa9026273d2cd0d','440e50f7fcc825368aa9026273d2cd0d','en','The timetable consists of courses you have subscribed to, which can be shown and hidden as well as adjusted in display size and form.','dispatch.php/calendar/schedule','4.4',0,0,1,'','',1412942388,0,NULL),
('44edb997707d1458cbf8a3f8f316b908','44edb997707d1458cbf8a3f8f316b908','en','The reference page offers teachers the possibility to create reference lists or to import them from reference management programs. These lists can be copied into courses and made visible. Depending on the connection, the actual book inventory of the university can be searched.','dispatch.php/course/literature','4.4',0,0,1,'','',1412942388,0,NULL),
('462f1447b1a8a93ab7bdb2524f968b1a','462f1447b1a8a93ab7bdb2524f968b1a','de','Hier kann die Zugehörigkeit zu Nutzerdomänen eingesehen, aber nicht geändert werden.','dispatch.php/settings/userdomains','3.1',0,0,1,'','',1406641688,0,NULL),
('4698cafeb9823735c50fd3a1745950ba','4698cafeb9823735c50fd3a1745950ba','de','In den Grunddaten können Titel, Beschreibung, Dozierende etc. geändert werden. Die Bearbeitung kann teilweise gesperrt sein, wenn Daten aus anderen Systemen (z.B. LSF/ UniVZ) übernommen werden.','dispatch.php/course/basicdata/view','3.1',0,0,1,'','',1406641688,0,NULL),
('4e14c94cda99e2ef6462f7fef06d9c91','4e14c94cda99e2ef6462f7fef06d9c91','en','With access authorisation (enrolment procedure), access to a course can be regulated e.g. by means of passwords, time control and participant restrictions.','dispatch.php/course/admission','4.4',0,0,1,'','',1412942388,0,NULL),
('4e60dd9635f3d3fddecc78e0d1f646c7','4e60dd9635f3d3fddecc78e0d1f646c7','de','Unter \"Studiendaten\" können manuell zusätzliche Studiengänge und Einrichtungen hinzugefügt werden, wenn sie nicht automatisch aus einem externen System (z.B. LSF/ UniVZ) übernommen wurden.','dispatch.php/settings/studies','3.1',0,0,1,'','',1406641688,0,NULL),
('4f9d79fe88e81486b8c1f192d70232d5','4f9d79fe88e81486b8c1f192d70232d5','de','Mit der Einrichtungssuche können Einrichtungen über ein freies Suchfeld oder den Einrichtungsbaum gefunden werden.','institut_browse.php','3.1',0,0,1,'','',1406641688,0,NULL),
('51a0399250de6365619c961ec3669ad3','51a0399250de6365619c961ec3669ad3','en','Blubber is a mixture of forum and chat. Messages are displayed in the public stream. Other users can be informed about a post by mentioning them by @username or @\'firstname surname\' in the post.','plugins.php/blubber/streams/profile','4.4',0,0,1,'','',1412942388,0,NULL),
('51b98d659590e1e37dae5e5e5cc028bb','51b98d659590e1e37dae5e5e5cc028bb','en','File management provides the ability to upload, manage, and download personal files that are not visible to others.','dispatch.php/document/files','4.4',0,0,1,'','',1412942388,0,NULL),
('5475d65b07fdaf5f234bf6eed3d5e4a9','5475d65b07fdaf5f234bf6eed3d5e4a9','en','The evaluation function can be used to create surveys with multiple-choice, and free text questions for courses, study groups, your own profile or institutions. Other people\'s public templates can also be used. All future, current and completed evaluations are displayed.','admin_evaluation.php','4.4',0,0,1,'','',1412942388,0,NULL),
('55499281ce1a4757f17aaf73faa072ea','55499281ce1a4757f17aaf73faa072ea','de','Auf dieser Seite können sie sich vor dem Archivieren vergewissern, das die richtige(n) Veranstaltunge(n) zum Archivieren ausgewählt wurden.','dispatch.php/course/archive/confirm','4.0',1,0,1,'','',0,0,NULL),
('57f1b29d3c1a558f5cc799c1aade7f14','57f1b29d3c1a558f5cc799c1aade7f14','en','Here, contact groups or the entire address book can be exported in order to import them into an external program.','contact_export.php','4.4',0,0,1,'','',1412942388,0,NULL),
('595c46d86f681f7da4bd2fae780db618','595c46d86f681f7da4bd2fae780db618','de','Wählen Sie das gewünschte System und anschließend das Lernmodul/ den Test aus. Schreibrechte bestimmen, wer zukünftig das Lernmodul bearbeiten darf. In der Sidebar befindet sich die Option \"Zuordnungen aktualisieren\", um geänderte Inhalte z.B. im ILIAS Kurs zu Stud.IP zu übertragen.','dispatch.php/course/elearning/edit','3.1',0,0,1,'','',1406641688,0,NULL),
('5a90d1219dbeb07c124156592fb5d877','5a90d1219dbeb07c124156592fb5d877','de','In den allgemeinen Einstellungen können verschiedene Anzeigeoptionen und Benachrichtigungsfunktionen ausgewählt und verändert werden.','dispatch.php/settings/general','3.1',0,0,1,'','',1406641688,0,NULL),
('5ae72abc0822570bfe839e3ee24f0c81','5ae72abc0822570bfe839e3ee24f0c81','en','Date allocation can be used to create appointments for consultation hours, exams, etc. in which students can enter themselves.','plugins.php/homepageterminvergabeplugin/showadmin','4.4',0,0,1,'','',1412942388,0,NULL),
('5fab81bbd1e19949f304df08ea21ca1b','5fab81bbd1e19949f304df08ea21ca1b','de','Mit der Bild-Hochladen-Funktion lässt sich das Bild der Veranstaltung ändern, was Studierenden bei der Unterscheidung von Veranstaltungen auf der Meine-Veranstaltungen-Seite helfen kann.','dispatch.php/course/avatar','3.1',0,0,1,'','',1406641688,0,NULL),
('60b6caf75d0004dfdb0a1adfd66027ed','60b6caf75d0004dfdb0a1adfd66027ed','de','Hier können Dozierende Ankündigungen für ihre Veranstaltungen, Einrichtungen und ihre Profilseite erstellen und anzeigen, wobei die Anzeige gefiltert werden kann.','dispatch.php/news/admin_news','3.1',0,0,1,'','',1406641688,0,NULL),
('615c1887f0ee080043f133681ebf0def','615c1887f0ee080043f133681ebf0def','en','Titles, descriptions, lecturers, etc. can be changed in the basic data. Editing can be partially blocked if data is transferred from other systems (for example, LSF/ UniVZ).','dispatch.php/course/basicdata/view','4.4',0,0,1,'','',1412942388,0,NULL),
('633dab120ce3969c42f33aeb3a59fcc1','633dab120ce3969c42f33aeb3a59fcc1','de','Der Gruppenkalender bietet eine Übersicht über Veranstaltungstermine und personalisierte Zusatztermine für diese Veranstaltung. ','plugins.php/gruppenkalenderplugin/show','3.1',0,0,1,'','',1406641688,0,NULL),
('63c2ecb12f30816aef0fb203eab4f40a','63c2ecb12f30816aef0fb203eab4f40a','de','Hier können Termine angelegt und bearbeitet werden.','plugins.php/homepageterminvergabeplugin/show_category','3.1',0,0,1,'','',1406641688,0,NULL),
('6529fd70b461fa4a9242e874fbf2a5d3','6529fd70b461fa4a9242e874fbf2a5d3','de','In DoIT! haben Lehrende die Möglichkeit, verschiedene Arten von Aufgaben zu stellen, inklusive Hochladen von Dateien, Multiple-Choice-Fragen und Peer Reviewing. Die Aufgabenbearbeitung kann zeitlich befristet werden und wahlweise in Gruppen erfolgen.','plugins.php/reloadedplugin/show','3.1',0,0,1,'','',1406641688,0,NULL),
('690e6eff3e83a5f372ec99fc49cafeb2','690e6eff3e83a5f372ec99fc49cafeb2','de','Blubbern ist das Stud.IP Echtzeitforum, eine Mischform aus Forum und Chat. Andere können über einen Beitrag informiert werden, indem sie per @benutzername oder @\"Vorname Nachname\" im Beitrag erwähnt werden. Texte lassen sich formatieren und durch Smileys ergänzen.','plugins.php/blubber/streams/global','3.1',0,0,1,'','',1406641688,0,NULL),
('6acc653cfabd3a0d4433ff0ab417bf6a','6acc653cfabd3a0d4433ff0ab417bf6a','de','Übersicht über gesendete, systeminterne Nachrichten, welche mit selbstgewählten Schlüsselwörtern (sog. Tags) versehen werden können, um sie später leichter wieder auffinden zu können. ','dispatch.php/messages/sent','3.1',0,0,1,'','',1406641688,0,NULL),
('6b331f5cc2176daba82a0cc71aaa576f','6b331f5cc2176daba82a0cc71aaa576f','en','On this page you can sort contacts into self-defined groups.','contact_statusgruppen.php','4.4',0,0,1,'','',1412942388,0,NULL),
('70274c459a69e34bbf520e690a8e472b','70274c459a69e34bbf520e690a8e472b','de','Mit der Zeiten/Räume-Funktion können die Semester-, Termin- und Raumangaben der Veranstaltung geändert werden. Die Bearbeitung kann gesperrt sein, wenn Daten aus anderen Systemen (z.B. LSF/ UniVZ) übernommen werden.','dispatch.php/course/timesrooms','3.1',0,0,1,'','',1406641688,0,NULL),
('707b0db0e45fc3bab04be7eff38c1d32','707b0db0e45fc3bab04be7eff38c1d32','de','Die Literaturseite bietet Lehrenden die Möglichkeit, Literaturlisten zu erstellen oder aus Literaturverwaltungsprogrammen zu importieren. Diese Listen können in Lehrveranstaltungen kopiert und sichtbar geschaltet werden. Je nach Anbindung kann im tatsächlichen Buchbestand der Hochschule recherchiert werden. ','dispatch.php/course/literature','3.1',0,0,1,'','',1406641688,0,NULL),
('72cec29d985f3e6d7df2b5fabb7fe666','72cec29d985f3e6d7df2b5fabb7fe666','de','Konfiguation des Lerntagebuchs für Studierende und Anlegen eines Lerntagebuchs für die Dozierenden.','plugins.php/lerntagebuchplugin/admin_settings','3.1',0,0,1,'','',1406641688,0,NULL),
('7465a4aeedb6a320d3455cf9ad0bebd0','7465a4aeedb6a320d3455cf9ad0bebd0','en','Possibility of providing lecture recordings and podcasts for students of the course (by linking to the files on the media server).','plugins.php/mediacastsplugin/show','4.4',0,0,1,'','',1412942388,0,NULL),
('74863847eec53a3d4c8264d8de526be8','74863847eec53a3d4c8264d8de526be8','de','Mit der Archivsuche können Veranstaltungen gefunden werden, die bereits archiviert wurden.','dispatch.php/search/archive','3.1',0,0,1,'','',1406641688,0,NULL),
('74c1da86f33f5adfb43e10220bfad238','74c1da86f33f5adfb43e10220bfad238','de','Die Veranstaltungsseite zeigt alle abonnierten Veranstaltungen (standardmäßig nur die der letzten beiden Semester), alle abonnierten Studiengruppen sowie alle Einrichtungen, denen man zugeordnet wurde. Die Anzeige lässt sich über Farbgruppierungen, Semesterfilter usw. anpassen.','dispatch.php/my_courses','3.1',0,0,1,'','',1406641688,0,NULL),
('752d441cd321b05c55c8a5d9aa48ddce','752d441cd321b05c55c8a5d9aa48ddce','de','Auf dieser Seite können Kontakte aus dem Adressbuch in selbstdefinierte Gruppen sortiert werden.','contact_statusgruppen.php','3.1',0,0,1,'','',1406641688,0,NULL),
('76195b21d485823fd7ca2fd499131c12','76195b21d485823fd7ca2fd499131c12','en','Here you can add and edit dates.','plugins.php/homepageterminvergabeplugin/show_category','4.4',0,0,1,'','',1412942388,0,NULL),
('7bf322a6c5f13db67e047b7afae83e58','7bf322a6c5f13db67e047b7afae83e58','en','By exporting, data about courses and co-workers can be exported into the following formats: RTF, TXT, CSV, PDF, HTML and XML.','export.php','4.4',0,0,1,'','',1412942388,0,NULL),
('7cb7026818c4b90935009d0548300674','7cb7026818c4b90935009d0548300674','en','A custom Blubber stream can be created here. It always consists of a collection of posts from selected courses, contact groups and keywords, which can be further restricted by filtering. The new user-defined stream can be found after clicking on the Save button in the navigation under Global Stream.','plugins.php/blubber/streams/edit','4.4',0,0,1,'','',1412942388,0,NULL),
('7d40379f54250b550065e062d71e8fd8','7d40379f54250b550065e062d71e8fd8','en','With the archive search you can search for courses that have already been archived.','dispatch.php/search/archive','4.4',0,0,1,'','',1412942388,0,NULL),
('7ebdd278d06f9fc1d2659a54bb3171c1','7ebdd278d06f9fc1d2659a54bb3171c1','de','Die Rangliste sortiert die Stud.IP-Nutzenden absteigend anhand ihrer Punktzahl. Die Punktzahl wächst mit den Aktivitäten in Stud.IP und repräsentiert so die Erfahrung der Nutzenden mit dem System. Indem das Kästchen links mit einem Haken versehen wird, wird der eigene Wert für andere NutzerInnen in der Rangliste sichtbar gemacht. In der Grundeinstellung ist der eigene Wert nicht öffentlich sichtbar.','dispatch.php/score','3.1',0,0,1,'','',1406641688,0,NULL),
('7edc08f2f7b0786ca036f8c448441e07','7edc08f2f7b0786ca036f8c448441e07','en','The Wiki enables a common, asynchronous creation and editing of texts. Texts can be formatted and linked so that a branched reference guide is created.','wiki.php','4.4',0,0,1,'','',1412942388,0,NULL),
('7f4a1f5e3dfe2a459cf0eb357667d91c','7f4a1f5e3dfe2a459cf0eb357667d91c','de','Mit den Verwaltungsfunktionen lassen sich die Eigenschaften der Veranstaltung nachträglich ändern. Unter Aktionen ist die Simulation der Studierendenansicht möglich.','dispatch.php/course/management','3.1',0,0,1,'','',1406641688,0,NULL),
('80286432bf17df20e5f11f86b421b0a7','80286432bf17df20e5f11f86b421b0a7','en','The forum is a text-based, time- and location-independent platform for the exchange of questions, opinions and experiences. Contributions can be subscribed to, exported, marked as favourites and edited. The navigation on the left allows you to select different views (e.g. New posts since last login).','dispatch.php/course/forum','4.4',0,0,1,'','',1412942388,0,NULL),
('82537b14dd3714ec9636124ed5af3272','82537b14dd3714ec9636124ed5af3272','de','Die Profilseite ermöglicht die Änderung der eigenen persönliche Angaben inkl. Profilbild und Kategorien. Ähnlich wie in Facebook können Kommentare hinterlassen werden. Das Profil von Lehrenden enthält Sprechstunden und Raumangaben. Daneben bietet die Seite die Verwaltung eigener Dateien.','dispatch.php/profile','3.1',0,0,1,'','',1406641688,0,NULL),
('82a17a5f19d211268b1fa90a1ebe0894','82a17a5f19d211268b1fa90a1ebe0894','de','Hier kann eine neue Studiengruppe angelegt werden. Jede/r Stud.IP-NutzerIn kann Studiengruppen anlegen und nach eigenen Bedürfnissen konfigurieren.','dispatch.php/course/studygroup/new','3.1',0,0,1,'','',1406641688,0,NULL),
('83fd70727605c485a0d8f2c5ef94289b','83fd70727605c485a0d8f2c5ef94289b','en','Here you can enter predefined information about yourself, that should appear on your profile page.','dispatch.php/settings/details','4.4',0,0,1,'','',1412942388,0,NULL),
('845d1ce67a62d376ec26c8ffbb22d492','845d1ce67a62d376ec26c8ffbb22d492','de','Die Einstellungen des Nachrichtensystems bieten die Möglichkeit z.B. eine Weiterleitung der in Stud.IP empfangenen Nachrichten an die E-Mail-Adresse zu veranlassen.','dispatch.php/settings/messaging','3.1',0,0,1,'','',1406641688,0,NULL),
('852991dc733639dd2df05fb627abf3db','852991dc733639dd2df05fb627abf3db','en','Here you can add further features to the course.','dispatch.php/course/plus','4.4',0,0,1,'','',1412942388,0,NULL),
('85c000e33732c5596d198776cb884860','85c000e33732c5596d198776cb884860','en','In the default substitution settings, lecturers can specify a default substitution that can manage and change all of the lecturer\'s courses.','dispatch.php/settings/deputies','4.4',0,0,1,'','',1412942388,0,NULL),
('85c709de75085bd56a739e4e8ac6fcad','85c709de75085bd56a739e4e8ac6fcad','en','The time/room feature can be used to change the semester, date and room details of the course. Editing can be blocked if data is transferred from other systems (e.g. LSF/ UniVZ).','dispatch.php/course/timesrooms','4.4',0,0,1,'','',1412942388,0,NULL),
('85cbaa1648af330cc4420b57df4be29c','85cbaa1648af330cc4420b57df4be29c','de','Die Einstellungen des Terminkalenders bieten die Möglichkeit, diesen an eigene Bedürfnisse anzupassen.','dispatch.php/settings/calendar','3.1',0,0,1,'','',1406641688,0,NULL),
('87489a40097e5c26f1d1349c072610de','87489a40097e5c26f1d1349c072610de','de','Mit der Veranstaltungssuche können Veranstaltungen, Studiengruppen usw. in verschiedenen Semestern und nach verschiedenen Suchkriterien (siehe \"Erweiterte Suche anzeigen\"in der Sidebar) gefunden werden. Das aktuelle Semester ist vorgewählt.','dispatch.php/search/courses','3.1',0,0,1,'','',1406641688,0,NULL),
('8a1d7d04c70d93be44e8fe6a8e8c3443','8a1d7d04c70d93be44e8fe6a8e8c3443','de','Das Lerntagebuch unterstützt den selbstgesteuerten Lernprozess der Studierenden und wird von ihnen selbstständig geführt. Anfragen zu Arbeitsschritten an die Dozierenden sind möglich, bestimmte Daten können individualisiert freigegeben werden.','plugins.php/lerntagebuchplugin/overview','3.1',0,0,1,'','',1406641688,0,NULL),
('8a32ca4e602a68307d4ae6ae51fa667e','8a32ca4e602a68307d4ae6ae51fa667e','en','With the institute search, institutions can be found via a free search field or the facility tree.','institut_browse.php','4.4',0,0,1,'','',1412942388,0,NULL),
('8ad364363acd415631226d5574d5592a','8ad364363acd415631226d5574d5592a','en','On this page you can enter self-defined information about yourself, which should appear on the profile page.','dispatch.php/settings/categories','4.4',0,0,1,'','',1412942388,0,NULL),
('8b690f942bf0cc0322e5bea0f1b9abed','8b690f942bf0cc0322e5bea0f1b9abed','en','Select the desired system and then the learning module/test. Writing permissions determine who can edit the learning module in the future. In the sidebar you will find the option \"Update assignments\" in order to transfer changed contents e.g. in the ILIAS course to Stud.IP.','dispatch.php/course/elearning/edit','4.4',0,0,1,'','',1412942388,0,NULL),
('8c2fc90bd8175e6d598f895944a8ddc2','8c2fc90bd8175e6d598f895944a8ddc2','en','The attendance list shows all course appointments (meeting, lecture, exercise, internship) of the schedule and allows students to be entered by the lecturers in Stud.IP as well as exporting the list to an overview or as a basis for handwritten entries.','participantsattendanceplugin/show','4.4',0,0,1,'','',1412942388,0,NULL),
('8c3067596811d3c6857d253299e01f6f','8c3067596811d3c6857d253299e01f6f','en','The schedule shows dates, topics and rooms of the course. Individual dates can be edited, for example, topics can be added to dates.','dispatch.php/course/dates','4.4',0,0,1,'','',1412942388,0,NULL),
('8dd3b80d9f95218d67edc3cb570559ff','8dd3b80d9f95218d67edc3cb570559ff','de','Hier lassen sich Literaturlisten bearbeiten und in der Veranstaltung sichtbar schalten (mit Klick auf das \"Auge\").','dispatch.php/literature/edit_list','3.1',0,0,1,'','',1406641688,0,NULL),
('90ffbd715843b02b3961907f81caf208','90ffbd715843b02b3961907f81caf208','en','The score list sorts the Stud.IP users in descending order according to their score. The number of points increases with the activities in Stud.IP and thus represents the experience of the users with the system. By ticking the box on the left, the own value is made visible to other users in the ranking. By default, your own value is not visible to the public.','dispatch.php/score','4.4',0,0,1,'','',1412942388,0,NULL),
('91d6f451c3ef8d8352a076773b0a19ee','91d6f451c3ef8d8352a076773b0a19ee','en','The courses page shows all subscribed courses (by default only those of the last two semesters), all subscribed study groups and all institutions to which you have been assigned. The display can be adjusted via colour codes, semester filters, etc.','dispatch.php/my_courses','4.4',0,0,1,'','',1412942388,0,NULL),
('94a193baa212abbc9004280a1498e724','94a193baa212abbc9004280a1498e724','de','Hier können Kontaktgruppen oder das gesamte Adressbuch exportiert werden, um sie in einem externen Programm importieren zu können.','contact_export.php','3.1',0,0,1,'','',1406641688,0,NULL),
('95ff3a2a68dae73bcb14a4a538a8e4b5','95ff3a2a68dae73bcb14a4a538a8e4b5','de','Blubbern ist eine Mischform aus Forum und Chat, bei dem Beiträge der Teilnehmenden in Echtzeit angezeigt werden. Andere können über einen Beitrag informiert werden, indem sie per @benutzername oder @\"Vorname Nachname\" im Beitrag erwähnt werden.','plugins.php/blubber/streams/forum','3.1',0,0,1,'','',1406641688,0,NULL),
('960d7bafb618853eced1b1b42a7dd412','960d7bafb618853eced1b1b42a7dd412','en','This page shows all study groups that exist in Stud.IP. Study groups are an easy way to collaborate with fellow students, colleagues and others. Each user can create study groups or search for them.','dispatch.php/studygroup/browse','4.4',0,0,1,'','',1412942388,0,NULL),
('970ebdf39ad5ca89083a52723c5c35f5','970ebdf39ad5ca89083a52723c5c35f5','en','Under \"Study details\", additional study programmes and institutions can be added manually if they have not been transferred automatically from an external system (e.g. LSF/ UniVZ).','dispatch.php/settings/studies','4.4',0,0,1,'','',1412942388,0,NULL),
('a1e3da35edc9b605f670e9c7f5019888','a1e3da35edc9b605f670e9c7f5019888','en','With the course search you can find courses, study groups etc. in different semesters and according to different search criteria (see \"Show advanced search\" in the sidebar). The current semester is preselected.','dispatch.php/search/courses','4.4',0,0,1,'','',1412942388,0,NULL),
('a1ea37130799a59f7774473f1a681141','a1ea37130799a59f7774473f1a681141','de','Die Lernmodulschnittstelle ermöglicht es, Selbstlerneinheiten oder Tests aus externen Programmen wie ILIAS und LON-CAPA in Stud.IP zur Verfügung zu stellen.','dispatch.php/course/elearning/show','3.1',0,0,1,'','',1406641688,0,NULL),
('a20036992a06e97a984832626121d99a','a20036992a06e97a984832626121d99a','de','Die TeilnehmerInnenliste zeigt eine Liste der Teilnehmenden dieser Veranstaltung. Weitere Teilnehmende können von Dozierenden hinzugefügt, entfernt, herabgestuft, heraufgestuft oder selbstdefinierten Gruppen zugeordnet werden.','dispatch.php/course/members','3.1',0,0,1,'','',1406641688,0,NULL),
('a202eb75df0a1da2a309ad7a4abfac59','a202eb75df0a1da2a309ad7a4abfac59','de','In den Privatsphäre-Einstellungen kann die Sichtbarkeit und Auffindbarkeit des eigenen Profils eingestellt werden.','dispatch.php/settings/privacy','3.1',0,0,1,'','',1406641688,0,NULL),
('a2a649de15c8d8473b11fccc731dc80f','a2a649de15c8d8473b11fccc731dc80f','en','Before archiving you can check on this page that the right course(s) have been selected for archiving.','dispatch.php/course/archive/confirm','4.4',1,0,1,'','',0,0,NULL),
('aa77d5ee6e0f9a9e6f4a1bbabeaf4a7e','aa77d5ee6e0f9a9e6f4a1bbabeaf4a7e','de','Die Anwesenheitsliste zeigt alle Sitzungstermine (Sitzung, Vorlesung, Übung, Praktikum) des Ablaufplans und ermöglicht das Eintragen von Studierenden durch die Dozierenden in Stud.IP sowie einen Export der Liste zur Übersicht oder als Grundlage handschriftlicher Eintragungen.','participantsattendanceplugin/show','3.1',0,0,1,'','',1406641688,0,NULL),
('abaa7b076e6923ac43120f3326322af0','abaa7b076e6923ac43120f3326322af0','en','This page allows the storing of free information, links etc.','dispatch.php/course/scm','4.4',0,0,1,'','',1412942388,0,NULL),
('abfb5d03de288d02df436f9a8bb96d9d','abfb5d03de288d02df436f9a8bb96d9d','en','With the image uploading feature, the image of a course can be changed, which can help students differentiate between courses on the My Courses page.','dispatch.php/course/avatar','4.4',0,0,1,'','',1412942388,0,NULL),
('ac5df1de9c75fc92af7718b2103d3037','ac5df1de9c75fc92af7718b2103d3037','de','Blubbern ist eine Mischform aus Forum und Chat. Nachrichten werden im öffentlichen Stream dargestellt. Andere Nutzer können über einen Beitrag informiert werden, indem sie per @benutzername oder @\"Vorname Nachname\" im Beitrag erwähnt werden.','plugins.php/blubber/streams/profile','3.1',0,0,1,'','',1406641688,0,NULL),
('ac7326260fd5ca4fa83c1154f2ffc7b9','ac7326260fd5ca4fa83c1154f2ffc7b9','de','Die Dateiverwaltung bietet die Möglichkeit zum Hochladen, Verlinken, Verwalten und Herunterladen von Dateien. ','folder.php','3.1',0,0,1,'','',1406641688,0,NULL),
('af7573cce1e898054db89a96284866f9','af7573cce1e898054db89a96284866f9','en','Here you can create a new study group. Each Stud.IP user can create study groups and configure them according to their own needs.','dispatch.php/course/studygroup/new','4.4',0,0,1,'','',1412942388,0,NULL),
('b05b27450e363c38c6b4620b902b3496','b05b27450e363c38c6b4620b902b3496','en','The start page opens after logging in and can be adjusted to your personal needs by using widgets.','dispatch.php/start','4.4',0,0,1,'','',1412942388,0,NULL),
('b283b58820db358284f4451dfb691678','b283b58820db358284f4451dfb691678','en','Here you can search for references in catalogues and add them to your list.','dispatch.php/literature/search','4.4',0,0,1,'','',1412942388,0,NULL),
('b32cb2c4ec56e925b07a5cb0105a6888','b32cb2c4ec56e925b07a5cb0105a6888','en','The password of the Stud.IP account can be changed here.','dispatch.php/settings/password','4.4',0,0,1,'','',1412942388,0,NULL),
('b3bd33cb0babbb0cc51a4f429d15d438','b3bd33cb0babbb0cc51a4f429d15d438','en','Here you can add members to a study group and send messages to them.','dispatch.php/course/studygroup/members','4.4',0,0,1,'','',1412942388,0,NULL),
('b5fabb1e5aed7ff8520314e9a86c5c87','b5fabb1e5aed7ff8520314e9a86c5c87','en','Here, individual content can be activated or deactivated. Active contents add new features to your profile or settings. These will usually appear as new tabs in the menu. If features are not required, they can be deactivated here. The corresponding menu items will then be hidden.','dispatch.php/profilemodules/index','4.4',0,0,1,'','',1412942388,0,NULL),
('b677e8b5f1bd7e8acbe474177449c4e1','b677e8b5f1bd7e8acbe474177449c4e1','de','Die Dateiverwaltung bietet die Möglichkeit zum Hochladen, Verwalten und Herunterladen persönlicher Dateien, die nicht für andere einsehbar sind. ','dispatch.php/document/files','3.1',0,0,1,'','',1406641688,0,NULL),
('b9586c280a0092f86f9392fe5b5ff2a0','b9586c280a0092f86f9392fe5b5ff2a0','en','Blubber is the Stud.IP real-time forum, a mixture of forum and chat. Others can be informed about a post by mentioning them by @username or @\'firstname surname\' in the post. Texts can be formatted and supplemented with smileys.','plugins.php/blubber/streams/global','4.4',0,0,1,'','',1412942388,0,NULL),
('bc1d6ecab9364cfe2c549d262bfda437','bc1d6ecab9364cfe2c549d262bfda437','de','Die Lernmodulschnittstelle ermöglicht es, Selbstlerneinheiten aus externen Programmen wie ILIAS und LON-CAPA in Stud.IP zur Verfügung zu stellen. Für jedes externe System wird ein eigener Benutzer-Account erstellt oder zugeordnet. Mit den entsprechenden Rechten können eigene Lernmodule erstellt werden.','dispatch.php/elearning/my_accounts','3.1',0,0,1,'','',1406641688,0,NULL),
('bcdedaf1b4bd3b96ef574e8230095b28','bcdedaf1b4bd3b96ef574e8230095b28','en','RSS feeds, i.e. news streams from external websites, can be integrated on the start page. The more feeds you include, the longer it takes to load the start page.','dispatch.php/admin/rss_feeds','4.4',0,0,1,'','',1412942388,0,NULL),
('bd0770f9eef5c10fc211114ac35fbe9b','bd0770f9eef5c10fc211114ac35fbe9b','de','Studiengruppen sind eine Möglichkeit, in Gruppen zusammenzuarbeiten. Jede Person kann eine Studiengruppe erstellen und so einen gemeinsamen Ort zum Lernen und Austauschen schaffen.\r\n\r\nDies ist die Übersicht aller Studiengruppen, in denen Sie eingetragen sind.\r\n\r\nUm zu erfahren wie man Studiengruppen anlegen kann, ist unten eine Tour zusammengestellt.\r\n\r\nHinweis: Auf der Startseite sind zwei Widgets zu den Studiengruppen zu finden. Diese ermöglichen eine bessere Sichtbarkeit und Vorschläge für interessante Studiengruppen.','dispatch.php/my_studygroups','6.0',0,0,1,'','',1406641688,0,NULL),
('bd5df4fb7b84da79149c96c5f43de46c','bd5df4fb7b84da79149c96c5f43de46c','en','Groups can be created and managed here. If the self-entry is activated, participants can register themselves and sign themselves out.','admin_statusgruppe.php','4.4',0,0,1,'','',1412942388,0,NULL),
('be204bdd0fce91702f51597bf8428fba','be204bdd0fce91702f51597bf8428fba','de','Das Wiki ermöglicht ein gemeinsames, asynchrones Erstellen und Bearbeiten von Texten. Texte lassen sich formatieren und miteinander verknüpfen, so dass ein verzweigtes Nachschlagewerk entsteht. ','wiki.php','3.1',0,0,1,'','',1406641688,0,NULL),
('bf9eb8f2c3842865009342b89fd35476','bf9eb8f2c3842865009342b89fd35476','de','Die Nachrichtenseite bietet einen Überblick über erhaltene, systeminterne Nachrichten, welche mit selbstgewählten Schlüsselwörtern (sog. Tags) versehen werden können, um sie später leichter wieder auffinden zu können.','dispatch.php/messages/overview','3.1',0,0,1,'','',1406641688,0,NULL),
('bfb70d5f036769d740fb2342b0b58183','bfb70d5f036769d740fb2342b0b58183','en','The learning module interface makes it possible to provide study units from external programs such as ILIAS and LON-CAPA in Stud.IP. A separate user account is created or assigned for each external system. With the appropriate rights, own learning modules can be created.','dispatch.php/elearning/my_accounts','4.4',0,0,1,'','',1412942388,0,NULL),
('c01725d6a3da568e1b07aee4e68a7e1f','c01725d6a3da568e1b07aee4e68a7e1f','de','Diese Seite ermöglicht das Hinterlegen von freien Informationen, Links etc.','dispatch.php/course/scm','3.1',0,0,1,'','',1406641688,0,NULL),
('c4dee277f741cfa7d5a65fa0c6bead4c','c4dee277f741cfa7d5a65fa0c6bead4c','de','Hier können Termine mit Themen versehen werden oder bereits eingegebene Themen übernommen und bearbeitet werden.','dispatch.php/course/topics','3.1',0,0,1,'','',1406641688,0,NULL),
('c8e789a0efb73f00f00dacf565524c73','c8e789a0efb73f00f00dacf565524c73','en','Various display and notification options can be selected and changed in the general settings.','dispatch.php/settings/general','4.4',0,0,1,'','',1412942388,0,NULL),
('cbd9b2b22fc00bc92df3589018644b70','cbd9b2b22fc00bc92df3589018644b70','de','Hier können vordefinierte Informationen über die eigene Person eingegeben werden, die auf der Profilseite erscheinen sollen. ','dispatch.php/settings/details','3.1',0,0,1,'','',1406641688,0,NULL),
('cd69b74cd46172785bf2147fb0582e3c','cd69b74cd46172785bf2147fb0582e3c','de','Hier kann ein benutzerdefinierter Blubber-Stream erstellt werden. Er besteht immer aus einer Sammlung von Beiträgen aus ausgewählten Veranstaltungen, Kontaktgruppen und Schlagwörten, die auf Basis einer Filterung noch weiter eingeschränkt werden können. Der neue benutzerdefinierte Stream findet sich nach dem Klick auf den Speichern-Button in der Navigation unter Globaler Stream.','plugins.php/blubber/streams/edit','3.1',0,0,1,'','',1406641688,0,NULL),
('ceb21257092b11dcf6897d5bb3085642','ceb21257092b11dcf6897d5bb3085642','en','An overview of sent, internal system messages, which can be provided with self-selected keywords (\"tags\") in order to be able to find them more easily later.','dispatch.php/messages/sent','4.4',0,0,1,'','',1412942388,0,NULL),
('d04ca1f9e867ee295a3025dac7ce9c7b','d04ca1f9e867ee295a3025dac7ce9c7b','en','View of the institutions assigned to the Stud.IP user.','dispatch.php/settings/statusgruppen','4.4',0,0,1,'','',1412942388,0,NULL),
('d1de152db139d8c12552610d2f7999c2','d1de152db139d8c12552610d2f7999c2','de','Mit dem Export können Daten über Veranstaltungen und MitarbeiterInnen in folgende Formate exportiert werden: RTF, TXT, CSV, PDF, HTML und XML.','export.php','3.1',0,0,1,'','',1406641688,0,NULL),
('d704267767d4c559aa9e552be60c49b5','d704267767d4c559aa9e552be60c49b5','de','Hier kann das Passwort für den Stud.IP-Account geändert werden.','dispatch.php/settings/password','3.1',0,0,1,'','',1406641688,0,NULL),
('d79ca3bc4a8251862339b1c934504a54','d79ca3bc4a8251862339b1c934504a54','de','Hier werden die selbstdefinierten Gruppen angezeigt. An diese können Nachrichten versendet werden. Ein Klick auf die orangenen Pfeile vor dem Gruppenname ordnet Sie der Gruppe zu.','statusgruppen.php','3.1',0,0,1,'','',1406641688,0,NULL),
('d97eff1196f6aed8e94f7c5096ebd2a9','d97eff1196f6aed8e94f7c5096ebd2a9','en','The overview contains course-related short and detailed information, announcements, dates and surveys.','dispatch.php/course/overview','4.4',0,0,1,'','',1412942388,0,NULL),
('db5a995bd12ba8e2ae96adcabeb8c8f7','db5a995bd12ba8e2ae96adcabeb8c8f7','de','Der Terminkalender besteht aus abonnierten Veranstaltungen und eigenen Terminen. Er kann bearbeitet, in der Anzeige verändert und mit externen Programmen (z.B. Outlook) abgeglichen werden. ','calendar.php','3.1',0,0,1,'','',1406641688,0,NULL),
('dddf5fd4406da0d91c9f121fcae607ad','dddf5fd4406da0d91c9f121fcae607ad','en','The appointment calendar consists of subscribed courses and your own appointments. It can be edited, changed in the display and compared with external programs (e.g. Outlook).','calendar.php','4.4',0,0,1,'','',1412942388,0,NULL),
('e03cec310c0a884aee80c2d1eea3a53e','e03cec310c0a884aee80c2d1eea3a53e','de','Diese Seite zeigt alle Studiengruppen an, die in Stud.IP existieren. Studiengruppen sind eine einfache Möglichkeit, mit Mitstudierenden, KollegInnen und anderen zusammenzuarbeiten. Jede/r NutzerIn kann Studiengruppen anlegen oder nach ihnen suchen.','dispatch.php/studygroup/browse','3.1',0,0,1,'','',1406641688,0,NULL),
('e206a4257e31a0f32ac516cefb8e8331','e206a4257e31a0f32ac516cefb8e8331','en','You can find university ressources like rooms, buildings etc. with the ressource search engine.','resources.php','4.4',0,0,1,'','',1412942388,0,NULL),
('e22701c71b4425fb5a95adf725866097','e22701c71b4425fb5a95adf725866097','de','Hier können Gruppen erstellt und verwaltet werden. Wenn der Selbsteintrag aktiviert ist, können sich TeilnehmerInnen selbst ein- und austragen.','admin_statusgruppe.php','3.1',0,0,1,'','',1406641688,0,NULL),
('e29098d188ae25c298d78978de50bf09','e29098d188ae25c298d78978de50bf09','de','Hier kann in Katalogen nach Literatur gesucht und diese zur Merkliste hinzugefügt werden.','dispatch.php/literature/search','3.1',0,0,1,'','',1406641688,0,NULL),
('e315a4c547be7f17d427b227f0f9d982','e315a4c547be7f17d427b227f0f9d982','de','Auf dieser Seite können selbstdefinierte Informationen über die eigene Person eingegeben werden, die auf der Profilseite erscheinen sollen. ','dispatch.php/settings/categories','3.1',0,0,1,'','',1406641688,0,NULL),
('e5bff29f7adee43202a2aa8f3f0a6ec7','e5bff29f7adee43202a2aa8f3f0a6ec7','en','The profile page allows you to change your own user data including profile picture and categories. Similar to Facebook, comments can be left. The lecturer\'s profile contains office hours and room details. In addition, the page offers the management of own files.','dispatch.php/profile','4.4',0,0,1,'','',1412942388,0,NULL),
('e939ac70210674f49a36ac428167a9b8','e939ac70210674f49a36ac428167a9b8','de','Mit der Umfragen-und-Tests-Funktion lassen sich (zeitgesteuerte) Umfragen oder einzelne Multiple-/Single-Choice-Fragen für Veranstaltungen, Studiengruppen oder das Profil erstellen.','admin_vote.php','3.1',0,0,1,'','',1406641688,0,NULL),
('ebb5bc1d831d460c06e3c6662236c159','ebb5bc1d831d460c06e3c6662236c159','de','Hier kann ein Profilbild hochgeladen werden.','dispatch.php/settings/avatar','3.1',0,0,1,'','',1406641688,0,NULL),
('ebcc460880b8a63af3f6e7eade97db78','ebcc460880b8a63af3f6e7eade97db78','en','With the user search, users can be found as long as their privacy settings do not prevent this. The search can be limited to certain courses or institutions.','browse.php','4.4',0,0,1,'','',1412942388,0,NULL),
('ee91ec0f9085221ada06d171a27d2405','ee91ec0f9085221ada06d171a27d2405','en','File management offers the possibility to upload, link to, manage and download files.','folder.php','4.4',0,0,1,'','',1412942388,0,NULL),
('eec46c5d8ea5523d959a8c334455c2ef','eec46c5d8ea5523d959a8c334455c2ef','en','You can use the fields of study-feature to assign a course to a field of study. Editing can be locked if data is transferred from other systems (for example, LSF/ UniVZ).','dispatch.php/course/study_areas/show','4.4',0,0,1,'','',1412942388,0,NULL),
('f3deb7a01205637d71a66e2b90b24cba','f3deb7a01205637d71a66e2b90b24cba','de','Hier können RSS-Feeds, d.h. Nachrichtenströme von externen Internetseiten, auf der Startseite eingebunden werden. Je mehr Feeds eingebunden werden, desto länger dauert das Laden der Startseite.','dispatch.php/admin/rss_feeds','3.1',0,0,1,'','',1406641688,0,NULL),
('f529bca4d1626b43cbb8149feea41a84','f529bca4d1626b43cbb8149feea41a84','en','The self-defined groups are displayed here. Messages can be sent to these groups. A click on the orange arrows in front of the group name assigns you to the group.','statusgruppen.php','4.4',0,0,1,'','',1412942388,0,NULL),
('f5e59c4fc98e1df7fe29b8e9320853e7','f5e59c4fc98e1df7fe29b8e9320853e7','en','In the privacy settings you can set the visibility and discoverability of your own profile.','dispatch.php/settings/privacy','4.4',0,0,1,'','',1412942388,0,NULL),
('f92b5422246f585f051de1a81602dd56','f92b5422246f585f051de1a81602dd56','de','Hier können Name, Funktionen und Zugangsbeschränkung der Studiengruppe bearbeitet werden.','dispatch.php/course/studygroup/edit','3.1',0,0,1,'','',1406641688,0,NULL),
('f966e348174927565b94e606bbcf064f','f966e348174927565b94e606bbcf064f','en','The message page provides an overview of received, internal system messages, which can be assigned self-selected keywords (\"tags\") to make them easier to find later.','dispatch.php/messages/overview','4.4',0,0,1,'','',1412942388,0,NULL),
('fa4bf491690645a5f12556f77e51233c','fa4bf491690645a5f12556f77e51233c','en','Here you can edit reference lists and make them visible in a course (click on the \"eye\").','dispatch.php/literature/edit_list.php','4.4',0,0,1,'','',1412942388,0,NULL),
('fe23b56f4d691c0f5e2f872e37ce38b5','fe23b56f4d691c0f5e2f872e37ce38b5','en','Individual user data e.g. email address, can be changed on this page.','dispatch.php/settings/account','4.4',0,0,1,'','',1412942388,0,NULL);
/*!40000 ALTER TABLE `help_content` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `help_tour_audiences`
--

LOCK TABLES `help_tour_audiences` WRITE;
/*!40000 ALTER TABLE `help_tour_audiences` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `help_tour_audiences` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `help_tour_settings`
--

LOCK TABLES `help_tour_settings` WRITE;
/*!40000 ALTER TABLE `help_tour_settings` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `help_tour_settings` VALUES
('05434e40601a9a2a7f5fa8208ae148c1',1,'standard',NULL,NULL),
('154e711257d4d32d865fb8f5fb70ad72',1,'standard',NULL,NULL),
('19ac063e8319310d059d28379139b1cf',1,'standard',NULL,NULL),
('1badcf28ab5b206d9150b2b9683b4cb6',1,'standard',NULL,NULL),
('21f487fa74e3bfc7789886f40fe4131a',1,'standard',NULL,NULL),
('3629493a16bf2680de64361f07cab096',1,'autostart_once',NULL,NULL),
('3a717a468afb0822cb1455e0ae6b6fce',1,'autostart_once',NULL,NULL),
('3dbe7099f82dcdbba4580acb1105a0d6',1,'standard',NULL,1631619331),
('44f859c50648d3410c39207048ddd833',1,'standard',NULL,1631619331),
('49604a77654617a745e29ad6b253e491',1,'standard',NULL,1631613451),
('4d41c9760a3248313236af202275107a',1,'autostart_once',NULL,NULL),
('4d41c9760a3248313236af202275107b',1,'standard',NULL,1631619212),
('4d41c9760a3248313236af202275107c',1,'standard',NULL,1631619264),
('55f3a548348dcbfdca67678588887ffd',1,'autostart_once',1631612143,1631612143),
('588effa83da976a889a68c152bcabc90',1,'autostart_once',NULL,NULL),
('5d41c9760a3248313236af202275107a',1,'autostart_once',NULL,NULL),
('5d41c9760a3248313236af202275107b',1,'standard',NULL,1631619212),
('5d41c9760a3248313236af202275107c',1,'standard',NULL,1631619264),
('6849293baa05be5bef8ff438dc7c438b',1,'standard',NULL,NULL),
('7af1e1fb7f53c910ba9f42f43a71c723',1,'standard',NULL,NULL),
('7cccbe3b22dfa745c17cb776fb04537c',1,'standard',NULL,NULL),
('83dc1d25e924f2748ee3293aaf0ede8e',1,'autostart_once',NULL,NULL),
('89786eac42f52ac316790825b4f5c0b2',1,'standard',NULL,NULL),
('96ea422f286fb5bbf9e41beadb484a9a',1,'standard',NULL,NULL),
('9e9dca9b1214294b9605824bfe90fba1',1,'standard',NULL,NULL),
('b74f8459dce2437463096d56db7c73b9',1,'standard',NULL,NULL),
('c89ce8e097f212e75686f73cc5008711',1,'standard',NULL,NULL),
('d9913517f9c81d2c0fa8362592ce5d0e',1,'autostart_once',NULL,NULL),
('d9a066071e2be43b2b51c37a9d692026',1,'autostart_once',1631612143,1631612143),
('dac47ec2e8a848744bde4b3881d31553',1,'autostart_once',NULL,NULL),
('de1fbce508d01cbd257f9904ff8c3b43',1,'standard',NULL,NULL),
('edfcf78c614869724f93488c4ed09582',1,'standard',NULL,NULL),
('ef5092ba722c81c37a5a6bd703890bd9',1,'autostart_once',NULL,NULL),
('f0aeb0f6c4da3bd61f48b445d9b30dc1',1,'standard',NULL,1631613451),
('fa963d2ca827b28e0082e98aafc88765',1,'standard',NULL,NULL);
/*!40000 ALTER TABLE `help_tour_settings` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `help_tour_steps`
--

LOCK TABLES `help_tour_steps` WRITE;
/*!40000 ALTER TABLE `help_tour_steps` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `help_tour_steps` VALUES
('05434e40601a9a2a7f5fa8208ae148c1',1,'My documents','This tour provides an overview of the personal document manager.\r\n\rTo proceed, please click \"Continue\" in the lower-right corner.','B',0,'','dispatch.php/files','','','root@localhost',1405592884,0),
('05434e40601a9a2a7f5fa8208ae148c1',2,'New documents and indices','New documents can be uploaded from the computer into the personal document area and new indices can be created here.','TL',0,'#layout-sidebar SECTION:eq(0)  DIV:eq(8)  DIV:eq(0)','dispatch.php/files','','','',1405593409,0),
('05434e40601a9a2a7f5fa8208ae148c1',3,'Document overview','All documents and indices are listed in a tabular form. In addition to the name even more information is displayed such as the document type or the document size.','TL',0,'#files_table_form TABLE:eq(0)  CAPTION:eq(0)  DIV:eq(0)','dispatch.php/files','','','',1405593089,0),
('05434e40601a9a2a7f5fa8208ae148c1',4,'','Already uploaded documents and folders can be edited, downloaded, shifted, copied and deleted here.','TL',0,'table.documents tfoot .footer-items','dispatch.php/files','','','',1405594079,0),
('05434e40601a9a2a7f5fa8208ae148c1',5,'Export','Here you have the possibility to download individual folders or the full document area as a ZIP document. All documents and indices are contained therein.','LT',0,'table.documents .action-menu-icon','dispatch.php/files','','','dozent@studip.de',1405593708,0),
('154e711257d4d32d865fb8f5fb70ad72',1,'Dateien','Dies ist der persönliche Dateibereich. Hier können Dateien in Stud.IP gespeichert werden, um sie von dort auf andere Rechner herunterladen zu können.\n\nAndere Studierende oder Dozierende erhalten keinen Zugriff auf Dateien, die in den persönlichen Dateibereich hochgeladen werden.\n\nUm auf den nächsten Schritt zu kommen, klicken Sie bitte rechts unten auf \"Weiter\".','B',0,'','dispatch.php/files','','','root@localhost',1405592884,0),
('154e711257d4d32d865fb8f5fb70ad72',2,'Neue Dateien und Verzeichnisse','Hier können neue Dateien von dem Computer in den persönlichen Dateibereich hochgeladen und neue Verzeichnisse erstellt werden.','TL',0,'#layout-sidebar SECTION:eq(0)  DIV:eq(8)  DIV:eq(0)','dispatch.php/files','','','',1405593409,0),
('154e711257d4d32d865fb8f5fb70ad72',3,'Dateiübersicht','Alle Dateien und Verzeichnisse werden tabellarisch aufgelistet. Neben dem Namen werden noch weitere Informationen wie der Dateityp oder die Dateigröße angezeigt.','TL',0,'#files_table_form TABLE:eq(0)  CAPTION:eq(0)  DIV:eq(0)','dispatch.php/files','','','',1405593089,0),
('154e711257d4d32d865fb8f5fb70ad72',4,'','Bereits hochgeladene Dateien und Ordner können hier bearbeitet, heruntergeladen, verschoben, kopiert und gelöscht werden.','TL',0,'table.documents tfoot .footer-items','dispatch.php/files','','','',1405594079,0),
('154e711257d4d32d865fb8f5fb70ad72',5,'Export','Hier besteht die Möglichkeit einzelne Ordner oder den vollständigen Dateibereich als ZIP-Datei herunterzuladen. Darin sind alle Dateien und Verzeichnisse enthalten.','LT',0,'table.documents .action-menu-icon','dispatch.php/files','','','dozent@studip.de',1405593708,0),
('19ac063e8319310d059d28379139b1cf',1,'Studiengruppe anlegen','Studiengruppen ermöglichen eine einfache Zusammenarbeit und den Austausch mit Kommiliton*innen.\nDiese Tour zeigt Ihnen Schritt für Schritt, wie Sie eine Studiengruppe erstellen.\n\rHinweis: Klicken Sie auf “Weiter”, um den nächsten Schritt zu starten.','R',0,'','dispatch.php/my_studygroups','','','',1405684423,0),
('19ac063e8319310d059d28379139b1cf',2,'Studiengruppe anlegen','Klicken Sie auf „Neue Studiengruppe anlegen“, um den Dialog zur Erstellung einer neuen Studiengruppe zu öffnen.','BL',0,'.sidebar-widget:eq(1) A:eq(0)','dispatch.php/my_studygroups','.ui-dialog-titlebar-close:eq(0)','','',1405684423,0),
('19ac063e8319310d059d28379139b1cf',3,'Name der Studiengruppe','Geben Sie einen klaren und aussagekräftigen Titel für Ihre Studiengruppe ein.','R',0,'#wizard-name','dispatch.php/my_studygroups','','.sidebar-widget:eq(1) li:eq(0) a:eq(0)','',1405684720,0),
('19ac063e8319310d059d28379139b1cf',4,'Beschreibung','Beschreiben Sie den Zweck oder die Ziele der Studiengruppe (z. B. Themen, Aktivitäten, Zielgruppe).','R',0,'#wizard-description','dispatch.php/my_studygroups','','','dozent@studip.de',1405684806,0),
('19ac063e8319310d059d28379139b1cf',6,'Zugang','Wählen Sie aus, ob die Studiengruppe für alle offen ist oder ein Beitritt nur auf Anfrage gewährt werden soll.','R',0,'#wizard-access','dispatch.php/my_studygroups','','','root@localhost',1405685334,0),
('19ac063e8319310d059d28379139b1cf',7,'','Die Laufzeit von Studiengruppen ist standardmäßig auf zwei\rJahre festgelegt. Gruppenadmins werden rechtzeitig vor Ablauf per E-Mail informiert und können die Laufzeit bei Bedarf verlängern. Diese Einstellung ist besonders hilfreich für zeitlich begrenzte Projekte.','R',0,'#wizard-datepicker','dispatch.php/my_studygroups','','','root@localhost',1405685652,0),
('19ac063e8319310d059d28379139b1cf',8,'','Fügen Sie optional Schlagwörter hinzu, die Ihre Gruppe beschreiben (z. B. „Mathe“, „Projektarbeit“). Dies erhöht die Sichtbarkeit für Interessierte.','R',0,'#studygroup-wizard-tags','dispatch.php/my_studygroups','','','root@localhost',1405685652,0),
('19ac063e8319310d059d28379139b1cf',9,'Studiengruppe speichern','Mit dem Klick auf den Button Studiengruppe anlegen wird die Studiengruppe erstellt. Sie können jetzt Mitglieder hinzufügen, Inhalte teilen und gemeinsam arbeiten.','T',0,'.ui-dialog-buttonset','dispatch.php/my_studygroups','.sidebar-widget:eq(1) li:eq(0) a:eq(0)','','root@localhost',1405686068,0),
('19ac063e8319310d059d28379139b1cf',10,'','Alle Einstellungen können jederzeit über die Verwaltungsoptionen der Gruppe angepasst werden.','B',0,'','dispatch.php/my_studygroups','','.ui-dialog-titlebar-close:eq(0)','root@localhost',1405686068,0),
('1badcf28ab5b206d9150b2b9683b4cb6',1,'My courses','This tour provides an overview of the functionality of \"My courses\".\r\n\rTo proceed, please click \"Continue\" in the lower-right corner.','TL',0,'','dispatch.php/my_courses','','','',1406125847,0),
('1badcf28ab5b206d9150b2b9683b4cb6',2,'Overview of courses','The courses of the current and past semester are displayed here. New courses initially appear in red.','BL',0,'#my_seminars TABLE:eq(0)  CAPTION:eq(0)','dispatch.php/my_courses','','','dozent@studip.de',1406125908,0),
('1badcf28ab5b206d9150b2b9683b4cb6',3,'Course details','With a click on the \"i\" a window appears with the most important facts of the courses.','BR',0,'#my_seminars .action-menu-icon','dispatch.php/my_courses','','#my_seminars TABLE:eq(0)  TBODY:eq(0)  TR:eq(1)  TD:eq(5)  NAV:eq(0)','dozent@studip.de',1406125992,0),
('1badcf28ab5b206d9150b2b9683b4cb6',4,'Course contents','All contents (such as e.g. a forum) are displayed by corresponding symbols here.\n\nIf there were any news since the last login these will appear in red.','B',0,'#my_seminars .my-courses-navigation-item','dispatch.php/my_courses','','','dozent@studip.de',1406126049,0),
('1badcf28ab5b206d9150b2b9683b4cb6',5,'Editing or deletion of a course','A click on the cog wheel enables you to edit a course.\n\nIf you have participant status in a course, you can sign out by clicking on the door icon.','BR',0,'#my_seminars .action-menu-icon','dispatch.php/my_courses','','','dozent@studip.de',1406126134,0),
('1badcf28ab5b206d9150b2b9683b4cb6',6,'Adjustment to the event view','In order to adjust the course overview you can order your courses according to certain criteria (such as e.g. fields of study, lecturers, or colours).','R',0,'#layout-sidebar SECTION:eq(0)  DIV:eq(11)  DIV:eq(0)','dispatch.php/my_courses','','','',1406126281,0),
('1badcf28ab5b206d9150b2b9683b4cb6',7,'Access to an event of past and future semesters','For example, by clicking on the drop-down menu, courses from past semesters can be displayed.','R',0,'#layout-sidebar SECTION:eq(0)  DIV:eq(5)  DIV:eq(1)','dispatch.php/my_courses','','','',1406126316,0),
('1badcf28ab5b206d9150b2b9683b4cb6',8,'Further possible actions','Here you can mark all news as read, change colour groups as you please, and also adjust the notifications about activities in the individual courses.','R',0,'#layout-sidebar SECTION:eq(0)  DIV:eq(8)  DIV:eq(0)','dispatch.php/my_courses','','','',1406126374,0),
('1badcf28ab5b206d9150b2b9683b4cb6',9,'Study groups and facilities','There is moreover the possibility to access personal study groups or facilities.','R',0,'#nav_browse_my_institutes A','dispatch.php/my_courses','','','',1406126415,0),
('21f487fa74e3bfc7789886f40fe4131a',1,'Forum','Diese Tour gibt einen Überblick über die Elemente und Interaktionsmöglichkeiten des Forums.\r\n\r\nUm zum nächsten Schritt zu gelangen, klicken Sie bitte rechts unten auf \"Weiter\".','BL',0,'','dispatch.php/course/forum','','','',1405415772,0),
('21f487fa74e3bfc7789886f40fe4131a',2,'Sie befinden sich hier:...','An dieser Stelle wird angezeigt, welcher Bereich des Forums gerade betrachtet wird.','BL',0,'DIV#tutorBreadcrumb','dispatch.php/course/forum','','','',1405415875,0),
('21f487fa74e3bfc7789886f40fe4131a',3,'Kategorie','Das Forum ist unterteilt in Kategorien, Themen und Beiträge. Eine Kategorie fasst Forumsbereiche in größere Sinneinheiten zusammen.','BL',0,'#tutorCategory','dispatch.php/course/forum','','','',1405416611,0),
('21f487fa74e3bfc7789886f40fe4131a',4,'Bereich','Das ist ein Bereich innerhalb einer Kategorie. Bereiche beinhalten die Diskussionstränge. Bereiche können mit per drag & drop in ihrer Reihenfolge verschoben werden.','BL',0,'#sortable_areas TABLE:eq(0)  TBODY:eq(0)  TR:eq(0)  TD:eq(1)  DIV:eq(0)  SPAN:eq(0)  A:eq(0)  SPAN:eq(0)','dispatch.php/course/forum','','','',1405416664,0),
('21f487fa74e3bfc7789886f40fe4131a',5,'Info-Icon','Dieses Icon färbt sich rot, sobald es etwas neues in diesem Bereich gibt.','B',0,'#sortable_areas TABLE:eq(0)  TBODY:eq(0)  TR:eq(0)  TD:eq(0)  A:eq(0)  IMG:eq(0)','dispatch.php/course/forum','','','',1405416705,0),
('21f487fa74e3bfc7789886f40fe4131a',6,'Suchen','Hier können sämtliche Inhalte dieses Forums durchsucht werden.\r\nUnterstützt werden auch Mehrwortsuchen. Außerdem kann die Suche auf eine beliebige Kombination aus Titel, Inhalt und Autor eingeschränkt werden.','BL',0,'#tutorSearchInfobox DIV:eq(0)','dispatch.php/course/forum','','','',1405417134,0),
('21f487fa74e3bfc7789886f40fe4131a',7,'Forum abonnieren','Das gesamte Forum, oder einzelne Themen können abonniert werden. Dann wird bei jedem neuen Beitrag in diesem Forum eine Benachrichtigung angezeigt und eine Nachricht versendet.','RT',0,'#layout-sidebar SECTION:eq(0)  DIV:eq(9)  DIV:eq(0)','dispatch.php/course/forum','','','dozent@studip.de',1405416795,0),
('3629493a16bf2680de64361f07cab096',1,'Was ist Blubbern?','Diese Tour gibt Ihnen einen Überblick über die wichtigsten Funktionen von \"Blubber\".\r\n\r\nUm auf den nächsten Schritt zu kommen, klicken Sie bitte rechts unten auf \"Weiter\".','TL',0,'','plugins.php/blubber/streams/forum','','','',1405507364,0),
('3629493a16bf2680de64361f07cab096',2,'Beitrag erstellen','Hier kann eine Diskussion durch Schreiben von Text begonnen werden. Absätze lassen sich durch Drücken von Umschalt+Eingabe erzeugen. Der Text wird durch Drücken von Eingabe abgeschickt.','BL',0,'TEXTAREA#new_posting.autoresize','plugins.php/blubber/streams/forum','','','',1405507478,0),
('3629493a16bf2680de64361f07cab096',3,'Text gestalten','Der Text kann formatiert und mit Smileys versehen werden.\r\nEs können die üblichen Formatierungen verwendet werden, wie z. B. **fett** oder %%kursiv%%.','BL',0,'TEXTAREA#new_posting.autoresize','plugins.php/blubber/streams/forum','','','',1405508371,0),
('3629493a16bf2680de64361f07cab096',4,'Personen erwähnen','Andere können über einen Beitrag informiert werden, indem sie per @benutzername oder @\"Vorname Nachname\" im Beitrag erwähnt werden.','BL',0,'TEXTAREA#new_posting.autoresize','plugins.php/blubber/streams/forum','','','',1405672301,0),
('3629493a16bf2680de64361f07cab096',5,'Datei hinzufügen','Dateien können in einen Beitrag eingefügt werden, indem sie per Drag&Drop in ein Eingabefeld gezogen werden.','BL',0,'TEXTAREA#new_posting.autoresize','plugins.php/blubber/streams/forum','','','',1405508401,0),
('3629493a16bf2680de64361f07cab096',6,'Schlagworte','Beiträge können mit Schlagworten (engl. \"Hashtags\") versehen werden, indem einem beliebigen Wort des Beitrags ein # vorangestellt wird.','BL',0,'TEXTAREA#new_posting.autoresize','plugins.php/blubber/streams/forum','','','',1405508442,0),
('3629493a16bf2680de64361f07cab096',7,'Schlagwortwolke','Durch Anklicken eines Schlagwortes werden alle Beiträge aufgelistet, die dieses Schlagwort enthalten.','RT',0,'DIV.sidebar-widget-header','plugins.php/blubber/streams/forum','','','',1405508505,0),
('3629493a16bf2680de64361f07cab096',8,'Beitrag ändern','Wird der Mauszeiger auf einem beliebigen Beitrag positioniert, erscheint dessen Datum. Bei eigenen Beiträgen erscheint außerdem rechts neben dem Datum ein Icon, mit dem der Beitrag nachträglich geändert werden kann.','BR',0,'DIV DIV A SPAN.time','plugins.php/blubber/streams/forum','','','',1405507901,0),
('3629493a16bf2680de64361f07cab096',9,'Beitrag verlinken','Wird der Mauszeiger auf dem ersten Diskussionsbeitrag positioniert, erscheint links neben dem Datum ein Link-Icon. Wenn dieses mit der rechten Maustaste angeklickt wird, kann der Link auf diesen Beitrag kopiert werden, um ihn an anderer Stelle einfügen zu können.','BR',0,'DIV DIV A.permalink','plugins.php/blubber/streams/forum','','','',1405508281,0),
('3a717a468afb0822cb1455e0ae6b6fce',1,'Was ist Blubbern?','Diese Tour gibt Ihnen einen Überblick über die wichtigsten Funktionen von \"Blubber\".\r\n\r\nUm auf den nächsten Schritt zu kommen, klicken Sie bitte rechts unten auf \"Weiter\".','TL',0,'','plugins.php/blubber/streams/profile','','','',1405507364,0),
('3a717a468afb0822cb1455e0ae6b6fce',2,'Beitrag erstellen','Hier kann eine Diskussion durch Schreiben von Text begonnen werden. Absätze lassen sich durch Drücken von Umschalt+Eingabe erzeugen. Der Text wird durch Drücken von Eingabe abgeschickt.','BL',0,'TEXTAREA#new_posting.autoresize','plugins.php/blubber/streams/profile','','','',1405507478,0),
('3a717a468afb0822cb1455e0ae6b6fce',3,'Text gestalten','Der Text kann formatiert und mit Smileys versehen werden.\r\nEs können die üblichen Formatierungen verwendet werden, wie z. B. **fett** oder %%kursiv%%.','BL',0,'TEXTAREA#new_posting.autoresize','plugins.php/blubber/streams/profile','','','',1405508371,0),
('3a717a468afb0822cb1455e0ae6b6fce',4,'Personen erwähnen','Andere können über einen Beitrag informiert werden, indem sie per @benutzername oder @\"Vorname Nachname\" im Beitrag erwähnt werden.','BL',0,'TEXTAREA#new_posting.autoresize','plugins.php/blubber/streams/profile','','','',1405672301,0),
('3a717a468afb0822cb1455e0ae6b6fce',5,'Datei hinzufügen','Dateien können in einen Beitrag eingefügt werden, indem sie per Drag&Drop in ein Eingabefeld gezogen werden.','BL',0,'TEXTAREA#new_posting.autoresize','plugins.php/blubber/streams/profile','','','',1405508401,0),
('3a717a468afb0822cb1455e0ae6b6fce',6,'Schlagworte','Beiträge können mit Schlagworten (engl. \"Hashtags\") versehen werden, indem einem beliebigen Wort des Beitrags ein # vorangestellt wird.','BL',0,'TEXTAREA#new_posting.autoresize','plugins.php/blubber/streams/profile','','','',1405508442,0),
('3a717a468afb0822cb1455e0ae6b6fce',7,'Schlagwortwolke','Durch Anklicken eines Schlagwortes werden alle Beiträge aufgelistet, die dieses Schlagwort enthalten.','RT',0,'DIV.sidebar-widget-header','plugins.php/blubber/streams/profile','','','',1405508505,0),
('3a717a468afb0822cb1455e0ae6b6fce',8,'Beitrag ändern','Wird der Mauszeiger auf einem beliebigen Beitrag positioniert, erscheint dessen Datum. Bei eigenen Beiträgen erscheint außerdem rechts neben dem Datum ein Icon, mit dem der Beitrag nachträglich geändert werden kann.','BR',0,'DIV DIV A SPAN.time','plugins.php/blubber/streams/profile','','','',1405507901,0),
('3a717a468afb0822cb1455e0ae6b6fce',9,'Beitrag verlinken','Wird der Mauszeiger auf dem ersten Diskussionsbeitrag positioniert, erscheint links neben dem Datum ein Link-Icon. Wenn dieses mit der rechten Maustaste angeklickt wird, kann der Link auf diesen Beitrag kopiert werden, um ihn an anderer Stelle einfügen zu können.','BR',0,'DIV DIV A.permalink','plugins.php/blubber/streams/profile','','','',1405508281,0),
('3dbe7099f82dcdbba4580acb1105a0d6',1,'Administering the forum','This tour provides an overview of the forum\'s administration.\r\n\rTo proceed, please click \"Continue\" in the lower-right corner.','TL',0,'','dispatch.php/course/forum','','','',1405418008,0),
('3dbe7099f82dcdbba4580acb1105a0d6',2,'Edit category','The name of the category can be changed or, however, the whole category deleted with these icons. The sectors will in this case be shifted into the category \"General\" and are thus retained.\n\nThe category \"General\" cannot be deleted and is therefore included in each forum.','L',0,'#forum #sortable_areas TABLE CAPTION #tutorCategoryIcons','dispatch.php/course/forum','','','dozent@studip.de',1405424216,0),
('3dbe7099f82dcdbba4580acb1105a0d6',3,'Edit area','Action icons will appear, if the cursor is positioned on an area\n\nYou can use the icons to change the name and description of an area, or to delete the whole area.\nThe deletion of an area causes all contained topics to be deleted.','L',0,'#sortable_areas TABLE:eq(0)  TBODY:eq(0)  TR:eq(0)  TD:eq(4)','dispatch.php/course/forum','','','dozent@studip.de',1405424346,0),
('3dbe7099f82dcdbba4580acb1105a0d6',4,'Sort area','With this hatched surface areas can be sorted in at any place by clicking and dragging. This can, on one hand, be used in order to sort areas within a category, and on the other hand, areas can be shifted into other categories.','R',0,'#sortable_areas TABLE:eq(0)  TBODY:eq(0)  TR:eq(0)  TD:eq(0)  IMG:eq(0)','dispatch.php/course/forum','','','dozent@studip.de',1405424379,0),
('3dbe7099f82dcdbba4580acb1105a0d6',5,'Add new area','New areas can be added to a category here.','BR',0,'TFOOT TR TD A SPAN','dispatch.php/course/forum','','','',1405424421,0),
('3dbe7099f82dcdbba4580acb1105a0d6',6,'Create new category','A new category in the forum can be created here. Enter the title of the new category for this purpose.','TL',0,'#tutorAddCategory FIELDSET:eq(0)  LEGEND:eq(0)','dispatch.php/course/forum','','','',1405424458,0),
('44f859c50648d3410c39207048ddd833',1,'Forum verwalten','Sie haben die Möglichkeit sich eine Tour zur Verwaltung des Forums anzuschauen.\r\n\r\nUm die Tour zu beginnen, klicken Sie bitte unten rechts auf \"Weiter\".','TL',0,'','dispatch.php/course/forum','','','',1405418008,0),
('44f859c50648d3410c39207048ddd833',2,'Kategorie bearbeiten','Mit diesen Icons kann der Name der Kategorie geändert oder aber die gesamte Kategorie gelöscht werden. Die Bereiche werden in diesem Fall in die Kategorie \"Allgemein\" verschoben und bleiben somit erhalten.\r\n\r\nDie Kategorie \"Allgemein\" kann nicht gelöscht werden und ist daher in jedem Forum enthalten.','L',0,'#forum #sortable_areas TABLE CAPTION #tutorCategoryIcons','dispatch.php/course/forum','','','dozent@studip.de',1405424216,0),
('44f859c50648d3410c39207048ddd833',3,'Bereich bearbeiten','Wird der Mauszeiger auf einem Bereich positioniert, erscheinen Aktions-Icons.\r\nMit diesen Icons kann der Name und die Beschreibung eines Bereiches geändert oder auch der gesamte Bereich gelöscht werden.\r\nDas Löschen eines Bereichs, führt dazu, dass alle enthaltenen Themen gelöscht werden.','L',0,'#sortable_areas TABLE:eq(0)  TBODY:eq(0)  TR:eq(0)  TD:eq(4)','dispatch.php/course/forum','','','dozent@studip.de',1405424346,0),
('44f859c50648d3410c39207048ddd833',4,'Bereiche sortieren','Mit dieser schraffierten Fläche können Bereiche an einer beliebigen Stelle durch Klicken-und-Ziehen einsortiert werden. Dies kann einerseits dazu verwendet werden, um Bereiche innerhalb einer Kategorie zu sortieren, andererseits können Bereiche in andere Kategorien verschoben werden.','R',0,'#sortable_areas TABLE:eq(0)  TBODY:eq(0)  TR:eq(0)  TD:eq(0)  IMG:eq(0)','dispatch.php/course/forum','','','dozent@studip.de',1405424379,0),
('44f859c50648d3410c39207048ddd833',5,'Neuen Bereich hinzufügen','Hier können neue Bereiche zu einer Kategorie hinzugefügt werden.','BR',0,'TFOOT TR TD A SPAN','dispatch.php/course/forum','','','',1405424421,0),
('44f859c50648d3410c39207048ddd833',6,'Neue Kategorie erstellen','Hier kann eine neue Kategorie im Forum erstellt werden. Geben Sie hierfür den Titel der neuen Kategorie ein.','TL',0,'#tutorAddCategory FIELDSET:eq(0)  LEGEND:eq(0)','dispatch.php/course/forum','','','',1405424458,0),
('49604a77654617a745e29ad6b253e491',1,'Funktionen und Gestaltungsmöglichkeiten der Startseite','Diese Tour gibt Ihnen einen Überblick über die wichtigsten Funktionen der \"Startseite\".\r\n\r\nUm auf den nächsten Schritt zu kommen, klicken Sie bitte rechts unten auf \"Weiter\".','B',0,'','dispatch.php/start','','','root@localhost',1405934926,0),
('49604a77654617a745e29ad6b253e491',2,'Individuelle Gestaltung der Startseite','Die Startseite ist standardmäßig so konfiguriert, dass die Elemente \"Schnellzugriff\", \"Ankündigungen\", \"Meine aktuellen Termine\" und  \"Umfragen\" angezeigt werden. Die Elemente werden Widgets genannt und  können entfernt, hinzugefügt und verschoben werde.n Jedes Widget kann individuell hinzugefügt, entfernt und verschoben werden.','TL',0,'','dispatch.php/start','','','',1405934970,0),
('49604a77654617a745e29ad6b253e491',3,'Widget hinzufügen','Hier können Widgets hinzugefügt werden. Zusätzlich zu den Standard-Widgets kann beispielsweise der persönliche Stundenplan auf der Startseite anzeigt werden. Neu hinzugefügte Widgets erscheinen ganz unten auf der Startseite. Darüber hinaus kann in der Sidebar direkt zu jedem Widget gesprungen werden.','R',0,'#layout-sidebar SECTION:eq(0)  DIV:eq(5)  DIV:eq(0)','dispatch.php/start','','','',1405935192,0),
('49604a77654617a745e29ad6b253e491',4,'Sprungmarken','Darüber hinaus kann mit Sprungmarken direkt zu jedem Widget gesprungen werden.','R',0,'#layout-sidebar SECTION:eq(0)  DIV:eq(2)  DIV:eq(0)','dispatch.php/start','','','',1406623464,0),
('49604a77654617a745e29ad6b253e491',5,'Widget positionieren','Ein Widget kann per Drag&Drop an die gewünschte Position verschoben werden: Dazu wird in die Titelzeile eines Widgets geklickt, die Maustaste gedrückt gehalten und das Widget an die gewünschte Position gezogen.','B',0,'.widget-header','dispatch.php/start','','','',1405935687,0),
('49604a77654617a745e29ad6b253e491',6,'Widget bearbeiten','Bei einigen Widgets wird neben dem X zum Schließen noch ein weiteres Symbol angezeigt. Der Schnellzugriff bspw. kann durch Klick auf diesen Button individuell angepasst, die Ankündigungen können abonniert und bei den aktuellen Terminen bzw. Stundenplan können Termine hinzugefügt werden.','L',0,'#widget-8','dispatch.php/start','','','',1405935792,0),
('49604a77654617a745e29ad6b253e491',7,'Widget entfernen','Jedes Widget kann durch Klicken auf das X in der rechten oberen Ecke entfernt werden. Bei Bedarf kann es jederzeit wieder hinzugefügt werden.','R',0,'.widget-header','dispatch.php/start','','','',1405935376,0),
('4d41c9760a3248313236af202275107a',1,'Allgemeines zum Wiki','Diese Tour gibt einen allgemeinen Überblick über das Wiki.\r\n\r\nUm zum nächsten Schritt zu gelangen, klicken Sie bitte rechts unten auf \"Weiter\".','T',0,'','wiki.php','','','',1441276241,0),
('4d41c9760a3248313236af202275107a',2,'Kooperative Textarbeit','Das Wiki ist ein Tool für kooperative Textarbeit. Alle Teilnehmenden einer Veranstaltung haben das Recht, Texte zu erstellen, zu ändern und zu löschen.','B',0,'','wiki.php','','','',1441276241,0),
('4d41c9760a3248313236af202275107a',3,'Textänderungen schaden nicht','Weil das Wiki alle Textänderungen einer Seite protokolliert, können vorhergehende Versionen der Seite wiederhergestellt werden.','B',0,'','wiki.php','','','',1441276241,0),
('4d41c9760a3248313236af202275107a',4,'Textänderungen zurücknehmen','Textänderungen in einer Wiki-Seite lassen sich rückgängig machen, indem eine vorhergehende Version der Seite wiederhergestellt wird.','B',0,'','wiki.php','','','',1441276241,0),
('4d41c9760a3248313236af202275107a',5,'Neue Version einer Wiki-Seite','Wird eine Wiki-Seite bearbeitet, so erfolgt die Übernahme der Textänderungen sofort beim Speichern. Eine neue Version der Seite wird dreißig Minuten nach der Speicherung erstellt.','B',0,'','wiki.php','','','',1441276241,0),
('4d41c9760a3248313236af202275107a',6,'Kein synchrones Schreiben','Das Wiki ist nicht zum synchronen Schreiben geeignet. Es kann immer nur eine Person an einer Seite gleichzeitig arbeiten. Sobald eine zweite Person die Seite im Editor öffnet, erscheint eine Warnmeldung.','B',0,'','wiki.php','','','',1441276241,0),
('4d41c9760a3248313236af202275107b',1,'Schreiben im Wiki','Diese Tour gibt einen Überblick über die Erstellung und Bearbeitung von Wiki-Seiten.\r\n\r\nUm zum nächsten Schritt zu gelangen, klicken Sie bitte rechts unten auf \"Weiter\".','T',0,'','wiki.php','','','',1441276241,0),
('4d41c9760a3248313236af202275107b',2,'Wiki-Startseite','Zeigt die Basis-Seite des Wikis an. Sie bildet die strukturelle Grundlage des gesamten Wikis.','R',0,'#nav_wiki_start','wiki.php','','','dozent@studip.de',1441276241,0),
('4d41c9760a3248313236af202275107b',3,'Neue Seiten','Zeigt eine tabellarische Übersicht neu erstellter und neu bearbeiteter Wiki-Seiten an.','R',0,'#nav_wiki_listnew','wiki.php','','','',1441276241,0),
('4d41c9760a3248313236af202275107b',4,'Alle Seiten','Zeigt eine tabellarische Übersicht aller Wiki-Seiten an.','R',0,'#nav_wiki_listall','wiki.php','','','',1441276241,0),
('4d41c9760a3248313236af202275107b',5,'Wiki-Seite bearbeiten','Durch einen Klick auf die Schaltfläche \"Bearbeiten\" öffnet sich ein Editor, über den eine Wiki-Seite mit Inhalt gefüllt werden kann.\r\n\r\nDie Eingabe eines Namens in doppelten eckigen Klammern erzeugt eine neue Wiki-Seite und vernetzt sie mit der angezeigten Seite.','B',0,'#main_content TABLE:eq(1)  TBODY:eq(0)  TR:eq(0)  TD:eq(0)  DIV:eq(0)  A:eq(0)','wiki.php','','','',1441276241,0),
('4d41c9760a3248313236af202275107b',6,'Inhalt einer Wiki-Seite löschen','Der Inhalt einer Wiki-Seite lässt sich mit Hilfe eines Klicks auf die Schaltfläche \"Löschen\" entfernen. Die Wiki-Seite bleibt dabei erhalten.','B',0,'#main_content TABLE:eq(1)  TBODY:eq(0)  TR:eq(0)  TD:eq(0)  DIV:eq(0)  A:eq(1)','wiki.php','','','',1441276241,0),
('4d41c9760a3248313236af202275107b',7,'QuickLinks','Dieser Bildschirmbereich zeigt eine Liste von QuickLinks (Verweisen) auf Wiki-Seiten. Ein Klick auf einen QuickLink öffnet die korrelierende Wiki-Seite. Deren Inhalt lässt sich mit Hilfe der Schaltflächen \"Bearbeiten\" und \"Löschen\" gestalten.','R',0,'#layout-sidebar SECTION:eq(0)  DIV:eq(6)  DIV:eq(0)','wiki.php','','','',1441276241,0),
('4d41c9760a3248313236af202275107b',8,'QuickLinks bearbeiten','Über das Icon zum Bearbeiten von QuickLinks öffnet sich ein Editor.\r\n\r\nNeue QuickLinks lassen sich mit doppelten eckigen Klammern erstellen: [[Name]]. Das Löschen eines QuickLinks entfernt die korrelierende Seite aus der Liste.','R',0,'#layout-sidebar SECTION:eq(0)  DIV:eq(6)  DIV:eq(0)','wiki.php','','','root@localhost',1441276241,0),
('4d41c9760a3248313236af202275107c',1,'Lesen im Wiki','Diese Tour gibt einen Überblick über die Anzeige von Wiki-Seiten.\r\n\r\nUm zum nächsten Schritt zu gelangen, klicken Sie bitte rechts unten auf \"Weiter\".','T',0,'','wiki.php','','','',1441276241,0),
('4d41c9760a3248313236af202275107c',2,'Wiki-Startseite','Zeigt die Basis-Seite des Wikis an.','R',0,'#nav_wiki_show','wiki.php','','','dozent@studip.de',1441276241,0),
('4d41c9760a3248313236af202275107c',3,'Neue Seiten','Zeigt eine tabellarische Übersicht neu erstellter und neu bearbeiteter Wiki-Seiten an.','R',0,'#nav_wiki_listnew','wiki.php','','','',1441276241,0),
('4d41c9760a3248313236af202275107c',4,'Alle Seiten','Zeigt eine tabellarische Übersicht aller Wiki-Seiten an.','R',0,'#nav_wiki_listall','wiki.php','','','',1441276241,0),
('4d41c9760a3248313236af202275107c',5,'Ansichten','Wenn eine Textänderung in einer Wiki-Seite vorgenommen wurde, stehen drei Anzeigemodi zur Auswahl:\r\n- Standard: Ohne Zusatzinformation\r\n- Textänderungen anzeigen: Welche Textpassagen wurden geändert?\r\n- Text mit AutorInnenzuordnung anzeigen: Wer hat hat etwas geändert?','R',0,'#layout-sidebar SECTION:eq(0)  DIV:eq(16)  DIV:eq(0)','wiki.php','','','',1441276241,0),
('4d41c9760a3248313236af202275107c',6,'Suche','Zeigt die Wiki-Seiten an, in denen der eingegebene Suchbegriff vorkommt. Die Suche steht nur in der Standard-Ansicht zur Verfügung.','R',0,'#layout-sidebar SECTION:eq(0)  DIV:eq(13)  DIV:eq(0)','wiki.php','','','',1441276241,0),
('4d41c9760a3248313236af202275107c',7,'Kommentare','Stellt verschiedene Modalitäten zur Anzeige von Kommentaren bereit, die in einer Wiki-Seite eingetragen wurden.','R',0,'#link-76d39424649110401006432124ea88a2 A:eq(0)','wiki.php','','','',1441276241,0),
('4d41c9760a3248313236af202275107c',8,'Kommentare einblenden','Alle Kommentare werden als Textblock an der Textposition angezeigt, an der sie in die Wiki-Seite eingefügt wurden.','R',0,'#link-76d39424649110401006432124ea88a2 A:eq(0)','wiki.php','','','',1441276241,0),
('4d41c9760a3248313236af202275107c',9,'Kommentare ausblenden','Die in einer Wiki-Seite eingefügten Kommentare werden nicht angezeigt.','R',0,'#link-b7e236be73b877f765813669fba3d56e A:eq(0)','wiki.php','','','',1441276241,0),
('55f3a548348dcbfdca67678588887ffd',1,'','Dies ist Ihr persönlicher Arbeitsplatz in Stud.IP. Hier sind Funktionen, die früher unter \"Tools\" zu finden waren, sowie neue Werkzeuge gesammelt.','B',0,'','dispatch.php/contents/overview','','','root@localhost',1630577268,1630577268),
('55f3a548348dcbfdca67678588887ffd',2,'','Courseware dient zum Erstellen von Lerninhalten. Texte, Dateien, Videos, Test und vieles mehr lassen sich einfach zu komplexen Lerninhalten kombinieren.','RT',0,'#layout_content UL:eq(0)  LI:eq(0)  A:eq(0)  DIV:eq(0)  IMG:eq(0)','dispatch.php/contents/overview','','','dozent@studip.de',1630577268,1630577268),
('55f3a548348dcbfdca67678588887ffd',3,'','Dateien zeigt Ihren persönlichen, geschützten Dateibereich sowie eine Übersichtsseite über alle Dateien, auf die Sie in Stud.IP Zugriff haben.','RT',0,'#layout_content UL:eq(0)  LI:eq(1)  A:eq(0)  DIV:eq(0)','dispatch.php/contents/overview','','','dozent@studip.de',1630577268,1630577268),
('55f3a548348dcbfdca67678588887ffd',4,'','Ankündigungen ermöglicht das Einstellen von News auf Ihrer Profilseite, in Veranstaltungen in denen Sie lehren oder Einrichtungen, die Sie administrieren.','LT',0,'#layout_content UL:eq(0)  LI:eq(2)  A:eq(0)  DIV:eq(0)  IMG:eq(0)','dispatch.php/contents/overview','','','dozent@studip.de',1630577268,1630577268),
('55f3a548348dcbfdca67678588887ffd',5,'','Fragebögen zeigt alle Fragebögen die Sie erstellt haben und die sich im persönlichen, Veranstaltungs- oder Einrichungskontext nutzen lassen.','LT',0,'#layout_content UL:eq(0)  LI:eq(3)  A:eq(0)  DIV:eq(0)  IMG:eq(0)','dispatch.php/contents/overview','','','dozent@studip.de',1630577268,1630577268),
('55f3a548348dcbfdca67678588887ffd',6,'','Evaluationen bietet einen Baukasten zum Erstellen komplexer Umfragen sowie deren Nutzung im persönlichen, Veranstaltungs- oder Einrichungskontext.','T',0,'#layout_content UL:eq(0)  LI:eq(4)  A:eq(0)  DIV:eq(0)  IMG:eq(0)','dispatch.php/contents/overview','','','dozent@studip.de',1630577268,1630577268),
('55f3a548348dcbfdca67678588887ffd',7,'','Lernmodule/ILIAS bietet je nach Standort Zugriff auf die Accountverwaltung von Plattformen und Werkzeugen, die an Stud.IP angedockt sind.','T',0,'#layout_content UL:eq(0)  LI:eq(5)  A:eq(0)  DIV:eq(0)  IMG:eq(0)','dispatch.php/contents/overview','','','dozent@studip.de',1630577268,1630577268),
('588effa83da976a889a68c152bcabc90',1,'What is Blubber?','This tour provides an overview of the functionality of \"Blubber\".\r\n\rTo proceed, please click \"Continue\" in the lower-right corner.','TL',0,'','plugins.php/blubber/streams/profile','','','',1405507364,0),
('588effa83da976a889a68c152bcabc90',2,'Create contribution','A discussion can be started here by writing a text. Paragraphs can be created by pressing shift+enter. The text will be sent by pressing enter.','BL',0,'TEXTAREA#new_posting.autoresize','plugins.php/blubber/streams/profile','','','',1405507478,0),
('588effa83da976a889a68c152bcabc90',3,'Design text','The text can be formatted and smileys can be used.\n\nThe customary formatting such as e.g. **bold** or %%italics%%  can be used.','BL',0,'TEXTAREA#new_posting.autoresize','plugins.php/blubber/streams/profile','','','',1405508371,0),
('588effa83da976a889a68c152bcabc90',4,'Mention persons','Others can be informed about a post by mentioning them in the post, using the format @user name or @\'first name last name\'.','BL',0,'TEXTAREA#new_posting.autoresize','plugins.php/blubber/streams/profile','','','',1405672301,0),
('588effa83da976a889a68c152bcabc90',5,'Add document','Documents can be inserted into a post by dragging them into an input field using drag&drop.','BL',0,'TEXTAREA#new_posting.autoresize','plugins.php/blubber/streams/profile','','','',1405508401,0),
('588effa83da976a889a68c152bcabc90',6,'Hashtags','Posts can be issued with key words (\"hashtags\") by placing a # in front of the chosen word.','BL',0,'TEXTAREA#new_posting.autoresize','plugins.php/blubber/streams/profile','','','',1405508442,0),
('588effa83da976a889a68c152bcabc90',7,'Hashtag cloud','By clicking on a hashtag, all posts containing this hashtag will be displayed.','RT',0,'DIV.sidebar-widget-header','plugins.php/blubber/streams/profile','','','',1405508505,0),
('588effa83da976a889a68c152bcabc90',8,'Change contribution','If the cursor is positioned on a post, its date will appear. For your own posts an additional icon will appear on the right next to the date. This icon allow you to subsequently edit your post.','BR',0,'DIV DIV A SPAN.time','plugins.php/blubber/streams/profile','','','',1405507901,0),
('588effa83da976a889a68c152bcabc90',9,'Link contribution','If the cursor is positioned on the first contribution to the discussion a link icon will appear on the left next to the date. If this is clicked using the right mouse button the link can be copied on this contribution in order to be able to insert it in another place.','BR',0,'DIV DIV A.permalink','plugins.php/blubber/streams/profile','','','',1405508281,0),
('5d41c9760a3248313236af202275107a',1,'General information on the Wiki','This tour provides general information about the Wiki.\r\n\r\nTo proceed, please click \"Continue\" on the lower-right button.','T',0,'','wiki.php','','','',1441276241,0),
('5d41c9760a3248313236af202275107a',2,'Tool for collaborative use','The Wiki is a collaborative tool. Every user may create, edit and delete content.','B',0,'','wiki.php','','','',1441276241,0),
('5d41c9760a3248313236af202275107a',3,'Changes in a Wiki page','Since all changes in a Wiki page are saved in a protocol, previous versions of its content can be restored.','B',0,'','wiki.php','','','',1441276241,0),
('5d41c9760a3248313236af202275107a',4,'New version of a Wiki page','While editing text in a Wiki page, clicking the Save-Button will save its content immediately. A new version of a Wiki page is displayed thirty minutes after saving at the latest.','B',0,'','wiki.php','','','',1441276241,0),
('5d41c9760a3248313236af202275107a',5,'Undo changes','All changes can be undone by restoring a previous version of text.','B',0,'','wiki.php','','','',1441276241,0),
('5d41c9760a3248313236af202275107a',6,'No support of synchronous editing','The editor is not designed for synchronous writing. Only one person may edit a page at the same time. If a second person links up to edit the same page, a warning message appears.','B',0,'','wiki.php','','','',1441276241,0),
('5d41c9760a3248313236af202275107b',1,'Editing the Wiki','This tour provides a general overview of how to create and edit Wiki pages.\r\n\r\nTo proceed, please click \"Continue\" on the lower-right button.','T',0,'','wiki.php','','','',1441276241,0),
('5d41c9760a3248313236af202275107b',2,'WikiWikiWeb','Displays the basic Wiki page, which is the foundation of all further Wiki pages.','R',0,'#nav_wiki_start','wiki.php','','','dozent@studip.de',1441276241,0),
('5d41c9760a3248313236af202275107b',3,'New pages','Displays a survey of all recently created or edited Wiki pages in table form.','R',0,'#nav_wiki_listnew','wiki.php','','','',1441276241,0),
('5d41c9760a3248313236af202275107b',4,'All pages','Displays a survey of all Wiki pages in table form.','R',0,'#nav_wiki_listall','wiki.php','','','',1441276241,0),
('5d41c9760a3248313236af202275107b',5,'Editing the a Wiki page','Clicking here will open an editor, allowing to fill a Wiki page with content.','B',0,'#main_content TABLE:eq(1)  TBODY:eq(0)  TR:eq(0)  TD:eq(0)  DIV:eq(0)  A:eq(0)','wiki.php','','','',1441276241,0),
('5d41c9760a3248313236af202275107b',6,'Deleting the content of a Wiki page','Clicking here will delete all content and links of a Wiki page leaving it blank.','B',0,'#main_content TABLE:eq(1)  TBODY:eq(0)  TR:eq(0)  TD:eq(0)  DIV:eq(0)  A:eq(1)','wiki.php','','','',1441276241,0),
('5d41c9760a3248313236af202275107b',7,'QuickLinks','This box displays links, leading to other Wiki pages. Selecting a link will forward to the related page.','R',0,'#layout-sidebar SECTION:eq(0)  DIV:eq(6)  DIV:eq(0)','wiki.php','','','',1441276241,0),
('5d41c9760a3248313236af202275107b',8,'Editing QuickLinks','A click on this icon will open an editor to edit the QuickLinks.\r\n\r\nEntering a name within double square brackets like [[name]] in the editor will create a new QuickLink leading to a correlating page. Deleting a QuickLink will cause its deletion in the QuickLink box.','R',0,'#layout-sidebar SECTION:eq(0)  DIV:eq(6)  DIV:eq(0)','wiki.php','','','root@localhost',1441276241,0),
('5d41c9760a3248313236af202275107c',1,'Reading the Wiki','This tour gives a general overview of the different modes to read Wiki pages.\r\n\r\nTo proceed, please click \"Continue\" on the lower-right button.','T',0,'','wiki.php','','','',1441276241,0),
('5d41c9760a3248313236af202275107c',2,'WikiWikiWeb','Displays the basic Wiki page, which is the foundation of all further Wiki pages.','R',0,'#nav_wiki_show','wiki.php','','','dozent@studip.de',1441276241,0),
('5d41c9760a3248313236af202275107c',3,'New pages','Displays a survey of all recently created or edited Wiki pages in table form.','R',0,'#nav_wiki_listnew','wiki.php','','','',1441276241,0),
('5d41c9760a3248313236af202275107c',4,'All pages','Displays a survey of all Wiki pages in table form.','R',0,'#nav_wiki_listall','wiki.php','','','',1441276241,0),
('5d41c9760a3248313236af202275107c',5,'Views','If a Wiki page has been edited, the user may choose between three modes of viewing content:\r\n- Standard: Without extra information\r\n- Show text changes: Which parts of text have been edited?\r\n- Show text changes and associated author: Who was editing a part of text?','R',0,'#layout-sidebar SECTION:eq(0)  DIV:eq(16)  DIV:eq(0)','wiki.php','','','',1441276241,0),
('5d41c9760a3248313236af202275107c',6,'Search','Shows all Wiki pages which contain the entered search term. The search is supported in Standard-View only.','R',0,'#layout-sidebar SECTION:eq(0)  DIV:eq(13)  DIV:eq(0)','wiki.php','','','',1441276241,0),
('5d41c9760a3248313236af202275107c',7,'Comments','Supports three modes of showing comments added to a Wiki page.','R',0,'#link-76d39424649110401006432124ea88a2 A:eq(0)','wiki.php','','','',1441276241,0),
('5d41c9760a3248313236af202275107c',8,'Show comments','All comments are shown as a block of text exactly in that position, in which they were added.','R',0,'#link-76d39424649110401006432124ea88a2 A:eq(0)','wiki.php','','','',1441276241,0),
('5d41c9760a3248313236af202275107c',9,'Hide comments','All added comments are hidden while displaying a page.','R',0,'#link-b7e236be73b877f765813669fba3d56e A:eq(0)','wiki.php','','','',1441276241,0),
('6849293baa05be5bef8ff438dc7c438b',1,'Suche','Diese Tour gibt Ihnen einen Überblick über die wichtigsten Funktionen der \"Suche\".\r\n\r\nUm auf den nächsten Schritt zu kommen, klicken Sie bitte rechts unten auf \"Weiter\".','B',0,'','dispatch.php/search/globalsearch','','','root@localhost',1405519865,0),
('6849293baa05be5bef8ff438dc7c438b',2,'Suchbegriff eingeben','In dieses Eingabefeld kann ein Suchbegriff (wie z.B. der Veranstaltungsname, Lehrperson) eingegeben werden.','B',0,'#search-input','dispatch.php/search/globalsearch','','','root@localhost',1405520106,0),
('6849293baa05be5bef8ff438dc7c438b',3,'Semesterauswahl','Durch einen Klick auf das Drop-Down Menü kann bestimmt werden, auf welches Semester sich der Suchbegriff beziehen soll. \r\n\r\nStandardgemäß ist das aktuelle Semester eingestellt.','TL',0,'#semester_filter DIV:eq(0)','dispatch.php/search/globalsearch','','','root@localhost',1405520208,0),
('6849293baa05be5bef8ff438dc7c438b',4,'Navigation','Falls nur in einem bestimmten Bereich (wie z.B. Lehre) gesucht werden soll, kann dieser hier ausgewählt werden.','BL',0,'#tabs','dispatch.php/search/globalsearch','','','dozent@studip.de',1406121826,0),
('6849293baa05be5bef8ff438dc7c438b',5,'Schnellsuche','Die Schnellsuche ist auch auf anderen Seiten von Stud.IP jederzeit verfügbar. Nach der Eingabe eines Stichwortes, wird mit \"Enter\" bestätigt, oder auf die Lupe rechts neben dem Feld geklickt.','B',0,'#globalsearch-input','dispatch.php/search/globalsearch','','','root@localhost',1405520634,0),
('6849293baa05be5bef8ff438dc7c438b',6,'Weitere Suchmöglichkeiten','Neben Veranstaltungen besteht auch die Möglichkeit, im Archiv, nach Personen, nach Einrichtungen oder nach Ressourcen zu suchen.','R',0,'#nav_search_resources A SPAN','dispatch.php/search/globalsearch','','','root@localhost',1405520751,0),
('7af1e1fb7f53c910ba9f42f43a71c723',1,'Search','This tour provides an overview of the supplied search options.\r\n\rTo proceed, please click \"Continue\" in the lower-right corner.','B',0,'','dispatch.php/search/globalsearch','','','root@localhost',1405519865,0),
('7af1e1fb7f53c910ba9f42f43a71c723',2,'Enter search term','A search term (such as event name, lecturer) can be entered in this input field.','B',0,'#search-input','dispatch.php/search/globalsearch','','','root@localhost',1405520106,0),
('7af1e1fb7f53c910ba9f42f43a71c723',3,'Semester selection','With a click on the drop-down menu you can choose to which semester the search term should refer. \n\nThe current semester is set as standard.','TL',0,'#semester_filter DIV:eq(0)','dispatch.php/search/globalsearch','','','root@localhost',1405520208,0),
('7af1e1fb7f53c910ba9f42f43a71c723',4,'Navigation','If you want to search only one particular area, you can select one here.','BL',0,'#tabs','dispatch.php/search/globalsearch','','','dozent@studip.de',1406121826,0),
('7af1e1fb7f53c910ba9f42f43a71c723',5,'Quick search','The quick search is also available on other sites of Stud.IP at all times. After entering a key word it is confirmed with \"Enter\" or by clicking the magnifying glass on the right next to the field.','B',0,'#globalsearch-input','dispatch.php/search/globalsearch','','','root@localhost',1405520634,0),
('7af1e1fb7f53c910ba9f42f43a71c723',6,'Further search possibilities','In addition to searching for events there is also the possibility to search the archive for persons, facilities, or resources.','R',0,'#nav_search_resources A SPAN','dispatch.php/search/globalsearch','','','root@localhost',1405520751,0),
('7cccbe3b22dfa745c17cb776fb04537c',1,'Meine Veranstaltungen','Diese Tour gibt einen Überblick über die wichtigsten Funktionen der Seite \"Meine Veranstaltungen\".\r\n\r\nUm auf den nächsten Schritt zu kommen, klicken Sie bitte rechts unten auf \"Weiter\".','TL',0,'','dispatch.php/my_courses','','','',1406125847,0),
('7cccbe3b22dfa745c17cb776fb04537c',2,'Veranstaltungsüberblick','Hier werden die  Veranstaltungen des aktuellen und vergangenen Semesters angezeigt. Neue Veranstaltungen erscheinen zunächst in rot.','BL',0,'#my_seminars TABLE:eq(0)  CAPTION:eq(0)','dispatch.php/my_courses','','','dozent@studip.de',1406125908,0),
('7cccbe3b22dfa745c17cb776fb04537c',3,'Veranstaltungsdetails','Mit Klick auf das \"i\" erscheint ein Fenster mit den wichtigsten Eckdaten der Veranstaltung.','BR',0,'#my_seminars .action-menu-icon','dispatch.php/my_courses','','#my_seminars TABLE:eq(0)  TBODY:eq(0)  TR:eq(1)  TD:eq(5)  NAV:eq(0)','dozent@studip.de',1406125992,0),
('7cccbe3b22dfa745c17cb776fb04537c',4,'Veranstaltungsinhalte','Hier werden alle Inhalte (wie z.B. ein Forum) durch entsprechende Symbole angezeigt.\r\nFalls es seit dem letzten Login Neuigkeiten gab, erscheinen diese in rot.','B',0,'#my_seminars .my-courses-navigation-item','dispatch.php/my_courses','','','dozent@studip.de',1406126049,0),
('7cccbe3b22dfa745c17cb776fb04537c',5,'Bearbeitung oder Löschung einer Veranstaltung','Der Klick auf das Zahnrad ermöglicht die Bearbeitung einer Veranstaltung.\r\nFalls bei einer Veranstaltung Teilnehmerstatus besteht, kann hier eine Austragung, durch Klick auf das Tür-Icon, vorgenommen werden.','BR',0,'#my_seminars .action-menu-icon','dispatch.php/my_courses','','','dozent@studip.de',1406126134,0),
('7cccbe3b22dfa745c17cb776fb04537c',6,'Anpassung der Veranstaltungsansicht','Zur Anpassung der Veranstaltungsübersicht, kann man die Veranstaltungen nach bestimmten Kriterien (wie z.B. Studienbereiche, Lehrende oder Farben) gliedern.','R',0,'#layout-sidebar SECTION:eq(0)  DIV:eq(11)  DIV:eq(0)','dispatch.php/my_courses','','','',1406126281,0),
('7cccbe3b22dfa745c17cb776fb04537c',7,'Zugriff auf Veranstaltung vergangener und zukünftiger Semester','Durch Klick auf das Drop-Down Menü können beispielsweise Veranstaltung aus vergangenen Semestern angezeigt werden.','R',0,'#layout-sidebar SECTION:eq(0)  DIV:eq(5)  DIV:eq(1)','dispatch.php/my_courses','','','',1406126316,0),
('7cccbe3b22dfa745c17cb776fb04537c',8,'Weitere mögliche Aktionen','Hier können Sie alle Neuigkeiten als gelesen markieren, Farbgruppierungen nach Belieben ändern oder\r\nauch die Benachrichtigungen über Aktivitäten in den einzelnen Veranstaltungen anpassen.','R',0,'#layout-sidebar SECTION:eq(0)  DIV:eq(8)  DIV:eq(0)','dispatch.php/my_courses','','','',1406126374,0),
('7cccbe3b22dfa745c17cb776fb04537c',9,'Studiengruppen und Einrichtungen','Es besteht zudem die Möglichkeit auf persönliche Studiengruppen oder Einrichtungen zuzugreifen.','R',0,'#nav_browse_my_institutes A','dispatch.php/my_courses','','','',1406126415,0),
('83dc1d25e924f2748ee3293aaf0ede8e',1,'What is Blubber?','This tour provides an overview of the functionality of \"Blubber\".\r\n\rTo proceed, please click \"Continue\" in the lower-right corner.','TL',0,'','plugins.php/blubber/streams/forum','','','',1405507364,0),
('83dc1d25e924f2748ee3293aaf0ede8e',2,'Create contribution','A discussion can be started here by writing a text. Paragraphs can be created by pressing shift+enter. The text will be sent by pressing enter.','BL',0,'TEXTAREA#new_posting.autoresize','plugins.php/blubber/streams/forum','','','',1405507478,0),
('83dc1d25e924f2748ee3293aaf0ede8e',3,'Design text','The text can be formatted and smileys can be used.\n\nThe customary formatting such as e.g. **bold** or %%italics%%  can be used.','BL',0,'TEXTAREA#new_posting.autoresize','plugins.php/blubber/streams/forum','','','',1405508371,0),
('83dc1d25e924f2748ee3293aaf0ede8e',4,'Mention persons','Others can be informed about a post by mentioning them in the post, using the format @user name or @\'first name last name\'.','BL',0,'TEXTAREA#new_posting.autoresize','plugins.php/blubber/streams/forum','','','',1405672301,0),
('83dc1d25e924f2748ee3293aaf0ede8e',5,'Add document','Documents can be inserted into a post by dragging them into an input field using drag&drop.','BL',0,'TEXTAREA#new_posting.autoresize','plugins.php/blubber/streams/forum','','','',1405508401,0),
('83dc1d25e924f2748ee3293aaf0ede8e',6,'Hashtags','Posts can be issued with key words (\"hashtags\") by placing a # in front of the chosen word.','BL',0,'TEXTAREA#new_posting.autoresize','plugins.php/blubber/streams/forum','','','',1405508442,0),
('83dc1d25e924f2748ee3293aaf0ede8e',7,'Hashtag cloud','By clicking on a hashtag, all posts containing this hashtag will be displayed.','RT',0,'DIV.sidebar-widget-header','plugins.php/blubber/streams/forum','','','',1405508505,0),
('83dc1d25e924f2748ee3293aaf0ede8e',8,'Change contribution','If the cursor is positioned on a post, its date will appear. For your own posts an additional icon will appear on the right next to the date. This icon allow you to subsequently edit your post.','BR',0,'DIV DIV A SPAN.time','plugins.php/blubber/streams/forum','','','',1405507901,0),
('83dc1d25e924f2748ee3293aaf0ede8e',9,'Link contribution','If the cursor is positioned on the first contribution to the discussion a link icon will appear on the left next to the date. If this is clicked using the right mouse button the link can be copied on this contribution in order to be able to insert it in another place.','BR',0,'DIV DIV A.permalink','plugins.php/blubber/streams/forum','','','',1405508281,0),
('89786eac42f52ac316790825b4f5c0b2',1,'Forum','This tour provides an overview of the forum\'s elements and options of interaction.\r\n\rTo proceed, please click \"Continue\" in the lower-right corner.','BL',0,'','dispatch.php/course/forum','','','',1405415772,0),
('89786eac42f52ac316790825b4f5c0b2',2,'You are here:...','Here you can see which sector of the forum you are currently looking at.','BL',0,'DIV#tutorBreadcrumb','dispatch.php/course/forum','','','',1405415875,0),
('89786eac42f52ac316790825b4f5c0b2',3,'Category','The forum is divided into categories, topics and posts. A category summarises forum areas into larger units of meaning.','BL',0,'#tutorCategory','dispatch.php/course/forum','','','',1405416611,0),
('89786eac42f52ac316790825b4f5c0b2',4,'Area','This is an area within a category. Areas contain threads. The order of areas can be altered using drag&drop','BL',0,'#sortable_areas TABLE:eq(0)  TBODY:eq(0)  TR:eq(0)  TD:eq(1)  DIV:eq(0)  SPAN:eq(0)  A:eq(0)  SPAN:eq(0)','dispatch.php/course/forum','','','',1405416664,0),
('89786eac42f52ac316790825b4f5c0b2',5,'Info-Icon','This icon turns red as soon as there is something new in this sector.','B',0,'#sortable_areas TABLE:eq(0)  TBODY:eq(0)  TR:eq(0)  TD:eq(0)  A:eq(0)  IMG:eq(0)','dispatch.php/course/forum','','','',1405416705,0),
('89786eac42f52ac316790825b4f5c0b2',6,'Search','All contents of this forum can be browsed here. Multiple word searches are also supported. In addition, the search can be limited to any combination of title, content and author.','BL',0,'#tutorSearchInfobox DIV:eq(0)','dispatch.php/course/forum','','','',1405417134,0),
('89786eac42f52ac316790825b4f5c0b2',7,'Subscribe to forum','You can subscribe to the whole forum or individual topics . In this case a notification will be generated and you receive a meassage for each new post in this forum.','RT',0,'#layout-sidebar SECTION:eq(0)  DIV:eq(9)  DIV:eq(0)','dispatch.php/course/forum','','','dozent@studip.de',1405416795,0),
('96ea422f286fb5bbf9e41beadb484a9a',1,'Profil-Tour','Diese Tour gibt Ihnen einen Überblick über die wichtigsten Funktionen des \"Profils\".\r\n\r\nUm auf den nächsten Schritt zu kommen, klicken Sie bitte rechts unten auf \"Weiter\".','T',0,'','dispatch.php/profile','','','',1406722657,0),
('96ea422f286fb5bbf9e41beadb484a9a',2,'Persönliches Bild','Wenn ein Bild hochgeladen wurde, wird es hier angezeigt. Dieses kann jederzeit geändert werden.','RT',0,'.avatar-normal','dispatch.php/profile','','','',1406722657,0),
('96ea422f286fb5bbf9e41beadb484a9a',3,'Stud.IP-Score','Der Stud.IP-Score wächst mit den Aktivitäten in Stud.IP und repräsentiert so die Erfahrung mit Stud.IP.','BL',0,'#layout_content TABLE:eq(0) TBODY:eq(0) TR:eq(0) TD:eq(0) A:eq(0)','dispatch.php/profile','','','',1406722657,0),
('96ea422f286fb5bbf9e41beadb484a9a',4,'Ankündigungen','Sie können auf dieser Seite persönliche Ankündigungen veröffentlichen.','B',0,'#layout_content ARTICLE:eq(0)  HEADER:eq(0)  H1:eq(0)','dispatch.php/profile','','','',1406722657,0),
('96ea422f286fb5bbf9e41beadb484a9a',5,'Neue Ankündigung','Klicken Sie auf das Plus-Zeichen, wenn Sie eine Ankündigung erstellen möchten.','BR',0,'#layout_content ARTICLE:eq(0)  HEADER:eq(0)  NAV:eq(0)  A:eq(0)','dispatch.php/profile','','','',1406722657,0),
('96ea422f286fb5bbf9e41beadb484a9a',6,'Persönliche Angaben','Weitere persönliche Angaben und Einstellungen können über diese Seiten geändert werden.','B',0,'#tabs li:eq(2)','dispatch.php/profile','','','dozent@studip.de',1406722657,0),
('9e9dca9b1214294b9605824bfe90fba1',1,'Create study group','This tour provides an overview of the creation of study groups to cooperate with fellow students.\r\n\rTo proceed, please click \"Continue\" in the lower-right corner.','R',0,'','dispatch.php/my_studygroups','','','',1405684423,0),
('9e9dca9b1214294b9605824bfe90fba1',2,'Create study group','A new study group can be created with a click on \"create new study group\".','BL',0,'.sidebar-widget:eq(1) A:eq(0)','dispatch.php/my_studygroups','','.sidebar-widget:eq(1) li:eq(0) a:eq(0)','dozent@studip.de',1406017730,0),
('9e9dca9b1214294b9605824bfe90fba1',3,'Name a study group','The name of a study group should be meaningful and unique in the whole Stud.IP.','R',0,'#wizard-name','dispatch.php/my_studygroups','','','',1405684720,0),
('9e9dca9b1214294b9605824bfe90fba1',4,'Add description','The description makes it possible to display additional information that makes it easier to find the group.','L',0,'#wizard-description','dispatch.php/my_studygroups','','','dozent@studip.de',1405684806,0),
('9e9dca9b1214294b9605824bfe90fba1',5,'Allocate content elements','Content elements can be activated here, which are to be available within the study group. The question mark provides more detailed information on the meaning of the individual content elements','R',0,'#wizard-access','dispatch.php/my_studygroups','','','dozent@studip.de',1405685093,0),
('9e9dca9b1214294b9605824bfe90fba1',6,'Stipulate access','The access to the study group can be restricted with this drop down menu.\n\nAll students can register freely and participate in the group with the access \"open for everyone\".\n\nWith the access \"upon request\" participants must be added by the group founder.','R',0,'#wizard-access','dispatch.php/my_studygroups','','','root@localhost',1405685334,0),
('9e9dca9b1214294b9605824bfe90fba1',7,'Accept terms of use','The terms of use have to be accepted before you can create a study group.','R',0,'#ui-id-1 FORM:eq(0)  FIELDSET:eq(0)  LABEL:eq(4)','dispatch.php/my_studygroups','','','root@localhost',1405685652,0),
('9e9dca9b1214294b9605824bfe90fba1',8,'Save study group','After you saved a study group it will appear under \"My courses\" > \"My study groups\".','L',0,'#layout_content FORM TABLE TBODY TR TD :eq(14)','dispatch.php/my_studygroups','','BUTTON.cancel:eq(0)','root@localhost',1405686068,0),
('b74f8459dce2437463096d56db7c73b9',1,'Meine Veranstaltungen','Diese Tour gibt einen Überblick über die wichtigsten Funktionen der Seite \"Meine Veranstaltungen\".\r\n\r\nUm auf den nächsten Schritt zu kommen, klicken Sie bitte rechts unten auf \"Weiter\".','TL',0,'','dispatch.php/my_courses','','','',1405521184,0),
('b74f8459dce2437463096d56db7c73b9',2,'Veranstaltungsüberblick','Hier werden die  Veranstaltungen des aktuellen und vergangenen Semesters angezeigt. Neue Veranstaltungen erscheinen zunächst in rot.','BL',0,'#my_seminars TABLE:eq(0)  CAPTION:eq(0)','dispatch.php/my_courses','','','autor@studip.de',1405521244,0),
('b74f8459dce2437463096d56db7c73b9',3,'Veranstaltungsdetails','Mit Klick auf das \"i\" erscheint ein Fenster mit den wichtigsten Eckdaten der Veranstaltung.','L',0,'#my_seminars .action-menu-icon','dispatch.php/my_courses','','','autor@studip.de',1405931069,0),
('b74f8459dce2437463096d56db7c73b9',4,'Veranstaltungsinhalte','Hier werden alle Inhalte (wie z.B. ein Forum) durch entsprechende Symbole angezeigt.\r\nFalls es seit dem letzten Login Neuigkeiten gab, erscheinen diese in rot.','LT',0,'#my_seminars .my-courses-navigation-item','dispatch.php/my_courses','','','',1405931225,0),
('b74f8459dce2437463096d56db7c73b9',5,'Verlassen der Veranstaltung','Ein Klick auf das Tür-Icon ermöglicht eine direkte Austragung aus der Veranstaltung.','L',0,'#my_seminars .action-menu-icon','dispatch.php/my_courses','','','autor@studip.de',1405931272,0),
('b74f8459dce2437463096d56db7c73b9',6,'Anpassung der Veranstaltungsansicht','Zur Anpassung der Veranstaltungsübersicht können die Veranstaltungen nach bestimmten Kriterien (wie z.B. Studienbereiche, Lehrende oder Farben) gruppiert werden.','R',0,'#layout-sidebar SECTION:eq(0)  DIV:eq(11)  DIV:eq(0)','dispatch.php/my_courses','','','',1405932131,0),
('b74f8459dce2437463096d56db7c73b9',7,'Zugriff auf Veranstaltung vergangener und zukünftiger Semester','Durch Klick auf das Drop-Down Menü können beispielsweise Veranstaltung aus vergangenen Semestern angezeigt werden.','R',0,'#layout-sidebar SECTION:eq(0)  DIV:eq(5)  DIV:eq(0)','dispatch.php/my_courses','','','',1405932230,0),
('b74f8459dce2437463096d56db7c73b9',8,'Weitere mögliche Aktionen','Hier können Sie alle Neuigkeiten als gelesen markieren, Farbgruppierungen nach Belieben ändern oder\r\nauch die Benachrichtigungen über Aktivitäten in den einzelnen Veranstaltungen anpassen.','R',0,'#layout-sidebar SECTION:eq(0)  DIV:eq(8)  DIV:eq(0)','dispatch.php/my_courses','','','',1405932320,0),
('b74f8459dce2437463096d56db7c73b9',9,'Studiengruppen und Einrichtungen','Es besteht zudem die Möglichkeit auf persönliche Studiengruppen oder Einrichtungen zuzugreifen.','R',0,'#nav_browse_my_institutes A','dispatch.php/my_courses','','','',1405932519,0),
('c89ce8e097f212e75686f73cc5008711',1,'Participant administration','This tour provides an overview of the participant administration\'s options.\r\n\rTo proceed, please click \"Continue\" in the lower-right corner.','B',0,'','dispatch.php/course/members','','','',1405688399,0),
('c89ce8e097f212e75686f73cc5008711',2,'Add persons','With these functions you can search for individual persons in Stud.IP and directly  select them as lecturer, tutor or author. It is also possible to insert a list of participants in order to allocate several persons as a tutor of the event at the same time.','R',0,'#layout-sidebar SECTION:eq(0)  DIV:eq(6)  DIV:eq(0)','dispatch.php/course/members','','','',1405688707,0),
('c89ce8e097f212e75686f73cc5008711',3,'Upgrade/ downgrade','In order to upgrade an already enroled person to a tutor, or to downgrade them to a reader select this person in the list and carry out the requested action by using the dropdown menu.','T',0,'#autor CAPTION','dispatch.php/course/members','','','',1405690324,0),
('c89ce8e097f212e75686f73cc5008711',4,'Send circular e-mail','A circular e-mail can be sent to all participants of the event here.','R',0,'#link-71a939b1cddd28322f902cdfbc330250 A:eq(0)','dispatch.php/course/members','','','dozent@studip.de',1406636964,0),
('c89ce8e097f212e75686f73cc5008711',5,'Send circular e-mail to user group','There is further the possibility to send a circular e-mail to individual user groups.','BR',0,'#autor CAPTION:eq(0)  SPAN:eq(0)  A:eq(0)  IMG:eq(0)','dispatch.php/course/members','','','',1406637123,0),
('d9913517f9c81d2c0fa8362592ce5d0e',1,'What is Blubber?','This tour provides an overview of the functionality of \"Blubber\".\r\n\rTo proceed, please click \"Continue\" in the lower-right corner.','TL',0,'','plugins.php/blubber/streams/global','','','',1405507364,0),
('d9913517f9c81d2c0fa8362592ce5d0e',2,'Create contribution','A discussion can be started here by writing a text. Paragraphs can be created by pressing shift+enter. The text will be sent by pressing enter.','BL',0,'TEXTAREA#new_posting.autoresize','plugins.php/blubber/streams/global','','','',1405507478,0),
('d9913517f9c81d2c0fa8362592ce5d0e',3,'Design text','The text can be formatted and smileys can be used.\n\nThe customary formatting such as e.g. **bold** or %%italics%%  can be used.','BL',0,'TEXTAREA#new_posting.autoresize','plugins.php/blubber/streams/global','','','',1405508371,0),
('d9913517f9c81d2c0fa8362592ce5d0e',4,'Mention persons','Others can be informed about a post by mentioning them in the post, using the format @user name or @\'first name last name\'.','BL',0,'TEXTAREA#new_posting.autoresize','plugins.php/blubber/streams/global','','','',1405672301,0),
('d9913517f9c81d2c0fa8362592ce5d0e',5,'Add document','Documents can be inserted into a post by dragging them into an input field using drag&drop.','BL',0,'TEXTAREA#new_posting.autoresize','plugins.php/blubber/streams/global','','','',1405508401,0),
('d9913517f9c81d2c0fa8362592ce5d0e',6,'Hashtags','Posts can be issued with key words (\"hashtags\") by placing a # in front of the chosen word.','BL',0,'TEXTAREA#new_posting.autoresize','plugins.php/blubber/streams/global','','','',1405508442,0),
('d9913517f9c81d2c0fa8362592ce5d0e',7,'Hashtag cloud','By clicking on a hashtag, all posts containing this hashtag will be displayed.','RT',0,'#layout-sidebar SECTION DIV DIV.sidebar-widget-header :eq(1)','plugins.php/blubber/streams/global','','','',1405508505,0),
('d9913517f9c81d2c0fa8362592ce5d0e',8,'Change contribution','If the cursor is positioned on a post, its date will appear. For your own posts an additional icon will appear on the right next to the date. This icon allow you to subsequently edit your post.','BR',0,'DIV DIV A SPAN.time','plugins.php/blubber/streams/global','','','',1405507901,0),
('d9913517f9c81d2c0fa8362592ce5d0e',9,'Link contribution','If the cursor is positioned on the first contribution to the discussion a link icon will appear on the left next to the date. If this is clicked using the right mouse button the link can be copied on this contribution in order to be able to insert it in another place.','BR',0,'DIV DIV A.permalink','plugins.php/blubber/streams/global','','','',1405508281,0),
('d9a066071e2be43b2b51c37a9d692026',1,'OER Campus','Der OER Campus ist neu in Stud.IP 5. Hier können Lernmaterialien gesucht, verwaltet und mit anderen geteilt werden.','B',0,'','dispatch.php/oer/market','','','root@localhost',1630577268,1630577268),
('d9a066071e2be43b2b51c37a9d692026',2,'','Lernmaterialien aus dem OER Campus können von hier aus direkt im Dateibereich einer Veranstaltung bereitgestellt werden.','B',0,'','dispatch.php/oer/market','','','root@localhost',1630577268,1630577268),
('d9a066071e2be43b2b51c37a9d692026',3,'','Um gezielt Lernmaterialien zu suchen, können Sie hier einen Suchbegriff eingeben.','B',0,'INPUT[name=search]','dispatch.php/oer/market','','','root@localhost',1630577268,1630577268),
('d9a066071e2be43b2b51c37a9d692026',4,'','Über die Filterfunktion können Sie die angezeigten Lernmaterialien weiter eingrenzen.','B',0,'#layout_content FORM:eq(0)  DIV:eq(0)  DIV:eq(0)  DIV:eq(0)  BUTTON:eq(0)','dispatch.php/oer/market','','','root@localhost',1630577268,1630577268),
('d9a066071e2be43b2b51c37a9d692026',5,'','Im Entdeckermodus finden Sie Lernmaterial nach Schlagwörtern und Themen geordnet.','L',0,'#layout_content FORM:eq(0)  DIV:eq(3)  DIV:eq(0)  DIV:eq(0)  DIV:eq(1)','dispatch.php/oer/market','','','root@localhost',1630577268,1630577268),
('d9a066071e2be43b2b51c37a9d692026',6,'','Wenn Sie selbst Lernmaterial erstellt haben, das Sie anderen zur Verfügung stellen möchten, können Sie es hier hochladen.','R',0,'div.sidebar-widget A:eq(0)','dispatch.php/oer/market','','','root@localhost',1630577268,1630577268),
('d9a066071e2be43b2b51c37a9d692026',7,'','Ihr hochgeladenes Lernmaterial können Sie im Bereich \"Meine Materialien\" verwalten. Nutzen Sie den Entwurfsmodus, wenn Sie Ihr Material noch nicht veröffentlichen wollen.','B',0,'#nav_oer_mymaterial A:eq(0)','dispatch.php/oer/market','','','root@localhost',1630577268,1630577268),
('dac47ec2e8a848744bde4b3881d31553',1,'Willkommen in Stud.IP 6!','In den folgenden Schritten möchten wir Ihnen kurz die wichtigsten Neuerungen vorstellen. Dazu klicken Sie auf \"weiter\" und folgen dem\r\nAblauf.\r\n\r\nDer Zeitpunkt passt gerade nicht? Kein Problem! Mit einem Klick auf das Fragezeichen-Icon oben rechts können Sie die Tour zu jedem Zeitpunkt erneut starten.','B',0,'','dispatch.php/start','','','',1737728592,0),
('dac47ec2e8a848744bde4b3881d31553',2,'Neue Loginseite','Die neue Loginseite haben Sie eben schon gesehen.\r\nDas einladende neue Design bietet Raum für wichtige News und Hinweise.','B',0,'','dispatch.php/start','','','',1737728592,0),
('dac47ec2e8a848744bde4b3881d31553',3,'','Erstellen und verwalten Sie Aufgabenblätter in Ihrem Arbeitsplatz, direkt in Ihren Veranstaltungen oder fügen Sie sie in Courseware hinzu.','T',0,'#content .content-item-vips','dispatch.php/contents/overview','','','',1737728592,0),
('dac47ec2e8a848744bde4b3881d31553',4,'','So können semesterbegleitende Tests und Lernstandskontrollen, aber auch Studienleistungen und sogar Prüfungen direkt in Stud.IP durchgeführt werden. Probieren Sie es einfach mal aus!','B',0,'','dispatch.php/contents/overview','','','',1737728592,0),
('dac47ec2e8a848744bde4b3881d31553',5,'','Entdecken Sie Studiengruppen ganz neu! Mit dem neuen Startseiten-Widget haben Sie den Überblick. Verknüpfen Sie Studiengruppen nun direkt mit Veranstaltungen und tauschen Sie sich in Lerngruppen aus.','B',0,'#nav_browse_my_studygroups A:eq(0)  SPAN:eq(0)','dispatch.php/my_studygroups','','','',1737728592,0),
('dac47ec2e8a848744bde4b3881d31553',6,'','Stundenplan und Kalender wurden ebenfalls überarbeitet, bieten einige geänderte Funktionen und kommen im frischen Design von Stud.IP 6.','B',0,'','dispatch.php/calendar/schedule','','','',1737728592,0),
('dac47ec2e8a848744bde4b3881d31553',7,'','Viel Spaß beim Entdecken!','B',0,'','dispatch.php/start','','','',1737728592,0),
('de1fbce508d01cbd257f9904ff8c3b43',1,'Profile tour','This tour provides a general overview of the profile page\'s structure.\r\n\rTo proceed, please click \"Continue\" in the lower-right corner.','T',0,'','dispatch.php/profile','','','',1406722657,0),
('de1fbce508d01cbd257f9904ff8c3b43',2,'Personal picture','If you uploaded a picture, it will be displayed here. You can change it at all times.','RT',0,'.avatar-normal','dispatch.php/profile','','','',1406722657,0),
('de1fbce508d01cbd257f9904ff8c3b43',3,'Stud.IP-Score','The Stud.IP-Score increases with the activities in Stud.IP and thus represents the experience with  Stud.IP.','BL',0,'#layout_content TABLE:eq(0) TBODY:eq(0) TR:eq(0) TD:eq(0) A:eq(0)','dispatch.php/profile','','','',1406722657,0),
('de1fbce508d01cbd257f9904ff8c3b43',4,'Announcements','You can publish personal announcements on this site.','B',0,'#layout_content ARTICLE:eq(0)  HEADER:eq(0)  H1:eq(0)','dispatch.php/profile','','','',1406722657,0),
('de1fbce508d01cbd257f9904ff8c3b43',5,'New announcement','Click on the plus sign, if you would like to create an announcement.','BR',0,'#layout_content ARTICLE:eq(0)  HEADER:eq(0)  NAV:eq(0)  A:eq(0)','dispatch.php/profile','','','',1406722657,0),
('de1fbce508d01cbd257f9904ff8c3b43',6,'Personal details','Your picture and additional user data can be changed on these sites.','B',0,'#tabs li:eq(2)','dispatch.php/profile','','','dozent@studip.de',1406722657,0),
('edfcf78c614869724f93488c4ed09582',1,'Teilnehmerverwaltung','Diese Tour gibt einen Überblick über die Teilnehmerverwaltung einer Veranstaltung.\r\n\r\nUm zum nächsten Schritt zu gelangen, klicken Sie bitte rechts unten auf \"Weiter\".','B',0,'','dispatch.php/course/members','','','',1405688399,0),
('edfcf78c614869724f93488c4ed09582',2,'Personen eintragen','Mit diesen Funktionen können entweder einzelne Personen in Stud.IP gesucht und direkt mit dem Status Dozent, Tutor oder Autor eintragen werden. Es ist auch möglich eine Teilnehmerliste einzugeben, um viele Personen auf einmal als TutorIn der Veranstaltung zuzuordnen.','R',0,'#layout-sidebar SECTION:eq(0)  DIV:eq(6)  DIV:eq(0)','dispatch.php/course/members','','','',1405688707,0),
('edfcf78c614869724f93488c4ed09582',3,'Hochstufen / Herabstufen','Um eine bereits eingetragene Person zum/zur TutorIn hochzustufen oder zum/zur LeserIn herabzustufen, wählen Sie diese Person in der Liste aus und führen Sie mit Hilfe des Dropdown-Menü die gewünschte Aktion aus.','T',0,'#autor CAPTION','dispatch.php/course/members','','','',1405690324,0),
('edfcf78c614869724f93488c4ed09582',4,'Rundmail verschicken','Hier kann eine Rundmail an alle Teilnehmende der Veranstaltung verschickt werden.','R',0,'#link-71a939b1cddd28322f902cdfbc330250 A:eq(0)','dispatch.php/course/members','','','dozent@studip.de',1406636964,0),
('edfcf78c614869724f93488c4ed09582',5,'Rundmail an Nutzergruppe versenden','Weiterhin besteht die Möglichkeit eine Rundmail an einzelne Nutzergruppen zu versenden.','BR',0,'#autor CAPTION:eq(0)  SPAN:eq(0)  A:eq(0)  IMG:eq(0)','dispatch.php/course/members','','','',1406637123,0),
('ef5092ba722c81c37a5a6bd703890bd9',1,'Was ist Blubbern?','Diese Tour gibt Ihnen einen Überblick über die wichtigsten Funktionen von \"Blubber\".\r\n\r\nUm auf den nächsten Schritt zu kommen, klicken Sie bitte rechts unten auf \"Weiter\".','TL',0,'','plugins.php/blubber/streams/global','','','',1405507364,0),
('ef5092ba722c81c37a5a6bd703890bd9',2,'Beitrag erstellen','Hier kann eine Diskussion durch Schreiben von Text begonnen werden. Absätze lassen sich durch Drücken von Umschalt+Eingabe erzeugen. Der Text wird durch Drücken von Eingabe abgeschickt.','BL',0,'TEXTAREA#new_posting.autoresize','plugins.php/blubber/streams/global','','','',1405507478,0),
('ef5092ba722c81c37a5a6bd703890bd9',3,'Text gestalten','Der Text kann formatiert und mit Smileys versehen werden.\r\nEs können die üblichen Formatierungen verwendet werden, wie z. B. **fett** oder %%kursiv%%.','BL',0,'TEXTAREA#new_posting.autoresize','plugins.php/blubber/streams/global','','','',1405508371,0),
('ef5092ba722c81c37a5a6bd703890bd9',4,'Personen erwähnen','Andere können über einen Beitrag informiert werden, indem sie per @benutzername oder @\"Vorname Nachname\" im Beitrag erwähnt werden.','BL',0,'TEXTAREA#new_posting.autoresize','plugins.php/blubber/streams/global','','','',1405672301,0),
('ef5092ba722c81c37a5a6bd703890bd9',5,'Datei hinzufügen','Dateien können in einen Beitrag eingefügt werden, indem sie per Drag&Drop in ein Eingabefeld gezogen werden.','BL',0,'TEXTAREA#new_posting.autoresize','plugins.php/blubber/streams/global','','','',1405508401,0),
('ef5092ba722c81c37a5a6bd703890bd9',6,'Schlagworte','Beiträge können mit Schlagworten (engl. \"Hashtags\") versehen werden, indem einem beliebigen Wort des Beitrags ein # vorangestellt wird.','BL',0,'TEXTAREA#new_posting.autoresize','plugins.php/blubber/streams/global','','','',1405508442,0),
('ef5092ba722c81c37a5a6bd703890bd9',7,'Schlagwortwolke','Durch Anklicken eines Schlagwortes werden alle Beiträge aufgelistet, die dieses Schlagwort enthalten.','RT',0,'#layout-sidebar SECTION DIV DIV.sidebar-widget-header :eq(1)','plugins.php/blubber/streams/global','','','',1405508505,0),
('ef5092ba722c81c37a5a6bd703890bd9',8,'Beitrag ändern','Wird der Mauszeiger auf einem beliebigen Beitrag positioniert, erscheint dessen Datum. Bei eigenen Beiträgen erscheint außerdem rechts neben dem Datum ein Icon, mit dem der Beitrag nachträglich geändert werden kann.','BR',0,'DIV DIV A SPAN.time','plugins.php/blubber/streams/global','','','',1405507901,0),
('ef5092ba722c81c37a5a6bd703890bd9',9,'Beitrag verlinken','Wird der Mauszeiger auf dem ersten Diskussionsbeitrag positioniert, erscheint links neben dem Datum ein Link-Icon. Wenn dieses mit der rechten Maustaste angeklickt wird, kann der Link auf diesen Beitrag kopiert werden, um ihn an anderer Stelle einfügen zu können.','BR',0,'DIV DIV A.permalink','plugins.php/blubber/streams/global','','','',1405508281,0),
('f0aeb0f6c4da3bd61f48b445d9b30dc1',1,'Functions and design possibilities of the start page','This tour provides an overview of the start page\'s features and functions.\r\n\rTo proceed, please click \"Continue\" in the lower-right corner.','B',0,'','dispatch.php/start','','','root@localhost',1405934926,0),
('f0aeb0f6c4da3bd61f48b445d9b30dc1',2,'Individual design of the start page','The default configuration of the start page is that the elements \"Quicklinks\", \"announcements\", \"my current appointments\" and  \"surveys\" are displayed. The elements are called widgets and  can be deleted, added and moved. Each widget can be individually added, deleted and moved.','TL',0,'','dispatch.php/start','','','',1405934970,0),
('f0aeb0f6c4da3bd61f48b445d9b30dc1',3,'Add widget','Widgets can be added here. In addition to the standard widgets the personal timetable can, for example, be displayed on the start page. Newly added widgets appear right at the bottom on the start page. In addition, it is possible to jump directly to each widget in the sidebar.','R',0,'#layout-sidebar SECTION:eq(0)  DIV:eq(5)  DIV:eq(0)','dispatch.php/start','','','',1405935192,0),
('f0aeb0f6c4da3bd61f48b445d9b30dc1',4,'Jump labels','In addition, it is possible to jump directly to each widget using jump labels.','R',0,'#layout-sidebar SECTION:eq(0)  DIV:eq(2)  DIV:eq(0)','dispatch.php/start','','','',1406623464,0),
('f0aeb0f6c4da3bd61f48b445d9b30dc1',5,'Position widget','A widget can be moved to the desired position using drag&drop: For this purpose you click into the headline of a widget, hold down the mouse button, and drag the widget to the desired position.','B',0,'.widget-header','dispatch.php/start','','','',1405935687,0),
('f0aeb0f6c4da3bd61f48b445d9b30dc1',6,'Edit widget','With several widgets a further symbol is displayed in addition to the X for closing. The widget \"Quicklinks\", for example, can be adjusted individually by clicking on this button, the announcements can be subscribed to and appointments can be added with the actual appointments or timetable.','L',0,'#widget-8','dispatch.php/start','','','',1405935792,0),
('f0aeb0f6c4da3bd61f48b445d9b30dc1',7,'Remove widget','Each widget can be removed by clicking on the X in the right upper corner. If required, it can be added again at all times.','R',0,'.widget-header','dispatch.php/start','','','',1405935376,0),
('fa963d2ca827b28e0082e98aafc88765',1,'My courses','This tour provides an overview of the functionality of \"My courses\".\r\n\rTo proceed, please click \"Continue\" in the lower-right corner.','TL',0,'','dispatch.php/my_courses','','','',1405521184,0),
('fa963d2ca827b28e0082e98aafc88765',2,'Overview of courses','The courses of the current and past semester are displayed here. New courses initially appear in red.','BL',0,'#my_seminars TABLE:eq(0)  CAPTION:eq(0)','dispatch.php/my_courses','','','autor@studip.de',1405521244,0),
('fa963d2ca827b28e0082e98aafc88765',3,'Course details','With a click on the \"i\" a window appears with the most important benchmark data of the course.','L',0,'#my_seminars .action-menu-icon','dispatch.php/my_courses','','','autor@studip.de',1405931069,0),
('fa963d2ca827b28e0082e98aafc88765',4,'Course contents','All contents (such as e.g. a forum) are displayed by corresponding symbols here.\n\nIf there were any news since the last login these will appear in red.','LT',0,'#my_seminars .my-courses-navigation-item','dispatch.php/my_courses','','','',1405931225,0),
('fa963d2ca827b28e0082e98aafc88765',5,'Leaving the course','A click on the door icon enables a direct removal from the course','L',0,'#my_seminars .action-menu-icon','dispatch.php/my_courses','','','autor@studip.de',1405931272,0),
('fa963d2ca827b28e0082e98aafc88765',6,'Adjustment to the course view','In order to adjust the course overview you can arrange your courses according to certain criteria (such as e.g. fields of study, lecturers or colours).','R',0,'#layout-sidebar SECTION:eq(0)  DIV:eq(11)  DIV:eq(0)','dispatch.php/my_courses','','','',1405932131,0),
('fa963d2ca827b28e0082e98aafc88765',7,'Access to an course of past and future semesters','By clicking on the drop-down menu courses from past semesters can be displayed for example.','R',0,'#layout-sidebar SECTION:eq(0)  DIV:eq(5)  DIV:eq(0)','dispatch.php/my_courses','','','',1405932230,0),
('fa963d2ca827b28e0082e98aafc88765',8,'Further possible actions','Here you can mark all news as read, change colour groups as you please, or\n\nalso adjust the notifications about activities in the individual events.','R',0,'#layout-sidebar SECTION:eq(0)  DIV:eq(8)  DIV:eq(0)','dispatch.php/my_courses','','','',1405932320,0),
('fa963d2ca827b28e0082e98aafc88765',9,'Study groups and institutes','There is moreover the possibility to access personal study groups or institutes.','R',0,'#nav_browse_my_institutes A','dispatch.php/my_courses','','','',1405932519,0);
/*!40000 ALTER TABLE `help_tour_steps` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `help_tour_user`
--

LOCK TABLES `help_tour_user` WRITE;
/*!40000 ALTER TABLE `help_tour_user` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `help_tour_user` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `help_tours`
--

LOCK TABLES `help_tours` WRITE;
/*!40000 ALTER TABLE `help_tours` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `help_tours` VALUES
('05434e40601a9a2a7f5fa8208ae148c1','05434e40601a9a2a7f5fa8208ae148c1','My documents','The personal document area will be presented in this tour.','tour','autor,tutor,dozent,admin,root',1,'en','5.0','','',1405592618,0),
('154e711257d4d32d865fb8f5fb70ad72','154e711257d4d32d865fb8f5fb70ad72','Meine Dateien','In dieser Tour wird der persönliche Dateibereich vorgestellt.','tour','autor,tutor,dozent,admin,root',1,'de','5.0','','',1405592618,0),
('19ac063e8319310d059d28379139b1cf','19ac063e8319310d059d28379139b1cf','Studiengruppe anlegen','In dieser Tour wird das Anlegen von Studiengruppen erklärt.','tour','autor,tutor,dozent,admin,root',1,'de','6.0','','',1405684299,0),
('1badcf28ab5b206d9150b2b9683b4cb6','1badcf28ab5b206d9150b2b9683b4cb6','My courses (lecturers)','The most important functions of the site \"My courses\" are presented in this tour.','tour','tutor,dozent,admin,root',1,'en','5.0','','',1406125685,0),
('21f487fa74e3bfc7789886f40fe4131a','21f487fa74e3bfc7789886f40fe4131a','Forum nutzen','Die Inhalte dieser Tour stammen aus der alten Tour des Forums (Sidebar > Aktionen > Tour starten).','tour','autor,tutor,dozent,admin,root',1,'de','5.0','','',1405415746,0),
('3629493a16bf2680de64361f07cab096','3629493a16bf2680de64361f07cab096','Blubber','In der Tour wird die Nutzung von Blubber erklärt.','tour','autor,tutor,dozent,admin,root',1,'de','3.1','','',1406709759,0),
('3a717a468afb0822cb1455e0ae6b6fce','3a717a468afb0822cb1455e0ae6b6fce','Blubber','In der Tour wird die Nutzung von Blubber erklärt.','tour','autor,tutor,dozent,admin,root',1,'de','3.1','','',1406709041,0),
('3dbe7099f82dcdbba4580acb1105a0d6','3dbe7099f82dcdbba4580acb1105a0d6','Administering the forum','The administration of the forum is explained in this tour.','tour','tutor,dozent,admin,root',1,'en','5.0','','root@localhost',1405417901,1631619331),
('44f859c50648d3410c39207048ddd833','44f859c50648d3410c39207048ddd833','Forum verwalten','Die Inhalte dieser Tour stammen aus der alten Tour des Forums (Sidebar > Aktionen > Tour starten).','tour','tutor,dozent,admin,root',1,'de','5.0','','root@localhost',1405417901,0),
('49604a77654617a745e29ad6b253e491','49604a77654617a745e29ad6b253e491','Gestaltung der Startseite','In dieser Tour werden die Funktionen und Gestaltungsmöglichkeiten der Startseite vorgestellt.','tour','autor,tutor,dozent,admin,root',1,'de','5.0','','root@localhost',1405934780,1631613451),
('','4d41c9760a3248313236af202275107a','Allgemeines zum Wiki','Die Tour gibt einen allgemeinen Überblick über das Wiki.','tour','autor,tutor,dozent,admin,root',1,'de','3.1','','',1441276241,0),
('','4d41c9760a3248313236af202275107b','Schreiben im Wiki','Die Tour erklärt, wie das Wiki bearbeitet werden kann.','tour','autor,tutor,dozent,admin,root',1,'de','5.0','','root@localhost',1441276241,1631619212),
('','4d41c9760a3248313236af202275107c','Lesen im Wiki','Die Tour erklärt die verschiedenen Anzeige-Modalitäten zum Lesen des Wikis.','tour','autor,tutor,dozent,admin,root',1,'de','5.0','','root@localhost',1441276241,1631619264),
('36821ce84e48f0bd68482f9f43099460','55f3a548348dcbfdca67678588887ffd','Mein Arbeitsplatz','Einführung Mein Arbeitsplatz','tour','autor,tutor,dozent,admin,root',1,'de','5.0','demo-installation','root@localhost',1631614324,1631614324),
('','588effa83da976a889a68c152bcabc90','Blubber','This tour explains how to use \"Blubber\"','tour','autor,tutor,dozent,admin,root',1,'en','3.1','','',1427784693,0),
('','5d41c9760a3248313236af202275107a','General information on the Wiki','This tour provides general information about the Wiki.','tour','autor,tutor,dozent,admin,root',1,'en','3.1','','',1441276241,0),
('','5d41c9760a3248313236af202275107b','Editing the Wiki','This tour provides help for editing Wiki pages.','tour','autor,tutor,dozent,admin,root',1,'en','5.0','','root@localhost',1441276241,1631619212),
('','5d41c9760a3248313236af202275107c','Reading the Wiki','This tour provides help for reading Wiki pages.','tour','autor,tutor,dozent,admin,root',1,'en','5.0','','root@localhost',1441276241,1631619264),
('6849293baa05be5bef8ff438dc7c438b','6849293baa05be5bef8ff438dc7c438b','Suche','In dieser Feature-Tour werden die wichtigsten Funktionen der Suche vorgestellt.','tour','autor,tutor,dozent,admin,root',1,'de','5.0','','',1405519609,0),
('7af1e1fb7f53c910ba9f42f43a71c723','7af1e1fb7f53c910ba9f42f43a71c723','Search','In this feature tour the most important search functions are explained','tour','autor,tutor,dozent,admin,root',1,'en','5.0','','',1405519609,0),
('7cccbe3b22dfa745c17cb776fb04537c','7cccbe3b22dfa745c17cb776fb04537c','Meine Veranstaltungen (Dozierende)','In dieser Tour werden die wichtigsten Funktionen der Seite \"Meine Veranstaltungen\" vorgestellt.','tour','tutor,dozent,admin,root',1,'de','5.0','','',1406125685,0),
('','83dc1d25e924f2748ee3293aaf0ede8e','Blubber','This tour explains how to use \"Blubber\"','tour','autor,tutor,dozent,admin,root',1,'en','3.1','','',1427784655,0),
('89786eac42f52ac316790825b4f5c0b2','89786eac42f52ac316790825b4f5c0b2','Use Forum','The content of this tour is from the old tour of the forum (Sidebar > actions > start tour).','tour','autor,tutor,dozent,admin,root',1,'en','5.0','','',1405415746,0),
('96ea422f286fb5bbf9e41beadb484a9a','96ea422f286fb5bbf9e41beadb484a9a','Profilseite','In dieser Tour werden die Grundfunktionen und Bereiche der Profilseite vorgestellt.','tour','autor,tutor,dozent,admin,root',1,'de','5.0','','root@localhost',1406722657,1631617118),
('9e9dca9b1214294b9605824bfe90fba1','9e9dca9b1214294b9605824bfe90fba1','Create study group','In this tour the creation of study groups is explained','tour','autor,tutor,dozent,admin,root',1,'en','5.0','','',1405684299,0),
('b74f8459dce2437463096d56db7c73b9','b74f8459dce2437463096d56db7c73b9','Meine Veranstaltungen (Studierende)','In dieser Tour werden die wichtigsten Funktionen der Seite \"Meine Veranstaltungen\" vorgestellt.','tour','autor,admin,root',1,'de','5.0','','',1405521073,0),
('c89ce8e097f212e75686f73cc5008711','c89ce8e097f212e75686f73cc5008711','Participant administration','The administration options of the participant administration are explained in this tour.','tour','tutor,dozent,admin,root',1,'en','5.0','','',1405688156,0),
('','d9913517f9c81d2c0fa8362592ce5d0e','Blubber','This tour explains how to use \"Blubber\"','tour','autor,tutor,dozent,admin,root',1,'en','3.1','','',1427784720,0),
('8e7a9f6b86255bc9034b71d8318419e6','d9a066071e2be43b2b51c37a9d692026','OER Campus','Einführung in den OER Campus','tour','autor,tutor,dozent,admin,root',1,'de','5.0','demo-installation','root@localhost',1631614324,1631614324),
('dac47ec2e8a848744bde4b3881d31553','dac47ec2e8a848744bde4b3881d31553','Willkommen in Stud.IP 6!','Einführung in Stud.IP 6','tour','autor,tutor,dozent',1,'de','6.0','','',1737728592,0),
('de1fbce508d01cbd257f9904ff8c3b43','de1fbce508d01cbd257f9904ff8c3b43','Profile page','The basic functions and areas of the profile page are presented in this tour.','tour','autor,tutor,dozent,admin,root',1,'en','5.0','','root@localhost',1406722657,1631617118),
('edfcf78c614869724f93488c4ed09582','edfcf78c614869724f93488c4ed09582','Teilnehmerverwaltung','In dieser Tour werden die Verwaltungsoptionen der Teilnehmerverwaltung erklärt.','tour','tutor,dozent,admin,root',1,'de','5.0','','',1405688156,0),
('ef5092ba722c81c37a5a6bd703890bd9','ef5092ba722c81c37a5a6bd703890bd9','Blubber','In der Tour wird die Nutzung von Blubber erklärt.','tour','autor,tutor,dozent,admin,root',1,'de','3.1','','',1405507317,0),
('f0aeb0f6c4da3bd61f48b445d9b30dc1','f0aeb0f6c4da3bd61f48b445d9b30dc1','Design of the start page','The functions and design possibilities of the start page are presented in this feature tour.','tour','autor,tutor,dozent,admin,root',1,'en','5.0','','root@localhost',1405934780,1631613451),
('fa963d2ca827b28e0082e98aafc88765','fa963d2ca827b28e0082e98aafc88765','My courses (students)','The most important functions of the site \"My courses\" are presented in this tour.','tour','autor,admin,root',1,'en','5.0','','',1405521073,0);
/*!40000 ALTER TABLE `help_tours` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `i18n`
--

LOCK TABLES `i18n` WRITE;
/*!40000 ALTER TABLE `i18n` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `i18n` VALUES
('28cb0aeb29e9b3046b6c2e958566d6a5','config','value','en_GB','Date allocation'),
('3c28f017886d9acf0b0f654195ec478f','config','value','en_GB','Using two-factor authentication you can protect your account by entering a token on each login. You get that token either via E-Mail or by using an appropriate authenticator app.'),
('698096bc7269ee90517c6f22a8711b4e','config','value','en_GB','Some of your personal data is not managed in Stud.IP and therefore cannot be changed here.'),
('e98bde4d61d028203eb3c2c26fa5ac4a','config','value','en_GB','Set up a suitable OTP authenticator app for this purpose. Here you will find a list of known and compatible apps:\n- [Authy]https://authy.com/\n- [FreeOTP]https://freeotp.github.io/\n- Google Authenticator: [Android]https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2 oder [iOS]https://apps.apple.com/app/google-authenticator/id388497605\n- [LastPass Authenticator]https://lastpass.com/auth/\n- [Microsoft Authenticator]https://www.microsoft.com/authenticator');
/*!40000 ALTER TABLE `i18n` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `institute_plan_columns`
--

LOCK TABLES `institute_plan_columns` WRITE;
/*!40000 ALTER TABLE `institute_plan_columns` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `institute_plan_columns` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `kategorien`
--

LOCK TABLES `kategorien` WRITE;
/*!40000 ALTER TABLE `kategorien` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `kategorien` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `keyrings`
--

LOCK TABLES `keyrings` WRITE;
/*!40000 ALTER TABLE `keyrings` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `keyrings` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `licenses`
--

LOCK TABLES `licenses` WRITE;
/*!40000 ALTER TABLE `licenses` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `licenses` VALUES
('CC-BY-1.0','Creative Commons Attribution 1.0 Generic','https://creativecommons.org/licenses/by/1.0/legalcode',0,NULL,'CC_BY','1.0',1640797278,1640797278),
('CC-BY-2.0','Creative Commons Attribution 2.0 Generic','https://creativecommons.org/licenses/by/2.0/legalcode',0,NULL,'CC_BY','2.0',1640797278,1640797278),
('CC-BY-2.5','Creative Commons Attribution 2.5 Generic','https://creativecommons.org/licenses/by/2.5/legalcode',0,NULL,'CC_BY','2.5',1640797278,1640797278),
('CC-BY-3.0','Creative Commons Attribution 3.0 Unported','https://creativecommons.org/licenses/by/3.0/legalcode',0,NULL,'CC_BY','3.0',1640797278,1640797278),
('CC-BY-4.0','Creative Commons Attribution 4.0 International','https://creativecommons.org/licenses/by/4.0/legalcode',0,NULL,'CC_BY','4.0',1640797278,1640797278),
('CC-BY-SA-1.0','Creative Commons Attribution Share Alike 1.0 Generic','https://creativecommons.org/licenses/by-sa/1.0/legalcode',0,NULL,'CC_BY_SA','1.0',1640797278,1640797278),
('CC-BY-SA-2.0','Creative Commons Attribution Share Alike 2.0 Generic','https://creativecommons.org/licenses/by-sa/2.0/legalcode',0,NULL,'CC_BY_SA','2.0',1640797278,1640797278),
('CC-BY-SA-2.5','Creative Commons Attribution Share Alike 2.5 Generic','https://creativecommons.org/licenses/by-sa/2.5/legalcode',0,NULL,'CC_BY_SA','2.5',1640797278,1640797278),
('CC-BY-SA-3.0','Creative Commons Attribution Share Alike 3.0 Unported','https://creativecommons.org/licenses/by-sa/3.0/legalcode',0,NULL,'CC_BY_SA','3.0',1640797278,1640797278),
('CC-BY-SA-4.0','Creative Commons Attribution Share Alike 4.0 International','https://creativecommons.org/licenses/by-sa/4.0/legalcode',1,NULL,'CC_BY_SA','4.0',1640797278,1640797278),
('CC-PDDC','Creative Commons Public Domain Dedication and Certification','https://creativecommons.org/licenses/publicdomain/',0,'Diese Lizenz ist nur sinnvoll, wenn Sie Material eintragen, das gemeinfrei ist. Gemeinfreie Materialien stammen von Autoren, die mindetens 80 Jahre tot sind, oder von Autoren, die im Ausland leben und ihre Werke unter die sogenannte Public Domain gestellt haben. Diese Lizenz ist nicht sinnvoll für Werke, bei denen ein Copyright besteht.',NULL,NULL,1640797278,1640797278),
('CC0-1.0','Creative Commons Zero v1.0 Universal','https://creativecommons.org/publicdomain/zero/1.0/legalcode',0,NULL,'CC_0','1.0',1640797278,1640797278);
/*!40000 ALTER TABLE `licenses` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `limitedadmissions`
--

LOCK TABLES `limitedadmissions` WRITE;
/*!40000 ALTER TABLE `limitedadmissions` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `limitedadmissions` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `lock_rules`
--

LOCK TABLES `lock_rules` WRITE;
/*!40000 ALTER TABLE `lock_rules` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `lock_rules` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `lockedadmissions`
--

LOCK TABLES `lockedadmissions` WRITE;
/*!40000 ALTER TABLE `lockedadmissions` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `lockedadmissions` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `log_actions`
--

LOCK TABLES `log_actions` WRITE;
/*!40000 ALTER TABLE `log_actions` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `log_actions` VALUES
('005df8d5eb23c66214b28b3c9792680b','SEM_CHANGED_ACCESS','Zugangsberechtigungen geändert','%user ändert die Zugangsberechtigungen der Veranstaltung %sem(%affected).',0,0,NULL,NULL,NULL,NULL,NULL),
('00af9c41fc56b617097bdef1e7dca397','MVV_FACHINST_NEW','MVV: Fach-Einrichtung Zuweisung erstellen','%user weist das Fach %fach(%affected) der Einrichtung %inst(%coaffected) zu.',1,0,NULL,'MVV','core',NULL,NULL),
('03d980e11c5a6b57e2bd680cf9bbc890','MVV_CONTACT_RANGE_UPDATE','MVV: Zuordnung der Kontaktperson ändern','%user ändert die Zuordnung der Kontaktperson %contact(%affected) zum Bereich %range(%coaffected).',1,0,NULL,'MVV','core',1716385357,1716385357),
('04061e4d13b416e10d6094679e7c9d9c','MVV_MODULTEIL_DEL','MVV: Modulteil löschen','%user löscht Modulteil %modulteil(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('060390e972f9580ab92c7febb99fb2fa','MVV_LVSEMINAR_NEW','MVV: LV-Gruppe zu Veranstaltung Zuweisung erstellen','%user weist der LV-Gruppe %lvgruppe(%affected) der Veranstaltung %sem(%coaffected) zu.',1,0,NULL,'MVV','core',NULL,NULL),
('07c384b8328f56a33b4edb2570e85a48','MVV_MODULTEIL_DESK_NEW','MVV: Modulteil Deskriptor erstellen','%user erstellt neuen Modulteil Deskriptor %modulteildesk(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('08a643645ddb5d3df5826d9fa863f665','MVV_STGTEILABS_NEW','MVV: Studiengangteilabschnitt erstellen','%user erstellt neuen Studiengangteilabschnitt %stgteilabs(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('0966a73e85ac73c64818cc6eae4be09e','MVV_MODUL_USER_DEL','MVV: Person zu Modul Zuweisung löschen','%user löscht die Zuweisung von %user(%coaffected) als %gruppe zum Modul %modul(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('0a3d58fbc0964ed9d85950e4d729715d','MVV_STUDIENGANG_NEW','MVV: Studiengang erstellen','%user erstellt neuen Studiengang %stg(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('0d87c25b624b16fb9b8cdaf9f4e96e53','INST_CREATE','Einrichtung anlegen','%user legt Einrichtung %inst(%affected) an.',1,0,NULL,NULL,NULL,NULL,NULL),
('0e46eec26da0b0217280e8be4b26227b','MVV_ABS_ZUORD_DEL','MVV: Abschluss-Kategorien Zuweisung  löschen','%user löscht die Zuweisung des Abschlusses %abschluss(%affected) zur Kategorie %abskategorie(%coaffected).',1,0,NULL,'MVV','core',NULL,NULL),
('0ee290df95f0547caafa163c4d533991','SEM_VISIBLE','Veranstaltung sichtbar schalten','%user schaltet %sem(%affected) sichtbar.',1,0,NULL,NULL,NULL,NULL,NULL),
('0ef58f0b0a97d83616efc6e9479c522e','MVV_FACHBERATER_DEL','MVV: Person zu Fach Zuweisung löschen','%user löscht die Zuweisung von %user(%coaffected) zum Studiengangteil %stgteil(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('10916a5a08ca16bfd055e4823311377a','MVV_DOK_ZUORD_DEL','MVV: Dokumentzuordnung löschen','%user löscht die Zuweisung des Dokumentes %dokument(%affected) zu %object_type(%coaffected).',1,0,NULL,'MVV','core',NULL,NULL),
('10c31be1aec819c03b0dc299d0111576','CHANGE_BASIC_DATA','Basisdaten geändert','%user hat in Veranstaltung %sem(%affected) die Daten %info geändert.',0,0,NULL,NULL,NULL,NULL,NULL),
('10c320bc80022f1ff1381857af46f474','SEM_DEL_FROM_GROUP','Veranstaltung aus Gruppe entfernen','%user entfernt Veranstaltung %sem(%affected) aus der Gruppe %sem(%coaffected).',1,0,NULL,NULL,'core',NULL,NULL),
('10e9a21d0dc5b627a529dda76a89a884','MVV_FILE_FILEREF_UPDATE','MVV: Datei ändern','%user ändert Datei %fileref(%affected).',1,0,NULL,'MVV','core',1716385357,1716385357),
('13b5297079e1600ccc3ca6f49081099f','MVV_STG_STGTEIL_DEL','MVV: Studiengang zu Studiengangteil Zuweisung löschen','%user löscht die Zuweisung des Studienganges %stg(%affected) zum Studiengangteil %stgteil(%coaffected).',1,0,NULL,'MVV','core',NULL,NULL),
('143ade01ac63524ee2ed0fac5a6f0c33','MVV_FILE_RANGE_NEW','MVV: Material/Dokument zuordnen','%user ordnet Material/Dokument %fileref(%affected) zum Bereich %range(%coaffected) zu.',1,0,NULL,'MVV','core',1716385357,1716385357),
('157f75beb242c7de8ff790d4005a259e','MVV_FACH_DEL','MVV: Fach löschen','%user löscht Fach %fach(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('1585bb70e5e403fd818e59bb622db4a3','STATUSGROUP_ADD_USER','Nutzer wird zu einer Statusgruppe hinzugefügt','%user fügt %user(%affected) zur %group(%coaffected) hinzu.',1,0,NULL,NULL,NULL,NULL,NULL),
('15b9fcdf82a617e9bd9d0ca112ecebac','MVV_FILE_FILEREF_DELETE','MVV: Datei löschen','%user löscht Datei %fileref(%affected).',1,0,NULL,'MVV','core',1716385357,1716385357),
('1601bfdca4988a309636d2bbbd3adb47','MVV_EXTERN_CONTACT_NEW','MVV: Externe Kontaktperson erstellen','%user erstellt neue externe Kontaktperson %contact(%affected).',1,0,NULL,'MVV','core',1716385357,1716385357),
('17f0a527e9db7dec09687a70681559cf','RES_ASSIGN_DEL_SINGLE','Direktbuchung löschen','%user löscht Direktbuchung für %res(%affected) (%info).',0,0,NULL,NULL,NULL,NULL,NULL),
('1a1e8c9c3125ea8d2c58c875a41226d6','INST_DEL','Einrichtung löschen','%user löscht Einrichtung %info (%affected).',1,0,NULL,NULL,NULL,NULL,NULL),
('1a27a101df926fafce35635f0dd72522','MVV_MODULTEIL_NEW','MVV: Modulteil erstellen','%user erstellt neuen Modulteil %modulteil(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('1e6debdbfd7a7aeef2dcb61fa65beddd','MVV_ABSCHLUSS_DEL','MVV: Abschluss löschen','%user löscht Abschluss %abschluss(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('1eb6597264fc19da6b48f519c3e47078','MVV_MODUL_DESK_DEL','MVV: Modul Deskriptor löschen','%user löscht Modul Deskriptor %moduldesk(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('23f50ebeab22138bc959f27111ae5ab0','MVV_STG_STGTEIL_NEW','MVV: Studiengang zu Studiengangteil Zuweisung erstellen','%user weist den Studiengang %stg(%affected) dem Studiengangteil %stgteil(%coaffected) zu.',1,0,NULL,'MVV','core',NULL,NULL),
('23f7a87368df1132df137e7d320fa698','MVV_LVMODULTEIL_NEW','MVV: LV-Gruppe zu Modulteil Zuweisung erstellen','%user weist der LV-Gruppe %lv(%affected) den Modulteil %modulteil(%coaffected) zu.',1,0,NULL,'MVV','core',NULL,NULL),
('2420da2946df66a5ad96c6d45e97d5b9','SEM_ADD_STUDYAREA','Studienbereich zu Veranst. hinzufügen','%user fügt Studienbereich \"%studyarea(%coaffected)\" zu %sem(%affected) hinzu.',0,0,NULL,NULL,NULL,NULL,NULL),
('248f54105b7102e5cbcc36e9439504fb','STUDYAREA_ADD','Studienbereich hinzufügen','%user legt Studienbereich %studyarea(%affected) an.',0,0,NULL,NULL,NULL,NULL,NULL),
('292b1cf6c0b46d7038baa75e0b273299','MVV_MODUL_DEL','MV: Modul löschen','%user löscht Modul %modul(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('293a742d791a1edbaaa738f8991aeb7b','MVV_LVSEMINAR_UPDATE','MVV: LV-Gruppe zu Veranstaltung Zuweisung ändern','%user ändert die Zuweisung der LV-Gruppe %lvgruppe(%affected) zur Veranstaltung %sem(%coaffected).',1,0,NULL,'MVV','core',NULL,NULL),
('2d5a23cf47bea8e77dcce0f3a100727c','MVV_EXTERN_CONTACT_UPDATE','MVV: Externe Kontaktperson ändern','%user ändert externe Kontaktperson %contact(%affected).',1,0,NULL,'MVV','core',1716385357,1716385357),
('2dd254c28cac83c59856fe89500e3bc3','MVV_STGTEILABS_MODUL_DEL','MVV: Stgteilabschnitt-Modul Zuweisung löschen','%user löscht die Zuweisung des Studiengangteilabschnitts %stgteilabs(%affected) zum Modul %modul(%coaffected).',1,0,NULL,'MVV','core',NULL,NULL),
('2e816bfd792e4a99913f11c04ad49198','SEM_UNDELETE_SINGLEDATE','Einzeltermin wiederherstellen','%user stellt Einzeltermin %singledate(%affected) in %sem(%coaffected) wieder her.',1,0,NULL,NULL,NULL,NULL,NULL),
('2e8ac58e7f181243e3a736ddd65416e1','MVV_FILE_UPDATE','MVV: Material/Dokument ändern','%user ändert Material/Dokument %file(%affected).',1,0,NULL,'MVV','core',1716385357,1716385357),
('30dfb509cb1a8e228af3bd17dd6c8d1d','RES_ASSIGN_SEM','Buchen einer Ressource (VA)','%user bucht %res(%affected) für %sem(%coaffected) (%info).',0,0,NULL,NULL,NULL,NULL,NULL),
('31fd4549853915608facb8c3e2b101d6','MVV_STGTEIL_DEL','MVV: Studiengangteil löschen','%user löscht Studiengangteil %stgteil(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('347738302758bea951248b255409fa85','MVV_KATEGORIE_UPDATE','MVV: Abschluss-Kategorie ändern','%user ändert Abschluss-Kategorie %abskategorie(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('370db4eb0e38051dd3c5d7c52717215a','SEM_DELETE_SINGLEDATE_REQUEST','Einzeltermin, Raumanfrage gelöscht','%user hat in %sem(%affected) die Raumanfrage für den Termin <em>%coaffected</em> gelöscht.',1,0,NULL,NULL,NULL,NULL,NULL),
('3861d5249b8fe7f2be57adfda8944f4d','MVV_LVGRUPPE_UPDATE','MVV: LV-Gruppe ändern','%user ändert LV-Gruppe %lvgruppe(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('39082f4fbfe61ff3aee8e93f47bc1722','FILE_DELETE','Nutzer löscht Datei','%user löscht Datei %info (File-Id: %affected)',1,0,NULL,NULL,NULL,NULL,NULL),
('398c62a78ec7e2b72d88cce504c2730f','MVV_MODULTEIL_STGTEILABS_NEW','MVV: Studiengangteilabschnitt zu Modulteil Zuweisung erstellen','%user weist den Modulteil %modulteil(%affected) dem Studiengangteilabschnitt %stgteilabs(%coaffected) im %fachsem. Fachsemester zu.',1,0,NULL,'MVV','core',NULL,NULL),
('3f7dcf6cc85d6fba1281d18c4d9aba6f','SEM_ADD_SINGLEDATE','Einzeltermin hinzufügen','%user hat in %sem(%affected) den Einzeltermin %singledate(%coaffected) hinzugefügt',1,0,NULL,NULL,NULL,NULL,NULL),
('3f9b68eacae768ff01cc1cc2d0d82174','MVV_FACHBERATER_NEW','MVV: Person zu Fach Zuweisung erstellen','%user weist dem Studiengangteil %stgteil(%affected) %user(%coaffected) zu.',1,0,NULL,'MVV','core',NULL,NULL),
('4015db0d107d4df6d071bf7ffc70f0a4','MVV_CONTACT_RANGE_DELETE','MVV: Zuordnung der Kontaktperson löschen','%user löscht die Zuordnung der Kontaktperson %contact(%affected) zum Bereich %range(%coaffected).',1,0,NULL,'MVV','core',1716385357,1716385357),
('40455e06f6a679cd87c68c375c9dfa5a','MVV_STGTEILBEZ_DEL','MVV: Studiengangteil-Bezeichnung löschen','%user löscht Studiengangteil-Bezeichnung %stgteilbez(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('4159b9cb03abe9bbe4a60d6083969544','MVV_CONTACT_DELETE','MVV: Kontaktperson löschen','%user löscht Kontaktperson %contact(%affected).',1,0,NULL,'MVV','core',1716385357,1716385357),
('428c09d5a31b1057b08ca5e3b3877109','MVV_FACHBERATER_UPDATE','MVV: Person zu Fach Zuweisung ändern','%user ändert die Zuweisung von %user(%coaffected) zum Studiengangteil %stgteil(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('42b01c873e3066a840ab3237e3aa0911','RES_PERM_CHANGE','Änderung der Berechtigungsstufe an einer Ressource.','%user ändert Berechtigung von %res(%affected): %info',1,0,NULL,NULL,NULL,NULL,NULL),
('447d6ae1b51b97b04f7ae290c6b002d7','MVV_DOKUMENT_DEL','MVV: Dokument löschen','%user löscht Dokument %dokument(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('4490aa3d29644e716440fada68f54032','LOG_ERROR','Allgemeiner Log-Fehler','Allgemeiner Logging-Fehler, Details siehe Debug-Info.',1,0,NULL,NULL,NULL,NULL,NULL),
('46bc7faabfc73864998b561b1011e3fe','RES_REQUEST_UPDATE','Geänderte Raumanfrage','%user ändert Raumanfrage für %sem(%affected), gewünschter Raum: %res(%coaffected), %info',0,0,NULL,NULL,NULL,NULL,NULL),
('4765700be65e3fe1c12e7d74a2579bed','MVV_MODULINST_UPDATE','MVV: Modul-Einrichtung Beziehung ändern','%user ändert die Zuweisung der Einrichtungen %inst(%coaffected) zum Modul %modul(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('47c4f06ce3de71213b69d8d0f8d24f8e','MVV_MODUL_LANG_NEW','MVV: Sprache zu Modul Zuweisung erstellen','%user weist dem Modul %modul(%affected) die Unterrichtssprache %language(%coaffected) zu.',1,0,NULL,'MVV','core',NULL,NULL),
('4869cd69f20d4d7ed4207e027d763a73','INST_USER_STATUS','Einrichtungsnutzerstatus ändern','%user ändert Status für %user(%coaffected) in Einrichtung %inst(%affected): %info.',1,0,NULL,NULL,NULL,NULL,NULL),
('494b5df89948da383d087107d4c0bbec','MVV_LVSEMINAR_DEL','MVV: LV-Gruppe zu Veranstaltung Zuweisung löschen','%user löscht die Zuweisung der LV-Gruppe %lvgruppe(%affected) zur Veranstaltung %sem(%coaffected).',1,0,NULL,'MVV','core',NULL,NULL),
('499a15daf534b3d810e6b9bac9a00d3e','MVV_MODUL_USER_NEW','MVV: Person zu Modul Zuweisung erstellen','%user weist dem Modul %modul(%affected) %user(%coaffected) als %gruppe zu.',1,0,NULL,'MVV','core',NULL,NULL),
('49d58ac93d608d731696eb75b29a1836','MVV_FACH_NEW','MVV: Fach erstellen','%user erstellt neues Fach %fach(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('4b08c6b8539466a1f1968b44ec2996ed','MVV_LVMODULTEIL_DEL','MVV: LV-Gruppe zu Modulteil Zuweisung löschen','%user löscht die Zuweisung der LV-Gruppe %lv(%affected) zum Modulteil %modulteil(%coaffected).',1,0,NULL,'MVV','core',NULL,NULL),
('4dd6b4101f7bf3bd7fe8374042da95e9','USER_NEWPWD','Neues Passwort','%user generiert neues Passwort für %user(%affected)',1,0,NULL,NULL,NULL,NULL,NULL),
('4e2cf05ca311e5a616a7612ce8f5a885','MVV_MODULTEIL_STGTEILABS_DEL','MVV: Studiengangteilabschnitt zu Modulteil Zuweisung löschen','%user löscht die Zuweisung des Modulteils %modulteil(%affected) im %fachsem. des Studiengangteilabschnitt %stgteilabs(%coaffected).',1,0,NULL,'MVV','core',NULL,NULL),
('4e705f2d91569d31dd0c9c197b3f28b5','MVV_FILE_FILEREF_NEW','MVV: Datei erstellen','%user erstellt neue Datei %fileref(%affected).',1,0,NULL,'MVV','core',1716385357,1716385357),
('535010528d6c012ec0e3535e2d754f66','SEM_USER_ADD','In Veranstaltung eingetragen','%user hat %user(%coaffected) für %sem(%affected) mit dem status %info eingetragen. (%dbg_info)',0,0,NULL,NULL,NULL,NULL,NULL),
('54fb2968cd737ed53f300864ee507260','USER_UNLOCK','Nutzer wird entsperrt','%user entsperrt %user(%affected) (%info)',1,0,NULL,NULL,NULL,NULL,NULL),
('566a614092e9f502d9340467ecef59d1','MVV_STGTEILVERSION_UPDATE','MVV: Studiengangteilversion ändern','%user ändert Studiengangteilversion %version(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('5a34b0ea6a824f96c0117a035d1cf9e9','MVV_DOKUMENT_UPDATE','MVV: Dokument ändern','%user ändert Dokument %dokument(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('5b96f2fe994637253ba0fe4a94ad1b98','SEM_ARCHIVE','Veranstaltung archivieren','%user archiviert %info (ID: %affected).',1,0,NULL,NULL,NULL,NULL,NULL),
('5f75cc8482e27c430eb5714b70c63c7d','MVV_FILE_NEW','MVV: Material/Dokument erstellen','%user erstellt neues Material/Dokument %file(%affected).',1,0,NULL,'MVV','core',1716385357,1716385357),
('5f8fda12a4c0bd6eadbb94861de83696','SEM_ADD_CYCLE','Regelmäßige Zeit hinzugefügt','%user hat in %sem(%affected) die regelmäßige Zeit %info hinzugefügt.',1,0,NULL,NULL,NULL,NULL,NULL),
('5fd9b4ddb5c4e035c0b0e7751613cd94','MVV_STGTEILABS_MODUL_NEW','MVV: Stgteilabschnitt-Modul Zuweisung erstellen','%user weist dem Studiengangteilabschnitt %stgteilabs(%affected) dem Modul %Modul(%coaffected) zu.',1,0,NULL,'MVV','core',NULL,NULL),
('63031a1eb903c1092752521ccf4b7456','FOLDER_DELETE','Nutzer löscht Ordner','%user löscht Datei %info (Folder-Id: %affected)',1,0,NULL,NULL,NULL,NULL,NULL),
('63042706e5cd50924987b9515e1e6cae','INST_USER_ADD','Benutzer zu Einrichtung hinzufügen','%user fügt %user(%coaffected) zu Einrichtung %inst(%affected) mit Status %info hinzu.',1,0,NULL,NULL,NULL,NULL,NULL),
('64c30d6741b4799dc06456cb8b5fbab9','USER_LOCK','Nutzer wird gesperrt','%user sperrt %user(%affected) (%info)',1,0,NULL,NULL,NULL,NULL,NULL),
('65c495cf5d5fcd7af862106bf13cff23','MVV_STGTEIL_UPDATE','MVV: Studiengangteil ändern','%user ändert Studiengangteil %stgteil(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('687433f4cf1b36cb93ad417738236484','MVV_STGTEILABS_UPDATE','MVV: Studiengangteilabschnitt ändern','%user ändert Studiengangteilabschnitt %stgteilabs(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('6af3ec5cc886036fd7795a66035a7cbd','MIGRATE_DOWN','Migration wird zurückgenommen','%user hat Migration %affected zurückgenommen (Domain: %coaffected)',1,0,NULL,NULL,NULL,1640797279,1640797279),
('6be59dcd70197c59d7bf3bcd3fec616f','INST_USER_DEL','Benutzer aus Einrichtung löschen','%user löscht %user(%coaffected) aus Einrichtung %inst(%affected).',1,0,NULL,NULL,NULL,NULL,NULL),
('6c5d5ed836c464be1c5547adcec3eae0','MVV_MODULTEIL_LANG_UPDATE','MVV: Sprache zu Modulteil Zuweisung ändern','%user ändert die Zuweisung der Unterrichtssprache %language(%coaffected) zum Modulteil %modulteil(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('6e2b789a57b9125af59c0273f5b47cb1','SEM_USER_DEL','Aus Veranstaltung ausgetragen','%user hat %user(%coaffected) aus %sem(%affected) ausgetragen. (%info)',0,0,NULL,NULL,NULL,NULL,NULL),
('6f4bb66c1caf89879d89f3b1921a93dd','SEM_DELETE_CYCLE','Regelmäßige Zeit gelöscht','%user hat in %sem(%affected) die regelmäßige Zeit %info gelöscht.',1,0,NULL,NULL,NULL,NULL,NULL),
('700efdc3f172bb73d326ce9824ec2580','MVV_FILE_RANGE_UPDATE','MVV: Zuordnung von Material/Dokument zu Bereich ändern.','%user ändert Zuordnung von Material/Dokument %fileref(%affected) zu Bereich %range(%coaffected).',1,0,NULL,'MVV','core',1716385357,1716385357),
('71bfea5eb0d9a85d1247b83383ac5b7e','MVV_MODUL_DESK_NEW','MVV: Modul Deskriptor erstellen','%user erstellt neuen Modul Deskriptor %moduldesk(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('754708c8c0c61a916855c5031014acbb','SEM_DELETE_STUDYAREA','Studienbereich aus Veranst. löschen','%user entfernt Studienbereich \"%studyarea(%coaffected)\" aus %sem(%affected).',0,0,NULL,NULL,NULL,NULL,NULL),
('770f76504dcbbfa6bdc51a8c0f6df4b2','MVV_STGTEILABS_DEL','MVV: Studiengangteilabschnitt löschen','%user löscht Studiengangteilabschnitt %stgteilabs(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('77df7d29c7a5cde1dff72a4a37f85414','MIGRATE_UP','Migration wird durchgeführt','%user hat Migration %affected ausgeführt (Domain: %coaffected)',1,0,NULL,NULL,NULL,1640797279,1640797279),
('7d26ffbf73103601966f7517e40d7e66','RES_REQUEST_NEW','Neue Raumanfrage','%user stellt neue Raumanfrage für %sem(%affected), gewünschter Raum: %res(%coaffected), %info',0,0,NULL,NULL,NULL,NULL,NULL),
('7d37d874592eea50eef5239fb7b8e3d7','MVV_MODULTEIL_LANG_DEL','MVV: Sprache zu Modulteil Zuweisung löschen','%user löscht die Zuweisung der Unterrichtssprache %language(%coaffected) zum Modulteil %modulteil(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('7f2ec1cbf988eee849de7cfa031b68f9','MVV_STGTEILVERSION_DEL','MVV: Studiengangteilversion löschen','%user löscht Studiengangteilversion %version(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('7f335b5d0d9f8d37652718cb89937b38','MVV_STGTEIL_NEW','MVV: Studiengangteil erstellen','%user erstellt neuen Studiengangteil %stgteil(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('811c6650f12229280382ff2793a4cb22','MVV_FILE_DELETE','MVV: Material/Dokument löschen','%user löscht Material/Dokument %file(%affected).',1,0,NULL,'MVV','core',1716385357,1716385357),
('8216cba6119cf4a4de82ec3ce8ac51b7','MVV_MODUL_USER_UPDATE','MVV: Person zu Modul Zuweisung ändern','%user ändert die Zuweisung von %user(%coaffected) als %gruppe zum Modul %modul(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('89114dcd6f02dd7f94488a616c21a7c3','PLUGIN_ENABLE','Plugin einschalten','%user hat in Veranstaltung %sem(%affected) das Plugin %plugin(%coaffected) aktiviert.',1,0,NULL,NULL,NULL,NULL,NULL),
('897207a36c411d736947052219624b72','USER_CHANGE_PASSWORD','Nutzerpasswort geändert','%user ändert/setzt das Passwort für %user(%affected)',0,0,NULL,NULL,NULL,NULL,NULL),
('8aad296e52423452fc75cabaf2bee384','USER_CHANGE_USERNAME','Benutzernamen ändern','%user ändert/setzt Benutzernamen für %user(%affected): %info.',1,0,NULL,NULL,NULL,NULL,NULL),
('8f87cf1a8546e9671244fba1ac51e805','MVV_LVGRUPPE_DEL','MVV: LV-Gruppe löschen','%user löscht LV-Gruppe %lvgruppe(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('9123d360316ba28ddb32c0ed1a0320f2','STUDYAREA_DELETE','Studienbereich löschen','%user entfernt Studienbereich %studyarea(%affected).',0,0,NULL,NULL,NULL,NULL,NULL),
('91251b5768b312ca23d6721cdc99a005','MVV_KATEGORIE_NEW','MVV: Abschluss-Kategorie erstellen','%user erstellt neue Abschluss-Kategorie %abskategorie(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('9179d3cf4e0353f9874bcde072d12b30','RES_REQUEST_DENY','Abgelehnte Raumanfrage','%user lehnt Raumanfrage für %sem(%affected), Raum: %res(%coaffected) ab. %info',0,0,NULL,NULL,NULL,NULL,NULL),
('95935c7997427ea42a3dd6be05b51e81','MVV_MODULTEIL_UPDATE','MVV: Modulteil ändern','%user ändert Modulteil %modulteil(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('997cf01328d4d9f36b9f50ac9b6ace47','SEM_DELETE_SINGLEDATE','Einzeltermin löschen','%user löscht Einzeltermin %singledate(%affected) in %sem(%coaffected).',1,0,NULL,NULL,NULL,NULL,NULL),
('9a7a5112de76fa0b8bd8910174d5f107','MVV_STGTEILBEZ_UPDATE','MVV: Studiengangteil-Bezeichnung ändern','%user ändert Studiengangteil-Bezeichnung %stgteilbez(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('9d13643a1833c061dc3d10b4fb227f12','SEM_SET_ENDSEMESTER','Semesterlaufzeit ändern','%user hat in %sem(%affected) die Laufzeit auf %semester(%coaffected) geändert',1,0,NULL,NULL,NULL,NULL,NULL),
('9d642dc93540580d42ba2ea502c3fbf6','SINGLEDATE_CHANGE_TIME','Einzeltermin bearbeiten','%user hat in %sem(%affected) den Einzeltermin %singledate(%coaffected) geändert.',1,0,NULL,NULL,NULL,NULL,NULL),
('9ed46a3ca3d4f43e17f91e314224dcae','SEM_CHANGE_CYCLE','Regelmäßige Zeit geändert','%user hat in %sem(%affected) die regelmäßige Zeit %info geändert',1,0,NULL,NULL,NULL,NULL,NULL),
('9eea9c8ec3fa6916fae974559f3a6e64','MVV_STGTEILABS_MODUL_UPDATE','MVV: Stgteilabschnitt-Modul Zuweisung ändern','%user ändert die Zuweisung des Studiengangteilabschnitts %stgteilabs(%affected) zum Modul %modul(%coaffected).',1,0,NULL,'MVV','core',NULL,NULL),
('a06bd1ca1b5eec038c079042eb25acb0','MVV_DOKUMENT_NEW','MVV: Dokument erstellen','%user erstellt neues Dokument %dokument(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('a0928e74639fd2a55f5d4d2a3c5a8e71','RES_REQUEST_DEL','Raumanfrage löschen','%user löscht Raumanfrage für %sem(%affected).',0,0,NULL,NULL,NULL,NULL,NULL),
('a0b8799fa671e0ce6069483e0c4b5123','MVV_ABS_ZUORD_NEW','MVV: Abschluss-Kategorien Zuweisung erstellen','%user weist den Abschluss %abschluss(%affected) der Kategorie %abskategorie(%coaffected) zu.',1,0,NULL,'MVV','core',NULL,NULL),
('a3856b6531e2f79d158b5ebfb998e5db','RES_ASSIGN_DEL_SEM','VA-Buchung löschen','%user löscht Ressourcenbelegung für %res(%affected) in Veranstaltung %sem(%coaffected), %info.',0,0,NULL,NULL,NULL,NULL,NULL),
('a3db2d066861fd1cf1d532d2a736d495','MVV_STUDIENGANG_DEL','MVV: Studiengang löschen','%user löscht Studiengang %stg(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('a66c9e04e9c41bf5cc4d23fa509a8667','PLUGIN_DISABLE','Plugin ausschalten','%user hat in Veranstaltung %sem(%affected) das Plugin %plugin(%coaffected) deaktiviert.',1,0,NULL,NULL,NULL,NULL,NULL),
('a92afa63584cc2a62d2dd2996727b2c5','USER_CREATE','Nutzer anlegen','%user legt Nutzer %user(%affected) an.',1,0,NULL,NULL,NULL,NULL,NULL),
('a94706b41493e32f8336194262418c01','SEM_INVISIBLE','Veranstaltung unsichtbar schalten','%user versteckt %sem(%affected).',1,0,NULL,NULL,NULL,NULL,NULL),
('aa12de984598c527bfb8b118affaf34a','MVV_STG_STGTEIL_UPDATE','MVV: Studiengang zu Studiengangteil Zuweisung ändern','%user ändert die Zuweisung des Studienganges %stg(%affected) zum Studiengangteil %stgteil(%coaffected).',1,0,NULL,'MVV','core',NULL,NULL),
('ad405d21c2e0df758ee8a61ed39901fe','MVV_LVMODULTEIL_UPDATE','MVV: LV-Gruppe zu Modulteil Zuweisung ändern','%user ändert die Zuweisung der LV-Gruppe %lv(%affected) zum Modulteil %modulteil(%coaffected).',1,0,NULL,'MVV','core',NULL,NULL),
('b035ea1a197edc6271fafa4094f87a57','MVV_DOK_ZUORD_NEW','MVV: Dokumentzuordnung erstellen','%user weist das Dokument %dokument(%affected) %object_type(%coaffected) zu.',1,0,NULL,'MVV','core',NULL,NULL),
('b19d01e45715df05f4b060cd56dc204f','MVV_MODULTEIL_DESK_UPDATE','MVV: Modulteil Deskriptor ändern','%user ändert Modulteil Deskriptor %modulteildesk(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('b205bde204b5607e036c10557a6ce149','SEM_SET_STARTSEMESTER','Startsemester ändern','%user hat in %sem(%affected) das Startsemester auf %semester(%coaffected) geändert.',1,0,NULL,NULL,NULL,NULL,NULL),
('b38ecbb1fddf6c3868a6a9a75bab8ef8','MVV_KATEGORIE_DEL','MVV: Abschluss-Kategorie löschen','%user löscht Abschluss-Kategorie %abskategorie(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('b5e3e8401e7e92051c13ba7b46e28f75','MVV_STGTEILVERSION_NEW','MVV: Studiengangteilversion erstellen','%user erstellt neue Studiengangteilversion %version(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('b8e940588cd8d5181774377586b85202','MVV_STGTEILBEZ_NEW','MVV: Studiengangteil-Bezeichnung erstellen','%user erstellt neue Studiengangteil-Bezeichnung %stgteilbez(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('bace236af595612b77943bcb47a0a7fe','MVV_STUDIENGANG_UPDATE','MVV: Studiengang ändern','%user ändert Studiengang %stg(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('bd2103035a8021942390a78a431ba0c4','DUMMY','Dummy-Aktion','%user tut etwas.',1,0,NULL,NULL,NULL,NULL,NULL),
('bd78090961a15a8010a566a6cd1355f2','MVV_DOK_ZUORD_UPDATE','MVV: Dokumentzuordnung ändern','%user ändert die Zuweisung des Dokumentes %dokument(%affected) zu %object_type(%coaffected).',1,0,NULL,'MVV','core',NULL,NULL),
('bf192518a9c3587129ed2fdb9ea56f73','SEM_DELETE_FROM_ARCHIVE','Veranstaltung aus Archiv löschen','%user löscht %info aus dem Archiv (ID: %affected).',1,0,NULL,NULL,NULL,NULL,NULL),
('c1d980ac5c5de271bd1471a11a3e37af','MVV_MODULINST_DEL','MVV: Modul-Einrichtung Beziehung löschen','%user löscht die Zuweisung der Einrichtungen %inst(%coaffected) zum Modul %modul(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('c36fa0f804cde78a6dcb1c30c2ee47ba','SEM_DELETE_REQUEST','Raumanfrage gelöscht','%user hat in %sem(%affected) die Raumanfrage für die gesamte Veranstaltung gelöscht.',1,0,NULL,NULL,NULL,NULL,NULL),
('c4b1d3305d017935c8b6946996594172','MVV_FACH_UPDATE','MVV: Fach ändern','%user ändert Fach %fach(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('c6a23a780aa2a219ddd0bb2445c19bf8','MVV_MODULTEIL_LANG_NEW','MVV: Sprache zu Modulteil Zuweisung erstellen','%user weist dem Modulteil %modulteil(%affected) die Unterrichtssprache %language(%coaffected) zu.',1,0,NULL,'MVV','core',NULL,NULL),
('ca216ccdf753f59ba7fd621f7b22f7bd','USER_CHANGE_NAME','Personennamen ändern','%user ändert/setzt Name für %user(%affected) - %info.',1,0,NULL,NULL,NULL,NULL,NULL),
('cbf93b2a248642289c2ad4b3d59b9d55','MVV_FACHINST_DEL','MVV: Fach-Einrichtung Zuweisung löschen','%user löscht die Zuweisung des Faches %fach(%affected) zur Einrichtung %inst(%coaffected).',1,0,NULL,'MVV','core',NULL,NULL),
('cf8986a67e67ca273e15fd9230f6e872','USER_CHANGE_TITLE','Akademische Titel ändern','%user ändert/setzt akademischen Titel für %user(%affected) - %info.',1,0,NULL,NULL,NULL,NULL,NULL),
('d07c8b37c6d3e206cd012d07ba8028b1','SEM_CHANGED_RIGHTS','Veranstaltungsrechte geändert','%user hat %user(%coaffected) in %sem(%affected) als %info eingetragen. (%dbg_info)',0,0,NULL,NULL,NULL,NULL,NULL),
('d18d750fb2c166e1c425976e8bca96e7','USER_CHANGE_EMAIL','E-Mail-Adresse ändern','%user ändert/setzt E-Mail-Adresse für %user(%affected): %info.',1,0,NULL,NULL,NULL,NULL,NULL),
('d1989a21fc77ffc34e705bb3dc215ebb','STATUSGROUP_REMOVE_USER','Nutzer wird aus einer Statusgruppe gelöscht','%user entfernt %user(%affected) aus %group(%coaffected).',1,0,NULL,NULL,NULL,NULL,NULL),
('d2ec04856dc8ca6776dac29fa9842aeb','MVV_CONTACT_NEW','MVV: Kontaktperson erstellen','%user erstellt neue Kontaktperson %contact(%affected).',1,0,NULL,'MVV','core',1716385357,1716385357),
('d4e99ffb6ffb32a20c0d9075bb73889d','MVV_ABS_ZUORD_UPDATE','MVV: Abschluss-Kategorien Zuweisung  ändern','%user ändert die Zuweisung des Abschlusses %abschluss(%affected) zur Kategorie %abskategorie(%coaffected).',1,0,NULL,'MVV','core',NULL,NULL),
('d5706ac17db2223637042af1c107ff44','MVV_EXTERN_CONTACT_DELETE','MVV: Externe Kontaktperson löschen','%user löscht externe Kontaktperson %contact(%affected).',1,0,NULL,'MVV','core',1716385357,1716385357),
('d8e863ca143ff87cce89f17b2e3b409e','MVV_ABSCHLUSS_UPDATE','MVV: Abschluss ändern','%user ändert Abschluss %abschluss(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('dcd16239a49c367e37faf8ffe9ae0081','SEM_ADD_TO_GROUP','Veranstaltung zu Gruppe hinzufügen','%user ordnet Veranstaltung %sem(%affected) der Gruppe %sem(%coaffected) zu.',1,0,NULL,NULL,'core',NULL,NULL),
('df41cc74f6fd857b1690e36dafa070a9','MVV_MODUL_LANG_DEL','MVV: Sprache zu Modul Zuweisung löschen','%user löscht die Zuweisung der Unterrichtssprache %language(%coaffected) zum Modul %modul(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('e2c703a9167804463112284853b9545b','MVV_MODUL_UPDATE','MVV: Modul ändern','%user ändert Modul %modul(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('e406e407501c8418f752e977182cd782','USER_CHANGE_PERMS','Globalen Nutzerstatus ändern','%user ändert/setzt globalen Status von %user(%affected): %info',1,0,NULL,NULL,NULL,NULL,NULL),
('e5c0ecfea7d12ed95ea485d4ec18c9ae','MVV_MODUL_LANG_UPDATE','MVV: Sprache zu Modul Zuweisung ändern','%user ändert die Zuweisung der Unterrichtssprache %language(%coaffected) zum Modul %modul(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('e694e86b2ec50bc0b99864acc947ed78','MVV_LVGRUPPE_NEW','MVV: LV-Gruppe erstellen','%user erstellt neue LV-Gruppe %lvgruppe(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('e7449d98b8ca69d16f13c0a342a8db41','MVV_ABSCHLUSS_NEW','MVV: Abschluss erstellen','%user erstellt neuen Abschluss %abschluss(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('e8423589894e5df742804e57f54fa5aa','MVV_MODULTEIL_STGTEILABS_UPDATE','MVV: Studiengangteilabschnitt zu Modulteil Zuweisung ändern','%user ändert die Zuweisung des Modulteils %modulteil(%affected) im %fachsem. des Studiengangteilabschnitt %stgteilabs(%coaffected).',1,0,NULL,'MVV','core',NULL,NULL),
('e8646729e5e04970954c8b9679af389b','USER_DEL','Benutzer löschen','%user löscht %user(%affected) (%info)',1,0,NULL,NULL,NULL,NULL,NULL),
('e8b1105ca4f2305ef0db6c961d2fbe4c','RES_ASSIGN_SINGLE','Buchen einer Ressource (Einzel)','%user bucht %res(%affected) direkt (%info).',0,0,NULL,NULL,NULL,NULL,NULL),
('e8d19770cc42028d124ca5a91beb1085','MVV_FILE_RANGE_DELETE','MVV: Zuordnung von Material/Dokument zu Bereich löschen','%user löscht Zuordnung von Material/Dokument %fileref(%affected) von Bereich %range/%coaffected).',1,0,NULL,'MVV','core',1716385357,1716385357),
('eac0850398466b86ec6af4068cff74ab','MVV_MODULTEIL_DESK_DEL','MVV: Modulteil Deskriptor löschen','%user löscht Modulteil Deskriptor %modulteildesk(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('efe4f97f61a5f61ac69d9edad7d9b16f','MVV_CONTACT_UPDATE','MVV: Kontaktperson ändern','%user ändert Kontaktperson %contact(%affected).',1,0,NULL,'MVV','core',1716385357,1716385357),
('f363c6db07203dfcec893b1b8fc0eaee','MVV_FACHINST_UPDATE','MVV: Fach-Einrichtung Zuweisung ändern','%user ändert die Zuweisung des Faches %fach(%affected) zur Einrichtung %inst(%coaffected).',1,0,NULL,'MVV','core',NULL,NULL),
('f858b05c11f5faa2198a109a783087a8','SEM_CREATE','Veranstaltung anlegen','%user legt %sem(%affected) an.',1,0,NULL,NULL,NULL,NULL,NULL),
('f97335d7f45fd87a4e5e2c1d17f38dc0','MVV_MODUL_DESK_UPDATE','MVV: Modul Deskriptor ändern','%user ändert Modul Deskriptor %moduldesk(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('fb550711e786f5d7d6a9ef8b4eb915f6','MVV_MODULINST_NEW','MVV: Modul-Einrichtung Beziehung erstellen','%user weist dem Modul %modul(%affected) die Einrichtungen %inst(%coaffected) zu.',1,0,NULL,'MVV','core',NULL,NULL),
('fc72d34ddb15a9819919fc42716830b3','MVV_MODUL_NEW','MVV: Modul erstellen','%user erstellt neues Modul %modul(%affected).',1,0,NULL,'MVV','core',NULL,NULL),
('fd599d69434d1024255fac81ed90a6ac','MVV_CONTACT_RANGE_NEW','MVV: Kontaktperson zuordnen','%user ordnet die Kontaktperson %contact(%affected) dem Bereich %range(%coaffected) zu.',1,0,NULL,'MVV','core',1716385357,1716385357),
('fd74339a9ea038d084569e33e2655b6a','CHANGE_INSTITUTE_DATA','Beteiligte Einrichtungen geändert','%user hat in Veranstaltung %sem(%affected) die Daten geändert. %info',0,0,NULL,NULL,NULL,NULL,NULL),
('ff806b4b26f8bc8c3e65e29d14176cd9','RES_REQUEST_RESOLVE','Aufgelöste Raumanfrage','%user löst Raumanfrage für %sem(%affected), Raum %res(%coaffected) auf.',0,0,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `log_actions` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `log_events`
--

LOCK TABLES `log_events` WRITE;
/*!40000 ALTER TABLE `log_events` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `log_events` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `login_faq`
--

LOCK TABLES `login_faq` WRITE;
/*!40000 ALTER TABLE `login_faq` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `login_faq` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `loginbackgrounds`
--

LOCK TABLES `loginbackgrounds` WRITE;
/*!40000 ALTER TABLE `loginbackgrounds` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `loginbackgrounds` VALUES
(1,'Login-Hintergrund.jpg',0,1,1,NULL,NULL),
(2,'Login-Hintergrund-mobil.jpg',1,0,1,NULL,NULL);
/*!40000 ALTER TABLE `loginbackgrounds` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `lti_deployments`
--

LOCK TABLES `lti_deployments` WRITE;
/*!40000 ALTER TABLE `lti_deployments` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `lti_deployments` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `lti_grade`
--

LOCK TABLES `lti_grade` WRITE;
/*!40000 ALTER TABLE `lti_grade` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `lti_grade` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `lti_resource_links`
--

LOCK TABLES `lti_resource_links` WRITE;
/*!40000 ALTER TABLE `lti_resource_links` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `lti_resource_links` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `lti_tool_privacy_settings`
--

LOCK TABLES `lti_tool_privacy_settings` WRITE;
/*!40000 ALTER TABLE `lti_tool_privacy_settings` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `lti_tool_privacy_settings` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `lti_tools`
--

LOCK TABLES `lti_tools` WRITE;
/*!40000 ALTER TABLE `lti_tools` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `lti_tools` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `mail_queue_entries`
--

LOCK TABLES `mail_queue_entries` WRITE;
/*!40000 ALTER TABLE `mail_queue_entries` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `mail_queue_entries` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `massmail_filter`
--

LOCK TABLES `massmail_filter` WRITE;
/*!40000 ALTER TABLE `massmail_filter` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `massmail_filter` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `massmail_markers`
--

LOCK TABLES `massmail_markers` WRITE;
/*!40000 ALTER TABLE `massmail_markers` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `massmail_markers` VALUES
(1,'FULLNAME','Voller Name','database','Hier wird der volle Name der jeweiligen Person eingesetzt, z.B. \"Prof. Max Mustermann, PhD\".',0,'user_info.title_front {{FIRSTNAME}} {{LASTNAME}} user_info.title_rear',NULL,NULL,2,1754464709,1754464709),
(2,'FIRSTNAME','Vorname','database','Hier wird der Vorname der jeweiligen Person eingesetzt.',0,'auth_user_md5.Vorname',NULL,NULL,3,1754464709,1754464709),
(3,'LASTNAME','Nachname','database','Hier wird der Nachname der jeweiligen Person eingesetzt.',0,'auth_user_md5.Nachname',NULL,NULL,4,1754464709,1754464709),
(4,'USERNAME','Benutzername','database','Hier wird der Benutzername der jeweiligen Person eingesetzt.',0,'auth_user_md5.username',NULL,NULL,5,1754464709,1754464709),
(5,'SEHRGEEHRTE','Anrede mit vollem Namen','text','Hier wird eine Anrede erzeugt: \"Sehr geehrte Michaela Musterfrau\" bzw. \"Sehr geehrter Max Mustermann\".',0,'Sehr geehrter {{FULLNAME}}','Sehr geehrte {{FULLNAME}}','Sehr geehrte/r {{FULLNAME}}',1,1754464709,1754464709),
(6,'DEARSIRMADAM','Anrede (englisch) mit vollem Namen','text','Creates a Salutation: \"Dear Jane Doe\" or \"Dear John Doe\".',0,'Dear {{FULLNAME}}',NULL,NULL,6,1754464709,1754464709),
(7,'TOKEN','Personalisierter Code o.ä.','token','Hier wird ein persönlicher Teilnahmecode o.ä. aus einer hochgeladenen Datei eingesetzt.',1,'massmail_tokens.token',NULL,NULL,7,1754464709,1754464709);
/*!40000 ALTER TABLE `massmail_markers` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `massmail_messages`
--

LOCK TABLES `massmail_messages` WRITE;
/*!40000 ALTER TABLE `massmail_messages` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `massmail_messages` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `massmail_permission_degree`
--

LOCK TABLES `massmail_permission_degree` WRITE;
/*!40000 ALTER TABLE `massmail_permission_degree` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `massmail_permission_degree` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `massmail_permission_institute`
--

LOCK TABLES `massmail_permission_institute` WRITE;
/*!40000 ALTER TABLE `massmail_permission_institute` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `massmail_permission_institute` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `massmail_permission_subject`
--

LOCK TABLES `massmail_permission_subject` WRITE;
/*!40000 ALTER TABLE `massmail_permission_subject` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `massmail_permission_subject` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `massmail_permissions`
--

LOCK TABLES `massmail_permissions` WRITE;
/*!40000 ALTER TABLE `massmail_permissions` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `massmail_permissions` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `massmail_tokens`
--

LOCK TABLES `massmail_tokens` WRITE;
/*!40000 ALTER TABLE `massmail_tokens` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `massmail_tokens` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `media_cache`
--

LOCK TABLES `media_cache` WRITE;
/*!40000 ALTER TABLE `media_cache` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `media_cache` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `message`
--

LOCK TABLES `message` WRITE;
/*!40000 ALTER TABLE `message` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `message` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `message_tags`
--

LOCK TABLES `message_tags` WRITE;
/*!40000 ALTER TABLE `message_tags` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `message_tags` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `message_user`
--

LOCK TABLES `message_user` WRITE;
/*!40000 ALTER TABLE `message_user` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `message_user` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `mvv_abschl_kategorie`
--

LOCK TABLES `mvv_abschl_kategorie` WRITE;
/*!40000 ALTER TABLE `mvv_abschl_kategorie` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `mvv_abschl_kategorie` VALUES
('1','Bachelor-Abschlüsse',NULL,NULL,1,'','',1545135981,1545135981),
('2','Master-Abschlüsse',NULL,NULL,2,'','',1545135981,1545135981);
/*!40000 ALTER TABLE `mvv_abschl_kategorie` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `mvv_abschl_zuord`
--

LOCK TABLES `mvv_abschl_zuord` WRITE;
/*!40000 ALTER TABLE `mvv_abschl_zuord` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `mvv_abschl_zuord` VALUES
('228234544820cdf75db55b42d1ea3ecc','1',0,'','',1545135981,1545135981),
('c7f569e815a35cf24a515a0e67928072','2',0,'','',1545135981,1545135981);
/*!40000 ALTER TABLE `mvv_abschl_zuord` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `mvv_aufbaustudiengang`
--

LOCK TABLES `mvv_aufbaustudiengang` WRITE;
/*!40000 ALTER TABLE `mvv_aufbaustudiengang` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `mvv_aufbaustudiengang` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `mvv_contacts`
--

LOCK TABLES `mvv_contacts` WRITE;
/*!40000 ALTER TABLE `mvv_contacts` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `mvv_contacts` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `mvv_contacts_ranges`
--

LOCK TABLES `mvv_contacts_ranges` WRITE;
/*!40000 ALTER TABLE `mvv_contacts_ranges` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `mvv_contacts_ranges` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `mvv_extern_contacts`
--

LOCK TABLES `mvv_extern_contacts` WRITE;
/*!40000 ALTER TABLE `mvv_extern_contacts` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `mvv_extern_contacts` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `mvv_fach_inst`
--

LOCK TABLES `mvv_fach_inst` WRITE;
/*!40000 ALTER TABLE `mvv_fach_inst` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `mvv_fach_inst` VALUES
('6b9ac09535885ca55e29dd011e377c0a','1535795b0d6ddecac6813f5f6ac47ef2',0,'','',1545135981,1545135981),
('f981c9b42ca72788a09da4a45794a737','1535795b0d6ddecac6813f5f6ac47ef2',0,'','',1545135981,1545135981);
/*!40000 ALTER TABLE `mvv_fach_inst` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `mvv_files`
--

LOCK TABLES `mvv_files` WRITE;
/*!40000 ALTER TABLE `mvv_files` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `mvv_files` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `mvv_files_filerefs`
--

LOCK TABLES `mvv_files_filerefs` WRITE;
/*!40000 ALTER TABLE `mvv_files_filerefs` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `mvv_files_filerefs` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `mvv_files_ranges`
--

LOCK TABLES `mvv_files_ranges` WRITE;
/*!40000 ALTER TABLE `mvv_files_ranges` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `mvv_files_ranges` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `mvv_lvgruppe`
--

LOCK TABLES `mvv_lvgruppe` WRITE;
/*!40000 ALTER TABLE `mvv_lvgruppe` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `mvv_lvgruppe` VALUES
('36677a1d815d4528bebf89833d168f56','GES-SK-01: Vorlesung',NULL,'','',1545135981,1545135981),
('40e1ada2c00e13a09e88143934e76efa','INF-CB: Vorlesung',NULL,'','',1545135981,1545135981),
('9938b594f4c50c21ed235b2f92e82177','INF-AA-01: Seminar',NULL,'','',1545135981,1545135981),
('daf94f1c25809886095022d124aaf1fa','GES-MIT: Vorlesung',NULL,'','',1545135981,1545135981);
/*!40000 ALTER TABLE `mvv_lvgruppe` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `mvv_lvgruppe_modulteil`
--

LOCK TABLES `mvv_lvgruppe_modulteil` WRITE;
/*!40000 ALTER TABLE `mvv_lvgruppe_modulteil` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `mvv_lvgruppe_modulteil` VALUES
('36677a1d815d4528bebf89833d168f56','7b89d02083ee74f2a52a17e913069cdf',9999,NULL,'','',1545135981,1545135981),
('40e1ada2c00e13a09e88143934e76efa','a34a823cb6c4b55f8f63f74088bbdb86',9999,NULL,'','',1545135981,1545135981),
('9938b594f4c50c21ed235b2f92e82177','ad5d5bdc988850fde010cc891d53469c',9999,NULL,'','',1545135981,1545135981),
('daf94f1c25809886095022d124aaf1fa','80f915287984887b2e7ec9b359418e56',9999,NULL,'','',1545135981,1545135981);
/*!40000 ALTER TABLE `mvv_lvgruppe_modulteil` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `mvv_lvgruppe_seminar`
--

LOCK TABLES `mvv_lvgruppe_seminar` WRITE;
/*!40000 ALTER TABLE `mvv_lvgruppe_seminar` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `mvv_lvgruppe_seminar` VALUES
('9938b594f4c50c21ed235b2f92e82177','a07535cf2f8a72df33c12ddfa4b53dde','76ed43ef286fb55cf9e41beadb484a9f','76ed43ef286fb55cf9e41beadb484a9f',1669044181,1669044181);
/*!40000 ALTER TABLE `mvv_lvgruppe_seminar` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `mvv_modul`
--

LOCK TABLES `mvv_modul` WRITE;
/*!40000 ALTER TABLE `mvv_modul` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `mvv_modul` VALUES
('36677a1d815d4528bebf89833d168f56',NULL,NULL,'','GES-SK-01',NULL,NULL,NULL,NULL,NULL,'1','1','',2.00,NULL,NULL,NULL,'1','genehmigt',NULL,'','de_DE','','',1545135981,1545135981),
('40e1ada2c00e13a09e88143934e76efa',NULL,NULL,'','INF-CB',NULL,NULL,NULL,NULL,NULL,'1','1','',3.00,NULL,NULL,NULL,'1','genehmigt',NULL,'','de_DE','','',1545135981,1545135981),
('9938b594f4c50c21ed235b2f92e82177',NULL,NULL,'','INF-AA-01',NULL,NULL,NULL,NULL,NULL,'1','1','',6.00,NULL,NULL,NULL,'1','genehmigt',NULL,'','de_DE','','',1545135981,1545135981),
('daf94f1c25809886095022d124aaf1fa',NULL,NULL,'','GES-MIT',NULL,NULL,NULL,NULL,NULL,'1','1','',2.00,NULL,NULL,NULL,'1','genehmigt',NULL,'','de_DE','','',1545135981,1545135981);
/*!40000 ALTER TABLE `mvv_modul` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `mvv_modul_deskriptor`
--

LOCK TABLES `mvv_modul_deskriptor` WRITE;
/*!40000 ALTER TABLE `mvv_modul_deskriptor` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `mvv_modul_deskriptor` VALUES
('36677a1d815d4528bebf89833d168f56','36677a1d815d4528bebf89833d168f56','','Schlüsselkompetenzen Geschichte',NULL,'','Thematischer Überblick zu Inhalten des gewählten Faches',NULL,NULL,NULL,'Jährlich',NULL,NULL,NULL,NULL,'','','','',NULL,'','',1545135981,1545135981),
('40e1ada2c00e13a09e88143934e76efa','40e1ada2c00e13a09e88143934e76efa','','Compilerbau',NULL,'','Einführung in Klassifikation höherer Programmiersprachen, Aufbau von Übersetzern inklusive Codeerzeugung',NULL,NULL,NULL,'unregelmäßig',NULL,NULL,NULL,NULL,'','','Klausur (90 min) oder mündliche Prüfung (30 min)','',NULL,'','',1545135981,1545135981),
('9938b594f4c50c21ed235b2f92e82177','9938b594f4c50c21ed235b2f92e82177','','Authentifizierung und Autorisierung',NULL,'','- Passwortbasierte Authentifizierungsverfahren\n- Zertifikatsbasierte Authentifizierungsverfahren\n- Biometrische Verfahren',NULL,NULL,NULL,'unregelmäßig',NULL,NULL,NULL,NULL,'','Vortrag über ein Teilthema (30 Minuten)','Klausur (120 Minuten) oder mündliche Prüfung (30 Minuten)','',NULL,'','',1545135981,1545135981),
('daf94f1c25809886095022d124aaf1fa','daf94f1c25809886095022d124aaf1fa','','Geschichte des frühen Mittelalters',NULL,'','Entwicklung der mitteralterlichen Gesellschaft in Europa',NULL,NULL,NULL,'Jährlich',NULL,NULL,NULL,NULL,'','','','',NULL,'','',1545135981,1545135981);
/*!40000 ALTER TABLE `mvv_modul_deskriptor` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `mvv_modul_inst`
--

LOCK TABLES `mvv_modul_inst` WRITE;
/*!40000 ALTER TABLE `mvv_modul_inst` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `mvv_modul_inst` VALUES
('36677a1d815d4528bebf89833d168f56','1535795b0d6ddecac6813f5f6ac47ef2','hauptverantwortlich',9999,'','',1545135981,1545135981),
('40e1ada2c00e13a09e88143934e76efa','1535795b0d6ddecac6813f5f6ac47ef2','hauptverantwortlich',9999,'','',1545135981,1545135981),
('9938b594f4c50c21ed235b2f92e82177','1535795b0d6ddecac6813f5f6ac47ef2','hauptverantwortlich',9999,'','',1545135981,1545135981),
('daf94f1c25809886095022d124aaf1fa','1535795b0d6ddecac6813f5f6ac47ef2','hauptverantwortlich',9999,'','',1545135981,1545135981);
/*!40000 ALTER TABLE `mvv_modul_inst` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `mvv_modul_language`
--

LOCK TABLES `mvv_modul_language` WRITE;
/*!40000 ALTER TABLE `mvv_modul_language` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `mvv_modul_language` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `mvv_modulteil`
--

LOCK TABLES `mvv_modulteil` WRITE;
/*!40000 ALTER TABLE `mvv_modulteil` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `mvv_modulteil` VALUES
('7b89d02083ee74f2a52a17e913069cdf','36677a1d815d4528bebf89833d168f56',0,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'','',1545135981,1545135981),
('80f915287984887b2e7ec9b359418e56','daf94f1c25809886095022d124aaf1fa',0,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'','',1545135981,1545135981),
('a34a823cb6c4b55f8f63f74088bbdb86','40e1ada2c00e13a09e88143934e76efa',0,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'','',1545135981,1545135981),
('ad5d5bdc988850fde010cc891d53469c','9938b594f4c50c21ed235b2f92e82177',0,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'','',1545135981,1545135981);
/*!40000 ALTER TABLE `mvv_modulteil` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `mvv_modulteil_deskriptor`
--

LOCK TABLES `mvv_modulteil_deskriptor` WRITE;
/*!40000 ALTER TABLE `mvv_modulteil_deskriptor` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `mvv_modulteil_deskriptor` VALUES
('7b89d02083ee74f2a52a17e913069cdf','7b89d02083ee74f2a52a17e913069cdf','Vorlesung',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'','',1545135981,1545135981),
('80f915287984887b2e7ec9b359418e56','80f915287984887b2e7ec9b359418e56','Vorlesung',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'','',1545135981,1545135981),
('a34a823cb6c4b55f8f63f74088bbdb86','a34a823cb6c4b55f8f63f74088bbdb86','Vorlesung',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'','',1545135981,1545135981),
('ad5d5bdc988850fde010cc891d53469c','ad5d5bdc988850fde010cc891d53469c','Seminar',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'','',1545135981,1545135981);
/*!40000 ALTER TABLE `mvv_modulteil_deskriptor` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `mvv_modulteil_language`
--

LOCK TABLES `mvv_modulteil_language` WRITE;
/*!40000 ALTER TABLE `mvv_modulteil_language` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `mvv_modulteil_language` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `mvv_modulteil_stgteilabschnitt`
--

LOCK TABLES `mvv_modulteil_stgteilabschnitt` WRITE;
/*!40000 ALTER TABLE `mvv_modulteil_stgteilabschnitt` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `mvv_modulteil_stgteilabschnitt` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `mvv_ovl_conflicts`
--

LOCK TABLES `mvv_ovl_conflicts` WRITE;
/*!40000 ALTER TABLE `mvv_ovl_conflicts` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `mvv_ovl_conflicts` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `mvv_ovl_excludes`
--

LOCK TABLES `mvv_ovl_excludes` WRITE;
/*!40000 ALTER TABLE `mvv_ovl_excludes` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `mvv_ovl_excludes` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `mvv_ovl_selections`
--

LOCK TABLES `mvv_ovl_selections` WRITE;
/*!40000 ALTER TABLE `mvv_ovl_selections` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `mvv_ovl_selections` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `mvv_stg_stgteil`
--

LOCK TABLES `mvv_stg_stgteil` WRITE;
/*!40000 ALTER TABLE `mvv_stg_stgteil` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `mvv_stg_stgteil` VALUES
('58f22c9a19b296e7e5fbfa7d7a059c79','e58b0eaf0c09c9fef4cab09cdec8dcc2','1',0,'','',1545135981,1545135981),
('7e5c6adb4152e8d402e5dba26664fa32','f6ec30150732fa6ae913f4c3779e305a','1',0,'','',1545135981,1545135981);
/*!40000 ALTER TABLE `mvv_stg_stgteil` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `mvv_stgteil`
--

LOCK TABLES `mvv_stgteil` WRITE;
/*!40000 ALTER TABLE `mvv_stgteil` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `mvv_stgteil` VALUES
('e58b0eaf0c09c9fef4cab09cdec8dcc2','6b9ac09535885ca55e29dd011e377c0a',NULL,NULL,'im Bachelor (Hauptfach)','','',1545135981,1545135981),
('f6ec30150732fa6ae913f4c3779e305a','f981c9b42ca72788a09da4a45794a737',NULL,NULL,'im Master','','',1545135981,1545135981);
/*!40000 ALTER TABLE `mvv_stgteil` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `mvv_stgteil_bez`
--

LOCK TABLES `mvv_stgteil_bez` WRITE;
/*!40000 ALTER TABLE `mvv_stgteil_bez` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `mvv_stgteil_bez` VALUES
('1','Hauptfach','HF',9999,'','',1545135981,1545135981),
('2','Nebenfach','NF',9999,'','',1545135981,1545135981);
/*!40000 ALTER TABLE `mvv_stgteil_bez` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `mvv_stgteilabschnitt`
--

LOCK TABLES `mvv_stgteilabschnitt` WRITE;
/*!40000 ALTER TABLE `mvv_stgteilabschnitt` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `mvv_stgteilabschnitt` VALUES
('4ef431ff53145290b8550250db7b657b','e926187d4022cb0af52ad0553e2f6852',0,'Schlüsselkompetenzen Geschichte',NULL,0.00,NULL,'','',1545135981,1545135981),
('bb9a7de27c1fce5620af1fd38c9635cc','fb029d7d1ac739bf363119b4ca1674c1',0,'Studienbegleitende Leistungen',NULL,90.00,NULL,'','',1545135981,1545135981);
/*!40000 ALTER TABLE `mvv_stgteilabschnitt` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `mvv_stgteilabschnitt_modul`
--

LOCK TABLES `mvv_stgteilabschnitt_modul` WRITE;
/*!40000 ALTER TABLE `mvv_stgteilabschnitt_modul` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `mvv_stgteilabschnitt_modul` VALUES
('41961b1eee17b8c4938c3c20b7ae8382','bb9a7de27c1fce5620af1fd38c9635cc','40e1ada2c00e13a09e88143934e76efa',NULL,NULL,1,NULL,'','',1545135981,1545135981),
('77946e2559842d574a1427f00efb2409','bb9a7de27c1fce5620af1fd38c9635cc','9938b594f4c50c21ed235b2f92e82177',NULL,NULL,0,NULL,'','',1545135981,1545135981),
('e0b95319a5c2768c5a7358255c2e6bcd','4ef431ff53145290b8550250db7b657b','daf94f1c25809886095022d124aaf1fa',NULL,NULL,1,NULL,'','',1545135981,1545135981),
('e9b957e2742ee8f953ea1d8cab303a0b','4ef431ff53145290b8550250db7b657b','36677a1d815d4528bebf89833d168f56',NULL,NULL,0,NULL,'','',1545135981,1545135981);
/*!40000 ALTER TABLE `mvv_stgteilabschnitt_modul` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `mvv_stgteilversion`
--

LOCK TABLES `mvv_stgteilversion` WRITE;
/*!40000 ALTER TABLE `mvv_stgteilversion` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `mvv_stgteilversion` VALUES
('e926187d4022cb0af52ad0553e2f6852','e58b0eaf0c09c9fef4cab09cdec8dcc2','322f640f3f4643ebe514df65f1163eb1',NULL,'20182',1545135981,NULL,NULL,NULL,'genehmigt',NULL,'','',1545135981,1545135981),
('fb029d7d1ac739bf363119b4ca1674c1','f6ec30150732fa6ae913f4c3779e305a','322f640f3f4643ebe514df65f1163eb1',NULL,'20102',1545135981,NULL,NULL,NULL,'genehmigt',NULL,'','',1545135981,1545135981);
/*!40000 ALTER TABLE `mvv_stgteilversion` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `mvv_studiengang`
--

LOCK TABLES `mvv_studiengang` WRITE;
/*!40000 ALTER TABLE `mvv_studiengang` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `mvv_studiengang` VALUES
('58f22c9a19b296e7e5fbfa7d7a059c79','228234544820cdf75db55b42d1ea3ecc','mehrfach','Bachelor Geschichte',NULL,NULL,'1535795b0d6ddecac6813f5f6ac47ef2',NULL,NULL,NULL,NULL,NULL,'genehmigt',NULL,NULL,NULL,NULL,NULL,NULL,'','',1545135981,1545135981),
('7e5c6adb4152e8d402e5dba26664fa32','c7f569e815a35cf24a515a0e67928072','einfach','Master Informatik',NULL,NULL,'1535795b0d6ddecac6813f5f6ac47ef2',NULL,NULL,NULL,NULL,NULL,'genehmigt',NULL,NULL,NULL,NULL,NULL,NULL,'','',1545135981,1545135981);
/*!40000 ALTER TABLE `mvv_studiengang` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `mvv_studycourse_language`
--

LOCK TABLES `mvv_studycourse_language` WRITE;
/*!40000 ALTER TABLE `mvv_studycourse_language` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `mvv_studycourse_language` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `mvv_studycourse_type`
--

LOCK TABLES `mvv_studycourse_type` WRITE;
/*!40000 ALTER TABLE `mvv_studycourse_type` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `mvv_studycourse_type` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `news`
--

LOCK TABLES `news` WRITE;
/*!40000 ALTER TABLE `news` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `news` VALUES
('29f2932ce32be989022c6f43b866e744','Herzlich Willkommen!','<!--HTML-->\nDas Stud.IP-Team heisst sie herzlich willkommen.<br />\nBitte schauen Sie sich ruhig um!<br /><br />\nWenn Sie das System selbst installiert haben und diese News sehen, haben Sie die Demonstrationsdaten in die Datenbank eingefügt. Wenn Sie produktiv mit dem System arbeiten wollen, sollten Sie diese Daten später wieder löschen, <strong>da die Passwörter der Accounts öffentlich bekannt sind</strong>.<br />\n \n<p>Wenn Sie mit der Stud.IP Open Source-Community in Kontakt treten wollen und Fragen oder Ideen haben, können Sie sich auf unserem eigenen Stud.IP-System mit uns austauschen: <a href=\"https://develop.studip.de/studip\" class=\"link-extern\" target=\"_blank\" rel=\"noreferrer noopener\">develop.studip.de</a></p>\n\n<p>Falls Sie die Einführung an einer Bildungseinrichtung planen und Unterstützung bei der Einführung oder Beratung zum Einsatz von digitalen Tools für die Online Lehre haben, steht Ihnen die Firma data-quest als Dienstleister rund um Stud.IP zur Seite: <a href=\"https://www.data-quest.de\" class=\"link-extern\" target=\"_blank\" rel=\"noreferrer noopener\">https://www.data-quest.de</a></p>','Root Studip',1754464706,'76ed43ef286fb55cf9e41beadb484a9f',14562502,1,0,1754464706,'',1754464706);
/*!40000 ALTER TABLE `news` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `news_range`
--

LOCK TABLES `news_range` WRITE;
/*!40000 ALTER TABLE `news_range` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `news_range` VALUES
('29f2932ce32be989022c6f43b866e744','76ed43ef286fb55cf9e41beadb484a9f',NULL,NULL),
('29f2932ce32be989022c6f43b866e744','studip',NULL,NULL);
/*!40000 ALTER TABLE `news_range` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `news_roles`
--

LOCK TABLES `news_roles` WRITE;
/*!40000 ALTER TABLE `news_roles` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `news_roles` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `news_rss_range`
--

LOCK TABLES `news_rss_range` WRITE;
/*!40000 ALTER TABLE `news_rss_range` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `news_rss_range` VALUES
('studip','70cefd1e80398bb20ff599636546cdff','global');
/*!40000 ALTER TABLE `news_rss_range` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `oauth2_access_tokens`
--

LOCK TABLES `oauth2_access_tokens` WRITE;
/*!40000 ALTER TABLE `oauth2_access_tokens` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `oauth2_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `oauth2_auth_codes`
--

LOCK TABLES `oauth2_auth_codes` WRITE;
/*!40000 ALTER TABLE `oauth2_auth_codes` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `oauth2_auth_codes` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `oauth2_clients`
--

LOCK TABLES `oauth2_clients` WRITE;
/*!40000 ALTER TABLE `oauth2_clients` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `oauth2_clients` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `oauth2_refresh_tokens`
--

LOCK TABLES `oauth2_refresh_tokens` WRITE;
/*!40000 ALTER TABLE `oauth2_refresh_tokens` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `oauth2_refresh_tokens` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `object_contentmodules`
--

LOCK TABLES `object_contentmodules` WRITE;
/*!40000 ALTER TABLE `object_contentmodules` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `object_contentmodules` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `object_user_visits`
--

LOCK TABLES `object_user_visits` WRITE;
/*!40000 ALTER TABLE `object_user_visits` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `object_user_visits` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `object_views`
--

LOCK TABLES `object_views` WRITE;
/*!40000 ALTER TABLE `object_views` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `object_views` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `oer_abo`
--

LOCK TABLES `oer_abo` WRITE;
/*!40000 ALTER TABLE `oer_abo` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `oer_abo` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `oer_comments`
--

LOCK TABLES `oer_comments` WRITE;
/*!40000 ALTER TABLE `oer_comments` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `oer_comments` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `oer_downloadcounter`
--

LOCK TABLES `oer_downloadcounter` WRITE;
/*!40000 ALTER TABLE `oer_downloadcounter` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `oer_downloadcounter` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `oer_hosts`
--

LOCK TABLES `oer_hosts` WRITE;
/*!40000 ALTER TABLE `oer_hosts` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `oer_hosts` VALUES
('333f8037bc5f78b8e2f27256fa244b5f','OERHostOERSI','OERSI','https://oersi.de','','',1,1,1,1669041528,1669041528,1669041528);
/*!40000 ALTER TABLE `oer_hosts` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `oer_material`
--

LOCK TABLES `oer_material` WRITE;
/*!40000 ALTER TABLE `oer_material` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `oer_material` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `oer_material_users`
--

LOCK TABLES `oer_material_users` WRITE;
/*!40000 ALTER TABLE `oer_material_users` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `oer_material_users` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `oer_post_upload`
--

LOCK TABLES `oer_post_upload` WRITE;
/*!40000 ALTER TABLE `oer_post_upload` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `oer_post_upload` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `oer_reviews`
--

LOCK TABLES `oer_reviews` WRITE;
/*!40000 ALTER TABLE `oer_reviews` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `oer_reviews` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `oer_tags`
--

LOCK TABLES `oer_tags` WRITE;
/*!40000 ALTER TABLE `oer_tags` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `oer_tags` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `oer_tags_material`
--

LOCK TABLES `oer_tags_material` WRITE;
/*!40000 ALTER TABLE `oer_tags_material` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `oer_tags_material` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `oer_user`
--

LOCK TABLES `oer_user` WRITE;
/*!40000 ALTER TABLE `oer_user` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `oer_user` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `opengraphdata`
--

LOCK TABLES `opengraphdata` WRITE;
/*!40000 ALTER TABLE `opengraphdata` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `opengraphdata` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `participantrestrictedadmissions`
--

LOCK TABLES `participantrestrictedadmissions` WRITE;
/*!40000 ALTER TABLE `participantrestrictedadmissions` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `participantrestrictedadmissions` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `passwordadmissions`
--

LOCK TABLES `passwordadmissions` WRITE;
/*!40000 ALTER TABLE `passwordadmissions` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `passwordadmissions` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `personal_notifications`
--

LOCK TABLES `personal_notifications` WRITE;
/*!40000 ALTER TABLE `personal_notifications` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `personal_notifications` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `personal_notifications_user`
--

LOCK TABLES `personal_notifications_user` WRITE;
/*!40000 ALTER TABLE `personal_notifications_user` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `personal_notifications_user` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `plugin_assets`
--

LOCK TABLES `plugin_assets` WRITE;
/*!40000 ALTER TABLE `plugin_assets` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `plugin_assets` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `plugins`
--

LOCK TABLES `plugins` WRITE;
/*!40000 ALTER TABLE `plugins` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `plugins` VALUES
(1,'Blubber','','Blubber','CorePlugin,StandardPlugin,StudipModule','yes',1,NULL,NULL,NULL,NULL,'add',NULL,NULL),
(2,'CoreForum','','Forum','CorePlugin,ForumModule,StudipModule,StandardPlugin','yes',2,NULL,NULL,NULL,NULL,'add',NULL,NULL),
(3,'EvaluationsWidget','','EvaluationsWidget','PortalPlugin','yes',3,NULL,NULL,NULL,NULL,'add',NULL,NULL),
(4,'NewsWidget','','NewsWidget','PortalPlugin','yes',4,NULL,NULL,NULL,NULL,'add',NULL,NULL),
(5,'QuickSelection','','QuickSelection','PortalPlugin','yes',5,NULL,NULL,NULL,NULL,'add',NULL,NULL),
(6,'ScheduleWidget','','ScheduleWidget','PortalPlugin','yes',6,NULL,NULL,NULL,NULL,'add',NULL,NULL),
(7,'TerminWidget','','TerminWidget','PortalPlugin','yes',7,NULL,NULL,NULL,NULL,'add',NULL,NULL),
(8,'ActivityFeed','','ActivityFeed','PortalPlugin','yes',8,NULL,NULL,NULL,NULL,'add',NULL,NULL),
(9,'IliasInterfaceModule','','Ilias-Interface','CorePlugin,StudipModule,SystemPlugin','yes',1,NULL,NULL,NULL,NULL,'add',NULL,NULL),
(10,'LtiToolModule','','LTI-Tool','CorePlugin,StudipModule,SystemPlugin,PrivacyPlugin','yes',1,NULL,NULL,NULL,NULL,'add',NULL,NULL),
(11,'GradebookModule','','Gradebook','CorePlugin,SystemPlugin,StudipModule','yes',1,NULL,NULL,NULL,NULL,'add',NULL,NULL),
(12,'FeedbackModule','','Feedback','CorePlugin,StudipModule,SystemPlugin','yes',1,NULL,NULL,NULL,NULL,'add',NULL,NULL),
(13,'ConsultationModule','','Terminvergabe','CorePlugin,StudipModule,SystemPlugin,PrivacyPlugin,HomepagePlugin','yes',1,NULL,NULL,NULL,NULL,'add',NULL,NULL),
(14,'CoreOverview','','CoreOverview','CorePlugin,StudipModule','yes',1,NULL,NULL,NULL,NULL,'add',NULL,NULL),
(15,'CoreAdmin','','CoreAdmin','CorePlugin,StudipModule','yes',1,NULL,NULL,NULL,NULL,'add',NULL,NULL),
(16,'CoreStudygroupAdmin','','CoreStudygroupAdmin','CorePlugin,StudipModule','yes',1,NULL,NULL,NULL,NULL,'add',NULL,NULL),
(17,'CoreDocuments','','CoreDocuments','CorePlugin,StudipModule,OERModule','yes',1,NULL,NULL,NULL,NULL,'add',NULL,NULL),
(18,'CoreParticipants','','CoreParticipants','CorePlugin,StudipModule','yes',1,NULL,NULL,NULL,NULL,'add',NULL,NULL),
(19,'CoreStudygroupParticipants','','CoreStudygroupParticipants','CorePlugin,StudipModule','yes',1,NULL,NULL,NULL,NULL,'add',NULL,NULL),
(20,'CoreSchedule','','CoreSchedule','CorePlugin,StudipModule','yes',1,NULL,NULL,NULL,NULL,'add',NULL,NULL),
(21,'CoreScm','','CoreScm','CorePlugin,StudipModule','yes',1,NULL,NULL,NULL,NULL,'add',NULL,NULL),
(22,'CoreWiki','','CoreWiki','CorePlugin,StudipModule','yes',1,NULL,NULL,NULL,NULL,'add',NULL,NULL),
(23,'CoreCalendar','','CoreCalendar','CorePlugin,StudipModule','yes',1,NULL,NULL,NULL,NULL,'add',NULL,NULL),
(25,'CorePersonal','','CorePersonal','CorePlugin,StudipModule','yes',1,NULL,NULL,NULL,NULL,'add',NULL,NULL),
(26,'CoursewareModule','','Courseware','CorePlugin,StudipModule,SystemPlugin','yes',1,NULL,NULL,NULL,NULL,'add',NULL,NULL),
(27,'ContentsWidget','','ContentsWidget','PortalPlugin','yes',9,NULL,NULL,NULL,NULL,'add',NULL,NULL),
(28,'MyCoursesWidget','','MyCoursesWidget','PortalPlugin','yes',10,NULL,NULL,NULL,NULL,'add',NULL,NULL),
(29,'StudygroupWidget','','StudygroupWidget','PortalPlugin','yes',11,NULL,NULL,NULL,NULL,'add',NULL,NULL),
(30,'MyStudygroupsWidget','','MyStudygroupsWidget','PortalPlugin','yes',12,NULL,NULL,NULL,NULL,'add',NULL,NULL),
(31,'VipsModule','','Aufgaben','StudipModule,SystemPlugin,PrivacyPlugin,Courseware\\CoursewarePlugin','yes',1,NULL,NULL,NULL,NULL,'add',NULL,NULL);
/*!40000 ALTER TABLE `plugins` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `plugins_activated`
--

LOCK TABLES `plugins_activated` WRITE;
/*!40000 ALTER TABLE `plugins_activated` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `plugins_activated` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `prefadmission_condition`
--

LOCK TABLES `prefadmission_condition` WRITE;
/*!40000 ALTER TABLE `prefadmission_condition` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `prefadmission_condition` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `prefadmissions`
--

LOCK TABLES `prefadmissions` WRITE;
/*!40000 ALTER TABLE `prefadmissions` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `prefadmissions` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `priorities`
--

LOCK TABLES `priorities` WRITE;
/*!40000 ALTER TABLE `priorities` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `priorities` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `questionnaire_anonymous_answers`
--

LOCK TABLES `questionnaire_anonymous_answers` WRITE;
/*!40000 ALTER TABLE `questionnaire_anonymous_answers` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `questionnaire_anonymous_answers` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `questionnaire_answers`
--

LOCK TABLES `questionnaire_answers` WRITE;
/*!40000 ALTER TABLE `questionnaire_answers` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `questionnaire_answers` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `questionnaire_assignments`
--

LOCK TABLES `questionnaire_assignments` WRITE;
/*!40000 ALTER TABLE `questionnaire_assignments` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `questionnaire_assignments` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `questionnaire_questions`
--

LOCK TABLES `questionnaire_questions` WRITE;
/*!40000 ALTER TABLE `questionnaire_questions` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `questionnaire_questions` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `questionnaires`
--

LOCK TABLES `questionnaires` WRITE;
/*!40000 ALTER TABLE `questionnaires` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `questionnaires` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `range_tree`
--

LOCK TABLES `range_tree` WRITE;
/*!40000 ALTER TABLE `range_tree` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `range_tree` VALUES
('105b70b72dc1908ce2925e057c4a8daa','a3d977a66f0010fa8e15c27dd71aff63',0,1,'externe Einrichtung B','inst','110ce78ffefaf1e5f167cd7019b728bf'),
('1323254564871354786157481484621','3f93863e3d37ba0df286a6e7e26974ef',1,0,'','inst','1535795b0d6ddecac6813f5f6ac47ef2'),
('2f4f90ac9d8d832cc8c8a95910fde4eb','1323254564871354786157481484621',0,1,'Test Lehrstuhl','inst','536249daa596905f433e1f73578019db'),
('3f93863e3d37ba0df286a6e7e26974ef','root',0,0,'Einrichtungen der Universität','',''),
('5d032f70c255f3e57cf8aa85a429ad4e','1323254564871354786157481484621',0,2,'Test Abteilung','inst','f02e2b17bc0e99fc885da6ac4c2532dc'),
('a3d977a66f0010fa8e15c27dd71aff63','root',0,1,'externe Bildungseinrichtungen','fak','ec2e364b28357106c0f8c282733dbe56'),
('ce6c87bbf759b4cfd6f92d0c5560da5c','1323254564871354786157481484621',0,0,'Test Einrichtung','inst','2560f7c7674942a7dce8eeb238e15d93'),
('e0ff0ead6a8c5191078ed787cd7c0c1f','a3d977a66f0010fa8e15c27dd71aff63',0,0,'externe Einrichtung A','inst','7a4f19a0a2c321ab2b8f7b798881af7c');
/*!40000 ALTER TABLE `range_tree` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `resource_booking_intervals`
--

LOCK TABLES `resource_booking_intervals` WRITE;
/*!40000 ALTER TABLE `resource_booking_intervals` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `resource_booking_intervals` VALUES
('103172da14b07526e9c6ceb224c11c23','728f1578de643fb08b32b4b8afb2db77','ed37afe9ff2d96c489d5cf6e2b4fba80',1731312000,1731319200,1716456982,1716456982,1),
('107c4ff6a9c207cfda35a80260a3f8c2','728f1578de643fb08b32b4b8afb2db77','a672b97ead875ef080706c6f6da33a44',1731916800,1731924000,1716456982,1716456982,1),
('11ab8ba99a07c70b2f7482b6e10405ee','728f1578de643fb08b32b4b8afb2db77','867744ed64e76e4b38dc92e26ba2495c',1763366400,1763373600,1754464711,1754464711,1),
('1a54a465d47863fce40661e53eaf0232','728f1578de643fb08b32b4b8afb2db77','c1aabbf3f10444b165c1217082e8eabc',1764576000,1764583200,1754464711,1754464711,1),
('1f3b18df65f42101408da9ca0f8b06ae','728f1578de643fb08b32b4b8afb2db77','aec837593cbd51d21d58b97e9af9ba19',1737360000,1737367200,1716456982,1716456982,1),
('22589cca9cf15513c507d6658d267492','728f1578de643fb08b32b4b8afb2db77','6cb4d34009cee46357a98006ef824930',1732521600,1732528800,1716456982,1716456982,1),
('2273be6840eb462735458020165dc663','728f1578de643fb08b32b4b8afb2db77','577b04b04575ce3a60328cf97ff801c8',1730707200,1730714400,1716456982,1716456982,1),
('2617d9f5f094798a559ff434e84c9f65','728f1578de643fb08b32b4b8afb2db77','527ddb74b97248683354a0b90a21bef5',1768204800,1768212000,1754464711,1754464711,1),
('298b0bc9d1be4d072c93c7cbc2647be1','728f1578de643fb08b32b4b8afb2db77','4effa9b74ecbe244bac766e6256a361c',1736150400,1736157600,1716456982,1716456982,1),
('35eda58d9f0ba50b26cb05b5822ffdca','728f1578de643fb08b32b4b8afb2db77','538ba36ef8ba7a10fba86d803e32b624',1760338800,1760346000,1754464711,1754464711,1),
('4628a5e642b621bafa4fe0f3f2fd9a41','728f1578de643fb08b32b4b8afb2db77','70bc2f183b95a98397c13094d09abd4f',1762156800,1762164000,1754464711,1754464711,1),
('4bf38db5dff602e6bef5a3d281783bc7','728f1578de643fb08b32b4b8afb2db77','cde83098d29a75de33e76a71b8a71a21',1736755200,1736762400,1716456982,1716456982,1),
('5a7c2cd2975447334f0b3575513a5e6e','728f1578de643fb08b32b4b8afb2db77','9e55f071dc674f92ef7c0032bdc623f4',1733126400,1733133600,1716456982,1716456982,1),
('5bff17112dccfc248aa354748afceb46','728f1578de643fb08b32b4b8afb2db77','a01a39e448aa6dbab5dd5dffaae78926',1738569600,1738576800,1716456982,1716456982,1),
('67fceecc176077eec62c5e5362f71c0f','728f1578de643fb08b32b4b8afb2db77','155c2487d750d7b350019b4d473e83e0',1762761600,1762768800,1754464711,1754464711,1),
('7006fd587cbd06fa48b2db010ddb31fe','728f1578de643fb08b32b4b8afb2db77','8be1f929c09d77bc84f764a91c66ac6a',1767600000,1767607200,1754464711,1754464711,1),
('7828e8d2cbf10f2a8e14026b91884f3e','728f1578de643fb08b32b4b8afb2db77','01c8cc7d1b26c398bd19c93510a6e69c',1768809600,1768816800,1754464711,1754464711,1),
('783aa785a85845d7eae547983afc0091','728f1578de643fb08b32b4b8afb2db77','bd9806b968f39178225603cb6812f2b0',1729494000,1729501200,1716456982,1716456982,1),
('7be5075f87473cdc17d20db1170d642d','728f1578de643fb08b32b4b8afb2db77','81e9e741bdd7ed3e17c85259c7087a95',1760943600,1760950800,1754464711,1754464711,1),
('7f7470f79dc5ca2cc9e2e73ac50b6287','728f1578de643fb08b32b4b8afb2db77','413cca32dbe8c3499ae1b7dae46b6e77',1730102400,1730109600,1716456982,1716456982,1),
('914b8a1d43e28ed42cccd4bdc52d0b5c','728f1578de643fb08b32b4b8afb2db77','2a251bfb01a99e533b3bbc841fc02ca7',1733731200,1733738400,1716456982,1716456982,1),
('9e6b85152d5815c19a383daca21c166e','728f1578de643fb08b32b4b8afb2db77','dff787ec7eee719ddc29dd8f0584da8a',1765180800,1765188000,1754464711,1754464711,1),
('b106a0590256fff0acbd79c3a14dac74','728f1578de643fb08b32b4b8afb2db77','cf0dd39936bfed59ba593578ee01e2ec',1761552000,1761559200,1754464711,1754464711,1),
('be54808a910ff656aa141cd2effe61a3','728f1578de643fb08b32b4b8afb2db77','6fb9fadeb6570fda655c91586ceaecc7',1769414400,1769421600,1754464711,1754464711,1),
('cb480eba301d9e11be5f25cc6711160d','728f1578de643fb08b32b4b8afb2db77','47624836db96f0d1340605b9a04d9928',1763971200,1763978400,1754464711,1754464711,1),
('d53513f7c1ae8879441e2949f73eac0c','728f1578de643fb08b32b4b8afb2db77','a07cc7a8cf8ce0b2cdc6befb47fdc5b1',1765785600,1765792800,1754464711,1754464711,1),
('d566a5583c94cb7bbdbb67596780852c','728f1578de643fb08b32b4b8afb2db77','e70aaf4655d2de64852b2212b04a0e67',1737964800,1737972000,1716456982,1716456982,1),
('deaa9da8e6003da444d2ff4756bbab9a','728f1578de643fb08b32b4b8afb2db77','06a133ec2958178551ee6b48957058b6',1734336000,1734343200,1716456982,1716456982,1);
/*!40000 ALTER TABLE `resource_booking_intervals` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `resource_bookings`
--

LOCK TABLES `resource_bookings` WRITE;
/*!40000 ALTER TABLE `resource_bookings` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `resource_bookings` VALUES
('01c8cc7d1b26c398bd19c93510a6e69c','728f1578de643fb08b32b4b8afb2db77','eb7f88966b07133303ffb858ed45f072',NULL,1768809600,1768816800,NULL,1754464711,1754464711,NULL,0,0,'76ed43ef286fb55cf9e41beadb484a9f','',''),
('06a133ec2958178551ee6b48957058b6','728f1578de643fb08b32b4b8afb2db77','221bb1927fcd93fab3ec7dde7c6b3cce','',1734336000,1734343200,NULL,1716456982,1716456982,'',0,0,'76ed43ef286fb55cf9e41beadb484a9f','',''),
('155c2487d750d7b350019b4d473e83e0','728f1578de643fb08b32b4b8afb2db77','0da3ff500c2b610b559ad67ec69f8158',NULL,1762761600,1762768800,NULL,1754464711,1754464711,NULL,0,0,'76ed43ef286fb55cf9e41beadb484a9f','',''),
('2a251bfb01a99e533b3bbc841fc02ca7','728f1578de643fb08b32b4b8afb2db77','5f87ebde55d5527ceb27ccd6dcd9f66e','',1733731200,1733738400,NULL,1716456982,1716456982,'',0,0,'76ed43ef286fb55cf9e41beadb484a9f','',''),
('413cca32dbe8c3499ae1b7dae46b6e77','728f1578de643fb08b32b4b8afb2db77','7ec82c654e1b41819cd476ec72e77a76','',1730102400,1730109600,NULL,1716456982,1716456982,'',0,0,'76ed43ef286fb55cf9e41beadb484a9f','',''),
('47624836db96f0d1340605b9a04d9928','728f1578de643fb08b32b4b8afb2db77','89db52985137b9e9718eb3d26313b109',NULL,1763971200,1763978400,NULL,1754464711,1754464711,NULL,0,0,'76ed43ef286fb55cf9e41beadb484a9f','',''),
('4effa9b74ecbe244bac766e6256a361c','728f1578de643fb08b32b4b8afb2db77','132eec0a28623f5afde092a6960e45f4','',1736150400,1736157600,NULL,1716456982,1716456982,'',0,0,'76ed43ef286fb55cf9e41beadb484a9f','',''),
('527ddb74b97248683354a0b90a21bef5','728f1578de643fb08b32b4b8afb2db77','d2e6708b3c86b5be09ae480af133a1e4',NULL,1768204800,1768212000,NULL,1754464711,1754464711,NULL,0,0,'76ed43ef286fb55cf9e41beadb484a9f','',''),
('538ba36ef8ba7a10fba86d803e32b624','728f1578de643fb08b32b4b8afb2db77','0afb3ee903d2679dce0d3e71796b0d15',NULL,1760338800,1760346000,NULL,1754464711,1754464711,NULL,0,0,'76ed43ef286fb55cf9e41beadb484a9f','',''),
('577b04b04575ce3a60328cf97ff801c8','728f1578de643fb08b32b4b8afb2db77','60b4659f960ef05807cbaea6368158aa','',1730707200,1730714400,NULL,1716456982,1716456982,'',0,0,'76ed43ef286fb55cf9e41beadb484a9f','',''),
('6cb4d34009cee46357a98006ef824930','728f1578de643fb08b32b4b8afb2db77','13bf7a5cd577bcba5bff88d46512baad','',1732521600,1732528800,NULL,1716456982,1716456982,'',0,0,'76ed43ef286fb55cf9e41beadb484a9f','',''),
('6fb9fadeb6570fda655c91586ceaecc7','728f1578de643fb08b32b4b8afb2db77','02191f8621da3f781ed714cae1d24e40',NULL,1769414400,1769421600,NULL,1754464711,1754464711,NULL,0,0,'76ed43ef286fb55cf9e41beadb484a9f','',''),
('70bc2f183b95a98397c13094d09abd4f','728f1578de643fb08b32b4b8afb2db77','1cc62381340bb933dce847648aa57c42',NULL,1762156800,1762164000,NULL,1754464711,1754464711,NULL,0,0,'76ed43ef286fb55cf9e41beadb484a9f','',''),
('81e9e741bdd7ed3e17c85259c7087a95','728f1578de643fb08b32b4b8afb2db77','25b8d290d6c8d1357053cff0d0f2c0c2',NULL,1760943600,1760950800,NULL,1754464711,1754464711,NULL,0,0,'76ed43ef286fb55cf9e41beadb484a9f','',''),
('867744ed64e76e4b38dc92e26ba2495c','728f1578de643fb08b32b4b8afb2db77','ab30294bab1ca419cfe4217e8388c47c',NULL,1763366400,1763373600,NULL,1754464711,1754464711,NULL,0,0,'76ed43ef286fb55cf9e41beadb484a9f','',''),
('8be1f929c09d77bc84f764a91c66ac6a','728f1578de643fb08b32b4b8afb2db77','0600718a336675ba27ea80121a4db9e2',NULL,1767600000,1767607200,NULL,1754464711,1754464711,NULL,0,0,'76ed43ef286fb55cf9e41beadb484a9f','',''),
('9e55f071dc674f92ef7c0032bdc623f4','728f1578de643fb08b32b4b8afb2db77','4f47c3d25eca9ab8fb2a1644209074ae','',1733126400,1733133600,NULL,1716456982,1716456982,'',0,0,'76ed43ef286fb55cf9e41beadb484a9f','',''),
('a01a39e448aa6dbab5dd5dffaae78926','728f1578de643fb08b32b4b8afb2db77','be1ad3a4bc5c933d4bfbaa2b313d3ab5','',1738569600,1738576800,NULL,1716456982,1716456982,'',0,0,'76ed43ef286fb55cf9e41beadb484a9f','',''),
('a07cc7a8cf8ce0b2cdc6befb47fdc5b1','728f1578de643fb08b32b4b8afb2db77','d8b107a56aaac63e3b64f1f4bbd267c7',NULL,1765785600,1765792800,NULL,1754464711,1754464711,NULL,0,0,'76ed43ef286fb55cf9e41beadb484a9f','',''),
('a672b97ead875ef080706c6f6da33a44','728f1578de643fb08b32b4b8afb2db77','c729ae36a1503f471a407802e8f72cec','',1731916800,1731924000,NULL,1716456982,1716456982,'',0,0,'76ed43ef286fb55cf9e41beadb484a9f','',''),
('aec837593cbd51d21d58b97e9af9ba19','728f1578de643fb08b32b4b8afb2db77','7aaa9681da31192e49eaa63a4cef3dfb','',1737360000,1737367200,NULL,1716456982,1716456982,'',0,0,'76ed43ef286fb55cf9e41beadb484a9f','',''),
('bd9806b968f39178225603cb6812f2b0','728f1578de643fb08b32b4b8afb2db77','749e52b43a4fe025442f355779489a9d','',1729494000,1729501200,NULL,1716456982,1716456982,'',0,0,'76ed43ef286fb55cf9e41beadb484a9f','',''),
('c1aabbf3f10444b165c1217082e8eabc','728f1578de643fb08b32b4b8afb2db77','c46b36d8600655e7eda58339876a7a8a',NULL,1764576000,1764583200,NULL,1754464711,1754464711,NULL,0,0,'76ed43ef286fb55cf9e41beadb484a9f','',''),
('cde83098d29a75de33e76a71b8a71a21','728f1578de643fb08b32b4b8afb2db77','1199f2c43a6ddcd05fd61456e6ac1451','',1736755200,1736762400,NULL,1716456982,1716456982,'',0,0,'76ed43ef286fb55cf9e41beadb484a9f','',''),
('cf0dd39936bfed59ba593578ee01e2ec','728f1578de643fb08b32b4b8afb2db77','2674f544de3a8a868427bf952d450e3f',NULL,1761552000,1761559200,NULL,1754464711,1754464711,NULL,0,0,'76ed43ef286fb55cf9e41beadb484a9f','',''),
('dff787ec7eee719ddc29dd8f0584da8a','728f1578de643fb08b32b4b8afb2db77','ed58ca52de8adaaf548c637950a4c63d',NULL,1765180800,1765188000,NULL,1754464711,1754464711,NULL,0,0,'76ed43ef286fb55cf9e41beadb484a9f','',''),
('e70aaf4655d2de64852b2212b04a0e67','728f1578de643fb08b32b4b8afb2db77','a8f402ada308f68d5f24374923d25580','',1737964800,1737972000,NULL,1716456982,1716456982,'',0,0,'76ed43ef286fb55cf9e41beadb484a9f','',''),
('ed37afe9ff2d96c489d5cf6e2b4fba80','728f1578de643fb08b32b4b8afb2db77','e8212b6e58109ae94ad2a31796c4a520','',1731312000,1731319200,NULL,1716456982,1716456982,'',0,0,'76ed43ef286fb55cf9e41beadb484a9f','','');
/*!40000 ALTER TABLE `resource_bookings` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `resource_categories`
--

LOCK TABLES `resource_categories` WRITE;
/*!40000 ALTER TABLE `resource_categories` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `resource_categories` VALUES
('05278c70d89ae99404727408ef111963','Standort','',1,0,'Location',0,0),
('3cbcc99c39476b8e2c8eef5381687461','Gebäude','',1,1,'Building',0,0),
('5a72dfe3f0c0295a8fe4e12c86d4c8f4','Übungsraum','',1,1,'Room',0,0),
('85d62e2a8a87a2924db8fc4ed3fde09d','Hörsaal','',1,1,'Room',0,0);
/*!40000 ALTER TABLE `resource_categories` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `resource_category_properties`
--

LOCK TABLES `resource_category_properties` WRITE;
/*!40000 ALTER TABLE `resource_category_properties` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `resource_category_properties` VALUES
('05278c70d89ae99404727408ef111963','282bd47d19f9df6469777fa5f46f57f0',0,0,1,NULL,0,0),
('3cbcc99c39476b8e2c8eef5381687461','282bd47d19f9df6469777fa5f46f57f0',0,0,1,NULL,0,0),
('3cbcc99c39476b8e2c8eef5381687461','5c01db06907efbcdc556b5688e70a6de',0,0,1,NULL,0,0),
('3cbcc99c39476b8e2c8eef5381687461','b79b77f40706ed598f5403f953c1f791',1,0,0,NULL,0,0),
('3cbcc99c39476b8e2c8eef5381687461','c4f13691419a6c12d38ad83daa926c7c',0,0,1,NULL,0,0),
('5a72dfe3f0c0295a8fe4e12c86d4c8f4','1f8cef2b614382e36eaa4a29f6027edf',1,0,0,NULL,0,0),
('5a72dfe3f0c0295a8fe4e12c86d4c8f4','28addfe18e86cc3587205734c8bc2372',1,0,0,NULL,0,0),
('5a72dfe3f0c0295a8fe4e12c86d4c8f4','44fd30e8811d0d962582fa1a9c452bdd',1,0,1,NULL,0,0),
('5a72dfe3f0c0295a8fe4e12c86d4c8f4','613cfdf6aa1072e21a1edfcfb0445c69',1,0,0,NULL,0,0),
('5a72dfe3f0c0295a8fe4e12c86d4c8f4','6ea541162f844090000d016740677385',0,0,1,NULL,0,0),
('5a72dfe3f0c0295a8fe4e12c86d4c8f4','6fc3efd459a0d38ceb5d85eaf1f4451d',0,0,1,NULL,0,0),
('5a72dfe3f0c0295a8fe4e12c86d4c8f4','7c1a8f6001cfdcb9e9c33eeee0ef343d',1,0,0,NULL,0,0),
('5a72dfe3f0c0295a8fe4e12c86d4c8f4','94514a9ff5b3336a03cb8b82c8eaf148',0,0,1,NULL,0,0),
('5a72dfe3f0c0295a8fe4e12c86d4c8f4','afb8675e2257c03098aa34b2893ba686',1,0,0,NULL,0,0),
('5a72dfe3f0c0295a8fe4e12c86d4c8f4','b79b77f40706ed598f5403f953c1f791',1,0,0,NULL,0,0),
('85d62e2a8a87a2924db8fc4ed3fde09d','1f8cef2b614382e36eaa4a29f6027edf',1,0,0,NULL,0,0),
('85d62e2a8a87a2924db8fc4ed3fde09d','28addfe18e86cc3587205734c8bc2372',1,0,0,NULL,0,0),
('85d62e2a8a87a2924db8fc4ed3fde09d','44fd30e8811d0d962582fa1a9c452bdd',1,0,1,NULL,0,0),
('85d62e2a8a87a2924db8fc4ed3fde09d','613cfdf6aa1072e21a1edfcfb0445c69',1,0,0,NULL,0,0),
('85d62e2a8a87a2924db8fc4ed3fde09d','6ea541162f844090000d016740677385',0,0,1,NULL,0,0),
('85d62e2a8a87a2924db8fc4ed3fde09d','6fc3efd459a0d38ceb5d85eaf1f4451d',0,0,1,NULL,0,0),
('85d62e2a8a87a2924db8fc4ed3fde09d','7c1a8f6001cfdcb9e9c33eeee0ef343d',1,0,0,NULL,0,0),
('85d62e2a8a87a2924db8fc4ed3fde09d','94514a9ff5b3336a03cb8b82c8eaf148',0,0,1,NULL,0,0),
('85d62e2a8a87a2924db8fc4ed3fde09d','afb8675e2257c03098aa34b2893ba686',1,0,0,NULL,0,0),
('85d62e2a8a87a2924db8fc4ed3fde09d','b79b77f40706ed598f5403f953c1f791',1,0,0,NULL,0,0);
/*!40000 ALTER TABLE `resource_category_properties` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `resource_permissions`
--

LOCK TABLES `resource_permissions` WRITE;
/*!40000 ALTER TABLE `resource_permissions` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `resource_permissions` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `resource_properties`
--

LOCK TABLES `resource_properties` WRITE;
/*!40000 ALTER TABLE `resource_properties` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `resource_properties` VALUES
('2760740189890f47537537ed7fa51a05','674ea21ef56fd973bb30ee6f247c0723','+0.0+0.0+0.0CRSWGS_84/',1591714592,1591714592),
('2f98bf64830043fd98a39fbbe2068678','2650f839a2a02d99f82d4a6c019da329','1',1591713936,1591713936),
('2f98bf64830043fd98a39fbbe2068678','28addfe18e86cc3587205734c8bc2372','1',0,0),
('2f98bf64830043fd98a39fbbe2068678','3089b4bf392b42e8d21218f29b24f799','76ed43ef286fb55cf9e41beadb484a9f',1084640542,1084640555),
('2f98bf64830043fd98a39fbbe2068678','44fd30e8811d0d962582fa1a9c452bdd','25',0,0),
('2f98bf64830043fd98a39fbbe2068678','613cfdf6aa1072e21a1edfcfb0445c69','1',0,0),
('2f98bf64830043fd98a39fbbe2068678','72723662c924e785a6662f42c84b8bb4','',1591714586,1591714586),
('2f98bf64830043fd98a39fbbe2068678','b79b77f40706ed598f5403f953c1f791','1',0,0),
('51ad4b7100d3a8a1db61c7b099f052a6','2650f839a2a02d99f82d4a6c019da329','1',1591713936,1591713936),
('51ad4b7100d3a8a1db61c7b099f052a6','28addfe18e86cc3587205734c8bc2372','1',0,0),
('51ad4b7100d3a8a1db61c7b099f052a6','3089b4bf392b42e8d21218f29b24f799','76ed43ef286fb55cf9e41beadb484a9f',1084640567,1084640578),
('51ad4b7100d3a8a1db61c7b099f052a6','44fd30e8811d0d962582fa1a9c452bdd','25',0,0),
('51ad4b7100d3a8a1db61c7b099f052a6','613cfdf6aa1072e21a1edfcfb0445c69','1',0,0),
('51ad4b7100d3a8a1db61c7b099f052a6','72723662c924e785a6662f42c84b8bb4','',1591714586,1591714586),
('51ad4b7100d3a8a1db61c7b099f052a6','afb8675e2257c03098aa34b2893ba686','1',0,0),
('5ead77812be3b601e2f08ed5da4c5630','1f8cef2b614382e36eaa4a29f6027edf','1',0,0),
('5ead77812be3b601e2f08ed5da4c5630','2650f839a2a02d99f82d4a6c019da329','1',1591713936,1591713936),
('5ead77812be3b601e2f08ed5da4c5630','28addfe18e86cc3587205734c8bc2372','0',0,0),
('5ead77812be3b601e2f08ed5da4c5630','3089b4bf392b42e8d21218f29b24f799','76ed43ef286fb55cf9e41beadb484a9f',1084640611,1084723704),
('5ead77812be3b601e2f08ed5da4c5630','44fd30e8811d0d962582fa1a9c452bdd','15',0,0),
('5ead77812be3b601e2f08ed5da4c5630','72723662c924e785a6662f42c84b8bb4','',1591714586,1591714586),
('5ead77812be3b601e2f08ed5da4c5630','afb8675e2257c03098aa34b2893ba686','1',0,0),
('6350c6ae2ec6fd8bd852d505789d0666','674ea21ef56fd973bb30ee6f247c0723','+51.5398160+9.9367200+0.0000000CRSWGS_84/',1591714594,1591715302),
('6350c6ae2ec6fd8bd852d505789d0666','b79b77f40706ed598f5403f953c1f791','1',0,0),
('6350c6ae2ec6fd8bd852d505789d0666','c4f13691419a6c12d38ad83daa926c7c','Liebigstr. 1',0,0),
('6350c6ae2ec6fd8bd852d505789d0666','e141f19ca6da2938d4c51cc59462884b','',1591714589,1591714589),
('728f1578de643fb08b32b4b8afb2db77','1f8cef2b614382e36eaa4a29f6027edf','1',0,0),
('728f1578de643fb08b32b4b8afb2db77','2650f839a2a02d99f82d4a6c019da329','1',1591713936,1591713936),
('728f1578de643fb08b32b4b8afb2db77','28addfe18e86cc3587205734c8bc2372','1',0,0),
('728f1578de643fb08b32b4b8afb2db77','3089b4bf392b42e8d21218f29b24f799','76ed43ef286fb55cf9e41beadb484a9f',1084640456,1084640468),
('728f1578de643fb08b32b4b8afb2db77','44fd30e8811d0d962582fa1a9c452bdd','500',0,0),
('728f1578de643fb08b32b4b8afb2db77','613cfdf6aa1072e21a1edfcfb0445c69','1',0,0),
('728f1578de643fb08b32b4b8afb2db77','72723662c924e785a6662f42c84b8bb4','',1591714470,1591714470),
('728f1578de643fb08b32b4b8afb2db77','7c1a8f6001cfdcb9e9c33eeee0ef343d','1',0,0),
('728f1578de643fb08b32b4b8afb2db77','afb8675e2257c03098aa34b2893ba686','1',0,0),
('728f1578de643fb08b32b4b8afb2db77','b79b77f40706ed598f5403f953c1f791','1',0,0),
('8a57860ca2be4cc3a77c06c1d346ea57','674ea21ef56fd973bb30ee6f247c0723','+51.5407270+9.9354050+0.0000000CRSWGS_84/',1591714991,1591715222),
('8a57860ca2be4cc3a77c06c1d346ea57','b79b77f40706ed598f5403f953c1f791','1',0,0),
('8a57860ca2be4cc3a77c06c1d346ea57','c4f13691419a6c12d38ad83daa926c7c','Universitätsstr. 1',0,0),
('8a57860ca2be4cc3a77c06c1d346ea57','e141f19ca6da2938d4c51cc59462884b','',1591714589,1591714589),
('a8c03520e8ad9dc90fb2d161ffca7d7b','2650f839a2a02d99f82d4a6c019da329','1',1591713936,1591713936),
('a8c03520e8ad9dc90fb2d161ffca7d7b','28addfe18e86cc3587205734c8bc2372','1',0,0),
('a8c03520e8ad9dc90fb2d161ffca7d7b','3089b4bf392b42e8d21218f29b24f799','76ed43ef286fb55cf9e41beadb484a9f',1084640590,1084640599),
('a8c03520e8ad9dc90fb2d161ffca7d7b','44fd30e8811d0d962582fa1a9c452bdd','30',0,0),
('a8c03520e8ad9dc90fb2d161ffca7d7b','613cfdf6aa1072e21a1edfcfb0445c69','1',0,0),
('a8c03520e8ad9dc90fb2d161ffca7d7b','72723662c924e785a6662f42c84b8bb4','',1591714586,1591714586),
('a8c03520e8ad9dc90fb2d161ffca7d7b','7c1a8f6001cfdcb9e9c33eeee0ef343d','1',0,0),
('a8c03520e8ad9dc90fb2d161ffca7d7b','afb8675e2257c03098aa34b2893ba686','1',0,0),
('a8c03520e8ad9dc90fb2d161ffca7d7b','b79b77f40706ed598f5403f953c1f791','1',0,0),
('b17c4ea6e053f2fffba8a5517fc277b3','2650f839a2a02d99f82d4a6c019da329','1',1591713936,1591713936),
('b17c4ea6e053f2fffba8a5517fc277b3','28addfe18e86cc3587205734c8bc2372','0',0,0),
('b17c4ea6e053f2fffba8a5517fc277b3','3089b4bf392b42e8d21218f29b24f799','76ed43ef286fb55cf9e41beadb484a9f',1084640520,1084640528),
('b17c4ea6e053f2fffba8a5517fc277b3','44fd30e8811d0d962582fa1a9c452bdd','150',0,0),
('b17c4ea6e053f2fffba8a5517fc277b3','72723662c924e785a6662f42c84b8bb4','',1591714586,1591714586),
('b17c4ea6e053f2fffba8a5517fc277b3','7c1a8f6001cfdcb9e9c33eeee0ef343d','1',0,0),
('b17c4ea6e053f2fffba8a5517fc277b3','b79b77f40706ed598f5403f953c1f791','1',0,0);
/*!40000 ALTER TABLE `resource_properties` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `resource_property_definitions`
--

LOCK TABLES `resource_property_definitions` WRITE;
/*!40000 ALTER TABLE `resource_property_definitions` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `resource_property_definitions` VALUES
('1f8cef2b614382e36eaa4a29f6027edf','has_loudspeakers','','bool','vorhanden',1,0,'Audio-Anlage',1,0,'admin-global',NULL,NULL,0,0),
('282bd47d19f9df6469777fa5f46f57f0','geo_coordinates',NULL,'position','',1,0,'',0,0,'admin-global',NULL,NULL,0,0),
('28addfe18e86cc3587205734c8bc2372','is_dimmable','','bool','vorhanden',1,0,'Verdunklung',1,0,'admin-global',NULL,NULL,0,0),
('44fd30e8811d0d962582fa1a9c452bdd','seats','','num','',1,0,'Sitzplätze',1,0,'admin-global',NULL,NULL,0,0),
('5c01db06907efbcdc556b5688e70a6de','number',NULL,'text','',1,0,'',0,0,'admin-global',NULL,NULL,0,0),
('613cfdf6aa1072e21a1edfcfb0445c69','has_overhead_projector','','bool','vorhanden',1,0,'Tageslichtprojektor',1,0,'admin-global',NULL,NULL,0,0),
('6ea541162f844090000d016740677385','responsible_person','','user','',1,1,'Raumverantwortung',0,0,'admin-global',NULL,NULL,1591630778,1591630778),
('6fc3efd459a0d38ceb5d85eaf1f4451d','room_type',NULL,'select','',1,0,'',0,0,'admin-global',NULL,NULL,0,0),
('7c1a8f6001cfdcb9e9c33eeee0ef343d','has_projector','','bool','vorhanden',1,0,'Beamer',1,0,'admin-global',NULL,NULL,0,0),
('94514a9ff5b3336a03cb8b82c8eaf148','booking_plan_is_public',NULL,'bool','',1,0,'',0,0,'admin-global',NULL,NULL,0,0),
('afb8675e2257c03098aa34b2893ba686','has_computer','','bool','vorhanden',1,0,'Dozentenrechner',1,0,'admin-global',NULL,NULL,0,0),
('b79b77f40706ed598f5403f953c1f791','accessible','','bool','vorhanden',1,0,'behindertengerecht',1,0,'admin-global',NULL,NULL,0,0),
('c4f13691419a6c12d38ad83daa926c7c','address','','text','',1,0,'Adresse',0,0,'admin-global',NULL,NULL,0,0);
/*!40000 ALTER TABLE `resource_property_definitions` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `resource_property_groups`
--

LOCK TABLES `resource_property_groups` WRITE;
/*!40000 ALTER TABLE `resource_property_groups` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `resource_property_groups` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `resource_request_appointments`
--

LOCK TABLES `resource_request_appointments` WRITE;
/*!40000 ALTER TABLE `resource_request_appointments` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `resource_request_appointments` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `resource_request_properties`
--

LOCK TABLES `resource_request_properties` WRITE;
/*!40000 ALTER TABLE `resource_request_properties` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `resource_request_properties` VALUES
('1f3e5754b287962a1b5c74b4148bfe27','44fd30e8811d0d962582fa1a9c452bdd','400',1754464711,1754464711),
('feb6119e0c41e15077a0ad25c0e853dc','44fd30e8811d0d962582fa1a9c452bdd','400',1754464711,1754464711);
/*!40000 ALTER TABLE `resource_request_properties` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `resource_requests`
--

LOCK TABLES `resource_requests` WRITE;
/*!40000 ALTER TABLE `resource_requests` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `resource_requests` VALUES
('1f3e5754b287962a1b5c74b4148bfe27','a07535cf2f8a72df33c12ddfa4b53dde','30b480d6506c4f2d2becceee29254e46','','205f3efb7997a0fc9755da2b535038da','cli','728f1578de643fb08b32b4b8afb2db77','',NULL,NULL,'requester',0,1754464711,1754464711,0,0,0,0),
('feb6119e0c41e15077a0ad25c0e853dc','7cb72dab1bf896a0b55c6aa7a70a3a86','42c1555ea5ee40618f5151472354b9f1','','205f3efb7997a0fc9755da2b535038da','cli','728f1578de643fb08b32b4b8afb2db77','',NULL,NULL,'requester',0,1754464711,1754464711,0,0,0,0);
/*!40000 ALTER TABLE `resource_requests` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `resource_temporary_permissions`
--

LOCK TABLES `resource_temporary_permissions` WRITE;
/*!40000 ALTER TABLE `resource_temporary_permissions` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `resource_temporary_permissions` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `resources`
--

LOCK TABLES `resources` WRITE;
/*!40000 ALTER TABLE `resources` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `resources` VALUES
('2760740189890f47537537ed7fa51a05','','05278c70d89ae99404727408ef111963',NULL,'Stud.IP','',0,1,1,1591713936,1591713936,0),
('2f98bf64830043fd98a39fbbe2068678','8a57860ca2be4cc3a77c06c1d346ea57','85d62e2a8a87a2924db8fc4ed3fde09d',2,'Hörsaal 3','',1,1,1,1084640542,1084640555,0),
('51ad4b7100d3a8a1db61c7b099f052a6','6350c6ae2ec6fd8bd852d505789d0666','5a72dfe3f0c0295a8fe4e12c86d4c8f4',2,'Seminarraum 1','',1,1,1,1084640567,1084640578,0),
('5ead77812be3b601e2f08ed5da4c5630','6350c6ae2ec6fd8bd852d505789d0666','5a72dfe3f0c0295a8fe4e12c86d4c8f4',2,'Seminarraum 3','',1,1,1,1084640611,1084723704,0),
('6350c6ae2ec6fd8bd852d505789d0666','2760740189890f47537537ed7fa51a05','3cbcc99c39476b8e2c8eef5381687461',1,'Übungsgebäude','',1,1,1,1084640386,1591715302,0),
('728f1578de643fb08b32b4b8afb2db77','8a57860ca2be4cc3a77c06c1d346ea57','85d62e2a8a87a2924db8fc4ed3fde09d',2,'Hörsaal 1','',1,1,1,1084640456,1084640468,0),
('8a57860ca2be4cc3a77c06c1d346ea57','2760740189890f47537537ed7fa51a05','3cbcc99c39476b8e2c8eef5381687461',1,'Hörsaalgebäude','',1,1,1,1084640042,1591715222,0),
('a8c03520e8ad9dc90fb2d161ffca7d7b','6350c6ae2ec6fd8bd852d505789d0666','5a72dfe3f0c0295a8fe4e12c86d4c8f4',2,'Seminarraum 2','',1,1,1,1084640590,1084640599,0),
('b17c4ea6e053f2fffba8a5517fc277b3','8a57860ca2be4cc3a77c06c1d346ea57','85d62e2a8a87a2924db8fc4ed3fde09d',2,'Hörsaal 2','',1,1,1,1084640520,1084640528,0);
/*!40000 ALTER TABLE `resources` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `roles` VALUES
(1,'Root-Administrator(in)','y'),
(2,'Administrator(in)','y'),
(3,'Mitarbeiter(in)','y'),
(4,'Lehrende(r)','y'),
(5,'Studierende(r)','y'),
(6,'Tutor(in)','y'),
(7,'Nobody','y'),
(8,'DedicatedAdmin','n'),
(9,'MVVAdmin','n'),
(10,'MVVFreigabe','n'),
(11,'MVVEntwickler','n'),
(12,'MVVRedakteur','n'),
(13,'MVVTranslator','n'),
(14,'MVVLvGruppenAdmin','n'),
(15,'Massenmail-Root','n');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `roles_plugins`
--

LOCK TABLES `roles_plugins` WRITE;
/*!40000 ALTER TABLE `roles_plugins` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `roles_plugins` VALUES
(1,1),
(1,2),
(1,3),
(1,4),
(1,5),
(1,6),
(1,7),
(1,8),
(1,9),
(1,10),
(1,11),
(1,12),
(1,13),
(1,14),
(1,15),
(1,16),
(1,17),
(1,18),
(1,19),
(1,20),
(1,21),
(1,22),
(1,23),
(1,25),
(1,26),
(1,27),
(1,28),
(1,29),
(1,30),
(1,31),
(2,1),
(2,2),
(2,3),
(2,4),
(2,5),
(2,6),
(2,7),
(2,8),
(2,9),
(2,10),
(2,11),
(2,12),
(2,13),
(2,14),
(2,15),
(2,16),
(2,17),
(2,18),
(2,19),
(2,20),
(2,21),
(2,22),
(2,23),
(2,25),
(2,26),
(2,27),
(2,28),
(2,29),
(2,30),
(2,31),
(3,1),
(3,2),
(3,3),
(3,4),
(3,5),
(3,6),
(3,7),
(3,8),
(3,9),
(3,10),
(3,11),
(3,12),
(3,13),
(3,14),
(3,15),
(3,16),
(3,17),
(3,18),
(3,19),
(3,20),
(3,21),
(3,22),
(3,23),
(3,25),
(3,26),
(3,27),
(3,28),
(3,29),
(3,30),
(3,31),
(4,1),
(4,2),
(4,3),
(4,4),
(4,5),
(4,6),
(4,7),
(4,8),
(4,9),
(4,10),
(4,11),
(4,12),
(4,13),
(4,14),
(4,15),
(4,16),
(4,17),
(4,18),
(4,19),
(4,20),
(4,21),
(4,22),
(4,23),
(4,25),
(4,26),
(4,27),
(4,28),
(4,29),
(4,30),
(4,31),
(5,1),
(5,2),
(5,3),
(5,4),
(5,5),
(5,6),
(5,7),
(5,8),
(5,9),
(5,10),
(5,11),
(5,12),
(5,13),
(5,14),
(5,15),
(5,16),
(5,17),
(5,18),
(5,19),
(5,20),
(5,21),
(5,22),
(5,23),
(5,25),
(5,26),
(5,27),
(5,28),
(5,29),
(5,30),
(5,31),
(6,1),
(6,2),
(6,3),
(6,4),
(6,5),
(6,6),
(6,7),
(6,8),
(6,9),
(6,10),
(6,11),
(6,12),
(6,13),
(6,14),
(6,15),
(6,16),
(6,17),
(6,18),
(6,19),
(6,20),
(6,21),
(6,22),
(6,23),
(6,25),
(6,26),
(6,27),
(6,28),
(6,29),
(6,30),
(6,31),
(7,1),
(7,2),
(7,10),
(7,11),
(7,12),
(7,13),
(7,14),
(7,15),
(7,16),
(7,17),
(7,18),
(7,19),
(7,20),
(7,21),
(7,22),
(7,23),
(7,25),
(7,26),
(7,31);
/*!40000 ALTER TABLE `roles_plugins` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `roles_studipperms`
--

LOCK TABLES `roles_studipperms` WRITE;
/*!40000 ALTER TABLE `roles_studipperms` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `roles_studipperms` VALUES
(1,'root'),
(2,'admin'),
(3,'admin'),
(3,'root'),
(4,'dozent'),
(5,'autor'),
(5,'tutor'),
(6,'tutor');
/*!40000 ALTER TABLE `roles_studipperms` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `roles_user`
--

LOCK TABLES `roles_user` WRITE;
/*!40000 ALTER TABLE `roles_user` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `roles_user` VALUES
(7,'nobody','');
/*!40000 ALTER TABLE `roles_user` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `schedule_courses`
--

LOCK TABLES `schedule_courses` WRITE;
/*!40000 ALTER TABLE `schedule_courses` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `schedule_courses` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `schedule_entries`
--

LOCK TABLES `schedule_entries` WRITE;
/*!40000 ALTER TABLE `schedule_entries` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `schedule_entries` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `schema_version`
--

LOCK TABLES `schema_version` WRITE;
/*!40000 ALTER TABLE `schema_version` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `schema_version` VALUES
('studip','1',327),
('studip','5.1',58),
('studip','5.2',16),
('studip','5.3',31),
('studip','5.4',19),
('studip','5.5',32),
('studip','5.5.23',1),
('studip','6.0',51);
/*!40000 ALTER TABLE `schema_version` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `scm`
--

LOCK TABLES `scm` WRITE;
/*!40000 ALTER TABLE `scm` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `scm` VALUES
('a07df31918cc8e5ca0597e959a4a5297','a07535cf2f8a72df33c12ddfa4b53dde','76ed43ef286fb55cf9e41beadb484a9f','Informationen','',1343924407,1343924407,0);
/*!40000 ALTER TABLE `scm` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `sem_classes`
--

LOCK TABLES `sem_classes` WRITE;
/*!40000 ALTER TABLE `sem_classes` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `sem_classes` VALUES
(1,'Lehre',0,1,1,1,1,1,0,0,1,0,'{\"CoreAdmin\":{\"activated\":\"1\",\"sticky\":\"1\"},\"CoreOverview\":{\"activated\":\"1\",\"sticky\":\"1\"},\"CoreDocuments\":{\"activated\":\"1\",\"sticky\":\"0\"},\"CoursewareModule\":{\"activated\":\"1\",\"sticky\":\"0\"},\"Blubber\":{\"activated\":\"1\",\"sticky\":\"0\"},\"CoreForum\":{\"activated\":\"0\",\"sticky\":\"0\"},\"CoreWiki\":{\"activated\":\"1\",\"sticky\":\"0\"},\"CoreParticipants\":{\"activated\":\"1\",\"sticky\":\"0\"},\"CoreSchedule\":{\"activated\":\"1\",\"sticky\":\"0\"},\"CoreScm\":{\"activated\":\"0\",\"sticky\":\"0\"},\"ConsultationModule\":{\"activated\":\"0\",\"sticky\":\"0\"},\"CoreElearningInterface\":{\"activated\":\"0\",\"sticky\":\"0\"},\"IliasInterfaceModule\":{\"activated\":\"0\",\"sticky\":\"0\"},\"LtiToolModule\":{\"activated\":\"0\",\"sticky\":\"0\"},\"GradebookModule\":{\"activated\":\"0\",\"sticky\":\"0\"},\"FeedbackModule\":{\"activated\":\"0\",\"sticky\":\"0\"},\"CoreCalendar\":{\"activated\":\"0\",\"sticky\":\"1\"}}','Hier finden Sie alle in Stud.IP registrierten Lehrveranstaltungen','',0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,1,0,0,0,1366882120,1641229573),
(2,'Organisation',0,0,0,0,0,1,0,0,1,0,'{\"CoreAdmin\":{\"activated\":\"1\",\"sticky\":\"1\"},\"CoreOverview\":{\"activated\":\"1\",\"sticky\":\"1\"},\"Blubber\":{\"activated\":\"1\",\"sticky\":\"0\"},\"CoreForum\":{\"activated\":\"0\",\"sticky\":\"0\"},\"CoreParticipants\":{\"activated\":\"1\",\"sticky\":\"0\"},\"CoreDocuments\":{\"activated\":\"1\",\"sticky\":\"0\"},\"CoreSchedule\":{\"activated\":\"1\",\"sticky\":\"0\"},\"CoreWiki\":{\"activated\":\"1\",\"sticky\":\"0\"},\"CoreScm\":{\"activated\":\"0\",\"sticky\":\"0\"},\"CoreElearningInterface\":{\"activated\":\"0\",\"sticky\":\"0\"},\"LtiToolModule\":{\"activated\":\"0\",\"sticky\":\"0\"},\"GradebookModule\":{\"activated\":\"0\",\"sticky\":\"0\"},\"CoursewareModule\":{\"activated\":\"0\",\"sticky\":\"0\"},\"ConsultationModule\":{\"activated\":\"0\",\"sticky\":\"0\"},\"CoreCalendar\":{\"activated\":\"0\",\"sticky\":\"0\"},\"FeedbackModule\":{\"activated\":\"0\",\"sticky\":\"0\"}}','Hier finden Sie virtuelle Veranstaltungen zu verschiedenen Gremien an der Universit&auml;t','',0,0,0,'LeiterIn','LeiterInnen','Mitglied','Mitglieder',NULL,NULL,1,0,0,0,1366882120,1641229564),
(3,'Community',0,1,1,0,0,1,1,0,1,0,'{\"CoreAdmin\":{\"activated\":\"1\",\"sticky\":\"1\"},\"CoreOverview\":{\"activated\":\"1\",\"sticky\":\"1\"},\"CoreForum\":{\"activated\":\"0\",\"sticky\":\"0\"},\"Blubber\":{\"activated\":\"1\",\"sticky\":\"0\"},\"CoreParticipants\":{\"activated\":\"0\",\"sticky\":\"0\"},\"CoreDocuments\":{\"activated\":\"0\",\"sticky\":\"0\"},\"CoursewareModule\":{\"activated\":\"0\",\"sticky\":\"0\"},\"CoreWiki\":{\"activated\":\"0\",\"sticky\":\"0\"},\"LtiToolModule\":{\"activated\":\"0\",\"sticky\":\"0\"},\"GradebookModule\":{\"activated\":\"0\",\"sticky\":\"0\"},\"FeedbackModule\":{\"activated\":\"0\",\"sticky\":\"0\"},\"CoreScm\":{\"activated\":\"0\",\"sticky\":\"0\"},\"CoreSchedule\":{\"activated\":\"0\",\"sticky\":\"0\"},\"ConsultationModule\":{\"activated\":\"0\",\"sticky\":\"0\"}}','Hier finden Sie virtuelle Veranstaltungen zu unterschiedlichen Themen','',0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,1,0,0,0,1366882120,1641229633),
(99,'Studiengruppen',0,0,0,0,0,0,0,1,0,0,'{\"CoreStudygroupAdmin\":{\"activated\":\"1\",\"sticky\":\"1\"},\"CoreOverview\":{\"activated\":\"1\",\"sticky\":\"1\"},\"CoreStudygroupParticipants\":{\"activated\":\"1\",\"sticky\":\"1\"},\"Blubber\":{\"activated\":\"1\",\"sticky\":\"0\"},\"CoreForum\":{\"activated\":\"0\",\"sticky\":\"0\"},\"CoreDocuments\":{\"activated\":\"1\",\"sticky\":\"0\"},\"CoreScm\":{\"activated\":\"0\",\"sticky\":\"0\"},\"CoreWiki\":{\"activated\":\"1\",\"sticky\":\"0\"},\"CoursewareModule\":{\"activated\":\"0\",\"sticky\":\"0\"},\"CoreCalendar\":{\"activated\":\"0\",\"sticky\":\"1\"}}','','',1,0,0,'GruppengründerIn','GruppengründerInnen','ModeratorIn','ModeratorInnen','Mitglied','Mitglieder',0,0,0,0,1366882120,1641229657);
/*!40000 ALTER TABLE `sem_classes` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `sem_tree`
--

LOCK TABLES `sem_tree` WRITE;
/*!40000 ALTER TABLE `sem_tree` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `sem_tree` VALUES
('01c8b1d188be40c5ac64b54a01aae294','5b73e28644a3e259a6e0bc1e1499773c',2,'','Test Studienbereich C',NULL,0,NULL,NULL),
('3d39528c1d560441fd4a8cb0b7717285','439618ae57d8c10dcaabcf7e21bcc1d9',0,'','Test Studienbereich A-1',NULL,0,NULL,NULL),
('439618ae57d8c10dcaabcf7e21bcc1d9','5b73e28644a3e259a6e0bc1e1499773c',0,'','Test Studienbereich A',NULL,0,NULL,NULL),
('5b73e28644a3e259a6e0bc1e1499773c','root',1,'','Test Fakultät',NULL,0,NULL,NULL),
('5c41d2b4a5a8338e069dda987a624b74','5b73e28644a3e259a6e0bc1e1499773c',1,'','Test Studienbereich B',NULL,0,NULL,NULL),
('dd7fff9151e85e7130cdb684edf0c370','439618ae57d8c10dcaabcf7e21bcc1d9',1,'','Test Studienbereich A-2',NULL,0,NULL,NULL);
/*!40000 ALTER TABLE `sem_tree` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `sem_types`
--

LOCK TABLES `sem_types` WRITE;
/*!40000 ALTER TABLE `sem_types` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `sem_types` VALUES
(1,'Vorlesung',1,1366882120,1366882120),
(2,'Seminar',1,1366882120,1366882120),
(3,'Übung',1,1366882120,1366882120),
(4,'Praktikum',1,1366882120,1366882120),
(5,'Colloquium',1,1366882120,1366882120),
(6,'Forschungsgruppe',1,1366882120,1366882120),
(7,'sonstige',1,1366882120,1366882120),
(8,'Gremium',2,1366882120,1366882120),
(9,'Projektgruppe',2,1366882120,1366882120),
(10,'sonstige',2,1366882120,1366882120),
(11,'Kulturforum',3,1366882120,1366882120),
(12,'Veranstaltungsboard',3,1366882120,1366882120),
(13,'sonstige',3,1366882120,1366882120),
(99,'Studiengruppe',99,1366882120,1366882120);
/*!40000 ALTER TABLE `sem_types` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `semester_courses`
--

LOCK TABLES `semester_courses` WRITE;
/*!40000 ALTER TABLE `semester_courses` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `semester_courses` VALUES
('4967f0a483e36554b77e3dc47aa58941','a07535cf2f8a72df33c12ddfa4b53dde',1641490271,1641490271);
/*!40000 ALTER TABLE `semester_courses` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `semester_data`
--

LOCK TABLES `semester_data` WRITE;
/*!40000 ALTER TABLE `semester_data` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `semester_data` VALUES
('322f640f3f4643ebe514df65f1163eb1','SoSe 2025','',1743458400,1759269599,NULL,1744581600,1752962399,1,'',NULL,1754464710),
('4967f0a483e36554b77e3dc47aa58941','WiSe 2025/2026','',1759269600,1774994399,NULL,1760306400,1771109999,1,'',NULL,1754464710);
/*!40000 ALTER TABLE `semester_data` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `semester_holiday`
--

LOCK TABLES `semester_holiday` WRITE;
/*!40000 ALTER TABLE `semester_holiday` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `semester_holiday` VALUES
('704038f0cb3ea0a285ba0a453788ebed','','Unterbrechung','',1766358000,1767481200,NULL,1754464710);
/*!40000 ALTER TABLE `semester_holiday` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `seminar_courseset`
--

LOCK TABLES `seminar_courseset` WRITE;
/*!40000 ALTER TABLE `seminar_courseset` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `seminar_courseset` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `seminar_cycle_dates`
--

LOCK TABLES `seminar_cycle_dates` WRITE;
/*!40000 ALTER TABLE `seminar_cycle_dates` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `seminar_cycle_dates` VALUES
('fc3c44f257e448e3cd36a88406a8a9c1','a07535cf2f8a72df33c12ddfa4b53dde','09:00:00','11:00:00',1,'',0.0,0,0,15,0,1530291739,1698856934);
/*!40000 ALTER TABLE `seminar_cycle_dates` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `seminar_inst`
--

LOCK TABLES `seminar_inst` WRITE;
/*!40000 ALTER TABLE `seminar_inst` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `seminar_inst` VALUES
('a07535cf2f8a72df33c12ddfa4b53dde','2560f7c7674942a7dce8eeb238e15d93');
/*!40000 ALTER TABLE `seminar_inst` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `seminar_sem_tree`
--

LOCK TABLES `seminar_sem_tree` WRITE;
/*!40000 ALTER TABLE `seminar_sem_tree` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `seminar_sem_tree` VALUES
('a07535cf2f8a72df33c12ddfa4b53dde','3d39528c1d560441fd4a8cb0b7717285'),
('a07535cf2f8a72df33c12ddfa4b53dde','5c41d2b4a5a8338e069dda987a624b74'),
('a07535cf2f8a72df33c12ddfa4b53dde','dd7fff9151e85e7130cdb684edf0c370');
/*!40000 ALTER TABLE `seminar_sem_tree` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `seminar_user`
--

LOCK TABLES `seminar_user` WRITE;
/*!40000 ALTER TABLE `seminar_user` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `seminar_user` VALUES
('7cb72dab1bf896a0b55c6aa7a70a3a86','e7a0a84b161f3e8c09b4a0a2e8a58147','dozent',0,8,0,'','unknown','',1),
('a07535cf2f8a72df33c12ddfa4b53dde','205f3efb7997a0fc9755da2b535038da','dozent',0,5,1343924407,'','yes','',1),
('a07535cf2f8a72df33c12ddfa4b53dde','7e81ec247c151c02ffd479511e24cc03','tutor',0,5,1343924407,'','yes','',1),
('a07535cf2f8a72df33c12ddfa4b53dde','e7a0a84b161f3e8c09b4a0a2e8a58147','autor',0,5,1343924589,'','unknown','',1);
/*!40000 ALTER TABLE `seminar_user` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `seminar_user_notifications`
--

LOCK TABLES `seminar_user_notifications` WRITE;
/*!40000 ALTER TABLE `seminar_user_notifications` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `seminar_user_notifications` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `seminar_userdomains`
--

LOCK TABLES `seminar_userdomains` WRITE;
/*!40000 ALTER TABLE `seminar_userdomains` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `seminar_userdomains` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `seminare`
--

LOCK TABLES `seminare` WRITE;
/*!40000 ALTER TABLE `seminare` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `seminare` VALUES
('7cb72dab1bf896a0b55c6aa7a70a3a86','','ec2e364b28357106c0f8c282733dbe56','Test Studiengruppe','',99,'Studiengruppen sind eine einfache Möglichkeit, mit KommilitonInnen, KollegInnen und anderen zusammenzuarbeiten.','','',1,1,'','','','','',1268739824,1607705186,'',0,0,0,'',0,1,0,NULL,0,NULL,0,0,0,NULL,NULL),
('a07535cf2f8a72df33c12ddfa4b53dde','12345','2560f7c7674942a7dce8eeb238e15d93','Test Lehrveranstaltung','eine normale Lehrveranstaltung',1,'','','',1,1,'','für alle Studierenden','abgeschlossenes Grundstudium','Referate in Gruppenarbeit','Klausur',1343924407,1716388347,'4',0,0,0,'',0,1,0,NULL,0,NULL,0,0,0,NULL,NULL);
/*!40000 ALTER TABLE `seminare` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `separable_room_parts`
--

LOCK TABLES `separable_room_parts` WRITE;
/*!40000 ALTER TABLE `separable_room_parts` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `separable_room_parts` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `separable_rooms`
--

LOCK TABLES `separable_rooms` WRITE;
/*!40000 ALTER TABLE `separable_rooms` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `separable_rooms` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `session_data`
--

LOCK TABLES `session_data` WRITE;
/*!40000 ALTER TABLE `session_data` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `session_data` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `siteinfo_details`
--

LOCK TABLES `siteinfo_details` WRITE;
/*!40000 ALTER TABLE `siteinfo_details` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `siteinfo_details` VALUES
(1,1,1,0,0,'[lang=de]Ansprechpartner[/lang][lang=en]Contact[/lang]','[style=float: right]\r\n[img]https://develop.studip.de/logos/logoklein.png\r\n**Version:** (:version:)\r\n[/style]\r\n[lang=de]Für diese Stud.IP-Installation ((:uniname:)) sind folgende Administratoren zuständig:[/lang]\r\n[lang=en]The following administrators are responsible for this Stud.IP installation ((:uniname:)):[/lang]\r\n(:rootlist:)\r\n[lang=de]allgemeine Anfragen wie Passwort-Anforderungen u.a. richten Sie bitte an:[/lang]\r\n[lang=en]General queries e.g., password queries, please contact:[/lang]\r\n(:unicontact:)\r\n[lang=de]Folgende Einrichtungen sind beteiligt:\r\n(Genannt werden die jeweiligen Administratoren der Einrichtungen für entsprechende Anfragen)[/lang]\r\n[lang=en]The following institutes participate:\r\n(Named are the institutes administrators responsible for the corresponding query areas)[/lang]\r\n(:adminlist:)'),
(2,1,2,0,0,'[lang=de]Entwickler[/lang][lang=en]Developer[/lang]','[style=float: right]\r\n\r\n[img]https://develop.studip.de/logos/logoklein.png\r\n\r\n**Version:** (:version:)\r\n\r\n[/style]\r\n\r\n[lang=de]Stud.IP ist ein Open Source Projekt zur Unterstützung von Präsenzlehre an Universitäten, Hochschulen und anderen Bildungseinrichtungen. Das System entstand am Zentrum für interdisziplinäre Medienwissenschaft (ZiM) der Georg-August-Universität Göttingen unter Mitwirkung der Suchi & Berg GmbH (data-quest) , Göttingen. Heute erfolgt die Weiterentwicklung von Stud.IP verteilt an vielen Standorten (Göttingen, Osnabrück, Oldenburg, Bremen, Hannover, Jena und weiteren). Die Koordination der Entwicklung erfolgt durch die Stud.IP-CoreGroup.\r\n\r\nStud.IP steht unter der GNU General Public License, Version 2.\r\n\r\n\r\n\r\nWeitere Informationen finden sie auf ** [www.studip.de]http://www.studip.de **,**  [develop.studip.de]http://develop.studip.de **.[/lang]\r\n\r\n\r\n\r\n[lang=en]Stud.IP is an opensource project for supporting attendance courses offered by universities, institutions of higher education and other educational institutions. The system was established at the Zentrum für interdisziplinäre Medienwissenschaft (ZiM) in the Georg-August-Universität Göttingen in cooperation with Suchi & Berg GmbH (data-quest) , Göttingen. At the present further developing takes place at various locations (among others Göttingen, Osnabrück, Oldenburg, Bremen, Hannover, Jena) under coordination through the Stud.IP-CoreGroup.\r\n\r\n\r\n\r\nStud.IP is covered by the GNU General Public Licence, version 2.\r\n\r\n\r\n\r\nFurther information can be found under ** [www.studip.de]http://www.studip.de **,**  [develop.studip.de]http://develop.studip.de **.[/lang]\r\n\r\n\r\n\r\n(:coregroup:)\r\n\r\n[lang=de]Sie erreichen uns auch über folgende **Mailinglisten**:\r\n\r\n\r\n\r\n**Nutzer-Anfragen**, E-Mail: studip-users@lists.sourceforge.net : Fragen, Anregungen und Vorschläge an die Entwickler - bitte __keine__ Passwort Anfragen!\r\n\r\n**News-Mailingsliste**, E-Mail: studip-news@lists.sourceforge.net : News rund um Stud.IP (Eintragung notwendig)\r\n\r\n\r\n\r\nWir laden alle Entwickler, Betreiber und Nutzer von Stud.IP ein, sich auf dem Developer-Server http://develop.studip.de an den Diskussionen rund um die Weiterentwicklung und Nutzung der Plattform zu beteiligen.[/lang]\r\n\r\n[lang=en]You can contact us via the following **mailing lists**:\r\n\r\n\r\n\r\n**User enquiries**, E-Mail: studip-users@lists.sourceforge.net : Questions, suggestions and recommendations to the developers - __please no password queries__!\r\n\r\n\r\n\r\n**News mailing list**, E-Mail: studip-news@lists.sourceforge.net : News about Stud.IP (registration necessary)\r\n\r\n\r\n\r\nWe invite all developers, administrators and users of Stud.IP to join the discussions on further developing and using the platform available at the developer server http://develop.studip.de[/lang]'),
(5,2,1,0,0,'History','(:history:)'),
(7,1,3,0,0,'Datenschutzerklärung','<!--HTML--><p>[lang=de]</p><h1><strong>Mustertext Datenschutzerklärung</strong></h1><p>Es handelt sich um einen Mustertext. Bitte passen Sie die entsprechenden Stellen in eckigen Klammern an und ergänzen, wo nötig, entsprechende Kontaktdaten/Links und streichen Unzutreffendes!</p><p>&nbsp;</p><h1><strong>Datenschutzerklärung:</strong></h1><p>Sie erhalten als Nutzer/in unserer Internetseite in dieser Datenschutzerklärung notwendige Informationen darüber, wie und in welchem Umfang sowie zu welchem Zweck die <strong>[Betreibereinrichtung]</strong> Daten von Ihnen erhebt und wie diese verwendet werden. Die Daten werden nur innerhalb der <strong>[Betreibereinrichtung]</strong> verarbeitet und verwendet und nicht an Dritte weitergegeben.&nbsp;<br><br><strong>1. Rechtsgrundlagen</strong>&nbsp;<br>Die Erhebung und Nutzung Ihrer Daten erfolgt streng nach den gesetzlichen Vorgaben. Regelungen dazu finden sich in:&nbsp;&nbsp;<br>&nbsp;</p><ul><li>Europäische Datenschutzgrundverordnung (EU DSGVO)</li><li>Bundesdatenschutzgesetz (BDSG)</li><li>Niedersächsisches Datenschutzgesetz (NDSG)</li><li>Teledienstegesetz (TDG)</li><li>Mediendienste-Staatsvertrag (MDStV)</li><li>Teledienstedatenschutzgesetz (TDDSG)</li></ul><p>&nbsp;</p><p><strong>2. Personenbezogene Daten</strong>&nbsp;<br>Personenbezogene Daten werden zum Zwecke der administrativen Nutzerverwaltung, zur Kontaktaufnahme und Interaktion mit Ihnen sowie zur Bereitstellung personalisierter Dienste <i>[zur Durchführung Ihres Studiums bzw. Ihrer Arbeit an</i> <i><strong>[Betreibereinrichtung]]</strong></i> von uns gespeichert.&nbsp;<br>Für die Nutzung von Stud.IP werden folgende Daten abgefragt und gespeichert:</p><ul><li>Nutzername</li><li>Vorname, Nachname</li><li>Mailadresse</li><li>[ggf. weitere Daten]</li></ul><p>Weitere Daten von Ihnen, die evtl. gespeichert werden, sind Inhalte, die Sie selbst im Rahmen Ihrer Arbeit selbst in Stud.IP einstellen. Dazu gehören:</p><ul><li>Freiwillige Angaben zur Person</li><li>Beiträge in Foren</li><li>hochgeladene Dateien</li><li>Chatverläufe</li><li>interne Nachrichten</li><li>Kalendereinträge und Stundenpläne</li><li>Teilnahme an Lehrveranstaltungen, Studiengruppen, Gremien</li><li>Persönliche Einstellungen und Konfigurationen</li><li>[ggf. Plugindaten]</li></ul><p>Diese Inhalte werden mit Ihrem Klarnamen gespeichert und angezeigt. Sie haben die Möglichkeit über die Privatsphäreeinstellungen selbst zu bestimmen, ob und ggf. welche Personengruppen diese Daten sehen dürfen. Diese Daten werden von Stud.IP intern verschlüsselt abgelegt.&nbsp;<br><br><br><strong>3. Aufbewahrungsfristen&nbsp;</strong>&nbsp;<br>Ihre personenbezogenen Daten werden für die Dauer Ihres Studiums/Ihrer Arbeit bei <strong>[Betreibereinrichtung]</strong> gespeichert. Nach Beendigung Ihrer Tätigkeit und Ablauf der gesetzlichen Aufbewahrungsfristen werden Ihre Daten gelöscht.</p><p><strong>4. Auskunft, Löschung, Sperrung</strong>&nbsp;<br>Sie erhalten jederzeit auf Anfrage Auskunft über die von uns über Sie gespeicherten personenbezogenen Daten sowie dem Zweck von Datenerhebung sowie Datenverarbeitung. Bitte wenden Sie sich hierzu an u.g. Kontaktadresse.&nbsp;<br><br>Außerdem haben Sie das Recht, die Berichtigung, die Sperrung oder Löschung Ihrer Daten zu verlangen. Sie können Ihre Einwilligung ohne Angabe von Gründen durch Schreiben an die o.g. Kontaktadresse widerrufen. Ihre Daten werden dann umgehend gelöscht. Eine weitere Nutzung von Stud.IP <strong>[der Betreibereinrichtung]</strong> ist dann aber nicht mehr möglich.&nbsp;<br><br>Ausgenommen von der Löschung sind Daten, die aufgrund gesetzlicher Vorschriften aufbewahrt oder zur ordnungsgemäßen Geschäftsabwicklung benötigt werden. Damit eine Datensperre jederzeit realisiert werden kann, werden Daten zu Kontrollzwecken in einer Sperrdatei vorgehalten.&nbsp;<br><br>Werden Daten nicht von einer gesetzlichen Archivierungspflicht erfasst, löschen wir Ihre Daten auf Ihren Wunsch. Greift die Archivierungspflicht, sperren wir Ihre Daten. Für alle Fragen und Anliegen zur Berichtigung, Sperrung oder Löschung von personenbezogenen Daten wenden Sie sich bitte an unsere Datenschutzbeauftragten unter den Kontaktdaten in dieser Datenschutzerklärung bzw. an die im Impressum genannte Adresse.&nbsp;<br><br><br><strong>5. Datenübertragbarkeit</strong>&nbsp;<br>Sie haben das Recht, jederzeit Ihre Daten ausgehändigt zu bekommen. Auf Anfrage stellen wir Ihnen Ihre Daten in menschenlesbaren, gängigen und bearbeitbaren Formaten zur Verfügung.&nbsp;<br><br><br><strong>6. Cookies</strong>&nbsp;<br>Stud.IP verwendet ein Session-Cookie. Diese kleine Textdatei beinhaltet lediglich eine verschlüsselte Zeichenfolge, die bei der Navigation im System hilft. Das Cookie wird bei der Abmeldung aus Stud.IP oder beim Schließen des Browsers gelöscht.&nbsp;<br><br><br><strong>7. Server Logfiles</strong>&nbsp;<br>Mit dem Zugriff auf Stud.IP werden IP-Adresse, Datum, Uhrzeit und Browserversion zum Zeitpunkt des Zugriffs registriert und anonymisiert gespeichert. Die Erhebung und Nutzung dieser Log-File-Daten dient lediglich der Auswertung zu rein statistischen Forschungs- und Evaluationszwecken der Lernplattform, werden also nicht in Verbindung mit Namen oder Mailadresse gespeichert oder ausgewertet. Diese Daten werden für die Zeit von <strong>[X]</strong> Monaten auf gesicherten Systemen der <strong>[Betreibereinrichtung]</strong> gespeichert und ebenfalls nicht an Dritte weitergegeben.&nbsp;<br><br><br><strong>8. SSL-Verschlüsselung</strong>&nbsp;<br>Die Verbindung zu Stud.IP erfolgt mit einer SSL-Verschlüsselung. Über SSL verschlüsselte Daten sind nicht von Dritten lesbar. Übermitteln Sie Ihre vertraulichen Informationen nur bei aktivierter SSL-Verschlüsselung und wenden Sie sich im Zweifel an uns.</p><p><br><strong>9. Kontaktdaten</strong></p><p><strong>[Adresse, Verantwortliche Person/ggf. Datenschutzbeauftragte/r, Mailkontakt, Telefonnummer]</strong></p><p>[/lang] [lang=en]</p><p><strong>Sample text privacy policy</strong></p><p>This is a sample text. Please adapt the relevant passages in square brackets and, where necessary, add the relevant contact details/links and delete what does not apply!</p><p><strong>Privacy policy:</strong></p><p>As a user of our website, you will receive the necessary information in this privacy policy about how and to what extent and for what purpose the <strong>[operatoring organization]</strong> collects data from you and how it is used. The data will only be processed and used within the <strong>[operating organization]</strong> and will not be passed on to third parties.</p><p><strong>1. Legal basis</strong></p><p>Your data is collected and used strictly in accordance with legal requirements. Regulations can be found in:</p><p>· European General Data Protection Regulation (EU GDPR)</p><p>· Federal Data Protection Act (BDSG)</p><p>· Lower Saxony Data Protection Act (NDSG)</p><p>· Teleservices Act (TDG)</p><p>· Interstate Media Services Treaty (MDStV)</p><p>· Teleservices Data Protection Act (TDDSG)</p><p><strong>2. Personal data</strong></p><p>Personal data is stored by us for the purpose of administrative user management, for contacting and interacting with you and for providing personalised services <i>[to carry out your studies or your work at</i> <i><strong>[operating institution]]</strong></i>.</p><p>The following data is requested and stored for the use of Stud.IP:</p><p>· User name</p><p>· First name, surname</p><p>· e-mail address</p><p>· [other data if applicable]</p><p>Other data that may be stored are contents that you yourself enter in Stud.IP as part of your work. This includes</p><p>· Voluntary personal details</p><p>· Posts in forums</p><p>· Uploaded files</p><p>· chat histories</p><p>· internal messages</p><p>· Calendar entries and timetables</p><p>· Participation in courses, study groups, committees</p><p>· Personal settings and configurations</p><p>· [Plugin data, if applicable]</p><p>This content is saved and displayed with your real name. You can use the privacy settings to determine whether and, if so, which groups of people are allowed to see this data. This data is stored internally encrypted by Stud.IP.</p><p><strong>3. Retention periods</strong></p><p>Your personal data will be stored for the duration of your studies/your work at <strong>[operating organization]</strong>. Your data will be deleted after the end of your employment and expiry of the statutory retention periods.</p><p><strong>4. Information, deletion, blocking</strong></p><p>You can request information about the personal data we have stored about you and the purpose of data collection and data processing at any time. Please use the contact address below for this purpose.</p><p>You also have the right to request the correction, blocking or deletion of your data. You can revoke your consent without giving reasons by writing to the above contact address. Your data will then be deleted immediately. However, further use of Stud.IP [the operating organization] is then no longer possible.</p><p>Excluded from the deletion are data that are stored due to legal regulations or are required for proper business transactions. To ensure that a data lock can be realised at any time, data is stored in a lock file for control purposes.</p><p>&nbsp;</p><p>If data is not covered by a statutory archiving obligation, we will delete your data at your request. If the archiving obligation applies, we will block your data. For all questions and concerns regarding the correction, blocking or deletion of personal data, please contact our data protection officer using the contact details in this privacy policy.</p><p><strong>5. Data portability</strong></p><p>You have the right to receive your data at any time. On request, we will provide you with your data in human-readable, common and editable formats.</p><p><strong>6. Cookies</strong></p><p>Stud.IP uses a session cookie. This small text file only contains an encrypted character string that helps you navigate the system. The cookie is deleted when you log out of Stud.IP or close the browser.</p><p><strong>7. Server log files</strong></p><p>When Stud.IP is accessed, the IP address, date, time and browser version at the time of access are registered and stored in anonymised form. This log file data is only collected and used for purely statistical research and evaluation purposes of the learning platform and is therefore not stored or analysed in connection with names or email addresses. This data is stored for a period of <strong>[X]</strong> months on secure systems of the <strong>[operating organization]</strong> and is also not passed on to third parties.</p><p><strong>8. SSL encryption</strong></p><p>The connection to Stud.IP is made using SSL encryption. Data encrypted via SSL cannot be read by third parties. Only transmit your confidential information if SSL encryption is activated and contact us if in doubt.</p><p><strong>9. Contact details</strong></p><p><strong>[Address, person responsible / data protection officer, email contact, telephone number]</strong></p><p>&nbsp;</p><p>[/lang]</p>'),
(8,1,4,0,0,'Barrierefreiheitserklärung','<!--HTML--><p>[lang=de]</p> <h1>Mustertext Erklärung zur Barrierefreiheit für Stud.IP</h1><p>[Text in eckigen Klammern ist ggf. zu ergänzen, zu streichen oder sprachlich anzupassen, je nachdem, wie das Ergebnis der Überprüfung der Barrierefreiheit ausfällt.]</p><p>Diese Erklärung zur Barrierefreiheit gilt für die Stud.IP-Installation unter der [URL der ergänzen; bitte Version und Datum angeben] der [Betreiber der Stud.IP-Installation ergänzen].</p><p>Als öffentliche Stelle im Sinne der Richtlinie (EU) 2016/2102 sind wir bemüht, unsere Websites und mobilen Anwendungen im Einklang mit den Bestimmungen des Behindertengleichstellungsgesetzes des Bundes (BGG) sowie der Barrierefreien-Informationstechnik-Verordnung (BITV 2.0) zur Umsetzung der Richtlinie (EU) 2016/2102 barrierefrei zugänglich zu machen.</p><p>[Hier ggf. jeweilige Landesverordnung zusätzlich einfügen, z.B. für Niedersachsen <i>§ 9 NBGG.</i>]</p><h2>Stand der Vereinbarkeit mit den Anforderungen</h2><p>Die Anforderungen der Barrierefreiheit ergeben sich aus §§ 3 Absätze 1 bis 4 und 4 der BITV 2.0, die auf der Grundlage von § 12d BGG erlassen wurde.</p><p>Die Überprüfung der Einhaltung der Anforderungen beruht auf einer von Materna Information &amp; Communications SE Anfang 2022 vorgenommenen Bewertung. Maßstab der Prüfung ist die EN 301 549 und der A sowie AA Status der WCAG 2.1. Überprüft wurden die Vorgaben der WCAG 2.1 (Konformitätsstufen A und AA) anhand der 98 Prüfschritte des BITV/WCAG-Tests.</p><p>Die Überprüfung bezieht sich auf das Stud.IP-Release 5.0 [Plugins und Inhalte müssen standortspezifisch geprüft und ggf. dokumentiert/diese Erklärung angepasst werden. Bitte ggf. ergänzen.]</p><p>Diese Stud.IP-Installation ist nicht vollständig mit den für uns geltenden Vorschriften zur Barrierefreiheit vereinbar. Im Einzelnen:</p><ul><li>Überschriftenhierarchien werden auf manchen Seiten nicht vollständig eingehalten.</li><li>Für Bilder, Bedienelemente und grafische Elemente sind in manchen Fällen keine, falsche oder unzureichende Alternativen vorhanden.</li><li>Grafiken und Bedienelementen fehlen in manchen Fällen korrekte Auszeichnungen, so dass sie von Assistenzsystemen nicht richtig erfasst werden können.</li><li>Die Sprache in Alternativtexten ist teilweise in Englisch angegeben ohne das der Sprachwechsel korrekt ausgezeichnet ist.</li><li>Listeneinträge, Tabellen(-spalten) und Formulare sind teilweise nicht korrekt ausgezeichnet.</li><li>Die sichtbare Reihenfolge von Seitenelementen weicht teilweise von der Reihenfolge im Quelltext ab.</li><li>Die Mindestanforderung an Kontraste ist nicht überall erfüllt.</li><li>Die Tastatursteuerung ist nicht uneingeschränkt benutzbar.</li><li>Auf der Startseite fehlt die Bereitstellung der Erläuterungen über die Website in Leichter Sprache und in Deutscher Gebärdensprache.</li><li>&nbsp;</li></ul><p>Zudem können von Nutzerinnen und Nutzern eingestellte Inhalte, z.B. PDFs oder Videos, Barrieren aufweisen.</p><p>Stud.IP wurde in Bezug auf Barrierefreiheit überarbeitet. Folgende Maßnahmen zur Verringerung von Barrieren sind in die Releases ab Stud.IP-Version 5.1 eingeflossen:</p><ul><li>Attribute und Alternativtexte wurden hinzugefügt und korrigiert, damit Screen Reader Schaltflächen, Links, Bedienelemente und Grafiken korrekt interpretieren und passende Texte ausgeben können.</li><li>Die Hierarchien von Überschriften und Reihenfolge von Seitenelementen wurden überarbeitet/korrigiert.</li><li>Sprachauszeichnungen wurden vereinheitlicht.</li><li>Listeneinträge, Tabellen(-spalten) und Formulare wurden korrekt ausgezeichnet und sinnvoll verknüpft.</li><li>Eine Möglichkeit den Kontrast zu verändern, wurde implementiert.</li><li>Die Tastatursteuerung wurde korrigiert.</li><li>Aktionsmenüs und Akkordeonelemente wurden überarbeitet.</li><li>Die responsive Navigation wurde verbessert.</li><li>Eine Mustererklärung zur leichten Sprache soll in Stud.IP 5.5 eingehen.</li></ul><p>&nbsp;</p><p>Folgende Inhalte sind aufgrund der Absicht, ein höheres Maß an digitaler Barrierefreiheit als gesetzlich gefordert umzusetzen, realisiert:<br>[Geben Sie die jeweiligen Inhalte an]</p><h2>Datum der Erstellung bzw. der letzten Aktualisierung der Erklärung</h2><p>Diese Erklärung wurde am [09/2023] erstellt und zuletzt am [Datum] aktualisiert.</p><h2>Barrieren melden: Kontakt zu den Feedback Ansprechpartnern</h2><p>Sie möchten uns bestehende Barrieren mitteilen oder Informationen zur Umsetzung der Barrierefreiheit erfragen? Für Ihr Feedback sowie alle weiteren Informationen sprechen Sie unsere verantwortlichen Kontaktpersonen unter xxx an.</p><p>[verlinkte URL mit Namen des Feedback-Mechanismus, z. B. „Barrieren melden“ angeben. Dabei sollte der Leitfaden „Erklärung zur Barrierefreiheit“ und der Leitfaden „Feedback-Mechanismus“ beachtet werden]</p><h2>Schlichtungsverfahren</h2><p>[Nicht zutreffende Stelle streichen ggf. Stelle Ihres Bundeslandes einfügen]</p><p>Wenn auch nach Ihrem Feedback an den oben genannten Kontakt keine zufriedenstellende Lösung gefunden wurde, können Sie sich an die Schlichtungsstelle nach § 16 BGG wenden. Die Schlichtungsstelle BGG hat die Aufgabe, bei Konflikten zum Thema Barrierefreiheit zwischen Menschen mit Behinderungen und öffentlichen Stellen des Bundes eine außergerichtliche Streitbeilegung zu unterstützen. Das Schlichtungsverfahren ist kostenlos. Es muss kein Rechtsbeistand eingeschaltet werden. Weitere Informationen zum Schlichtungsverfahren und den Möglichkeiten der Antragstellung erhalten Sie unter: <a href=\"http://www.schlichtungsstelle-bgg.de/\"><u>www.schlichtungsstelle-bgg.de</u></a>.</p><p>Direkt kontaktieren können Sie die Schlichtungsstelle BGG unter <a href=\"mailto:info@schlichtungsstelle-bgg.de\"><u>info@schlichtungsstelle-bgg.de</u></a>.</p><p>(:reportbarrierlink:)[/lang]&nbsp;</p><p>[lang=en]</p><h1>Sample Text Accessibility Statement</h1><p>[Text in square brackets may need to be added, deleted, or linguistically adjusted depending on the outcome of the accessibility review].</p><p>This accessibility statement applies to the Stud.IP installation at the [add URL of; please specify version and date] of the [add operator of Stud.IP installation].</p><p>As a public body within the meaning of Directive (EU) 2016/2102, we strive to make our websites and mobile applications accessible in accordance with the provisions of the Federal Disability Equality Act (BGG) and the Barrier-Free Information Technology Ordinance (BITV 2.0) implementing Directive (EU) 2016/2102.</p><p>[Here, if necessary, insert the respective state ordinance additionally, e.g. for Lower Saxony § 9 NBGG].</p><h2>Status of compatibility with the requirements</h2><p>The accessibility requirements result from §§ 3 paragraphs 1 to 4 and 4 of BITV 2.0, which was issued on the basis of § 12d BGG.</p><p>The review of compliance with the requirements is based on</p><p>an assessment performed by Materna Information &amp; Communications SE in the beginning of 2022. The benchmark for the test is EN 301 549 and the A and AA status of WCAG 2.1. The requirements of WCAG 2.1 (conformance levels A and AA) were checked using the 98 test steps of the BITV/WCAG test.</p><p>The check refers to Stud.IP release 5.0 [Plugins and content must be checked site-specifically and documented/adapted to this statement if necessary. Please add if necessary].</p><p>This Stud.IP installation is not fully compliant with the accessibility regulations that apply to us. In detail:</p><ul><li>Heading hierarchies are not fully respected on some pages.</li><li>In some cases, there are no, incorrect or insufficient alternatives for images, control elements and graphical elements.</li><li>Graphics and control elements lack correct markup in some cases, so that they cannot be correctly detected by assistance systems.</li><li>The language in alternative texts is sometimes specified in English without the language change being correctly marked.</li><li>List entries, tables (columns) and forms are sometimes not correctly labelled.</li><li>The visible order of page elements sometimes differs from the order in the source text.</li><li>The minimum requirement for contrasts is not met everywhere.</li><li>The keyboard control is not fully usable.</li><li>The home page lacks the provision of explanations about the website in plain language and in German sign language.</li></ul><p>In addition, content posted by users, e.g. PDFs or videos, may have barriers.</p><p>Stud.IP is currently being revised with regard to accessibility. The following measures to reduce barriers are expected to be included in the release of Stud.IP 5.1 and following:</p><ul><li>Attributes and alternative texts are added and corrected so that screen readers can correctly interpret buttons, links, controls and graphics and output appropriate texts.</li><li>Heading hierarchies and page element order are revised/corrected.</li><li>Language mark-ups will be standardized.</li><li>List entries, tables (columns) and forms will be labelled correctly and linked in a meaningful way.</li><li>A possibility to change the contrast is implemented.</li><li>The keyboard control is corrected.</li><li>Action menus and accordion elements are revised.</li><li>Responsive navigation will be improved.</li><li>A sample declaration on easy language should be included in Stud.IP 5.5.</li></ul><p>&nbsp;</p><p>The following content is implemented due to the intention to implement a higher level of digital accessibility than required by law:</p><p>[Specify the respective content]</p><h2>Date of preparation or last update of the declaration</h2><p>This statement was created on [09/2021] and last updated on [date].</p><h2>Report Barriers: Contact Feedback Contacts</h2><p>Would you like to report existing barriers or request information on implementing accessibility? For your feedback as well as any further information, please contact our responsible contact persons at xxx.</p><p>[provide linked URL with name of feedback mechanism, e.g. \"Report barriers\". The \"Accessibility Statement\" guide and the \"Feedback Mechanism\" guide should be followed].</p><h2>Arbitration</h2><p>[Delete non-applicable body, if necessary insert body of your federal state].</p><p>If a satisfactory solution has not been found even after you have sent feedback to the above-mentioned contact, you can turn to the conciliation body pursuant to Section 16 BGG. The BGG conciliation body is tasked with supporting out-of-court dispute resolution in the event of conflicts on the topic of accessibility between people with disabilities and federal public agencies. The conciliation procedure is free of charge. No legal counsel needs to be involved. For more information on the conciliation process and how to submit a request, visit: www.schlichtungsstelle-bgg.de.</p><p>You can contact the BGG conciliation body directly at <a href=\"mailto:info@schlichtungsstelle-bgg.de\"><u>info@schlichtungsstelle-bgg.de</u></a>.</p><p>(:reportbarrierlink:)[/lang]</p>'),
(9,1,5,1,0,'Leichte Sprache','++**Leichte Sprache**\n\n1) Beschreibung des Anbieters und des Zwecks der Seite\nDies sind die Internet-Seiten für Inhalte zum Lernen und Lehren von **[Einrichtung einsetzen]** .\nEine [anpassen: Universität/Hochschule/Volks-Hochschule/ oder anderes] ist ein Ort an dem man nach der normalen Schule weiter lernen kann.\nWenn man hier lernt [ggf. anpassen/arbeitet/eine Ausbildung macht] oder als Lehrer arbeitet, bekommt man Anmelde-Daten.\nWenn man angemeldet ist, findet man Material zum Unterricht.\nAußerdem kann man seinen Kalender und Stundenplan sehen.\nMan kann mit anderen Nachrichten schreiben.\n\n2) Hinweise zur Navigation\nUm sich anzumelden, braucht man einen Benutzer-Namen und ein Passwort.\nBenutzer-Name und Passwort bekommt man von **[Name bzw. Einrichtung angeben].**\nMan meldet sich in dem Kasten an, wo Login steht.\nHilfe bei der Anmeldung findet man [Link von Einrichtung einzusetzen oder Text von Einrichtung zu ergänzen].\n[Falls auf der Startseite vorhanden:\nHilfe gibt es oben rechts [ggf. anpassen] bei dem Fragezeichen.\nGanz unten bei Impressum findet man Angaben dazu, wer die Seite gemacht hat.\nGanz unten bei Datenschutz steht, welche Daten von Besuchern der Seite verwendet werden.\nGanz unten kann man unter Barriere melden sich beschweren, wenn man die Seite nicht bedienen kann.\n\n3) Erläuterung der wesentlichen Inhalte der Erklärung zur Barrierefreiheit\n[Je nach Standort sind ggf. die Gesetzesstellen und Behörden anzupassen.]\n\nErklärung zur Barriere-Freiheit in leichter Sprache\nDie [Betreibername einsetzen] ist für Barriere-Freiheit im Internet.\nDas bedeutet: Alle Menschen bekommen alle wichtigen Infos.\nZum Beispiel können blinde Menschen Vorlese-Programme nutzen.\n\nDie [Betreibername einsetzen] beachtet die Vorschriften.\nDazu ist man gesetzlich verpflichtet.\n\nDas sind:\n- das Behinderten-Gleichstellungs-Gesetz (BGG)\n- Verordnung zur Schaffung barrierefreier Informations-Technik nach dem Behinderten-Gleichstellungs-Gesetz (BITV)\n- das Behinderten-Gleichstellungs-Gesetz des [Bundesland oder Bund einfügen]\n\n[Da es Pflicht ist, auf bekannte Barrieren hinzuweisen, sind diese hier vom jeweiligen Betreiber zusammenzufassen und in leichter Sprache zu erläutern.\nIn etwa:\n- Auf manchen Seiten sind die Überschriften ein bisschen durcheinander. Zum Beispiel: Da steht was unten mit kleinen Buchstaben. Das müsste aber oben mit größeren Buchstaben stehen.\n- Manche Sachen werden so vorgelesen, dass blinde Menschen sie schlecht verstehen. Sie sehen das ja nicht.\n…]\n\nSind Sie nicht zufrieden?\nHaben Sie eine Barriere gefunden?\nSie können uns schreiben.\n**Hier ist ein Formular:**\n[jeweiliges Barriere-melden-Formular am Standort verlinken]\n\n**Hier ist unsere Adresse:**\n[Adresse einfügen]\n\n**Sie können uns anrufen:**\n[Telefonnummer einfügen]\n\nEs gibt die **Schlichtungs-Stelle.**\n\n**Schlichtung** bedeutet:\n- Sich einigen.\n- Sich vertragen.\nDie Schlichtungs-Stelle **hilft bei einem Streit.**\n\nZum Beispiel:\n1. Es gibt eine Barriere bei der [Einrichtung einfügen] auf den Internet-Seiten.\n2. Sie haben sich darüber beschwert.\n3. Die Barriere bleibt aber.\n\nJetzt kann die **Schlichtungs-Stelle helfen.**\nDer Streit muss dann nicht vor ein Gericht.\nBeide Seiten sollen sich vertragen.\nSie können eine **Schlichtung beantragen.**\n\nZum Beispiel:\nSie sind mit einer Antwort von [Einrichtung einfügen] zur Barriere-Freiheit nicht zufrieden.\n\nEine **Schlichtung** kostet nichts.\nSie brauchen **keinen Anwalt.**\nSie können den **Antrag in Leichter Sprache oder in Deutscher Gebärden-Sprache** stellen.\n\n**Hier gibt es weitere Informationen:**\n[Idealerweise Link auf jeweilige Schlichtungsstelle und deren Informationen in leichter Sprache einfügen]\n\n**Hier ist die Adresse der Schlichtungs-Stelle:**\n[jeweils zuständige Stelle einfügen]\n\n**Hier ist die Telefonnummer:**\n[Telefonnummer einfügen]\n\n4) Hinweise auf weitere in diesem Auftritt vorhandene Informationen in Deutscher Gebärdensprache und in Leichter Sprache\n\n[So weitere Hinweise in Leichter Sprache oder Gebärdensprache vorhanden sind, muss auf die entsprechenden Orte verwiesen werden.]\nHier finden Sie weitere Hinweise in Leichter Sprache: [Link einfügen]\nOder:\nEs sind keine weiteren Informationen auf diesen Seiten in leichter oder Gebärden-Sprache enthalten.++\n'),
(10,1,NULL,1,0,'Nutzungsbedingungen','<!--HTML--><p>[lang=de]</p><ol><li>Bei Stud.IP besteht Klarnamenpflicht. Der Benutzer oder die Benutzerin verpflichtet sich, seinen/ihren korrekten Vornamen und Nachnamen anzugeben. Der zum Login benötigte Anmeldename ist innerhalb der programmtechnisch festgelegten Grenzen frei wählbar.</li><li>Der Benutzer oder die Benutzerin hat sicherzustellen, dass seine/ihre angegebene E-Mailadresse gültig und funktionsfähig ist.</li><li>Alle anderen Angaben zu Ihrer Person erfolgen freiwillig. Wenn Sie weitere Daten von sich angeben, sind diese nur für andere, registrierte Nutzer des Systems zugänglich. Eine Ausnahme hiervon sind automatisch aus dem System generierte Personalverzeichnisse der beteiligten Institute.</li><li>Der Benutzer oder die Benutzerin stellt sicher, dass er/sie bei der Nutzung des Kommunikationssystems Stud.IP nicht gegen eine geltende Rechtsvorschrift verstößt. Insbesondere verpflichtet sich der Benutzer oder die Benutzerin:<ol style=\"list-style-type:lower-latin;\"><li>Stud.IP weder zum Abruf noch zur Verbreitung von sitten- oder rechtswidrigen Inhalten zu benutzen.</li><li>Die geltenden Jugendschutzvorschriften zu beachten.</li><li>Die Privatsphäre anderer zu respektieren und daher in keinem Fall belästigende, verleumderische oder bedrohende Inhalte einzustellen oder zu verschicken.</li><li>Keine Anwendungen auszuführen, die zu einer Veränderung der physikalischen oder logischen Struktur der genutzten Netze führen können.</li></ol></li><li>Die Nutzung von Stud.IP für jede andere Form von Werbe- oder Marketingbotschaften ist nicht gestattet und verpflichtet den Benutzern oder die Benutzerin zum Ersatz des Stud.IP entstandenen Schadens.</li><li>Der Benutzer oder die Benutzerin verpflichtet sich, seinen/ihren Zugang gegen die unbefugte Benutzung durch Dritte zu schützen. Stud.IP weist an dieser Stelle darauf hin, dass das Passwort nicht weitergegeben werden darf. Der Benutzer oder die Benutzerin haftet für jede durch sein/ihr Verhalten ermöglichte unbefugte Benutzung seines/ihres Accounts, soweit ihn/sie ein Verschulden trifft.</li><li>Bei einem Verstoß des Benutzers oder der Benutzerin gegen die oben aufgeführten Obliegenheiten erfolgt eine unverzügliche Sperrung des Zugangs.</li></ol><p>[/lang] [lang=en]</p><ol><li>Stud.IP bears a RealName obligation. &nbsp;The user is obliged to give his or her correct forename and surname. The registration name, necessary to login, is arbitary within the technical limitations of the program.</li><li>The user must ensure that his or her entered E-mail address is valid and functional.</li><li>All other information about the user is not compulsory. If you enter further information, it will only be accessed by other, registered users in the system. The only exception is system automatically generated personel indexes of the participating institutes.</li><li>The user must make sure that he or she does not violate any applicable laws or regulations by using Stud.IP communication system. In particular, the user is obliged:<ol><li>not to use Stud.IP to either call up or distribute immoral or illegal material.</li><li>to heed the applicable child protection regulations.</li><li>to respect the privacy of others and under no circumstances to call up or send harrassing, libellous or threatening material.</li><li>not to execute any applications that may lead to a change in the physical or logical structure of the shared net.</li></ol></li><li>The usage of Stud.IP for all other forms of advertising or marketing is not permitted, in which case the user is obliged to compensate Stud.IP for any damage caused.</li><li>The user is obliged to protect his or her access to Stud.IP against the unauthorised use by a third party. To this affect, Stud.IP advises that the password should not be passed on. The user is liable for every unauthorised usage of his or her account, as long as it is his or her fault.</li><li>Following a breachment of the above conditions by the user, the access will be immediately blocked.</li></ol><p>[/lang]</p>');
/*!40000 ALTER TABLE `siteinfo_details` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `siteinfo_rubrics`
--

LOCK TABLES `siteinfo_rubrics` WRITE;
/*!40000 ALTER TABLE `siteinfo_rubrics` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `siteinfo_rubrics` VALUES
(1,NULL,'[lang=de]Kontakt[/lang][lang=en]Contact[/lang]'),
(2,NULL,'[lang=de]Über Stud.IP[/lang][lang=en]About Stud.IP[/lang]');
/*!40000 ALTER TABLE `siteinfo_rubrics` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `statusgruppe_user`
--

LOCK TABLES `statusgruppe_user` WRITE;
/*!40000 ALTER TABLE `statusgruppe_user` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `statusgruppe_user` VALUES
('2f597139a049a768dbf8345a0a0af3de','e7a0a84b161f3e8c09b4a0a2e8a58147',1,1,1,NULL),
('5d40b1fc0434e6589d7341a3ee742baf','205f3efb7997a0fc9755da2b535038da',1,1,1,NULL),
('efb56e092f33cb78a8766676042dc1c5','7e81ec247c151c02ffd479511e24cc03',1,1,1,NULL),
('f4319d9909e9f7cb4692c16771887f22','205f3efb7997a0fc9755da2b535038da',1,1,1,NULL),
('f4319d9909e9f7cb4692c16771887f22','7e81ec247c151c02ffd479511e24cc03',2,1,1,NULL);
/*!40000 ALTER TABLE `statusgruppe_user` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `statusgruppen`
--

LOCK TABLES `statusgruppen` WRITE;
/*!40000 ALTER TABLE `statusgruppen` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `statusgruppen` VALUES
('2f597139a049a768dbf8345a0a0af3de','Studierende',NULL,'a07535cf2f8a72df33c12ddfa4b53dde',1,0,0,0,0,1343924562,1343924562,0,NULL,NULL),
('5d40b1fc0434e6589d7341a3ee742baf','Direktor/-in',NULL,'2560f7c7674942a7dce8eeb238e15d93',1,0,0,0,0,1156516698,1156516698,0,NULL,NULL),
('600403561c21a50ae8b4d41655bd2191','Hochschullehrer/-in',NULL,'2560f7c7674942a7dce8eeb238e15d93',4,0,0,0,0,1156516698,1156516698,0,NULL,NULL),
('86498c641ccf4f4d4e02f4961ccc3829','Lehrbeauftragte',NULL,'2560f7c7674942a7dce8eeb238e15d93',3,0,0,0,0,1156516698,1156516698,0,NULL,NULL),
('efb56e092f33cb78a8766676042dc1c5','wiss. Mitarbeiter/-in',NULL,'2560f7c7674942a7dce8eeb238e15d93',2,0,0,0,0,1156516698,1156516698,0,NULL,NULL),
('f4319d9909e9f7cb4692c16771887f22','Lehrende',NULL,'a07535cf2f8a72df33c12ddfa4b53dde',0,0,0,0,0,1343924551,1343924551,0,NULL,NULL);
/*!40000 ALTER TABLE `statusgruppen` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `stock_images`
--

LOCK TABLES `stock_images` WRITE;
/*!40000 ALTER TABLE `stock_images` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `stock_images` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `studygroup_courses`
--

LOCK TABLES `studygroup_courses` WRITE;
/*!40000 ALTER TABLE `studygroup_courses` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `studygroup_courses` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `studygroup_courses_proposals`
--

LOCK TABLES `studygroup_courses_proposals` WRITE;
/*!40000 ALTER TABLE `studygroup_courses_proposals` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `studygroup_courses_proposals` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `studygroup_invitations`
--

LOCK TABLES `studygroup_invitations` WRITE;
/*!40000 ALTER TABLE `studygroup_invitations` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `studygroup_invitations` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `studygroup_stgteil`
--

LOCK TABLES `studygroup_stgteil` WRITE;
/*!40000 ALTER TABLE `studygroup_stgteil` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `studygroup_stgteil` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `tags`
--

LOCK TABLES `tags` WRITE;
/*!40000 ALTER TABLE `tags` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `tags` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `tags_relations`
--

LOCK TABLES `tags_relations` WRITE;
/*!40000 ALTER TABLE `tags_relations` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `tags_relations` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `termin_related_groups`
--

LOCK TABLES `termin_related_groups` WRITE;
/*!40000 ALTER TABLE `termin_related_groups` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `termin_related_groups` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `termin_related_persons`
--

LOCK TABLES `termin_related_persons` WRITE;
/*!40000 ALTER TABLE `termin_related_persons` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `termin_related_persons` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `termine`
--

LOCK TABLES `termine` WRITE;
/*!40000 ALTER TABLE `termine` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `termine` VALUES
('02191f8621da3f781ed714cae1d24e40','a07535cf2f8a72df33c12ddfa4b53dde','cli','',1769414400,1769421600,1754464711,1754464711,1,NULL,'fc3c44f257e448e3cd36a88406a8a9c1',NULL),
('0600718a336675ba27ea80121a4db9e2','a07535cf2f8a72df33c12ddfa4b53dde','cli','',1767600000,1767607200,1754464711,1754464711,1,NULL,'fc3c44f257e448e3cd36a88406a8a9c1',NULL),
('0afb3ee903d2679dce0d3e71796b0d15','a07535cf2f8a72df33c12ddfa4b53dde','cli','',1760338800,1760346000,1754464710,1754464710,1,NULL,'fc3c44f257e448e3cd36a88406a8a9c1',NULL),
('0da3ff500c2b610b559ad67ec69f8158','a07535cf2f8a72df33c12ddfa4b53dde','cli','',1762761600,1762768800,1754464711,1754464711,1,NULL,'fc3c44f257e448e3cd36a88406a8a9c1',NULL),
('1cc62381340bb933dce847648aa57c42','a07535cf2f8a72df33c12ddfa4b53dde','cli','',1762156800,1762164000,1754464711,1754464711,1,NULL,'fc3c44f257e448e3cd36a88406a8a9c1',NULL),
('25b8d290d6c8d1357053cff0d0f2c0c2','a07535cf2f8a72df33c12ddfa4b53dde','cli','',1760943600,1760950800,1754464711,1754464711,1,NULL,'fc3c44f257e448e3cd36a88406a8a9c1',NULL),
('2674f544de3a8a868427bf952d450e3f','a07535cf2f8a72df33c12ddfa4b53dde','cli','',1761552000,1761559200,1754464711,1754464711,1,NULL,'fc3c44f257e448e3cd36a88406a8a9c1',NULL),
('30b480d6506c4f2d2becceee29254e46','a07535cf2f8a72df33c12ddfa4b53dde','76ed43ef286fb55cf9e41beadb484a9f','',1771664400,1760788800,1754464711,1754464711,3,NULL,NULL,NULL),
('42c1555ea5ee40618f5151472354b9f1','7cb72dab1bf896a0b55c6aa7a70a3a86','76ed43ef286fb55cf9e41beadb484a9f','',1753516800,1745064000,1754464711,1754464711,3,NULL,NULL,NULL),
('89db52985137b9e9718eb3d26313b109','a07535cf2f8a72df33c12ddfa4b53dde','cli','',1763971200,1763978400,1754464711,1754464711,1,NULL,'fc3c44f257e448e3cd36a88406a8a9c1',NULL),
('ab30294bab1ca419cfe4217e8388c47c','a07535cf2f8a72df33c12ddfa4b53dde','cli','',1763366400,1763373600,1754464711,1754464711,1,NULL,'fc3c44f257e448e3cd36a88406a8a9c1',NULL),
('c46b36d8600655e7eda58339876a7a8a','a07535cf2f8a72df33c12ddfa4b53dde','cli','',1764576000,1764583200,1754464711,1754464711,1,NULL,'fc3c44f257e448e3cd36a88406a8a9c1',NULL),
('d2e6708b3c86b5be09ae480af133a1e4','a07535cf2f8a72df33c12ddfa4b53dde','cli','',1768204800,1768212000,1754464711,1754464711,1,NULL,'fc3c44f257e448e3cd36a88406a8a9c1',NULL),
('d8b107a56aaac63e3b64f1f4bbd267c7','a07535cf2f8a72df33c12ddfa4b53dde','cli','',1765785600,1765792800,1754464711,1754464711,1,NULL,'fc3c44f257e448e3cd36a88406a8a9c1',NULL),
('eb7f88966b07133303ffb858ed45f072','a07535cf2f8a72df33c12ddfa4b53dde','cli','',1768809600,1768816800,1754464711,1754464711,1,NULL,'fc3c44f257e448e3cd36a88406a8a9c1',NULL),
('ed58ca52de8adaaf548c637950a4c63d','a07535cf2f8a72df33c12ddfa4b53dde','cli','',1765180800,1765188000,1754464711,1754464711,1,NULL,'fc3c44f257e448e3cd36a88406a8a9c1',NULL);
/*!40000 ALTER TABLE `termine` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `termsadmissions`
--

LOCK TABLES `termsadmissions` WRITE;
/*!40000 ALTER TABLE `termsadmissions` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `termsadmissions` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `themen`
--

LOCK TABLES `themen` WRITE;
/*!40000 ALTER TABLE `themen` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `themen` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `themen_termine`
--

LOCK TABLES `themen_termine` WRITE;
/*!40000 ALTER TABLE `themen_termine` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `themen_termine` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `timedadmissions`
--

LOCK TABLES `timedadmissions` WRITE;
/*!40000 ALTER TABLE `timedadmissions` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `timedadmissions` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `tools_activated`
--

LOCK TABLES `tools_activated` WRITE;
/*!40000 ALTER TABLE `tools_activated` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `tools_activated` VALUES
('110ce78ffefaf1e5f167cd7019b728bf','institute',14,1,NULL,1640880515,1640880515),
('110ce78ffefaf1e5f167cd7019b728bf','institute',15,0,NULL,1640880515,1640880515),
('110ce78ffefaf1e5f167cd7019b728bf','institute',25,2,NULL,1640880515,1640880515),
('1535795b0d6ddecac6813f5f6ac47ef2','institute',14,1,NULL,1640880515,1640880515),
('1535795b0d6ddecac6813f5f6ac47ef2','institute',15,0,NULL,1640880515,1640880515),
('1535795b0d6ddecac6813f5f6ac47ef2','institute',25,2,NULL,1640880515,1640880515),
('2560f7c7674942a7dce8eeb238e15d93','institute',14,1,NULL,1640880515,1640880515),
('2560f7c7674942a7dce8eeb238e15d93','institute',15,0,NULL,1640880515,1640880515),
('2560f7c7674942a7dce8eeb238e15d93','institute',25,2,NULL,1640880515,1640880515),
('536249daa596905f433e1f73578019db','institute',14,1,NULL,1640880515,1640880515),
('536249daa596905f433e1f73578019db','institute',15,0,NULL,1640880515,1640880515),
('536249daa596905f433e1f73578019db','institute',25,2,NULL,1640880515,1640880515),
('7a4f19a0a2c321ab2b8f7b798881af7c','institute',14,1,NULL,1640880515,1640880515),
('7a4f19a0a2c321ab2b8f7b798881af7c','institute',15,0,NULL,1640880515,1640880515),
('7a4f19a0a2c321ab2b8f7b798881af7c','institute',25,2,NULL,1640880515,1640880515),
('7cb72dab1bf896a0b55c6aa7a70a3a86','course',1,2,NULL,1640880515,1640880515),
('7cb72dab1bf896a0b55c6aa7a70a3a86','course',14,1,NULL,1640880515,1640880515),
('7cb72dab1bf896a0b55c6aa7a70a3a86','course',16,0,NULL,1640880515,1640880515),
('7cb72dab1bf896a0b55c6aa7a70a3a86','course',17,3,NULL,1640880515,1640880515),
('7cb72dab1bf896a0b55c6aa7a70a3a86','course',19,4,NULL,1640880515,1640880515),
('7cb72dab1bf896a0b55c6aa7a70a3a86','course',22,5,NULL,1640880515,1640880515),
('a07535cf2f8a72df33c12ddfa4b53dde','course',1,3,'[]',1640880515,1640885052),
('a07535cf2f8a72df33c12ddfa4b53dde','course',2,4,'[]',1640880515,1640885052),
('a07535cf2f8a72df33c12ddfa4b53dde','course',13,10,'[]',1640885044,1640885052),
('a07535cf2f8a72df33c12ddfa4b53dde','course',14,1,NULL,1640880515,1640880515),
('a07535cf2f8a72df33c12ddfa4b53dde','course',15,0,NULL,1640880515,1640880515),
('a07535cf2f8a72df33c12ddfa4b53dde','course',17,2,'[]',1640880515,1640885052),
('a07535cf2f8a72df33c12ddfa4b53dde','course',18,7,'[]',1640880515,1640885063),
('a07535cf2f8a72df33c12ddfa4b53dde','course',20,8,'[]',1640880515,1640885063),
('a07535cf2f8a72df33c12ddfa4b53dde','course',21,9,'[]',1640880515,1640885052),
('a07535cf2f8a72df33c12ddfa4b53dde','course',22,6,'[]',1640880515,1640885061),
('a07535cf2f8a72df33c12ddfa4b53dde','course',26,5,'[]',1640885037,1640885052),
('ec2e364b28357106c0f8c282733dbe56','institute',14,1,NULL,1640880515,1640880515),
('ec2e364b28357106c0f8c282733dbe56','institute',15,0,NULL,1640880515,1640880515),
('ec2e364b28357106c0f8c282733dbe56','institute',25,2,NULL,1640880515,1640880515),
('f02e2b17bc0e99fc885da6ac4c2532dc','institute',14,1,NULL,1640880515,1640880515),
('f02e2b17bc0e99fc885da6ac4c2532dc','institute',15,0,NULL,1640880515,1640880515),
('f02e2b17bc0e99fc885da6ac4c2532dc','institute',25,2,NULL,1640880515,1640880515);
/*!40000 ALTER TABLE `tools_activated` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `user_factorlist`
--

LOCK TABLES `user_factorlist` WRITE;
/*!40000 ALTER TABLE `user_factorlist` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `user_factorlist` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `user_info`
--

LOCK TABLES `user_info` WRITE;
/*!40000 ALTER TABLE `user_info` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `user_info` VALUES
('205f3efb7997a0fc9755da2b535038da','','','','','','','','',0,0,0,0,'','',NULL,1,'',0,'','',NULL),
('6235c46eb9e962866ebdceece739ace5','','','','','','','','',0,0,0,0,'','',NULL,1,'',0,'','',NULL),
('76ed43ef286fb55cf9e41beadb484a9f','','','','','','','','',0,0,0,1698855190,'','',NULL,1,'',0,'','',NULL),
('7e81ec247c151c02ffd479511e24cc03','','','','','','','','',0,0,0,0,'','',NULL,1,'',0,'','',NULL),
('e7a0a84b161f3e8c09b4a0a2e8a58147','','','','','','','','',0,0,0,0,'','',NULL,1,'',0,'','',NULL);
/*!40000 ALTER TABLE `user_info` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `user_inst`
--

LOCK TABLES `user_inst` WRITE;
/*!40000 ALTER TABLE `user_inst` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `user_inst` VALUES
(1,'205f3efb7997a0fc9755da2b535038da','2560f7c7674942a7dce8eeb238e15d93','dozent','','','','',0,0,1,NULL,NULL),
(2,'6235c46eb9e962866ebdceece739ace5','2560f7c7674942a7dce8eeb238e15d93','admin','','','','',0,0,1,NULL,NULL),
(3,'7e81ec247c151c02ffd479511e24cc03','2560f7c7674942a7dce8eeb238e15d93','tutor','','','','',0,0,1,NULL,NULL),
(4,'e7a0a84b161f3e8c09b4a0a2e8a58147','2560f7c7674942a7dce8eeb238e15d93','user','','','','',1,0,1,NULL,NULL);
/*!40000 ALTER TABLE `user_inst` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `user_online`
--

LOCK TABLES `user_online` WRITE;
/*!40000 ALTER TABLE `user_online` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `user_online` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `user_studiengang`
--

LOCK TABLES `user_studiengang` WRITE;
/*!40000 ALTER TABLE `user_studiengang` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `user_studiengang` VALUES
('7e81ec247c151c02ffd479511e24cc03','f981c9b42ca72788a09da4a45794a737',1,'228234544820cdf75db55b42d1ea3ecc',NULL,NULL,NULL),
('e7a0a84b161f3e8c09b4a0a2e8a58147','6b9ac09535885ca55e29dd011e377c0a',2,'228234544820cdf75db55b42d1ea3ecc',NULL,NULL,NULL);
/*!40000 ALTER TABLE `user_studiengang` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `user_token`
--

LOCK TABLES `user_token` WRITE;
/*!40000 ALTER TABLE `user_token` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `user_token` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `user_userdomains`
--

LOCK TABLES `user_userdomains` WRITE;
/*!40000 ALTER TABLE `user_userdomains` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `user_userdomains` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `user_visibility`
--

LOCK TABLES `user_visibility` WRITE;
/*!40000 ALTER TABLE `user_visibility` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `user_visibility` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `user_visibility_settings`
--

LOCK TABLES `user_visibility_settings` WRITE;
/*!40000 ALTER TABLE `user_visibility_settings` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `user_visibility_settings` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `userdomains`
--

LOCK TABLES `userdomains` WRITE;
/*!40000 ALTER TABLE `userdomains` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `userdomains` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `userfilter`
--

LOCK TABLES `userfilter` WRITE;
/*!40000 ALTER TABLE `userfilter` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `userfilter` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `userfilter_fields`
--

LOCK TABLES `userfilter_fields` WRITE;
/*!40000 ALTER TABLE `userfilter_fields` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `userfilter_fields` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `userlimits`
--

LOCK TABLES `userlimits` WRITE;
/*!40000 ALTER TABLE `userlimits` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `userlimits` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `users_tfa`
--

LOCK TABLES `users_tfa` WRITE;
/*!40000 ALTER TABLE `users_tfa` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `users_tfa` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `users_tfa_tokens`
--

LOCK TABLES `users_tfa_tokens` WRITE;
/*!40000 ALTER TABLE `users_tfa_tokens` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `users_tfa_tokens` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `widget_default`
--

LOCK TABLES `widget_default` WRITE;
/*!40000 ALTER TABLE `widget_default` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `widget_default` VALUES
(3,0,3,'user'),
(4,0,0,'user'),
(5,1,0,'user'),
(7,0,2,'user'),
(27,0,1,'user'),
(3,0,3,'autor'),
(4,0,0,'autor'),
(5,1,0,'autor'),
(6,0,4,'autor'),
(7,0,2,'autor'),
(27,0,1,'autor'),
(29,0,6,'autor'),
(30,0,5,'autor'),
(3,0,3,'tutor'),
(4,0,0,'tutor'),
(5,1,0,'tutor'),
(6,0,4,'tutor'),
(7,0,2,'tutor'),
(27,0,1,'tutor'),
(29,0,6,'tutor'),
(30,0,5,'tutor'),
(3,0,3,'dozent'),
(4,0,0,'dozent'),
(5,1,0,'dozent'),
(6,0,4,'dozent'),
(7,0,2,'dozent'),
(27,0,1,'dozent'),
(3,0,3,'admin'),
(4,0,0,'admin'),
(5,1,0,'admin'),
(7,0,2,'admin'),
(27,0,1,'admin'),
(3,0,3,'root'),
(4,0,0,'root'),
(5,1,0,'root'),
(7,0,2,'root'),
(27,0,1,'root');
/*!40000 ALTER TABLE `widget_default` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `widget_user`
--

LOCK TABLES `widget_user` WRITE;
/*!40000 ALTER TABLE `widget_user` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `widget_user` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `wiki_links`
--

LOCK TABLES `wiki_links` WRITE;
/*!40000 ALTER TABLE `wiki_links` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `wiki_links` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `wiki_online_editing_users`
--

LOCK TABLES `wiki_online_editing_users` WRITE;
/*!40000 ALTER TABLE `wiki_online_editing_users` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `wiki_online_editing_users` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `wiki_pages`
--

LOCK TABLES `wiki_pages` WRITE;
/*!40000 ALTER TABLE `wiki_pages` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `wiki_pages` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping data for table `wiki_versions`
--

LOCK TABLES `wiki_versions` WRITE;
/*!40000 ALTER TABLE `wiki_versions` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `wiki_versions` ENABLE KEYS */;
UNLOCK TABLES;
commit;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2025-08-06  9:18:32
