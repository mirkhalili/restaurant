<section class="page-head"><div><div class="eyebrow">POS / فروش</div><h1>ثبت سفارش و صدور فاکتور</h1><p>مشتری را انتخاب کنید، سپس محصولات را به پیش‌فاکتور اضافه کنید.</p></div></section>
<?php if(!empty($message)): ?><div class="alert success"><?=htmlspecialchars($message)?></div><?php endif; ?>
<?php if(!empty($error)): ?><div class="alert"><?=htmlspecialchars($error)?></div><?php endif; ?>
<div class="pos-grid">
<section class="card pos-main">
<?php if(!$customer): ?>
  <div class="card-head"><div><h2>۱. انتخاب مشتری</h2><small>جستجو به‌صورت لحظه‌ای</small></div></div>
  <div class="customer-search"><input class="field" id="customer-live-search" autocomplete="off" value="<?=htmlspecialchars($phone??'')?>" inputmode="search" placeholder="نام، نام خانوادگی یا شماره تلفن"><span class="search-status" id="customer-search-status">برای جستجو شروع به تایپ کنید</span></div>
  <div id="customer-suggestions" class="customer-suggestions" hidden></div>
  <?php if(($phone??'')!==''): ?>
    <form method="post" class="new-order-customer">
      <input type="hidden" name="action" value="add_customer_order">
      <div class="new-customer-head"><strong>مشتری پیدا نشد</strong><small>اطلاعات را ثبت کنید تا برای همین سفارش انتخاب شود.</small></div>
      <div class="order-customer-grid">
        <label>نام *<input name="first_name" required></label>
        <label>نام خانوادگی *<input name="last_name" required></label>
        <label>شماره تلفن *<input name="phone" required value="<?=htmlspecialchars($phone)?>"></label>
        <label>موبایل<input name="mobile"></label>
        <label>کد اشتراک<input name="subscription_code"></label>
        <label>تاریخ عضویت<input name="membership_date"></label>
        <label>تاریخ تولد<input name="birth_date"></label>
        <label>آدرس<input name="address"></label>
      </div>
      <button class="btn btn-primary">ثبت و انتخاب مشتری</button>
    </form>
  <?php endif; ?>
<?php else: ?>
  <div class="order-type-panel">
    <div><strong>نوع سفارش</strong><small>سالن یا بیرون‌بر را مشخص کنید</small></div>
    <div class="order-type-buttons">
      <?php foreach(['سالن'=>'🍽️','بیرون‌بر'=>'🥡'] as $type=>$icon): ?>
        <form method="post">
          <input type="hidden" name="action" value="set_order_type">
          <input type="hidden" name="order_type" value="<?=htmlspecialchars($type)?>">
          <button class="order-type-btn <?=($orderType===$type?'active':'')?>" type="submit"><span><?=$icon?></span><?=$type?></button>
        </form>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="card-head product-head">
    <div><h2>۲. انتخاب محصولات</h2><small>محصولات مجاز برای «<?=htmlspecialchars($orderType)?>» به‌صورت پیش‌فرض در فاکتور قرار گرفته‌اند.</small></div>
    <input class="search-input" data-product-search placeholder="جستجوی محصول...">
  </div>

  <div class="product-type-filters" data-type-filters>
    <button type="button" class="type-filter <?=($selectedType??'غذا')==='غذا'?'active':''?>" data-type="غذا"><span>غذا</span></button>
    <?php foreach($productTypes as $t): ?>
      <button type="button" class="type-filter <?=($selectedType??'all')===$t['name']?'active':''?>" data-type="<?=htmlspecialchars($t['name'])?>" style="--filter-color:<?=htmlspecialchars($t['color'])?>"><span><?=htmlspecialchars($t['name'])?></span></button>
    <?php endforeach; ?>
  </div>

  <div class="product-grid" data-products>
    <?php foreach($products as $p): ?>
      <form class="product-card" method="post" data-product data-type="<?=htmlspecialchars($p['category_name']??$p['product_type']??'')?>" style="--card-type-color:<?=htmlspecialchars($p['category_color']??'var(--line)')?>">
        <input type="hidden" name="action" value="add_item">
        <input type="hidden" name="product_id" value="<?=$p['id']?>"><input type="hidden" name="selected_type" value="<?=htmlspecialchars($selectedType??'all')?>">
        <button type="submit">
          <span class="product-type"><?=htmlspecialchars($p['category_name']??$p['product_type']??'بدون نوع')?></span>
          <strong><?=htmlspecialchars($p['name'])?></strong>
          <small><?=htmlspecialchars($p['product_code']??'')?></small>
          <b><?=number_format((float)$p['price'])?> <i>ریال</i></b>
        </button>
      </form>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
</section>

<aside class="card invoice-card">
  <div class="invoice-top">
    <div><span>پیش‌فاکتور</span><strong><?=!empty($_SESSION['order_draft_id'])?'ذخیره‌شده':'جدید'?></strong></div>
    <div class="invoice-date"><?=htmlspecialchars(App\Support\PersianDate::today())?></div>
  </div>

  <div class="invoice-order-type" aria-label="نوع سفارش">
    <strong><?=htmlspecialchars($orderType)?></strong>
  </div>

  <div class="invoice-customer">
    <?php if($customer): ?>
      <div class="invoice-customer-main">
        <span>مشتری</span>
        <strong><?=htmlspecialchars($customer['name'])?></strong>
        <small>تلفن: <?=htmlspecialchars($customer['phone'])?></small>
      </div>
      <form method="post" class="invoice-customer-remove">
        <input type="hidden" name="action" value="remove_customer_order">
        <button type="submit" class="invoice-customer-remove-btn" aria-label="حذف مشتری از سفارش" title="حذف مشتری از سفارش">×</button>
      </form>
    <?php else: ?>
      <span class="muted">مشتری انتخاب نشده</span>
    <?php endif; ?>
  </div>

  <div class="invoice-items">
    <?php if(!$items): ?>
      <div class="empty compact"><strong>فاکتور خالی است</strong><span>از لیست محصولات انتخاب کنید.</span></div>
    <?php else: foreach($items as $it): ?>
      <div class="invoice-item" role="button" tabindex="0" title="برای کم کردن یک عدد کلیک کنید" onclick="if(!event.target.closest('form'))this.querySelector('form').submit()" onkeydown="if((event.key==='Enter'||event.key===' ')&&!event.target.closest('form')){event.preventDefault();this.querySelector('form').submit()}">
        <div><strong><?=htmlspecialchars($it['name'])?></strong><small><?=number_format((float)$it['price'])?> × <?=$it['qty']?></small></div>
        <b><?=number_format((float)$it['line_total'])?></b>
        <form method="post"><input type="hidden" name="action" value="remove_item"><input type="hidden" name="product_id" value="<?=$it['id']?>"><button aria-label="کم کردن یک عدد" title="کم کردن یک عدد">−</button></form>
      </div>
    <?php endforeach; endif; ?>
  </div>

  <div class="invoice-summary">
    <div><span>جمع سفارش</span><strong><?=number_format((float)$subtotal)?> <small>ریال</small></strong></div>
    <form method="post" class="discount-form">
      <input type="hidden" name="action" value="apply_discount">
      <label>درصد تخفیف <input id="discount-percent" class="field" type="number" min="0" max="100" step="0.01" name="discount_percent" value="<?=htmlspecialchars((string)$discountPercent)?>"> <small>%</small></label>
      <button class="btn" type="submit">اعمال</button>
    </form>
    <div class="discount-row"><span>تخفیف (<?=htmlspecialchars((string)$discountPercent)?>٪)</span><strong><?=number_format((float)$discountAmount)?> <small>ریال</small></strong></div>
  </div>
  <div class="invoice-total"><span>مبلغ نهایی</span><strong><?=number_format((float)$total)?> <small>ریال</small></strong></div>
  <form method="post" class="save-draft-bottom">
    <input type="hidden" name="action" value="save_draft">
    <input type="hidden" id="save-draft-discount" name="discount_percent" value="<?=htmlspecialchars((string)$discountPercent)?>">
    <button class="btn btn-primary save-draft-btn" type="submit" <?=!$items?'disabled':''?>>ذخیره فاکتور</button>
  </form>
  <form method="post" class="finalize-form" onsubmit="document.getElementById('final-discount-percent').value=document.getElementById('discount-percent').value">
    <input type="hidden" name="action" value="finalize">
    <input type="hidden" name="customer_phone" value="<?=htmlspecialchars($customer['phone']??'')?>">
    <input type="hidden" id="final-discount-percent" name="discount_percent" value="<?=htmlspecialchars((string)$discountPercent)?>">
    <label>شیوه پرداخت<select name="payment_method"><option value="cash">نقدی</option><option value="card">کارتخوان</option><option value="online">آنلاین</option><option value="mixed">ترکیبی</option></select></label>
    <button class="btn btn-primary finalize-btn" <?=(!$customer||!$items)?'disabled':''?>>پرداخت و ادامه به چاپ فیش</button>
  </form>
  <form method="post"><input type="hidden" name="action" value="new_order"><button class="btn clear-btn" type="submit">فاکتور جدید</button></form>
</aside>
</div>

<?php if(!empty($pendingDrafts)): ?>
<section class="card pending-invoices pending-invoices-bottom">
  <div class="card-head">
    <div><h2>فاکتورهای در دست اقدام</h2><small>فاکتورهای ذخیره‌شده تا زمان نهایی‌شدن باقی می‌مانند.</small></div>
    <form method="post"><input type="hidden" name="action" value="new_order"><button class="btn btn-primary" type="submit">＋ فاکتور جدید</button></form>
  </div>
  <div class="pending-invoice-grid">
    <?php foreach($pendingDrafts as $draft): ?>
      <article class="pending-invoice">
        <div class="pending-invoice-head"><strong><?=htmlspecialchars($draft['customer_name'])?></strong><span><?=htmlspecialchars($draft['order_type'])?></span></div>
        <div class="pending-invoice-meta"><span><?=htmlspecialchars((string)$draft['item_count'])?> قلم</span><span>تخفیف <?=number_format((float)$draft['discount_percent'],2)?>٪</span><strong><?=number_format((float)$draft['total_amount'])?> ریال</strong></div>
        <small>آخرین تغییر: <?=htmlspecialchars(App\Support\PersianDate::format($draft['updated_at']?:$draft['created_at']))?></small>
        <div class="pending-invoice-actions">
          <form method="post"><input type="hidden" name="action" value="load_draft"><input type="hidden" name="draft_id" value="<?=htmlspecialchars((string)$draft['id'])?>"><button class="btn" type="submit">باز کردن فاکتور</button></form>
          <form method="post" onsubmit="return confirm('این فاکتور در دست اقدام حذف شود؟');"><input type="hidden" name="action" value="delete_draft"><input type="hidden" name="draft_id" value="<?=htmlspecialchars((string)$draft['id'])?>"><button class="btn btn-danger" type="submit">حذف</button></form>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</section>
<?php else: ?>
<section class="card pending-empty pending-invoices-bottom"><div><strong>فاکتور در دست اقدامی وجود ندارد</strong><small>برای شروع، از دکمه «فاکتور جدید» در پایین صفحه استفاده کنید.</small></div><form method="post"><input type="hidden" name="action" value="new_order"><button class="btn btn-primary" type="submit">＋ فاکتور جدید</button></form></section>
<?php endif; ?>

<script>
(()=>{const input=document.getElementById('customer-live-search'),box=document.getElementById('customer-suggestions'),status=document.getElementById('customer-search-status');if(!input||!box)return;let timer;const normalize=s=>s.replace(/[يى]/g,'ی').replace(/ك/g,'ک');input.addEventListener('input',()=>{clearTimeout(timer);const q=normalize(input.value.trim());if(q.length<2){box.hidden=true;status.textContent='حداقل دو حرف یا رقم وارد کنید';return}status.textContent='در حال جستجو…';timer=setTimeout(()=>fetch('/api/v1/customers/search?q='+encodeURIComponent(q)).then(r=>r.json()).then(d=>{box.innerHTML='';if(!d.items.length){box.hidden=true;status.textContent='مشتری پیدا نشد';return}d.items.forEach(c=>{const a=document.createElement('button');a.type='button';a.className='customer-suggestion';a.innerHTML='<strong>'+c.name+'</strong><small>'+c.phone+(c.mobile?' · '+c.mobile:'')+'</small>';a.addEventListener('click',()=>location.href='/?page=orders&phone='+encodeURIComponent(c.phone));box.appendChild(a)});box.hidden=false;status.textContent=d.items.length+' مشتری پیدا شد'}).catch(()=>status.textContent='خطا در جستجو'),220)});document.addEventListener('click',e=>{if(!box.contains(e.target)&&e.target!==input)box.hidden=true})})();
const search=document.querySelector('[data-product-search]');let selectedType=<?=json_encode((string)($selectedType??'غذا'),JSON_UNESCAPED_UNICODE)?>;function filterProducts(){const q=(search?.value||'').trim().toLowerCase();document.querySelectorAll('[data-product]').forEach(x=>{const type=x.dataset.type||'';const okType=selectedType==='غذا'?type.includes('غذا'):selectedType==='all'||type===selectedType;const okSearch=!q||x.textContent.toLowerCase().includes(q);x.hidden=!(okType&&okSearch);});}search?.addEventListener('input',filterProducts);document.querySelectorAll('[data-type-filters] .type-filter').forEach(b=>b.addEventListener('click',()=>{selectedType=b.dataset.type;document.querySelectorAll('input[name="selected_type"]').forEach(x=>x.value=selectedType);document.querySelectorAll('[data-type-filters] .type-filter').forEach(x=>x.classList.toggle('active',x===b));filterProducts();}));filterProducts();
const discountInput=document.getElementById('discount-percent');const saveDraftDiscount=document.getElementById('save-draft-discount');discountInput?.addEventListener('input',()=>{if(saveDraftDiscount)saveDraftDiscount.value=discountInput.value;});
</script>
