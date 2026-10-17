CREATE TABLE IF NOT EXISTS `whatsapp_attendance_schedule` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `send_time` time NOT NULL DEFAULT '17:00:00',
  `is_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `whatsapp_attendance_schedule` (`id`, `send_time`, `is_enabled`, `updated_at`)
VALUES (1, '17:00:00', 1, CURRENT_TIMESTAMP)
ON DUPLICATE KEY UPDATE `id` = `id`;
