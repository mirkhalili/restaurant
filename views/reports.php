<section class="page-head"><div><div class="eyebrow">REPORTS / ANALYTICS</div><h1>گزارشات و فاکتورها</h1><p>گزارش‌های فروش با تاریخ شمسی و فهرست کامل فاکتورها را بر اساس بازه زمانی و مشتری مشاهده و مدیریت کنید.</p></div></section>
<?php if(!empty($message)): ?><div class="alert success"><?=htmlspecialchars($message)?></div><?php endif; ?>
<?php if(!empty($error)): ?><div class="alert"><?=htmlspecialchars($error)?></div><?php endif; ?>
<form class="card toolbar report-filter" method="get"><input type="hidden" name="page" value="reports"><div class="filters">
<label>از تاریخ شمسی <input class="field" type="text" name="from" value="<?=htmlspecialchars($fromJ)?>" placeholder="۱۴۰۵/۰۷/۱۴" inputmode="numeric"></label>
<label>تا تاریخ شمسی <input class="field" type="text" name="to" value="<?=htmlspecialchars($toJ)?>" placeholder="۱۴۰۵/۰۷/۱۴" inputmode="numeric"></label>
<label>مشتری <select class="field" name="customer_id"><option value="0">همه مشتریان</option><?php foreach($customers as $c): ?><option value="<?=$c['id']?>" <?=$customerId===(int)$c['id']?'selected':''?>><?=htmlspecialchars($c['name'])?> — <?=htmlspecialchars($c['phone'])?></option><?php endforeach; ?></select></label>
<label>وضعیت <select class="field" name="status"><option value="completed" <?=$status==='completed'?'selected':''?>>تکمیل‌شده</option><option value="all" <?=$status==='all'?'selected':''?>>همه</option><option value="cancelled" <?=$status==='cancelled'?'selected':''?>>لغوشده</option><option value="refunded" <?=$status==='refunded'?'selected':''?>>مرجوع‌شده</option></select></label>
<button class="btn btn-primary">اعمال فیلتر</button></div></form>

<div class="grid kpi-grid report-kpis">
<div class="card stat-card"><div class="stat-top"><span>فروش</span><span class="stat-icon">↗</span></div><div class="stat-value"><?=number_format((float)($summary['sales']??0))?></div><div class="stat-meta">ریال</div></div>
<div class="card stat-card"><div class="stat-top"><span>تعداد سفارش</span><span class="stat-icon">▤</span></div><div class="stat-value"><?=number_format((int)($summary['order_count']??0))?></div></div>
<div class="card stat-card"><div class="stat-top"><span>میانگین سفارش</span><span class="stat-icon">◈</span></div><div class="stat-value"><?=number_format((float)($summary['avg_order']??0))?></div><div class="stat-meta">ریال</div></div>
<div class="card stat-card"><div class="stat-top"><span>پرداخت‌شده</span><span class="stat-icon">✓</span></div><div class="stat-value"><?=number_format((float)($summary['paid']??0))?></div><div class="stat-meta">ریال</div></div></div>

<section class="card section-card report-section"><div class="card-head"><div><h2>لیست فاکتورها</h2><small><?=number_format(count($invoices))?> فاکتور در فیلتر فعلی</small></div></div>
<div class="table-wrap"><table><thead><tr><th>فاکتور</th><th>تاریخ</th><th>مشتری</th><th>نوع سفارش</th><th>مبلغ</th><th>پرداخت</th><th>وضعیت</th><th>عملیات</th></tr></thead><tbody>
<?php if(!$invoices): ?><tr><td colspan="8"><div class="empty"><strong>فاکتوری در این بازه پیدا نشد.</strong></div></td></tr>
<?php else: foreach($invoices as $i): ?><tr>
<td><strong><?=htmlspecialchars($i['invoice_no'])?></strong><small><?=htmlspecialchars($i['order_no'])?></small></td>
<td><?=htmlspecialchars(App\Support\PersianDate::format($i['created_at']))?></td>
<td><?=htmlspecialchars($i['customer_name'])?></td>
<td><span class="badge order-type-badge"><?=htmlspecialchars($i['order_type']??'—')?></span></td>
<td><?=number_format((float)$i['total_amount'])?> ریال</td>
<td><?=htmlspecialchars(['cash'=>'نقدی','card'=>'کارتخوان','online'=>'آنلاین','mixed'=>'ترکیبی'][$i['payment_method']??'']??'—')?></td>
<td><span class="badge <?=in_array($i['order_status'],['cancelled','refunded'],true)?'danger':'success'?>"><?=htmlspecialchars(['completed'=>'تکمیل‌شده','cancelled'=>'لغوشده','refunded'=>'مرجوع‌شده','draft'=>'پیش‌نویس','confirmed'=>'تأییدشده','preparing'=>'درحال آماده‌سازی','ready'=>'آماده','delivered'=>'تحویل داده‌شده','picked_up'=>'تحویل حضوری'][$i['order_status']]??$i['order_status'])?></span></td>
<td class="row-actions"><a class="btn btn-sm" href="/?page=invoice&id=<?=$i['id']?>">مشاهده / ویرایش</a><a class="btn btn-sm" href="/?page=print&invoice=<?=urlencode($i['invoice_no'])?>">چاپ</a><?php if(in_array(($user['role_name']??''),['مدیر سیستم','مدیر رستوران'],true)): ?><form method="post" onsubmit="return confirm('فاکتور و اطلاعات سفارش آن حذف شود؟ این عملیات قابل بازگشت نیست.')"><input type="hidden" name="action" value="delete_invoice"><input type="hidden" name="invoice_id" value="<?=$i['id']?>"><button class="btn btn-sm btn-danger">حذف</button></form><?php endif; ?></td>
</tr><?php endforeach; endif; ?></tbody></table></div></section>

<div class="grid dashboard-grid"><section class="card section-card"><div class="card-head"><h2>فروش روزانه</h2></div><div class="table-wrap"><table><thead><tr><th>تاریخ شمسی</th><th>سفارش</th><th>فروش</th></tr></thead><tbody><?php foreach($daily as $r): ?><tr><td><?=htmlspecialchars(App\Support\PersianDate::format($r['day'],false))?></td><td><?=number_format($r['orders'])?></td><td><?=number_format($r['amount'])?> ریال</td></tr><?php endforeach; ?></tbody></table></div></section>
<section class="card section-card"><div class="card-head"><h2>روش‌های پرداخت</h2></div><div class="table-wrap"><table><thead><tr><th>روش</th><th>تعداد</th><th>مبلغ</th></tr></thead><tbody><?php foreach($payments as $r): ?><tr><td><?=htmlspecialchars(['cash'=>'نقدی','card'=>'کارتخوان','online'=>'آنلاین','mixed'=>'ترکیبی'][$r['payment_method']??'']??'—')?></td><td><?=number_format($r['orders'])?></td><td><?=number_format($r['amount'])?> ریال</td></tr><?php endforeach; ?></tbody></table></div></section></div>
<section class="card section-card report-section"><div class="card-head"><h2>پرفروش‌ترین محصولات</h2><small>۲۰ محصول برتر</small></div><div class="table-wrap"><table><thead><tr><th>محصول</th><th>تعداد</th><th>فروش</th></tr></thead><tbody><?php foreach($products as $r): ?><tr><td><strong><?=htmlspecialchars($r['name'])?></strong></td><td><?=number_format((int)$r['qty'])?></td><td><?=number_format((float)$r['amount'])?> ریال</td></tr><?php endforeach; ?></tbody></table></div></section>