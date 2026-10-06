<section class="page-head"><div><div class="eyebrow">ACCOUNT / PROFILE</div><h1>پروفایل من</h1><p>اطلاعات حساب، نام نمایشی، موبایل و رمز عبور خود را مدیریت کنید.</p></div></section>
<?php if(!empty($message)): ?><div class="alert success"><?=htmlspecialchars($message)?></div><?php endif; ?>
<?php if(!empty($error)): ?><div class="alert"><?=htmlspecialchars($error)?></div><?php endif; ?>
<section class="card form-card profile-card"><div class="profile-summary"><div class="avatar large"><?=htmlspecialchars(mb_substr($u['display_name']??$u['email']??'م',0,1))?></div><div><strong><?=htmlspecialchars($u['display_name']??'کاربر')?></strong><span><?=htmlspecialchars($u['role_name']??'کاربر')?></span></div></div>
<form method="post" class="form-grid"><input type="hidden" name="action" value="update_profile">
<label>نام کاربری *<input name="username" required value="<?=htmlspecialchars($u['username']??'')?>" autocomplete="username"></label><label>نام نمایشی *<input name="display_name" required value="<?=htmlspecialchars($u['display_name']??'')?>"></label>
<label>ایمیل *<input type="email" name="email" required value="<?=htmlspecialchars($u['email']??'')?>"></label>
<label>موبایل<input name="mobile" inputmode="tel" value="<?=htmlspecialchars($u['mobile']??'')?>"></label>
<label>رمز عبور جدید<input type="password" name="password" minlength="8" autocomplete="new-password" placeholder="در صورت نیاز، حداقل ۸ کاراکتر"></label>
<div class="form-actions"><button class="btn btn-primary">ذخیره پروفایل</button></div></form></section>