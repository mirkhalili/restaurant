<section class="page-head"><div><div class="eyebrow">ACCESS / USERS</div><h1>کاربران و دسترسی‌ها</h1><p>تعریف کاربران، تعیین نقش، وضعیت حساب و مدیریت دسترسی عملیاتی.</p></div><div class="head-actions"><a class="btn btn-primary" href="/?page=users&new=1">＋ کاربر جدید</a></div></section>
<?php if(!empty($message)): ?><div class="alert success"><?=htmlspecialchars($message)?></div><?php endif; ?>
<?php if(!empty($error)): ?><div class="alert"><?=htmlspecialchars($error)?></div><?php endif; ?>
<?php if(isset($_GET['new']) || $edit): $r=$edit?:[]; ?>
<form method="post" class="card form-card"><input type="hidden" name="action" value="<?=$edit?'update_user':'create_user'?>"><?php if($edit): ?><input type="hidden" name="id" value="<?=$r['id']?>"><?php endif; ?>
<div class="form-grid">
<label>نام نمایشی *<input name="display_name" required value="<?=htmlspecialchars($r['display_name']??'')?>"></label>
<label>ایمیل *<input type="email" name="email" required value="<?=htmlspecialchars($r['email']??'')?>"></label>
<label>شماره موبایل<input name="mobile" inputmode="tel" value="<?=htmlspecialchars($r['mobile']??'')?>"></label>
<label>نقش *<select name="role_id" required><?php foreach($roles as $role): ?><option value="<?=$role['id']?>" <?=((int)($r['role_id']??0)===(int)$role['id'])?'selected':''?>><?=htmlspecialchars($role['name'])?></option><?php endforeach; ?></select></label>
<label>رمز عبور <?=$edit?'جدید':''?><input type="password" name="password" minlength="8" <?=$edit?'':'required'?> autocomplete="new-password" placeholder="<?=$edit?'برای عدم تغییر خالی بگذارید':''?>"></label>
<?php if($edit): ?><label>وضعیت<select name="status"><option value="active" <?=($r['status']??'active')==='active'?'selected':''?>>فعال</option><option value="inactive" <?=($r['status']??'')==='inactive'?'selected':''?>>غیرفعال</option></select></label><?php endif; ?>
<div class="form-actions"><button class="btn btn-primary"><?=$edit?'ذخیره تغییرات':'ایجاد کاربر'?></button><a class="btn" href="/?page=users">انصراف</a></div>
</div></form>
<?php endif; ?>
<section class="card section-card"><div class="card-head"><div><h2>فهرست کاربران</h2><small>مدیر می‌تواند نقش و وضعیت کاربران را مدیریت کند.</small></div></div>
<div class="table-wrap"><table><thead><tr><th>نام</th><th>ایمیل</th><th>نقش</th><th>موبایل</th><th>وضعیت</th><th>عملیات</th></tr></thead><tbody>
<?php if(!$rows): ?><tr><td colspan="6"><div class="empty"><strong>کاربری پیدا نشد</strong></div></td></tr>
<?php else: foreach($rows as $r): ?><tr><td><strong><?=htmlspecialchars($r['display_name']??$r['email'])?></strong></td><td><?=htmlspecialchars($r['email'])?></td><td><?=htmlspecialchars($r['role_name'])?></td><td><?=htmlspecialchars($r['mobile']??'—')?></td><td><span class="badge <?=($r['status']==='active'?'success':'danger')?>"><?=($r['status']==='active'?'فعال':'غیرفعال')?></span></td><td class="row-actions"><a class="btn btn-sm" href="/?page=users&edit=<?=$r['id']?>">ویرایش</a><?php if((int)$r['id']!==(int)($user['id']??0)): ?><form method="post"><input type="hidden" name="action" value="toggle_user"><input type="hidden" name="id" value="<?=$r['id']?>"><button class="btn btn-sm"><?=($r['status']==='active'?'غیرفعال کردن':'فعال کردن')?></button></form><?php endif; ?></td></tr><?php endforeach; endif; ?></tbody></table></div></section>