<?php
declare(strict_types=1);

namespace AppHttpControllers;

use AppCoreAuth;
use AppSupportAuditLogger;
use AppSupportPersianDate;
use PDO;

final class ReportsController
{
    public static function index(PDO $db): array
    {
        $message=null;$error=null;
        try {
            if($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action']??'')==='delete_invoice'){
                if(!self::canManageInvoices()) throw new \RuntimeException('فقط مدیر سیستم یا مدیر رستوران مجاز به حذف فاکتور است.');
                InvoiceController::delete($db,(int)($_POST['invoice_id']??0));
                $message='فاکتور با موفقیت حذف شد.';
            }
        } catch(\Throwable $e){$error=$e->getMessage();}

        $today=PersianDate::format(date('Y-m-d'),false);
        $fromJ=(string)($_GET['from']??$today);
        $toJ=(string)($_GET['to']??$today);
        $from=PersianDate::toGregorian($fromJ)??date('Y-m-d');
        $to=PersianDate::toGregorian($toJ)??$from;
        if($from>$to){$tmp=$from;$from=$to;$to=$tmp;$tmp=$fromJ;$fromJ=$toJ;$toJ=$tmp;}
        $status=(string)($_GET['status']??'completed');
        $customerId=(int)($_GET['customer_id']??0);

        $where="o.created_at>=? AND o.created_at<DATE_ADD(?,INTERVAL 1 DAY)";
        $params=[$from,$to];
        if($status!=='all'){ $where.=" AND o.status=?"; $params[]=$status; }
        if($customerId>0){ $where.=" AND o.customer_id=?"; $params[]=$customerId; }

        $s=$db->prepare("SELECT COUNT(*) order_count,COALESCE(SUM(o.total_amount),0) sales,COALESCE(SUM(o.paid_amount),0) paid,COALESCE(AVG(o.total_amount),0) avg_order FROM orders o WHERE {$where}");
        $s->execute($params);$summary=$s->fetch()?:[];

        $s=$db->prepare("SELECT o.payment_method,COUNT(*) orders,COALESCE(SUM(o.total_amount),0) amount FROM orders o WHERE {$where} AND o.status NOT IN ('cancelled','refunded') GROUP BY o.payment_method ORDER BY amount DESC");
        $s->execute($params);$payments=$s->fetchAll();

        $s=$db->prepare("SELECT p.name,SUM(oi.quantity) qty,SUM(oi.line_total) amount FROM order_items oi JOIN orders o ON o.id=oi.order_id JOIN products p ON p.id=oi.product_id WHERE {$where} AND o.status NOT IN ('cancelled','refunded') GROUP BY p.id,p.name ORDER BY amount DESC LIMIT 20");
        $s->execute($params);$products=$s->fetchAll();

        $s=$db->prepare("SELECT DATE(o.created_at) day,COUNT(*) orders,COALESCE(SUM(o.total_amount),0) amount FROM orders o WHERE {$where} AND o.status NOT IN ('cancelled','refunded') GROUP BY DATE(o.created_at) ORDER BY day DESC");
        $s->execute($params);$daily=$s->fetchAll();

        $invoiceWhere="i.created_at>=? AND i.created_at<DATE_ADD(?,INTERVAL 1 DAY)";
        $invoiceParams=[$from,$to];
        if($customerId>0){$invoiceWhere.=" AND o.customer_id=?";$invoiceParams[]=$customerId;}
        if($status!=='all'){$invoiceWhere.=" AND o.status=?";$invoiceParams[]=$status;}
        $s=$db->prepare("SELECT i.id,i.invoice_no,i.total_amount,i.status invoice_status,i.created_at,o.id order_id,o.order_no,o.order_type,o.payment_method,o.status order_status,COALESCE(c.name,o.customer_phone,'بدون مشتری') customer_name FROM invoices i JOIN orders o ON o.id=i.order_id LEFT JOIN customers c ON c.id=o.customer_id WHERE {$invoiceWhere} ORDER BY i.id DESC LIMIT 300");
        $s->execute($invoiceParams);$invoices=$s->fetchAll();

        $customers=$db->query("SELECT id,name,phone FROM customers ORDER BY name LIMIT 500")->fetchAll();
        return compact('fromJ','toJ','status','customerId','summary','payments','products','daily','invoices','customers','message','error');
    }

    private static function canManageInvoices(): bool
    {
        $u=Auth::user();$role=(string)($u['role_name']??'');
        return in_array($role,['مدیر سیستم','مدیر رستوران'],true);
    }
}
