<section class="page-head"><div><div class="eyebrow">SYSTEM / AUDIT</div><h1>رویدادنگاری</h1><p>تمام عملیات مهم سامانه با کاربر، زمان، IP و موجودیت ثبت می‌شود.</p></div></section>
<form class="card toolbar" method="get">
  <input type="hidden" name="page" value="audit">
  <div class="filters">
    <label>از تاریخ شمسی <input class="field" type="text" name="from" value="<?=htmlspecialchars($fromJ)?>" placeholder="۱۴۰۵/۰۷/۱۴" inputmode="numeric"></label>
    <label>تا تاریخ شمسی <input class="field" type="text" name="to" value="<?=htmlspecialchars($toJ)?>" placeholder="۱۴۰۵/۰۷/۱۴" inputmode="numeric"></label>
    <label>عملیات <select class="field" name="action"><option value="">همه</option><?php foreach($actions as $a): ?><option value="<?=htmlspecialchars($a)?>" <?=$action===$a?'selected':''?>><?=htmlspecialchars($a)?></option><?php endforeach; ?></select></label>
    <button class="btn btn-primary">فیلتر</button>
  </div>
</form>
<section class="card section-card">
  <div class="table-wrap"><table><thead><tr><th>زمان شمسی</th><th>کاربر</th><th>عملیات</th><th>موجودیت</th><th>شناسه</th><th>IP</th><th>جزئیات</th></tr></thead>
  <tbody>
  <?php foreach($rows as $r): ?>
    <tr>
      <td><?=htmlspecialchars(App\Support\PersianDate::format($r['created_at']))?></td>
      <td><?=htmlspecialchars($r['user_name'])?></td>
      <td><span class="badge info"><?=htmlspecialchars($r['action'])?></span></td>
      <td><?=htmlspecialchars($r['entity_type']??'—')?></td>
      <td><?=htmlspecialchars((string)($r['entity_id']??'—'))?></td>
      <td><?=htmlspecialchars($r['ip_address']??'—')?></td>
      <td>
        <details class="audit-details">
          <summary>مشاهده</summary>
          <pre class="audit-json"><?=htmlspecialchars(json_encode(['قبل'=>json_decode((string)$r['before_data'],true),'بعد'=>json_decode((string)$r['after_data'],true),'علت'=>$r['reason']],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT))?></pre>
        </details>
      </td>
    </tr>
  <?php endforeach; ?>
  <?php if(!$rows): ?><tr><td colspan="7"><div class="empty">رویدادی در این بازه ثبت نشده است.</div></td></tr><?php endif; ?>
  </tbody></table></div>
</section>
