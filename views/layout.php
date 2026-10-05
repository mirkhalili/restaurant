<?php
$activePage = $page ?? ($_GET['page'] ?? 'dashboard');
$navGroups = [
  ['title'=>'عملیات','items'=>[
    ['key'=>'dashboard','label'=>'داشبورد','icon'=>'⌂','url'=>'/'],
    ['key'=>'orders','label'=>'سفارش‌ها','icon'=>'▤','url'=>'/?page=orders'],
    ['key'=>'customers','label'=>'مشتریان','icon'=>'♙','url'=>'/?page=customers'],
  ]],
  ['title'=>'مدیریت','items'=>[
    ['key'=>'products','label'=>'منو و محصولات','icon'=>'◈','url'=>'/?page=products'],
    ['key'=>'inventory','label'=>'انبار و خرید','icon'=>'▥','url'=>'/?page=inventory'],
    ['key'=>'kitchen','label'=>'آشپزخانه','icon'=>'◉','url'=>'/?page=kitchen'],
  ]],
  ['title'=>'تحلیل و سیستم','items'=>[
    ['key'=>'reports','label'=>'گزارش‌ها','icon'=>'◫','url'=>'/?page=reports'],
    ['key'=>'users','label'=>'کاربران و دسترسی‌ها','icon'=>'♟','url'=>'/?page=users'],
    ['key'=>'settings','label'=>'تنظیمات','icon'=>'⚙','url'=>'/?page=settings'],
    ['key'=>'audit','label'=>'Audit Log','icon'=>'◌','url'=>'/?page=audit'],
  ]]
];
?>
<!doctype html>
<html lang="fa" dir="rtl" data-theme="light">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=htmlspecialchars($title??'رستوران')?></title>
<meta name="color-scheme" content="light dark">
<link rel="stylesheet" href="/assets/style.css">
</head>
<body class="app-body">
<div class="app-shell">
  <div class="sidebar-backdrop" data-sidebar-backdrop></div>
  <aside class="sidebar" data-sidebar aria-label="منوی اصلی">
    <div class="brand">
      <div class="brand-mark">🍽</div>
      <div class="brand-copy"><strong>رستوران</strong><small>اتوماسیون مدیریت</small></div>
      <button class="icon-btn sidebar-collapse" data-sidebar-collapse aria-label="جمع کردن منو">‹</button>
    </div>
    <div class="branch-switcher">
      <span class="branch-icon">⌂</span><div><small>شعبه فعال</small><strong>شعبه مرکزی</strong></div><span>⌄</span>
    </div>
    <nav class="nav-groups">
      <?php foreach($navGroups as $group): ?>
        <div class="nav-group">
          <div class="nav-title"><?=htmlspecialchars($group['title'])?></div>
          <?php foreach($group['items'] as $item): ?>
            <a class="nav-item <?=($activePage===$item['key']?'active':'')?>" href="<?=htmlspecialchars($item['url'])?>" title="<?=htmlspecialchars($item['label'])?>">
              <span class="nav-icon"><?=$item['icon']?></span><span class="nav-label"><?=htmlspecialchars($item['label'])?></span>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>
    </nav>
    <div class="sidebar-footer">
      <div class="mini-help"><span>?</span><div><strong>راهنما</strong><small>راهنمای سامانه</small></div></div>
      <a class="nav-item logout" href="/?page=logout"><span class="nav-icon">↪</span><span class="nav-label">خروج از حساب</span></a>
    </div>
  </aside>

  <section class="main-shell">
    <header class="topbar">
      <div class="topbar-start">
        <button class="icon-btn mobile-menu" data-sidebar-open aria-label="باز کردن منو">☰</button>
        <div class="breadcrumb">
          <span>اتوماسیون</span><b>/</b><strong><?=htmlspecialchars($title??'داشبورد')?></strong>
        </div>
      </div>
      <div class="topbar-actions">
        <label class="global-search"><span>⌕</span><input data-global-search type="search" placeholder="جستجوی سریع..."><kbd>Ctrl K</kbd></label>
        <button class="icon-btn" data-theme-toggle aria-label="تغییر پوسته">☾</button>
        <button class="icon-btn notification" aria-label="اعلان‌ها">♢<i></i></button>
        <a class="user-menu user-menu-link" href="/?page=profile" title="پروفایل من">
          <div class="avatar"><?=htmlspecialchars(mb_substr($user['display_name']??$user['email']??'م',0,1))?></div>
          <div class="user-info"><strong><?=htmlspecialchars($user['display_name']??$user['email']??'کاربر')?></strong><small><?=htmlspecialchars($user['role_name']??'کاربر')?></small></div>
        </a>
      </div>
    </header>
    <main class="page-content"><?=$content?></main>
  </section>
</div>
<script src="/assets/app.js"></script>
</body>
</html>