USE `edusync`;

CREATE TABLE IF NOT EXISTS `master_kokurikuler` (
    `kokurikuler_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `nama_kegiatan` VARCHAR(100) NOT NULL,
    `deskripsi` TEXT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    PRIMARY KEY (`kokurikuler_id`),
    UNIQUE KEY `uq_master_kokurikuler_name` (`nama_kegiatan`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_general_ci;

GRANT SELECT, INSERT, UPDATE, DELETE
ON `edusync`.`master_kokurikuler`
TO 'edusync_ro_f7c170'@'localhost';

FLUSH PRIVILEGES;
