-- Restaurant Automation / MariaDB shared-host installation
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;

CREATE TABLE IF NOT EXISTS roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(80) UNIQUE NOT NULL,
    created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_id BIGINT UNSIGNED NOT NULL,
    email VARCHAR(190) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL,
    CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS branches (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS customers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(190) NOT NULL,
    notes TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL,
    INDEX idx_customers_name(name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(190) NOT NULL,
    price DECIMAL(18,2) NOT NULL DEFAULT 0,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL,
    INDEX idx_products_status(status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_no VARCHAR(40) UNIQUE NOT NULL,
    customer_id BIGINT UNSIGNED NULL,
    branch_id BIGINT UNSIGNED NULL,
    order_type ENUM('حضوری','بیرون‌بر','تلفنی','ارسال','رزرو') NOT NULL,
    status ENUM('draft','confirmed','preparing','ready','delivered','picked_up','completed','cancelled','refunded') NOT NULL DEFAULT 'draft',
    total_amount DECIMAL(18,2) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL,
    CONSTRAINT fk_orders_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
    CONSTRAINT fk_orders_branch FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE SET NULL,
    INDEX idx_orders_status_created(status,created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS order_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    quantity DECIMAL(12,3) NOT NULL,
    unit_price DECIMAL(18,2) NOT NULL,
    line_total DECIMAL(18,2) NOT NULL,
    created_at DATETIME NOT NULL,
    CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE RESTRICT,
    CONSTRAINT fk_order_items_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS invoices (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    invoice_no VARCHAR(40) UNIQUE NOT NULL,
    order_id BIGINT UNSIGNED NOT NULL,
    total_amount DECIMAL(18,2) NOT NULL,
    status ENUM('issued','void','refunded') DEFAULT 'issued',
    created_at DATETIME NOT NULL,
    CONSTRAINT fk_invoices_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    payment_no VARCHAR(40) UNIQUE NOT NULL,
    invoice_id BIGINT UNSIGNED NOT NULL,
    method ENUM('cash','card','online','mixed') NOT NULL,
    amount DECIMAL(18,2) NOT NULL,
    status ENUM('paid','refunded','void') DEFAULT 'paid',
    created_at DATETIME NOT NULL,
    CONSTRAINT fk_payments_invoice FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    action VARCHAR(120) NOT NULL,
    entity_type VARCHAR(80) NULL,
    entity_id BIGINT UNSIGNED NULL,
    before_data JSON NULL,
    after_data JSON NULL,
    reason VARCHAR(500) NULL,
    ip_address VARCHAR(45) NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_audit_entity(entity_type,entity_id),
    INDEX idx_audit_created(created_at),
    CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    scope VARCHAR(50) NOT NULL,
    setting_key VARCHAR(120) NOT NULL,
    setting_value JSON NULL,
    UNIQUE KEY uq_setting(scope,setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS=1;
