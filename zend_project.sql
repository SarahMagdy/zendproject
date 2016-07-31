-- MySQL dump 10.13  Distrib 5.5.41, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: zend_project
-- ------------------------------------------------------
-- Server version	5.5.41-0ubuntu0.14.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `zend_project`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `zend_project` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `zend_project`;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `image` varchar(30) NOT NULL,
  `name` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Music.jpeg','Music'),(2,'Children.jpg','Children'),(3,'Health.png','Health');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forums`
--

DROP TABLE IF EXISTS `forums`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forums` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `image` varchar(30) DEFAULT NULL,
  `is_locked` enum('0','1') DEFAULT '0',
  `cat_id` int(4) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cat_id` (`cat_id`),
  CONSTRAINT `forums_ibfk_1` FOREIGN KEY (`cat_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forums`
--

LOCK TABLES `forums` WRITE;
/*!40000 ALTER TABLE `forums` DISABLE KEYS */;
INSERT INTO `forums` VALUES (1,'Arabic Songs','Arabic Songs1.jpg','0',1),(2,'English Songs','English Songs1.jpg','0',1),(3,'Girls','Girls2.jpg','0',2),(4,'Boys','Boys2.jpg','0',2),(5,'Cardiologist','Cardiologist3.jpg','0',3),(6,'Plastic Surgeon','Plastic Surgeon3.png','0',3),(7,'Surgeon','Surgeon3.jpg','0',3);
/*!40000 ALTER TABLE `forums` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `replies`
--

DROP TABLE IF EXISTS `replies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `replies` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `body` varchar(500) NOT NULL,
  `image` varchar(30) DEFAULT NULL,
  `user_id` int(4) unsigned NOT NULL,
  `thread_id` int(4) unsigned NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `thread_id` (`thread_id`),
  CONSTRAINT `replies_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `replies_ibfk_2` FOREIGN KEY (`thread_id`) REFERENCES `threads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `replies`
--

LOCK TABLES `replies` WRITE;
/*!40000 ALTER TABLE `replies` DISABLE KEYS */;
INSERT INTO `replies` VALUES (1,'Greate Singer  :)','1810890765qnt.png',2,1,'2015-03-26 22:52:09'),(2,'i like her','554919099toj.png',3,1,'2015-03-26 23:01:28'),(3,'hiiiiiiiii :*',NULL,1,6,'2015-03-26 23:03:03'),(4,'thx ;)','508080786fft.png',1,9,'2015-03-26 23:05:15'),(5,'like :)','979310917byg.png',1,1,'2015-03-26 23:10:06');
/*!40000 ALTER TABLE `replies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sys_status`
--

DROP TABLE IF EXISTS `sys_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sys_status` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `is_closed` enum('0','1') DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sys_status`
--

LOCK TABLES `sys_status` WRITE;
/*!40000 ALTER TABLE `sys_status` DISABLE KEYS */;
INSERT INTO `sys_status` VALUES (1,'0');
/*!40000 ALTER TABLE `sys_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `threads`
--

DROP TABLE IF EXISTS `threads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `threads` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `body` varchar(500) NOT NULL,
  `image` varchar(30) DEFAULT NULL,
  `th_image` varchar(30) DEFAULT 'default.png',
  `is_sticky` enum('0','1') DEFAULT '0',
  `is_locked` enum('0','1') DEFAULT '0',
  `user_id` int(4) unsigned NOT NULL,
  `forum_id` int(4) unsigned NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `forum_id` (`forum_id`),
  CONSTRAINT `threads_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `threads_ibfk_2` FOREIGN KEY (`forum_id`) REFERENCES `forums` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `threads`
--

LOCK TABLES `threads` WRITE;
/*!40000 ALTER TABLE `threads` DISABLE KEYS */;
INSERT INTO `threads` VALUES (1,'Om-Kalthom','All the songs is available ','2120186275tmv.jpg','default.png','1','0',1,1,'2015-03-26 22:44:24'),(2,'Westlife ','songs','352131796wvp.jpg','default.png','0','0',1,2,'2015-03-26 22:54:31'),(3,'3bl el7alem','kare2t l fngan','1495568241nsa.jpg','default.png','0','0',1,1,'2015-03-26 22:55:40'),(4,'1 year old','tell us about your story',NULL,'default.png','0','0',1,3,'2015-03-26 22:59:11'),(5,'2 years','kinder garden','154800488bxd.png','default.png','0','0',1,4,'2015-03-26 23:00:09'),(6,'my baby name is saraa','hii there','1898252887zsh.jpg','default.png','0','0',3,3,'2015-03-26 23:02:25'),(7,'first','hiii',NULL,'default.png','0','0',1,5,'2015-03-26 23:03:22'),(8,'any qustions','i am here to help you','1368214315lqf.png','default.png','0','0',1,6,'2015-03-26 23:03:59'),(9,'free consultant','any time','93426881nfp.jpeg','default.png','0','0',1,7,'2015-03-26 23:05:01');
/*!40000 ALTER TABLE `threads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `email` varchar(40) NOT NULL,
  `password` varchar(32) NOT NULL,
  `country` varchar(30) NOT NULL,
  `signature` varchar(30) NOT NULL,
  `image` varchar(30) NOT NULL,
  `gender` enum('male','female') NOT NULL,
  `is_admin` enum('0','1') DEFAULT '0',
  `is_confirmed` enum('0','1') DEFAULT '0',
  `is_banned` enum('0','1') DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin','admin@admin.com','202cb962ac59075b964b07152d234b70','Egypt','signAdmin.jpg','admin.jpg','male','1','1','0'),(2,'eman','ema_shoot@hotmail.com','202cb962ac59075b964b07152d234b70','Egypt','signeman.jpg','eman.jpeg','female','0','1','0'),(3,'sara','zendproject3@gmail.com','202cb962ac59075b964b07152d234b70','Oman','signsara.jpg','sara.jpg','female','0','1','0');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-03-27  1:13:09
