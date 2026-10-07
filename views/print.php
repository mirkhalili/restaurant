<!doctype html>
<html lang="fa" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>چاپ فیش <?=htmlspecialchars((string)($invoice['ticket_no']??$invoice['invoice_no']))?></title>
<link rel="stylesheet" href="/assets/style.css">
<style>
.print-page{background:#f5f6f8}.print-toolbar{display:flex;align-items:center;gap:8px;flex-wrap:wrap;padding:14px 18px}.print-toolbar>div{margin-left:auto;display:flex;flex-direction:column;gap:3px}.print-toolbar small{opacity:.7}
.receipt{width:var(--receipt-width,80mm);margin:12px auto;background:#fff;padding:5mm;box-shadow:0 8px 30px rgba(0,0,0,.08);font-family:inherit}.receipt h1{font-size:20px;margin:0}.receipt-line{display:flex;justify-content:space-between;gap:8px;margin:5px 0}.receipt-order-type{text-align:center;justify-content:center}.receipt-order-type strong{font-size:18px;border:1px solid #222;border-radius:7px;padding:4px 16px}.receipt-item{display:flex;justify-content:space-between;gap:8px;margin:6px 0}.receipt-item>div{display:flex;flex-direction:column}.receipt-item small{font-size:10px}.receipt-summary{margin-top:7px}.receipt-summary>div{display:flex;justify-content:space-between;margin:4px 0}.receipt-discount{font-weight:700}.receipt-total{display:flex;justify-content:space-between;font-size:15px;font-weight:800;border-top:1px dashed #555;padding-top:7px;margin-top:8px}.receipt footer{text-align:center;border-top:1px dashed #777;margin-top:10px;padding-top:7px;font-size:10px}
.kitchen-slip{width:var(--receipt-width,80mm);margin:12px auto;background:#fff;padding:4mm;box-shadow:0 8px 30px rgba(0,0,0,.08);font-family:Vazirmatn,Tahoma,sans-serif;color:#111}.kitchen-header{display:grid;grid-template-columns:24% 52% 24%;align-items:center;border-bottom:2px solid #111;padding-bottom:6px}.kitchen-chef{text-align:center;font-size:25px}.kitchen-title{text-align:center;font-size:14px;font-weight:600;line-height:1.1}.kitchen-ticket{text-align:center;font-size:11px;font-weight:700}.kitchen-ticket b{display:block;font-size:25px;line-height:1.05;margin-top:2px}.kitchen-order-type{text-align:center;font-size:29px;font-weight:900;border:2px solid #111;border-radius:9px;padding:4px 12px;margin:7px auto;width:max-content;max-width:100%}.kitchen-meta{display:flex;justify-content:center;gap:8px;flex-wrap:wrap;font-size:10px;font-weight:700;margin-bottom:7px}.kitchen-table{width:100%;border-collapse:collapse;table-layout:fixed}.kitchen-table th,.kitchen-table td{border:1px solid #222;text-align:center;padding:5px 4px;overflow:hidden;vertical-align:middle;word-break:break-word}.kitchen-table th{font-size:14px;font-weight:800;white-space:normal}.kitchen-table th:nth-child(1),.kitchen-table td:nth-child(1){width:25%}.kitchen-table th:nth-child(2),.kitchen-table td:nth-child(2){width:55%}.kitchen-table th:nth-child(3),.kitchen-table td:nth-child(3){width:20%}.kitchen-group{font-size:16px;font-weight:900;vertical-align:middle;line-height:1.4;overflow:hidden}.kitchen-group-icon{display:block;font-size:23px;margin-bottom:2px}.kitchen-product{font-size:15px;font-weight:400;text-align:right!important;padding-right:8px!important}.kitchen-group-start td{border-top:3px solid #111}.kitchen-group-start .kitchen-group{border-top:3px solid #111}.kitchen-group-label{display:block;font-size:17px;font-weight:900;border-bottom:2px solid #222;padding-bottom:3px;margin-bottom:4px}.kitchen-qty{font-size:23px;font-weight:900}.kitchen-date{text-align:center;font-size:10px;font-weight:700;margin:5px 0}.kitchen-footer{text-align:center;border-top:2px solid #111;margin-top:7px;padding-top:5px;font-size:9px;font-weight:700}
@media print{body.print-page{background:#fff}.no-print{display:none!important}.receipt{margin:0;box-shadow:none}.kitchen-slip{margin:0;box-shadow:none}.print-target-customer #kitchen-preview{display:none!important}.print-target-kitchen #customer-receipt{display:none!important}.print-target-kitchen #kitchen-preview{page-break-before:auto}.print-target-both #kitchen-preview{page-break-before:always}}
</style>
</head>
<body class="print-page">
<div class="print-toolbar no-print">
  <div><strong>چاپ فیش</strong><small id="print-status">چاپ مستقیم فیش مشتری و آشپزخانه</small></div>
  <button type="button" class="btn btn-primary" id="print-customer-btn">چاپ فیش مشتری</button>
  <button type="button" class="btn" id="print-kitchen-btn">چاپ فیش آشپزخانه</button>
  <button type="button" class="btn" id="print-both-btn">چاپ هر دو</button>
  <button type="button" class="btn" id="browser-print-btn">پیش‌نمایش هر دو</button>
  <a class="btn" href="/?page=invoice&id=<?=$invoice['id']?>">ویرایش فاکتور</a>
  <a class="btn" href="/?page=orders">ثبت سفارش جدید</a>
</div>

<main class="receipt" id="customer-receipt" style="--receipt-width:<?=htmlspecialchars($settings['paper_width']??'80mm')?>">
  <header>
    <?php if(($settings['show_logo']??'1')==='1'): ?><div class="receipt-logo"><?php if(!empty($restaurant['logo_path'])): ?><img src="<?=htmlspecialchars($restaurant['logo_path'])?>" alt="لوگو"><?php else: ?>🍽️<?php endif; ?></div><?php endif; ?>
    <h1><?=htmlspecialchars($restaurant['name']??'رستوران')?></h1>
    <?php if(($settings['show_address']??'1')==='1' && !empty($restaurant['address'])): ?><small><?=htmlspecialchars($restaurant['address'])?></small><?php endif; ?>
    <?php if(($settings['show_phone']??'1')==='1' && !empty($restaurant['phone'])): ?><small><?=htmlspecialchars($restaurant['phone'])?></small><?php endif; ?>
  </header>
  <hr>
  <div class="receipt-line"><span>شماره فیش</span><b><?=htmlspecialchars((string)($invoice['ticket_no']??'—'))?></b></div>
  <div class="receipt-line"><span>تاریخ و ساعت</span><span><?=htmlspecialchars(App\Support\PersianDate::format($invoice['created_at']))?></span></div>
  <div class="receipt-line receipt-order-type"><strong><?=htmlspecialchars($invoice['order_type']??'سالن')?></strong></div>
  <?php if(($settings['show_customer']??'1')==='1'): ?><div class="receipt-line"><span>مشتری</span><span><?=htmlspecialchars($invoice['customer_name'])?></span></div><?php endif; ?>
  <hr>
  <?php foreach($items as $it): ?>
    <div class="receipt-item"><div><b><?=htmlspecialchars($it['name'])?></b><small><?=number_format($it['unit_price'])?> × <?=htmlspecialchars((string)(int)$it['quantity'])?></small></div><strong><?=number_format($it['line_total'])?></strong></div>
  <?php endforeach; ?>
  <hr>
  <div class="receipt-summary">
    <div><span>جمع سفارش</span><strong><?=number_format((float)($invoice['subtotal_amount']??$invoice['total_amount']))?> ریال</strong></div>
    <div class="receipt-discount"><span>تخفیف (<?=number_format((float)($invoice['discount_percent']??0),2)?>٪)</span><strong><?=number_format((float)($invoice['discount_amount']??0))?> ریال</strong></div>
  </div>
  <div class="receipt-total"><span>مبلغ نهایی</span><strong><?=number_format((float)$invoice['total_amount'])?> ریال</strong></div>
  <?php if(($settings['show_payment']??'1')==='1'): ?><div class="receipt-line"><span>پرداخت</span><span><?=htmlspecialchars(['cash'=>'نقدی','card'=>'کارتخوان','online'=>'آنلاین','mixed'=>'ترکیبی'][$invoice['payment_method']]??'—')?></span></div><?php endif; ?>
  <?php if(($settings['show_footer']??'1')==='1'): ?><footer><?=nl2br(htmlspecialchars($settings['footer_text']??''))?></footer><?php endif; ?>
</main>

<section class="kitchen-slip" id="kitchen-preview" style="--receipt-width:<?=htmlspecialchars($settings['paper_width']??'80mm')?>">
  <header class="kitchen-header">
    <div class="kitchen-chef" aria-hidden="true">👨‍🍳</div>
    <div class="kitchen-title">آشپزخانه</div>
    <div class="kitchen-ticket">شماره فیش:<b><?=htmlspecialchars((string)($invoice['ticket_no']??'—'))?></b></div>
  </header>
  <div class="kitchen-order-type"><?=htmlspecialchars($invoice['order_type']??'سالن')?></div>
  <div class="kitchen-meta"><?php if(($settings['kitchen_show_customer']??'1')==='1'): ?><span>مشتری: <?=htmlspecialchars($invoice['customer_name'])?></span><?php endif; ?></div>
  <?php if(($settings['kitchen_show_date']??'1')==='1'): ?><div class="kitchen-date">تاریخ و ساعت: <?=htmlspecialchars(App\Support\PersianDate::format($invoice['created_at']))?></div><?php endif; ?>
  <table class="kitchen-table"><thead><tr><th>نوع سفارش</th><th>سفارش</th><th>تعداد</th></tr></thead><tbody id="kitchen-groups"></tbody></table>
  <div class="kitchen-footer"><?=htmlspecialchars($settings['kitchen_footer']??'مخصوص آشپزخانه — بدون قیمت')?></div>
</section>

<script>
const invoiceId=<?=json_encode((int)$invoice['id'])?>;
const invoiceNo=<?=json_encode((string)$invoice['invoice_no'],JSON_UNESCAPED_UNICODE)?>;
const ticketNo=<?=json_encode((string)($invoice['ticket_no']??''),JSON_UNESCAPED_UNICODE)?>;
const restaurantName=<?=json_encode((string)($restaurant['name']??'رستوران'),JSON_UNESCAPED_UNICODE)?>;
const orderType=<?=json_encode((string)($invoice['order_type']??'سالن'),JSON_UNESCAPED_UNICODE)?>;
const customerName=<?=json_encode((string)($invoice['customer_name']??'مشتری'),JSON_UNESCAPED_UNICODE)?>;
const createdAt=<?=json_encode(App\Support\PersianDate::format($invoice['created_at']),JSON_UNESCAPED_UNICODE)?>;
const subtotal=<?=json_encode((float)($invoice['subtotal_amount']??$invoice['total_amount']))?>;
const discountPercent=<?=json_encode((float)($invoice['discount_percent']??0))?>;
const discountAmount=<?=json_encode((float)($invoice['discount_amount']??0))?>;
const total=<?=json_encode((float)$invoice['total_amount'])?>;
const paymentMethod=<?=json_encode(['cash'=>'نقدی','card'=>'کارتخوان','online'=>'آنلاین','mixed'=>'ترکیبی'][$invoice['payment_method']]??'—',JSON_UNESCAPED_UNICODE)?>;
const items=<?=json_encode($items,JSON_UNESCAPED_UNICODE|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT)?>;
function esc(v){return String(v??'').replace(/[&<>"']/g,m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));}
function groups(){const map=new Map();items.forEach(it=>{const key=(it.product_type||'سایر').trim()||'سایر';if(!map.has(key))map.set(key,[]);map.get(key).push(it);});return [...map.entries()];}
function kitchenRows(){return groups().map(([type,list])=>list.map((it,index)=>'<tr class="'+(index===0?'kitchen-group-start':'')+'">'+(index===0?'<td class="kitchen-group" rowspan="'+list.length+'"><span class="kitchen-group-icon">'+esc(it.category_icon||'•')+'</span>'+esc(type)+'</td>':'')+'<td class="kitchen-product">'+(index===0?'<span class="kitchen-group-label">'+esc(type)+'</span>':'')+'<span>'+esc(it.name)+'</span></td><td class="kitchen-qty">'+Math.trunc(Number(it.quantity||0)).toLocaleString('fa-IR')+'</td></tr>').join('')).join('');}
function setStatus(v,error=false){const el=document.getElementById('print-status');el.textContent=v;el.classList.toggle('print-error',error);}
async function markPrinted(printerId,kind){const fd=new FormData();fd.append('action','mark_printed');fd.append('invoice_id',String(invoiceId));fd.append('printer_id',String(printerId||0));fd.append('print_kind',kind);try{await fetch('/?page=print&invoice='+encodeURIComponent(invoiceNo),{method:'POST',body:fd,credentials:'same-origin'});}catch(e){}}
function printTarget(target){
 const body=document.body;
 body.classList.remove('print-target-customer','print-target-kitchen','print-target-both');
 body.classList.add('print-target-'+target);
 setStatus(target==='customer'?'آماده چاپ فیش مشتری…':target==='kitchen'?'آماده چاپ فیش آشپزخانه…':'آماده چاپ هر دو فیش…');
 window.print();
}
window.addEventListener('afterprint',()=>{
 document.body.classList.remove('print-target-customer','print-target-kitchen');
 document.body.classList.add('print-target-both');
 setStatus('چاپ مرورگر انجام شد.');
 markPrinted(0,'customer');
 markPrinted(0,'kitchen');
});
document.getElementById('print-customer-btn').addEventListener('click',()=>printTarget('customer'));
document.getElementById('print-kitchen-btn').addEventListener('click',()=>printTarget('kitchen'));
document.getElementById('print-both-btn').addEventListener('click',()=>printTarget('both'));
document.getElementById('browser-print-btn').addEventListener('click',()=>printTarget('both'));
document.getElementById('kitchen-groups').innerHTML=kitchenRows();
</script>
</body>
</html>
