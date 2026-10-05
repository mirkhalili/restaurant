<section class="page-head"><div><div class="eyebrow">داشبورد مدیریتی</div><h1>خلاصه عملکرد امروز</h1><p>وضعیت فروش و عملیات رستوران را در یک نگاه ببینید.</p></div><div class="head-actions"><a class="btn" href="/?page=reports">گزارش کامل</a><a class="btn btn-primary" href="/?page=orders&new=1">＋ سفارش جدید</a></div></section>
<div class="grid kpi-grid">
<?php $icons=['فروش امروز'=>'↗','سفارش‌ها'=>'▤','مشتریان'=>'♙','میانگین سفارش'=>'◈']; foreach($stats as $i=>$s): ?>
<div class="card stat-card"><div class="stat-top"><span><?=htmlspecialchars($s['label'])?></span><span class="stat-icon"><?=$icons[$s['label']]??'•'?></span></div><div class="stat-value"><?=number_format($s['value'])?></div><div class="stat-meta"><span class="up">●</span> به‌روزرسانی امروز</div></div>
<?php endforeach; ?>
</div>
<div class="grid dashboard-grid">
<section class="card section-card"><div class="card-head"><h2>آخرین سفارش‌ها</h2><a class="btn" href="/?page=orders">مشاهده همه</a></div><div class="table-wrap"><table><thead><tr><th>شماره</th><th>نوع</th><th>مبلغ</th><th>وضعیت</th></tr></thead><tbody><?php foreach($orders as $o): ?><tr><td><strong>#<?=htmlspecialchars($o['order_no'])?></strong></td><td><?=htmlspecialchars($o['order_type'])?></td><td><?=number_format($o['total_amount'])?></td><td><span class="badge <?=in_array($o['status'],['completed','ready'])?'success':($o['status']==='cancelled'?'danger':'primary')?>"><?=htmlspecialchars($o['status'])?></span></td></tr><?php endforeach; ?></tbody></table></div></section>
<section class="card section-card"><div class="card-head"><h2>دسترسی سریع</h2><small>عملیات پرتکرار</small></div><div class="card-body quick-grid">
<a class="quick-action" href="/?page=orders&new=1"><span>🧾</span><div><b>سفارش جدید</b><small>ثبت سفارش و پرداخت</small></div></a>
<a class="quick-action" href="/?page=customers&new=1"><span>👤</span><div><b>مشتری جدید</b><small>افزودن اطلاعات مشتری</small></div></a>
<a class="quick-action" href="/?page=products&new=1"><span>🍔</span><div><b>محصول جدید</b><small>مدیریت منو و قیمت</small></div></a>
<a class="quick-action" href="/?page=inventory"><span>📦</span><div><b>کنترل موجودی</b><small>بررسی اقلام کم‌موجودی</small></div></a>
</div></section></div>