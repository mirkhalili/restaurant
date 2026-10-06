-- Restaurant Automation v013.7.0.1
-- افزودن اطلاعات پروفایل کاربران بدون شکستن دیتابیس‌های موجود.

SET NAMES utf8mb4;

SET @has_display_name := (
 SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
 WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='users' AND COLUMN_NAME='display_name'
);
SET @sql := IF(@has_display_name=0,
 'ALTER TABLE users ADD COLUMN display_name VARCHAR(150) NULL AFTER email',
 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_mobile := (
 SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
 WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='users' AND COLUMN_NAME='mobile'
);
SET @sql := IF(@has_mobile=0,
 'ALTER TABLE users ADD COLUMN mobile VARCHAR(30) NULL AFTER display_name',
 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

UPDATE users
SET display_name=COALESCE(NULLIF(display_name,''),SUBSTRING_INDEX(email,'@',1))
WHERE display_name IS NULL OR display_name='';

SELECT COUNT(*) AS users_with_profile FROM users WHERE display_name IS NOT NULL;
