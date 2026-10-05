<section class="page-head"><div><div class="eyebrow">SETTINGS / MENU</div><h1>تعریف انواع / دسته‌بندی محصولات</h1><p>نوع محصول همان دسته‌بندی است؛ رنگ کارت‌ها و ترتیب نمایش از این بخش مدیریت می‌شود.</p></div></section>
<?php if(!empty($message)): ?><div class="alert success"><?=htmlspecialchars($message)?></div><?php endif; ?>
<?php if(!empty($error)): ?><div class="alert"><?=htmlspecialchars($error)?></div><?php endif; ?>
<div class="settings-grid">
<section class="card form-card"><div class="card-head"><div><h2>نوع / دسته‌بندی جدید</h2><small>برای هر دسته یک رنگ اختصاصی انتخاب کنید.</small></div></div>
<form method="post" class="form-grid settings-form"><input type="hidden" name="action" value="create_category">
<label>نام نوع / دسته‌بندی *<input name="name" required placeholder="مثلاً نوشیدنی"></label>
<label>رنگ کارت<input name="color" type="color" value="#2563eb"></label>
<label>ترتیب نمایش<input name="sort_order" type="number" value="0"></label>
<label>وضعیت<select name="status"><option value="active">فعال</option><option value="inactive">غیرفعال</option></select></label>
<div class="form-actions"><button class="btn btn-primary">ثبت دسته‌بندی</button></div></form></section>
<section class="card section-card"><div class="card-head"><div><h2>انواع / انواع / دسته‌بندی‌های فعلی</h2><small>محصولات دارای همین نوع در POS با همین رنگ نمایش داده می‌شوند.</small></div></div>
<div class="table-wrap"><table><thead><tr><th>دسته</th><th>رنگ</th><th>تعداد محصول</th><th>وضعیت</th><th>عملیات</th></tr></thead><tbody>
<?php foreach($rows as $r): ?><tr><td><strong><?=htmlspecialchars($r['name'])?></strong></td><td><span class="color-chip" style="--chip:<?=htmlspecialchars($r['color'])?>"><?=htmlspecialchars($r['color'])?></span></td><td><?=number_format((int)$r['product_count'])?></td><td><?=($r['status']==='active'?'فعال':'غیرفعال')?></td><td class="row-actions">
<form method="post" class="inline-category-edit"><input type="hidden" name="action" value="update_category"><input type="hidden" name="id" value="<?=$r['id']?>"><input name="name" value="<?=htmlspecialchars($r['name'])?>" required><input name="color" type="color" value="<?=htmlspecialchars($r['color'])?>"><input name="sort_order" type="number" value="<?=$r['sort_order']?>" title="ترتیب"><button class="btn btn-sm btn-primary">ذخیره</button></form>
<form method="post" onsubmit="return confirm('این نوع حذف شود؟ محصولات آن بدون نوع باقی می‌مانند.')"><input type="hidden" name="action" value="delete_category"><input type="hidden" name="id" value="<?=$r['id']?>"><button class="btn btn-sm btn-danger">حذف</button></form></td></tr><?php endforeach; ?></tbody></table></div></section></div>
