<?php
$moduleMeta=[
'inventory'=>['انبار و خرید','کنترل موجودی، ورود و خروج و خرید'],
'kitchen'=>['آشپزخانه','صف سفارش‌ها و وضعیت آماده‌سازی'],
'reports'=>['گزارش‌ها','گزارش‌های عملیاتی، مالی و مدیریتی'],
'users'=>['کاربران و دسترسی‌ها','مدیریت کاربران، نقش‌ها و مجوزها'],
'settings'=>['تنظیمات','تنظیمات عمومی، شعبه و سامانه'],
'audit'=>['Audit Log','ردیابی عملیات حساس و تغییرات'],
];
$meta=$moduleMeta[$page]??['ماژول','ماژول سامانه'];
?>
<section class="page-head"><div><div class="eyebrow">ماژول سامانه</div><h1><?=htmlspecialchars($meta[0])?></h1><p><?=htmlspecialchars($meta[1])?></p></div><div class="head-actions"><button class="btn btn-primary">＋ عملیات جدید</button></div></section>
<section class="card section-card"><div class="card-body"><div class="empty"><strong>رابط کاربری این ماژول آماده است</strong><span>ساختار صفحه، Navigation، Theme و Responsive مطابق Design System پروژه پیاده‌سازی شده است. منطق عملیاتی این بخش طبق roadmap در گام بعد تکمیل می‌شود.</span></div></div></section>