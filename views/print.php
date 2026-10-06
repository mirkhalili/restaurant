<!doctype html>
<html lang="fa" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>چاپ فاکتور <?=htmlspecialchars($invoice['invoice_no'])?></title>
<link rel="stylesheet" href="/assets/style.css">
<script src="https://cdn.jsdelivr.net/npm/qz-tray@2.3.0/qz-tray.js"></script>
</head>
<body class="print-page">
<div class="print-toolbar no-print">
  <div><strong>چاپ فاکتور</strong><small id="print-status">چاپ مستقیم مشتری و آشپزخانه با چاپگر پیش‌فرض</small></div>
  <button type="button" class="btn btn-primary" id="direct-print-btn">چاپ فاکتور</button>
  <button type="button" class="btn" id="browser-print-btn">پیش‌نمایش مرورگر</button>
  <a class="btn" href="/?page=invoice&id=<?=$invoice['id']?>">ویرایش فاکتور</a>
  <a class="btn" href="/?page=orders">ثبت سفارش</a>
</div>

<main class="receipt" id="customer-receipt" style="--receipt-width:<?=htmlspecialchars($settings['paper_width']??'80mm')?>">
  <header>
    <?php if(($settings['show_logo']??'1')==='1'): ?><div class="receipt-logo"><?php if(!empty($restaurant['logo_path'])): ?><img src="<?=htmlspecialchars($restaurant['logo_path'])?>" alt="لوگو"><?php else: ?>🍽️<?php endif; ?></div><?php endif; ?>
    <h1><?=htmlspecialchars($restaurant['name']??'رستوران')?></h1>
    <?php if(($settings['show_address']??'1')==='1' && !empty($restaurant['address'])): ?><small><?=htmlspecialchars($restaurant['address'])?></small><?php endif; ?>
    <?php if(($settings['show_phone']??'1')==='1' && !empty($restaurant['phone'])): ?><small><?=htmlspecialchars($restaurant['phone'])?></small><?php endif; ?>
  </header>
  <hr>
  <div class="receipt-line"><span>فاکتور</span><b><?=htmlspecialchars($invoice['invoice_no'])?></b></div>
  <div class="receipt-line"><span>تاریخ</span><span><?=htmlspecialchars(App\Support\PersianDate::format($invoice['created_at']))?></span></div>
  <div class="receipt-line receipt-order-type"><span>سفارش</span><strong><?=($settings['kitchen_show_order_type']??'1')==='1'?htmlspecialchars($invoice['order_type']??'سالن'):''?></strong></div>
  <?php if(($settings['show_customer']??'1')==='1'): ?><div class="receipt-line"><span>مشتری</span><span><?=htmlspecialchars($invoice['customer_name'])?></span></div><?php endif; ?>
  <hr>
  <?php foreach($items as $it): ?>
    <div class="receipt-item"><div><b><?=htmlspecialchars($it['name'])?></b><small><?=number_format($it['unit_price'])?> × <?=htmlspecialchars((string)(int)$it['quantity'])?></small></div><strong><?=number_format($it['line_total'])?></strong></div>
  <?php endforeach; ?>
  <hr>
  <div class="receipt-total"><span>مبلغ نهایی</span><strong><?=number_format($invoice['total_amount'])?> ریال</strong></div>
  <?php if(($settings['show_payment']??'1')==='1'): ?><div class="receipt-line"><span>پرداخت</span><span><?=htmlspecialchars(['cash'=>'نقدی','card'=>'کارتخوان','online'=>'آنلاین','mixed'=>'ترکیبی'][$invoice['payment_method']]??'—')?></span></div><?php endif; ?>
  <?php if(($settings['show_footer']??'1')==='1'): ?><footer><?=nl2br(htmlspecialchars($settings['footer_text']??''))?></footer><?php endif; ?>
</main>

<div id="kitchen-preview" class="kitchen-slip no-print">
  <header><strong><?=htmlspecialchars($settings['kitchen_title']??'آشپزخانه کوچک')?></strong><div class="kitchen-order-type"><?=htmlspecialchars($invoice['order_type']??'سالن')?></div><small><?=($settings['kitchen_show_invoice_no']??'1')==='1'?htmlspecialchars($invoice['invoice_no']):''?></small></header>
  <div id="kitchen-groups"></div>
</div>

<script>
const invoiceId=<?=json_encode((int)$invoice['id'])?>;
const invoiceNo=<?=json_encode((string)$invoice['invoice_no'],JSON_UNESCAPED_UNICODE)?>;
const restaurantName=<?=json_encode((string)($restaurant['name']??'رستوران'),JSON_UNESCAPED_UNICODE)?>;
const orderType=<?=json_encode((string)($invoice['order_type']??'سالن'),JSON_UNESCAPED_UNICODE)?>;
const customerName=<?=json_encode((string)($invoice['customer_name']??'مشتری'),JSON_UNESCAPED_UNICODE)?>;
const createdAt=<?=json_encode(App\Support\PersianDate::format($invoice['created_at']),JSON_UNESCAPED_UNICODE)?>;
const total=<?=json_encode(number_format((float)$invoice['total_amount']).' ریال',JSON_UNESCAPED_UNICODE)?>;
const paymentMethod=<?=json_encode(['cash'=>'نقدی','card'=>'کارتخوان','online'=>'آنلاین','mixed'=>'ترکیبی'][$invoice['payment_method']]??'—',JSON_UNESCAPED_UNICODE)?>;
const items=<?=json_encode($items,JSON_UNESCAPED_UNICODE|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT)?>;
const kitchenPrinterHint=<?=json_encode((string)($kitchenPrinters[0]['name']??''),JSON_UNESCAPED_UNICODE)?>;
const paperWidth=<?=json_encode((string)($settings['paper_width']??'80mm'))?>;
const kitchenSettings=<?=json_encode(['title'=>$settings['kitchen_title']??'آشپزخانه کوچک','show_restaurant'=>($settings['kitchen_show_restaurant']??'1')==='1','show_order_type'=>($settings['kitchen_show_order_type']??'1')==='1','show_customer'=>($settings['kitchen_show_customer']??'1')==='1','show_invoice_no'=>($settings['kitchen_show_invoice_no']??'1')==='1','show_date'=>($settings['kitchen_show_date']??'0')==='1','show_product_type'=>($settings['kitchen_show_product_type']??'1')==='1','footer'=>$settings['kitchen_footer']??'مخصوص آشپزخانه — بدون قیمت'],JSON_UNESCAPED_UNICODE)?>;

function esc(v){return String(v??'').replace(/[&<>"']/g,m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));}
function groups(){
  const map=new Map();
  items.forEach(it=>{const key=(it.product_type||'سایر').trim()||'سایر';if(!map.has(key))map.set(key,[]);map.get(key).push(it);});
  return [...map.entries()];
}
function kitchenHtml(){
  const header=[];
  if(kitchenSettings.show_restaurant) header.push('<strong>'+esc(restaurantName)+'</strong>');
  header.push('<div class="kitchen-title">'+esc(kitchenSettings.title)+'</div>');
  if(kitchenSettings.show_order_type) header.push('<div class="kitchen-order-type">'+esc(orderType)+'</div>');
  const meta=[];
  if(kitchenSettings.show_invoice_no) meta.push('فاکتور '+esc(invoiceNo));
  if(kitchenSettings.show_customer) meta.push('مشتری: '+esc(customerName));
  if(kitchenSettings.show_date) meta.push('تاریخ: '+esc(createdAt));
  const metaHtml=meta.length?'<div class="kitchen-meta">'+meta.join(' · ')+'</div>':'';
  const grouped=groups().map(([type,list])=>{
    const title=kitchenSettings.show_product_type?'<div class="group-title">'+esc(list[0]?.category_icon||'•')+' '+esc(type)+'</div>':'';
    return '<section class="group">'+title+list.map(it=>'<div class="item"><b>'+esc(it.name)+'</b><strong>'+Math.trunc(Number(it.quantity||0)).toLocaleString('fa-IR')+'</strong></div>').join('')+'</section>';
  }).join('');
  return '<!doctype html><html lang="fa" dir="rtl"><head><meta charset="utf-8"><style>@import url(https://fonts.googleapis.com/css2?family=Vazirmatn:wght@500;700;800);*{box-sizing:border-box}body{font-family:Vazirmatn,Tahoma,sans-serif;width:100%;margin:0;padding:4mm;color:#111;font-size:14px}header{text-align:center;border-bottom:2px solid #111;padding-bottom:5px;margin-bottom:8px}header strong{display:block;font-size:18px}.kitchen-title{font-size:20px;font-weight:800;margin:3px 0}.kitchen-order-type{font-size:28px;font-weight:800;margin:5px 0}.kitchen-meta{font-size:12px;line-height:1.8}.group{margin:0 0 10px}.group-title{font-size:13px;font-weight:800;border-top:1px dashed #555;border-bottom:1px dashed #555;padding:4px 0;margin-bottom:5px}.item{display:flex;justify-content:space-between;gap:8px;align-items:center;padding:4px 0}.item b{font-size:17px}.item strong{font-size:22px;line-height:1}.footer{border-top:2px solid #111;margin-top:8px;padding-top:5px;text-align:center;font-size:11px}</style></head><body><header>'+header.join('')+metaHtml+'</header>'+grouped+'<div class="footer">'+esc(kitchenSettings.footer)+'</div></body></html>';
}
function customerHtml(){
  return '<!doctype html><html lang="fa" dir="rtl"><head><meta charset="utf-8"><style>@import url(https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;600;700;800);*{box-sizing:border-box}body{font-family:Vazirmatn,Tahoma,sans-serif;width:100%;margin:0;padding:4mm;color:#111;font-size:11px}header{text-align:center}h1{font-size:18px;margin:0 0 3px}small{color:#555}.line{display:flex;justify-content:space-between;gap:8px;margin:4px 0}.type{text-align:center;font-size:17px;font-weight:800;border:1px solid #222;border-radius:6px;padding:5px;margin:6px 0}.item{display:flex;justify-content:space-between;gap:7px;margin:6px 0}.item div{display:flex;flex-direction:column}.item small{font-size:9px}.total{display:flex;justify-content:space-between;font-size:14px;font-weight:800;border-top:1px dashed #555;padding-top:7px;margin-top:8px}hr{border:0;border-top:1px dashed #777;margin:8px 0}footer{text-align:center;border-top:1px dashed #777;margin-top:9px;padding-top:6px;font-size:9px}</style></head><body><header><h1>'+esc(restaurantName)+'</h1><small>فاکتور '+esc(invoiceNo)+'</small></header><hr><div class="line"><span>تاریخ</span><span>'+esc(createdAt)+'</span></div><div class="type">'+esc(orderType)+'</div><div class="line"><span>مشتری</span><span>'+esc(customerName)+'</span></div><hr>'+items.map(it=>'<div class="item"><div><b>'+esc(it.name)+'</b><small>'+Number(it.unit_price||0).toLocaleString('fa-IR')+' × '+Math.trunc(Number(it.quantity||0)).toLocaleString('fa-IR')+'</small></div><strong>'+Math.round(Number(it.line_total||0)).toLocaleString('fa-IR')+'</strong></div>').join('')+'<hr><div class="total"><span>مبلغ نهایی</span><strong>'+esc(total)+'</strong></div><div class="line"><span>پرداخت</span><span>'+esc(paymentMethod)+'</span></div></body></html>';
}
function setStatus(v,error=false){const el=document.getElementById('print-status');el.textContent=v;el.classList.toggle('print-error',error);}
async function markPrinted(printerId,kind){const fd=new FormData();fd.append('action','mark_printed');fd.append('invoice_id',String(invoiceId));fd.append('printer_id',String(printerId||0));fd.append('print_kind',kind);try{await fetch('/?page=print&invoice='+encodeURIComponent(invoiceNo),{method:'POST',body:fd,credentials:'same-origin'});}catch(e){}}
async function resolvePrinter(hint){
  if(hint){const found=await qz.printers.find(hint);if(found)return found;}
  return qz.printers.getDefault();
}
async function directPrint(){
  if(typeof qz==='undefined'){setStatus('QZ Tray نصب یا در حال اجرا نیست؛ چاپ مستقیم فعال نمی‌شود.',true);return;}
  const btn=document.getElementById('direct-print-btn');btn.disabled=true;setStatus('در حال اتصال به سرویس چاپ…');
  try{
    if(!qz.websocket.isActive()) await qz.websocket.connect({retries:3,delay:1});
    const customerPrinter=await qz.printers.getDefault();
    if(!customerPrinter) throw new Error('چاپگر پیش‌فرض سیستم پیدا نشد.');
    const kitchenPrinter=await resolvePrinter(kitchenPrinterHint);
    const cfgCustomer=qz.configs.create(customerPrinter,{margins:0,scaleContent:true,jobName:'فاکتور '+invoiceNo});
    const cfgKitchen=qz.configs.create(kitchenPrinter,{margins:0,scaleContent:true,jobName:'آشپزخانه '+invoiceNo});
    const customerData=[{type:'pixel',format:'html',flavor:'plain',data:customerHtml()}];
    const kitchenData=[{type:'pixel',format:'html',flavor:'plain',data:kitchenHtml()}];
    setStatus('در حال چاپ فاکتور مشتری…');await qz.print(cfgCustomer,customerData);await markPrinted(0,'customer');
    setStatus('در حال چاپ فیش آشپزخانه…');await qz.print(cfgKitchen,kitchenData);await markPrinted(0,'kitchen');
    setStatus('هر دو چاپ با موفقیت ارسال شد.');
  }catch(e){setStatus('چاپ مستقیم انجام نشد: '+(e?.message||e),true);}
  finally{btn.disabled=false;}
}
document.getElementById('direct-print-btn').addEventListener('click',directPrint);
document.getElementById('browser-print-btn').addEventListener('click',()=>window.print());

const kg=document.getElementById('kitchen-groups');
kg.innerHTML=groups().map(([type,list])=>'<section><strong>'+esc(list[0]?.category_icon||'•')+' '+esc(type)+'</strong>'+list.map(it=>'<div><span>'+esc(it.name)+'</span><b>'+esc(it.quantity)+'</b></div>').join('')+'</section>').join('');
</script>
</body></html>
