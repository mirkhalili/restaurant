<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Support\AuditLogger;
use PDO;

final class PrintController
{
    public static function receipt(PDO $db): array
    {
        $invoiceNo=trim((string)($_GET['invoice']??''));
        $s=$db->prepare("SELECT i.*,o.order_no,o.customer_phone,o.payment_method,o.created_at,
                                COALESCE(c.name,o.customer_phone,'مشتری') customer_name
                         FROM invoices i JOIN orders o ON o.id=i.order_id
                         LEFT JOIN customers c ON c.id=o.customer_id WHERE i.invoice_no=? LIMIT 1");
        $s->execute([$invoiceNo]);$invoice=$s->fetch();
        if(!$invoice) throw new \RuntimeException('فاکتور برای چاپ پیدا نشد.');
        $s=$db->prepare("SELECT oi.quantity,oi.unit_price,oi.line_total,p.name,p.unit FROM order_items oi JOIN products p ON p.id=oi.product_id WHERE oi.order_id=? ORDER BY oi.id");
        $s->execute([(int)$invoice['order_id']]);$items=$s->fetchAll();
        $printers=$db->query("SELECT * FROM printers WHERE status='active' ORDER BY is_default DESC,name")->fetchAll();
        $settings=self::settings($db);$restaurant=self::restaurant($db);
        if($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action']??'')==='print_receipt'){
            AuditLogger::log($db,'چاپ فیش','invoice',(int)$invoice['id'],null,['printer_id'=>(int)($_POST['printer_id']??0),'width'=>$settings['paper_width']??'80mm']);
            $message='فیش برای چاپ آماده شد. در مرحله چاپ مرورگر، چاپگر انتخاب‌شده را انتخاب/تأیید کنید.';
        } else $message=null;
        return compact('invoice','items','printers','settings','restaurant','message');
    }

    private static function restaurant(PDO $db): array
    {
        $s=$db->query('SELECT * FROM restaurant_profile WHERE id=1');return $s->fetch()?:['name'=>'رستوران','address'=>'','phone'=>'','logo_path'=>''];
    }

    public static function settings(PDO $db): array
    {
        $defaults=['paper_width'=>'80mm','show_logo'=>'1','show_address'=>'1','show_phone'=>'1','show_customer'=>'1','show_invoice_no'=>'1','show_date'=>'1','show_payment'=>'1','show_footer'=>'1','footer_text'=>'از خرید شما سپاسگزاریم','feed'=>'3','cut'=>'1'];
        $rows=$db->query("SELECT setting_key,setting_value FROM settings WHERE scope='print'")->fetchAll();
        foreach($rows as $r){$v=json_decode((string)$r['setting_value'],true);$defaults[$r['setting_key']]=is_string($v)||is_numeric($v)?(string)$v:$v;}
        return $defaults;
    }
}
