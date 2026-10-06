<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use PDO;

final class AuditController
{
    public static function index(PDO $db): array
    {
        $from = trim((string)($_GET['from'] ?? date('Y-m-d', strtotime('-7 days'))));
        $to = trim((string)($_GET['to'] ?? date('Y-m-d')));
        $action = trim((string)($_GET['action'] ?? ''));
        $sql = "SELECT a.*, COALESCE(u.display_name,u.username,u.email,'سیستم') user_name
                FROM audit_logs a LEFT JOIN users u ON u.id=a.user_id
                WHERE a.created_at >= ? AND a.created_at < DATE_ADD(?,INTERVAL 1 DAY)";
        $params=[$from,$to];
        if($action!==''){ $sql.=" AND a.action=?"; $params[]=$action; }
        $sql.=" ORDER BY a.id DESC LIMIT 500";
        $s=$db->prepare($sql);$s->execute($params);$rows=$s->fetchAll();
        $actions=$db->query("SELECT DISTINCT action FROM audit_logs ORDER BY action LIMIT 100")->fetchAll(PDO::FETCH_COLUMN);
        return compact('rows','from','to','action','actions');
    }
}
