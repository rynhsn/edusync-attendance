-- MySQL dump 10.13  Distrib 8.4.11, for Linux (x86_64)
--
-- Host: localhost    Database: sekolahku_db
-- ------------------------------------------------------
-- Server version	8.4.11-0ubuntu0.26.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `achievements`
--

DROP TABLE IF EXISTS `achievements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `achievements` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `student_name` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `photo` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `class_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `achievement` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `level` enum('Nasional','Provinsi','Kota','Kecamatan') COLLATE utf8mb4_general_ci DEFAULT 'Kota',
  `year` year DEFAULT NULL,
  `medal` enum('gold','silver','bronze') COLLATE utf8mb4_general_ci DEFAULT 'gold',
  `sort_order` tinyint unsigned DEFAULT '0',
  `show` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `achievements`
--

LOCK TABLES `achievements` WRITE;
/*!40000 ALTER TABLE `achievements` DISABLE KEYS */;
INSERT INTO `achievements` VALUES (1,'Alya Putri',NULL,'Kelas XII IPA','Juara 1 Olimpiade Sains Nasional','Nasional',2026,'gold',1,1,'2026-09-06 11:31:50','2026-09-06 11:31:50'),(2,'Raka Firmansyah',NULL,'Kelas XI IPS','Juara 2 Lomba Debat Bahasa Inggris Provinsi','Provinsi',2026,'silver',2,1,'2026-09-06 11:31:50','2026-09-06 11:31:50'),(3,'Sinta Nurhaliza',NULL,'Kelas X IPA','Medali Emas Olimpiade Matematika','Nasional',2026,'gold',3,1,'2026-09-06 11:31:50','2026-09-06 11:31:50'),(4,'Dimas Aditya',NULL,'Kelas XII IPA','Juara 1 Taekwondo Tingkat Kota','Kota',2026,'bronze',4,1,'2026-09-06 11:31:50','2026-09-06 11:31:50'),(5,'Nindy Ayu',NULL,'Kelas XI IPA','Harapan 1 Lomba Karya Ilmiah Remaja','Provinsi',2025,'silver',5,1,'2026-09-06 11:31:50','2026-09-06 11:31:50'),(6,'Bayu Prasetyo',NULL,'Kelas X IPS','Juara 3 Desain Grafis Tingkat Nasional','Nasional',2025,'gold',6,1,'2026-09-06 11:31:50','2026-09-06 11:31:50');
/*!40000 ALTER TABLE `achievements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_messages`
--

DROP TABLE IF EXISTS `contact_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact_messages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_general_ci NOT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_messages_read` (`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_messages`
--

LOCK TABLES `contact_messages` WRITE;
/*!40000 ALTER TABLE `contact_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `downloads`
--

DROP TABLE IF EXISTS `downloads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `downloads` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `url` varchar(500) COLLATE utf8mb4_general_ci NOT NULL,
  `file_size` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sort_order` int unsigned DEFAULT '0',
  `show` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_downloads_category` (`category`),
  KEY `idx_downloads_sort_order` (`sort_order`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `downloads`
--

LOCK TABLES `downloads` WRITE;
/*!40000 ALTER TABLE `downloads` DISABLE KEYS */;
INSERT INTO `downloads` VALUES (1,'Kalender Akademik','Kalender Akademik 2026/2027','Kalender akademik tahun pelajaran 2026/2027 untuk seluruh jenjang pendidikan.','https://drive.google.com/example-kalender-akademik','1.2 MB',0,1,'2026-09-06 11:31:51','2026-09-06 11:31:51'),(2,'Kalender Akademik','Jadwal UTS Semester Ganjil 2026/2027','Jadwal pelaksanaan Ujian Tengah Semester Ganjil tahun ajaran 2026/2027.','https://drive.google.com/example-jadwal-uts','850 KB',1,1,'2026-09-06 11:31:51','2026-09-06 11:31:51'),(3,'Kurikulum','Kurikulum Merdeka - Panduan Implementasi','Panduan implementasi Kurikulum Merdeka untuk guru dan tenaga kependidikan.','https://drive.google.com/example-kurikulum-merdeka','3.5 MB',0,1,'2026-09-06 11:31:51','2026-09-06 11:31:51'),(4,'Kurikulum','Capaian Pembelajaran per Fase','Dokumen capaian pembelajaran untuk setiap fase dalam Kurikulum Merdeka.','https://drive.google.com/example-capaian-pembelajaran','2.1 MB',1,1,'2026-09-06 11:31:51','2026-09-06 11:31:51'),(5,'Formulir','Formulir Pendaftaran Siswa Baru','Formulir pendaftaran untuk calon siswa baru tahun ajaran 2026/2027.','https://drive.google.com/example-formulir-ppdb','450 KB',0,1,'2026-09-06 11:31:51','2026-09-06 11:31:51'),(6,'Formulir','Surat Pernyataan Orang Tua/Wali','Formulir surat pernyataan orang tua/wali untuk keperluan administrasi sekolah.','https://drive.google.com/example-surat-pernyataan','320 KB',1,1,'2026-09-06 11:31:51','2026-09-06 11:31:51');
/*!40000 ALTER TABLE `downloads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `events` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `location` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `event_date` date NOT NULL,
  `event_time` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sort_order` tinyint unsigned DEFAULT '0',
  `status` enum('upcoming','ongoing','completed','cancelled') COLLATE utf8mb4_general_ci DEFAULT 'upcoming',
  `show` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_events_date` (`event_date`),
  KEY `idx_events_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
INSERT INTO `events` VALUES (1,'Upacara HUT RI ke-81','upacara-hut-ri-ke-81','Seluruh siswa dan guru mengikuti upacara peringatan Hari Kemerdekaan RI.','Lapangan Sekolah','2026-08-15','07.00 WIB',1,'upcoming',1,'2026-09-06 11:31:51','2026-09-06 11:31:51'),(2,'Lomba Antar Kelas','lomba-antar-kelas','Perlombaan antar kelas meliputi cerdas cermat, seni, dan olahraga.','Aula Sekolah','2026-08-22','08.00 WIB',2,'upcoming',1,'2026-09-06 11:31:51','2026-09-06 11:31:51'),(3,'Kegiatan Bakti Sosial','kegiatan-bakti-sosial','Kunjungan dan bakti sosial siswa ke panti asuhan di sekitar sekolah.','Panti Asuhan','2026-09-05','07.30 WIB',3,'upcoming',1,'2026-09-06 11:31:51','2026-09-06 11:31:51'),(4,'Pentas Seni Akhir Semester','pentas-seni-akhir-semester','Pertunjukan seni dari siswa sebagai puncak kegiatan akhir semester.','Gedung Serba Guna','2026-09-12','09.00 WIB',4,'upcoming',1,'2026-09-06 11:31:51','2026-09-06 11:31:51');
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `extracurriculars`
--

DROP TABLE IF EXISTS `extracurriculars`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `extracurriculars` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `icon` varchar(50) COLLATE utf8mb4_general_ci DEFAULT 'star',
  `icon_color` varchar(7) COLLATE utf8mb4_general_ci DEFAULT '#14b8a6',
  `sort_order` tinyint unsigned DEFAULT '0',
  `show` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `extracurriculars`
--

LOCK TABLES `extracurriculars` WRITE;
/*!40000 ALTER TABLE `extracurriculars` DISABLE KEYS */;
INSERT INTO `extracurriculars` VALUES (1,'Robotik','Belajar merakit dan memprogram robot untuk kompetisi nasional.','fas fa-microchip','#14b8a6',1,1,'2026-09-06 11:31:50','2026-09-06 11:31:50'),(2,'Paduan Suara','Mengembangkan bakat vokal dan apresiasi musik.','fas fa-music','#f59e0b',2,1,'2026-09-06 11:31:50','2026-09-06 11:31:50'),(3,'Olahraga','Futsal, basket, voli, taekwondo, dan atletik.','fas fa-futbol','#ef4444',3,1,'2026-09-06 11:31:50','2026-09-06 11:31:50'),(4,'Seni Rupa','Melukis, menggambar, desain grafis, dan kriya.','fas fa-paint-brush','#8b5cf6',4,1,'2026-09-06 11:31:50','2026-09-06 11:31:50'),(5,'Jurnalistik','Menulis, fotografi, dan publikasi majalah sekolah.','fas fa-book','#0ea5e9',5,1,'2026-09-06 11:31:50','2026-09-06 11:31:50'),(6,'Pramuka','Pembentukan karakter lewat kegiatan kepramukaan.','fas fa-leaf','#10b981',6,1,'2026-09-06 11:31:50','2026-09-06 11:31:50');
/*!40000 ALTER TABLE `extracurriculars` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `faq`
--

DROP TABLE IF EXISTS `faq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `faq` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `question` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `answer` text COLLATE utf8mb4_general_ci NOT NULL,
  `category` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sort_order` tinyint unsigned DEFAULT '0',
  `show` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `faq`
--

LOCK TABLES `faq` WRITE;
/*!40000 ALTER TABLE `faq` DISABLE KEYS */;
INSERT INTO `faq` VALUES (1,'Bagaimana cara mendaftar di SekolahKu?','Pendaftaran dapat dilakukan secara online melalui portal SPMB SekolahKu atau datang langsung ke kantor pendaftaran di sekolah. Informasi lengkap mengenai persyaratan dan jadwal pendaftaran dapat dilihat di halaman SPMB.',NULL,1,1,'2026-09-06 11:31:51','2026-09-06 11:31:51'),(2,'Apa saja program unggulan yang tersedia?','SekolahKu memiliki program unggulan seperti Sains & Teknologi, Seni & Budaya, Olahraga, Bahasa Asing, Karakter & Religi, serta Digital Literacy. Selain itu terdapat berbagai ekstrakurikuler yang dapat dipilih sesuai minat siswa.',NULL,2,1,'2026-09-06 11:31:51','2026-09-06 11:31:51'),(3,'Apakah SekolahKu menyediakan beasiswa?','Ya, SekolahKu menyediakan program beasiswa bagi siswa berprestasi akademik dan non-akademik, serta beasiswa untuk siswa kurang mampu. Informasi lebih lanjut dapat menghubungi bagian kesiswaan.',NULL,3,1,'2026-09-06 11:31:51','2026-09-06 11:31:51'),(4,'Bagaimana sistem pembelajaran di SekolahKu?','SekolahKu menerapkan Kurikulum Merdeka dengan pendekatan pembelajaran aktif, kolaboratif, dan berbasis proyek. Kami juga mengintegrasikan teknologi digital dalam proses belajar mengajar.',NULL,4,1,'2026-09-06 11:31:51','2026-09-06 11:31:51'),(5,'Bagaimana cara menghubungi SekolahKu?','Anda dapat menghubungi kami melalui telepon (021) 1234-5678, email info@sekolahku.sch.id, atau datang langsung ke Jl. Pendidikan No. 123, Kota Pelajar.',NULL,5,1,'2026-09-06 11:31:51','2026-09-06 11:31:51');
/*!40000 ALTER TABLE `faq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `galleries`
--

DROP TABLE IF EXISTS `galleries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `galleries` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `caption` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT '0',
  `sort_order` tinyint unsigned DEFAULT '0',
  `show` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `galleries`
--

LOCK TABLES `galleries` WRITE;
/*!40000 ALTER TABLE `galleries` DISABLE KEYS */;
INSERT INTO `galleries` VALUES (1,'Kegiatan','https://placehold.co/800x600/0c4a6e/ffffff?text=Upacara+Bendera','Upacara bendera setiap hari Senin',1,1,1,'2026-09-06 11:31:51','2026-09-06 11:31:51'),(2,'Kegiatan','https://placehold.co/800x600/14b8a6/ffffff?text=Kegiatan+Belajar','Suasana belajar mengajar di kelas',1,2,1,'2026-09-06 11:31:51','2026-09-06 11:31:51'),(3,'Prestasi','https://placehold.co/800x600/f59e0b/ffffff?text=Penghargaan','Penyerahan penghargaan kepada siswa berprestasi',1,3,1,'2026-09-06 11:31:51','2026-09-06 11:31:51'),(4,'Kegiatan','https://placehold.co/800x600/ef4444/ffffff?text=Olahraga','Kegiatan olahraga dan ekstrakurikuler',0,4,1,'2026-09-06 11:31:51','2026-09-06 11:31:51'),(5,'Akademik','https://placehold.co/800x600/8b5cf6/ffffff?text=Lab+Komputer','Praktik di laboratorium komputer',0,5,1,'2026-09-06 11:31:51','2026-09-06 11:31:51'),(6,'Kegiatan','https://placehold.co/800x600/0ea5e9/ffffff?text=Seni+Budaya','Pentas seni budaya siswa',0,6,1,'2026-09-06 11:31:51','2026-09-06 11:31:51');
/*!40000 ALTER TABLE `galleries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_items`
--

DROP TABLE IF EXISTS `menu_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu_items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int unsigned DEFAULT NULL,
  `title` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `target` enum('_self','_blank') COLLATE utf8mb4_general_ci DEFAULT '_self',
  `icon` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `section_key` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sort_order` int unsigned DEFAULT '0',
  `status` tinyint(1) DEFAULT '1',
  `type` varchar(20) COLLATE utf8mb4_general_ci DEFAULT 'custom',
  `page_id` int unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `page_id` (`page_id`),
  CONSTRAINT `menu_items_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `menu_items_ibfk_2` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_items`
--

LOCK TABLES `menu_items` WRITE;
/*!40000 ALTER TABLE `menu_items` DISABLE KEYS */;
INSERT INTO `menu_items` VALUES (1,NULL,'Beranda','','_self',NULL,NULL,0,1,'system',NULL,'2026-09-06 11:31:51','2026-09-06 11:31:51'),(2,NULL,'Profil','#profile','_self',NULL,'profile',1,1,'system',NULL,'2026-09-06 11:31:51','2026-09-06 11:31:51'),(3,NULL,'Program','#programs','_self',NULL,'programs',2,1,'system',NULL,'2026-09-06 11:31:51','2026-09-06 11:31:51'),(4,NULL,'Berita','news','_self',NULL,NULL,3,1,'system',NULL,'2026-09-06 11:31:51','2026-09-06 11:31:51'),(5,NULL,'Kontak','#contact','_self',NULL,'contact',4,1,'system',NULL,'2026-09-06 11:31:51','2026-09-06 11:31:51'),(6,NULL,'Lainnya','#','_self',NULL,NULL,5,1,'system',NULL,'2026-09-06 11:31:51','2026-09-06 11:31:51'),(7,6,'Pengajar','#teachers','_self',NULL,'teachers',0,1,'system',NULL,'2026-09-06 11:31:51','2026-09-06 11:31:51'),(8,6,'Prestasi','#achievements','_self',NULL,'achievements',1,1,'system',NULL,'2026-09-06 11:31:51','2026-09-06 11:31:51'),(9,6,'Ekstrakurikuler','#extracurriculars','_self',NULL,'extracurriculars',2,1,'system',NULL,'2026-09-06 11:31:51','2026-09-06 11:31:51'),(10,6,'Testimoni','#testimonials','_self',NULL,'testimonials',3,1,'system',NULL,'2026-09-06 11:31:51','2026-09-06 11:31:51'),(11,6,'Galeri','#gallery','_self',NULL,'gallery',4,1,'system',NULL,'2026-09-06 11:31:51','2026-09-06 11:31:51'),(12,6,'Agenda','#events','_self',NULL,'events',5,1,'system',NULL,'2026-09-06 11:31:51','2026-09-06 11:31:51'),(13,6,'FAQ','#faq','_self',NULL,'faq',6,1,'system',NULL,'2026-09-06 11:31:51','2026-09-06 11:31:51'),(14,6,'Download','downloads','_self',NULL,NULL,7,1,'system',NULL,'2026-09-06 11:31:51','2026-09-06 11:31:51');
/*!40000 ALTER TABLE `menu_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `news` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tags` json DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `excerpt` text COLLATE utf8mb4_general_ci,
  `content` longtext COLLATE utf8mb4_general_ci,
  `image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `author` varchar(100) COLLATE utf8mb4_general_ci DEFAULT 'Admin',
  `published_at` datetime DEFAULT NULL,
  `status` enum('draft','published','archived') COLLATE utf8mb4_general_ci DEFAULT 'draft',
  `is_featured` tinyint(1) DEFAULT '0',
  `meta_title` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `meta_description` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `view_count` int unsigned DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_news_status` (`status`),
  KEY `idx_news_published` (`published_at`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news`
--

LOCK TABLES `news` WRITE;
/*!40000 ALTER TABLE `news` DISABLE KEYS */;
INSERT INTO `news` VALUES (1,'Prestasi','[\"prestasi\", \"olimpiade\", \"sains\"]','Siswa SekolahKu Raih Medali Emas Olimpiade Sains Nasional','siswa-sekolahku-raih-medali-emas-olimpiade-sains-nasional','Alya Putri, siswa kelas XII IPA, berhasil meraih medali emas dalam ajang Olimpiade Sains Nasional yang diselenggarakan di Jakarta.','<p>Prestasi membanggakan kembali diraih oleh siswa SekolahKu. Alya Putri, siswa kelas XII IPA, berhasil meraih <strong>medali emas</strong> dalam ajang Olimpiade Sains Nasional (OSN) 2026 yang diselenggarakan di Jakarta pada tanggal 15-20 Maret 2026.</p><blockquote>\"Saya sangat bersyukur dan bangga bisa mengharumkan nama SekolahKu. Terima kasih kepada guru pembimbing yang selalu mendukung saya.\"</blockquote><p>Kepala SekolahKu, Dr. Sari Wijaya, M.Pd., menyampaikan apresiasi setinggi-tingginya atas prestasi yang diraih. \"Semoga prestasi ini menjadi motivasi bagi siswa lainnya untuk terus berprestasi,\" ujarnya.</p>',NULL,'Admin','2026-03-22 08:00:00','published',1,NULL,NULL,0,'2026-09-06 11:31:50','2026-09-06 11:31:50'),(2,'Kegiatan','[\"kegiatan\", \"bakti-sosial\", \"peduli\"]','Kegiatan Bakti Sosial Siswa SekolahKu ke Panti Asuhan','kegiatan-bakti-sosial-siswa-sekolahku-ke-panti-asuhan','Siswa SekolahKu mengadakan kegiatan bakti sosial ke panti asuhan sebagai bagian dari program pendidikan karakter.','<p>Sebanyak 50 siswa SekolahKu mengadakan kunjungan dan bakti sosial ke Panti Asuhan Harapan Mulia pada Sabtu, 5 September 2026. Kegiatan ini merupakan bagian dari program pendidikan karakter yang rutin dilaksanakan setiap semester.</p><p>Dalam kegiatan tersebut, siswa menyalurkan donasi berupa sembako, alat tulis, dan pakaian layak pakai. Selain itu, siswa juga berinteraksi dan bermain bersama anak-anak panti asuhan.</p>',NULL,'Admin','2026-09-06 10:00:00','published',1,NULL,NULL,0,'2026-09-06 11:31:50','2026-09-06 11:31:50'),(3,'Pengumuman','[\"spmb\", \"pendaftaran\", \"siswa-baru\"]','Pendaftaran SPMB Tahun Ajaran 2026/2027 Telah Dibuka','pendaftaran-spmb-tahun-ajaran-2026-2027-telah-dibuka','Penerimaan Peserta Didik Baru (SPMB) untuk tahun ajaran 2026/2027 telah resmi dibuka. Simak informasi lengkapnya di sini.','<p>SekolahKu resmi membuka pendaftaran Penerimaan Peserta Didik Baru (SPMB) untuk tahun ajaran 2026/2027. Pendaftaran dimulai dari tanggal 1 Juni hingga 31 Juli 2026.</p><p>Calon siswa dapat mendaftar secara online melalui portal SPMB SekolahKu atau datang langsung ke kantor pendaftaran. Persyaratan lengkap dan jadwal seleksi dapat diunduh di website resmi SekolahKu.</p>',NULL,'Admin','2026-06-01 07:00:00','published',0,NULL,NULL,0,'2026-09-06 11:31:50','2026-09-06 11:31:50');
/*!40000 ALTER TABLE `news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `news_comments`
--

DROP TABLE IF EXISTS `news_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `news_comments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `news_id` int unsigned NOT NULL,
  `parent_id` int unsigned DEFAULT NULL,
  `name` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `comment` text COLLATE utf8mb4_general_ci NOT NULL,
  `is_approved` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `news_id` (`news_id`),
  KEY `parent_id` (`parent_id`),
  KEY `idx_comments_approved` (`is_approved`),
  CONSTRAINT `news_comments_ibfk_1` FOREIGN KEY (`news_id`) REFERENCES `news` (`id`) ON DELETE CASCADE,
  CONSTRAINT `news_comments_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `news_comments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news_comments`
--

LOCK TABLES `news_comments` WRITE;
/*!40000 ALTER TABLE `news_comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `news_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_general_ci,
  `excerpt` text COLLATE utf8mb4_general_ci,
  `image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('draft','published') COLLATE utf8mb4_general_ci DEFAULT 'draft',
  `meta_title` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `meta_description` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `programs`
--

DROP TABLE IF EXISTS `programs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `programs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `icon` varchar(50) COLLATE utf8mb4_general_ci DEFAULT 'star',
  `link_url` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `link_text` varchar(100) COLLATE utf8mb4_general_ci DEFAULT 'Selengkapnya',
  `sort_order` tinyint unsigned DEFAULT '0',
  `show` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `programs`
--

LOCK TABLES `programs` WRITE;
/*!40000 ALTER TABLE `programs` DISABLE KEYS */;
INSERT INTO `programs` VALUES (1,'Sains & Teknologi','Laboratorium modern dan pembelajaran berbasis proyek untuk riset dan inovasi.','fas fa-flask',NULL,'Selengkapnya',1,1,'2026-09-06 11:31:50','2026-09-06 11:31:50'),(2,'Seni & Budaya','Wadah pengembangan bakat seni melalui musik, tari, teater, dan seni rupa.','fas fa-palette',NULL,'Selengkapnya',2,1,'2026-09-06 11:31:50','2026-09-06 11:31:50'),(3,'Olahraga','Fasilitas lengkap dan pembinaan atlet muda di berbagai cabang olahraga.','fas fa-running',NULL,'Selengkapnya',3,1,'2026-09-06 11:31:50','2026-09-06 11:31:50'),(4,'Bahasa Asing','Program bilingual dengan native speaker untuk menghadapi era globalisasi.','fas fa-globe',NULL,'Selengkapnya',4,1,'2026-09-06 11:31:50','2026-09-06 11:31:50'),(5,'Karakter & Religi','Pendidikan karakter berbasis nilai agama untuk pribadi berakhlak mulia.','fas fa-hand-holding-heart',NULL,'Selengkapnya',5,1,'2026-09-06 11:31:50','2026-09-06 11:31:50'),(6,'Digital Literacy','Pembelajaran coding, desain grafis, dan literasi digital untuk era 4.0.','fas fa-laptop-code',NULL,'Selengkapnya',6,1,'2026-09-06 11:31:50','2026-09-06 11:31:50');
/*!40000 ALTER TABLE `programs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `value` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'site_name','SekolahKu','2026-09-06 11:31:50','2026-09-06 11:31:50'),(2,'site_tagline','Membangun Generasi Cerdas, Berkarakter, dan Berprestasi','2026-09-06 11:31:50','2026-09-06 11:31:50'),(3,'site_description','Sekolah unggulan yang berkomitmen mencetak generasi penerus bangsa yang berkualitas','2026-09-06 11:31:50','2026-09-06 11:31:50'),(4,'site_logo_text','SekolahKu','2026-09-06 11:31:50','2026-09-06 11:31:50'),(5,'site_logo_icon','graduation-cap','2026-09-06 11:31:50','2026-09-06 11:31:50'),(6,'site_url','https://sekolahku.sch.id','2026-09-06 11:31:50','2026-09-06 11:31:50'),(7,'contact_phone','(021) 1234-5678','2026-09-06 11:31:50','2026-09-06 11:31:50'),(8,'contact_email','info@sekolahku.sch.id','2026-09-06 11:31:50','2026-09-06 11:31:50'),(9,'contact_address','Jl. Pendidikan No. 123, Kelurahan Cerdas, Kecamatan Unggul, Kota Pelajar 12345','2026-09-06 11:31:50','2026-09-06 11:31:50'),(10,'contact_hours','Senin - Jumat: 07.00 - 16.00 WIB','2026-09-06 11:31:50','2026-09-06 11:31:50'),(11,'social_facebook','#','2026-09-06 11:31:50','2026-09-06 11:31:50'),(12,'social_instagram','#','2026-09-06 11:31:50','2026-09-06 11:31:50'),(13,'social_youtube','#','2026-09-06 11:31:50','2026-09-06 11:31:50'),(14,'social_tiktok','#','2026-09-06 11:31:50','2026-09-06 11:31:50'),(15,'meta_author','SekolahKu','2026-09-06 11:31:50','2026-09-06 11:31:50'),(16,'meta_keywords','sekolah, pendidikan, kurikulum merdeka, sekolah unggulan, SPMB','2026-09-06 11:31:50','2026-09-06 11:31:50'),(17,'meta_description','SekolahKu adalah institusi pendidikan unggulan yang berkomitmen mencetak generasi cerdas, berkarakter, dan berprestasi dengan kurikulum Merdeka.','2026-09-06 11:31:50','2026-09-06 11:31:50'),(18,'hero_badge','Terakreditasi A · Kurikulum Merdeka','2026-09-06 11:31:50','2026-09-06 11:31:50'),(19,'hero_title','Selamat Datang di SekolahKu','2026-09-06 11:31:50','2026-09-06 11:31:50'),(20,'hero_subtitle','Membangun Generasi Cerdas, Berkarakter, dan Berprestasi menuju Masa Depan Gemilang','2026-09-06 11:31:50','2026-09-06 11:31:50'),(21,'hero_btn_primary_text','Jelajahi Sekolah','2026-09-06 11:31:50','2026-09-06 11:31:50'),(22,'hero_btn_primary_url','#profile','2026-09-06 11:31:50','2026-09-06 11:31:50'),(23,'hero_btn_secondary_text','Hubungi Kami','2026-09-06 11:31:50','2026-09-06 11:31:50'),(24,'hero_btn_secondary_url','#contact','2026-09-06 11:31:50','2026-09-06 11:31:50'),(25,'spmb_url','#','2026-09-06 11:31:50','2026-09-06 11:31:50'),(26,'hero_stats','[{\"label\":\"Siswa\",\"value\":\"1200+\",\"icon\":\"user-graduate\"},{\"label\":\"Guru\",\"value\":\"85+\",\"icon\":\"chalkboard-teacher\"},{\"label\":\"Prestasi\",\"value\":\"150+\",\"icon\":\"trophy\"}]','2026-09-06 11:31:50','2026-09-06 11:31:50'),(27,'principal','{\"name\":\"Dr. Sari Wijaya, M.Pd.\",\"photo\":\"\\/images\\/default-principal.jpg\",\"role_title\":\"Kepala Sekolah\",\"welcome_message\":\"Selamat datang di SekolahKu. Kami berkomitmen mencetak generasi cerdas, berkarakter, dan berprestasi. Mari bersama-sama membangun masa depan gemilang untuk putra-putri kita.\",\"education\":\"S3 Pendidikan\",\"years_of_service\":\"10 Thn Mengabdi\"}','2026-09-06 11:31:50','2026-09-06 11:31:50'),(28,'about','{\"image\":\"https:\\/\\/placehold.co\\/600x400\\/0c4a6e\\/ffffff?text=SekolahKu\",\"content_title\":\"Mewujudkan Pendidikan Berkualitas untuk Masa Depan\",\"content_1\":\"SekolahKu adalah institusi pendidikan yang berdedikasi memberikan pengalaman belajar terbaik. Dengan kurikulum terkini dan tenaga pengajar profesional, kami siap membentuk karakter dan kompetensi siswa.\",\"content_2\":\"Kami percaya setiap anak memiliki potensi unik. Melalui pendekatan holistik, kami mendorong siswa tumbuh secara akademis, sosial, dan spiritual.\",\"accreditation\":\"A\",\"accreditation_label\":\"Standar Nasional\",\"highlights\":[\"Akreditasi A\",\"Kurikulum Merdeka\",\"Lulusan 100% Terserap\",\"Lab & Perpustakaan Digital\"]}','2026-09-06 11:31:50','2026-09-06 11:31:50'),(29,'section_settings','{\"hero\":{\"title\":\"Selamat Datang di SekolahKu\",\"subtitle\":\"Mewujudkan Generasi Cerdas, Berkarakter, dan Berdaya Saing Global\",\"icon\":\"fa-school\",\"show\":true},\"profile\":{\"title\":\"Profil Sekolah\",\"subtitle\":\"Mengenal lebih dekat visi, misi, dan sejarah SekolahKu\",\"icon\":\"fa-building-columns\",\"show\":true},\"programs\":{\"title\":\"Program Unggulan\",\"subtitle\":\"Program unggulan yang mendukung pengembangan bakat dan prestasi\",\"icon\":\"fa-gem\",\"show\":true},\"extracurriculars\":{\"title\":\"Ekstrakurikuler\",\"subtitle\":\"Wadah pengembangan minat dan bakat di luar kelas\",\"icon\":\"fa-futbol\",\"show\":true},\"teachers\":{\"title\":\"Tenaga Pengajar\",\"subtitle\":\"Guru profesional dan berpengalaman di bidangnya\",\"icon\":\"fa-chalkboard-user\",\"show\":true},\"achievements\":{\"title\":\"Prestasi Siswa\",\"subtitle\":\"Capaian membanggakan yang telah diraih siswa\\/i SekolahKu\",\"icon\":\"fa-trophy\",\"show\":true},\"testimonials\":{\"title\":\"Testimoni\",\"subtitle\":\"Apa kata mereka tentang SekolahKu?\",\"icon\":\"fa-comment\",\"show\":true},\"news\":{\"title\":\"Berita & Artikel\",\"subtitle\":\"Informasi terkini seputar SekolahKu\",\"icon\":\"fa-newspaper\",\"show\":true},\"events\":{\"title\":\"Agenda & Kegiatan\",\"subtitle\":\"Jadwal kegiatan dan acara mendatang\",\"icon\":\"fa-calendar-days\",\"show\":true},\"gallery\":{\"title\":\"Galeri\",\"subtitle\":\"Dokumentasi kegiatan dan momen berharga\",\"icon\":\"fa-images\",\"show\":true},\"faq\":{\"title\":\"FAQ\",\"subtitle\":\"Pertanyaan yang sering diajukan\",\"icon\":\"fa-circle-question\",\"show\":true},\"downloads\":{\"title\":\"Download Center\",\"subtitle\":\"Unduh berkas-berkas penting seputar akademik, kurikulum, dan administrasi sekolah\",\"icon\":\"fa-download\",\"show\":true},\"contact\":{\"title\":\"Kontak Kami\",\"subtitle\":\"Hubungi kami untuk informasi lebih lanjut\",\"icon\":\"fa-address-card\",\"show\":true}}','2026-09-06 11:31:50','2026-09-06 11:31:50'),(30,'page_banners','{\"news\":{\"badge\":\"Berita & Artikel\",\"title\":\"Baca Berita Terbaru\",\"subtitle\":\"Informasi terkini seputar kegiatan dan prestasi di lingkungan SekolahKu\"},\"single_post\":{\"badge\":\"Detail Berita\",\"title\":\"Baca Berita\",\"subtitle\":\"Informasi lengkap seputar kegiatan dan prestasi di lingkungan SekolahKu\"},\"pages\":{\"badge\":\"Halaman\",\"title\":\"Baca Halaman\",\"subtitle\":\"Informasi lengkap seputar halaman ini\"},\"downloads\":{\"badge\":\"Download Center\",\"title\":\"Pusat Unduhan\",\"subtitle\":\"Unduh berkas-berkas penting seputar akademik, kurikulum, dan administrasi sekolah\"}}','2026-09-06 11:31:50','2026-09-06 11:31:50'),(31,'footer_description','SekolahKu berkomitmen mencetak generasi penerus bangsa yang cerdas, berkarakter, dan berprestasi.','2026-09-06 11:31:50','2026-09-06 11:31:50'),(32,'footer_copyright','© 2026 SekolahKu — All rights reserved.','2026-09-06 11:31:50','2026-09-06 11:31:50'),(33,'footer_services','[{\"label\":\"Sains & Teknologi\",\"url\":\"#programs\"},{\"label\":\"Seni & Budaya\",\"url\":\"#programs\"},{\"label\":\"Olahraga\",\"url\":\"#programs\"},{\"label\":\"Bahasa Asing\",\"url\":\"#programs\"},{\"label\":\"Digital Literacy\",\"url\":\"#programs\"}]','2026-09-06 11:31:50','2026-09-06 11:31:50'),(34,'footer_links','[{\"label\":\"SPMB Online\",\"url\":\"#\"},{\"label\":\"E-Learning\",\"url\":\"#\"},{\"label\":\"Perpustakaan\",\"url\":\"#\"},{\"label\":\"FAQ\",\"url\":\"#faq\"},{\"label\":\"Pengaduan\",\"url\":\"#\"}]','2026-09-06 11:31:50','2026-09-06 11:31:50'),(35,'theme_color','default','2026-09-06 11:31:50','2026-09-06 11:31:50'),(36,'counter_stats','[{\"icon\":\"user-graduate\",\"number\":\"1200\",\"suffix\":\"+\",\"label\":\"Siswa\"},{\"icon\":\"chalkboard-teacher\",\"number\":\"85\",\"suffix\":\"+\",\"label\":\"Guru\"},{\"icon\":\"trophy\",\"number\":\"150\",\"suffix\":\"+\",\"label\":\"Prestasi\"},{\"icon\":\"book\",\"number\":\"50\",\"suffix\":\"+\",\"label\":\"Program\"}]','2026-09-06 11:31:50','2026-09-06 11:31:50');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teachers`
--

DROP TABLE IF EXISTS `teachers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `teachers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `photo` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `role` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `experience` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `education` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `social_facebook` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `social_instagram` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `social_linkedin` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sort_order` tinyint unsigned DEFAULT '0',
  `show` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teachers`
--

LOCK TABLES `teachers` WRITE;
/*!40000 ALTER TABLE `teachers` DISABLE KEYS */;
INSERT INTO `teachers` VALUES (1,'Dr. Sari Wijaya, M.Pd.','','Kepala Sekolah','20 Tahun Pengalaman','S3 Pendidikan','','','',1,1,'2026-09-06 04:31:51','2026-09-06 04:31:51'),(2,'Budi Santoso, S.Si., M.T.','','Guru Sains & Teknologi','15 Tahun Pengalaman','S2 Teknik','','','',2,1,'2026-09-06 04:31:51','2026-09-06 04:31:51'),(3,'Ani Rahmawati, S.Pd., M.Hum.','','Guru Bahasa & Sastra','12 Tahun Pengalaman','S2 Linguistik','','','',3,1,'2026-09-06 04:31:51','2026-09-06 04:31:51'),(4,'Hendra Pratama, S.Pd.','','Guru Olahraga','10 Tahun Pengalaman','S1 Pendidikan','','','',4,1,'2026-09-06 04:31:51','2026-09-06 04:31:51'),(5,'Dewi Lestari, S.Pd., M.Pd.','','Guru Matematika','18 Tahun Pengalaman','S2 Pendidikan','','','',5,1,'2026-09-06 04:31:51','2026-09-06 04:31:51'),(6,'Rudi Hermawan, S.Si., M.Kom.','','Guru Informatika','14 Tahun Pengalaman','S2 Komputer','','','',6,1,'2026-09-06 04:31:51','2026-09-06 04:31:51');
/*!40000 ALTER TABLE `teachers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `testimonials`
--

DROP TABLE IF EXISTS `testimonials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `testimonials` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `photo` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `role` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `quote` text COLLATE utf8mb4_general_ci NOT NULL,
  `sort_order` tinyint unsigned DEFAULT '0',
  `show` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `testimonials`
--

LOCK TABLES `testimonials` WRITE;
/*!40000 ALTER TABLE `testimonials` DISABLE KEYS */;
INSERT INTO `testimonials` VALUES (1,'Alya Putri',NULL,'Siswi Kelas XII · Juara Olimpiade Sains','SekolahKu memberikan pengalaman belajar yang luar biasa. Gurunya sangat mendukung dan fasilitasnya lengkap. Saya bangga menjadi bagian dari SekolahKu.',1,1,'2026-09-06 11:31:50','2026-09-06 11:31:50'),(2,'Andi Pratama',NULL,'Orang Tua Siswa','Pendidikan karakter di SekolahKu sangat membekas. Anak saya jadi lebih mandiri, percaya diri, dan memiliki akhlak yang baik. Terima kasih SekolahKu.',2,1,'2026-09-06 11:31:50','2026-09-06 11:31:50'),(3,'Raka Firmansyah',NULL,'Alumni · Universitas Indonesia','Berkat bimbingan guru di SekolahKu, saya berhasil lolos ke PTN favorit melalui jalur SNBP. Program pembinaan olimpiadenya juga sangat membantu pengembangan diri.',3,1,'2026-09-06 11:31:50','2026-09-06 11:31:50');
/*!40000 ALTER TABLE `testimonials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `full_name` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `role` enum('superadmin','admin','editor') COLLATE utf8mb4_general_ci DEFAULT 'editor',
  `is_active` tinyint(1) DEFAULT '1',
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'administrator','admin@sekolahku.sch.id','$2y$12$tTP1Qbc98xDXkwjoHhn8Ku6eFJ/ZZA8tZhhTjsicOXvnB6ck2Uw7K','Administrator',NULL,'superadmin',1,NULL,'2026-09-06 11:31:50','2026-09-06 11:31:50');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'sekolahku_db'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-06 11:31:51
