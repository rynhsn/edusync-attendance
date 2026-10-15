USE `edusync`;

CREATE TABLE IF NOT EXISTS `role_action_settings` (
    `role` VARCHAR(32) NOT NULL,
    `action_key` VARCHAR(80) NOT NULL,
    `is_enabled` TINYINT(1) NOT NULL DEFAULT 0,
    `updated_at` DATETIME NULL,
    PRIMARY KEY (`role`, `action_key`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `user_action_settings` (
    `user_id` INT(11) UNSIGNED NOT NULL,
    `action_key` VARCHAR(80) NOT NULL,
    `is_enabled` TINYINT(1) NOT NULL DEFAULT 0,
    `updated_at` DATETIME NULL,
    PRIMARY KEY (`user_id`, `action_key`),
    CONSTRAINT `user_action_settings_user_fk`
        FOREIGN KEY (`user_id`) REFERENCES `auth_users` (`user_id`)
        ON DELETE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_general_ci;

GRANT SELECT
ON `edusync`.*
TO 'edusync_ro_f7c170'@'localhost';

GRANT INSERT, UPDATE, DELETE
ON `edusync`.`role_action_settings`
TO 'edusync_ro_f7c170'@'localhost';

GRANT INSERT, UPDATE, DELETE
ON `edusync`.`user_action_settings`
TO 'edusync_ro_f7c170'@'localhost';

FLUSH PRIVILEGES;

SHOW GRANTS FOR 'edusync_ro_f7c170'@'localhost';
