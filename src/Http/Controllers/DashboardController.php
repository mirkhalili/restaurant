<?php
declare(strict_types=1);
namespace App\Http\Controllers;
use PDO;
final class DashboardController{public static function index(PDO $db):array{$stats=[['label'=>'سفارش امروز','value'=>(int)$db->query("SELECT COUNT(*) FROM orders WHERE DATE(created_at)=CURRENT_DATE")->fetchColumn()],['label'=>'فروش امروز','value'=>(float)$db->query("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE DATE(created_at)=CURRENT_DATE AND status NOT IN ('cancelled','refunded')")->fetchColumn()],['label'=>'مشتریان','value'=>(int)$db->query("SELECT COUNT(*) FROM customers")->fetchColumn()],['label'=>'غذاهای فعال','value'=>(int)$db->query("SELECT COUNT(*) FROM products WHERE status='active'")->fetchColumn()]];$recentInvoices=InvoiceController::recent($db);return compact('stats','recentInvoices');}}