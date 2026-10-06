<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use PDO;

final class ReportsController
{
    public static function index(PDO $db): array
    {
        $from=(string)($_GET['from']??date('Y-m-d'));
        $to=(string)($_GET['to']??date('Y-m-d'));
        $status=(string)($_GET['status']??'completed');
        $sql="SELECT COUNT(*) order_count, COALESCE(SUM(total_amount),0) sales, COALESCE(SUM(paid_amount),0) paid,
                     COALESCE(AVG(total_amount),0) avg_order
              FROM orders WHERE created_at>=? AND created_at<DATE_ADD(?,INTERVAL 1 DAY)";
        $params=[$from,$to];
        if($status!=='all'){ $sql.=" AND status=?"; $params[]=$status; }
        $s=$db->prepare($sql);$s->execute($params);$summary=$s->fetch()?:[];
        $s=$db->prepare("SELECT payment_method,COUNT(*) orders,COALESCE(SUM(total_amount),0) amount
                         FROM orders WHERE created_at>=? AND created_at<DATE_ADD(?,INTERVAL 1 DAY)
                         AND status NOT IN ('cancelled','refunded') GROUP BY payment_method ORDER BY amount DESC");
        $s->execute([$from,$to]);$payments=$s->fetchAll();
        $s=$db->prepare("SELECT p.name,SUM(oi.quantity) qty,SUM(oi.line_total) amount
                         FROM order_items oi JOIN orders o ON o.id=oi.order_id JOIN products p ON p.id=oi.product_id
                         WHERE o.created_at>=? AND o.created_at<DATE_ADD(?,INTERVAL 1 DAY)
                         AND o.status NOT IN ('cancelled','refunded')
                         GROUP BY p.id,p.name ORDER BY amount DESC LIMIT 20");
        $s->execute([$from,$to]);$products=$s->fetchAll();
        $s=$db->prepare("SELECT DATE(created_at) day,COUNT(*) orders,COALESCE(SUM(total_amount),0) amount
                         FROM orders WHERE created_at>=? AND created_at<DATE_ADD(?,INTERVAL 1 DAY)
                         AND status NOT IN ('cancelled','refunded') GROUP BY DATE(created_at) ORDER BY day DESC");
        $s->execute([$from,$to]);$daily=$s->fetchAll();
        return compact('from','to','status','summary','payments','products','daily');
    }
}
