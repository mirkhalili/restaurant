-- Migration 002: customer, product and order enhancements
ALTER TABLE customers ADD COLUMN subscription_code VARCHAR(60) NULL;
ALTER TABLE customers ADD COLUMN first_name VARCHAR(100) NULL;
ALTER TABLE customers ADD COLUMN last_name VARCHAR(120) NULL;
ALTER TABLE customers ADD COLUMN phone VARCHAR(30) NULL;
ALTER TABLE customers ADD COLUMN mobile VARCHAR(30) NULL;
ALTER TABLE customers ADD COLUMN membership_date DATE NULL;
ALTER TABLE customers ADD COLUMN address VARCHAR(500) NULL;
ALTER TABLE customers ADD COLUMN birth_date DATE NULL;
ALTER TABLE customers ADD UNIQUE KEY uq_customers_subscription_code (subscription_code);
ALTER TABLE customers ADD UNIQUE KEY uq_customers_phone (phone);
ALTER TABLE customers ADD UNIQUE KEY uq_customers_mobile (mobile);

ALTER TABLE products ADD COLUMN product_code VARCHAR(60) NULL;
ALTER TABLE products ADD COLUMN unit VARCHAR(50) NULL;
ALTER TABLE products ADD COLUMN product_type VARCHAR(100) NULL;
ALTER TABLE products ADD UNIQUE KEY uq_products_product_code (product_code);

ALTER TABLE orders ADD COLUMN customer_phone VARCHAR(30) NULL;
ALTER TABLE orders ADD COLUMN invoice_no VARCHAR(40) NULL;
ALTER TABLE orders ADD COLUMN payment_method ENUM('cash','card','online','mixed') NULL;
ALTER TABLE orders ADD COLUMN paid_amount DECIMAL(18,2) NOT NULL DEFAULT 0;
ALTER TABLE orders ADD COLUMN finalized_at DATETIME NULL;
ALTER TABLE orders ADD UNIQUE KEY uq_orders_invoice_no (invoice_no);
