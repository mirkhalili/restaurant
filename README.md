# Restaurant Automation

سامانه اتوماسیون رستوران بر مبنای مستندات `Docs/` و معماری Modular Monolith.

## نسخه

**QAVNS: `003.3.0.0`**

این نگارش یک تغییر بنیادی زیرساختی است: پروژه برای استقرار مستقیم روی هاست اشتراکی پارس‌پک با **PHP + MariaDB** آماده شده و وابستگی به Docker، Docker Compose و `.env` حذف شده است.

## ساختار استقرار

```text
restaurant/
├── public_html/              # تنها مسیر قابل دسترسی از وب
│   ├── index.php
│   ├── .htaccess
│   └── assets/
├── config/                   # تنظیمات خصوصی
│   ├── config.php
│   └── config.local.php      # روی سرور ایجاد می‌شود؛ در Git قرار نمی‌گیرد
├── src/                      # کد برنامه
├── views/                    # قالب‌ها
├── database/
│   ├── schema.sql
│   └── seed.sql
├── storage/                  # فایل‌های تولیدی و موقت
├── Docs/
├── tests/
└── VERSION
```

## نیازمندی هاست

- PHP 8.1 یا بالاتر
- MariaDB 10.5+ یا نسخه سازگار ارائه‌شده توسط هاست
- PHP extensions: `PDO` و `pdo_mysql`
- Apache با `mod_rewrite` برای routing
- HTTPS فعال
- امکان ساخت Database و Database User از پنل هاست
- امکان Import SQL در phpMyAdmin

## نصب روی پارس‌پک

1. یک Database و User از پنل هاست ایجاد کنید.
2. Database را در phpMyAdmin انتخاب و `database/schema.sql` را Import کنید.
3. سپس `database/seed.sql` را Import کنید.
4. فایل `config/config.local.php.example` را به `config/config.local.php` کپی کنید.
5. مقادیر اتصال MariaDB و دامنه را در `config/config.local.php` وارد کنید.
6. کل پروژه را روی هاست قرار دهید و **Document Root** دامنه را روی `public_html` بگذارید.
7. دسترسی نوشتن را فقط برای `storage/` در صورت نیاز فعال کنید.
8. سایت را با HTTPS باز کنید.

### نکته امنیتی

فایل `config/config.local.php` حاوی رمز دیتابیس است و نباید در Git یا `public_html` قرار گیرد. همچنین رمز حساب مدیر اولیه را بلافاصله بعد از نصب تغییر دهید.

حساب seed اولیه:
`admin@example.com` / `password`

## API سلامت

پس از نصب، مسیر زیر برای بررسی سلامت برنامه در دسترس است:

`/api/v1/health`

## توسعه

قواعد نسخه‌گذاری QAVNS در `Docs/19-versioning.md` و راهنمای استقرار هاست اشتراکی در `Docs/21-shared-host-deployment.md` قرار دارد.
