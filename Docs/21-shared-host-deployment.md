# راهنمای استقرار مستقیم ZIP روی public_html پارس‌پک

## اصل استقرار

**Repository Root = public_html**

هیچ پوشه‌ای به نام `public_html/` داخل Repository وجود ندارد. پس از دریافت ZIP از GitHub، محتویات Repository را مستقیماً داخل `public_html/` استخراج کنید.

## ساختار نهایی

```text
/home/CPANEL_USER/public_html/
├── index.php
├── .htaccess
├── assets/
├── config/
├── src/
├── views/
├── database/
├── storage/
├── Docs/
├── tests/
├── VERSION
└── README.md
```

## نصب

1. Database و User بسازید.
2. `database/schema.sql` و `database/seed.sql` را در phpMyAdmin Import کنید.
3. `config/config.local.php.example` را به `config/config.local.php` تبدیل کنید.
4. اطلاعات واقعی MariaDB را وارد کنید.
5. محتویات ZIP را مستقیماً داخل `public_html` قرار دهید.

## Entry Point

مسیر نهایی: `/home/CPANEL_USER/public_html/index.php`

## امنیت

`.htaccess` در ریشه Repository قرار دارد و دسترسی HTTP به مسیرهای `config`، `database`، `src`، `views`، `storage`، `Docs` و `tests` را مسدود می‌کند. همچنین Directory Listing و فایل‌های حساس مسدود می‌شوند.

## ارتقا

ZIP هر Release را دریافت و محتویات آن را مستقیماً داخل `public_html` قرار دهید. قبل از ارتقا Database Backup تهیه کنید.

## سلامت

`/api/v1/health` باید `status=ok` و نسخه جاری را برگرداند.

## رمز اولیه

`admin@example.com` / `password` — پس از اولین ورود تغییر دهید.