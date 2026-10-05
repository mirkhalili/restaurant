# معماری پیشنهادی

## رویکرد
Modular Monolith با مرزبندی دامنه‌ای. در فاز اول Microservice توصیه نمی‌شود.

## لایه‌ها
UI → API/Application → Domain/Services → Infrastructure/Database/Devices

## دامنه‌ها
Identity, Restaurant, Catalog, Customers, Orders, Billing, Inventory, Purchasing, Kitchen, Devices, Printing, Reporting, Files, Settings, Audit.

## Adapter
چاپگر و Caller ID پشت Interface/Adapter قرار گیرند.

## تراکنش
ثبت سفارش، فاکتور، پرداخت و تغییر موجودی در عملیات مرتبط Atomic باشد.

## Async
Print Job، اعلان، گزارش سنگین و Backup می‌تواند با Queue/Worker اجرا شود.

## API
REST/JSON با نسخه‌بندی /api/v1، Pagination، Filtering، Error Contract و Idempotency برای عملیات مالی.

## Observability
Structured Log، Health Check و Metrics برای DB، Queue، Printer و Caller ID.
