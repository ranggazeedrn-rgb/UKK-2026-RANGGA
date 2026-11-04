/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.16-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: db_ukk_2026
-- ------------------------------------------------------
-- Server version	10.11.16-MariaDB

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
-- Table structure for table `t_guru`
--

DROP TABLE IF EXISTS `t_guru`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `t_guru` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nip` varchar(30) NOT NULL,
  `nama` varchar(150) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_t_guru_nip` (`nip`),
  UNIQUE KEY `uq_t_guru_email` (`email`),
  UNIQUE KEY `uq_t_guru_user_id` (`user_id`),
  CONSTRAINT `t_guru_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `t_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_guru`
--

LOCK TABLES `t_guru` WRITE;
/*!40000 ALTER TABLE `t_guru` DISABLE KEYS */;
INSERT INTO `t_guru` (`id`, `nip`, `nama`, `email`, `status_aktif`, `user_id`, `created_at`, `updated_at`) VALUES (52,'198001012006011001','Wawan Kurniawan, S.Pd.I','wawan.kurniawan@sekolah.sch.id',1,151,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(53,'198001012006011002','Ai Nuryaidah, S.Pd','ai.nuryaidah@sekolah.sch.id',1,152,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(54,'198001012006011003','Maya Permata Endah, S.Pd','maya.permata@sekolah.sch.id',1,153,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(55,'198001012006011004','Dody Jaenudin, S.Pd.','dody.jaenudin@sekolah.sch.id',1,154,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(56,'198001012006011005','Dedi Kardiman, S.Pd','dedi.kardiman@sekolah.sch.id',1,155,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(57,'198001012006011006','Uca Musa Yusenda, S.Pd','uca.musa@sekolah.sch.id',1,156,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(58,'198001012006011007','Drs. Ahmad Nur Mukhlis','ahmad.nur@sekolah.sch.id',1,157,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(59,'198001012006011008','Drs. Heri','heri@sekolah.sch.id',1,158,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(60,'198001012006011009','Asep Bastaman','asep.bastaman@sekolah.sch.id',1,159,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(61,'198001012006011010','H. Encep Maemun, S.T., M.Pd','encep.maemun@sekolah.sch.id',1,160,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(62,'198001012006011011','Opik','opik@sekolah.sch.id',1,161,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(63,'198001012006011012','Ida Farida, S.Pd','ida.farida@sekolah.sch.id',1,162,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(64,'198001012006011013','Idah Mahmudah, S.Pd','idah.mahmudah@sekolah.sch.id',1,163,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(65,'198001012006011014','Drs. Muhamad Ali','muhamad.ali@sekolah.sch.id',1,164,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(66,'198001012006011015','Helmi Ali Akbar, S.Pd.I','helmi.ali@sekolah.sch.id',1,165,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(67,'198001012006011016','Syeni Suswarini, S.Pd','syeni.suswarini@sekolah.sch.id',1,166,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(68,'198001012006011017','Wiwin Yulianti, S.Pd','wiwin.yulianti@sekolah.sch.id',1,167,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(69,'198001012006011018','Ansor Muhajir, S.T','ansor.muhajir@sekolah.sch.id',1,168,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(70,'198001012006011019','Ogi Gaosun Gias, S.Pd.I','ogi.gaosun@sekolah.sch.id',1,169,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(71,'198001012006011020','Sandi Mulyana, S.Pd','sandi.mulyana@sekolah.sch.id',1,170,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(72,'198001012006011021','Erna Suryani, S.Pd','erna.suryani@sekolah.sch.id',1,171,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(73,'198001012006011022','Ai Wahidah, S.Ag','ai.wahidah@sekolah.sch.id',1,172,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(74,'198001012006011023','Chandra Aditya Yusup, S.Pd','chandra.aditya@sekolah.sch.id',1,173,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(75,'198001012006011024','Arif Kusmawan, S.Pd','arif.kusmawan@sekolah.sch.id',1,174,'2026-11-04 05:20:57','2026-11-04 15:04:29'),
(76,'198001012006011025','Asep Taufiq Hidayat, S.Kom','asep.taufiq@sekolah.sch.id',1,175,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(77,'198001012006011026','Rendy Ichsan Kusumah, S.T','rendy.ichsan@sekolah.sch.id',1,176,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(78,'198001012006011027','Feri Rahmawan, S.Pd','feri.rahmawan@sekolah.sch.id',1,177,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(79,'198001012006011028','Ari Sandriana, S.Kom','ari.sandriana@sekolah.sch.id',1,178,'2026-11-04 05:20:57','2026-11-04 15:13:07'),
(80,'198001012006011029','Yaneu Nurdian, S.Pd, M.Pd','yaneu.nurdian@sekolah.sch.id',1,179,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(81,'198001012006011030','Ucu Muhammad Khoer N, S.Pd','ucu.muhammad@sekolah.sch.id',1,180,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(82,'198001012006011031','Mira Dwi Rahmawati, S.Ag','mira.dwi@sekolah.sch.id',1,181,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(83,'198001012006011032','Aldi Maulana, S.Pd','aldi.maulana@sekolah.sch.id',1,182,'2026-11-04 05:20:57','2026-11-04 15:22:32'),
(84,'198001012006011033','Eli Rosliah, S.Pd','eli.rosliah@sekolah.sch.id',1,183,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(85,'198001012006011034','Isni Nur Fitriani, S.Pd','isni.nur@sekolah.sch.id',1,184,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(86,'198001012006011035','Aanisah Putri Nur Aini, S.Pd','aanisah.putri@sekolah.sch.id',1,185,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(87,'198001012006011036','Yusi Luthfiani, S.Sos.I','yusi.luthfiani@sekolah.sch.id',1,186,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(88,'198001012006011037','Lilis Rustandy Mawardi, S.Pd., M.Pd','lilis.rustandy@sekolah.sch.id',1,187,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(89,'198001012006011038','Yusrin Noer Sabrina, S.Pd','yusrin.noer@sekolah.sch.id',1,188,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(90,'198001012006011039','Senny Indayani, S.Pd','senny.indayani@sekolah.sch.id',1,189,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(91,'198001012006011040','Ilma Amalia, S.Pd','ilma.amalia@sekolah.sch.id',1,190,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(92,'198001012006011041','Alvira Sofwatunnisa, S.Pd','alvira.sofwatunnisa@sekolah.sch.id',1,191,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(93,'198001012006011042','Risna Andriyani, S.Pd','risna.andriyani@sekolah.sch.id',1,192,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(94,'198001012006011043','Dra. Tintin Noerhayati','tintin.noerhayati@sekolah.sch.id',1,193,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(95,'198001012006011044','Rizkia Fahira, S.Pd','rizkia.fahira@sekolah.sch.id',1,194,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(96,'198001012006011045','Frasetio','frasetio@sekolah.sch.id',1,195,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(97,'198001012006011046','Ratnaningsih Hatil Aeni, S.Pd.','ratnaningsih.hatil@sekolah.sch.id',1,196,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(98,'198001012006011047','Dheny Darajatul Hasani, S.Pd.','dheny.darajatul@sekolah.sch.id',1,197,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(99,'198001012006011048','Fahrullah, S.Pd','fahrullah@sekolah.sch.id',1,198,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(100,'198001012006011049','Aan Andriana, S.T','aan.andriana@sekolah.sch.id',1,199,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(101,'198001012006011050','Fitri Fauziah, S.T','fitri.fauziah@sekolah.sch.id',1,200,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(102,'198001012006011051','Nida Aulia, S.Pd','nida.aulia@sekolah.sch.id',1,201,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(103,'198001012006011052','Sri Nurrizki Putri Krisnadi, S.Pd','sri.nurrizki@sekolah.sch.id',1,202,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(104,'198001012006011053','Riesy Putri Navia, S.Kom','riesy.putri@sekolah.sch.id',1,203,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(105,'198001012006011054','Rurry Pebriani Maryadi, S.Pd','rurry.pebriani@sekolah.sch.id',1,204,'2026-11-04 05:20:57','2026-11-04 07:09:11'),
(106,'198001012006011055','Ivan Rizal, S.Pd','ivan.rizal@sekolah.sch.id',1,205,'2026-11-04 05:20:57','2026-11-04 07:09:11');
/*!40000 ALTER TABLE `t_guru` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_kelas`
--

DROP TABLE IF EXISTS `t_kelas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `t_kelas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) DEFAULT NULL,
  `tingkat` varchar(20) NOT NULL,
  `jurusan` varchar(100) NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_t_kelas_nama` (`nama`)
) ENGINE=InnoDB AUTO_INCREMENT=112 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_kelas`
--

LOCK TABLES `t_kelas` WRITE;
/*!40000 ALTER TABLE `t_kelas` DISABLE KEYS */;
INSERT INTO `t_kelas` (`id`, `nama`, `tingkat`, `jurusan`, `status_aktif`, `created_at`, `updated_at`) VALUES (11,'XII RPL 1','XII','RPL',1,'2026-09-28 06:01:44','2026-11-04 06:17:13'),
(26,'XII RPL 2','XII','RPL',1,'2026-09-28 06:01:44','2026-09-28 06:01:44'),
(111,'XI RPL 1','XI','RPL',1,'2026-11-04 07:41:15','2026-11-04 07:41:27');
/*!40000 ALTER TABLE `t_kelas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_kelas_siswa`
--

DROP TABLE IF EXISTS `t_kelas_siswa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `t_kelas_siswa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `siswa_id` int(11) DEFAULT NULL,
  `tahun_ajaran_id` int(11) DEFAULT NULL,
  `kelas_id` int(11) DEFAULT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_t_kelas_siswa_siswa_ta` (`siswa_id`,`tahun_ajaran_id`),
  KEY `kelas_id` (`kelas_id`),
  KEY `siswa_id` (`siswa_id`),
  KEY `tahun_ajaran_id` (`tahun_ajaran_id`),
  CONSTRAINT `t_kelas_siswa_ibfk_1` FOREIGN KEY (`siswa_id`) REFERENCES `t_siswa` (`id`) ON DELETE CASCADE,
  CONSTRAINT `t_kelas_siswa_ibfk_2` FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `t_tahun_ajaran` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=158 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_kelas_siswa`
--

LOCK TABLES `t_kelas_siswa` WRITE;
/*!40000 ALTER TABLE `t_kelas_siswa` DISABLE KEYS */;
INSERT INTO `t_kelas_siswa` (`id`, `siswa_id`, `tahun_ajaran_id`, `kelas_id`, `tanggal_mulai`, `tanggal_selesai`, `status_aktif`, `created_at`, `updated_at`) VALUES (104,101,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(105,102,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(106,103,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(107,104,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(108,105,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(109,106,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(110,107,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(111,108,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(112,109,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(113,110,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(114,111,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(115,112,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(116,113,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(117,114,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(118,115,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(119,116,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(120,117,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(121,118,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(122,119,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(123,120,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(124,121,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(125,122,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(126,123,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(127,124,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(128,125,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(129,126,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(130,127,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(131,128,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(132,129,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(133,130,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(134,131,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(135,132,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(136,133,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(137,134,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(138,135,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(139,136,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(140,137,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(141,138,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(142,139,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(143,140,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(144,141,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(145,142,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(146,143,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(147,144,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(148,145,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(149,146,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(150,147,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(151,148,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(152,149,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),
(153,150,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36');
/*!40000 ALTER TABLE `t_kelas_siswa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_pelanggaran`
--

DROP TABLE IF EXISTS `t_pelanggaran`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `t_pelanggaran` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pelanggaran_kategori_id` int(11) DEFAULT NULL,
  `kode` varchar(30) DEFAULT NULL,
  `nama` varchar(30) DEFAULT NULL,
  `poin` int(11) NOT NULL,
  `deskripsi` text NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_t_pelanggaran_kode` (`kode`),
  KEY `pelanggaran_kategori_id` (`pelanggaran_kategori_id`)
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_pelanggaran`
--

LOCK TABLES `t_pelanggaran` WRITE;
/*!40000 ALTER TABLE `t_pelanggaran` DISABLE KEYS */;
INSERT INTO `t_pelanggaran` (`id`, `pelanggaran_kategori_id`, `kode`, `nama`, `poin`, `deskripsi`, `status_aktif`, `created_at`, `updated_at`) VALUES (1,1,'P01','Pelanggaran 1',5,'Des 1',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(2,2,'P02','Pelanggaran 2',5,'Des 2',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(3,3,'P03','Pelanggaran 3',5,'Des 3',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(4,4,'P04','Pelanggaran 4',5,'Des 4',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(5,5,'P05','Pelanggaran 5',5,'Des 5',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(6,6,'P06','Pelanggaran 6',5,'Des 6',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(7,7,'P07','Pelanggaran 7',5,'Des 7',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(8,8,'P08','Pelanggaran 8',5,'Des 8',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(9,9,'P09','Pelanggaran 9',5,'Des 9',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(10,10,'P10','Pelanggaran 10',5,'Des 10',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(11,11,'P11','Pelanggaran 11',5,'Des 11',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(12,12,'P12','Pelanggaran 12',5,'Des 12',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(13,13,'P13','Pelanggaran 13',5,'Des 13',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(14,14,'P14','Pelanggaran 14',5,'Des 14',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(15,15,'P15','Pelanggaran 15',5,'Des 15',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(16,16,'P16','Pelanggaran 16',5,'Des 16',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(17,17,'P17','Pelanggaran 17',5,'Des 17',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(18,18,'P18','Pelanggaran 18',5,'Des 18',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(19,19,'P19','Pelanggaran 19',5,'Des 19',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(20,20,'P20','Pelanggaran 20',5,'Des 20',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(21,21,'P21','Pelanggaran 21',5,'Des 21',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(22,22,'P22','Pelanggaran 22',5,'Des 22',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(23,23,'P23','Pelanggaran 23',5,'Des 23',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(24,24,'P24','Pelanggaran 24',5,'Des 24',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(25,25,'P25','Pelanggaran 25',5,'Des 25',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(26,26,'P26','Pelanggaran 26',5,'Des 26',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(27,27,'P27','Pelanggaran 27',5,'Des 27',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(28,28,'P28','Pelanggaran 28',5,'Des 28',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(29,29,'P29','Pelanggaran 29',5,'Des 29',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(30,30,'P30','Pelanggaran 30',5,'Des 30',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(31,31,'P31','Pelanggaran 31',5,'Des 31',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(32,32,'P32','Pelanggaran 32',5,'Des 32',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(33,33,'P33','Pelanggaran 33',5,'Des 33',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(34,34,'P34','Pelanggaran 34',5,'Des 34',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(35,35,'P35','Pelanggaran 35',5,'Des 35',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(36,36,'P36','Pelanggaran 36',5,'Des 36',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(37,37,'P37','Pelanggaran 37',5,'Des 37',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(38,38,'P38','Pelanggaran 38',5,'Des 38',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(39,39,'P39','Pelanggaran 39',5,'Des 39',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(40,40,'P40','Pelanggaran 40',5,'Des 40',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(41,41,'P41','Pelanggaran 41',5,'Des 41',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(42,42,'P42','Pelanggaran 42',5,'Des 42',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(43,43,'P43','Pelanggaran 43',5,'Des 43',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(44,44,'P44','Pelanggaran 44',5,'Des 44',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(45,45,'P45','Pelanggaran 45',5,'Des 45',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(46,46,'P46','Pelanggaran 46',5,'Des 46',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(47,47,'P47','Pelanggaran 47',5,'Des 47',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(48,48,'P48','Pelanggaran 48',5,'Des 48',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(49,49,'P49','Pelanggaran 49',5,'Des 49',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(50,50,'P50','Pelanggaran 50',5,'Des 50',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(51,51,'P51','Pelanggaran 51',5,'Des 51',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(52,52,'P52','Pelanggaran 52',5,'Des 52',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(53,53,'P53','Pelanggaran 53',5,'Des 53',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(54,54,'P54','Pelanggaran 54',5,'Des 54',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(55,55,'P55','Pelanggaran 55',5,'Des 55',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(56,56,'P56','Pelanggaran 56',5,'Des 56',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(57,57,'P57','Pelanggaran 57',5,'Des 57',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(58,58,'P58','Pelanggaran 58',5,'Des 58',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(59,59,'P59','Pelanggaran 59',5,'Des 59',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(60,60,'P60','Pelanggaran 60',5,'Des 60',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(61,61,'P61','Pelanggaran 61',5,'Des 61',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(62,62,'P62','Pelanggaran 62',5,'Des 62',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(63,63,'P63','Pelanggaran 63',5,'Des 63',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(64,64,'P64','Pelanggaran 64',5,'Des 64',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(65,65,'P65','Pelanggaran 65',5,'Des 65',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(66,66,'P66','Pelanggaran 66',5,'Des 66',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(67,67,'P67','Pelanggaran 67',5,'Des 67',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(68,68,'P68','Pelanggaran 68',5,'Des 68',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(69,69,'P69','Pelanggaran 69',5,'Des 69',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(70,70,'P70','Pelanggaran 70',5,'Des 70',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(71,71,'P71','Pelanggaran 71',5,'Des 71',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(72,72,'P72','Pelanggaran 72',5,'Des 72',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(73,73,'P73','Pelanggaran 73',5,'Des 73',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(74,74,'P74','Pelanggaran 74',5,'Des 74',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(75,75,'P75','Pelanggaran 75',5,'Des 75',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(76,76,'P76','Pelanggaran 76',5,'Des 76',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(77,77,'P77','Pelanggaran 77',5,'Des 77',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(78,78,'P78','Pelanggaran 78',5,'Des 78',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(79,79,'P79','Pelanggaran 79',5,'Des 79',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(80,80,'P80','Pelanggaran 80',5,'Des 80',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(81,81,'P81','Pelanggaran 81',5,'Des 81',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(82,82,'P82','Pelanggaran 82',5,'Des 82',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(83,83,'P83','Pelanggaran 83',5,'Des 83',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(84,84,'P84','Pelanggaran 84',5,'Des 84',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(85,85,'P85','Pelanggaran 85',5,'Des 85',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(86,86,'P86','Pelanggaran 86',5,'Des 86',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(87,87,'P87','Pelanggaran 87',5,'Des 87',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(88,88,'P88','Pelanggaran 88',5,'Des 88',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(89,89,'P89','Pelanggaran 89',5,'Des 89',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(90,90,'P90','Pelanggaran 90',5,'Des 90',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(91,91,'P91','Pelanggaran 91',5,'Des 91',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(92,92,'P92','Pelanggaran 92',5,'Des 92',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(93,93,'P93','Pelanggaran 93',5,'Des 93',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(94,94,'P94','Pelanggaran 94',5,'Des 94',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(95,95,'P95','Pelanggaran 95',5,'Des 95',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(96,96,'P96','Pelanggaran 96',5,'Des 96',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(97,97,'P97','Pelanggaran 97',5,'Des 97',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(98,98,'P98','Pelanggaran 98',5,'Des 98',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(99,99,'P99','Pelanggaran 99',5,'Des 99',1,'2026-09-28 05:58:31','2026-09-28 05:58:31'),
(100,100,'P100','Pelanggaran 100',5,'Des 100',1,'2026-09-28 05:58:31','2026-09-28 05:58:31');
/*!40000 ALTER TABLE `t_pelanggaran` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_pelanggaran_kategori`
--

DROP TABLE IF EXISTS `t_pelanggaran_kategori`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `t_pelanggaran_kategori` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) DEFAULT NULL,
  `deskripsi` text NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_t_pelanggaran_kategori_nama` (`nama`)
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_pelanggaran_kategori`
--

LOCK TABLES `t_pelanggaran_kategori` WRITE;
/*!40000 ALTER TABLE `t_pelanggaran_kategori` DISABLE KEYS */;
INSERT INTO `t_pelanggaran_kategori` (`id`, `nama`, `deskripsi`, `status_aktif`, `created_at`, `updated_at`) VALUES (1,'Kategori 1','Deskripsi 1',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(2,'Kategori 2','Deskripsi 2',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(3,'Kategori 3','Deskripsi 3',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(4,'Kategori 4','Deskripsi 4',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(5,'Kategori 5','Deskripsi 5',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(6,'Kategori 6','Deskripsi 6',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(7,'Kategori 7','Deskripsi 7',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(8,'Kategori 8','Deskripsi 8',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(9,'Kategori 9','Deskripsi 9',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(10,'Kategori 10','Deskripsi 10',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(11,'Kategori 11','Deskripsi 11',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(12,'Kategori 12','Deskripsi 12',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(13,'Kategori 13','Deskripsi 13',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(14,'Kategori 14','Deskripsi 14',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(15,'Kategori 15','Deskripsi 15',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(16,'Kategori 16','Deskripsi 16',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(17,'Kategori 17','Deskripsi 17',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(18,'Kategori 18','Deskripsi 18',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(19,'Kategori 19','Deskripsi 19',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(20,'Kategori 20','Deskripsi 20',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(21,'Kategori 21','Deskripsi 21',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(22,'Kategori 22','Deskripsi 22',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(23,'Kategori 23','Deskripsi 23',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(24,'Kategori 24','Deskripsi 24',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(25,'Kategori 25','Deskripsi 25',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(26,'Kategori 26','Deskripsi 26',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(27,'Kategori 27','Deskripsi 27',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(28,'Kategori 28','Deskripsi 28',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(29,'Kategori 29','Deskripsi 29',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(30,'Kategori 30','Deskripsi 30',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(31,'Kategori 31','Deskripsi 31',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(32,'Kategori 32','Deskripsi 32',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(33,'Kategori 33','Deskripsi 33',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(34,'Kategori 34','Deskripsi 34',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(35,'Kategori 35','Deskripsi 35',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(36,'Kategori 36','Deskripsi 36',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(37,'Kategori 37','Deskripsi 37',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(38,'Kategori 38','Deskripsi 38',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(39,'Kategori 39','Deskripsi 39',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(40,'Kategori 40','Deskripsi 40',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(41,'Kategori 41','Deskripsi 41',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(42,'Kategori 42','Deskripsi 42',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(43,'Kategori 43','Deskripsi 43',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(44,'Kategori 44','Deskripsi 44',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(45,'Kategori 45','Deskripsi 45',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(46,'Kategori 46','Deskripsi 46',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(47,'Kategori 47','Deskripsi 47',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(48,'Kategori 48','Deskripsi 48',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(49,'Kategori 49','Deskripsi 49',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(50,'Kategori 50','Deskripsi 50',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(51,'Kategori 51','Deskripsi 51',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(52,'Kategori 52','Deskripsi 52',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(53,'Kategori 53','Deskripsi 53',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(54,'Kategori 54','Deskripsi 54',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(55,'Kategori 55','Deskripsi 55',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(56,'Kategori 56','Deskripsi 56',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(57,'Kategori 57','Deskripsi 57',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(58,'Kategori 58','Deskripsi 58',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(59,'Kategori 59','Deskripsi 59',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(60,'Kategori 60','Deskripsi 60',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(61,'Kategori 61','Deskripsi 61',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(62,'Kategori 62','Deskripsi 62',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(63,'Kategori 63','Deskripsi 63',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(64,'Kategori 64','Deskripsi 64',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(65,'Kategori 65','Deskripsi 65',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(66,'Kategori 66','Deskripsi 66',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(67,'Kategori 67','Deskripsi 67',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(68,'Kategori 68','Deskripsi 68',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(69,'Kategori 69','Deskripsi 69',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(70,'Kategori 70','Deskripsi 70',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(71,'Kategori 71','Deskripsi 71',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(72,'Kategori 72','Deskripsi 72',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(73,'Kategori 73','Deskripsi 73',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(74,'Kategori 74','Deskripsi 74',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(75,'Kategori 75','Deskripsi 75',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(76,'Kategori 76','Deskripsi 76',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(77,'Kategori 77','Deskripsi 77',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(78,'Kategori 78','Deskripsi 78',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(79,'Kategori 79','Deskripsi 79',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(80,'Kategori 80','Deskripsi 80',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(81,'Kategori 81','Deskripsi 81',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(82,'Kategori 82','Deskripsi 82',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(83,'Kategori 83','Deskripsi 83',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(84,'Kategori 84','Deskripsi 84',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(85,'Kategori 85','Deskripsi 85',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(86,'Kategori 86','Deskripsi 86',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(87,'Kategori 87','Deskripsi 87',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(88,'Kategori 88','Deskripsi 88',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(89,'Kategori 89','Deskripsi 89',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(90,'Kategori 90','Deskripsi 90',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(91,'Kategori 91','Deskripsi 91',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(92,'Kategori 92','Deskripsi 92',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(93,'Kategori 93','Deskripsi 93',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(94,'Kategori 94','Deskripsi 94',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(95,'Kategori 95','Deskripsi 95',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(96,'Kategori 96','Deskripsi 96',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(97,'Kategori 97','Deskripsi 97',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(98,'Kategori 98','Deskripsi 98',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(99,'Kategori 99','Deskripsi 99',1,'2026-09-28 05:58:08','2026-09-28 05:58:08'),
(100,'Kategori 100','Deskripsi 100',1,'2026-09-28 05:58:08','2026-09-28 05:58:08');
/*!40000 ALTER TABLE `t_pelanggaran_kategori` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_pelanggaran_siswa`
--

DROP TABLE IF EXISTS `t_pelanggaran_siswa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `t_pelanggaran_siswa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tahun_ajaran_id` int(11) DEFAULT NULL,
  `siswa_id` int(11) DEFAULT NULL,
  `nama_siswa` varchar(150) NOT NULL,
  `kelas_id` int(11) DEFAULT NULL,
  `nama_kelas` varchar(100) NOT NULL,
  `pelanggaran_id` int(11) DEFAULT NULL,
  `nama_pelanggaran` int(11) NOT NULL,
  `pelanggaran_kategori_id` int(11) DEFAULT NULL,
  `guru_id` int(11) DEFAULT NULL,
  `nama_guru` varchar(150) NOT NULL,
  `tanggal` date DEFAULT NULL,
  `keterangan` text NOT NULL,
  `poin` int(11) NOT NULL,
  `tindakan` text NOT NULL,
  `status` varchar(30) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `guru_id` (`guru_id`),
  KEY `tahun_ajaran_id` (`tahun_ajaran_id`),
  KEY `siswa_id` (`siswa_id`),
  KEY `kelas_id` (`kelas_id`),
  KEY `pelanggaran_id` (`pelanggaran_id`),
  KEY `pelanggaran_kategori_id` (`pelanggaran_kategori_id`),
  CONSTRAINT `t_pelanggaran_siswa_ibfk_1` FOREIGN KEY (`guru_id`) REFERENCES `t_guru` (`id`) ON DELETE CASCADE,
  CONSTRAINT `t_pelanggaran_siswa_ibfk_3` FOREIGN KEY (`kelas_id`) REFERENCES `t_kelas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `t_pelanggaran_siswa_ibfk_5` FOREIGN KEY (`pelanggaran_id`) REFERENCES `t_pelanggaran` (`id`) ON DELETE CASCADE,
  CONSTRAINT `t_pelanggaran_siswa_ibfk_6` FOREIGN KEY (`siswa_id`) REFERENCES `t_siswa` (`id`) ON DELETE CASCADE,
  CONSTRAINT `t_pelanggaran_siswa_ibfk_7` FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `t_tahun_ajaran` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_pelanggaran_siswa`
--

LOCK TABLES `t_pelanggaran_siswa` WRITE;
/*!40000 ALTER TABLE `t_pelanggaran_siswa` DISABLE KEYS */;
INSERT INTO `t_pelanggaran_siswa` (`id`, `tahun_ajaran_id`, `siswa_id`, `nama_siswa`, `kelas_id`, `nama_kelas`, `pelanggaran_id`, `nama_pelanggaran`, `pelanggaran_kategori_id`, `guru_id`, `nama_guru`, `tanggal`, `keterangan`, `poin`, `tindakan`, `status`, `created_at`, `updated_at`) VALUES (2,NULL,121,'RANGGA RAMADIYANTO NURHAKIM',11,'XII RPL 1',1,0,NULL,NULL,'','2026-11-04','merokok',5,'Diberi teguran & pembinaan','','2026-11-04 06:15:12','2026-11-04 06:15:12');
/*!40000 ALTER TABLE `t_pelanggaran_siswa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_siswa`
--

DROP TABLE IF EXISTS `t_siswa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `t_siswa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nis` varchar(20) DEFAULT NULL,
  `nisn` varchar(20) DEFAULT NULL,
  `nama` varchar(150) DEFAULT NULL,
  `jenis_kelamin` varchar(4) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `alamat` text NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_t_siswa_nisn` (`nisn`),
  UNIQUE KEY `uq_t_siswa_nis` (`nis`)
) ENGINE=InnoDB AUTO_INCREMENT=10000 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_siswa`
--

LOCK TABLES `t_siswa` WRITE;
/*!40000 ALTER TABLE `t_siswa` DISABLE KEYS */;
INSERT INTO `t_siswa` (`id`, `nis`, `nisn`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `alamat`, `status_aktif`, `created_at`, `updated_at`) VALUES (101,'202600001',NULL,'ALFIN RAHADIAN','L','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:16'),
(102,'202600002',NULL,'ALISA RAMADHANI','P','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),
(103,'202600003',NULL,'AMA AURA LIVIA','P','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),
(104,'202600004',NULL,'ANIS KHOERUNNISA NUR RAMADANI','P','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),
(105,'202600005',NULL,'AZMIL AMIRA','P','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),
(106,'202600006',NULL,'AZRIL AKBAR FAWZI ABDILLAH','L','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),
(107,'202600007',NULL,'ERLANGGA SAPUTRA HIDAYAT','L','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),
(108,'202600008',NULL,'HILMIA HUMMA TUNNUNJAH','P','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),
(109,'202600009',NULL,'ILMA NAJYA','P','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),
(110,'202600010',NULL,'JULIANA DHARMA PANGESTU','P','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),
(111,'202600011',NULL,'KAYLA NUR FADILAH','P','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),
(112,'202600012',NULL,'MOZA DINA NABILLAH','P','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),
(113,'202600013',NULL,'MUHAMAD ALFARIZI','L','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),
(114,'202600014',NULL,'MUHAMAD FAHRIN ADAM','L','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),
(115,'202600015',NULL,'MUHAMAD ZIDAN MAULIDAN','L','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),
(116,'202600016',NULL,'MUHAMAD ZULFAN NURFALANSYAH','L','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),
(117,'202600017',NULL,'NATIYA NUR AZKIYA','P','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),
(118,'202600018',NULL,'NOVITA PUSPITASARI','P','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),
(119,'202600019',NULL,'NUCKY ANDREAS FEBRIAN','L','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),
(120,'202600020',NULL,'RADITYA JETI PRASETYA','L','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),
(121,'202600021',NULL,'RANGGA RAMADIYANTO NURHAKIM','L','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),
(122,'202600022',NULL,'RISQI MUHAMAD IHWANI','L','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),
(123,'202600023',NULL,'SALWA SABILATUL BARAKAH','P','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),
(124,'202600024',NULL,'TAZKIA ZAHRA','P','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),
(125,'102410544',NULL,'ADITTIA NUR LANTIP','L','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),
(126,'102410545',NULL,'AGNIA ROSYA NOERILLAH','P','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),
(127,'102410546',NULL,'ANISA AMIRULLA','P','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),
(128,'102410547',NULL,'ARADEA KANDI','L','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),
(129,'102410548',NULL,'ARFA DIESA OKMALELIZA','P','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),
(130,'102410549',NULL,'FITRI NUR SYIAMI','P','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),
(131,'102410550',NULL,'JUNITA PUTRI NUR RAHMAN','P','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),
(132,'102410551',NULL,'MOHAMMAD ATALARIK ALI SYAHBANA','L','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),
(133,'102410552',NULL,'MUHAMAD FAHRIL FAHRUROZI','L','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),
(134,'102410750',NULL,'MUHAMMAD ADNAN SYAEFUL ISLAMI','L','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),
(135,'102410553',NULL,'MUHAMMAD ZAHRU AQILA FIKRI','L','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),
(136,'102410554',NULL,'NADIA ZULFA SORAYA','P','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),
(137,'102410555',NULL,'NAZRIL ILHAM','L','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),
(138,'102410556',NULL,'PUSPITA DEWI','P','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),
(139,'102410558',NULL,'RAKHA MUHAMMAD HAYKAL','L','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),
(140,'102410559',NULL,'RANI MEILANI','P','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),
(141,'102410560',NULL,'REIHAN MUZAKI','L','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),
(142,'102410561',NULL,'RESA JULIANSYAH','L','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),
(143,'102410562',NULL,'RIZKI PERMATA SUGIANTO','L','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),
(144,'102410563',NULL,'SAHIRA FIRAJ','P','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),
(145,'102410564',NULL,'SENDI MARDIAN','L','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),
(146,'102410755',NULL,'SHAHAIDAN NIZZAR','L','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),
(147,'102410565',NULL,'SHERLY SATRIANI','P','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),
(148,'102410566',NULL,'SOFI NURJANAH','P','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),
(149,'102410567',NULL,'SURAIYA SALSABILA AZZAHRA','P','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),
(150,'102410568',NULL,'TONI ANTONIO','L','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01');
/*!40000 ALTER TABLE `t_siswa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_tahun_ajaran`
--

DROP TABLE IF EXISTS `t_tahun_ajaran`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `t_tahun_ajaran` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(20) DEFAULT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_t_tahun_ajaran_nama` (`nama`)
) ENGINE=InnoDB AUTO_INCREMENT=112 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_tahun_ajaran`
--

LOCK TABLES `t_tahun_ajaran` WRITE;
/*!40000 ALTER TABLE `t_tahun_ajaran` DISABLE KEYS */;
INSERT INTO `t_tahun_ajaran` (`id`, `nama`, `tanggal_mulai`, `tanggal_selesai`, `status_aktif`, `created_at`, `updated_at`) VALUES (101,'2026/2027','2026-07-01','2027-06-30',1,'2026-11-04 05:36:36','2026-11-04 05:36:36');
/*!40000 ALTER TABLE `t_tahun_ajaran` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_users`
--

DROP TABLE IF EXISTS `t_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `t_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `password` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) NOT NULL,
  `role` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_t_users_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=221 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_users`
--

LOCK TABLES `t_users` WRITE;
/*!40000 ALTER TABLE `t_users` DISABLE KEYS */;
INSERT INTO `t_users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `role`, `created_at`, `updated_at`) VALUES (1,'Administrator','admin@ukk2026.com','2026-10-02 01:48:51','$2y$10$RS53mKxMjN8wP7lPw1ZSn.4ef06mI0yzBHBRvUyBJzUpM4830o.Au','','Admin','2026-10-02 01:48:51','2026-10-02 01:48:51'),
(2,'Bapak Guru','guru@ukk2026.com','2026-10-02 01:57:34','$2y$10$GHyInVTPyZNxGFAWxrCvQe3b1nI.0jkzVWd.Zcg4waGWGjx06qsZS','','Guru','2026-10-02 01:57:34','2026-10-02 01:57:34'),
(3,'Ibu Wali Kelas','walikelas@ukk2026.com','2026-10-02 01:57:34','$2y$10$UuGg3CXnKYPiX0zEPXaxJOnan8OSv9p9Pv5xfOdOu7efAlAvy/Jxa','','Guru','2026-10-02 01:57:34','2026-10-02 01:57:34'),
(4,'nonok','nonok1@gmail.com','2026-10-02 03:25:49','guru123','','guru','2026-10-02 03:25:49','2026-10-02 03:25:49'),
(151,'Wawan Kurniawan, S.Pd.I','wawan.kurniawan@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 06:17:13'),
(152,'Ai Nuryaidah, S.Pd','ai.nuryaidah@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:35:49'),
(153,'Maya Permata Endah, S.Pd','maya.permata@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(154,'Dody Jaenudin, S.Pd.','dody.jaenudin@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(155,'Dedi Kardiman, S.Pd','dedi.kardiman@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(156,'Uca Musa Yusenda, S.Pd','uca.musa@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(157,'Drs. Ahmad Nur Mukhlis','ahmad.nur@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(158,'Drs. Heri','heri@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(159,'Asep Bastaman','asep.bastaman@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(160,'H. Encep Maemun, S.T., M.Pd','encep.maemun@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(161,'Opik','opik@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(162,'Ida Farida, S.Pd','ida.farida@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(163,'Idah Mahmudah, S.Pd','idah.mahmudah@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(164,'Drs. Muhamad Ali','muhamad.ali@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(165,'Helmi Ali Akbar, S.Pd.I','helmi.ali@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(166,'Syeni Suswarini, S.Pd','syeni.suswarini@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(167,'Wiwin Yulianti, S.Pd','wiwin.yulianti@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(168,'Ansor Muhajir, S.T','ansor.muhajir@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(169,'Ogi Gaosun Gias, S.Pd.I','ogi.gaosun@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(170,'Sandi Mulyana, S.Pd','sandi.mulyana@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(171,'Erna Suryani, S.Pd','erna.suryani@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(172,'Ai Wahidah, S.Ag','ai.wahidah@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(173,'Chandra Aditya Yusup, S.Pd','chandra.aditya@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(174,'Arif Kusmawan, S.Pd','arif.kusmawan@sekolah.sch.id','2026-11-04 05:20:57','$2y$12$Nhh5mcoOSp5IbvMeOEfXSO.rDWn3K4.myqKx3V.4QGkGDXt/npe/u','','guru','2026-11-04 05:20:57','2026-11-04 15:04:29'),
(175,'Asep Taufiq Hidayat, S.Kom','asep.taufiq@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(176,'Rendy Ichsan Kusumah, S.T','rendy.ichsan@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(177,'Feri Rahmawan, S.Pd','feri.rahmawan@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(178,'Ari Sandriana, S.Kom','ari.sandriana@sekolah.sch.id','2026-11-04 05:20:57','$2y$12$mCQAsfS2ADwnk2Ugkrhp5.xSTrGWH3V587p7Uqk/pUjtq.cwh9kEW','','guru','2026-11-04 05:20:57','2026-11-04 15:13:07'),
(179,'Yaneu Nurdian, S.Pd, M.Pd','yaneu.nurdian@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(180,'Ucu Muhammad Khoer N, S.Pd','ucu.muhammad@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(181,'Mira Dwi Rahmawati, S.Ag','mira.dwi@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(182,'Aldi Maulana, S.Pd','aldi.maulana@sekolah.sch.id','2026-11-04 05:20:57','$2y$12$FBKKcdIq3KOkXv29nfbC9OSM1460tWuAgeeVDTPzawcFd.H8KEhRe','','guru','2026-11-04 05:20:57','2026-11-04 15:22:32'),
(183,'Eli Rosliah, S.Pd','eli.rosliah@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(184,'Isni Nur Fitriani, S.Pd','isni.nur@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(185,'Aanisah Putri Nur Aini, S.Pd','aanisah.putri@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(186,'Yusi Luthfiani, S.Sos.I','yusi.luthfiani@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(187,'Lilis Rustandy Mawardi, S.Pd., M.Pd','lilis.rustandy@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(188,'Yusrin Noer Sabrina, S.Pd','yusrin.noer@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(189,'Senny Indayani, S.Pd','senny.indayani@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(190,'Ilma Amalia, S.Pd','ilma.amalia@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(191,'Alvira Sofwatunnisa, S.Pd','alvira.sofwatunnisa@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(192,'Risna Andriyani, S.Pd','risna.andriyani@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(193,'Dra. Tintin Noerhayati','tintin.noerhayati@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(194,'Rizkia Fahira, S.Pd','rizkia.fahira@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(195,'Frasetio','frasetio@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(196,'Ratnaningsih Hatil Aeni, S.Pd.','ratnaningsih.hatil@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(197,'Dheny Darajatul Hasani, S.Pd.','dheny.darajatul@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(198,'Fahrullah, S.Pd','fahrullah@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(199,'Aan Andriana, S.T','aan.andriana@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(200,'Fitri Fauziah, S.T','fitri.fauziah@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(201,'Nida Aulia, S.Pd','nida.aulia@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(202,'Sri Nurrizki Putri Krisnadi, S.Pd','sri.nurrizki@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(203,'Riesy Putri Navia, S.Kom','riesy.putri@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(204,'Rurry Pebriani Maryadi, S.Pd','rurry.pebriani@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),
(205,'Ivan Rizal, S.Pd','ivan.rizal@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57');
/*!40000 ALTER TABLE `t_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_wali_kelas`
--

DROP TABLE IF EXISTS `t_wali_kelas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `t_wali_kelas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tahun_ajaran_id` int(11) DEFAULT NULL,
  `kelas_id` int(11) DEFAULT NULL,
  `guru_id` int(11) DEFAULT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_t_wali_kelas_kelas_ta` (`kelas_id`,`tahun_ajaran_id`),
  KEY `tahun_ajaran_id` (`tahun_ajaran_id`),
  KEY `kelas_id` (`kelas_id`),
  KEY `guru_id` (`guru_id`),
  CONSTRAINT `t_wali_kelas_ibfk_1` FOREIGN KEY (`kelas_id`) REFERENCES `t_kelas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `t_wali_kelas_ibfk_2` FOREIGN KEY (`guru_id`) REFERENCES `t_guru` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_wali_kelas`
--

LOCK TABLES `t_wali_kelas` WRITE;
/*!40000 ALTER TABLE `t_wali_kelas` DISABLE KEYS */;
INSERT INTO `t_wali_kelas` (`id`, `tahun_ajaran_id`, `kelas_id`, `guru_id`, `tanggal_mulai`, `tanggal_selesai`, `status_aktif`, `created_at`, `updated_at`) VALUES (18,101,26,83,'0000-00-00','0000-00-00',1,'2026-11-04 06:25:29','2026-11-04 06:25:29'),
(20,101,11,79,'0000-00-00','0000-00-00',1,'2026-11-04 15:13:44','2026-11-04 15:17:03');
/*!40000 ALTER TABLE `t_wali_kelas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'db_ukk_2026'
--
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `GenerateDummyDataSchool` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `GenerateDummyDataSchool`()
BEGIN
    DECLARE i INT DEFAULT 1;
    DECLARE tingkat_val VARCHAR(20);
    DECLARE jurusan_val VARCHAR(100);

    -- 1. Insert 100 Users (Campuran Guru dan Siswa)[cite: 1]
    SET i = 1;
    WHILE i <= 100 DO
        INSERT INTO users (name, email, password, role, created_at, updated_at)
        VALUES (CONCAT('User ', i), CONCAT('user', i, '@sekolah.com'), 'password123', IF(i <= 50, 'guru', 'siswa'), NOW(), NOW());
        SET i = i + 1;
    END WHILE;

    -- 2. Insert 100 Tahun Ajaran[cite: 1]
    SET i = 1;
    WHILE i <= 100 DO
        INSERT INTO tahun_ajaran (nama, tanggal_mulai, tanggal_selesai, status_aktif, created_at, updated_at)
        VALUES (CONCAT('TA 20', LPAD(i, 2, '0'), '/20', LPAD(i+1, 2, '0')), CONCAT('20', LPAD(i, 2, '0'), '-07-01'), CONCAT('20', LPAD(i+1, 2, '0'), '-06-30'), IF(i=100, 1, 0), NOW(), NOW());
        SET i = i + 1;
    END WHILE;

    -- 3. Insert 50 Guru[cite: 1]
    SET i = 1;
    WHILE i <= 50 DO
        INSERT INTO guru (nip, nama, email, status_aktif, user_id, created_at, updated_at)
        VALUES (CONCAT('1980010120050110', LPAD(i, 2, '0')), CONCAT('Guru ', i), CONCAT('guru', i, '@sekolah.com'), 1, i, NOW(), NOW());
        SET i = i + 1;
    END WHILE;

    -- 4. Insert 100 Siswa[cite: 1]
    SET i = 1;
    WHILE i <= 100 DO
        INSERT INTO siswa (nis, nisn, nama, jenis_kelamin, tanggal_lahir, alamat, status_aktif, created_at, updated_at)
        VALUES (CONCAT('100', LPAD(i, 3, '0')), CONCAT('0050000', LPAD(i, 3, '0')), CONCAT('Siswa ', i), IF(i % 2 = 0, 'L', 'P'), '2005-01-01', CONCAT('Alamat Siswa ', i), 1, NOW(), NOW());
        SET i = i + 1;
    END WHILE;

    -- 5. Insert 100 Kelas (Tingkat X, XI, XII & Jurusan RPL, TKJ, BD, TKR, TSM)[cite: 1]
    SET i = 1;
    WHILE i <= 100 DO
        -- Rotasi Tingkat
        IF i % 3 = 1 THEN SET tingkat_val = 'X';
        ELSEIF i % 3 = 2 THEN SET tingkat_val = 'XI';
        ELSE SET tingkat_val = 'XII';
        END IF;

        -- Rotasi Jurusan
        IF i % 5 = 1 THEN SET jurusan_val = 'RPL';
        ELSEIF i % 5 = 2 THEN SET jurusan_val = 'TKJ';
        ELSEIF i % 5 = 3 THEN SET jurusan_val = 'BD';
        ELSEIF i % 5 = 4 THEN SET jurusan_val = 'TKR';
        ELSE SET jurusan_val = 'TSM';
        END IF;

        INSERT INTO kelas (nama, tingkat, jurusan, status_aktif, created_at, updated_at)
        VALUES (CONCAT(tingkat_val, ' ', jurusan_val, ' ', CEIL(i / 15)), tingkat_val, jurusan_val, 1, NOW(), NOW());
        SET i = i + 1;
    END WHILE;

    -- 6. Insert 100 Kelas Siswa (Mapping Siswa ke Kelas)[cite: 1]
    SET i = 1;
    WHILE i <= 100 DO
        INSERT INTO kelas_siswa (siswa_id, tahun_ajaran_id, kelas_id, tanggal_mulai, tanggal_selesai, status_aktif, created_at, updated_at)
        VALUES (i, 100, i, '2025-07-01', '2026-06-30', 1, NOW(), NOW());
        SET i = i + 1;
    END WHILE;

    -- 7. Insert 100 Wali Kelas[cite: 1]
    SET i = 1;
    WHILE i <= 100 DO
        INSERT INTO wali_kelas (tahun_ajaran_id, kelas_id, guru_id, tanggal_mulai, tanggal_selesai, status_aktif, created_at, updated_at)
        VALUES (100, i, IF(i <= 50, i, i - 50), '2025-07-01', '2026-06-30', 1, NOW(), NOW());
        SET i = i + 1;
    END WHILE;

    -- 8. Insert 100 Pelanggaran Kategori[cite: 1]
    SET i = 1;
    WHILE i <= 100 DO
        INSERT INTO pelanggaran_kategori (nama, deskripsi, status_aktif, created_at, updated_at)
        VALUES (CONCAT('Kategori Pelanggaran ', i), CONCAT('Deskripsi untuk kategori ', i), 1, NOW(), NOW());
        SET i = i + 1;
    END WHILE;

    -- 9. Insert 100 Pelanggaran[cite: 1]
    SET i = 1;
    WHILE i <= 100 DO
        INSERT INTO pelanggaran (pelanggaran_kategori_id, kode, nama, poin, deskripsi, status_aktif, created_at, updated_at)
        VALUES (i, CONCAT('P', LPAD(i, 3, '0')), CONCAT('Nama Pelanggaran ', i), (i % 10) * 5 + 5, CONCAT('Detail pelanggaran ', i), 1, NOW(), NOW());
        SET i = i + 1;
    END WHILE;

    -- 10. Insert 100 Pelanggaran Siswa (History)[cite: 1]
    SET i = 1;
    WHILE i <= 100 DO
        INSERT INTO pelanggaran_siswa (tahun_ajaran_id, siswa_id, nama_siswa, kelas_id, nama_kelas, pelanggaran_id, nama_pelanggaran, pelanggaran_kategori_id, guru_id, nama_guru, tanggal, keterangan, poin, tindakan, status, created_at, updated_at)
        VALUES (
            100, i, CONCAT('Siswa ', i), i, CONCAT('Kelas ', i), i, CONCAT('Nama Pelanggaran ', i), i,
            IF(i <= 50, i, i - 50), CONCAT('Guru ', IF(i <= 50, i, i - 50)),
            CURDATE(), 'Terbukti melanggar aturan', (i % 10) * 5 + 5, 'Diberikan peringatan lisan', 'Selesai', NOW(), NOW()
        );
        SET i = i + 1;
    END WHILE;

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-04 22:24:04
