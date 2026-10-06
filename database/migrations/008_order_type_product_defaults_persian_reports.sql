SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;

ALTER TABLE products
  ADD COLUMN allow_dine_in TINYINT(1) NOT NULL DEFAULT 1 AFTER product_type,
  ADD COLUMN allow_takeaway TINYINT(1) NOT NULL DEFAULT 1 AFTER allow_dine_in;

ALTER TABLE orders
  MODIFY COLUMN order_type ENUM('سالن','بیرون‌بر','تلفنی','ارسال','رزرو','حضوری') NOT NULL DEFAULT 'سالن';

UPDATE orders SET order_type='سالن' WHERE order_type='حضوری';

INSERT INTO settings(scope,setting_key,setting_value)
VALUES ('sales','default_order_type','"سالن"')
ON DUPLICATE KEY UPDATE setting_value='"سالن"';

SET FOREIGN_KEY_CHECKS=1;
