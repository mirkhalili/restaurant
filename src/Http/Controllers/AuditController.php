<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Support\PersianDate;
use PDO;

final class AuditController
{
    public static function index(PDO $db): array
    {
        $todayJ=PersianDate::format(date('Y-m-d'),false);
        $defaultFrom=PersianDate::format(date('Y-m-d',strtotime('-7 days')),false);
        $fromJ=trim((string)($_GET['from']??$defaultFrom));
        $toJ=trim((string)($_GET['to']??$todayJ));
        $from=PersianDate::toGregorian($fromJ)??date('Y-m-d',strtotime('-7 days'));
        $to=PersianDate::toGregorian($toJ)??date('Y-m-d');
        if($from>$to){[$from,$to]=[$to,$from];[$fromJ,$toJ]=[$toJ,$fromJ];}
        $action=trim((string)($_GET['action']??''));

        $sql="SELECT a.*,COALESCE(u.display_name,u.username,u.email,'سیستم') user_name
              FROM audit_logs a LEFT JOIN users u ON u.id=a.user_id
              WHERE a.created_at>=? AND a.created_at<DATE_ADD(?,INTERVAL 1 DAY)";
        $params=[$from,$to];
        if($action!==''){$sql.=" AND a.action=?";$params[]=$action;}
        $sql.=" ORDER BY a.id DESC LIMIT 500";
        $s=$db->prepare($sql);$s->execute($params);$rows=$s->fetchAll();
        $actions=$db->query("SELECT DISTINCT action FROM audit_logs ORDER BY action LIMIT 100")->fetchAll(PDO::FETCH_COLUMN);
        return compact('rows','fromJ','toJ','action','actions');
    }
}
