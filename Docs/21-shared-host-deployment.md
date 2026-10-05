# راهنمای استقرار روی هاست اشتراکی پارس‌پک

## 1. معماری استقرار

در نسخه `003.3.0.0` پروژه برای هاست اشتراکی طراحی شده است:

- وب‌روت فقط `public_html/` است.
- کد PHP، تنظیمات، Viewها و فایل SQL خارج از `public_html/` قرار دارند.
- Docker و Docker Compose مورد نیاز نیستند.
- Composer برای اجرای هسته فعلی مورد نیاز نیست.
- اتصال دیتابیس با PDO و درایور `pdo_mysql` انجام می‌شود.
- دیتابیس هدف MariaDB است.

## 2. ساختار روی سرور

نمونه مسیر:

```text
/home/CPANEL_USER/
├── public_html/
│   ├── index.php
│   ├── .htaccess
│   └── assets/
├── config/
│   ├── config.php
│   └── config.local.php
├── src/
├── views/
├── database/
├── storage/
└── VERSION
```

نام دقیق مسیر home به حساب هاست بستگی دارد.

## 3. ساخت دیتابیس

در کنترل‌پنل هاست:

1. یک Database بسازید.
2. یک Database User بسازید.
3. User را با دسترسی کامل به Database متصل کنید.
4. نام واقعی Database و User را یادداشت کنید؛ در بعضی کنترل‌پنل‌ها نام‌ها با پیشوند حساب ساخته می‌شوند.

در phpMyAdmin، Database ایجادشده را انتخاب کنید و ابتدا:

`database/schema.sql`

و سپس:

`database/seed.sql`

را Import کنید.

## 4. تنظیم PHP

نسخه PHP پیشنهادی پروژه 8.1 یا بالاتر است.

Extensionهای مورد نیاز:

- PDO
- pdo_mysql
- session
- mbstring

اگر هاست امکان انتخاب Extension دارد، `pdo_mysql` را فعال کنید.

## 5. تنظیم برنامه

فایل زیر را روی سرور کپی کنید:

`config/config.local.php.example`

و نام آن را به:

`config/config.local.php`

تغییر دهید.

نمونه:

```php
<?php
return [
    'app_env' => 'production',
    'app_url' => 'https://restaurant.example',
    'db' => [
        'host' => 'localhost',
        'port' => 3306,
        'name' => 'CPANEL_DATABASE_NAME',
        'user' => 'CPANEL_DATABASE_USER',
        'pass' => 'DATABASE_PASSWORD',
        'charset' => 'utf8mb4',
    ],
    'session' => [
        'name' => 'restaurant_session',
        'secure' => true,
    ],
];
```

این فایل نباید در Git commit شود.

## 6. Document Root

اگر امکان تنظیم Document Root برای دامنه وجود دارد، آن را روی:

`public_html`

قرار دهید.

اگر دامنه اصلی هاست به صورت پیش‌فرض همین `public_html` را استفاده می‌کند، فقط محتوای این پوشه را به عنوان web root در نظر بگیرید.

**نباید** کل repository به عنوان web root معرفی شود.

## 7. Rewrite

فایل `public_html/.htaccess` درخواست‌های غیر از فایل‌های واقعی را به `index.php` منتقل می‌کند.

اگر Rewrite روی هاست فعال نباشد، مسیرهای داخلی سامانه به درستی کار نخواهند کرد؛ در این حالت باید از پشتیبانی هاست بخواهید `mod_rewrite` فعال باشد.

## 8. دسترسی فایل‌ها

- فایل‌های PHP و تنظیمات: معمولاً 644
- پوشه‌ها: معمولاً 755
- `storage/`: در صورت نیاز به نوشتن توسط PHP، فقط همین مسیر writable باشد.
- به هیچ عنوان برای رفع خطا کل پروژه را 777 نکنید.

## 9. امنیت

- HTTPS را فعال کنید.
- رمز Database را فقط در `config/config.local.php` نگه دارید.
- `config.local.php` را داخل `public_html` قرار ندهید.
- رمز `admin@example.com` را پس از اولین ورود تغییر دهید.
- در محیط عملیاتی نمایش خطاهای PHP به کاربر غیرفعال باشد.
- دسترسی عمومی به فایل‌های SQL، Log و مستندات نباید وجود داشته باشد.
- قبل از هر ارتقا از Database نسخه پشتیبان تهیه کنید.

## 10. بررسی سلامت

پس از نصب، این مسیر را باز کنید:

`/api/v1/health`

باید پاسخ JSON شامل `status=ok` و نسخه فعلی پروژه برگرداند.

## 11. ارتقاهای بعدی

هر نسخه جدید باید طبق QAVNS توسعه یابد. نسخه فعلی:

`003.3.0.0`

نسخه قبلی نباید overwrite شود؛ تغییرات بعدی در branch نسخه جدید انجام شده، بررسی و سپس به `main` merge شوند.
