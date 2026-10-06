SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;

ALTER TABLE users ADD COLUMN username VARCHAR(80) NULL;
UPDATE users SET username=LOWER(SUBSTRING_INDEX(email,'@',1)) WHERE username IS NULL OR TRIM(username)='';
UPDATE users SET username=CONCAT(username,'_',id) WHERE id IN (SELECT id FROM (SELECT u1.id FROM users u1 JOIN users u2 ON u1.username=u2.username AND u1.id>u2.id) x);
ALTER TABLE users ADD UNIQUE KEY uq_users_username(username);

CREATE TABLE IF NOT EXISTS password_resets (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id BIGINT UNSIGNED NOT NULL,
 token_hash CHAR(64) NOT NULL,
 expires_at DATETIME NOT NULL,
 created_at DATETIME NOT NULL,
 UNIQUE KEY uq_password_reset_token(token_hash),
 INDEX idx_password_reset_user(user_id),
 CONSTRAINT fk_password_reset_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS printers (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(120) NOT NULL,
 location VARCHAR(120) NULL,
 printer_type ENUM('receipt','kitchen','bar','packing','other') NOT NULL DEFAULT 'receipt',
 connection_type ENUM('browser','network','usb','bluetooth') NOT NULL DEFAULT 'browser',
 address VARCHAR(255) NULL,
 paper_width ENUM('58mm','80mm','A4') NOT NULL DEFAULT '80mm',
 is_default TINYINT(1) NOT NULL DEFAULT 0,
 status ENUM('active','inactive') NOT NULL DEFAULT 'active',
 created_at DATETIME NOT NULL,
 updated_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS print_jobs (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 invoice_id BIGINT UNSIGNED NOT NULL,
 printer_id BIGINT UNSIGNED NULL,
 status ENUM('queued','printed','failed') NOT NULL DEFAULT 'queued',
 attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
 created_at DATETIME NOT NULL,
 printed_at DATETIME NULL,
 error_message VARCHAR(500) NULL,
 INDEX idx_print_jobs_invoice(invoice_id),
 CONSTRAINT fk_print_jobs_invoice FOREIGN KEY(invoice_id) REFERENCES invoices(id) ON DELETE RESTRICT,
 CONSTRAINT fk_print_jobs_printer FOREIGN KEY(printer_id) REFERENCES printers(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO settings(scope,setting_key,setting_value) VALUES
('print','paper_width','"80mm"'),
('print','show_logo','"1"'),('print','show_address','"1"'),('print','show_phone','"1"'),
('print','show_customer','"1"'),('print','show_invoice_no','"1"'),('print','show_date','"1"'),
('print','show_payment','"1"'),('print','show_footer','"1"'),('print','footer_text','"از خرید شما سپاسگزاریم"'),
('print','feed','"3"'),('print','cut','"1"')
ON DUPLICATE KEY UPDATE setting_key=VALUES(setting_key);

INSERT INTO printers(name,location,printer_type,connection_type,paper_width,is_default,status,created_at)
SELECT 'چاپگر صندوق','صندوق','receipt','browser','80mm',1,'active',NOW()
WHERE NOT EXISTS (SELECT 1 FROM printers);

SET FOREIGN_KEY_CHECKS=1;
