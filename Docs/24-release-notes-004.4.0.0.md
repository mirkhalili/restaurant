# Release Notes — 004.4.0.0

## خلاصه

ساختار Repository با روش استقرار مستقیم روی `public_html` هماهنگ شد.

## تغییرات

- حذف پوشه `public_html/` از Repository.
- انتقال Entry Point به `index.php` در ریشه Repository.
- انتقال assets به `assets/` در ریشه.
- اصلاح مسیرهای include در Entry Point.
- قرارگیری `.htaccess` در ریشه Repository.
- حفاظت HTTP از مسیرهای داخلی.

## استقرار

محتویات ZIP مستقیماً داخل `public_html` قرار می‌گیرد و نتیجه باید `public_html/index.php` باشد.

## QAVNS

`004.4.0.0`