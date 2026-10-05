# مدل داده سطح بالا

## هویت
users, roles, permissions, role_permissions, user_roles, audit_logs, settings

## رستوران
branches, dining_areas, tables, cash_registers, shifts

## فروش
customers, customer_phones, customer_addresses, orders, order_items, modifiers, invoices, invoice_items, payments, refunds, discounts, taxes

## منو
categories, products, menus, menu_items, modifier_groups, product_prices

## آشپزخانه
kitchen_stations, kitchen_tickets, kitchen_ticket_items

## انبار
warehouses, inventory_items, stock_movements, recipes, recipe_items, units, suppliers, purchase_orders, purchase_items, goods_receipts, wastes, stock_counts, stock_transfers

## دستگاه
printers, printer_routes, print_templates, print_jobs, caller_id_devices, device_events

## فایل
files, file_links, import_batches, import_errors

## اصول
کلید داخلی از شماره نمایشی اسناد جدا باشد؛ مبلغ با Decimal ذخیره شود؛ Foreign Key و Unique Constraint رعایت شود؛ برای تاریخ، Actor و جستجو Index مناسب تعریف شود.
