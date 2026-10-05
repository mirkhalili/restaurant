# راهنمای UI/UX سامانه — Gauzy-inspired

این سند مرجع اجرایی رابط کاربری سامانه اتوماسیون رستوران است. طراحی از الگوهای عمومی و حرفه‌ای Ever Gauzy الهام می‌گیرد؛ شامل Shell مدیریتی، Sidebar، Header، Breadcrumb، داشبورد ویجتی، کارت‌های آماری، جدول‌های مدیریتی، Loading/Empty/Error و Theme قابل تغییر. هیچ کد یا هویت بصری Gauzy کپی نمی‌شود.

## اصول
- RTL واقعی و فارسی‌محور
- Desktop-first برای پنل مدیریتی و Responsive برای تبلت/موبایل
- کاهش کلیک در POS
- سلسله‌مراتب بصری روشن و تراکم کنترل‌شده
- Card/Badge/Table/Drawer/Modal/Toast با رفتار استاندارد
- عدم اتکا فقط به رنگ
- Keyboard navigation و Focus واضح
- Light/Dark theme
- Permission-aware UI

## App Shell
Sidebar ثابت/جمع‌شونده + Header + Breadcrumb + محتوای اصلی با یک Scroll surface. در موبایل Sidebar به Drawer تبدیل می‌شود.

## Sidebar
داشبورد، فروش، مشتریان، منو و محصولات، سالن و میزها، آشپزخانه، انبار و خرید، مالی، گزارش‌ها، دستگاه‌ها و چاپ، کاربران و دسترسی‌ها، تنظیمات و Audit Log. گروه‌های تو‌در‌تو Accordion هستند و آیتم‌های بدون Permission نمایش داده نمی‌شوند.

## Dashboard
مدیر: فروش/مالی/عملیات؛ صندوقدار: صندوق/سفارش؛ انباردار: موجودی/خرید؛ آشپز: صف آماده‌سازی. ویجت‌های پایه: فروش امروز، سفارش، میانگین سفارش، مشتریان، سفارش‌های در انتظار، آشپزخانه، موجودی کم، آخرین سفارش‌ها و میانبرها.

## Table
Toolbar با Search/Filter/Sort/Refresh/Export بر اساس Permission، Pagination، Actions، Badge، Loading/Empty و Row selection در صورت نیاز. در موبایل Card/List یا اسکرول کنترل‌شده.

## POS
سه ناحیه: دسته و جستجو، اقلام سفارش، خلاصه و پرداخت. در صفحه کوچک خلاصه به Drawer/Bottom Sheet تبدیل شود. دکمه‌های لمسی حداقل 44px.

## Form
Label واضح، Validation لحظه‌ای، خطای فارسی، حفظ داده در خطا، Submit با حالت Loading و Confirm برای عملیات مخرب.

## Accessibility
کنتراست مناسب، Focus ring، aria-label، Keyboard navigation و عدم انتقال مفهوم فقط با رنگ.
