SET @add_status_column = IF(
    EXISTS (
        SELECT 1
        FROM information_schema.columns
        WHERE table_schema = DATABASE()
          AND table_name = 'master_siswa'
          AND column_name = 'status'
    ),
    'SELECT 1',
    'ALTER TABLE `master_siswa` ADD COLUMN `status` VARCHAR(20) NOT NULL DEFAULT ''aktif'' AFTER `foto`'
);

PREPARE add_status_column_stmt FROM @add_status_column;
EXECUTE add_status_column_stmt;
DEALLOCATE PREPARE add_status_column_stmt;

UPDATE `master_siswa`
SET `status` = 'tidak_aktif'
WHERE `deleted_at` IS NOT NULL;
