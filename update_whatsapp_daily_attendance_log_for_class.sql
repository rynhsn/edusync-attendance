ALTER TABLE `whatsapp_daily_attendance_log`
  DROP INDEX `whatsapp_daily_attendance_date_parent_unique`,
  ADD COLUMN `absensi_kelas_id` int(11) UNSIGNED DEFAULT NULL AFTER `orang_tua_id`,
  ADD UNIQUE KEY `whatsapp_daily_attendance_date_parent_session_unique`
    (`tanggal`, `orang_tua_id`, `absensi_kelas_id`);
