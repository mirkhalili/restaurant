<?php
declare(strict_types=1);

namespace App\Support;

use PDO;

final class AuditLogger
{
    public static function log(PDO $db, string $action, ?string $entityType=null, ?int $entityId=null, ?array $before=null, ?array $after=null, ?string $reason=null): void
    {
        try {
            $userId = isset($_SESSION['user']['id']) ? (int)$_SESSION['user']['id'] : null;
            $ip = substr((string)($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45);
            $s = $db->prepare('INSERT INTO audit_logs(user_id,action,entity_type,entity_id,before_data,after_data,reason,ip_address,created_at) VALUES(?,?,?,?,?,?,?,?,NOW())');
            $s->execute([$userId ?: null,$action,$entityType,$entityId,$before ? json_encode($before,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) : null,$after ? json_encode($after,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) : null,$reason,$ip ?: null]);
        } catch (\Throwable $e) { /* logging must never break the business transaction */ }
    }
}
