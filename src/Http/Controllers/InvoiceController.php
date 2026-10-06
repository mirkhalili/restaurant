<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Support\AuditLogger;
use PDO;

final class InvoiceController
{
    public static function index(PDO $db): array
    {
        $message=null;$error=null;
        try {
            if($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action']??'')==='update_invoice') {
                $invoiceId=(int)($_POST['invoice_id']??0);
                self::update($db,$invoiceId);
                $message='فاکتور با موفقیت ویرایش شد.';
            }
        } catch(\Throwable $e){$error=self::friendly($e);}
        $id=(int)($_GET['id']??$_POST['invoice_id']??0);
        if($id<1) throw new \RuntimeException('فاکتور نامعتبر است.');
        $s=$db->prepare("SELECT i.*,o.order_no,o.customer_id,o.customer_phone,o.payment_method,o.status order_status,o.created_at order_created,
                                c.name customer_name,c.phone customer_phone_real
                         FROM invoices i JOIN orders o ON o.id=i.order_id
                         LEFT JOIN customers c ON c.id=o.customer_id WHERE i.id=? LIMIT 1");
        $s->execute([$id]);$invoice=$s->fetch();
        if(!$invoice) throw new \RuntimeException('فاکتور پیدا نشد.');
        $s=$db->prepare("SELECT oi.*,p.name,p.product_code,p.unit FROM order_items oi JOIN products p ON p.id=oi.product_id WHERE oi.order_id=? ORDER BY oi.id");
        $s->execute([(int)$invoice['order_id']]);$items=$s->fetchAll();
        $products=$db->query("SELECT id,name,price,product_code,unit FROM products WHERE status='active' ORDER BY name LIMIT 500")->fetchAll();
        return compact('invoice','items','products','message','error');
    }

    public static function delete(PDO $db,int $invoiceId): void
    {
        if($invoiceId<1) throw new \RuntimeException('فاکتور نامعتبر است.');
        $s=$db->prepare("SELECT i.*,o.id order_id,o.order_no,o.customer_id,o.total_amount FROM invoices i JOIN orders o ON o.id=i.order_id WHERE i.id=? LIMIT 1");
        $s->execute([$invoiceId]);$invoice=$s->fetch();
        if(!$invoice) throw new \RuntimeException('فاکتور پیدا نشد.');
        $db->beginTransaction();
        try{
            $s=$db->prepare("SELECT payment_no,method,amount FROM payments WHERE invoice_id=?");$s->execute([$invoiceId]);$payments=$s->fetchAll();
            $s=$db->prepare("SELECT product_id,quantity,unit_price,line_total FROM order_items WHERE order_id=?");$s->execute([(int)$invoice['order_id']);$items=$s->fetchAll();
            $db->prepare('DELETE FROM payments WHERE invoice_id=?')->execute([$invoiceId]);
            $db->prepare('DELETE FROM invoices WHERE id=?')->execute([$invoiceId]);
            $db->prepare('DELETE FROM order_items WHERE order_id=?')->execute([(int)$invoice['order_id']]);
            $db->prepare('DELETE FROM orders WHERE id=?')->execute([(int)$invoice['order_id']]);
            AuditLogger::log($db,'حذف فاکتور','invoice',$invoiceId,
                ['invoice_no'=>$invoice['invoice_no'],'order_id'=>$invoice['order_id'],'total_amount'=>$invoice['total_amount'],'items'=>$items,'payments'=>$payments],
                ['deleted'=>true]);
            $db->commit();
        }catch(\Throwable $e){$db->rollBack();throw $e;}
    }

    public static function recent(PDO $db): array
    {
        return $db->query("SELECT i.id,i.invoice_no,i.total_amount,i.created_at,o.order_no,o.order_type,o.payment_method,o.status,
                                  COALESCE(c.name,o.customer_phone,'بدون مشتری') customer_name
                           FROM invoices i JOIN orders o ON o.id=i.order_id
                           LEFT JOIN customers c ON c.id=o.customer_id
                           ORDER BY i.id DESC LIMIT 10")->fetchAll();
    }

    private static function update(PDO $db,int $invoiceId): void
    {
        if($invoiceId<1) throw new \RuntimeException('فاکتور نامعتبر است.');
        $s=$db->prepare('SELECT i.*,o.id order_id,o.payment_method FROM invoices i JOIN orders o ON o.id=i.order_id WHERE i.id=?');
        $s->execute([$invoiceId]);$invoice=$s->fetch();
        if(!$invoice) throw new \RuntimeException('فاکتور پیدا نشد.');
        $db->beginTransaction();
        try {
            $before=['invoice_id'=>$invoiceId,'total_amount'=>$invoice['total_amount'],'payment_method'=>$invoice['payment_method']];
            $total=0;
            $q=$db->prepare('SELECT id,quantity,unit_price FROM order_items WHERE order_id=?');
            $q->execute([(int)$invoice['order_id']]);
            $existing=$q->fetchAll();
            $upd=$db->prepare('UPDATE order_items SET quantity=?,line_total=? WHERE id=?');
            $del=$db->prepare('DELETE FROM order_items WHERE id=?');
            $submitted=(array)($_POST['qty']??[]);
            foreach($existing as $it){
                $id=(int)$it['id'];$qty=(float)($submitted[$id]??0);
                if($qty<=0){$del->execute([$id]);continue;}
                $line=$qty*(float)$it['unit_price'];$upd->execute([$qty,$line,$id]);$total+=$line;
            }
            $newProduct=(int)($_POST['new_product_id']??0);$newQty=(float)($_POST['new_quantity']??0);
            if($newProduct>0&&$newQty>0){
                $p=$db->prepare("SELECT id,price FROM products WHERE id=? AND status='active'");$p->execute([$newProduct]);$product=$p->fetch();
                if(!$product) throw new \RuntimeException('محصول جدید معتبر نیست.');
                $line=$newQty*(float)$product['price'];
                $ins=$db->prepare('INSERT INTO order_items(order_id,product_id,quantity,unit_price,line_total,created_at) VALUES(?,?,?,?,?,NOW())');
                $ins->execute([(int)$invoice['order_id'],$newProduct,$newQty,$product['price'],$line]);$total+=$line;
            }
            $method=$_POST['payment_method']??$invoice['payment_method'];
            if(!in_array($method,['cash','card','online','mixed'],true)) throw new \RuntimeException('شیوه پرداخت نامعتبر است.');
            $db->prepare('UPDATE orders SET total_amount=?,paid_amount=?,payment_method=?,updated_at=NOW() WHERE id=?')->execute([$total,$total,$method,(int)$invoice['order_id']]);
            $db->prepare('UPDATE invoices SET total_amount=? WHERE id=?')->execute([$total,$invoiceId]);
            $db->prepare('UPDATE payments SET method=?,amount=? WHERE invoice_id=?')->execute([$method,$total,$invoiceId]);
            AuditLogger::log($db,'ویرایش فاکتور','invoice',$invoiceId,$before,['total_amount'=>$total,'payment_method'=>$method]);
            $db->commit();
        } catch(\Throwable $e){$db->rollBack();throw $e;}
    }

    private static function friendly(\Throwable $e): string
    {
        return str_contains($e->getMessage(),'foreign key')?'این فاکتور به اطلاعات عملیاتی وابسته است و قابل تغییر نیست.':$e->getMessage();
    }
}
