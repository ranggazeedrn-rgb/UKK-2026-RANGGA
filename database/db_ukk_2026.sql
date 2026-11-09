-- MariaDB dump 10.19  Distrib 10.4.25-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: db_ukk_2026
-- ------------------------------------------------------
-- Server version	10.4.25-MariaDB

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
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_guru`
--

LOCK TABLES `t_guru` WRITE;
/*!40000 ALTER TABLE `t_guru` DISABLE KEYS */;
INSERT INTO `t_guru` VALUES (52,'198001012006011001','Wawan Kurniawan, S.Pd.I','wawan.kurniawan@sekolah.sch.id',1,151,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(53,'198001012006011002','Ai Nuryaidah, S.Pd','ai.nuryaidah@sekolah.sch.id',1,152,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(54,'198001012006011003','Maya Permata Endah, S.Pd','maya.permata@sekolah.sch.id',1,153,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(55,'198001012006011004','Dody Jaenudin, S.Pd.','dody.jaenudin@sekolah.sch.id',1,154,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(56,'198001012006011005','Dedi Kardiman, S.Pd','dedi.kardiman@sekolah.sch.id',1,155,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(57,'198001012006011006','Uca Musa Yusenda, S.Pd','uca.musa@sekolah.sch.id',1,156,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(58,'198001012006011007','Drs. Ahmad Nur Mukhlis','ahmad.nur@sekolah.sch.id',1,157,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(59,'198001012006011008','Drs. Heri','heri@sekolah.sch.id',1,158,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(60,'198001012006011009','Asep Bastaman','asep.bastaman@sekolah.sch.id',1,159,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(61,'198001012006011010','H. Encep Maemun, S.T., M.Pd','encep.maemun@sekolah.sch.id',1,160,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(62,'198001012006011011','Opik','opik@sekolah.sch.id',1,161,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(63,'198001012006011012','Ida Farida, S.Pd','ida.farida@sekolah.sch.id',1,162,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(64,'198001012006011013','Idah Mahmudah, S.Pd','idah.mahmudah@sekolah.sch.id',1,163,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(65,'198001012006011014','Drs. Muhamad Ali','muhamad.ali@sekolah.sch.id',1,164,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(66,'198001012006011015','Helmi Ali Akbar, S.Pd.I','helmi.ali@sekolah.sch.id',1,165,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(67,'198001012006011016','Syeni Suswarini, S.Pd','syeni.suswarini@sekolah.sch.id',1,166,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(68,'198001012006011017','Wiwin Yulianti, S.Pd','wiwin.yulianti@sekolah.sch.id',1,167,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(69,'198001012006011018','Ansor Muhajir, S.T','ansor.muhajir@sekolah.sch.id',1,168,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(70,'198001012006011019','Ogi Gaosun Gias, S.Pd.I','ogi.gaosun@sekolah.sch.id',1,169,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(71,'198001012006011020','Sandi Mulyana, S.Pd','sandi.mulyana@sekolah.sch.id',1,170,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(72,'198001012006011021','Erna Suryani, S.Pd','erna.suryani@sekolah.sch.id',1,171,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(73,'198001012006011022','Ai Wahidah, S.Ag','ai.wahidah@sekolah.sch.id',1,172,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(74,'198001012006011023','Chandra Aditya Yusup, S.Pd','chandra.aditya@sekolah.sch.id',1,173,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(75,'198001012006011024','Arif Kusmawan, S.Pd','arif.kusmawan@sekolah.sch.id',1,174,'2026-11-04 05:20:57','2026-11-04 15:04:29'),(76,'198001012006011025','Asep Taufiq Hidayat, S.Kom','asep.taufiq@sekolah.sch.id',1,175,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(77,'198001012006011026','Rendy Ichsan Kusumah, S.T','rendy.ichsan@sekolah.sch.id',1,176,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(78,'198001012006011027','Feri Rahmawan, S.Pd','feri.rahmawan@sekolah.sch.id',1,177,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(79,'198001012006011028','Ari Sandriana, S.Kom','ari.sandriana@sekolah.sch.id',1,178,'2026-11-04 05:20:57','2026-11-04 15:13:07'),(80,'198001012006011029','Yaneu Nurdian, S.Pd, M.Pd','yaneu.nurdian@sekolah.sch.id',1,179,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(81,'198001012006011030','Ucu Muhammad Khoer N, S.Pd','ucu.muhammad@sekolah.sch.id',1,180,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(82,'198001012006011031','Mira Dwi Rahmawati, S.Ag','mira.dwi@sekolah.sch.id',1,181,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(83,'198001012006011032','Aldi Maulana, S.Pd','aldi.maulana@sekolah.sch.id',1,182,'2026-11-04 05:20:57','2026-11-04 15:22:32'),(84,'198001012006011033','Eli Rosliah, S.Pd','eli.rosliah@sekolah.sch.id',1,183,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(85,'198001012006011034','Isni Nur Fitriani, S.Pd','isni.nur@sekolah.sch.id',1,184,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(86,'198001012006011035','Aanisah Putri Nur Aini, S.Pd','aanisah.putri@sekolah.sch.id',1,185,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(87,'198001012006011036','Yusi Luthfiani, S.Sos.I','yusi.luthfiani@sekolah.sch.id',1,186,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(88,'198001012006011037','Lilis Rustandy Mawardi, S.Pd., M.Pd','lilis.rustandy@sekolah.sch.id',1,187,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(89,'198001012006011038','Yusrin Noer Sabrina, S.Pd','yusrin.noer@sekolah.sch.id',1,188,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(90,'198001012006011039','Senny Indayani, S.Pd','senny.indayani@sekolah.sch.id',1,189,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(91,'198001012006011040','Ilma Amalia, S.Pd','ilma.amalia@sekolah.sch.id',1,190,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(92,'198001012006011041','Alvira Sofwatunnisa, S.Pd','alvira.sofwatunnisa@sekolah.sch.id',1,191,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(93,'198001012006011042','Risna Andriyani, S.Pd','risna.andriyani@sekolah.sch.id',1,192,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(94,'198001012006011043','Dra. Tintin Noerhayati','tintin.noerhayati@sekolah.sch.id',1,193,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(95,'198001012006011044','Rizkia Fahira, S.Pd','rizkia.fahira@sekolah.sch.id',1,194,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(96,'198001012006011045','Frasetio','frasetio@sekolah.sch.id',1,195,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(97,'198001012006011046','Ratnaningsih Hatil Aeni, S.Pd.','ratnaningsih.hatil@sekolah.sch.id',1,196,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(98,'198001012006011047','Dheny Darajatul Hasani, S.Pd.','dheny.darajatul@sekolah.sch.id',1,197,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(99,'198001012006011048','Fahrullah, S.Pd','fahrullah@sekolah.sch.id',1,198,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(100,'198001012006011049','Aan Andriana, S.T','aan.andriana@sekolah.sch.id',1,199,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(101,'198001012006011050','Fitri Fauziah, S.T','fitri.fauziah@sekolah.sch.id',1,200,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(102,'198001012006011051','Nida Aulia, S.Pd','nida.aulia@sekolah.sch.id',1,201,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(103,'198001012006011052','Sri Nurrizki Putri Krisnadi, S.Pd','sri.nurrizki@sekolah.sch.id',1,202,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(104,'198001012006011053','Riesy Putri Navia, S.Kom','riesy.putri@sekolah.sch.id',1,203,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(105,'198001012006011054','Rurry Pebriani Maryadi, S.Pd','rurry.pebriani@sekolah.sch.id',1,204,'2026-11-04 05:20:57','2026-11-04 07:09:11'),(106,'198001012006011055','Ivan Rizal, S.Pd','ivan.rizal@sekolah.sch.id',1,205,'2026-11-04 05:20:57','2026-11-04 07:09:11');
/*!40000 ALTER TABLE `t_guru` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_kelas`
--

DROP TABLE IF EXISTS `t_kelas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=112 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_kelas`
--

LOCK TABLES `t_kelas` WRITE;
/*!40000 ALTER TABLE `t_kelas` DISABLE KEYS */;
INSERT INTO `t_kelas` VALUES (11,'XII RPL 1','XII','RPL',1,'2026-09-28 06:01:44','2026-11-04 06:17:13'),(26,'XII RPL 2','XII','RPL',1,'2026-09-28 06:01:44','2026-09-28 06:01:44'),(111,'XI RPL 1','XI','RPL',1,'2026-11-04 07:41:15','2026-11-04 07:41:27');
/*!40000 ALTER TABLE `t_kelas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_kelas_siswa`
--

DROP TABLE IF EXISTS `t_kelas_siswa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=158 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_kelas_siswa`
--

LOCK TABLES `t_kelas_siswa` WRITE;
/*!40000 ALTER TABLE `t_kelas_siswa` DISABLE KEYS */;
INSERT INTO `t_kelas_siswa` VALUES (104,101,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(105,102,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(106,103,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(107,104,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(108,105,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(109,106,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(110,107,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(111,108,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(112,109,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(113,110,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(114,111,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(115,112,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(116,113,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(117,114,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(118,115,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(119,116,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(120,117,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(121,118,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(122,119,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(123,120,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(124,121,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(125,122,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(126,123,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(127,124,101,11,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(128,125,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(129,126,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(130,127,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(131,128,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(132,129,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(133,130,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(134,131,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(135,132,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(136,133,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(137,134,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(138,135,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(139,136,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(140,137,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(141,138,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(142,139,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(143,140,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(144,141,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(145,142,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(146,143,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(147,144,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(148,145,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(149,146,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(150,147,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(151,148,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(152,149,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36'),(153,150,101,26,'2026-07-01','2027-06-30',1,'2026-11-04 06:29:36','2026-11-04 06:29:36');
/*!40000 ALTER TABLE `t_kelas_siswa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_pelanggaran`
--

DROP TABLE IF EXISTS `t_pelanggaran`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=121 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_pelanggaran`
--

LOCK TABLES `t_pelanggaran` WRITE;
/*!40000 ALTER TABLE `t_pelanggaran` DISABLE KEYS */;
INSERT INTO `t_pelanggaran` VALUES (103,103,'P001','Terlambat masuk kelas',5,'Datang terlambat tanpa alasan yang sah.',1,'2026-11-09 04:07:59','2026-11-09 04:13:39'),(104,104,'P002','Bolos pelajaran',15,'Tidak mengikuti pelajaran tanpa izin guru.',1,'2026-11-09 04:07:59','2026-11-09 04:13:39'),(105,105,'P003','Merokok di lingkungan sekolah',25,'Merokok atau membawa rokok di lingkungan sekolah.',1,'2026-11-09 04:07:59','2026-11-09 04:17:42'),(107,103,'P004','Seragam tidak lengkap',5,'Tidak memakai atribut seragam sesuai ketentuan.',1,'2026-11-09 04:13:39','2026-11-09 04:13:39'),(108,103,'P005','Tidak mengerjakan tugas/PR',5,'Tidak menyelesaikan tugas yang diberikan guru.',1,'2026-11-09 04:13:39','2026-11-09 04:13:39'),(109,103,'P006','Membuang sampah sembarangan',5,'Membuang sampah tidak pada tempatnya.',1,'2026-11-09 04:13:39','2026-11-09 04:13:39'),(110,103,'P007','Ribut saat pelajaran',5,'Mengobrol atau membuat keributan saat pelajaran.',1,'2026-11-09 04:13:39','2026-11-09 04:13:39'),(112,104,'P008','Tidak ikut upacara bendera',10,'Tidak mengikuti upacara tanpa alasan yang sah.',1,'2026-11-09 04:13:39','2026-11-09 04:13:39'),(113,104,'P009','Berkata kasar/menghina teman',15,'Menggunakan kata kasar atau menghina teman.',1,'2026-11-09 04:13:39','2026-11-09 04:13:39'),(114,104,'P010','Merusak fasilitas sekolah',15,'Merusak sarana atau fasilitas sekolah.',1,'2026-11-09 04:13:39','2026-11-09 04:13:39'),(115,104,'P011','Membawa HP saat pelajaran',10,'Menggunakan HP tanpa izin saat kegiatan belajar.',1,'2026-11-09 04:13:39','2026-11-09 04:13:39'),(117,105,'P012','Berkelahi di sekolah',30,'Terlibat perkelahian di lingkungan sekolah.',1,'2026-11-09 04:13:39','2026-11-09 04:13:39'),(118,105,'P013','Mencuri barang orang lain',50,'Mengambil barang milik orang lain tanpa izin.',1,'2026-11-09 04:13:39','2026-11-09 04:13:39'),(119,105,'P014','Memalsukan tanda tangan',25,'Memalsukan tanda tangan guru atau orang tua.',1,'2026-11-09 04:13:39','2026-11-09 04:13:39'),(120,105,'P015','Mengancam/mem-bully teman',40,'Mengancam atau melakukan perundungan terhadap teman.',1,'2026-11-09 04:13:39','2026-11-09 04:13:39');
/*!40000 ALTER TABLE `t_pelanggaran` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_pelanggaran_kategori`
--

DROP TABLE IF EXISTS `t_pelanggaran_kategori`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_pelanggaran_kategori` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) DEFAULT NULL,
  `deskripsi` text NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_t_pelanggaran_kategori_nama` (`nama`)
) ENGINE=InnoDB AUTO_INCREMENT=109 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_pelanggaran_kategori`
--

LOCK TABLES `t_pelanggaran_kategori` WRITE;
/*!40000 ALTER TABLE `t_pelanggaran_kategori` DISABLE KEYS */;
INSERT INTO `t_pelanggaran_kategori` VALUES (103,'Ringan','Pelanggaran ringan: tidak mengganggu kegiatan belajar secara langsung.',1,'2026-11-09 04:07:59','2026-11-09 04:13:39'),(104,'Sedang','Pelanggaran sedang: mengganggu kegiatan belajar atau tata tertib sekolah.',1,'2026-11-09 04:07:59','2026-11-09 04:13:39'),(105,'Berat','Pelanggaran berat: merugikan diri sendiri, orang lain, atau nama sekolah.',1,'2026-11-09 04:07:59','2026-11-09 04:13:39');
/*!40000 ALTER TABLE `t_pelanggaran_kategori` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_pelanggaran_siswa`
--

DROP TABLE IF EXISTS `t_pelanggaran_siswa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_pelanggaran_siswa`
--

LOCK TABLES `t_pelanggaran_siswa` WRITE;
/*!40000 ALTER TABLE `t_pelanggaran_siswa` DISABLE KEYS */;
INSERT INTO `t_pelanggaran_siswa` VALUES (2,NULL,121,'RANGGA RAMADIYANTO NURHAKIM',11,'XII RPL 1',105,0,105,NULL,'','2026-11-04','merokok',25,'Diberi teguran & pembinaan','','2026-11-04 06:15:12','2026-11-09 04:13:39');
/*!40000 ALTER TABLE `t_pelanggaran_siswa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_siswa`
--

DROP TABLE IF EXISTS `t_siswa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=10000 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_siswa`
--

LOCK TABLES `t_siswa` WRITE;
/*!40000 ALTER TABLE `t_siswa` DISABLE KEYS */;
INSERT INTO `t_siswa` VALUES (101,'202600001',NULL,'ALFIN RAHADIAN','L','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:16'),(102,'202600002',NULL,'ALISA RAMADHANI','P','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),(103,'202600003',NULL,'AMA AURA LIVIA','P','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),(104,'202600004',NULL,'ANIS KHOERUNNISA NUR RAMADANI','P','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),(105,'202600005',NULL,'AZMIL AMIRA','P','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),(106,'202600006',NULL,'AZRIL AKBAR FAWZI ABDILLAH','L','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),(107,'202600007',NULL,'ERLANGGA SAPUTRA HIDAYAT','L','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),(108,'202600008',NULL,'HILMIA HUMMA TUNNUNJAH','P','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),(109,'202600009',NULL,'ILMA NAJYA','P','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),(110,'202600010',NULL,'JULIANA DHARMA PANGESTU','P','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),(111,'202600011',NULL,'KAYLA NUR FADILAH','P','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),(112,'202600012',NULL,'MOZA DINA NABILLAH','P','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),(113,'202600013',NULL,'MUHAMAD ALFARIZI','L','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),(114,'202600014',NULL,'MUHAMAD FAHRIN ADAM','L','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),(115,'202600015',NULL,'MUHAMAD ZIDAN MAULIDAN','L','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),(116,'202600016',NULL,'MUHAMAD ZULFAN NURFALANSYAH','L','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),(117,'202600017',NULL,'NATIYA NUR AZKIYA','P','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),(118,'202600018',NULL,'NOVITA PUSPITASARI','P','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),(119,'202600019',NULL,'NUCKY ANDREAS FEBRIAN','L','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),(120,'202600020',NULL,'RADITYA JETI PRASETYA','L','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),(121,'202600021',NULL,'RANGGA RAMADIYANTO NURHAKIM','L','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),(122,'202600022',NULL,'RISQI MUHAMAD IHWANI','L','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),(123,'202600023',NULL,'SALWA SABILATUL BARAKAH','P','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),(124,'202600024',NULL,'TAZKIA ZAHRA','P','1900-01-01','',1,'2026-11-04 05:16:24','2026-11-04 06:38:06'),(125,'102410544',NULL,'ADITTIA NUR LANTIP','L','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),(126,'102410545',NULL,'AGNIA ROSYA NOERILLAH','P','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),(127,'102410546',NULL,'ANISA AMIRULLA','P','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),(128,'102410547',NULL,'ARADEA KANDI','L','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),(129,'102410548',NULL,'ARFA DIESA OKMALELIZA','P','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),(130,'102410549',NULL,'FITRI NUR SYIAMI','P','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),(131,'102410550',NULL,'JUNITA PUTRI NUR RAHMAN','P','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),(132,'102410551',NULL,'MOHAMMAD ATALARIK ALI SYAHBANA','L','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),(133,'102410552',NULL,'MUHAMAD FAHRIL FAHRUROZI','L','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),(134,'102410750',NULL,'MUHAMMAD ADNAN SYAEFUL ISLAMI','L','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),(135,'102410553',NULL,'MUHAMMAD ZAHRU AQILA FIKRI','L','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),(136,'102410554',NULL,'NADIA ZULFA SORAYA','P','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),(137,'102410555',NULL,'NAZRIL ILHAM','L','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),(138,'102410556',NULL,'PUSPITA DEWI','P','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),(139,'102410558',NULL,'RAKHA MUHAMMAD HAYKAL','L','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),(140,'102410559',NULL,'RANI MEILANI','P','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),(141,'102410560',NULL,'REIHAN MUZAKI','L','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),(142,'102410561',NULL,'RESA JULIANSYAH','L','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),(143,'102410562',NULL,'RIZKI PERMATA SUGIANTO','L','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),(144,'102410563',NULL,'SAHIRA FIRAJ','P','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),(145,'102410564',NULL,'SENDI MARDIAN','L','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),(146,'102410755',NULL,'SHAHAIDAN NIZZAR','L','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),(147,'102410565',NULL,'SHERLY SATRIANI','P','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),(148,'102410566',NULL,'SOFI NURJANAH','P','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),(149,'102410567',NULL,'SURAIYA SALSABILA AZZAHRA','P','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01'),(150,'102410568',NULL,'TONI ANTONIO','L','1900-01-01','',1,'2026-11-04 05:25:01','2026-11-04 05:25:01');
/*!40000 ALTER TABLE `t_siswa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_tahun_ajaran`
--

DROP TABLE IF EXISTS `t_tahun_ajaran`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=112 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_tahun_ajaran`
--

LOCK TABLES `t_tahun_ajaran` WRITE;
/*!40000 ALTER TABLE `t_tahun_ajaran` DISABLE KEYS */;
INSERT INTO `t_tahun_ajaran` VALUES (101,'2026/2027','2026-07-01','2027-06-30',1,'2026-11-04 05:36:36','2026-11-04 05:36:36');
/*!40000 ALTER TABLE `t_tahun_ajaran` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_users`
--

DROP TABLE IF EXISTS `t_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=221 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_users`
--

LOCK TABLES `t_users` WRITE;
/*!40000 ALTER TABLE `t_users` DISABLE KEYS */;
INSERT INTO `t_users` VALUES (1,'Administrator','admin@ukk2026.com','2026-10-02 01:48:51','$2y$10$RS53mKxMjN8wP7lPw1ZSn.4ef06mI0yzBHBRvUyBJzUpM4830o.Au','','Admin','2026-10-02 01:48:51','2026-10-02 01:48:51'),(2,'Bapak Guru','guru@ukk2026.com','2026-10-02 01:57:34','$2y$10$GHyInVTPyZNxGFAWxrCvQe3b1nI.0jkzVWd.Zcg4waGWGjx06qsZS','','Guru','2026-10-02 01:57:34','2026-10-02 01:57:34'),(3,'Ibu Wali Kelas','walikelas@ukk2026.com','2026-10-02 01:57:34','$2y$10$UuGg3CXnKYPiX0zEPXaxJOnan8OSv9p9Pv5xfOdOu7efAlAvy/Jxa','','Guru','2026-10-02 01:57:34','2026-10-02 01:57:34'),(4,'nonok','nonok1@gmail.com','2026-10-02 03:25:49','guru123','','guru','2026-10-02 03:25:49','2026-10-02 03:25:49'),(151,'Wawan Kurniawan, S.Pd.I','wawan.kurniawan@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 06:17:13'),(152,'Ai Nuryaidah, S.Pd','ai.nuryaidah@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:35:49'),(153,'Maya Permata Endah, S.Pd','maya.permata@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(154,'Dody Jaenudin, S.Pd.','dody.jaenudin@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(155,'Dedi Kardiman, S.Pd','dedi.kardiman@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(156,'Uca Musa Yusenda, S.Pd','uca.musa@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(157,'Drs. Ahmad Nur Mukhlis','ahmad.nur@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(158,'Drs. Heri','heri@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(159,'Asep Bastaman','asep.bastaman@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(160,'H. Encep Maemun, S.T., M.Pd','encep.maemun@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(161,'Opik','opik@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(162,'Ida Farida, S.Pd','ida.farida@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(163,'Idah Mahmudah, S.Pd','idah.mahmudah@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(164,'Drs. Muhamad Ali','muhamad.ali@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(165,'Helmi Ali Akbar, S.Pd.I','helmi.ali@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(166,'Syeni Suswarini, S.Pd','syeni.suswarini@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(167,'Wiwin Yulianti, S.Pd','wiwin.yulianti@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(168,'Ansor Muhajir, S.T','ansor.muhajir@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(169,'Ogi Gaosun Gias, S.Pd.I','ogi.gaosun@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(170,'Sandi Mulyana, S.Pd','sandi.mulyana@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(171,'Erna Suryani, S.Pd','erna.suryani@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(172,'Ai Wahidah, S.Ag','ai.wahidah@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(173,'Chandra Aditya Yusup, S.Pd','chandra.aditya@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(174,'Arif Kusmawan, S.Pd','arif.kusmawan@sekolah.sch.id','2026-11-04 05:20:57','$2y$12$Nhh5mcoOSp5IbvMeOEfXSO.rDWn3K4.myqKx3V.4QGkGDXt/npe/u','','guru','2026-11-04 05:20:57','2026-11-04 15:04:29'),(175,'Asep Taufiq Hidayat, S.Kom','asep.taufiq@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(176,'Rendy Ichsan Kusumah, S.T','rendy.ichsan@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(177,'Feri Rahmawan, S.Pd','feri.rahmawan@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(178,'Ari Sandriana, S.Kom','ari.sandriana@sekolah.sch.id','2026-11-04 05:20:57','$2y$12$mCQAsfS2ADwnk2Ugkrhp5.xSTrGWH3V587p7Uqk/pUjtq.cwh9kEW','','guru','2026-11-04 05:20:57','2026-11-04 15:13:07'),(179,'Yaneu Nurdian, S.Pd, M.Pd','yaneu.nurdian@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(180,'Ucu Muhammad Khoer N, S.Pd','ucu.muhammad@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(181,'Mira Dwi Rahmawati, S.Ag','mira.dwi@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(182,'Aldi Maulana, S.Pd','aldi.maulana@sekolah.sch.id','2026-11-04 05:20:57','$2y$12$FBKKcdIq3KOkXv29nfbC9OSM1460tWuAgeeVDTPzawcFd.H8KEhRe','','guru','2026-11-04 05:20:57','2026-11-04 15:22:32'),(183,'Eli Rosliah, S.Pd','eli.rosliah@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(184,'Isni Nur Fitriani, S.Pd','isni.nur@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(185,'Aanisah Putri Nur Aini, S.Pd','aanisah.putri@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(186,'Yusi Luthfiani, S.Sos.I','yusi.luthfiani@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(187,'Lilis Rustandy Mawardi, S.Pd., M.Pd','lilis.rustandy@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(188,'Yusrin Noer Sabrina, S.Pd','yusrin.noer@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(189,'Senny Indayani, S.Pd','senny.indayani@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(190,'Ilma Amalia, S.Pd','ilma.amalia@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(191,'Alvira Sofwatunnisa, S.Pd','alvira.sofwatunnisa@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(192,'Risna Andriyani, S.Pd','risna.andriyani@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(193,'Dra. Tintin Noerhayati','tintin.noerhayati@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(194,'Rizkia Fahira, S.Pd','rizkia.fahira@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(195,'Frasetio','frasetio@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(196,'Ratnaningsih Hatil Aeni, S.Pd.','ratnaningsih.hatil@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(197,'Dheny Darajatul Hasani, S.Pd.','dheny.darajatul@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(198,'Fahrullah, S.Pd','fahrullah@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(199,'Aan Andriana, S.T','aan.andriana@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(200,'Fitri Fauziah, S.T','fitri.fauziah@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(201,'Nida Aulia, S.Pd','nida.aulia@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(202,'Sri Nurrizki Putri Krisnadi, S.Pd','sri.nurrizki@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(203,'Riesy Putri Navia, S.Kom','riesy.putri@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(204,'Rurry Pebriani Maryadi, S.Pd','rurry.pebriani@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57'),(205,'Ivan Rizal, S.Pd','ivan.rizal@sekolah.sch.id','2026-11-04 05:20:57','$2y$10$Thde/yhO9GxaV1yhCZsZH.EAJVDRgibBJpoQOilUp0pCB/X4.e5FO','','guru','2026-11-04 05:20:57','2026-11-04 05:20:57');
/*!40000 ALTER TABLE `t_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_wali_kelas`
--

DROP TABLE IF EXISTS `t_wali_kelas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_wali_kelas`
--

LOCK TABLES `t_wali_kelas` WRITE;
/*!40000 ALTER TABLE `t_wali_kelas` DISABLE KEYS */;
INSERT INTO `t_wali_kelas` VALUES (18,101,26,83,'0000-00-00','0000-00-00',1,'2026-11-04 06:25:29','2026-11-04 06:25:29'),(20,101,11,79,'0000-00-00','0000-00-00',1,'2026-11-04 15:13:44','2026-11-04 15:17:03');
/*!40000 ALTER TABLE `t_wali_kelas` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-09 11:17:45
