<section class="page-head"><div><div class="eyebrow">POS / فروش</div><h1>ثبت سفارش و صدور فاکتور</h1><p>مشتری را با شماره تلفن پیدا کنید، سپس با یک کلیک محصولات را به فاکتور اضافه کنید.</p></div></section>
<?php if(!empty($message)): ?><div class="alert success"><?=htmlspecialchars($message)?></div><?php endif; ?><?php if(!empty($error)): ?><div class="alert"><?=htmlspecialchars($error)?></div><?php endif; ?><?php if(!empty($_GET['success'])): ?><div class="success-box"><strong>فاکتور با موفقیت ثبت شد</strong><span>شماره فاکتور: <?=htmlspecialchars($_GET['success'])?></span></div><?php endif; ?>
<div class="pos-grid">
 <section class="card pos-main">
  <div class="card-head"><div><h2>۱. انتخاب مشتری</h2><small>جستجو فقط بر اساس شماره تلفن یکتا</small></div></div>
  <form class="customer-search" method="get"><input type="hidden" name="page" value="orders"><input class="field" name="phone" value="<?=htmlspecialchars($phone??'')?>" inputmode="tel" placeholder="مثلاً 09121234567" required><button class="btn btn-primary">جستجوی مشتری</button></form>
  <?php if($customer): ?><div class="customer-result"><div class="customer-avatar">♙</div><div><strong><?=htmlspecialchars($customer['name'])?></strong><small><?=htmlspecialchars($customer['phone'])?> · کد اشتراک <?=htmlspecialchars($customer['subscription_code']??'—')?></small></div><span class="badge success">مشتری یافت شد</span></div><?php elseif(($phone??'')!==''): ?><div class="empty compact"><strong>مشتری پیدا نشد</strong><span>شماره را بررسی کنید یا ابتدا مشتری را ثبت کنید.</span></div><?php endif; ?>
  <div class="card-head product-head"><div><h2>۲. انتخاب محصولات</h2><small>با هر کلیک، تعداد همان محصول یک واحد افزایش می‌یابد.</small></div><input class="search-input" data-product-search placeholder="جستجوی محصول..."></div>
  <div class="product-grid" data-products>
   <?php foreach($products as $p): ?><form class="product-card" method="post" data-product><input type="hidden" name="action" value="add_item"><input type="hidden" name="product_id" value="<?=$p['id']?>"><button type="submit"><span class="product-type"><?=htmlspecialchars($p['product_type']??'محصول')?></span><strong><?=htmlspecialchars($p['name'])?></strong><small><?=htmlspecialchars($p['product_code'])?></small><b><?=number_format((float)$p['price'])?> <i>ریال</i></b></button></form><?php endforeach; ?>
  </div>
 </section>
 <aside class="card invoice-card">
  <div class="invoice-top"><div><span>پیش‌فاکتور</span><strong>جدید</strong></div><div class="invoice-date"><?=htmlspecialchars(App\Support\PersianDate::today())?></div></div>
  <div class="invoice-customer"><?php if($customer): ?><span>مشتری</span><strong><?=htmlspecialchars($customer['name'])?></strong><small><?=htmlspecialchars($customer['phone'])?></small><?php else: ?><span class="muted">مشتری انتخاب نشده</span><?php endif; ?></div>
  <div class="invoice-items"><?php if(!$items): ?><div class="empty compact"><strong>فاکتور خالی است</strong><span>از لیست محصولات انتخاب کنید.</span></div><?php else: foreach($items as $it): ?><div class="invoice-item"><div><strong><?=htmlspecialchars($it['name'])?></strong><small><?=number_format((float)$it['price'])?> × <?=$it['qty']?></small></div><b><?=number_format((float)$it['line_total'])?></b><form method="post"><input type="hidden" name="action" value="remove_item"><input type="hidden" name="product_id" value="<?=$it['id']?>"><button aria-label="حذف">×</button></form></div><?php endforeach; endif; ?></div>
  <div class="invoice-total"><span>مبلغ نهایی</span><strong><?=number_format((float)$total)?> <small>ریال</small></strong></div>
  <form method="post" class="finalize-form"><input type="hidden" name="action" value="finalize"><input type="hidden" name="customer_phone" value="<?=htmlspecialchars($customer['phone']??'')?>"><label>شیوه پرداخت<select name="payment_method"><option value="cash">نقدی</option><option value="card">کارتخوان</option><option value="online">آنلاین</option><option value="mixed">ترکیبی</option></select></label><button class="btn btn-primary finalize-btn" <?=(!$customer||!$items)?'disabled':''?>>ثبت نهایی و صدور فاکتور</button></form>
  <form method="post"><input type="hidden" name="action" value="clear_cart"><button class="btn clear-btn" <?=!$items?'disabled':''?>>خالی کردن فاکتور</button></form>
 </aside>
</div>
<script>
document.querySelector('[data-product-search]')?.addEventListener('input',e=>{const q=e.target.value.trim().toLowerCase();document.querySelectorAll('[data-product]').forEach(x=>x.hidden=q&&!x.textContent.toLowerCase().includes(q));});
</script>