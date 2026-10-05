USE restaurant;
INSERT INTO roles(name,created_at) VALUES ('مدیر سیستم',NOW()),('مدیر رستوران',NOW()),('صندوقدار',NOW()),('انباردار',NOW()),('آشپز',NOW()),('پذیرش',NOW()),('اپراتور تلفن',NOW()),('حسابدار',NOW()),('گزارش‌گیر',NOW());
INSERT INTO users(role_id,email,password_hash,created_at) SELECT id,'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC1p7X2uWj5kK0k7J9a',NOW() FROM roles WHERE name='مدیر سیستم' LIMIT 1;
INSERT INTO branches(name,created_at) VALUES('شعبه اصلی',NOW());
INSERT INTO products(name,price,created_at) VALUES('چلو جوجه',250000,NOW()),('پیتزا مخصوص',420000,NOW()),('نوشابه',45000,NOW());
