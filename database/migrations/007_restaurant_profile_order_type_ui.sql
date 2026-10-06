SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;
ALTER TABLE product_categories ADD COLUMN icon VARCHAR(20) NULL AFTER color;
CREATE TABLE IF NOT EXISTS restaurant_profile (
 id TINYINT UNSIGNED NOT NULL PRIMARY KEY,
 name VARCHAR(190) NOT NULL DEFAULT 'رستوران',
 address VARCHAR(500) NULL,
 phone VARCHAR(50) NULL,
 mobile VARCHAR(50) NULL,
 email VARCHAR(190) NULL,
 website VARCHAR(190) NULL,
 logo_path VARCHAR(255) NULL,
 description VARCHAR(500) NULL,
 updated_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO restaurant_profile(id,name) VALUES(1,'رستوران') ON DUPLICATE KEY UPDATE id=id;
UPDATE product_categories SET icon=CASE name WHEN 'پیتزا' THEN '🍕' WHEN 'برگر' THEN '🍔' WHEN 'نوشیدنی' THEN '🥤' WHEN 'پیش‌غذا' THEN '🥗' WHEN 'غذا' THEN '🍛' WHEN 'دسر' THEN '🍰' ELSE COALESCE(icon,'🍽️') END WHERE icon IS NULL OR TRIM(icon)='';
INSERT INTO settings(scope,setting_key,setting_value) VALUES ('sales','default_order_type','"حضوری"') ON DUPLICATE KEY UPDATE setting_key=VALUES(setting_key);
SET FOREIGN_KEY_CHECKS=1;