-- 021.10.0.0 / persistent drafts, manual discounts and daily ticket numbering
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;

ALTER TABLE orders
  ADD COLUMN created_by BIGINT UNSIGNED NULL AFTER branch_id,
  ADD COLUMN subtotal_amount DECIMAL(18,2) NOT NULL DEFAULT 0 AFTER status,
  ADD COLUMN discount_percent DECIMAL(5,2) NOT NULL DEFAULT 0 AFTER subtotal_amount,
  ADD COLUMN discount_amount DECIMAL(18,2) NOT NULL DEFAULT 0 AFTER discount_percent,
  ADD KEY idx_orders_created_by_status(created_by,status),
  ADD CONSTRAINT fk_orders_created_by FOREIGN KEY(created_by) REFERENCES users(id) ON DELETE SET NULL;

ALTER TABLE invoices
  ADD COLUMN discount_percent DECIMAL(5,2) NOT NULL DEFAULT 0 AFTER total_amount,
  ADD COLUMN discount_amount DECIMAL(18,2) NOT NULL DEFAULT 0 AFTER discount_percent,
  ADD COLUMN ticket_date DATE NULL AFTER discount_amount,
  ADD COLUMN ticket_no INT UNSIGNED NULL AFTER ticket_date,
  ADD UNIQUE KEY uq_invoices_ticket(ticket_date,ticket_no);

CREATE TABLE daily_ticket_sequences (
  ticket_date DATE NOT NULL PRIMARY KEY,
  last_no INT UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS=1;
