-- Restaurant Automation v012.6.0.0
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

ALTER TABLE products ADD COLUMN category_id BIGINT UNSIGNED NULL AFTER product_type;
ALTER TABLE products ADD INDEX idx_products_category(category_id);
ALTER TABLE products ADD CONSTRAINT fk_products_category FOREIGN KEY(category_id) REFERENCES product_categories(id) ON DELETE SET NULL;

INSERT INTO product_categories(name,color,sort_order,status,created_at)
SELECT DISTINCT TRIM(product_type),'#16a34a',100,'active',NOW()
FROM products
WHERE product_type IS NOT NULL AND TRIM(product_type) <> ''
ON DUPLICATE KEY UPDATE name=VALUES(name);

UPDATE products p
LEFT JOIN product_categories c ON c.name=TRIM(p.product_type)
SET p.category_id=COALESCE(c.id,(SELECT id FROM product_categories WHERE name='سایر' LIMIT 1));

UPDATE customers SET
 first_name=REPLACE(REPLACE(REPLACE(first_name,'ي','ی'),'ى','ی'),'ئ','ی'),
 last_name=REPLACE(REPLACE(REPLACE(last_name,'ي','ی'),'ى','ی'),'ئ','ی'),
 name=REPLACE(REPLACE(REPLACE(name,'ي','ی'),'ى','ی'),'ئ','ی'),
 address=REPLACE(REPLACE(address,'ك','ک'),'ي','ی'),
 notes=REPLACE(REPLACE(notes,'ك','ک'),'ي','ی');

UPDATE customers SET first_name=REPLACE(first_name,'ك','ک'),last_name=REPLACE(last_name,'ك','ک'),name=REPLACE(name,'ك','ک');
UPDATE products SET name=REPLACE(REPLACE(name,'ي','ی'),'ك','ک'),unit=REPLACE(REPLACE(unit,'ي','ی'),'ك','ک'),product_type=REPLACE(REPLACE(product_type,'ي','ی'),'ك','ک');
UPDATE product_categories SET name=REPLACE(REPLACE(name,'ي','ی'),'ك','ک');
UPDATE roles SET name=REPLACE(REPLACE(name,'ي','ی'),'ك','ک');
UPDATE branches SET name=REPLACE(REPLACE(name,'ي','ی'),'ك','ک');

SET FOREIGN_KEY_CHECKS=1;
