<?php
namespace App\Http\Controllers;
use PDO;
final class RestaurantController {
 public static function customerSearch(PDO $db,string $phone): ?array {
  if($phone==='') return null;
  $s=$db->prepare('SELECT * FROM customers WHERE phone LIKE ? OR mobile LIKE ? LIMIT 1');
  $s->execute(['%'.$phone.'%','%'.$phone.'%']);
  return $s->fetch() ?: null;
 }
 public static function addCustomer(PDO $db): void {
  $first=trim($_POST['first_name']??''); $last=trim($_POST['last_name']??''); $phone=trim($_POST['phone']??'');
  if($phone==='') throw new \RuntimeException('شماره تلفن الزامی است.');
  $s=$db->prepare('INSERT INTO customers(subscription_code,first_name,last_name,name,phone,mobile,membership_date,address,birth_date,created_at) VALUES(?,?,?,?,?,?,?,?,?,NOW())');
  $s->execute([trim($_POST['subscription_code']??'')?:null,$first,$last,trim($first.' '.$last),$phone,trim($_POST['mobile']??'')?:null,trim($_POST['membership_date']??'')?:null,trim($_POST['address']??'')?:null,trim($_POST['birth_date']??'')?:null]);
 }
 public static function addProduct(PDO $db): void {
  $s=$db->prepare('INSERT INTO products(product_code,name,price,unit,product_type,status,created_at) VALUES(?,?,?,?,?,?,NOW())');
  $s->execute([trim($_POST['product_code']??''),trim($_POST['name']??''),(float)($_POST['price']??0),trim($_POST['unit']??''),trim($_POST['product_type']??''),($_POST['status']??'active')==='active'?'active':'inactive']);
 }
 public static function addItem(): void {
  if(!isset($_SESSION['order_cart'])) $_SESSION['order_cart']=[];
  $id=(int)($_POST['product_id']??0); if($id>0) $_SESSION['order_cart'][$id]=($_SESSION['order_cart'][$id]??0)+1;
 }
 public static function removeItem(): void {
  $id=(int)($_POST['product_id']??0); unset($_SESSION['order_cart'][$id]);
 }
 public static function cart(PDO $db): array {
  if(!isset($_SESSION['order_cart'])) $_SESSION['order_cart']=[];
  $cart=$_SESSION['order_cart']; $items=[]; $total=0;
  if($cart){$ids=array_keys($cart);$in=implode(',',array_fill(0,count($ids),'?'));$s=$db->prepare("SELECT * FROM products WHERE id IN ($in) AND status='active'");$s->execute($ids);foreach($s->fetchAll() as $p){$p['qty']=$cart[$p['id']];$p['line_total']=$p['qty']*$p['price'];$items[]=$p;$total+=$p['line_total'];}}
  return [$items,$total];
 }
 public static function finalize(PDO $db,array $customer): string {
  [$items,$total]=self::cart($db); if(!$customer||!$items) throw new \RuntimeException('مشتری و حداقل یک قلم سفارش الزامی است.');
  $method=$_POST['payment_method']??'cash'; $orderNo='R'.date('YmdHis').random_int(100,999); $invoiceNo='F'.date('YmdHis').random_int(100,999);
  $db->beginTransaction();
  try{
   $s=$db->prepare("INSERT INTO orders(order_no,customer_id,customer_phone,order_type,status,total_amount,invoice_no,payment_method,paid_amount,finalized_at,created_at) VALUES(?,?,?,'حضوری','completed',?,?,?,?,NOW(),NOW())");
   $s->execute([$orderNo,$customer['id'],$customer['phone'],$total,$invoiceNo,$method,$total]); $oid=(int)$db->lastInsertId();
   $s=$db->prepare('INSERT INTO order_items(order_id,product_id,quantity,unit_price,line_total,created_at) VALUES(?,?,?,?,?,NOW())');
   foreach($items as $p){$q=(int)$p['qty'];$s->execute([$oid,$p['id'],$q,$p['price'],$q*$p['price']]);}
   $s=$db->prepare('INSERT INTO invoices(invoice_no,order_id,total_amount,created_at) VALUES(?,?,?,NOW())');$s->execute([$invoiceNo,$oid,$total]);$iid=(int)$db->lastInsertId();
   $s=$db->prepare('INSERT INTO payments(payment_no,invoice_id,method,amount,created_at) VALUES(?,?,?,?,NOW())');$s->execute(['P'.date('YmdHis').random_int(100,999),$iid,$method,$total]);
   $db->commit();$_SESSION['order_cart']=[];return $invoiceNo.'|'.$total;
  }catch(\Throwable $e){$db->rollBack();throw $e;}
 }
}