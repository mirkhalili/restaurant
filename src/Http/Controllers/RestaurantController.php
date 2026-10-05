<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Support\PersianDate;
use App\Support\PersianText;
use PDO;

final class RestaurantController
{
    public static function page(PDO $db): array
    {
        self::ensureCart();
        $phone = trim((string)($_GET['phone'] ?? $_SESSION['order_customer_phone'] ?? ''));
        $customer = $phone !== '' ? self::customerSearch($db, $phone) : null;
        if ($customer) $_SESSION['order_customer_phone'] = $customer['phone'];

        [$items,$total] = self::cart($db);
        $products = $db->query("SELECT p.*,c.name AS category_name,c.color AS category_color FROM products p LEFT JOIN product_categories c ON c.id=p.category_id WHERE p.status='active' ORDER BY c.sort_order,c.name,p.name LIMIT 500")->fetchAll();
        $message = null; $error = null;

        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $action = $_POST['action'] ?? '';
                if ($action === 'add_customer_order') { self::addCustomerFromOrder($db); $message='مشتری با موفقیت ثبت و انتخاب شد.'; }
                elseif ($action === 'add_item') { self::addItem(); }
                elseif ($action === 'remove_item') { self::removeItem(); }
                elseif ($action === 'clear_cart') { self::clearCart(); }
                elseif ($action === 'finalize') {
                    $customerPhone = trim((string)($_POST['customer_phone'] ?? $_SESSION['order_customer_phone'] ?? ''));
                    $customer = self::customerSearch($db, $customerPhone);
                    $result = self::finalize($db, $customer ?: []);
                    header('Location: /?page=orders&success='.rawurlencode($result['invoice_no']).'&amount='.rawurlencode((string)$result['total']));
                    exit;
                }
                [$items,$total] = self::cart($db);
                $phone = trim((string)($_SESSION['order_customer_phone'] ?? $phone));
                $customer = $phone !== '' ? self::customerSearch($db, $phone) : null;
            }
        } catch (\Throwable $e) {
            $error = self::friendly($e);
        }
        return compact('products','items','total','customer','phone','message','error');
    }

    public static function customerSearch(PDO $db, string $query): ?array
    {
        $query = PersianText::normalize($query);
        if ($query === '') return null;
        $phone = self::normalizeDigits($query);
        $s=$db->prepare('SELECT * FROM customers WHERE phone = ? OR name LIKE ? OR first_name LIKE ? OR last_name LIKE ? ORDER BY (phone = ?) DESC, id DESC LIMIT 1');
        $like='%'.$query.'%';
        $s->execute([$phone,$like,$like,$like,$phone]);
        return $s->fetch() ?: null;
    }

    public static function addCustomer(PDO $db): void
    {
        $phone=self::normalizeDigits($_POST['phone']??'');
        $first=PersianText::normalize($_POST['first_name']??'');
        $last=PersianText::normalize($_POST['last_name']??'');
        if($first===''||$last===''||$phone==='') throw new \RuntimeException('نام، نام خانوادگی و شماره تلفن الزامی است.');
        $s=$db->prepare('INSERT INTO customers(subscription_code,first_name,last_name,name,phone,mobile,membership_date,address,birth_date,created_at) VALUES(?,?,?,?,?,?,?,?,?,NOW())');
        $s->execute([
            PersianText::normalize($_POST['subscription_code']??'')?:null,$first,$last,trim($first.' '.$last),$phone,
            self::normalizeDigits($_POST['mobile']??'')?:null,
            PersianDate::toGregorian($_POST['membership_date']??''),
            PersianText::normalize($_POST['address']??'')?:null,
            PersianDate::toGregorian($_POST['birth_date']??'')
        ]);
    }

    public static function updateCustomer(PDO $db): void
    {
        $id=(int)($_POST['id']??0); $phone=self::normalizeDigits($_POST['phone']??'');
        $first=PersianText::normalize($_POST['first_name']??''); $last=PersianText::normalize($_POST['last_name']??'');
        if($id<1||$first===''||$last===''||$phone==='') throw new \RuntimeException('نام، نام خانوادگی و شماره تلفن الزامی است.');
        $s=$db->prepare('UPDATE customers SET subscription_code=?,first_name=?,last_name=?,name=?,phone=?,mobile=?,membership_date=?,address=?,birth_date=?,updated_at=NOW() WHERE id=?');
        $s->execute([PersianText::normalize($_POST['subscription_code']??'')?:null,$first,$last,trim($first.' '.$last),$phone,self::normalizeDigits($_POST['mobile']??'')?:null,PersianDate::toGregorian($_POST['membership_date']??''),PersianText::normalize($_POST['address']??'')?:null,PersianDate::toGregorian($_POST['birth_date']??''),$id]);
    }

    public static function deleteCustomer(PDO $db): void
    {
        $id=(int)($_POST['id']??0); if($id<1) throw new \RuntimeException('مشتری نامعتبر است.');
        $s=$db->prepare('DELETE FROM customers WHERE id=?'); $s->execute([$id]);
    }

    public static function addCustomerFromOrder(PDO $db): void
    {
        self::addCustomer($db);
        $_SESSION['order_customer_phone']=self::normalizeDigits($_POST['phone']??'');
    }

    public static function updateProduct(PDO $db): void
    {
        $id=(int)($_POST['id']??0); $code=trim((string)($_POST['product_code']??'')); $name=PersianText::normalize($_POST['name']??'');
        if($id<1||$code===''||$name==='') throw new \RuntimeException('کد کالا و نام کالا الزامی است.');
        $s=$db->prepare('UPDATE products SET product_code=?,name=?,price=?,unit=?,product_type=?,category_id=?,status=?,updated_at=NOW() WHERE id=?');
        $s->execute([$code,$name,(float)($_POST['price']??0),PersianText::normalize($_POST['unit']??'')?:null,PersianText::normalize($_POST['product_type']??'')?:null,((int)($_POST['category_id']??0)?:null),($_POST['status']??'active')==='active'?'active':'inactive',$id]);
    }

    public static function deleteProduct(PDO $db): void
    {
        $id=(int)($_POST['id']??0); if($id<1) throw new \RuntimeException('محصول نامعتبر است.');
        $s=$db->prepare('DELETE FROM products WHERE id=?'); $s->execute([$id]);
    }

    public static function addProduct(PDO $db): void
    {
        $code=trim((string)($_POST['product_code']??'')); $name=PersianText::normalize($_POST['name']??'');
        if($code===''||$name==='') throw new \RuntimeException('کد کالا و نام کالا الزامی است.');
        $s=$db->prepare('INSERT INTO products(product_code,name,price,unit,product_type,category_id,status,created_at) VALUES(?,?,?,?,?,?,?,NOW())');
        $s->execute([$code,$name,(float)($_POST['price']??0),PersianText::normalize($_POST['unit']??'')?:null,PersianText::normalize($_POST['product_type']??'')?:null,((int)($_POST['category_id']??0)?:null),($_POST['status']??'active')==='active'?'active':'inactive']);
    }

    public static function addItem(): void
    {
        self::ensureCart();
        $id=(int)($_POST['product_id']??0);
        if($id>0) $_SESSION['order_cart'][$id]=($_SESSION['order_cart'][$id]??0)+1;
    }

    public static function removeItem(): void
    {
        self::ensureCart();
        $id=(int)($_POST['product_id']??0);
        unset($_SESSION['order_cart'][$id]);
    }

    public static function clearCart(): void
    {
        $_SESSION['order_cart']=[];
    }

    public static function cart(PDO $db): array
    {
        self::ensureCart();
        $items=[];$total=0;$ids=array_keys($_SESSION['order_cart']);
        if($ids){
            $in=implode(',',array_fill(0,count($ids),'?'));
            $s=$db->prepare("SELECT * FROM products WHERE id IN ($in) AND status='active'");
            $s->execute($ids);
            foreach($s->fetchAll() as $p){
                $p['qty']=(int)($_SESSION['order_cart'][$p['id']]??0);
                $p['line_total']=$p['qty']*(float)$p['price'];$items[]=$p;$total+=$p['line_total'];
            }
        }
        return [$items,$total];
    }

    public static function finalize(PDO $db,array $customer): array
    {
        [$items,$total]=self::cart($db);
        if(!$customer||!$items) throw new \RuntimeException('ابتدا مشتری و حداقل یک محصول را انتخاب کنید.');
        $method=$_POST['payment_method']??'cash';
        if(!in_array($method,['cash','card','online','mixed'],true)) throw new \RuntimeException('شیوه پرداخت نامعتبر است.');
        $orderNo='R'.date('YmdHis').random_int(100,999);
        $invoiceNo='F'.date('YmdHis').random_int(100,999);
        $paymentNo='P'.date('YmdHis').random_int(100,999);
        $db->beginTransaction();
        try{
            $s=$db->prepare("INSERT INTO orders(order_no,customer_id,customer_phone,order_type,status,total_amount,invoice_no,payment_method,paid_amount,finalized_at,created_at) VALUES(?,?,?,'حضوری','completed',?,?,?,?,NOW(),NOW())");
            $s->execute([$orderNo,$customer['id'],$customer['phone'],$total,$invoiceNo,$method,$total]); $oid=(int)$db->lastInsertId();
            $s=$db->prepare('INSERT INTO order_items(order_id,product_id,quantity,unit_price,line_total,created_at) VALUES(?,?,?,?,?,NOW())');
            foreach($items as $p){$q=(int)$p['qty'];$s->execute([$oid,$p['id'],$q,$p['price'],$q*$p['price']]);}
            $s=$db->prepare('INSERT INTO invoices(invoice_no,order_id,total_amount,created_at) VALUES(?,?,?,NOW())');$s->execute([$invoiceNo,$oid,$total]);$iid=(int)$db->lastInsertId();
            $s=$db->prepare('INSERT INTO payments(payment_no,invoice_id,method,amount,created_at) VALUES(?,?,?,?,NOW())');$s->execute([$paymentNo,$iid,$method,$total]);
            $db->commit();self::clearCart();unset($_SESSION['order_customer_phone']);
            return ['invoice_no'=>$invoiceNo,'total'=>$total];
        }catch(\Throwable $e){$db->rollBack();throw $e;}
    }

    private static function ensureCart(): void { if(!isset($_SESSION['order_cart'])||!is_array($_SESSION['order_cart'])) $_SESSION['order_cart']=[]; }
    private static function normalizeDigits(string $v): string { return PersianDate::latinDigits(trim($v)); }
    private static function friendly(\Throwable $e): string {
        $m=$e->getMessage();
        if(str_contains($m,'Duplicate entry')) return 'اطلاعات تکراری است؛ شماره تلفن مشتری، کد اشتراک یا کد کالا باید یکتا باشد.';
        return $m;
    }
}
