# Restaurant Automation

سامانه اتوماسیون رستوران بر مبنای مستندات `Docs/` و معماری Modular Monolith.

## نسخه

**QAVNS: `005.4.1.0`**

از این نسخه **Repository Root = public_html** است. رابط کاربری نیز در این نسخه بازطراحی و بهینه شده است. یعنی ZIP ریپو مستقیماً محتوای مورد نیاز `public_html` را در اختیار شما قرار می‌دهد و `index.php` باید مستقیماً در ریشه `public_html` قرار گیرد.

## ساختار Repository

```text
restaurant/
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
├── .github/
├── .gitignore
├── VERSION
└── README.md
```

در ZIP خروجی GitHub دیگر پوشه‌ای به نام `public_html/` داخل Repository وجود ندارد. محتویات Repository را مستقیماً داخل `public_html` هاست قرار دهید.

## نصب روی پارس‌پک

1. Database و Database User بسازید.
2. `database/schema.sql` و سپس `database/seed.sql` را در phpMyAdmin Import کنید.
3. `config/config.local.php.example` را به `config/config.local.php` تبدیل و اطلاعات MariaDB را وارد کنید.
4. کل محتویات ZIP Repository را مستقیماً داخل `public_html` قرار دهید.
5. اطمینان حاصل کنید مسیر نهایی `public_html/index.php` است.
6. HTTPS را فعال کنید.

`config/config.local.php` در Git قرار نمی‌گیرد و با `.htaccess` از دسترسی HTTP محافظت می‌شود.

## نیازمندی

- PHP 8.1+
- MariaDB 10.5+
- PDO و pdo_mysql
- Apache + mod_rewrite
- HTTPS

## API سلامت

`/api/v1/health` باید JSON شامل `status=ok` و نسخه جاری پروژه برگرداند.