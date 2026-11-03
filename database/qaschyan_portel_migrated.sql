-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: qaschyan_emp
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `active_sessions`
--

DROP TABLE IF EXISTS `active_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `active_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` varchar(128) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'admin',
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `device_type` varchar(50) DEFAULT NULL,
  `browser` varchar(50) DEFAULT NULL,
  `platform` varchar(50) DEFAULT NULL,
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_id` (`session_id`),
  KEY `idx_user_role` (`user_id`,`role`),
  KEY `idx_last_activity` (`last_activity`)
) ENGINE=InnoDB AUTO_INCREMENT=633 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `active_sessions`
--

LOCK TABLES `active_sessions` WRITE;
/*!40000 ALTER TABLE `active_sessions` DISABLE KEYS */;
INSERT INTO `active_sessions` VALUES (264,'f624mritobb2egpqo0clbijvkc',1,'admin','49.37.223.36','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 154','Android','2026-09-29 11:20:19','2026-09-29 11:18:04'),(283,'896qjn7ec1ivdq06d3gb5pasr4',1,'admin','152.57.90.75','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 154','Android','2026-09-30 03:00:24','2026-09-30 02:59:20'),(291,'hdi5p4r8noc9fod6tk79tqv9tq',1,'admin','49.37.223.36','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','Desktop','Google Chrome 154','Windows','2026-09-30 03:45:50','2026-09-30 03:42:05'),(304,'mlf99577lqc9nl4ofd2b59jtjt',1,'admin','152.57.94.253','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','Desktop','Google Chrome 154','Linux','2026-09-30 06:57:54','2026-09-30 06:56:11'),(312,'8o5mj53v4a6011adhsi4v5j0im',1,'admin','49.37.223.36','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 154','Android','2026-09-30 08:59:23','2026-09-30 08:59:03'),(315,'4kmqf4hcsmk628gu66t2t5fh7o',1,'admin','49.37.223.36','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 154','Android','2026-09-30 18:28:28','2026-09-30 18:23:06'),(332,'htub40skas9ln4vu7nnd1ioi3n',1,'admin','49.37.223.36','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 154','Android','2026-09-30 18:29:41','2026-09-30 18:29:40'),(334,'9pft52k1rhoor3hjqimvshhpgb',1,'admin','49.37.223.36','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 154','Android','2026-10-01 11:13:58','2026-10-01 11:12:28'),(339,'umhtu5rlujhesc2ccm077rcolh',1,'admin','152.57.93.149','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 154','Android','2026-10-03 04:08:10','2026-10-03 03:55:09'),(355,'l90a1vkvvketkplhdep5f1lpee',1,'admin','152.57.93.149','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 154','Android','2026-10-03 05:28:44','2026-10-03 05:28:31'),(358,'2kvetg7r92k617cpgee5c5kg1n',1,'admin','49.37.223.36','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 154','Android','2026-10-05 17:43:39','2026-10-05 17:43:25'),(361,'tgo4sd65i1aq9k95ck57hir4i2',1,'admin','152.57.87.103','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 154','Android','2026-10-07 04:44:08','2026-10-07 04:29:32'),(373,'8pl9hauj2lcuirvffetvet8jtl',1,'admin','157.51.115.64','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 154','Android','2026-10-08 03:00:07','2026-10-08 02:59:52'),(377,'dlsr1fvqmd61a4v5fom4rikmks',1,'admin','49.37.221.102','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 154','Android','2026-10-08 03:54:25','2026-10-08 03:54:00'),(382,'6g82ep1n3n1uhp70kj45ljdcla',1,'admin','49.37.221.102','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 154','Android','2026-10-08 16:35:14','2026-10-08 16:34:00'),(386,'u8p5hlesic41491gkvgejephht',1,'admin','157.51.134.225','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 154','Android','2026-10-09 02:50:15','2026-10-09 02:50:07'),(388,'op22p4ml7ccc4p5ntk6rb5hgrv',1,'admin','49.37.221.102','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','Desktop','Google Chrome 154','Windows','2026-10-09 09:11:16','2026-10-09 09:08:02'),(398,'ef1uhf9uehk46kuln6j4n5sqpi',1,'admin','152.57.89.195','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 154','Android','2026-10-09 09:25:26','2026-10-09 09:25:02'),(401,'u1iumh1oe76vkbcilqc52au7nb',1,'admin','49.37.219.248','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 154','Android','2026-10-10 05:04:24','2026-10-10 05:03:27'),(407,'eumsan0phquoup0vugd11cqk4h',1,'admin','157.51.148.66','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 154','Android','2026-10-12 01:51:39','2026-10-12 01:51:26'),(410,'fpt79eej6kelo9fn784j6trhiq',1,'admin','157.51.149.28','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 154','Android','2026-10-12 03:05:30','2026-10-12 03:05:30'),(411,'im8btt8rdkjke3a80rv33nepl4',1,'admin','157.51.149.28','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 154','Android','2026-10-12 03:32:26','2026-10-12 03:32:16'),(415,'kju5ctest6eb9roqnd669eplfd',1,'admin','49.37.219.248','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 154','Android','2026-10-12 03:55:05','2026-10-12 03:54:13'),(420,'b7hon86tk9kei12lo7rh1cco79',1,'admin','49.37.219.248','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 154','Android','2026-10-12 05:24:15','2026-10-12 05:19:43'),(428,'o7vqn9271usu0mfshrmi9v3bi9',1,'admin','49.37.219.248','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 154','Android','2026-10-12 06:28:36','2026-10-12 06:28:28'),(432,'3esshvfnut5is8crjqt8hefmue',1,'admin','157.51.122.18','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 154','Android','2026-10-13 03:46:28','2026-10-13 03:40:05'),(438,'oqlpj0vjfuseac9mua6q0l9bjg',1,'admin','157.51.122.18','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 154','Android','2026-10-13 03:51:44','2026-10-13 03:51:35'),(442,'uoklbtt79frsl6nk70mpmp2log',1,'admin','49.37.223.96','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','Desktop','Google Chrome 154','Windows','2026-10-14 05:32:33','2026-10-14 05:19:25'),(485,'ufp8ebqnb716j4n362kpbbsnga',1,'admin','49.37.223.96','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','Desktop','Google Chrome 155','Windows','2026-10-15 03:36:00','2026-10-15 03:36:00'),(486,'aadvrqkda4e0t98gunjfa070jd',1,'admin','49.37.223.96','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 155','Android','2026-10-15 03:51:27','2026-10-15 03:51:06'),(489,'388strgb7uuep87vvbfvsrq2i2',1,'admin','49.37.223.96','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','Desktop','Google Chrome 155','Windows','2026-10-15 06:17:01','2026-10-15 06:17:01'),(490,'c1gqi6gnuhq9tapufrmr3qpmoi',1,'admin','49.37.223.96','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 155','Android','2026-10-15 11:46:59','2026-10-15 11:46:22'),(493,'dismh8uccf46qi7l17mgn44ou2',1,'admin','49.37.217.70','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 155','Android','2026-10-16 14:48:15','2026-10-16 14:47:05'),(503,'2p5osmd2ghjdlkd8i7rl4gq5ms',1,'admin','49.37.219.40','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 155','Android','2026-10-17 06:52:12','2026-10-17 06:51:53'),(507,'mh34jnndf2674308v0n634gdg5',1,'admin','157.51.40.128','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 155','Android','2026-10-19 14:40:57','2026-10-19 14:40:54'),(509,'jegtkgaanqqt6nsfrn27s8lomf',1,'admin','157.51.40.128','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 155','Android','2026-10-19 14:41:12','2026-10-19 14:41:12'),(510,'v8vq1ubaq36ot6rpajhghi61qu',1,'admin','49.37.221.186','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 155','Android','2026-10-21 04:11:30','2026-10-21 04:11:00'),(517,'8v3men8a3fpasbdpf9n0m025n8',1,'admin','49.37.221.186','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','Desktop','Google Chrome 155','Windows','2026-10-22 08:02:22','2026-10-22 07:57:54'),(520,'ovnaagjh3nvoss8uk9pbjhlt85',1,'admin','49.37.221.186','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','Desktop','Google Chrome 155','Windows','2026-10-23 06:39:50','2026-10-23 06:33:33'),(527,'oubdld0kkhk8dh36mrljdi3rhh',1,'admin','152.57.95.131','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 156','Android','2026-10-23 19:43:04','2026-10-23 19:41:50'),(535,'g0lqud50se4ib4p97e0c87rd3r',1,'admin','152.57.95.131','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 156','Android','2026-10-23 19:43:14','2026-10-23 19:43:14'),(536,'g7ie7jrmch68p18mikv8kiobhc',1,'admin','49.37.221.186','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 156','Android','2026-10-24 04:34:57','2026-10-24 04:34:57'),(537,'7uup8c3bfshrpuichbbuvsvbhp',1,'admin','49.37.221.186','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 156','Android','2026-10-24 04:35:57','2026-10-24 04:35:08'),(549,'5sstjm1p3el4bpjvqt11ejfroj',1,'admin','49.37.223.204','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 156','Android','2026-10-25 10:21:55','2026-10-25 10:21:29'),(554,'epttakum3he0oc8158cieeaaqk',1,'admin','49.37.223.204','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 156','Android','2026-10-26 03:44:54','2026-10-26 03:44:08'),(562,'l2462910t9k7t80urrh8ksd8gj',1,'admin','49.37.223.204','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 156','Android','2026-10-26 05:13:26','2026-10-26 05:13:26'),(563,'sqjt6osp3n3p7a3ust9kgv0ll2',1,'admin','152.57.80.241','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 156','Android','2026-10-27 06:13:32','2026-10-27 06:11:32'),(572,'814pq1rbur48i8sd1r6fnr4lbc',1,'admin','49.37.221.14','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','Desktop','Google Chrome 155','Windows','2026-10-27 08:48:17','2026-10-27 08:30:56'),(581,'vg7f8lcs3npmqgnmr076ebsqrj',1,'admin','49.37.217.154','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 156','Android','2026-10-28 07:04:23','2026-10-28 06:58:26'),(595,'jrperdvdrmgig1jks0di5457bl',1,'admin','49.37.217.154','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 156','Android','2026-10-28 09:00:08','2026-10-28 08:58:23'),(598,'qdcbn46u38o760llmqjhnamf87',1,'admin','49.37.217.154','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 156','Android','2026-10-28 09:07:19','2026-10-28 09:05:36'),(605,'fgvos6fpvotb6uhia0i1nia1ep',1,'admin','49.37.217.154','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 156','Android','2026-10-28 12:08:20','2026-10-28 12:06:47'),(611,'93vv39qfq044amn1m17p117p08',1,'admin','157.51.119.3','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 156','Android','2026-10-29 03:37:17','2026-10-29 03:37:04'),(617,'7qkr66a38crbv0v8jv2aniu9mo',1,'admin','157.51.116.212','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 156','Android','2026-10-29 04:09:18','2026-10-29 04:09:18'),(618,'54jqd9gppiv5raijjm9enkiv9h',1,'admin','49.37.217.70','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 156','Android','2026-10-29 05:00:33','2026-10-29 05:00:31'),(621,'bal72qth2iu56cjti8vjani6et',1,'admin','49.37.217.70','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 156','Android','2026-10-30 03:51:42','2026-10-30 03:51:10'),(627,'vuorfg92i8948vl4h92q3t1dkh',1,'admin','152.57.88.213','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Mobile Safari/537.36','Mobile','Google Chrome 156','Android','2026-11-02 04:27:51','2026-11-02 04:27:47'),(628,'s1j071g75v7ucogp08v5vttebn',1,'admin','127.0.0.1','','Desktop','Web Browser','Unknown OS','2026-11-03 08:36:25','2026-11-03 08:36:25'),(629,'rfqo88qdrthk8ejm2k52d0af68',1,'admin','127.0.0.1','','Desktop','Web Browser','Unknown OS','2026-11-03 08:39:32','2026-11-03 08:39:32'),(630,'gqtknmjthp042fq842bob8ngek',1,'admin','127.0.0.1','','Desktop','Web Browser','Unknown OS','2026-11-03 08:39:50','2026-11-03 08:39:50'),(631,'kmabfhmnfhdr1lqskbjt6nhn3g',1,'admin','127.0.0.1','','Desktop','Web Browser','Unknown OS','2026-11-03 08:40:13','2026-11-03 08:40:13'),(632,'u4q7j8tfcr5co100n8p22jlulj',1,'admin','127.0.0.1','','Desktop','Web Browser','Unknown OS','2026-11-03 08:44:34','2026-11-03 08:44:34');
/*!40000 ALTER TABLE `active_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin`
--

LOCK TABLES `admin` WRITE;
/*!40000 ALTER TABLE `admin` DISABLE KEYS */;
INSERT INTO `admin` VALUES (1,'ca_cloudaspire','cloudaspire2025@gmail.com','$2y$10$tUbx1dUk1Y8zP.kDPaKUne62/2TkYWtvSZ7HXU4ipxeA.bS2nU7GC','2026-06-28 05:07:38'),(2,'admin','admin@cloudaspire.test','$2y$10$OONAlF8hOOv/ux/SzP43iuDQjoq.NXVBmE1D8Dninc1mXfzlB3tvi','2026-10-28 09:24:10');
/*!40000 ALTER TABLE `admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_audit_logs`
--

DROP TABLE IF EXISTS `admin_audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_audit_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) DEFAULT NULL,
  `admin_username` varchar(100) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `target_type` varchar(50) DEFAULT NULL,
  `target_id` int(11) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_admin_id` (`admin_id`),
  KEY `idx_action` (`action`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=206 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_audit_logs`
--

LOCK TABLES `admin_audit_logs` WRITE;
/*!40000 ALTER TABLE `admin_audit_logs` DISABLE KEYS */;
INSERT INTO `admin_audit_logs` VALUES (2,2,'admin','admin_login_success','admin',2,'{\"username\":\"admin\",\"role\":\"super_admin\",\"ip\":\"::1\"}','::1','','2026-10-28 09:25:09'),(3,NULL,'System','employee_login_success','employees',22,'{\"email\":\"rbac_test_worker@cloudaspire.test\",\"role\":\"employee\",\"ip\":\"::1\"}','::1','','2026-10-28 09:25:15'),(4,NULL,'System','employee_login_success','employees',22,'{\"email\":\"rbac_test_worker@cloudaspire.test\",\"role\":\"employee\",\"ip\":\"::1\"}','::1','','2026-10-28 09:25:20'),(5,NULL,'System','employee_login_success','employees',22,'{\"email\":\"rbac_test_worker@cloudaspire.test\",\"role\":\"hr_manager\",\"ip\":\"::1\"}','::1','','2026-10-28 09:25:25'),(6,NULL,'System','employee_login_success','employees',23,'{\"email\":\"ui_fixes_test_emp@cloudaspire.test\",\"role\":\"employee\",\"ip\":\"::1\"}','::1','','2026-10-28 09:25:42'),(7,NULL,'System','employee_login_success','employees',24,'{\"email\":\"dash_restore_test@cloudaspire.test\",\"role\":\"employee\",\"ip\":\"::1\"}','::1','','2026-10-28 09:25:54'),(8,NULL,'System','employee_logout','employees',14,'{\"email\":\"priya30shanmuga@gmail.com\",\"name\":\"Shanmuga Priya\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-28 09:31:38'),(9,NULL,'System','employee_login_success','employees',12,'{\"email\":\"tamilaninstamedia1@gmail.com\",\"role\":\"employee\",\"ip\":\"::1\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-28 09:31:48'),(10,NULL,'System','employee_logout','employees',12,'{\"email\":\"tamilaninstamedia1@gmail.com\",\"name\":\"Prem Kumar M\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-28 09:32:04'),(11,NULL,'System','login_failed','users',NULL,'{\"identity\":\"cloudaspire2025@gmail.com\",\"ip\":\"::1\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-28 09:32:11'),(12,NULL,'System','login_failed','users',NULL,'{\"identity\":\"cloudaspire2026@gmail.com\",\"ip\":\"::1\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-28 09:32:28'),(13,NULL,'System','login_failed','users',NULL,'{\"identity\":\"cloudaspire2025@gmail.com\",\"ip\":\"::1\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-28 09:34:36'),(14,NULL,'System','login_failed','users',NULL,'{\"identity\":\"cloudaspire2025@gmail.com\",\"ip\":\"::1\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-28 09:35:04'),(15,1,'ca_cloudaspire','admin_login_success','admin',1,'{\"username\":\"ca_cloudaspire\",\"role\":\"super_admin\",\"ip\":\"::1\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-28 09:36:56'),(16,1,'ca_cloudaspire','update_employee_role','employee',12,'{\"employee_name\":\"Prem Kumar M\",\"previous_role_id\":null,\"new_role_id\":2,\"new_role_name\":\"HR Operations Manager\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-28 09:37:21'),(17,1,'ca_cloudaspire','update_employee_role','employee',13,'{\"employee_name\":\"Karthick Raja\",\"previous_role_id\":null,\"new_role_id\":2,\"new_role_name\":\"HR Operations Manager\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-28 09:37:23'),(18,1,'ca_cloudaspire','update_office_location','site_settings',3,'{\"office_name\":\"Cloud Aspire Solutions\",\"latitude\":11.9530984,\"longitude\":79.8134584,\"radius\":100,\"location_enabled\":1}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-28 09:38:14'),(19,1,'ca_cloudaspire','logout','admin',1,'{\"username\":\"ca_cloudaspire\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-28 09:38:28'),(20,NULL,'System','login_failed','users',NULL,'{\"identity\":\"priya30shanmuga@gmail.com\",\"ip\":\"::1\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-28 09:38:34'),(21,NULL,'System','login_failed','users',NULL,'{\"identity\":\"priya30shanmuga@gmail.com\",\"ip\":\"::1\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-28 09:38:37'),(22,NULL,'System','login_failed','users',NULL,'{\"identity\":\"priya30shanmuga@gmail.com\",\"ip\":\"::1\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-28 09:38:50'),(23,NULL,'System','employee_login_success','employees',12,'{\"email\":\"tamilaninstamedia1@gmail.com\",\"role\":\"hr_manager\",\"ip\":\"::1\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-28 09:38:56'),(24,12,'Prem Kumar M','logout','admin',12,'{\"username\":\"Prem Kumar M\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-28 09:52:59'),(25,NULL,'System','employee_login_success','employees',12,'{\"email\":\"tamilaninstamedia1@gmail.com\",\"role\":\"hr_manager\",\"ip\":\"::1\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-28 09:53:07'),(26,12,'Prem Kumar M','logout','admin',12,'{\"username\":\"Prem Kumar M\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-28 09:53:20'),(27,NULL,'System','login_failed','users',NULL,'{\"identity\":\"admin@cloudaspire.com\",\"ip\":\"::1\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-28 09:53:36'),(28,NULL,'System','login_failed','users',NULL,'{\"identity\":\"cloudaspire2025@gmail.com\",\"ip\":\"::1\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-28 09:53:58'),(29,NULL,'System','login_failed','users',NULL,'{\"identity\":\"cloudaspire2025@gmail.com\",\"ip\":\"::1\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-28 09:54:15'),(30,2,'admin','admin_login_success','admin',2,'{\"username\":\"admin\",\"role\":\"super_admin\",\"ip\":\"::1\"}','::1','','2026-10-28 09:54:19'),(31,NULL,'System','employee_login_success','employees',25,'{\"email\":\"rbac_test_worker@cloudaspire.test\",\"role\":\"employee\",\"ip\":\"::1\"}','::1','','2026-10-28 09:54:25'),(32,NULL,'System','employee_login_success','employees',25,'{\"email\":\"rbac_test_worker@cloudaspire.test\",\"role\":\"employee\",\"ip\":\"::1\"}','::1','','2026-10-28 09:54:30'),(33,NULL,'System','employee_login_success','employees',25,'{\"email\":\"rbac_test_worker@cloudaspire.test\",\"role\":\"hr_manager\",\"ip\":\"::1\"}','::1','','2026-10-28 09:54:35'),(34,1,'ca_cloudaspire','admin_login_success','admin',1,'{\"username\":\"ca_cloudaspire\",\"role\":\"super_admin\",\"ip\":\"::1\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-28 09:54:39'),(35,1,'ca_cloudaspire','logout','admin',1,'{\"username\":\"ca_cloudaspire\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-28 09:57:13'),(36,NULL,'System','employee_login_success','employees',12,'{\"email\":\"tamilaninstamedia1@gmail.com\",\"role\":\"hr_manager\",\"ip\":\"::1\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-28 09:57:19'),(37,NULL,'Prem Kumar M','employee_logout','employees',12,'{\"email\":\"tamilaninstamedia1@gmail.com\",\"name\":\"Prem Kumar M\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-28 09:57:49'),(38,1,'ca_cloudaspire','admin_login_success','admin',1,'{\"username\":\"ca_cloudaspire\",\"role\":\"super_admin\",\"ip\":\"::1\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-28 09:57:58'),(39,2,'admin','admin_login_success','admin',2,'{\"username\":\"admin\",\"role\":\"super_admin\",\"ip\":\"::1\"}','::1','','2026-10-28 10:20:55'),(40,NULL,'System','employee_login_success','employees',26,'{\"email\":\"rbac_test_worker@cloudaspire.test\",\"role\":\"employee\",\"ip\":\"::1\"}','::1','','2026-10-28 10:21:00'),(41,NULL,'System','employee_login_success','employees',26,'{\"email\":\"rbac_test_worker@cloudaspire.test\",\"role\":\"employee\",\"ip\":\"::1\"}','::1','','2026-10-28 10:21:05'),(42,NULL,'System','employee_login_success','employees',26,'{\"email\":\"rbac_test_worker@cloudaspire.test\",\"role\":\"hr_manager\",\"ip\":\"::1\"}','::1','','2026-10-28 10:21:10'),(43,2,'admin','admin_login_success','admin',2,'{\"username\":\"admin\",\"role\":\"super_admin\",\"ip\":\"::1\"}','::1','','2026-10-28 10:23:00'),(44,NULL,'System','employee_login_success','employees',27,'{\"email\":\"rbac_test_worker@cloudaspire.test\",\"role\":\"employee\",\"ip\":\"::1\"}','::1','','2026-10-28 10:23:05'),(45,NULL,'System','employee_login_success','employees',27,'{\"email\":\"rbac_test_worker@cloudaspire.test\",\"role\":\"employee\",\"ip\":\"::1\"}','::1','','2026-10-28 10:23:10'),(46,NULL,'System','employee_login_success','employees',27,'{\"email\":\"rbac_test_worker@cloudaspire.test\",\"role\":\"hr_manager\",\"ip\":\"::1\"}','::1','','2026-10-28 10:23:15'),(47,1,'ca_cloudaspire','update_employee','employee',21,'{\"code\":\"EMP-009\",\"name\":\"Anantha Lisha M\",\"email\":\"vijaylisha210@gmail.com\",\"role_id\":5,\"shift_id\":null,\"status\":\"active\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-28 10:24:40'),(48,1,'ca_cloudaspire','logout','admin',1,'{\"username\":\"ca_cloudaspire\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-28 10:27:16'),(49,1,'ca_cloudaspire','admin_login_success','admin',1,'{\"username\":\"ca_cloudaspire\",\"role\":\"super_admin\",\"ip\":\"::1\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-28 10:27:21'),(50,1,'ca_cloudaspire','logout','admin',1,'{\"username\":\"ca_cloudaspire\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-28 10:27:46'),(51,NULL,'System','employee_login_success','employees',12,'{\"email\":\"tamilaninstamedia1@gmail.com\",\"role\":\"hr_manager\",\"ip\":\"::1\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-28 10:27:58'),(52,2,'admin','admin_login_success','admin',2,'{\"username\":\"admin\",\"role\":\"super_admin\",\"ip\":\"::1\"}','::1','','2026-10-28 10:32:16'),(53,2,'admin','create_employee','employee',28,'{\"code\":\"EMP-010\",\"name\":\"AdminUser Test\",\"email\":\"test_role1_admin@cloudaspire.test\",\"role_id\":1,\"shift_id\":null,\"status\":\"active\"}','::1','','2026-10-28 10:32:21'),(54,2,'admin','create_employee','employee',29,'{\"code\":\"EMP-011\",\"name\":\"HrUser Test\",\"email\":\"test_role2_hr@cloudaspire.test\",\"role_id\":2,\"shift_id\":null,\"status\":\"active\"}','::1','','2026-10-28 10:32:22'),(55,2,'admin','create_employee','employee',30,'{\"code\":\"EMP-012\",\"name\":\"MgrUser Test\",\"email\":\"test_role3_mgr@cloudaspire.test\",\"role_id\":3,\"shift_id\":null,\"status\":\"active\"}','::1','','2026-10-28 10:32:22'),(56,2,'admin','create_employee','employee',31,'{\"code\":\"EMP-013\",\"name\":\"SupUser Test\",\"email\":\"test_role4_sup@cloudaspire.test\",\"role_id\":4,\"shift_id\":null,\"status\":\"active\"}','::1','','2026-10-28 10:32:22'),(57,2,'admin','create_employee','employee',32,'{\"code\":\"EMP-014\",\"name\":\"StaffUser Test\",\"email\":\"test_role5_stf@cloudaspire.test\",\"role_id\":5,\"shift_id\":null,\"status\":\"active\"}','::1','','2026-10-28 10:32:22'),(58,2,'admin','create_employee','employee',33,'{\"code\":\"EMP-015\",\"name\":\"MbrUser Test\",\"email\":\"test_role6_mbr@cloudaspire.test\",\"role_id\":6,\"shift_id\":null,\"status\":\"active\"}','::1','','2026-10-28 10:32:22'),(59,2,'admin','update_employee_role','employee',32,'{\"employee_name\":\"StaffUser Test\",\"previous_role_id\":5,\"new_role_id\":4,\"new_role_name\":\"Department Supervisor\"}','::1','','2026-10-28 10:32:22'),(60,2,'admin','update_employee','employee',32,'{\"code\":\"EMP-014\",\"name\":\"StaffUser Promoted\",\"email\":\"test_role5_stf@cloudaspire.test\",\"role_id\":3,\"shift_id\":null,\"status\":\"active\"}','::1','','2026-10-28 10:32:22'),(61,NULL,'System','employee_login_success','employees',32,'{\"email\":\"test_role5_stf@cloudaspire.test\",\"role\":\"manager\",\"ip\":\"::1\"}','::1','','2026-10-28 10:32:22'),(62,NULL,'System','employee_login_success','employees',29,'{\"email\":\"test_role2_hr@cloudaspire.test\",\"role\":\"hr_manager\",\"ip\":\"::1\"}','::1','','2026-10-28 10:32:28'),(63,NULL,'System','employee_login_success','employees',30,'{\"email\":\"test_role3_mgr@cloudaspire.test\",\"role\":\"manager\",\"ip\":\"::1\"}','::1','','2026-10-28 10:32:35'),(64,NULL,'System','employee_login_success','employees',31,'{\"email\":\"test_role4_sup@cloudaspire.test\",\"role\":\"supervisor\",\"ip\":\"::1\"}','::1','','2026-10-28 10:32:40'),(65,NULL,'System','employee_login_success','employees',32,'{\"email\":\"test_role5_stf@cloudaspire.test\",\"role\":\"staff\",\"ip\":\"::1\"}','::1','','2026-10-28 10:32:45'),(66,NULL,'System','employee_login_success','employees',33,'{\"email\":\"test_role6_mbr@cloudaspire.test\",\"role\":\"member\",\"ip\":\"::1\"}','::1','','2026-10-28 10:32:51'),(67,2,'admin','admin_login_success','admin',2,'{\"username\":\"admin\",\"role\":\"super_admin\",\"ip\":\"::1\"}','::1','','2026-10-28 10:34:09'),(68,2,'admin','create_employee','employee',34,'{\"code\":\"EMP-010\",\"name\":\"AdminUser Test\",\"email\":\"test_role1_admin@cloudaspire.test\",\"role_id\":1,\"shift_id\":null,\"status\":\"active\"}','::1','','2026-10-28 10:34:14'),(69,2,'admin','create_employee','employee',35,'{\"code\":\"EMP-011\",\"name\":\"HrUser Test\",\"email\":\"test_role2_hr@cloudaspire.test\",\"role_id\":2,\"shift_id\":null,\"status\":\"active\"}','::1','','2026-10-28 10:34:14'),(70,2,'admin','create_employee','employee',36,'{\"code\":\"EMP-012\",\"name\":\"MgrUser Test\",\"email\":\"test_role3_mgr@cloudaspire.test\",\"role_id\":3,\"shift_id\":null,\"status\":\"active\"}','::1','','2026-10-28 10:34:14'),(71,2,'admin','create_employee','employee',37,'{\"code\":\"EMP-013\",\"name\":\"SupUser Test\",\"email\":\"test_role4_sup@cloudaspire.test\",\"role_id\":4,\"shift_id\":null,\"status\":\"active\"}','::1','','2026-10-28 10:34:14'),(72,2,'admin','create_employee','employee',38,'{\"code\":\"EMP-014\",\"name\":\"StaffUser Test\",\"email\":\"test_role5_stf@cloudaspire.test\",\"role_id\":5,\"shift_id\":null,\"status\":\"active\"}','::1','','2026-10-28 10:34:14'),(73,2,'admin','create_employee','employee',39,'{\"code\":\"EMP-015\",\"name\":\"MbrUser Test\",\"email\":\"test_role6_mbr@cloudaspire.test\",\"role_id\":6,\"shift_id\":null,\"status\":\"active\"}','::1','','2026-10-28 10:34:15'),(74,2,'admin','update_employee_role','employee',38,'{\"employee_name\":\"StaffUser Test\",\"previous_role_id\":5,\"new_role_id\":4,\"new_role_name\":\"Department Supervisor\"}','::1','','2026-10-28 10:34:15'),(75,2,'admin','update_employee','employee',38,'{\"code\":\"EMP-014\",\"name\":\"StaffUser Promoted\",\"email\":\"test_role5_stf@cloudaspire.test\",\"role_id\":3,\"shift_id\":null,\"status\":\"active\"}','::1','','2026-10-28 10:34:15'),(76,NULL,'System','employee_login_success','employees',38,'{\"email\":\"test_role5_stf@cloudaspire.test\",\"role\":\"manager\",\"ip\":\"::1\"}','::1','','2026-10-28 10:34:15'),(77,NULL,'System','employee_login_success','employees',35,'{\"email\":\"test_role2_hr@cloudaspire.test\",\"role\":\"hr_manager\",\"ip\":\"::1\"}','::1','','2026-10-28 10:34:20'),(78,NULL,'System','employee_login_success','employees',36,'{\"email\":\"test_role3_mgr@cloudaspire.test\",\"role\":\"manager\",\"ip\":\"::1\"}','::1','','2026-10-28 10:34:26'),(79,NULL,'System','employee_login_success','employees',37,'{\"email\":\"test_role4_sup@cloudaspire.test\",\"role\":\"supervisor\",\"ip\":\"::1\"}','::1','','2026-10-28 10:34:31'),(80,NULL,'System','employee_login_success','employees',38,'{\"email\":\"test_role5_stf@cloudaspire.test\",\"role\":\"staff\",\"ip\":\"::1\"}','::1','','2026-10-28 10:34:39'),(81,NULL,'System','employee_login_success','employees',39,'{\"email\":\"test_role6_mbr@cloudaspire.test\",\"role\":\"member\",\"ip\":\"::1\"}','::1','','2026-10-28 10:34:44'),(82,2,'admin','admin_login_success','admin',2,'{\"username\":\"admin\",\"role\":\"super_admin\",\"ip\":\"::1\"}','::1','','2026-10-28 10:34:59'),(83,NULL,'System','employee_login_success','employees',40,'{\"email\":\"rbac_test_worker@cloudaspire.test\",\"role\":\"employee\",\"ip\":\"::1\"}','::1','','2026-10-28 10:35:04'),(84,NULL,'System','employee_login_success','employees',40,'{\"email\":\"rbac_test_worker@cloudaspire.test\",\"role\":\"employee\",\"ip\":\"::1\"}','::1','','2026-10-28 10:35:09'),(85,NULL,'System','employee_login_success','employees',40,'{\"email\":\"rbac_test_worker@cloudaspire.test\",\"role\":\"hr_manager\",\"ip\":\"::1\"}','::1','','2026-10-28 10:35:14'),(86,2,'admin','admin_login_success','admin',2,'{\"username\":\"admin\",\"role\":\"super_admin\",\"ip\":\"::1\"}','::1','','2026-10-28 11:23:46'),(87,2,'admin','create_employee','employee',42,'{\"code\":\"EMP-010\",\"name\":\"AdminUser Test\",\"email\":\"test_role1_admin@cloudaspire.test\",\"role_id\":1,\"shift_id\":null,\"status\":\"active\"}','::1','','2026-10-28 11:23:51'),(88,2,'admin','create_employee','employee',43,'{\"code\":\"EMP-011\",\"name\":\"HrUser Test\",\"email\":\"test_role2_hr@cloudaspire.test\",\"role_id\":2,\"shift_id\":null,\"status\":\"active\"}','::1','','2026-10-28 11:23:51'),(89,2,'admin','create_employee','employee',44,'{\"code\":\"EMP-012\",\"name\":\"MgrUser Test\",\"email\":\"test_role3_mgr@cloudaspire.test\",\"role_id\":3,\"shift_id\":null,\"status\":\"active\"}','::1','','2026-10-28 11:23:51'),(90,2,'admin','create_employee','employee',45,'{\"code\":\"EMP-013\",\"name\":\"SupUser Test\",\"email\":\"test_role4_sup@cloudaspire.test\",\"role_id\":4,\"shift_id\":null,\"status\":\"active\"}','::1','','2026-10-28 11:23:51'),(91,2,'admin','create_employee','employee',46,'{\"code\":\"EMP-014\",\"name\":\"StaffUser Test\",\"email\":\"test_role5_stf@cloudaspire.test\",\"role_id\":5,\"shift_id\":null,\"status\":\"active\"}','::1','','2026-10-28 11:23:52'),(92,2,'admin','create_employee','employee',47,'{\"code\":\"EMP-015\",\"name\":\"MbrUser Test\",\"email\":\"test_role6_mbr@cloudaspire.test\",\"role_id\":6,\"shift_id\":null,\"status\":\"active\"}','::1','','2026-10-28 11:23:52'),(93,2,'admin','update_employee_role','employee',46,'{\"employee_name\":\"StaffUser Test\",\"previous_role_id\":5,\"new_role_id\":4,\"new_role_name\":\"Department Supervisor\"}','::1','','2026-10-28 11:23:52'),(94,2,'admin','update_employee','employee',46,'{\"code\":\"EMP-014\",\"name\":\"StaffUser Promoted\",\"email\":\"test_role5_stf@cloudaspire.test\",\"role_id\":3,\"shift_id\":null,\"status\":\"active\"}','::1','','2026-10-28 11:23:52'),(95,NULL,'System','employee_login_success','employees',46,'{\"email\":\"test_role5_stf@cloudaspire.test\",\"role\":\"manager\",\"ip\":\"::1\"}','::1','','2026-10-28 11:23:52'),(96,NULL,'System','employee_login_success','employees',43,'{\"email\":\"test_role2_hr@cloudaspire.test\",\"role\":\"hr_manager\",\"ip\":\"::1\"}','::1','','2026-10-28 11:23:57'),(97,NULL,'System','employee_login_success','employees',44,'{\"email\":\"test_role3_mgr@cloudaspire.test\",\"role\":\"manager\",\"ip\":\"::1\"}','::1','','2026-10-28 11:24:03'),(98,NULL,'System','employee_login_success','employees',45,'{\"email\":\"test_role4_sup@cloudaspire.test\",\"role\":\"supervisor\",\"ip\":\"::1\"}','::1','','2026-10-28 11:24:09'),(99,NULL,'System','employee_login_success','employees',46,'{\"email\":\"test_role5_stf@cloudaspire.test\",\"role\":\"staff\",\"ip\":\"::1\"}','::1','','2026-10-28 11:24:16'),(100,NULL,'System','employee_login_success','employees',47,'{\"email\":\"test_role6_mbr@cloudaspire.test\",\"role\":\"member\",\"ip\":\"::1\"}','::1','','2026-10-28 11:24:22'),(101,NULL,'Prem Kumar M','employee_logout','employees',12,'{\"email\":\"tamilaninstamedia1@gmail.com\",\"name\":\"Prem Kumar M\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-28 11:24:27'),(102,2,'admin','admin_login_success','admin',2,'{\"username\":\"admin\",\"role\":\"super_admin\",\"ip\":\"::1\"}','::1','','2026-10-28 11:24:41'),(103,NULL,'System','employee_login_success','employees',48,'{\"email\":\"rbac_test_worker@cloudaspire.test\",\"role\":\"employee\",\"ip\":\"::1\"}','::1','','2026-10-28 11:24:46'),(104,NULL,'System','employee_login_success','employees',48,'{\"email\":\"rbac_test_worker@cloudaspire.test\",\"role\":\"employee\",\"ip\":\"::1\"}','::1','','2026-10-28 11:24:51'),(105,NULL,'System','employee_login_success','employees',48,'{\"email\":\"rbac_test_worker@cloudaspire.test\",\"role\":\"hr_manager\",\"ip\":\"::1\"}','::1','','2026-10-28 11:24:56'),(106,1,'ca_cloudaspire','admin_login_success','admin',1,'{\"username\":\"ca_cloudaspire\",\"role\":\"super_admin\",\"ip\":\"::1\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-28 11:25:42'),(107,2,'admin','admin_login_success','admin',2,'{\"username\":\"admin\",\"role\":\"super_admin\",\"ip\":\"::1\"}','::1','','2026-10-28 11:28:09'),(108,2,'admin','edit_role','roles',5,'{\"role_name\":\"Staff Regular\",\"permissions_count\":3}','::1','','2026-10-28 11:28:14'),(109,NULL,'System','employee_login_success','employees',70,'{\"email\":\"t_hr@testsuite-portal.com\",\"role\":\"hr_manager\",\"ip\":\"::1\"}','::1','','2026-10-28 11:28:14'),(110,70,'Test HR RoleUser','edit_role','roles',4,'{\"role_name\":\"Department Supervisor\",\"permissions_count\":0}','::1','','2026-10-28 11:28:19'),(111,NULL,'System','employee_login_success','employees',72,'{\"email\":\"t_sup@testsuite-portal.com\",\"role\":\"supervisor\",\"ip\":\"::1\"}','::1','','2026-10-28 11:28:19'),(112,NULL,'System','employee_login_success','employees',73,'{\"email\":\"t_stf@testsuite-portal.com\",\"role\":\"staff\",\"ip\":\"::1\"}','::1','','2026-10-28 11:28:24'),(113,2,'admin','admin_login_success','admin',2,'{\"username\":\"admin\",\"role\":\"super_admin\",\"ip\":\"::1\"}','::1','','2026-10-28 11:28:51'),(114,2,'admin','edit_role','roles',5,'{\"role_name\":\"Staff Regular\",\"permissions_count\":3}','::1','','2026-10-28 11:28:56'),(115,NULL,'System','employee_login_success','employees',76,'{\"email\":\"t_hr@testsuite-portal.com\",\"role\":\"hr_manager\",\"ip\":\"::1\"}','::1','','2026-10-28 11:28:56'),(116,76,'Test HR RoleUser','edit_role','roles',4,'{\"role_name\":\"Department Supervisor\",\"permissions_count\":0}','::1','','2026-10-28 11:29:01'),(117,NULL,'System','employee_login_success','employees',78,'{\"email\":\"t_sup@testsuite-portal.com\",\"role\":\"supervisor\",\"ip\":\"::1\"}','::1','','2026-10-28 11:29:01'),(118,NULL,'System','employee_login_success','employees',79,'{\"email\":\"t_stf@testsuite-portal.com\",\"role\":\"staff\",\"ip\":\"::1\"}','::1','','2026-10-28 11:29:06'),(119,2,'admin','admin_login_success','admin',2,'{\"username\":\"admin\",\"role\":\"super_admin\",\"ip\":\"::1\"}','::1','','2026-10-28 11:29:36'),(120,2,'admin','edit_role','roles',5,'{\"role_name\":\"Staff Regular\",\"permissions_count\":3}','::1','','2026-10-28 11:29:41'),(121,NULL,'System','employee_login_success','employees',82,'{\"email\":\"t_hr@testsuite-portal.com\",\"role\":\"hr_manager\",\"ip\":\"::1\"}','::1','','2026-10-28 11:29:42'),(122,82,'Test HR RoleUser','edit_role','roles',4,'{\"role_name\":\"Department Supervisor\",\"permissions_count\":0}','::1','','2026-10-28 11:29:47'),(123,NULL,'System','employee_login_success','employees',84,'{\"email\":\"t_sup@testsuite-portal.com\",\"role\":\"supervisor\",\"ip\":\"::1\"}','::1','','2026-10-28 11:29:48'),(124,NULL,'System','employee_login_success','employees',85,'{\"email\":\"t_stf@testsuite-portal.com\",\"role\":\"staff\",\"ip\":\"::1\"}','::1','','2026-10-28 11:29:53'),(125,2,'admin','admin_login_success','admin',2,'{\"username\":\"admin\",\"role\":\"super_admin\",\"ip\":\"::1\"}','::1','','2026-10-28 11:30:09'),(126,2,'admin','create_employee','employee',87,'{\"code\":\"EMP-010\",\"name\":\"AdminUser Test\",\"email\":\"test_role1_admin@cloudaspire.test\",\"role_id\":1,\"shift_id\":null,\"status\":\"active\"}','::1','','2026-10-28 11:30:15'),(127,2,'admin','create_employee','employee',88,'{\"code\":\"EMP-011\",\"name\":\"HrUser Test\",\"email\":\"test_role2_hr@cloudaspire.test\",\"role_id\":2,\"shift_id\":null,\"status\":\"active\"}','::1','','2026-10-28 11:30:15'),(128,2,'admin','create_employee','employee',89,'{\"code\":\"EMP-012\",\"name\":\"MgrUser Test\",\"email\":\"test_role3_mgr@cloudaspire.test\",\"role_id\":3,\"shift_id\":null,\"status\":\"active\"}','::1','','2026-10-28 11:30:15'),(129,2,'admin','create_employee','employee',90,'{\"code\":\"EMP-013\",\"name\":\"SupUser Test\",\"email\":\"test_role4_sup@cloudaspire.test\",\"role_id\":4,\"shift_id\":null,\"status\":\"active\"}','::1','','2026-10-28 11:30:15'),(130,2,'admin','create_employee','employee',91,'{\"code\":\"EMP-014\",\"name\":\"StaffUser Test\",\"email\":\"test_role5_stf@cloudaspire.test\",\"role_id\":5,\"shift_id\":null,\"status\":\"active\"}','::1','','2026-10-28 11:30:16'),(131,2,'admin','create_employee','employee',92,'{\"code\":\"EMP-015\",\"name\":\"MbrUser Test\",\"email\":\"test_role6_mbr@cloudaspire.test\",\"role_id\":6,\"shift_id\":null,\"status\":\"active\"}','::1','','2026-10-28 11:30:16'),(132,2,'admin','update_employee_role','employee',91,'{\"employee_name\":\"StaffUser Test\",\"previous_role_id\":5,\"new_role_id\":4,\"new_role_name\":\"Department Supervisor\"}','::1','','2026-10-28 11:30:16'),(133,2,'admin','update_employee','employee',91,'{\"code\":\"EMP-014\",\"name\":\"StaffUser Promoted\",\"email\":\"test_role5_stf@cloudaspire.test\",\"role_id\":3,\"shift_id\":null,\"status\":\"active\"}','::1','','2026-10-28 11:30:16'),(134,NULL,'System','employee_login_success','employees',91,'{\"email\":\"test_role5_stf@cloudaspire.test\",\"role\":\"manager\",\"ip\":\"::1\"}','::1','','2026-10-28 11:30:16'),(135,NULL,'System','employee_login_success','employees',88,'{\"email\":\"test_role2_hr@cloudaspire.test\",\"role\":\"hr_manager\",\"ip\":\"::1\"}','::1','','2026-10-28 11:30:21'),(136,NULL,'System','employee_login_success','employees',89,'{\"email\":\"test_role3_mgr@cloudaspire.test\",\"role\":\"manager\",\"ip\":\"::1\"}','::1','','2026-10-28 11:30:26'),(137,NULL,'System','employee_login_success','employees',90,'{\"email\":\"test_role4_sup@cloudaspire.test\",\"role\":\"supervisor\",\"ip\":\"::1\"}','::1','','2026-10-28 11:30:32'),(138,NULL,'System','employee_login_success','employees',91,'{\"email\":\"test_role5_stf@cloudaspire.test\",\"role\":\"staff\",\"ip\":\"::1\"}','::1','','2026-10-28 11:30:40'),(139,NULL,'System','employee_login_success','employees',92,'{\"email\":\"test_role6_mbr@cloudaspire.test\",\"role\":\"member\",\"ip\":\"::1\"}','::1','','2026-10-28 11:30:45'),(140,2,'admin','admin_login_success','admin',2,'{\"username\":\"admin\",\"role\":\"super_admin\",\"ip\":\"::1\"}','::1','','2026-10-28 12:03:49'),(141,2,'admin','edit_role','roles',5,'{\"role_name\":\"Staff Regular\",\"permissions_count\":3}','::1','','2026-10-28 12:03:54'),(142,NULL,'System','employee_login_success','employees',98,'{\"email\":\"t_hr@testsuite-portal.com\",\"role\":\"hr_manager\",\"ip\":\"::1\"}','::1','','2026-10-28 12:03:54'),(143,98,'Test HR RoleUser','edit_role','roles',4,'{\"role_name\":\"Department Supervisor\",\"permissions_count\":0}','::1','','2026-10-28 12:04:02'),(144,NULL,'System','employee_login_success','employees',100,'{\"email\":\"t_sup@testsuite-portal.com\",\"role\":\"supervisor\",\"ip\":\"::1\"}','::1','','2026-10-28 12:04:03'),(145,NULL,'System','employee_login_success','employees',101,'{\"email\":\"t_stf@testsuite-portal.com\",\"role\":\"staff\",\"ip\":\"::1\"}','::1','','2026-10-28 12:04:08'),(146,NULL,'System','login_failed','users',NULL,'{\"identity\":\"cloudaspire2025@gmail.com\",\"ip\":\"49.37.217.70\"}','49.37.217.70','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-29 04:41:17'),(147,NULL,'System','login_failed','users',NULL,'{\"identity\":\"cloudaspire2025@gmail.com\",\"ip\":\"2409:4091:a019:ca13:1c9c:9796:f6fb:db2e\"}','2409:4091:a019:ca13:1c9c:9796:f6fb:db2e','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-29 04:46:21'),(148,2,'admin','admin_login_success','admin',2,'{\"username\":\"admin\",\"role\":\"super_admin\",\"ip\":\"2409:4091:a019:ca13:1c9c:9796:f6fb:db2e\"}','2409:4091:a019:ca13:1c9c:9796:f6fb:db2e','','2026-10-29 04:46:27'),(149,NULL,'System','login_failed','users',NULL,'{\"identity\":\"cloudaspire2025@gmail.com\",\"ip\":\"2409:4091:a019:ca13:1c9c:9796:f6fb:db2e\"}','2409:4091:a019:ca13:1c9c:9796:f6fb:db2e','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-29 04:46:46'),(150,NULL,'System','login_failed','users',NULL,'{\"identity\":\"cloudaspire2025@gmail.com\",\"ip\":\"::1\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-29 05:59:32'),(151,NULL,'System','login_failed','users',NULL,'{\"identity\":\"cloudaspire2025@gmail.com\",\"ip\":\"::1\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-29 05:59:39'),(152,NULL,'System','employee_login_success','employees',12,'{\"email\":\"tamilaninstamedia1@gmail.com\",\"role\":\"hr_manager\",\"ip\":\"::1\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-29 05:59:46'),(153,2,'admin','admin_login_success','admin',2,'{\"username\":\"admin\",\"role\":\"super_admin\",\"ip\":\"::1\"}','::1','','2026-10-30 06:52:19'),(154,2,'admin','create_employee','employee',104,'{\"code\":\"EMP-010\",\"name\":\"AdminUser Test\",\"email\":\"test_role1_admin@cloudaspire.test\",\"gender\":null,\"role_id\":1,\"shift_id\":null,\"status\":\"active\"}','::1','','2026-10-30 06:52:25'),(155,2,'admin','create_employee','employee',105,'{\"code\":\"EMP-011\",\"name\":\"HrUser Test\",\"email\":\"test_role2_hr@cloudaspire.test\",\"gender\":null,\"role_id\":2,\"shift_id\":null,\"status\":\"active\"}','::1','','2026-10-30 06:52:25'),(156,2,'admin','create_employee','employee',106,'{\"code\":\"EMP-012\",\"name\":\"MgrUser Test\",\"email\":\"test_role3_mgr@cloudaspire.test\",\"gender\":null,\"role_id\":3,\"shift_id\":null,\"status\":\"active\"}','::1','','2026-10-30 06:52:25'),(157,2,'admin','create_employee','employee',107,'{\"code\":\"EMP-013\",\"name\":\"SupUser Test\",\"email\":\"test_role4_sup@cloudaspire.test\",\"gender\":null,\"role_id\":4,\"shift_id\":null,\"status\":\"active\"}','::1','','2026-10-30 06:52:25'),(158,2,'admin','create_employee','employee',108,'{\"code\":\"EMP-014\",\"name\":\"StaffUser Test\",\"email\":\"test_role5_stf@cloudaspire.test\",\"gender\":null,\"role_id\":5,\"shift_id\":null,\"status\":\"active\"}','::1','','2026-10-30 06:52:25'),(159,2,'admin','create_employee','employee',109,'{\"code\":\"EMP-015\",\"name\":\"MbrUser Test\",\"email\":\"test_role6_mbr@cloudaspire.test\",\"gender\":null,\"role_id\":6,\"shift_id\":null,\"status\":\"active\"}','::1','','2026-10-30 06:52:25'),(160,2,'admin','update_employee_role','employee',108,'{\"employee_name\":\"StaffUser Test\",\"previous_role_id\":5,\"new_role_id\":4,\"new_role_name\":\"Department Supervisor\"}','::1','','2026-10-30 06:52:25'),(161,2,'admin','update_employee','employee',108,'{\"code\":\"EMP-014\",\"name\":\"StaffUser Promoted\",\"email\":\"test_role5_stf@cloudaspire.test\",\"gender\":null,\"role_id\":3,\"shift_id\":null,\"status\":\"active\"}','::1','','2026-10-30 06:52:25'),(162,NULL,'System','employee_login_success','employees',108,'{\"email\":\"test_role5_stf@cloudaspire.test\",\"role\":\"manager\",\"ip\":\"::1\"}','::1','','2026-10-30 06:52:25'),(163,NULL,'System','employee_login_success','employees',105,'{\"email\":\"test_role2_hr@cloudaspire.test\",\"role\":\"hr_manager\",\"ip\":\"::1\"}','::1','','2026-10-30 06:52:30'),(164,NULL,'System','employee_login_success','employees',106,'{\"email\":\"test_role3_mgr@cloudaspire.test\",\"role\":\"manager\",\"ip\":\"::1\"}','::1','','2026-10-30 06:52:35'),(165,NULL,'System','employee_login_success','employees',107,'{\"email\":\"test_role4_sup@cloudaspire.test\",\"role\":\"supervisor\",\"ip\":\"::1\"}','::1','','2026-10-30 06:52:40'),(166,NULL,'System','employee_login_success','employees',108,'{\"email\":\"test_role5_stf@cloudaspire.test\",\"role\":\"staff\",\"ip\":\"::1\"}','::1','','2026-10-30 06:52:45'),(167,NULL,'System','employee_login_success','employees',109,'{\"email\":\"test_role6_mbr@cloudaspire.test\",\"role\":\"member\",\"ip\":\"::1\"}','::1','','2026-10-30 06:52:51'),(168,NULL,'System','employee_login_success','employees',12,'{\"email\":\"tamilaninstamedia1@gmail.com\",\"role\":\"hr_manager\",\"ip\":\"::1\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-02 04:20:13'),(169,12,'Prem Kumar M','toggle_employee_index','site_settings',0,'{\"employee_index_enabled\":0}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-02 04:25:23'),(170,NULL,'Prem Kumar M','employee_logout','employees',12,'{\"email\":\"tamilaninstamedia1@gmail.com\",\"name\":\"Prem Kumar M\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-02 04:27:25'),(171,NULL,'System','login_failed','users',NULL,'{\"identity\":\"tamilaninstamedia1@gmail.com\",\"ip\":\"::1\"}','::1','','2026-11-02 05:07:23'),(172,NULL,'System','login_failed','users',NULL,'{\"identity\":\"tamilaninstamedia1@gmail.com\",\"ip\":\"::1\"}','::1','','2026-11-02 05:23:02'),(173,NULL,'System','employee_login_success','employees',12,'{\"email\":\"tamilaninstamedia1@gmail.com\",\"role\":\"hr_manager\",\"ip\":\"::1\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-02 05:35:59'),(174,12,'Prem Kumar M','toggle_employee_index','site_settings',0,'{\"employee_index_enabled\":0}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-02 05:36:15'),(175,12,'Prem Kumar M','toggle_employee_index','site_settings',0,'{\"employee_index_enabled\":1}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-02 05:36:18'),(176,12,'Prem Kumar M','toggle_employee_index','site_settings',0,'{\"employee_index_enabled\":0}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-02 05:38:24'),(177,12,'Prem Kumar M','toggle_employee_index','site_settings',0,'{\"employee_index_enabled\":1}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-02 05:38:36'),(178,12,'Prem Kumar M','toggle_employee_index','site_settings',0,'{\"employee_index_enabled\":0}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-02 05:38:37'),(179,NULL,'Prem Kumar M','employee_login_success','employees',12,'{\"email\":\"tamilaninstamedia1@gmail.com\",\"role\":\"hr_manager\",\"ip\":\"::1\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-02 05:38:42'),(180,12,'Prem Kumar M','toggle_employee_index','site_settings',0,'{\"employee_index_enabled\":1}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-02 05:39:43'),(181,NULL,'System','employee_login_success','employees',12,'{\"email\":\"tamilaninstamedia1@gmail.com\",\"role\":\"hr_manager\",\"ip\":\"::1\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) TestRunner/1.0','2026-11-02 06:18:59'),(182,12,'Prem Kumar M','toggle_employee_index','site_settings',0,'{\"employee_index_enabled\":0}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-02 06:19:31'),(183,12,'Prem Kumar M','toggle_employee_index','site_settings',0,'{\"employee_index_enabled\":1}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-02 06:19:35'),(184,NULL,'System','employee_login_success','employees',14,'{\"email\":\"priya30shanmuga@gmail.com\",\"role\":\"staff\",\"ip\":\"::1\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) TestRunner/1.0','2026-11-02 06:19:38'),(185,NULL,'System','employee_login_success','employees',14,'{\"email\":\"priya30shanmuga@gmail.com\",\"role\":\"staff\",\"ip\":\"::1\"}','::1','Mozilla/5.0','2026-11-02 06:20:05'),(186,12,'Prem Kumar M','toggle_employee_index','site_settings',0,'{\"employee_index_enabled\":0}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-02 06:20:21'),(187,12,'Prem Kumar M','toggle_employee_index','site_settings',0,'{\"employee_index_enabled\":1}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-02 06:20:23'),(188,12,'Prem Kumar M','toggle_employee_index','site_settings',0,'{\"employee_index_enabled\":0}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-02 06:20:33'),(189,12,'Prem Kumar M','toggle_employee_index','site_settings',0,'{\"employee_index_enabled\":1}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-02 06:20:48'),(190,12,'Prem Kumar M','toggle_employee_index','site_settings',0,'{\"employee_index_enabled\":0}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-02 06:21:06'),(191,12,'Prem Kumar M','toggle_employee_index','site_settings',0,'{\"employee_index_enabled\":1}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-02 06:21:14'),(192,12,'Prem Kumar M','toggle_employee_index','site_settings',0,'{\"employee_index_enabled\":0}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-02 06:21:18'),(193,12,'Prem Kumar M','logout','admin',12,'{\"username\":\"Prem Kumar M\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-02 06:21:24'),(194,NULL,'System','employee_login_success','employees',12,'{\"email\":\"tamilaninstamedia1@gmail.com\",\"role\":\"hr_manager\",\"ip\":\"::1\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-02 06:22:17'),(195,NULL,'System','employee_login_success','employees',12,'{\"email\":\"tamilaninstamedia1@gmail.com\",\"role\":\"hr_manager\",\"ip\":\"::1\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-02 11:10:39'),(196,NULL,'System','employee_login_success','employees',12,'{\"email\":\"tamilaninstamedia1@gmail.com\",\"role\":\"hr_manager\",\"ip\":\"::1\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-03 04:14:47'),(197,NULL,'System','employee_login_success','employees',14,'{\"email\":\"priya30shanmuga@gmail.com\",\"role\":\"staff\",\"ip\":\"2409:4091:a019:ca13:9040:d3a8:c931:3e4e\"}','2409:4091:a019:ca13:9040:d3a8:c931:3e4e','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-03 05:19:55'),(198,NULL,'System','employee_logout','employees',14,'{\"email\":\"priya30shanmuga@gmail.com\",\"name\":\"Shanmuga Priya\"}','2409:4091:a019:ca13:9040:d3a8:c931:3e4e','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-03 05:20:34'),(199,1,'ca_cloudaspire','admin_login_success','admin',1,'{\"username\":\"ca_cloudaspire\",\"role\":\"super_admin\",\"ip\":\"2409:4091:a019:ca13:9040:d3a8:c931:3e4e\"}','2409:4091:a019:ca13:9040:d3a8:c931:3e4e','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-03 05:22:06'),(200,1,'ca_cloudaspire','toggle_employee_index','site_settings',0,'{\"employee_index_enabled\":1}','2409:4091:a019:ca13:118d:91b0:46c5:82e4','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-03 05:48:01'),(201,1,'ca_cloudaspire','toggle_employee_index','site_settings',0,'{\"employee_index_enabled\":0}','2409:4091:a019:ca13:118d:91b0:46c5:82e4','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-03 05:48:21'),(202,1,'ca_cloudaspire','toggle_employee_index','site_settings',0,'{\"employee_index_enabled\":0}','2409:4091:a019:ca13:118d:91b0:46c5:82e4','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-03 05:48:21'),(203,NULL,'System','employee_login_success','employees',12,'{\"email\":\"tamilaninstamedia1@gmail.com\",\"role\":\"hr_manager\",\"ip\":\"::1\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-03 06:10:25'),(204,1,'ca_cloudaspire','admin_login_success','admin',1,'{\"username\":\"ca_cloudaspire\",\"role\":\"super_admin\",\"ip\":\"2409:4091:a019:ca13:845f:4f5e:d2fa:5ba7\"}','2409:4091:a019:ca13:845f:4f5e:d2fa:5ba7','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-03 06:22:48'),(205,1,'ca_cloudaspire','admin_login_success','admin',1,'{\"username\":\"ca_cloudaspire\",\"role\":\"super_admin\",\"ip\":\"::1\"}','::1','','2026-11-03 07:32:01');
/*!40000 ALTER TABLE `admin_audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_roles`
--

DROP TABLE IF EXISTS `admin_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_roles` (
  `admin_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`admin_id`,`role_id`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_roles`
--

LOCK TABLES `admin_roles` WRITE;
/*!40000 ALTER TABLE `admin_roles` DISABLE KEYS */;
INSERT INTO `admin_roles` VALUES (1,1),(2,1);
/*!40000 ALTER TABLE `admin_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendance`
--

DROP TABLE IF EXISTS `attendance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attendance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `check_in` time DEFAULT NULL,
  `check_out` time DEFAULT NULL,
  `punch_history` text DEFAULT NULL,
  `leave_code` varchar(50) DEFAULT NULL,
  `status` enum('present','absent','half_day','late','on_leave') DEFAULT 'present',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `delay_minutes` int(11) DEFAULT 0,
  `early_exit_minutes` int(11) DEFAULT 0,
  `total_work_minutes` int(11) DEFAULT 0,
  `working_hours` decimal(5,2) DEFAULT 0.00,
  `attendance_status` varchar(50) DEFAULT 'present',
  `remarks` text DEFAULT NULL,
  `work_summary` text DEFAULT NULL,
  `completed_tasks` text DEFAULT NULL,
  `pending_tasks` text DEFAULT NULL,
  `check_in_lat` decimal(10,8) DEFAULT NULL,
  `check_in_lng` decimal(11,8) DEFAULT NULL,
  `check_in_dist` int(11) DEFAULT NULL,
  `check_out_lat` decimal(10,8) DEFAULT NULL,
  `check_out_lng` decimal(11,8) DEFAULT NULL,
  `check_out_dist` int(11) DEFAULT NULL,
  `is_flagged_for_review` tinyint(1) NOT NULL DEFAULT 0,
  `flag_reason` varchar(255) DEFAULT NULL,
  `closed_by_system` tinyint(1) NOT NULL DEFAULT 0,
  `checkout_type` varchar(30) NOT NULL DEFAULT 'manual',
  `scheduled_checkout` time DEFAULT NULL,
  `summary_submitted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_emp_date` (`employee_id`,`date`),
  KEY `idx_emp_checkout` (`employee_id`,`check_out`),
  KEY `idx_attendance_flagged` (`is_flagged_for_review`,`date`),
  KEY `idx_attendance_checkout_type` (`checkout_type`),
  KEY `idx_attendance_status` (`attendance_status`)
) ENGINE=InnoDB AUTO_INCREMENT=181 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance`
--

LOCK TABLES `attendance` WRITE;
/*!40000 ALTER TABLE `attendance` DISABLE KEYS */;
INSERT INTO `attendance` VALUES (1,12,'2026-09-18','08:55:56','17:13:48','[{\"in\":\"08:55:56\",\"out\":\"17:13:48\"}]',NULL,'present',NULL,'2026-09-18 03:25:56','2026-09-18 11:43:48',0,0,498,8.30,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(2,13,'2026-09-18','08:57:46','17:15:42','[{\"in\":\"08:57:46\",\"out\":\"17:15:42\"}]',NULL,'present',NULL,'2026-09-18 03:27:46','2026-09-18 11:45:42',0,0,498,8.30,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(3,14,'2026-09-18','09:04:10','17:30:46','[{\"in\":\"09:04:10\",\"out\":\"17:30:46\"}]',NULL,'present',NULL,'2026-09-18 03:34:10','2026-09-18 12:00:46',0,0,507,8.45,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(4,15,'2026-09-18','09:05:18','17:24:03','[{\"in\":\"09:05:18\",\"out\":\"17:24:03\"}]',NULL,'present',NULL,'2026-09-18 03:35:18','2026-09-18 11:54:03',0,0,499,8.32,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(5,17,'2026-09-18','09:07:02','17:15:12','[{\"in\":\"09:07:02\",\"out\":\"17:15:12\"}]',NULL,'present',NULL,'2026-09-18 03:37:02','2026-09-18 11:45:12',0,0,488,8.13,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(6,12,'2026-09-21','08:43:30','17:06:42','[{\"in\":\"08:43:30\",\"out\":\"17:06:42\"}]',NULL,'present',NULL,'2026-09-21 03:13:30','2026-09-21 11:36:42',0,0,503,8.38,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(7,14,'2026-09-21','08:57:37','17:04:39','[{\"in\":\"08:57:37\",\"out\":\"17:04:39\"}]',NULL,'present',NULL,'2026-09-21 03:27:37','2026-09-21 11:34:39',0,0,487,8.12,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(8,17,'2026-09-21','08:59:04','17:03:39','[{\"in\":\"08:59:04\",\"out\":\"17:03:39\"}]',NULL,'present',NULL,'2026-09-21 03:29:04','2026-09-21 11:33:39',0,0,485,8.08,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(9,13,'2026-09-21','08:59:04',NULL,'[{\"in\":\"08:59:04\",\"out\":null}]',NULL,'present',NULL,'2026-09-21 03:29:04','2026-09-21 03:29:04',0,0,0,0.00,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(10,15,'2026-09-21','09:02:45','17:06:42','[{\"in\":\"09:02:45\",\"out\":\"17:06:42\"}]',NULL,'present',NULL,'2026-09-21 03:32:45','2026-09-21 11:36:42',0,0,484,8.07,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(11,20,'2026-09-21','09:03:02','17:10:14','[{\"in\":\"09:03:02\",\"out\":\"17:10:14\"}]',NULL,'present',NULL,'2026-09-21 03:33:02','2026-09-21 11:40:14',0,0,487,8.12,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(12,12,'2026-09-22','08:50:38','16:49:36','[{\"in\":\"08:50:38\",\"out\":\"16:49:36\"}]','Medical Issue','present',NULL,'2026-09-22 03:20:38','2026-09-22 11:19:36',0,10,479,7.98,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(13,15,'2026-09-22','08:50:43',NULL,'[{\"in\":\"08:50:43\",\"out\":null}]',NULL,'present',NULL,'2026-09-22 03:20:43','2026-09-22 03:20:43',0,0,0,0.00,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(14,14,'2026-09-22','08:51:20','17:15:22','[{\"in\":\"08:51:20\",\"out\":\"17:15:22\"}]',NULL,'present',NULL,'2026-09-22 03:21:20','2026-09-22 11:45:22',0,0,504,8.40,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(15,17,'2026-09-22','08:52:44','17:20:40','[{\"in\":\"08:52:44\",\"out\":\"17:20:40\"}]',NULL,'present',NULL,'2026-09-22 03:22:44','2026-09-22 11:50:40',0,0,508,8.47,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(16,13,'2026-09-22','08:52:46',NULL,'[{\"in\":\"08:52:46\",\"out\":null}]',NULL,'present',NULL,'2026-09-22 03:22:46','2026-09-22 03:22:46',0,0,0,0.00,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(17,20,'2026-09-22','09:00:05','17:22:07','[{\"in\":\"09:00:05\",\"out\":\"17:21:53\"},{\"in\":\"17:22:01\",\"out\":\"17:22:07\"}]',NULL,'present',NULL,'2026-09-22 03:30:05','2026-09-22 11:52:07',0,0,502,8.37,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(18,12,'2026-09-23','08:49:27','17:07:26','[{\"in\":\"08:49:27\",\"out\":\"17:07:26\"}]',NULL,'present',NULL,'2026-09-23 03:19:27','2026-09-23 11:37:26',0,0,498,8.30,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(19,17,'2026-09-23','08:50:21','17:38:01','[{\"in\":\"08:50:21\",\"out\":\"17:38:01\"}]',NULL,'present',NULL,'2026-09-23 03:20:21','2026-09-23 12:08:01',0,0,528,8.80,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(20,20,'2026-09-23','09:04:12','16:48:19','[{\"in\":\"09:04:12\",\"out\":\"16:48:19\"}]','Other Authorized','present',NULL,'2026-09-23 03:34:12','2026-09-23 11:18:19',0,11,464,7.73,'left_early',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(21,14,'2026-09-23','09:07:18',NULL,'[{\"in\":\"09:07:18\",\"out\":null}]',NULL,'present',NULL,'2026-09-23 03:37:18','2026-09-23 03:37:18',0,0,0,0.00,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(22,13,'2026-09-23','09:07:34','17:06:54','[{\"in\":\"09:07:34\",\"out\":\"17:06:54\"}]',NULL,'present',NULL,'2026-09-23 03:37:34','2026-09-23 11:36:54',0,0,479,7.98,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(23,15,'2026-09-23','09:09:16','17:06:35','[{\"in\":\"09:09:16\",\"out\":\"17:06:35\"}]',NULL,'present',NULL,'2026-09-23 03:39:16','2026-09-23 11:36:35',0,0,477,7.95,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(24,20,'2026-09-24','08:50:28','17:29:34','[{\"in\":\"08:50:28\",\"out\":\"17:29:34\"}]',NULL,'present',NULL,'2026-09-24 03:20:28','2026-09-24 11:59:34',0,0,519,8.65,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(25,14,'2026-09-24','08:56:33','17:05:19','[{\"in\":\"08:56:33\",\"out\":\"17:05:19\"}]',NULL,'present',NULL,'2026-09-24 03:26:33','2026-09-24 11:35:19',0,0,489,8.15,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(26,12,'2026-09-24','08:56:33','17:16:03','[{\"in\":\"08:56:33\",\"out\":\"17:16:03\"}]',NULL,'present',NULL,'2026-09-24 03:26:33','2026-09-24 11:46:03',0,0,500,8.33,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(27,17,'2026-09-24','08:59:49','17:04:12','[{\"in\":\"08:59:49\",\"out\":\"17:04:12\"}]',NULL,'present',NULL,'2026-09-24 03:29:49','2026-09-24 11:34:12',0,0,484,8.07,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(28,13,'2026-09-24','08:59:59','17:00:32','[{\"in\":\"08:59:59\",\"out\":\"17:00:32\"}]',NULL,'present',NULL,'2026-09-24 03:29:59','2026-09-24 11:30:32',0,0,481,8.02,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(29,15,'2026-09-24','08:59:59','17:02:12','[{\"in\":\"08:59:59\",\"out\":\"17:02:12\"}]',NULL,'present',NULL,'2026-09-24 03:29:59','2026-09-24 11:32:12',0,0,482,8.03,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(30,20,'2026-09-25','08:46:59',NULL,'[{\"in\":\"08:46:59\",\"out\":null}]',NULL,'present',NULL,'2026-09-25 03:16:59','2026-09-25 03:16:59',0,0,0,0.00,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(31,17,'2026-09-25','08:51:00','17:00:00','[{\"in\":\"08:51:28\",\"out\":null}]',NULL,'present','','2026-09-25 03:21:28','2026-09-25 20:49:54',0,0,489,8.15,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(32,15,'2026-09-25','08:56:00','17:19:00','[{\"in\":\"08:56:27\",\"out\":null}]',NULL,'present','','2026-09-25 03:26:27','2026-09-25 20:49:26',0,0,503,8.38,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(33,14,'2026-09-25','08:56:00','17:00:00','[{\"in\":\"08:56:40\",\"out\":null}]',NULL,'present','','2026-09-25 03:26:40','2026-09-25 20:49:38',0,0,484,8.07,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(34,12,'2026-09-25','08:59:00','17:00:00','[{\"in\":\"08:59:58\",\"out\":null}]',NULL,'present','','2026-09-25 03:29:58','2026-09-25 20:50:19',0,0,481,8.02,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(35,13,'2026-09-25','09:00:35','10:09:30','[{\"in\":\"09:00:35\",\"out\":\"10:09:30\"}]','Family Responsibility','present',NULL,'2026-09-25 03:30:35','2026-09-25 04:39:30',0,410,69,1.15,'left_early',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(41,12,'2026-09-28','08:46:58','17:02:10','[{\"in\":\"08:46:58\",\"out\":\"17:02:10\"}]',NULL,'present',NULL,'2026-09-28 03:16:58','2026-09-28 11:32:10',0,0,495,8.25,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(42,15,'2026-09-28','08:55:27','17:01:21','[{\"in\":\"08:55:27\",\"out\":\"17:01:21\"}]',NULL,'present',NULL,'2026-09-28 03:25:27','2026-09-28 11:31:21',0,0,486,8.10,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(43,14,'2026-09-28','08:56:59','17:01:16','[{\"in\":\"08:56:59\",\"out\":\"17:01:16\"}]',NULL,'present',NULL,'2026-09-28 03:26:59','2026-09-28 11:31:16',0,0,484,8.07,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(44,17,'2026-09-28','08:58:05','17:02:03','[{\"in\":\"08:58:05\",\"out\":\"17:02:03\"}]',NULL,'present',NULL,'2026-09-28 03:28:05','2026-09-28 11:32:03',0,0,484,8.07,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(45,13,'2026-09-28','09:00:24','17:02:34','[{\"in\":\"09:00:24\",\"out\":\"17:02:34\"}]',NULL,'present',NULL,'2026-09-28 03:30:24','2026-09-28 11:32:34',0,0,482,8.03,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(46,20,'2026-09-28','09:22:18','17:11:08','[{\"in\":\"09:22:18\",\"out\":\"17:11:08\"}]',NULL,'present',NULL,'2026-09-28 03:52:18','2026-09-28 11:41:08',0,0,469,7.82,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(47,20,'2026-09-29','08:37:00','17:36:12','[{\"in\":\"08:37:20\",\"out\":\"17:36:12\"}]',NULL,'present','','2026-09-29 03:07:20','2026-09-29 12:06:12',0,0,539,8.98,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(48,12,'2026-09-29','08:40:00','17:11:34','[{\"in\":\"08:40:30\",\"out\":\"17:11:34\"}]',NULL,'present','','2026-09-29 03:10:30','2026-09-29 11:41:34',0,0,511,8.52,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(49,15,'2026-09-29','08:54:31','14:10:24','[{\"in\":\"08:54:31\",\"out\":\"14:10:24\"}]','Medical Issue','present',NULL,'2026-09-29 03:24:31','2026-09-29 08:40:24',0,169,316,5.27,'left_early',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(50,17,'2026-09-29','08:55:04','14:08:17','[{\"in\":\"08:55:04\",\"out\":\"14:07:16\"},{\"in\":\"14:08:08\",\"out\":\"14:08:17\"}]','Approved Half Day','present',NULL,'2026-09-29 03:25:04','2026-09-29 08:38:17',0,171,312,5.20,'left_early',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(51,14,'2026-09-29','08:59:34','14:11:28','[{\"in\":\"08:59:34\",\"out\":\"14:10:36\"},{\"in\":\"14:11:04\",\"out\":\"14:11:28\"}]','Approved Half Day','present',NULL,'2026-09-29 03:29:34','2026-09-29 08:41:28',0,168,311,5.18,'left_early',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(52,13,'2026-09-29','09:00:00','15:42:00','[{\"in\":\"09:00:31\",\"out\":null}]',NULL,'present','','2026-09-29 03:30:31','2026-09-29 10:12:47',0,78,402,6.70,'left_early',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(56,12,'2026-09-30','08:30:35','18:00:39','[{\"in\":\"08:30:35\",\"out\":\"18:00:39\"}]',NULL,'present',NULL,'2026-09-30 03:00:35','2026-09-30 12:30:39',0,0,570,9.50,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(57,13,'2026-09-30','09:22:51','18:01:02','[{\"in\":\"09:22:51\",\"out\":\"18:01:02\"}]',NULL,'present',NULL,'2026-09-30 03:52:51','2026-09-30 12:31:02',0,0,518,8.63,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(58,12,'2026-10-01','08:58:00','17:02:07','[{\"in\":\"08:58:00\",\"out\":\"17:02:07\"}]',NULL,'present',NULL,'2026-10-01 03:28:00','2026-10-01 11:32:07',0,0,484,8.07,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(59,13,'2026-10-01','08:59:32','17:14:35','[{\"in\":\"08:59:32\",\"out\":\"17:14:35\"}]',NULL,'present',NULL,'2026-10-01 03:29:32','2026-10-01 11:44:35',0,0,495,8.25,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(60,20,'2026-10-01','11:51:50','17:54:54','[{\"in\":\"11:51:50\",\"out\":\"17:54:54\"}]',NULL,'late',NULL,'2026-10-01 06:21:50','2026-10-01 12:24:54',121,0,363,6.05,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(61,13,'2026-10-03','13:43:34','17:23:30','[{\"in\":\"13:43:34\",\"out\":\"17:23:30\"}]',NULL,'late',NULL,'2026-10-03 08:13:34','2026-10-03 11:53:30',233,0,220,3.67,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(62,12,'2026-10-05','08:39:00','17:13:00','[{\"in\":\"08:39:30\",\"out\":null}]',NULL,'present','','2026-10-05 03:09:30','2026-10-05 17:43:39',0,0,514,8.57,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(63,20,'2026-10-05','09:02:49','17:00:22','[{\"in\":\"09:02:49\",\"out\":\"17:00:22\"}]',NULL,'present',NULL,'2026-10-05 03:32:49','2026-10-05 11:30:22',0,0,478,7.97,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(65,12,'2026-10-06','12:04:47','17:09:50','[{\"in\":\"12:04:47\",\"out\":\"17:09:50\"}]',NULL,'late',NULL,'2026-10-06 06:34:47','2026-10-06 11:39:50',134,0,305,5.08,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(66,17,'2026-10-07','09:01:57','18:04:15','[{\"in\":\"09:01:57\",\"out\":\"18:04:15\"}]',NULL,'present',NULL,'2026-10-07 03:31:57','2026-10-07 12:34:15',0,0,542,9.03,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(67,14,'2026-10-07','09:07:36','18:06:42','[{\"in\":\"09:07:36\",\"out\":\"18:06:42\"}]',NULL,'present',NULL,'2026-10-07 03:37:36','2026-10-07 12:36:42',0,0,539,8.98,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(68,13,'2026-10-07','09:43:05','17:03:10','[{\"in\":\"09:43:05\",\"out\":\"17:03:10\"}]',NULL,'present',NULL,'2026-10-07 04:13:05','2026-10-07 11:33:10',0,0,440,7.33,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(69,12,'2026-10-07','09:57:03','18:03:34','[{\"in\":\"09:57:03\",\"out\":\"18:03:34\"}]',NULL,'present',NULL,'2026-10-07 04:27:03','2026-10-07 12:33:34',0,0,487,8.12,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(70,15,'2026-10-07','10:19:17','18:04:07','[{\"in\":\"10:19:17\",\"out\":\"18:04:07\"}]',NULL,'late',NULL,'2026-10-07 04:49:17','2026-10-07 12:34:07',29,0,465,7.75,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(71,17,'2026-10-08','09:03:10','22:05:47','[{\"in\":\"09:03:10\",\"out\":\"22:05:47\"}]',NULL,'present',NULL,'2026-10-08 03:33:10','2026-10-08 16:35:47',0,0,783,13.05,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(72,14,'2026-10-08','09:07:45','18:36:56','[{\"in\":\"09:07:45\",\"out\":\"18:36:56\"}]',NULL,'present',NULL,'2026-10-08 03:37:45','2026-10-08 13:06:56',0,0,569,9.48,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(73,12,'2026-10-08','09:07:59','18:38:27','[{\"in\":\"09:07:59\",\"out\":\"18:38:27\"}]',NULL,'present',NULL,'2026-10-08 03:37:59','2026-10-08 13:08:27',0,0,570,9.50,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(74,13,'2026-10-08','09:09:00','18:29:00','[{\"in\":\"09:09:52\",\"out\":\"22:03:22\"}]',NULL,'present','','2026-10-08 03:39:52','2026-10-08 16:34:35',0,0,560,9.33,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(75,15,'2026-10-08','09:10:33','18:37:19','[{\"in\":\"09:10:33\",\"out\":\"18:37:19\"}]',NULL,'present',NULL,'2026-10-08 03:40:33','2026-10-08 13:07:19',0,0,567,9.45,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(77,20,'2026-10-09','08:57:37','17:01:44','[{\"in\":\"08:57:37\",\"out\":\"17:01:44\"}]',NULL,'present',NULL,'2026-10-09 03:27:37','2026-10-09 11:31:44',0,0,484,8.07,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(78,15,'2026-10-09','09:00:03','17:13:02','[{\"in\":\"09:00:03\",\"out\":\"17:13:02\"}]',NULL,'present',NULL,'2026-10-09 03:30:03','2026-10-09 11:43:02',0,0,493,8.22,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(79,14,'2026-10-09','09:00:42','17:18:41','[{\"in\":\"09:00:42\",\"out\":\"17:18:41\"}]',NULL,'present',NULL,'2026-10-09 03:30:42','2026-10-09 11:48:41',0,0,498,8.30,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(80,12,'2026-10-09','09:13:16','14:32:36','[{\"in\":\"09:13:16\",\"out\":\"14:32:36\"}]','Approved Half Day','present',NULL,'2026-10-09 03:43:16','2026-10-09 09:02:36',0,147,319,5.32,'left_early',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(81,17,'2026-10-09','09:25:16','17:29:56','[{\"in\":\"09:25:16\",\"out\":\"17:29:56\"}]',NULL,'present',NULL,'2026-10-09 03:55:16','2026-10-09 11:59:56',0,0,485,8.08,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(82,13,'2026-10-09','09:39:47','17:11:57','[{\"in\":\"09:39:47\",\"out\":\"17:11:57\"}]',NULL,'present',NULL,'2026-10-09 04:09:47','2026-10-09 11:41:57',0,0,452,7.53,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(84,15,'2026-10-12','08:52:51','17:18:59','[{\"in\":\"08:52:51\",\"out\":\"17:18:59\"}]',NULL,'present',NULL,'2026-10-12 03:22:51','2026-10-12 11:48:59',0,0,506,8.43,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(85,13,'2026-10-12','08:56:34','22:20:47','[{\"in\":\"08:56:34\",\"out\":\"22:20:47\"}]',NULL,'present',NULL,'2026-10-12 03:26:34','2026-10-12 16:50:47',0,0,804,13.40,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(86,17,'2026-10-12','08:57:29','17:14:18','[{\"in\":\"08:57:29\",\"out\":\"17:14:18\"}]',NULL,'present',NULL,'2026-10-12 03:27:29','2026-10-12 11:44:18',0,0,497,8.28,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(87,12,'2026-10-12','09:00:17','17:12:53','[{\"in\":\"09:00:17\",\"out\":\"17:12:53\"}]',NULL,'present',NULL,'2026-10-12 03:30:17','2026-10-12 11:42:53',10,0,493,8.22,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(88,14,'2026-10-12','09:01:46','22:19:54','[{\"in\":\"09:01:46\",\"out\":\"22:19:54\"}]',NULL,'late',NULL,'2026-10-12 03:31:46','2026-10-12 16:49:54',11,0,798,13.30,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(89,20,'2026-10-13','08:39:28','17:09:55','[{\"in\":\"08:39:28\",\"out\":\"17:09:55\"}]',NULL,'present',NULL,'2026-10-13 03:09:28','2026-10-13 11:39:55',0,0,510,8.50,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(90,15,'2026-10-13','09:02:41','17:28:08','[{\"in\":\"09:02:41\",\"out\":\"17:28:08\"}]',NULL,'late',NULL,'2026-10-13 03:32:41','2026-10-13 11:58:08',12,0,505,8.42,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(91,17,'2026-10-13','09:11:36','17:28:23','[{\"in\":\"09:11:36\",\"out\":\"17:28:23\"}]',NULL,'late',NULL,'2026-10-13 03:41:36','2026-10-13 11:58:23',21,0,497,8.28,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(92,12,'2026-10-13','09:16:39','17:25:21','[{\"in\":\"09:16:39\",\"out\":\"17:25:21\"}]',NULL,'late',NULL,'2026-10-13 03:46:39','2026-10-13 11:55:21',26,0,489,8.15,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(93,12,'2026-10-14','08:59:21','17:35:45','[{\"in\":\"08:59:21\",\"out\":\"17:35:45\"}]',NULL,'present',NULL,'2026-10-14 03:29:21','2026-10-14 12:05:45',0,0,516,8.60,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(94,15,'2026-10-14','08:59:53','17:23:59','[{\"in\":\"08:59:53\",\"out\":\"17:23:59\"}]',NULL,'present',NULL,'2026-10-14 03:29:53','2026-10-14 11:53:59',0,0,504,8.40,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(95,14,'2026-10-14','09:04:14','17:32:48','[{\"in\":\"09:04:14\",\"out\":\"17:32:48\"}]',NULL,'late',NULL,'2026-10-14 03:34:14','2026-10-14 12:02:48',14,0,509,8.48,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(96,13,'2026-10-14','09:04:47','17:35:43','[{\"in\":\"09:04:47\",\"out\":\"17:35:43\"}]',NULL,'late',NULL,'2026-10-14 03:34:47','2026-10-14 12:05:43',14,0,511,8.52,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(97,17,'2026-10-14','09:07:21','17:24:46','[{\"in\":\"09:07:21\",\"out\":\"17:24:46\"}]',NULL,'late',NULL,'2026-10-14 03:37:21','2026-10-14 11:54:46',17,0,497,8.28,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(98,20,'2026-10-14','09:26:17',NULL,'[{\"in\":\"09:26:17\",\"out\":null}]',NULL,'late',NULL,'2026-10-14 03:56:17','2026-10-14 03:56:17',36,0,0,0.00,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(99,20,'2026-10-15','08:44:52',NULL,'[{\"in\":\"08:44:52\",\"out\":null}]',NULL,'present',NULL,'2026-10-15 03:14:52','2026-10-15 03:14:52',0,0,0,0.00,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(100,13,'2026-10-15','08:53:39','17:06:00','[{\"in\":\"08:53:39\",\"out\":\"17:06:00\"}]',NULL,'present',NULL,'2026-10-15 03:23:39','2026-10-15 11:36:00',0,0,492,8.20,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(101,15,'2026-10-15','09:02:43','17:04:20','[{\"in\":\"09:02:43\",\"out\":\"17:04:20\"}]',NULL,'late',NULL,'2026-10-15 03:32:43','2026-10-15 11:34:20',12,0,482,8.03,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(102,14,'2026-10-15','09:05:03','17:05:17','[{\"in\":\"09:05:03\",\"out\":\"17:05:17\"}]',NULL,'late',NULL,'2026-10-15 03:35:03','2026-10-15 11:35:17',15,0,480,8.00,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(103,17,'2026-10-15','09:05:15','17:05:33','[{\"in\":\"09:05:15\",\"out\":\"17:05:33\"}]',NULL,'late',NULL,'2026-10-15 03:35:15','2026-10-15 11:35:33',15,0,480,8.00,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(104,12,'2026-10-15','09:05:35','17:49:53','[{\"in\":\"09:05:35\",\"out\":\"17:49:53\"}]',NULL,'late',NULL,'2026-10-15 03:35:35','2026-10-15 12:19:53',15,0,524,8.73,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(105,17,'2026-10-16','08:56:05','17:19:29','[{\"in\":\"08:56:05\",\"out\":\"17:19:29\"}]',NULL,'present',NULL,'2026-10-16 03:26:05','2026-10-16 11:49:29',0,0,503,8.38,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(106,15,'2026-10-16','08:56:24','17:12:22','[{\"in\":\"08:56:24\",\"out\":\"17:12:22\"}]',NULL,'present',NULL,'2026-10-16 03:26:24','2026-10-16 11:42:22',0,0,496,8.27,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(107,14,'2026-10-16','09:00:49','17:18:18','[{\"in\":\"09:00:49\",\"out\":\"17:18:18\"}]',NULL,'present',NULL,'2026-10-16 03:30:49','2026-10-16 11:48:18',10,0,497,8.28,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(108,13,'2026-10-16','09:01:25',NULL,'[{\"in\":\"09:01:25\",\"out\":null}]',NULL,'late',NULL,'2026-10-16 03:31:25','2026-10-16 03:31:25',11,0,0,0.00,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(109,12,'2026-10-16','09:14:10','20:16:47','[{\"in\":\"09:14:10\",\"out\":\"20:16:47\"}]',NULL,'late',NULL,'2026-10-16 03:44:10','2026-10-16 14:46:47',24,0,663,11.05,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(110,20,'2026-10-17','09:10:37','14:06:44','[{\"in\":\"09:10:37\",\"out\":\"14:06:44\"}]','Other Authorized','half_day',NULL,'2026-10-17 03:40:37','2026-10-17 08:36:44',20,173,296,4.93,'half_day',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(111,15,'2026-10-17','10:12:39',NULL,'[{\"in\":\"10:12:39\",\"out\":null}]',NULL,'late',NULL,'2026-10-17 04:42:39','2026-10-17 04:42:39',82,0,0,0.00,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(112,17,'2026-10-17','10:15:15','14:02:49','[{\"in\":\"10:15:15\",\"out\":\"14:02:49\"}]','Other Authorized','half_day',NULL,'2026-10-17 04:45:15','2026-10-17 08:32:49',85,177,228,3.80,'half_day',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(113,14,'2026-10-17','10:21:15','14:03:19','[{\"in\":\"10:21:15\",\"out\":\"14:03:19\"}]','Other Authorized','half_day',NULL,'2026-10-17 04:51:15','2026-10-17 08:33:19',91,176,222,3.70,'half_day',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(114,12,'2026-10-17','22:21:00',NULL,'[{\"in\":\"12:21:36\",\"out\":null}]',NULL,'late','','2026-10-17 06:51:36','2026-10-17 06:52:12',811,0,0,0.00,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(116,12,'2026-10-19','20:10:50','20:10:53','[{\"in\":\"20:10:50\",\"out\":\"20:10:53\"}]',NULL,'late',NULL,'2026-10-19 14:40:50','2026-10-19 14:40:53',680,0,0,0.00,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(117,17,'2026-10-21','08:56:46','17:33:06','[{\"in\":\"08:56:46\",\"out\":\"17:33:06\"}]',NULL,'present',NULL,'2026-10-21 03:26:46','2026-10-21 12:03:06',0,0,516,8.60,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(118,14,'2026-10-21','08:57:54','17:33:55','[{\"in\":\"08:57:54\",\"out\":\"17:33:55\"}]',NULL,'present',NULL,'2026-10-21 03:27:54','2026-10-21 12:03:55',0,0,516,8.60,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(119,15,'2026-10-21','08:58:08','17:32:40','[{\"in\":\"08:58:08\",\"out\":\"17:32:40\"}]',NULL,'present',NULL,'2026-10-21 03:28:08','2026-10-21 12:02:40',0,0,515,8.58,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(120,12,'2026-10-21','09:09:09','18:18:21','[{\"in\":\"09:09:09\",\"out\":\"18:18:21\"}]',NULL,'late',NULL,'2026-10-21 03:39:09','2026-10-21 12:48:21',19,0,549,9.15,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(121,13,'2026-10-21','09:36:21','17:32:22','[{\"in\":\"09:36:21\",\"out\":\"17:32:22\"}]',NULL,'late',NULL,'2026-10-21 04:06:21','2026-10-21 12:02:22',46,0,476,7.93,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(122,21,'2026-10-23','08:52:53','17:37:15','[{\"in\":\"08:52:53\",\"out\":\"17:37:15\"}]',NULL,'present',NULL,'2026-10-23 03:22:53','2026-10-23 12:07:15',0,0,524,8.73,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(123,15,'2026-10-23','09:01:13','17:07:39','[{\"in\":\"09:01:13\",\"out\":\"17:07:39\"}]',NULL,'late',NULL,'2026-10-23 03:31:13','2026-10-23 11:37:39',11,0,486,8.10,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(124,17,'2026-10-23','09:04:08','17:05:00','[{\"in\":\"09:04:08\",\"out\":\"17:05:00\"}]',NULL,'late',NULL,'2026-10-23 03:34:08','2026-10-23 11:35:00',14,0,481,8.02,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(125,14,'2026-10-23','09:04:40','17:02:07','[{\"in\":\"09:04:40\",\"out\":\"17:02:07\"}]',NULL,'late',NULL,'2026-10-23 03:34:40','2026-10-23 11:32:08',14,0,477,7.95,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(126,12,'2026-10-23','09:19:32','17:07:26','[{\"in\":\"09:19:32\",\"out\":\"17:07:26\"}]',NULL,'late',NULL,'2026-10-23 03:49:32','2026-10-23 11:37:26',29,0,468,7.80,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(127,13,'2026-10-23','10:21:00','17:12:00','[{\"in\":\"10:21:49\",\"out\":null}]',NULL,'present','','2026-10-23 04:51:49','2026-10-23 19:42:49',91,0,411,6.85,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(128,20,'2026-10-23','10:27:01','17:00:45','[{\"in\":\"10:27:01\",\"out\":\"17:00:45\"}]',NULL,'late',NULL,'2026-10-23 04:57:01','2026-10-23 11:30:45',97,0,394,6.57,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(130,17,'2026-10-24','08:57:07','17:42:10','[{\"in\":\"08:57:07\",\"out\":\"17:42:10\"}]',NULL,'present',NULL,'2026-10-24 03:27:07','2026-10-24 12:12:10',0,0,525,8.75,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(131,14,'2026-10-24','09:01:01','17:32:19','[{\"in\":\"09:01:01\",\"out\":\"17:32:19\"}]',NULL,'late',NULL,'2026-10-24 03:31:01','2026-10-24 12:02:19',11,0,511,8.52,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(132,15,'2026-10-24','09:01:29','17:22:45','[{\"in\":\"09:01:29\",\"out\":\"17:22:45\"}]',NULL,'late',NULL,'2026-10-24 03:31:29','2026-10-24 11:52:45',11,0,501,8.35,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(133,12,'2026-10-24','09:09:30','17:24:46','[{\"in\":\"09:09:30\",\"out\":\"17:24:46\"}]',NULL,'late',NULL,'2026-10-24 03:39:30','2026-10-24 11:54:46',19,0,495,8.25,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(134,21,'2026-10-24','09:59:04','17:09:37','[{\"in\":\"09:59:04\",\"out\":\"17:09:37\"}]',NULL,'late',NULL,'2026-10-24 04:29:04','2026-10-24 11:39:37',69,0,431,7.18,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(135,13,'2026-10-24','10:04:55','18:09:38','[{\"in\":\"10:04:55\",\"out\":\"18:09:38\"}]',NULL,'late',NULL,'2026-10-24 04:34:55','2026-10-24 12:39:38',74,0,485,8.08,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(136,15,'2026-10-26','08:57:53','17:31:43','[{\"in\":\"08:57:53\",\"out\":\"17:31:43\"}]',NULL,'present',NULL,'2026-10-26 03:27:53','2026-10-26 12:01:43',0,0,514,8.57,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(137,12,'2026-10-26','08:58:07','18:40:51','[{\"in\":\"08:58:07\",\"out\":\"18:40:51\"}]',NULL,'present',NULL,'2026-10-26 03:28:07','2026-10-26 13:10:51',0,0,583,9.72,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(138,17,'2026-10-26','08:59:00','18:41:57','[{\"in\":\"08:59:00\",\"out\":\"18:41:57\"}]',NULL,'present',NULL,'2026-10-26 03:29:00','2026-10-26 13:11:57',0,0,583,9.72,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(139,14,'2026-10-26','09:05:03','18:05:08','[{\"in\":\"09:05:03\",\"out\":\"18:05:08\"}]',NULL,'late',NULL,'2026-10-26 03:35:03','2026-10-26 12:35:08',15,0,540,9.00,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(140,13,'2026-10-26','09:06:36','20:07:16','[{\"in\":\"09:06:36\",\"out\":\"20:07:16\"}]',NULL,'late',NULL,'2026-10-26 03:36:36','2026-10-26 14:37:16',16,0,661,11.02,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(141,21,'2026-10-26','09:40:32','22:12:06','[{\"in\":\"09:40:32\",\"out\":\"22:12:06\"}]',NULL,'late',NULL,'2026-10-26 04:10:32','2026-10-26 16:42:06',50,0,752,12.53,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(142,21,'2026-10-27','09:00:36','18:06:06','[{\"in\":\"09:00:36\",\"out\":\"18:06:06\"}]',NULL,'present',NULL,'2026-10-27 03:30:36','2026-10-27 12:36:06',10,0,546,9.10,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(143,14,'2026-10-27','09:05:21','23:09:54','[{\"in\":\"09:05:21\",\"out\":\"23:09:54\"}]',NULL,'late',NULL,'2026-10-27 03:35:21','2026-10-27 17:39:54',15,0,845,14.08,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(144,12,'2026-10-27','09:08:19','17:25:37','[{\"in\":\"09:08:19\",\"out\":\"17:25:37\"}]',NULL,'late',NULL,'2026-10-27 03:38:19','2026-10-27 11:55:37',18,0,497,8.28,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(145,13,'2026-10-27','09:17:46','17:12:07','[{\"in\":\"09:17:46\",\"out\":\"17:12:07\"}]',NULL,'late',NULL,'2026-10-27 03:47:46','2026-10-27 11:42:07',27,0,474,7.90,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(146,14,'2026-10-28','09:10:00','23:16:24','[{\"in\":\"09:10:00\",\"out\":\"23:16:24\"}]',NULL,'late','off Day Leave','2026-10-28 03:40:00','2026-10-28 17:46:24',20,0,846,14.10,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(147,15,'2026-10-28','09:11:58','17:32:36','[{\"in\":\"09:11:58\",\"out\":\"17:32:36\"}]',NULL,'late',NULL,'2026-10-28 03:41:58','2026-10-28 12:02:36',21,0,501,8.35,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(148,17,'2026-10-28','09:14:58','17:40:40','[{\"in\":\"09:14:58\",\"out\":\"17:40:40\"}]',NULL,'late',NULL,'2026-10-28 03:44:58','2026-10-28 12:10:40',24,0,506,8.43,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(149,12,'2026-10-28','09:26:30','17:31:31','[{\"in\":\"09:26:30\",\"out\":\"17:31:31\"}]',NULL,'late',NULL,'2026-10-28 03:56:30','2026-10-28 12:01:31',36,0,485,8.08,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(150,21,'2026-10-28','09:39:51',NULL,'[{\"in\":\"09:39:51\",\"out\":null}]',NULL,'late',NULL,'2026-10-28 04:09:51','2026-10-28 04:09:51',49,0,0,0.00,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(151,20,'2026-10-28','10:00:00',NULL,'[{\"in\":\"11:23:02\",\"out\":null}]',NULL,'late','','2026-10-28 05:53:02','2026-10-28 07:00:50',70,0,0,0.00,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(154,13,'2026-10-28','09:15:00','17:31:00','[{\"in\":\"17:31:13\",\"out\":\"17:31:21\"}]',NULL,'late','','2026-10-28 12:01:13','2026-10-28 12:08:20',25,0,496,8.27,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(158,13,'2026-10-29','09:03:04','19:40:51','[{\"in\":\"09:03:04\",\"out\":\"19:40:51\"}]',NULL,'late',NULL,'2026-10-29 03:33:04','2026-10-29 14:10:51',13,0,638,10.63,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(159,15,'2026-10-29','09:03:59','17:10:22','[{\"in\":\"09:03:59\",\"out\":\"17:10:22\"}]',NULL,'late',NULL,'2026-10-29 03:33:59','2026-10-29 11:40:22',13,0,486,8.10,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(160,17,'2026-10-29','09:05:02','17:15:41','[{\"in\":\"09:05:02\",\"out\":\"17:15:41\"}]',NULL,'late',NULL,'2026-10-29 03:35:02','2026-10-29 11:45:41',15,0,491,8.18,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(161,16,'2026-10-29','09:06:04','17:08:15','[{\"in\":\"09:06:04\",\"out\":\"17:08:15\"}]',NULL,'late',NULL,'2026-10-29 03:36:04','2026-10-29 11:38:15',16,0,482,8.03,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(162,12,'2026-10-29','10:13:06',NULL,'[{\"in\":\"10:13:06\",\"out\":null}]',NULL,'late',NULL,'2026-10-29 04:43:06','2026-10-29 04:43:06',83,0,0,0.00,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(163,14,'2026-10-29','10:18:55','17:25:29','[{\"in\":\"10:18:55\",\"out\":\"17:25:29\"}]',NULL,'late',NULL,'2026-10-29 04:48:55','2026-10-29 11:55:29',88,0,427,7.12,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(164,16,'2026-10-30','09:00:43','17:01:32','[{\"in\":\"09:00:43\",\"out\":\"17:01:32\"}]',NULL,'present',NULL,'2026-10-30 03:30:43','2026-10-30 11:31:32',10,0,481,8.02,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(165,15,'2026-10-30','09:05:23','17:08:06','[{\"in\":\"09:05:23\",\"out\":\"17:08:06\"}]',NULL,'late',NULL,'2026-10-30 03:35:23','2026-10-30 11:38:06',15,0,483,8.05,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(166,17,'2026-10-30','09:05:47','17:10:49','[{\"in\":\"09:05:47\",\"out\":\"17:10:49\"}]',NULL,'late',NULL,'2026-10-30 03:35:47','2026-10-30 11:40:49',15,0,485,8.08,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(167,14,'2026-10-30','09:12:21','17:07:50','[{\"in\":\"09:12:21\",\"out\":\"17:07:50\"}]',NULL,'late',NULL,'2026-10-30 03:42:21','2026-10-30 11:37:50',22,0,475,7.92,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(168,12,'2026-10-30','09:14:34','17:31:11','[{\"in\":\"09:14:34\",\"out\":\"17:31:11\"}]',NULL,'late',NULL,'2026-10-30 03:44:34','2026-10-30 12:01:11',24,0,497,8.28,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(169,13,'2026-10-30','09:19:43','14:19:06','[{\"in\":\"09:19:43\",\"out\":\"14:19:06\"}]','Approved Half Day','half_day',NULL,'2026-10-30 03:49:43','2026-10-30 08:49:06',29,160,299,4.98,'half_day',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(170,15,'2026-11-02','09:00:55','17:16:37','[{\"in\":\"09:00:55\",\"out\":\"17:16:37\"}]',NULL,'present',NULL,'2026-11-02 03:30:55','2026-11-02 11:46:37',10,0,496,8.27,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(171,17,'2026-11-02','09:01:53','17:17:40','[{\"in\":\"09:01:53\",\"out\":\"17:17:40\"}]',NULL,'late',NULL,'2026-11-02 03:31:53','2026-11-02 11:47:40',11,0,496,8.27,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(172,16,'2026-11-02','09:02:40','17:05:24','[{\"in\":\"09:02:40\",\"out\":\"17:05:24\"}]',NULL,'late',NULL,'2026-11-02 03:32:40','2026-11-02 11:35:24',12,0,483,8.05,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(173,12,'2026-11-02','09:07:02','17:17:42','[{\"in\":\"09:07:02\",\"out\":\"17:17:42\"}]',NULL,'late',NULL,'2026-11-02 03:37:02','2026-11-02 11:47:42',17,0,491,8.18,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(174,20,'2026-11-02','09:19:33','17:01:24','[{\"in\":\"09:19:33\",\"out\":\"17:01:24\"}]',NULL,'late',NULL,'2026-11-02 03:49:33','2026-11-02 11:31:24',29,0,462,7.70,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(175,13,'2026-11-02','09:27:07','17:26:07','[{\"in\":\"09:27:07\",\"out\":\"17:26:07\"}]',NULL,'late',NULL,'2026-11-02 03:57:07','2026-11-02 11:56:07',37,0,479,7.98,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(176,16,'2026-11-03','09:00:05',NULL,'[{\"in\":\"09:00:05\",\"out\":null}]',NULL,'present',NULL,'2026-11-03 03:30:05','2026-11-03 03:30:05',10,0,0,0.00,'completed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(177,20,'2026-11-03','09:12:35',NULL,'[{\"in\":\"09:12:35\",\"out\":null}]',NULL,'late',NULL,'2026-11-03 03:42:35','2026-11-03 03:42:35',22,0,0,0.00,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(178,15,'2026-11-03','09:20:03',NULL,'[{\"in\":\"09:20:03\",\"out\":null}]',NULL,'late',NULL,'2026-11-03 03:50:03','2026-11-03 03:50:03',30,0,0,0.00,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(179,17,'2026-11-03','09:21:05',NULL,'[{\"in\":\"09:21:05\",\"out\":null}]',NULL,'late',NULL,'2026-11-03 03:51:05','2026-11-03 03:51:05',31,0,0,0.00,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL),(180,13,'2026-11-03','09:26:18',NULL,'[{\"in\":\"09:26:18\",\"out\":null}]',NULL,'late',NULL,'2026-11-03 03:56:18','2026-11-03 03:56:18',36,0,0,0.00,'delayed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,'manual',NULL,NULL);
/*!40000 ALTER TABLE `attendance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendance_requests`
--

DROP TABLE IF EXISTS `attendance_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attendance_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `requested_work_mode` enum('wfh','on_site_exception') NOT NULL DEFAULT 'wfh',
  `reason` text NOT NULL,
  `supporting_details` text DEFAULT NULL,
  `requested_check_in_time` time NOT NULL,
  `approved_check_in_time` time DEFAULT NULL,
  `approved_check_out_time` time DEFAULT NULL,
  `status` enum('pending','approved','rejected','cancelled','checked_in','expired') NOT NULL DEFAULT 'pending',
  `approver_id` int(11) DEFAULT NULL,
  `decision_timestamp` datetime DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `actual_check_in` time DEFAULT NULL,
  `submitted_lat` decimal(10,8) DEFAULT NULL,
  `submitted_lng` decimal(11,8) DEFAULT NULL,
  `submitted_dist` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_emp_date` (`employee_id`,`attendance_date`),
  KEY `idx_status` (`status`),
  KEY `idx_date_status` (`attendance_date`,`status`),
  KEY `fk_att_req_approver` (`approver_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance_requests`
--

LOCK TABLES `attendance_requests` WRITE;
/*!40000 ALTER TABLE `attendance_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `attendance_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendance_summary_tokens`
--

DROP TABLE IF EXISTS `attendance_summary_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attendance_summary_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `attendance_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `shift_date` date NOT NULL,
  `scheduled_checkout_time` time NOT NULL,
  `token_hash` varchar(64) NOT NULL,
  `status` enum('pending','completed','expired','cancelled') NOT NULL DEFAULT 'pending',
  `email_sent` tinyint(1) NOT NULL DEFAULT 0,
  `email_sent_at` datetime DEFAULT NULL,
  `email_delivery_status` enum('pending','sent','failed') NOT NULL DEFAULT 'pending',
  `email_error_message` varchar(255) DEFAULT NULL,
  `email_attempts` int(11) NOT NULL DEFAULT 0,
  `last_reminder_sent_at` datetime DEFAULT NULL,
  `reminder_count` int(11) NOT NULL DEFAULT 0,
  `expires_at` datetime NOT NULL,
  `completed_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_attendance_token` (`attendance_id`),
  KEY `idx_token_hash` (`token_hash`),
  KEY `idx_employee_date` (`employee_id`,`shift_date`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance_summary_tokens`
--

LOCK TABLES `attendance_summary_tokens` WRITE;
/*!40000 ALTER TABLE `attendance_summary_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `attendance_summary_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `daily_work_summaries`
--

DROP TABLE IF EXISTS `daily_work_summaries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `daily_work_summaries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `attendance_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `work_summary` text NOT NULL,
  `completed_tasks` text NOT NULL,
  `pending_tasks` text DEFAULT NULL,
  `additional_notes` text DEFAULT NULL,
  `submission_source` varchar(30) NOT NULL DEFAULT 'modal',
  `submitted_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_attendance_summary` (`attendance_id`),
  KEY `idx_attendance` (`attendance_id`),
  KEY `idx_employee_date` (`employee_id`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `daily_work_summaries`
--

LOCK TABLES `daily_work_summaries` WRITE;
/*!40000 ALTER TABLE `daily_work_summaries` DISABLE KEYS */;
/*!40000 ALTER TABLE `daily_work_summaries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `department` varchar(50) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `status` enum('active','inactive','on_leave') DEFAULT 'active',
  `work_mode` enum('on_site','wfh') NOT NULL DEFAULT 'on_site',
  `shift_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `avatar` varchar(255) DEFAULT NULL,
  `aadhar_image` varchar(255) DEFAULT NULL,
  `aadhar_verified` tinyint(1) DEFAULT 0,
  `employee_id` varchar(10) DEFAULT NULL,
  `first_login_completed` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_employees_shift` (`shift_id`),
  KEY `fk_employees_role` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES (12,'Prem','Kumar M','tamilaninstamedia1@gmail.com','$2y$10$2FI6rX1WvXjYN9MQZqh1B.bo78aBuCydxzn2onwnE5mVjdl8tZpGm','+918098830937','HR',2,'Funder',NULL,0.00,'2026-06-29','active','on_site',1,'2026-06-29 02:08:05','2026-10-15 11:46:59','avatar_12_1789706469.jpg','aadhar_12_1782700791.png',2,'EMP-001',1),(13,'Karthick','Raja','kr8654460@gmail.com','$2y$10$x82W2gGNyLxbiRLZYcP.bu9UAtu1PjMpgqR9K1jiVyYk2Mlu7PZHS','8525813179','Engineering',2,'Co-founder & CEO',NULL,0.00,'2026-06-29','active','on_site',1,'2026-06-29 03:09:30','2026-09-18 11:45:37','avatar_13_1782724870.jpg',NULL,0,'EMP-002',1),(14,'Shanmuga','Priya','priya30shanmuga@gmail.com','$2y$10$9nHrdAeIUgTVQs3.lJxqjO9HEWge1aP364viP2sIbS3ZDw3R8lwVG','9952426770','Engineering',2,'Juniour Devloper',NULL,12000.00,'2026-06-29','active','on_site',1,'2026-06-29 03:12:49','2026-09-30 06:57:04',NULL,'aadhar_14_1782724367.jpg',2,'EMP-003',1),(15,'Jeya','Chandra','jeyachandra185@gmail.com','$2y$10$GA1KbUw0aKClkzldKgxabOKXLp0EbHU9fmLqPAiBVxLop/4eCZ8yK','9087407463','Engineering',2,'Junior SEO Executive',NULL,11000.00,'2026-06-29','active','on_site',1,'2026-06-29 03:19:51','2026-10-07 04:39:22',NULL,'aadhar_15_1782727821.jpg',2,'EMP-004',1),(16,'Abirami','.R','abiramiramar07@gmail.com','$2y$10$VjpfHS293atqy9VG5S3lZehOO8iKlH97ofaLL2kDtS5yTmIr98LZe','7502121506','Engineering',2,'Junior  SEO Executive',NULL,13500.00,'2026-06-29','active','on_site',1,'2026-06-29 03:23:02','2026-10-23 03:47:09',NULL,'aadhar_16_1782728193.jpg',2,'EMP-005',1),(17,'Kavitha','A','kavikavitha98861@gmail.com','$2y$10$yncLJztNFaCX/OOc.QsyC.m1mJ1MSWVcDNHz2sg5.EJVtDJxt9vm6','7548835595','Engineering',2,'Junior   Assit Devloper',NULL,11500.00,'2026-06-29','active','on_site',1,'2026-06-29 03:24:48','2026-09-25 04:18:29','avatar_17_1789703129.jpg','aadhar_17_1789703686.jpg',2,'EMP-006',1),(18,'Priya','Dharshini R','priyatheangel920@gmail.com','$2y$10$1fOaCYLjH.z85.WqmhC3nuGCERO55iUiWr873gZeMDEvytrUliYDK','8111067718','Design',2,'Front-End Devloper',NULL,0.00,'2026-06-29','inactive','on_site',1,'2026-06-29 03:27:18','2026-09-18 03:45:06',NULL,NULL,0,'EMP-007',0),(20,'Divyasri','.K','divyakutty6294@gmail.com','$2y$10$hRwThrSKiZzLetYGKpY0f.PUPK2srK4X49Z7ilLoZHhhf0dKde0re','733 903 6114','Support',2,'TeleCaller',NULL,7000.00,'2026-07-27','active','on_site',1,'2026-07-27 03:07:17','2026-10-15 03:51:27','avatar_20_1785122469.jpg','aadhar_20_1785122462.jpg',2,'EMP-008',1),(21,'Anantha','Lisha M','vijaylisha210@gmail.com','$2y$10$owJIZAqfv1onUObA/5WR3uQnEAoX.u0y5.3yBtl5rGKAUe.yux/Ye','93449 83531','Design',2,'Video Graphy',NULL,6000.00,'2026-10-21','active','on_site',1,'2026-10-22 08:02:22','2026-10-22 08:06:43',NULL,NULL,0,'EMP-009',1),(50,'System','Administrator','cloudaspire2025@gmail.com','$2y$10$5juBxMGdJALcinsqM9oXLu2RgGyFKnwHQv0yjpsRKgPIP71pT009C',NULL,'Executive',1,'Super Administrator',NULL,NULL,NULL,'active','on_site',NULL,'2026-10-28 11:25:42','2026-11-03 05:24:58',NULL,'aadhar_50_9303667732eb45a692f7a8198f97eb2d.jpg',1,'EMP-ADM-00',1);
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leave_balances`
--

DROP TABLE IF EXISTS `leave_balances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leave_balances` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `leave_type` enum('annual','sick','casual','maternity','paternity') NOT NULL,
  `year` int(11) NOT NULL,
  `total_days` int(11) NOT NULL,
  `used_days` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `emp_leave_year` (`employee_id`,`leave_type`,`year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_balances`
--

LOCK TABLES `leave_balances` WRITE;
/*!40000 ALTER TABLE `leave_balances` DISABLE KEYS */;
/*!40000 ALTER TABLE `leave_balances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leave_code_history`
--

DROP TABLE IF EXISTS `leave_code_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leave_code_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `leave_id` int(11) NOT NULL,
  `leave_code` varchar(50) DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `performed_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `leave_id` (`leave_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_code_history`
--

LOCK TABLES `leave_code_history` WRITE;
/*!40000 ALTER TABLE `leave_code_history` DISABLE KEYS */;
INSERT INTO `leave_code_history` VALUES (1,1,'LV-IWBRE3','used',1,'2026-09-18 03:54:46'),(2,2,'LV-F91PRG','used',1,'2026-09-25 05:15:13'),(3,5,'LV-BM0QGP','used',1,'2026-09-29 11:18:16'),(4,5,'LV-BM0QGP','used',1,'2026-09-29 11:18:33'),(5,5,'LV-BM0QGP','used',1,'2026-09-29 11:18:35'),(6,4,'LV-VHR510','used',1,'2026-09-29 11:18:38'),(7,3,'LV-U4N08B','used',1,'2026-09-29 11:18:41'),(8,6,'LV-39I2HR','used',1,'2026-09-30 02:59:33'),(9,11,'LV-MLNHPE','used',1,'2026-09-30 18:23:51'),(10,12,'LV-VGRKAT','used',1,'2026-10-03 05:28:44'),(11,14,'LV-DBZFQ9','used',1,'2026-10-07 04:29:42'),(12,15,'LV-60XRSH','used',1,'2026-10-07 04:44:08'),(13,16,'LV-DMQ37N','used',1,'2026-10-08 03:00:07'),(14,17,'LV-KYFX3U','used',1,'2026-10-09 09:25:26'),(15,20,'LV-GO9D2K','used',1,'2026-10-16 14:47:26'),(16,19,'LV-PV3CB4','used',1,'2026-10-16 14:47:29'),(17,21,'LV-RSZ8YC','used',1,'2026-10-21 04:11:14'),(18,23,'LV-AC2I3W','used',1,'2026-10-26 03:44:39'),(19,22,'LV-8LMP1Q','used',1,'2026-10-26 03:44:44'),(20,24,'LV-WEJD80','used',1,'2026-10-27 06:11:56'),(21,25,'LV-EIPLQ7','used',1,'2026-10-27 06:12:05'),(22,26,'LV-ZI9CLE','used',1,'2026-10-27 08:48:17');
/*!40000 ALTER TABLE `leave_code_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leave_requests`
--

DROP TABLE IF EXISTS `leave_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leave_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `leave_type` enum('annual','sick','casual','early_departure','maternity','paternity') NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `days` int(11) NOT NULL,
  `reason` text NOT NULL,
  `early_departure_time` time DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `rejection_reason` text DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `leave_code` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `employee_id` (`employee_id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_requests`
--

LOCK TABLES `leave_requests` WRITE;
/*!40000 ALTER TABLE `leave_requests` DISABLE KEYS */;
INSERT INTO `leave_requests` VALUES (1,20,'casual','2026-09-18','2026-09-18',1,'Asking Persnoal Levae',NULL,'approved',NULL,1,'2026-09-18 03:54:42','LV-IWBRE3','2026-09-18 03:54:42'),(2,13,'casual','2026-09-25','2026-09-25',1,'My family some problem so i want leave today then head ache So Kindly request',NULL,'approved',NULL,1,'2026-09-25 05:15:10','LV-F91PRG','2026-09-25 04:38:37'),(3,15,'casual','2026-09-29','2026-09-29',1,'Medical issues',NULL,'approved',NULL,1,'2026-09-29 11:18:30','LV-U4N08B','2026-09-29 08:40:10'),(4,14,'early_departure','2026-09-29','2026-09-29',1,'Half day leave','14:10:00','approved',NULL,1,'2026-09-29 11:18:22','LV-VHR510','2026-09-29 08:42:32'),(5,17,'early_departure','2026-09-29','2026-09-29',1,'Half day leave','02:05:00','approved',NULL,1,'2026-09-29 11:18:12','LV-BM0QGP','2026-09-29 08:44:00'),(6,20,'casual','2026-09-30','2026-09-30',1,'I have personal work to do',NULL,'approved',NULL,1,'2026-09-30 02:59:30','LV-39I2HR','2026-09-30 02:07:53'),(7,17,'casual','2026-09-30','2026-10-05',6,'Going to home',NULL,'approved',NULL,1,'2026-09-30 06:56:22','LV-G2ALX1','2026-09-30 06:51:59'),(11,14,'casual','2026-09-30','2026-09-30',1,'Going to home',NULL,'approved',NULL,1,'2026-09-30 18:23:22','LV-MLNHPE','2026-09-30 08:52:32'),(12,20,'casual','2026-10-03','2026-10-03',1,'Personal leave',NULL,'approved',NULL,1,'2026-10-03 03:55:15','LV-VGRKAT','2026-10-03 01:53:28'),(13,20,'sick','2026-10-06','2026-10-06',1,'Cold and  cough',NULL,'rejected',NULL,1,'2026-10-07 04:29:47',NULL,'2026-10-06 01:19:41'),(14,20,'sick','2026-10-07','2026-10-07',1,'Sick leave',NULL,'approved',NULL,1,'2026-10-07 04:29:40','LV-DBZFQ9','2026-10-07 02:12:26'),(15,12,'sick','2026-10-07','2026-10-07',1,'I Need 1\r\n Hour  Sick Levae',NULL,'approved',NULL,1,'2026-10-07 04:44:05','LV-60XRSH','2026-10-07 04:43:27'),(16,20,'sick','2026-10-08','2026-10-08',1,'Sick leave',NULL,'approved',NULL,1,'2026-10-08 03:00:03','LV-DMQ37N','2026-10-08 01:47:28'),(17,12,'early_departure','2026-10-09','2026-10-09',1,'I Need take rest of My mind','14:32:00','approved',NULL,1,'2026-10-09 09:09:25','LV-KYFX3U','2026-10-09 09:03:43'),(18,20,'sick','2026-10-12','2026-10-12',1,'One day personal leave',NULL,'rejected',NULL,1,'2026-10-12 01:51:39',NULL,'2026-10-12 01:42:59'),(19,20,'casual','2026-10-12','2026-10-12',1,'Some family  issues',NULL,'approved',NULL,1,'2026-10-12 06:28:34','LV-PV3CB4','2026-10-12 05:32:03'),(20,20,'casual','2026-10-16','2026-10-16',1,'For bank work',NULL,'approved',NULL,1,'2026-10-16 14:47:24','LV-GO9D2K','2026-10-16 02:06:53'),(21,20,'sick','2026-10-21','2026-10-21',1,'Fever and cold cough',NULL,'approved',NULL,1,'2026-10-21 04:11:09','LV-RSZ8YC','2026-10-21 02:04:43'),(22,20,'sick','2026-10-24','2026-10-24',1,'Due to high fever',NULL,'approved',NULL,1,'2026-10-24 04:35:15','LV-8LMP1Q','2026-10-24 01:11:54'),(23,20,'casual','2026-10-26','2026-10-26',1,'Personal Leave',NULL,'approved',NULL,1,'2026-10-26 03:44:33','LV-AC2I3W','2026-10-26 03:44:33'),(24,15,'casual','2026-10-27','2026-10-27',1,'Personal leave',NULL,'approved',NULL,1,'2026-10-27 06:11:41','LV-WEJD80','2026-10-27 03:36:55'),(25,20,'casual','2026-10-27','2026-10-27',1,'Transportation issues',NULL,'approved',NULL,1,'2026-10-27 06:11:46','LV-EIPLQ7','2026-10-27 04:07:05'),(26,17,'casual','2026-10-27','2026-10-27',1,'Going to Chennai',NULL,'approved',NULL,1,'2026-10-27 08:48:14','LV-ZI9CLE','2026-10-27 08:47:35'),(27,14,'sick','2026-10-28','2026-10-28',1,'Half Day Levae',NULL,'approved',NULL,1,'2026-10-28 09:07:06','LV-7EW4CO','2026-10-28 09:07:06'),(28,12,'casual','2026-10-29','2026-10-29',1,'I need leave for my personal helth issue',NULL,'approved',NULL,1,'2026-10-29 03:37:10','LV-ES7LDG','2026-10-29 03:36:01'),(29,20,'casual','2026-10-29','2026-10-29',1,'Some family issues',NULL,'approved',NULL,1,'2026-10-30 03:51:30','LV-C5ZY7M','2026-10-29 05:11:37'),(30,21,'casual','2026-10-29','2026-10-29',1,'Hospital',NULL,'approved',NULL,1,'2026-10-30 03:51:22','LV-8MQYSV','2026-10-29 05:58:31'),(32,12,'sick','2026-11-03','2026-11-03',1,'Due to personal reasons',NULL,'pending',NULL,NULL,NULL,NULL,'2026-11-03 03:50:13');
/*!40000 ALTER TABLE `leave_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login_attempts`
--

DROP TABLE IF EXISTS `login_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(45) NOT NULL,
  `timestamp` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ip` (`ip`),
  KEY `idx_timestamp` (`timestamp`)
) ENGINE=InnoDB AUTO_INCREMENT=251 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_attempts`
--

LOCK TABLES `login_attempts` WRITE;
/*!40000 ALTER TABLE `login_attempts` DISABLE KEYS */;
INSERT INTO `login_attempts` VALUES (246,'49.37.221.174',1793679234),(247,'49.37.221.174',1793679239),(248,'49.37.221.174',1793679245),(249,'49.37.221.174',1793679246),(250,'49.37.221.174',1793679254);
/*!40000 ALTER TABLE `login_attempts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `monthly_working_days`
--

DROP TABLE IF EXISTS `monthly_working_days`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `monthly_working_days` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `year` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `day_type` enum('working_day','weekly_off','holiday') NOT NULL DEFAULT 'working_day',
  `title` varchar(100) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `date` (`date`),
  UNIQUE KEY `uniq_working_day_date` (`date`),
  KEY `idx_year_month` (`year`,`month`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `monthly_working_days`
--

LOCK TABLES `monthly_working_days` WRITE;
/*!40000 ALTER TABLE `monthly_working_days` DISABLE KEYS */;
/*!40000 ALTER TABLE `monthly_working_days` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `passkey_credentials`
--

DROP TABLE IF EXISTS `passkey_credentials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `passkey_credentials` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_type` enum('employee','admin') NOT NULL DEFAULT 'employee',
  `user_id` int(11) NOT NULL,
  `credential_id` varchar(255) NOT NULL,
  `public_key` text NOT NULL,
  `user_handle` varchar(64) DEFAULT NULL,
  `sign_count` bigint(20) unsigned DEFAULT 0,
  `transports` varchar(255) DEFAULT NULL,
  `device_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_used_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_credential_id` (`credential_id`),
  KEY `idx_user_type_id` (`user_type`,`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `passkey_credentials`
--

LOCK TABLES `passkey_credentials` WRITE;
/*!40000 ALTER TABLE `passkey_credentials` DISABLE KEYS */;
INSERT INTO `passkey_credentials` VALUES (2,'employee',14,'PP4SzeCAuOMAjbnQOpmG1g','-----BEGIN PUBLIC KEY-----\nMFkwEwYHKoZIzj0CAQYIKoZIzj0DAQcDQgAESXf8IUL8NIkpqTnXjkzHRgEWBqOR\n6IjrNcGnPhIzdi+2whWytieydcF4vS2wxCJ8cSfJNCq0CWhM3i4yQMKYXA==\n-----END PUBLIC KEY-----\n','employee_14',0,'[\"hybrid\",\"internal\"]','Android Phone (Fingerprint / PIN)','2026-09-18 07:10:00','2026-09-18 14:22:47'),(3,'employee',15,'c6jN5UMh6j0JyLo9GL8F0g','-----BEGIN PUBLIC KEY-----\nMFkwEwYHKoZIzj0CAQYIKoZIzj0DAQcDQgAEqOXr/lP0VNDChwLvd2JLipwuDcdP\nFJCXYp37Fn5wnppnSd5J0jDAoQs0w3Me8YT2UYfl+RRtaW0osq1dp24lkQ==\n-----END PUBLIC KEY-----\n','employee_15',0,'[\"hybrid\",\"internal\"]','Android Phone (Fingerprint / PIN)','2026-09-18 07:11:35','2026-09-18 07:11:51'),(4,'employee',13,'4Lx49Yb5RKE0Xqn384CI0A','-----BEGIN PUBLIC KEY-----\nMFkwEwYHKoZIzj0CAQYIKoZIzj0DAQcDQgAE016qLWl6VF9QbIPTCRivlSmDYT7B\nULXYqx3DJJrfaDcLzceONjT7fM3jP5JKoFQlhx5QisEG3FEa8HfI0+THEg==\n-----END PUBLIC KEY-----\n','employee_13',0,'[\"hybrid\",\"internal\"]','Android Phone (Fingerprint / PIN)','2026-09-18 11:47:40','2026-09-29 03:30:25'),(5,'admin',1,'VcFKsGZp-UYFyTYA5_YjhQ','-----BEGIN PUBLIC KEY-----\nMFkwEwYHKoZIzj0CAQYIKoZIzj0DAQcDQgAE6j8QQJ+wCNbNWK1Ja/DvJDYjQqh0\nYkfeoHIr9ZIku+LeuOftSCK9/F7j/HVRZ1rf57wigBNdlkUYsR0Rpe0m7Q==\n-----END PUBLIC KEY-----\n','admin_1',0,'[\"hybrid\",\"internal\"]','Windows PC (Windows Hello)','2026-09-22 03:28:11','2026-09-28 11:31:33'),(6,'employee',20,'sezFqexdnkaiE7omBJz5VA','-----BEGIN PUBLIC KEY-----\nMFkwEwYHKoZIzj0CAQYIKoZIzj0DAQcDQgAEK37skWjy4otOnvXESnqEbQRWzauM\nHNZ0h4cza+b9ZwqTvWFv1JYr49DleH6NZryrWR+LoGBb2QVV7P9KG1Meuw==\n-----END PUBLIC KEY-----\n','employee_20',0,'[\"hybrid\",\"internal\"]','Android Phone (Fingerprint / PIN)','2026-09-23 04:43:24','2026-09-29 06:52:06'),(7,'employee',20,'K-NrcS1fgkdgEPeiF_aDWQ','-----BEGIN PUBLIC KEY-----\nMFkwEwYHKoZIzj0CAQYIKoZIzj0DAQcDQgAE+W1e/H6ISlBTg4wQSlE5Q7zxuNAd\nD8MQHwq+30TQsVc+QeTjGvWcs1uk6nDZrzBo3ffloi+5CvL/UaWba6vazA==\n-----END PUBLIC KEY-----\n','employee_20',0,'[\"hybrid\",\"internal\"]','Android Phone (Fingerprint / PIN)','2026-09-24 11:58:58','2026-09-24 11:58:58'),(8,'employee',12,'ASk9sKCqZOIcRwMAOPzoaw','-----BEGIN PUBLIC KEY-----\nMFkwEwYHKoZIzj0CAQYIKoZIzj0DAQcDQgAE/8OX0rGeJ/qeJ6LbxMw4tmDc7GQm\nXrwbXqogGmKDBfoQM8IXrsTu+109EoKh3PMZS4KcsLuddWoImvJoB8mD+Q==\n-----END PUBLIC KEY-----\n','employee_12',0,'[\"hybrid\",\"internal\"]','Android Phone (Fingerprint / PIN)','2026-09-29 11:09:50','2026-10-30 03:44:28'),(9,'admin',1,'_0NrHL9uLEoeJBsg8Cj95g','-----BEGIN PUBLIC KEY-----\nMFkwEwYHKoZIzj0CAQYIKoZIzj0DAQcDQgAEoN+ysJsENRDUlTOCWhP3Ttk1xnx3\noP0VdyktyJoqrJSCqa0s/D/y6ZJ7/vSxEpLi/l/DIU6XC0XVsEk1/8ZOBA==\n-----END PUBLIC KEY-----\n','admin_1',0,'[\"hybrid\",\"internal\"]','Android Phone (Fingerprint / PIN)','2026-09-29 11:20:04','2026-10-30 03:51:09'),(10,'employee',20,'w1NF6xlm5Uwzp7hjr51J9Q','-----BEGIN PUBLIC KEY-----\nMFkwEwYHKoZIzj0CAQYIKoZIzj0DAQcDQgAEZkHxHa+iQVh44kqCOhOuRSaGwLof\nMz9iUCj3MrdLkHZYSOgi7WuyyuBSzEMzNtmcbmn59KokGhD6yvLZU/VbCw==\n-----END PUBLIC KEY-----\n','employee_20',0,'[\"hybrid\",\"internal\"]','Android Phone (Fingerprint / PIN)','2026-10-28 06:02:14','2026-10-28 06:02:14');
/*!40000 ALTER TABLE `passkey_credentials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `module` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'Attendance: Approve / Reject WFH & Exceptions','attendance.approve_requests','Attendance','Authorize or reject employee work from home and on-site location exception requests','2026-10-28 09:24:09'),(2,'Employees: Create Employee','employees.create','Employees','Add new employees to the workforce directory','2026-10-23 05:20:26'),(3,'Employees: Edit Employee','employees.edit','Employees','Modify employee profile, compensation, and work mode','2026-10-23 05:20:26'),(4,'Employees: Delete Employee','employees.delete','Employees','Remove employees and revoke portal access','2026-10-23 05:20:26'),(5,'Attendance: View Records','attendance.view','Attendance','View daily and monthly attendance logs','2026-10-23 05:20:26'),(6,'Attendance: Manage & Edit','attendance.manage','Attendance','Edit check-in, check-out, status, and manual overrides','2026-10-23 05:20:26'),(7,'Attendance: Review Work Summaries','attendance.review_summaries','Attendance','Review employee daily work summaries and blockers','2026-10-23 05:20:26'),(8,'Attendance: Export CSV','attendance.export','Attendance','Export attendance reports to CSV and spreadsheets','2026-10-23 05:20:26'),(9,'Tasks: View All Tasks','tasks.view','Tasks','View tasks across all departments and employees','2026-10-23 05:20:26'),(10,'Tasks: Create & Assign','tasks.create','Tasks','Create new tasks and assign to employees','2026-10-23 05:20:26'),(11,'Tasks: Manage & Update','tasks.manage','Tasks','Update task status, priority, and verify completion','2026-10-23 05:20:26'),(12,'Projects: View Submissions','projects.view','Projects','Review employee submitted drive links and deliverables','2026-10-23 05:20:26'),(13,'Projects: Manage & Review','projects.manage','Projects','Approve, request changes, or reject submitted projects','2026-10-23 05:20:26'),(14,'Leaves: View Requests','leaves.view','Leaves','View leave applications and calendar balances','2026-10-23 05:20:26'),(15,'Leaves: Approve / Reject','leaves.manage','Leaves','Approve or reject leave requests and generate leave codes','2026-10-23 05:20:26'),(16,'Shifts: View & Configure','shifts.view','Shifts','View shift schedules and grace thresholds','2026-10-23 05:20:26'),(17,'Shifts: Manage Shifts','shifts.manage','Shifts','Create, edit, and activate operational shift timings','2026-10-23 05:20:26'),(18,'Reports: View Analytics','reports.view','Reports','Access executive dashboards, KPI telemetry, and analytics','2026-10-23 05:20:26'),(19,'Reports: Export Reports','reports.export','Reports','Export detailed workforce analytics','2026-10-23 05:20:26'),(20,'Settings: View Settings','settings.view','Settings','View system settings and console configurations','2026-10-23 05:20:26'),(21,'Settings: Manage Settings','settings.manage','Settings','Manage branding, SMTP, and office geofence locations','2026-10-23 05:20:26'),(22,'Roles: View Roles','roles.view','Role Management','View roles and their assigned permission matrices','2026-10-23 05:20:26'),(23,'Roles: Manage Roles','roles.manage','Role Management','Create and edit roles and customize permission allocations','2026-10-23 05:20:26'),(24,'Employees: View Directory','employees.view','Employees','View employee listings, profile cards, and details','2026-10-28 10:03:06');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `project_status_updates`
--

DROP TABLE IF EXISTS `project_status_updates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `project_status_updates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `status` varchar(40) NOT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project_status_updates`
--

LOCK TABLES `project_status_updates` WRITE;
/*!40000 ALTER TABLE `project_status_updates` DISABLE KEYS */;
INSERT INTO `project_status_updates` VALUES (1,1,'submitted','Project submitted by employee','2026-10-24 04:44:55'),(4,1,'completed',NULL,'2026-10-25 10:21:54');
/*!40000 ALTER TABLE `project_status_updates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `drive_link` varchar(2048) NOT NULL,
  `status` varchar(40) NOT NULL DEFAULT 'submitted',
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `employee_id` (`employee_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `projects`
--

LOCK TABLES `projects` WRITE;
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
INSERT INTO `projects` VALUES (1,21,'Ai Diwali add',NULL,'https://drive.google.com/file/d/1-KAhzhO6LVs31bgjPVgBkxjqZstR9lUf/view?usp=drivesdk','completed','2026-10-24 04:44:55','2026-10-25 10:21:54');
/*!40000 ALTER TABLE `projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_permissions`
--

DROP TABLE IF EXISTS `role_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `permission_id` (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permissions`
--

LOCK TABLES `role_permissions` WRITE;
/*!40000 ALTER TABLE `role_permissions` DISABLE KEYS */;
INSERT INTO `role_permissions` VALUES (1,1),(1,2),(1,3),(1,4),(1,5),(1,6),(1,7),(1,8),(1,9),(1,10),(1,11),(1,12),(1,13),(1,14),(1,15),(1,16),(1,17),(1,18),(1,19),(1,20),(1,21),(1,22),(1,23),(1,24),(2,1),(2,2),(2,3),(2,5),(2,6),(2,7),(2,8),(2,14),(2,15),(2,16),(2,17),(2,18),(2,19),(2,24),(3,1),(3,5),(3,7),(3,9),(3,10),(3,11),(3,12),(3,13),(3,18),(4,1),(4,5),(4,7),(4,9),(4,12),(4,14),(4,18),(5,5),(5,9),(5,12),(5,14),(6,5),(6,9);
/*!40000 ALTER TABLE `role_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Super Administrator','super_admin','Full root access to all system functions, security controls, and workforce modules.',1,'2026-10-23 05:20:26','2026-10-23 05:20:26'),(2,'HR Operations Manager','hr_manager','Manages employee directory, attendance verification, leave approvals, shifts, and daily work summaries.',1,'2026-10-23 05:20:26','2026-10-28 10:03:06'),(3,'Manager','manager','Assigns and reviews daily employee tasks, project deliverables, and reviews work summaries.',1,'2026-10-23 05:20:26','2026-10-28 10:03:06'),(4,'Department Supervisor','supervisor',NULL,1,'2026-10-23 05:20:26','2026-10-28 12:04:02'),(5,'Staff','staff','Standard corporate staff members with self-service capabilities.',1,'2026-10-28 10:03:06','2026-10-28 12:03:54'),(6,'Member','member','Team member with access to attendance, assigned tasks, and personal account profile.',1,'2026-10-28 10:03:06','2026-10-28 10:03:06');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shift_settings`
--

DROP TABLE IF EXISTS `shift_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shift_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shift_name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `checkin_start` time NOT NULL,
  `checkin_end` time NOT NULL,
  `checkout_time` time NOT NULL,
  `grace_minutes` int(11) DEFAULT 0,
  `late_threshold` int(11) DEFAULT 0,
  `early_exit_threshold` int(11) DEFAULT 0,
  `color` varchar(20) DEFAULT '#4f46e5',
  `is_active` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shift_settings`
--

LOCK TABLES `shift_settings` WRITE;
/*!40000 ALTER TABLE `shift_settings` DISABLE KEYS */;
INSERT INTO `shift_settings` VALUES (1,'Regular Shift','','08:50:00','09:10:00','17:00:00',10,10,10,'#0284c7',1,'2026-06-28 16:27:53','2026-10-16 14:48:09');
/*!40000 ALTER TABLE `shift_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `site_settings`
--

DROP TABLE IF EXISTS `site_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_name` varchar(120) NOT NULL,
  `tagline` varchar(255) DEFAULT NULL,
  `brand_color` varchar(20) DEFAULT '#4f46e5',
  `support_email` varchar(255) DEFAULT NULL,
  `support_phone` varchar(50) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `favicon` varchar(255) DEFAULT NULL,
  `smtp_host` varchar(255) DEFAULT NULL,
  `smtp_port` smallint(5) unsigned NOT NULL DEFAULT 587,
  `smtp_encryption` varchar(10) NOT NULL DEFAULT 'tls',
  `smtp_username` varchar(255) DEFAULT NULL,
  `smtp_password` text DEFAULT NULL,
  `smtp_from_email` varchar(255) DEFAULT NULL,
  `smtp_from_name` varchar(120) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `office_name` varchar(150) NOT NULL DEFAULT 'Cloud Aspire Headquarters',
  `office_address` varchar(255) DEFAULT NULL,
  `office_latitude` decimal(10,8) DEFAULT 11.94160000,
  `office_longitude` decimal(11,8) DEFAULT 79.80830000,
  `office_radius_meters` int(11) NOT NULL DEFAULT 100,
  `location_attendance_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `employee_index_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `wfh_early_window_minutes` int(11) NOT NULL DEFAULT 15,
  `wfh_grace_period_minutes` int(11) NOT NULL DEFAULT 15,
  `wfh_late_threshold_minutes` int(11) NOT NULL DEFAULT 30,
  `wfh_auto_reschedule_allowed` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_settings`
--

LOCK TABLES `site_settings` WRITE;
/*!40000 ALTER TABLE `site_settings` DISABLE KEYS */;
INSERT INTO `site_settings` VALUES (3,'Cloud Aspire',NULL,'#4f46e5','cloudaspire2025@gmail.com','+918098830937','logo-96e285f855212224d62cf669.png','favicon-b9f474fa06bc608e0ac6586c.png','smtp.gmail.com',587,'tls','priyalove920@gmail.com','gcm:40A1zQmlr7I4WoQomzqrEwMgVNoAJ17jMfpdoeBCEhHcHPTrlrmQ13K0IeHJxOo=','priyalove920@gmail.com','Cloud Aspire',1,'2026-06-28 14:31:52','2026-11-03 08:35:21','Cloud Aspire Solutions',NULL,11.95309840,79.81345840,100,1,0,15,15,30,0);
/*!40000 ALTER TABLE `site_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tasks`
--

DROP TABLE IF EXISTS `tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `priority` enum('low','medium','high','critical') DEFAULT 'medium',
  `status` enum('sent','in_progress','verify','completed') DEFAULT 'sent',
  `due_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `employee_id` (`employee_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tasks`
--

LOCK TABLES `tasks` WRITE;
/*!40000 ALTER TABLE `tasks` DISABLE KEYS */;
INSERT INTO `tasks` VALUES (1,14,'Introduction to Web Development','How a Website Actually Works?','low','completed','2026-10-01','2026-09-28 04:47:25','2026-09-29 04:08:07'),(2,17,'Understanding The Api','Learn what an API is and why it is used in web development. Understand how a frontend communicates with a backend using an API. Learn the basics of GET and POST requests, JSON data, and simple API responses through a practical example.','low','completed','2026-10-01','2026-09-28 04:50:41','2026-09-29 04:07:42'),(3,20,'Telecalling Fundamentals','Understand the basics of professional telecalling, including how to start a call, introduce yourself, communicate clearly with customers, understand their requirements, handle common objections, explain services, and follow up effectively.','low','completed','2026-10-01','2026-09-28 04:51:48','2026-09-29 04:03:21'),(7,15,'Introduction of SEO for beginners','This topic not only teaching previous days how to you understand and explain your own ideas share your knowledge with practical example in SEO overview','low','completed','2026-09-28','2026-09-28 05:30:12','2026-09-29 04:07:12'),(8,16,'Lead generation min 100 leads','Use Apify Or Google Map Scrab a min 100 leads From Pondicherry','low','completed','2026-09-29','2026-09-28 05:33:29','2026-10-23 06:34:33'),(9,20,'How to Handle Customer Objections & Convert Calls into Leads','Topics:\r\nCustomer “No” சொல்லும்போது எப்படி respond பண்ணுவது\r\nCommon objections – “Not interested”, “Too costly”, “Later call pannunga”\r\nCustomer needs-ஐ identify செய்வது\r\nEffective questioning techniques\r\nTrust & rapport build செய்வது\r\nFollow-up call எப்போது, எப்படி செய்வது\r\nLive Role Play: Telecaller vs Customer\r\nLead conversionக்கு simple closing techniques','low','completed','2026-10-02','2026-09-29 06:46:56','2026-10-05 11:31:04'),(10,17,'How a Website Works – End-to-End','This seminar explains how a website works from the user’s browser to the server and back, covering the complete journey of a web request.\r\nParticipants will understand what happens when a user enters a website URL, including Domain Name, DNS, Browser, HTTP/HTTPS, Frontend, Backend, Server, Database, APIs, Hosting, and Deployment.\r\nKey Topics Covered\r\n🌐 What is a Website?\r\n🔗 Domain Name & DNS – How a domain finds the server\r\n🖥️ Browser – How websites are requested and displayed\r\n📡 HTTP & HTTPS – How browser and server communicate\r\n🎨 Frontend – HTML, CSS & JavaScript\r\n⚙️ Backend – Server-side processing and business logic\r\n🗄️ Database – How website data is stored and retrieved\r\n🔌 APIs – Communication between frontend and backend\r\n☁️ Hosting & Web Servers – Where websites actually run\r\n🚀 Deployment – How a website goes from development to live\r\n🔒 SSL, Authentication & Basic Web Security\r\n🔄 Complete Request → Processing → Response Flow\r\nEnd-to-End Flow\r\nUser → Browser → DNS → Server → Backend → Database → Backend → Server → Browser → Website\r\nBy the end of the seminar, participants will have a clear understanding of what happens behind the screen when they open, interact with, or submit data on a website.\r\nSeminar Outcome\r\nThis session provides a practical foundation for beginners who want to understand Web Development, Full-Stack Development, Hosting, APIs, and modern web applications.','low','completed','2026-10-02','2026-09-29 06:50:03','2026-10-23 06:34:12'),(11,21,'Make A Proepr  Ai Video','this Is Our Script \r\n\r\nCLOUD ASPIRE — DIWALI SPECIAL WEBSITE OFFER\r\n\r\n[00–05 sec | HOOK]\r\n\r\nBusiness இருக்கு… Shop இருக்கு… Visiting card கூட இருக்கு… ஆனா உங்க Business-க்கு ஒரு Website இருக்கா?\r\n\r\n[05–11 sec | OFFER INTRO]\r\n\r\nஇந்த Diwali-ல உங்க Business-ஐ online-ல கொண்டு போக, Cloud Aspire ஒரு Special Offer கொண்டு வந்திருக்கோம்!\r\n\r\n[11–23 sec | PRICE + FEATURES]\r\n\r\nஉங்க Business-க்கான 5 to 7 pages கொண்ட Professional Website, ஒரு வருட Domain, Hosting, SSL Certificate உடன் வெறும் ₹3,499-க்கு!\r\n\r\n[23–34 sec | BENEFITS]\r\n\r\nMobile-responsive design, Technical SEO basics, On-page SEO setup, Contact Form மற்றும் ஒரு வருட Technical Support-ம் கிடைக்கும்!\r\n\r\n\r\n\r\n[40–50 sec | CALL TO ACTION]\r\n\r\nஇந்த Diwali Offer November 15 வரைதான்! உங்க Website-ஐ start பண்ண இப்பவே WhatsApp பண்ணுங்க அல்லது call பண்ணுங்க!\r\n\r\nCloud Aspire Solutions — Let\'s Grow Digitally!\r\n\r\nWhatsApp / Call: 8610439418','high','completed','2026-10-26','2026-10-23 06:39:03','2026-10-24 04:49:02'),(12,21,'Creat a ads creative','🚀 **Unga Business-ku Online Presence Irukka? Ippo Start Pannalaam!** 💙\r\n\r\nOru customer unga business pathi therinjukka virumburanga… avanga Google-la search pannumbodhu, unga business professional-ah theriyanum, illaya? 👀\r\n\r\nAdhukkuthaan oru nalla website important! ✨\r\n\r\n**Cloud Aspire Solutions**-la unga business-ku suitable-ah modern, mobile-friendly website create pannrom.\r\n\r\n💻 Professional Website Design  \r\n📱 Mobile-Friendly Experience  \r\n⚡ Fast & Secure Performance  \r\n📈 Better Online Visibility  \r\n🤝 Unga Business-ku Suitable Solution  \r\n\r\nNeenga small business run pannalum, startup start pannalum, unga online journey-ai strong-ah build pannalaam. 💪\r\n\r\n**Website venum-nu yosichittu irukkeengala? Let\'s make it happen! 🚀**\r\n\r\n📩 **Instagram DM:** @cloud_aspire  \r\n💬 **WhatsApp:** 8610439418','critical','in_progress','2026-10-31','2026-10-28 09:00:07','2026-10-28 09:02:41');
/*!40000 ALTER TABLE `tasks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'qaschyan_emp'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-03 14:15:16
