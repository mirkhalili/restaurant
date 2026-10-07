<section class="page-head"><div><div class="eyebrow">مدیریت / انبار</div><h1>انبار و کنترل موجودی</h1><p>موجودی کالا، حداقل موجودی و میزان مصرف هر کالا در سفارش را مدیریت کنید.</p></div></section>
<?php if(!empty($message)): ?><div class="alert success"><?=htmlspecialchars($message)?></div><?php endif; ?>
<?php if(!empty($error)): ?><div class="alert"><?=htmlspecialchars($error)?></div><?php endif; ?>

<div class="inventory-kpis">
 <div class="card inventory-kpi"><span>کالاهای انبار</span><strong><?=number_format(count($items))?></strong><small>اقلام ثبت‌شده</small></div>
 <div class="card inventory-kpi"><span>کنترل موجودی</span><strong><?=number_format($controlledCount)?></strong><small>کالاهای دارای کسر خودکار</small></div>
 <div class="card inventory-kpi inventory-kpi-alert"><span>نیاز به تأمین</span><strong><?=number_format(count($lowStock))?></strong><small>رسیده به حد سفارش یا کمتر</small></div>
</div>

<div class="inventory-layout">
<section class="card">
 <div class="card-head"><div><h2><?=!empty($editItem)?'ویرایش کالای انبار':'ثبت کالای جدید'?></h2><small>کالاهایی که کنترل موجودی ندارند، در سفارش باعث کسر یا هشدار تأمین نمی‌شوند.</small></div></div>
 <form method="post" class="inventory-form">
  <input type="hidden" name="action" value="save_item"><input type="hidden" name="id" value="<?=!empty($editItem)?(int)$editItem['id']:0?>">
  <label>کد کالا<input name="item_code" value="<?=htmlspecialchars($editItem['item_code']??'')?>" placeholder="مثلاً RIC-001"></label>
  <label>نام کالا *<input name="name" required value="<?=htmlspecialchars($editItem['name']??'')?>" placeholder="مثلاً برنج"></label>
  <label>واحد *<input name="unit" required value="<?=htmlspecialchars($editItem['unit']??'عدد')?>" placeholder="کیلوگرم، لیتر، عدد..."></label>
  <label>موجودی فعلی<input name="current_quantity" type="number" min="0" step="0.001" value="<?=htmlspecialchars((string)($editItem['current_quantity']??0))?>"></label>
  <label>حد سفارش<input name="reorder_level" type="number" min="0" step="0.001" value="<?=htmlspecialchars((string)($editItem['reorder_level']??0))?>"><small>اگر موجودی به این مقدار برسد، کالا در «نیاز به تأمین» قرار می‌گیرد.</small></label>
  <label class="inventory-check"><input type="checkbox" name="track_stock" value="1" <?=(!isset($editItem)||!empty($editItem['track_stock']))?'checked':''?>> <span>کنترل موجودی و کسر خودکار هنگام سفارش</span></label>
  <div class="form-actions"><button class="btn btn-primary">ذخیره کالا</button><?php if(!empty($editItem)): ?><a class="btn" href="/?page=inventory">لغو و ثبت جدید</a><?php endif; ?></div>
 </form>
</section>

<section class="card">
 <div class="card-head"><div><h2>اصلاح موجودی</h2><small>برای خرید/ورود کالا مقدار مثبت و برای کسری یا ضایعات مقدار منفی وارد کنید.</small></div></div>
 <form method="post" class="inventory-form inventory-adjust-form">
  <input type="hidden" name="action" value="adjust_stock">
  <label>کالا<select name="inventory_item_id" required><option value="">انتخاب کالا</option><?php foreach($inventoryItems as $i): ?><option value="<?=$i['id']?>"><?=htmlspecialchars($i['name'])?> — <?=number_format((float)$i['current_quantity'],3)?> <?=htmlspecialchars($i['unit'])?></option><?php endforeach; ?></select></label>
  <label>مقدار تغییر<input name="quantity_change" type="number" step="0.001" required placeholder="+10 یا -2"></label>
  <label>شرح<input name="note" placeholder="خرید، ضایعات، اصلاح شمارش..."></label>
  <div class="form-actions"><button class="btn btn-primary">ثبت تغییر موجودی</button></div>
 </form>
</section>
</div>

<section class="card inventory-stock-section">
 <div class="card-head"><div><h2>موجودی کالاها</h2><small>موجودی صفر برای کالاهای کنترل‌شده، محصول مرتبط را از صفحه سفارش حذف می‌کند.</small></div></div>
 <div class="table-wrap"><table class="inventory-table"><thead><tr><th>کالا</th><th>موجودی</th><th>حد سفارش</th><th>وضعیت</th><th>فرمول مصرف</th><th>عملیات</th></tr></thead><tbody>
 <?php foreach($items as $i): $low=(int)$i['track_stock']===1&&(float)$i['current_quantity']<=(float)$i['reorder_level']; ?>
 <tr>
  <td><strong><?=htmlspecialchars($i['name'])?></strong><small><?=htmlspecialchars($i['item_code']??'—')?> · <?=htmlspecialchars($i['unit'])?></small></td>
  <td><b class="<?=$low?'inventory-low-number':''?>"><?=number_format((float)$i['current_quantity'],3)?></b> <?=htmlspecialchars($i['unit'])?></td>
  <td><?=number_format((float)$i['reorder_level'],3)?></td>
  <td><?php if(!(int)$i['track_stock']): ?><span class="badge">بدون کنترل انبار</span><?php elseif((float)$i['current_quantity']<=0): ?><span class="badge danger">ناموجود</span><?php elseif($low): ?><span class="badge warning">نیاز به تأمین</span><?php else: ?><span class="badge success">موجود</span><?php endif; ?></td>
  <td><?=number_format((int)$i['recipe_count'])?> محصول</td>
  <td><div class="row-actions"><a class="btn btn-sm" href="/?page=inventory&edit_id=<?=$i['id']?>">ویرایش</a><form method="post"><input type="hidden" name="action" value="toggle_item"><input type="hidden" name="id" value="<?=$i['id']?>"><button class="btn btn-sm" type="submit"><?=((int)$i['track_stock']===1)?'بدون کنترل':'کنترل موجودی'?></button></form></div></td>
 </tr>
 <?php endforeach; ?>
 <?php if(!$items): ?><tr><td colspan="6"><div class="empty compact"><strong>هنوز کالایی در انبار ثبت نشده است.</strong><span>از فرم بالا اولین کالا را ثبت کنید.</span></div></td></tr><?php endif; ?>
 </tbody></table></div>
</section>

<section class="card inventory-alert-section">
 <div class="card-head"><div><h2>نیاز به تأمین</h2><small>این فهرست بر اساس «حد سفارش» کالاهای دارای کنترل موجودی ساخته می‌شود.</small></div></div>
 <?php if($lowStock): ?><div class="inventory-alert-grid"><?php foreach($lowStock as $i): ?><div class="inventory-alert"><strong><?=htmlspecialchars($i['name'])?></strong><span><?=number_format((float)$i['current_quantity'],3)?> <?=htmlspecialchars($i['unit'])?> موجود</span><b>حد سفارش: <?=number_format((float)$i['reorder_level'],3)?> <?=htmlspecialchars($i['unit'])?></b></div><?php endforeach; ?></div><?php else: ?><div class="empty compact"><strong>موردی برای تأمین فوری وجود ندارد.</strong><span>موجودی همه کالاهای کنترل‌شده بالاتر از حد سفارش است.</span></div><?php endif; ?>
</section>

<section class="card">
 <div class="card-head"><div><h2>میزان مصرف در هر سفارش</h2><small>برای هر محصول مشخص کنید از کدام کالای انبار و چه مقداری در هر سفارش مصرف می‌شود.</small></div></div>
 <form method="post" class="inventory-form inventory-rule-form">
  <input type="hidden" name="action" value="save_rule">
  <label>محصول *<select name="product_id" required><option value="">انتخاب محصول</option><?php foreach($products as $p): ?><option value="<?=$p['id']?>"><?=htmlspecialchars($p['name'])?><?=!empty($p['product_code'])?' — '.htmlspecialchars($p['product_code']):''?></option><?php endforeach; ?></select></label>
  <label>کالای انبار *<select name="inventory_item_id" required><option value="">انتخاب کالا</option><?php foreach($inventoryItems as $i): ?><option value="<?=$i['id']?>"><?=htmlspecialchars($i['name'])?> (<?=htmlspecialchars($i['unit'])?>)</option><?php endforeach; ?></select></label>
  <label>مقدار مصرف در یک سفارش *<input name="quantity_per_order" type="number" min="0.001" step="0.001" required placeholder="مثلاً 0.250"></label>
  <div class="form-actions"><button class="btn btn-primary">ثبت / بروزرسانی فرمول</button></div>
 </form>
 <div class="table-wrap"><table class="inventory-table"><thead><tr><th>محصول</th><th>کالای انبار</th><th>مصرف هر سفارش</th><th>موجودی</th><th>وضعیت</th><th></th></tr></thead><tbody>
 <?php foreach($rules as $r): ?>
 <tr>
  <td><strong><?=htmlspecialchars($r['product_name'])?></strong></td>
  <td><?=htmlspecialchars($r['item_name'])?></td>
  <td><?=number_format((float)$r['quantity_per_order'],3)?> <?=htmlspecialchars($r['unit'])?></td>
  <td><?=number_format((float)$r['current_quantity'],3)?> <?=htmlspecialchars($r['unit'])?></td>
  <td><?php if(!(int)$r['track_stock']): ?><span class="badge">بدون کنترل</span><?php elseif((float)$r['current_quantity']<(float)$r['quantity_per_order']): ?><span class="badge warning">برای یک سفارش کافی نیست</span><?php else: ?><span class="badge success">قابل سفارش</span><?php endif; ?></td>
  <td><form method="post"><input type="hidden" name="action" value="delete_rule"><input type="hidden" name="id" value="<?=$r['id']?>"><button class="btn btn-sm btn-danger" type="submit">حذف</button></form></td>
 </tr>
 <?php endforeach; ?>
 <?php if(!$rules): ?><tr><td colspan="6"><div class="empty compact"><strong>هنوز فرمول مصرفی ثبت نشده است.</strong><span>با ثبت اولین فرمول، کسر موجودی سفارش‌ها خودکار می‌شود.</span></div></td></tr><?php endif; ?>
 </tbody></table></div>
</section>
