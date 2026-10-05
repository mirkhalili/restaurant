# مستند پیاده‌سازی

## نسخه هدف
002.0.0.0 — بازطراحی بنیادی UI/UX و App Shell.

## Stack
PHP 8.3+، MySQL 8، PDO، HTML/CSS/Vanilla JS، Modular Monolith.

## UI Architecture
- Shared App Shell در `views/layout.php`
- Navigation داده‌محور در Sidebar
- Theme با CSS custom properties
- Progressive Enhancement با Vanilla JS
- صفحات فعلی: Dashboard، Orders، Customers، Products
- ساختار UI برای Inventory/Reports/Settings در Navigation آماده است و منطق Backend آن‌ها باید مطابق roadmap توسعه یابد.

## الهام طراحی
الگوهای عمومی Ever Gauzy شامل Sidebar/Header، Layoutهای چندستونه، داشبورد ویجتی، Breadcrumb، Theme و Table-oriented management به‌عنوان مرجع UX مطالعه شده‌اند؛ کد و ظاهر اختصاصی آن پروژه کپی نشده است.

## تست
پس از هر تغییر UI: PHP syntax، smoke test، responsive layout و keyboard navigation بررسی شود.
