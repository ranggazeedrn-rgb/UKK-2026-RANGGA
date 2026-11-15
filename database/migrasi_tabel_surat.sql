-- Migrasi: Tambah tabel t_surat untuk fitur surat peringatan / panggilan orang tua
-- Jalankan query ini di phpMyAdmin atau MySQL CLI pada database db_ukk_2026

CREATE TABLE IF NOT EXISTS `t_surat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nomor_surat` varchar(50) NOT NULL,
  `jenis_surat` varchar(50) NOT NULL COMMENT 'Surat Peringatan / Surat Panggilan Orang Tua',
  `siswa_id` int(11) NOT NULL,
  `kelas_id` int(11) DEFAULT NULL,
  `total_poin` int(11) NOT NULL DEFAULT 0,
  `perihal` varchar(255) NOT NULL,
  `isi_surat` text NOT NULL,
  `tanggal_surat` date NOT NULL,
  `dibuat_oleh` int(11) DEFAULT NULL COMMENT 'user_id yang membuat surat',
  `nama_pembuat` varchar(150) NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'Diterbitkan' COMMENT 'Diterbitkan / Dibatalkan',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `siswa_id` (`siswa_id`),
  KEY `kelas_id` (`kelas_id`),
  KEY `dibuat_oleh` (`dibuat_oleh`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
