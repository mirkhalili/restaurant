SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;

CREATE TABLE IF NOT EXISTS inventory_items (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 item_code VARCHAR(60) NULL,
 name VARCHAR(190) NOT NULL,
 unit VARCHAR(50) NOT NULL DEFAULT 'عدد',
 current_quantity DECIMAL(18,3) NOT NULL DEFAULT 0,
 reorder_level DECIMAL(18,3) NOT NULL DEFAULT 0,
 track_stock TINYINT(1) NOT NULL DEFAULT 1,
 status ENUM('active','inactive') NOT NULL DEFAULT 'active',
 created_at DATETIME NOT NULL,
 updated_at DATETIME NULL,
 UNIQUE KEY uq_inventory_item_code(item_code),
 INDEX idx_inventory_item_status(status),
 INDEX idx_inventory_item_stock(track_stock,current_quantity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS inventory_product_rules (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 product_id BIGINT UNSIGNED NOT NULL,
 inventory_item_id BIGINT UNSIGNED NOT NULL,
 quantity_per_order DECIMAL(18,3) NOT NULL,
 created_at DATETIME NOT NULL,
 updated_at DATETIME NULL,
 UNIQUE KEY uq_inventory_product_rule(product_id,inventory_item_id),
 CONSTRAINT fk_inventory_rule_product FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE CASCADE,
 CONSTRAINT fk_inventory_rule_item FOREIGN KEY(inventory_item_id) REFERENCES inventory_items(id) ON DELETE CASCADE,
 INDEX idx_inventory_rule_product(product_id),
 INDEX idx_inventory_rule_item(inventory_item_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS inventory_movements (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 inventory_item_id BIGINT UNSIGNED NOT NULL,
 product_id BIGINT UNSIGNED NULL,
 order_id BIGINT UNSIGNED NULL,
 quantity_change DECIMAL(18,3) NOT NULL,
 movement_type ENUM('purchase','order','adjustment','reversal') NOT NULL,
 note VARCHAR(500) NULL,
 created_by BIGINT UNSIGNED NULL,
 created_at DATETIME NOT NULL,
 CONSTRAINT fk_inventory_movement_item FOREIGN KEY(inventory_item_id) REFERENCES inventory_items(id) ON DELETE RESTRICT,
 CONSTRAINT fk_inventory_movement_product FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE SET NULL,
 CONSTRAINT fk_inventory_movement_order FOREIGN KEY(order_id) REFERENCES orders(id) ON DELETE SET NULL,
 CONSTRAINT fk_inventory_movement_user FOREIGN KEY(created_by) REFERENCES users(id) ON DELETE SET NULL,
 INDEX idx_inventory_movement_item_created(inventory_item_id,created_at),
 INDEX idx_inventory_movement_order(order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS=1;
