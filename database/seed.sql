SET NAMES utf8mb4;

INSERT INTO roles (name, created_at)
SELECT 'مدیر سیستم', NOW()
WHERE NOT EXISTS (SELECT 1 FROM roles WHERE name='مدیر سیستم');
INSERT INTO roles (name, created_at)
SELECT 'مدیر رستوران', NOW()
WHERE NOT EXISTS (SELECT 1 FROM roles WHERE name='مدیر رستوران');
INSERT INTO roles (name, created_at)
SELECT 'صندوقدار', NOW()
WHERE NOT EXISTS (SELECT 1 FROM roles WHERE name='صندوقدار');
INSERT INTO roles (name, created_at)
SELECT 'انباردار', NOW()
WHERE NOT EXISTS (SELECT 1 FROM roles WHERE name='انباردار');
INSERT INTO roles (name, created_at)
SELECT 'آشپز', NOW()
WHERE NOT EXISTS (SELECT 1 FROM roles WHERE name='آشپز');
INSERT INTO roles (name, created_at)
SELECT 'پذیرش', NOW()
WHERE NOT EXISTS (SELECT 1 FROM roles WHERE name='پذیرش');
INSERT INTO roles (name, created_at)
SELECT 'اپراتور تلفن', NOW()
WHERE NOT EXISTS (SELECT 1 FROM roles WHERE name='اپراتور تلفن');
INSERT INTO roles (name, created_at)
SELECT 'حسابدار', NOW()
WHERE NOT EXISTS (SELECT 1 FROM roles WHERE name='حسابدار');
INSERT INTO roles (name, created_at)
SELECT 'گزارش‌گیر', NOW()
WHERE NOT EXISTS (SELECT 1 FROM roles WHERE name='گزارش‌گیر');

INSERT INTO branches (name, status, created_at)
SELECT 'شعبه اصلی','active',NOW()
WHERE NOT EXISTS (SELECT 1 FROM branches WHERE name='شعبه اصلی');

INSERT INTO products (name, price, status, created_at)
SELECT 'چلو جوجه',180000,'active',NOW()
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='چلو جوجه');
INSERT INTO products (name, price, status, created_at)
SELECT 'پیتزا مخصوص',250000,'active',NOW()
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='پیتزا مخصوص');
INSERT INTO products (name, price, status, created_at)
SELECT 'نوشابه',30000,'active',NOW()
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='نوشابه');

INSERT INTO users (role_id,username,email,display_name,password_hash,status,created_at)
SELECT id,'admin','admin@example.com','مدیر سیستم',
'$2y$12$McvX49hKrZ3OpYWMj/t53.q0d3o8JJZzYC55CI4ztuoaiVCv9QN2q',
'active',NOW()
FROM roles
WHERE name='مدیر سیستم'
AND NOT EXISTS (SELECT 1 FROM users WHERE email='admin@example.com');
