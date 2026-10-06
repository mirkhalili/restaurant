<?php
$isOrders = $action === 'create_order';
?>
<section class="page-head <?= $isOrders ? 'orders-page-head' : '' ?>">
  <div>
    <div class="eyebrow">مدیریت</div>
    <h1><?=htmlspecialchars($heading)?></h1>
    <p><?= $isOrders ? 'ثبت، پیگیری و مدیریت سفارش‌های رستوران' : 'مدیریت اطلاعات و عملیات این بخش.' ?></p>
  </div>
  <div class="head-actions">
    <a class="btn btn-primary" href="<?=$createUrl?>&new=1">＋ <?= $isOrders ? 'سفارش جدید' : 'افزودن' ?></a>
  </div>
</section>

<?php if($isOrders && isset($_GET['new'])): ?>
<section class="order-entry card">
  <div class="order-entry-head">
    <div>
      <span class="order-entry-icon">🧾</span>
      <div><strong>ثبت سفارش جدید</strong><small>اطلاعات سفارش را وارد کنید</small></div>
    </div>
    <a class="icon-btn" href="<?=$createUrl?>" aria-label="بستن">×</a>
  </div>
  <form method="post" class="order-form">
    <input type="hidden" name="action" value="<?=$action?>">
    <label>نوع سفارش
      <select name="order_type" required>
        <option value="حضوری">حضوری</option>
        <option value="بیرون‌بر">بیرون‌بر</option>
        <option value="تلفنی">تلفنی</option>
        <option value="ارسال">ارسال</option>
      </select>
    </label>
    <label>مبلغ کل
      <div class="input-with-suffix"><input name="total" type="number" min="0" step="1000" required placeholder="مثلاً ۵۰۰۰۰۰"><span>تومان</span></div>
    </label>
    <div class="order-form-note"><span>✓</span> سفارش پس از ثبت با وضعیت «تأیید شده» ذخیره می‌شود.</div>
    <div class="form-actions">
      <button class="btn btn-primary" type="submit">ثبت سفارش</button>
      <a class="btn" href="<?=$createUrl?>">انصراف</a>
    </div>
  </form>
</section>
<?php elseif(isset($_GET['new'])): ?>
<form class="card form-card" method="post">
  <div class="form-grid">
    <input type="hidden" name="action" value="<?=$action?>">
    <?php if($action==='create_customer'): ?>
      <label>نام مشتری<input name="name" required></label>
      <label>یادداشت<input name="notes"></label>
    <?php elseif($action==='create_product'): ?>
      <label>نام غذا<input name="name" required></label>
      <label>قیمت<input name="price" type="number" min="0" required></label>
    <?php endif; ?>
    <div class="form-actions"><button class="btn btn-primary">ثبت اطلاعات</button><a class="btn" href="<?=$createUrl?>">انصراف</a></div>
  </div>
</form>
<?php endif; ?>

<section class="card section-card <?= $isOrders ? 'orders-card' : '' ?>">
  <div class="toolbar <?= $isOrders ? 'orders-toolbar' : '' ?>">
    <div class="toolbar-title">
      <strong><?= $isOrders ? 'فهرست سفارش‌ها' : 'فهرست اطلاعات' ?></strong>
      <small><?= $isOrders ? 'آخرین ۱۰۰ سفارش ثبت‌شده' : 'رکوردهای ثبت‌شده' ?></small>
    </div>
    <div class="filters">
      <input class="search-input" data-global-search-local placeholder="<?= $isOrders ? 'جستجوی شماره سفارش...' : 'جستجو در جدول...' ?>">
      <?php if($isOrders): ?>
        <select class="table-filter" aria-label="فیلتر وضعیت">
          <option>همه وضعیت‌ها</option><option>تأیید شده</option><option>در حال آماده‌سازی</option><option>تکمیل شده</option><option>لغو شده</option>
        </select>
      <?php endif; ?>
      <button class="btn" type="button" onclick="window.location.href='<?=$createUrl?>'">↻ تازه‌سازی</button>
    </div>
  </div>
  <div class="table-wrap">
    <table class="<?= $isOrders ? 'orders-table' : '' ?>">
      <thead>
      <?php if($action==='create_customer'): ?>
        <tr><th>نام</th><th>یادداشت</th><th>تاریخ</th><th>عملیات</th></tr>
      <?php elseif($action==='create_product'): ?>
        <tr><th>غذا</th><th>قیمت</th><th>وضعیت</th><th>عملیات</th></tr>
      <?php else: ?>
        <tr><th>سفارش</th><th>نوع</th><th>مبلغ</th><th>وضعیت</th><th>زمان ثبت</th><th class="action-col">عملیات</th></tr>
      <?php endif; ?>
      </thead>
      <tbody>
      <?php if(empty($rows)): ?>
        <tr><td colspan="<?=$isOrders ? '6' : '6'?>"><div class="empty"><strong><?= $isOrders ? 'هنوز سفارشی ثبت نشده است' : 'رکوردی یافت نشد' ?></strong><span>برای شروع یک مورد جدید اضافه کنید.</span></div></td></tr>
      <?php else: foreach($rows as $r): ?>
        <?php if($action==='create_customer'): ?>
          <tr><td><strong><?=htmlspecialchars($r['name'])?></strong></td><td><?=htmlspecialchars($r['notes']??'—')?></td><td><?=htmlspecialchars($r['created_at'])?></td><td><button class="btn">مشاهده</button></td></tr>
        <?php elseif($action==='create_product'): ?>
          <tr><td><strong><?=htmlspecialchars($r['name'])?></strong></td><td><?=number_format($r['price'])?></td><td><span class="badge success"><?=htmlspecialchars($r['status'])?></span></td><td><button class="btn">ویرایش</button></td></tr>
        <?php else:
          $status = (string)$r['status'];
          $statusClass = match ($status) {
            'confirmed','تأیید شده' => 'primary',
            'preparing','در حال آماده‌سازی' => 'warning',
            'completed','تکمیل شده' => 'success',
            'cancelled','لغو شده' => 'danger',
            default => 'info'
          };
        ?>
          <tr>
            <td><span class="order-number">#<?=htmlspecialchars($r['order_no'])?></span></td>
            <td><span class="order-type"><?=htmlspecialchars($r['order_type'])?></span></td>
            <td><strong class="order-amount"><?=number_format((float)$r['total_amount'])?></strong><small class="currency"> تومان</small></td>
            <td><span class="badge <?=$statusClass?>"><?=htmlspecialchars($status)?></span></td>
            <td><span class="order-time"><?=htmlspecialchars($r['created_at'])?></span></td>
            <td class="action-col"><button class="btn btn-sm">جزئیات <span>←</span></button></td>
          </tr>
        <?php endif; ?>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</section>