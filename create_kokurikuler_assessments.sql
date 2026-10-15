USE `edusync`;

CREATE TABLE IF NOT EXISTS `academic_student_kokurikuler` (
    `kokurikuler_nilai_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `siswa_id` INT(11) UNSIGNED NOT NULL,
    `kelas_id` INT(11) UNSIGNED NOT NULL,
    `tahun_ajaran_id` INT(11) UNSIGNED NOT NULL,
    `nama_kegiatan` VARCHAR(100) NOT NULL,
    `nilai` VARCHAR(5) NULL,
    `dicatat_oleh` INT(11) UNSIGNED NOT NULL,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    PRIMARY KEY (`kokurikuler_nilai_id`),
    UNIQUE KEY `uq_student_kokurikuler_assessment` (`siswa_id`, `kelas_id`, `tahun_ajaran_id`, `nama_kegiatan`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_general_ci;

GRANT SELECT, INSERT, UPDATE, DELETE
ON `edusync`.`academic_student_kokurikuler`
TO 'edusync_ro_f7c170'@'localhost';

FLUSH PRIVILEGES;
