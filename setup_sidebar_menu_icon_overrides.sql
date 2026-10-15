CREATE TABLE IF NOT EXISTS `sidebar_menu_icon_overrides` (
    `menu_key` VARCHAR(80) NOT NULL,
    `icon` VARCHAR(64) NOT NULL,
    `updated_at` DATETIME NULL,
    PRIMARY KEY (`menu_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

GRANT SELECT, INSERT, UPDATE
ON `edusync`.`sidebar_menu_icon_overrides`
TO 'edusync_ro_f7c170'@'localhost';
