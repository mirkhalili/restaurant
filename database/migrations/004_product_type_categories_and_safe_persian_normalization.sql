-- Restaurant Automation v013.7.0.1
-- هدف: استفاده از product_type به‌عنوان «نوع/دسته‌بندی» بدون category_id
-- و اصلاح امن حروف عربی ي/ى/ك در اطلاعات موجود مشتریان و محصولات.
-- این اسکریپت قابل اجرای مجدد است.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;

CREATE TABLE IF NOT EXISTS product_categories (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(120) NOT NULL,
 color VARCHAR(30) NOT NULL DEFAULT '#2563eb',
 sort_order INT NOT NULL DEFAULT 0,
 status ENUM('active','inactive') NOT NULL DEFAULT 'active',
 created_at DATETIME NOT NULL,
 updated_at DATETIME NULL,
 UNIQUE KEY uq_product_categories_name(name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO product_categories(name,color,sort_order,status,created_at)
VALUES ('سایر','#2563eb',999,'active',NOW())
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- اگر category_id از نسخه قبلی وجود داشته باشد، ابتدا نوع محصول را از آن بازیابی می‌کنیم.
SET @has_category_id := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='products' AND COLUMN_NAME='category_id'
);

SET @sql := IF(@has_category_id > 0,
  'UPDATE products p LEFT JOIN product_categories c ON c.id=p.category_id
   SET p.product_type=COALESCE(NULLIF(TRIM(p.product_type),''''),c.name)
   WHERE p.category_id IS NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- هر نوع/دسته موجود در products را در جدول تعریف انواع ثبت می‌کنیم.
INSERT INTO product_categories(name,color,sort_order,status,created_at)
SELECT DISTINCT TRIM(product_type),'#16a34a',100,'active',NOW()
FROM products
WHERE product_type IS NOT NULL AND TRIM(product_type) <> ''
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- اصلاح داده‌های موجود: فقط حروف عربی معادل مستقیم اصلاح می‌شوند.
-- «ئ» عمداً تغییر نمی‌کند چون معادل «ی» نیست.
UPDATE customers
SET first_name=REPLACE(REPLACE(first_name,'ي','ی'),'ى','ی'),
    last_name=REPLACE(REPLACE(last_name,'ي','ی'),'ى','ی'),
    name=REPLACE(REPLACE(name,'ي','ی'),'ى','ی'),
    address=REPLACE(REPLACE(address,'ك','ک'),'ي','ی'),
    notes=REPLACE(REPLACE(notes,'ك','ک'),'ي','ی')
WHERE first_name LIKE '%ي%' OR first_name LIKE '%ى%' OR first_name LIKE '%ك%'
   OR last_name LIKE '%ي%' OR last_name LIKE '%ى%' OR last_name LIKE '%ك%'
   OR name LIKE '%ي%' OR name LIKE '%ى%' OR name LIKE '%ك%'
   OR address LIKE '%ي%' OR address LIKE '%ى%' OR address LIKE '%ك%'
   OR notes LIKE '%ي%' OR notes LIKE '%ى%' OR notes LIKE '%ك%';

UPDATE products
SET name=REPLACE(REPLACE(name,'ي','ی'),'ى','ی'),
    unit=REPLACE(REPLACE(unit,'ي','ی'),'ى','ی'),
    product_type=REPLACE(REPLACE(product_type,'ي','ی'),'ى','ی')
WHERE name LIKE '%ي%' OR name LIKE '%ى%' OR name LIKE '%ك%'
   OR unit LIKE '%ي%' OR unit LIKE '%ى%' OR unit LIKE '%ك%'
   OR product_type LIKE '%ي%' OR product_type LIKE '%ى%' OR product_type LIKE '%ك%';

-- پس از اصلاح product_type، انواع اصلاح‌شده نیز در فهرست دسته‌بندی‌ها ثبت می‌شوند.
INSERT INTO product_categories(name,color,sort_order,status,created_at)
SELECT DISTINCT TRIM(product_type),'#16a34a',100,'active',NOW()
FROM products
WHERE product_type IS NOT NULL AND TRIM(product_type) <> ''
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- حذف وابستگی قدیمی products.category_id، فقط اگر واقعاً وجود داشته باشد.
SET @fk_name := (
  SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
  WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='products'
    AND COLUMN_NAME='category_id' AND REFERENCED_TABLE_NAME='product_categories'
  LIMIT 1
);
SET @sql := IF(@fk_name IS NOT NULL,
  CONCAT('ALTER TABLE products DROP FOREIGN KEY ', @fk_name),
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_category_index := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
  WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='products' AND INDEX_NAME='idx_products_category'
);
SET @sql := IF(@has_category_index > 0,
  'ALTER TABLE products DROP INDEX idx_products_category',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(@has_category_id > 0,
  'ALTER TABLE products DROP COLUMN category_id',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET FOREIGN_KEY_CHECKS=1;

-- گزارش کنترل پس از اجرا:
SELECT
  (SELECT COUNT(*) FROM customers WHERE first_name LIKE '%ي%' OR last_name LIKE '%ي%' OR name LIKE '%ي%' OR address LIKE '%ي%' OR notes LIKE '%ي%') AS customers_with_arabic_ye,
  (SELECT COUNT(*) FROM customers WHERE first_name LIKE '%ك%' OR last_name LIKE '%ك%' OR name LIKE '%ك%' OR address LIKE '%ك%' OR notes LIKE '%ك%') AS customers_with_arabic_kaf,
  (SELECT COUNT(*) FROM products WHERE name LIKE '%ي%' OR unit LIKE '%ي%' OR product_type LIKE '%ي%') AS products_with_arabic_ye,
  (SELECT COUNT(*) FROM products WHERE name LIKE '%ك%' OR unit LIKE '%ك%' OR product_type LIKE '%ك%') AS products_with_arabic_kaf;
