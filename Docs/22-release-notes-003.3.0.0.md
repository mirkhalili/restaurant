# Release Notes — 003.3.0.0

## نوع نگارش

نسل جدید طبق QAVNS به دلیل تغییر بنیادی زیرساخت استقرار.

## هدف

آماده‌سازی سامانه برای استقرار مستقیم روی هاست اشتراکی پارس‌پک با PHP و MariaDB، بدون Docker و بدون وابستگی به Composer برای هسته فعلی.

## تغییرات

- ایجاد web root مستقل با نام `public_html/`
- انتقال assetهای وب به `public_html/assets/`
- انتقال ورودی اصلی برنامه به `public_html/index.php`
- اضافه شدن `public_html/.htaccess` برای routing
- انتقال تنظیمات واقعی دیتابیس به `config/config.local.php`
- ایجاد نمونه تنظیمات در `config/config.local.php.example`
- ایجاد ساختار `storage/` خارج از web root
- تبدیل SQL اصلی به ساختار مناسب MariaDB و phpMyAdmin
- حذف Dockerfile
- حذف docker-compose
- حذف `.env.example`
- حذف Composer از deployment هسته فعلی
- به‌روزرسانی CI برای ساختار جدید
- به‌روزرسانی smoke test
- امن‌سازی session با HttpOnly، SameSite و Secure
- اصلاح hash حساب مدیر اولیه
- مستندسازی کامل نصب روی هاست اشتراکی

## حساب اولیه

- Email: `admin@example.com`
- Password: `password`

این رمز فقط برای نصب اولیه است و باید بلافاصله تغییر کند.

## نکته

این نگارش ساختار استقرار را نهایی می‌کند، اما به معنی تکمیل همه ماژول‌های functional سامانه رستوران نیست. ماژول‌هایی که در roadmap قبلی هنوز placeholder هستند باید در نگارش‌های بعدی طبق QAVNS پیاده‌سازی شوند.
