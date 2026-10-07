<?php
declare(strict_types=1);
namespace App\Http\Controllers;
use App\Core\Auth;
use App\Support\AuditLogger;
use App\Support\PersianText;
use PDO;

final class InventoryController{
 public static function index(PDO $db):array{
  $message=null;$error=null;
  try{
   if($_SERVER['REQUEST_METHOD']==='POST'){
    switch((string)($_POST['action']??'')){
     case'save_item':self::saveItem($db);$message='کالای انبار ذخیره شد.';break;
     case'adjust_stock':self::adjustStock($db);$message='موجودی با موفقیت به‌روزرسانی شد.';break;
     case'save_rule':self::saveRule($db);$message='میزان مصرف در هر سفارش ذخیره شد.';break;
     case'delete_rule':self::deleteRule($db);$message='رابطه مصرف حذف شد.';break;
     case'toggle_item':self::toggleItem($db);$message='وضعیت کنترل موجودی تغییر کرد.';break;
    }
   }
  }catch(\Throwable $e){$error=self::friendly($e);}
  $items=$db->query("SELECT i.*,
    (SELECT COUNT(*) FROM inventory_product_rules r WHERE r.inventory_item_id=i.id) recipe_count
    FROM inventory_items i ORDER BY (i.track_stock=1 AND i.current_quantity<=i.reorder_level) DESC,i.name")->fetchAll();
  $products=$db->query("SELECT id,name,product_code FROM products WHERE status='active' ORDER BY name")->fetchAll();
  $inventoryItems=$db->query("SELECT id,name,unit,current_quantity,track_stock FROM inventory_items WHERE status='active' ORDER BY name")->fetchAll();
  $rules=$db->query("SELECT r.*,p.name product_name,p.product_code,i.name item_name,i.unit,i.current_quantity,i.reorder_level,i.track_stock
    FROM inventory_product_rules r
    INNER JOIN products p ON p.id=r.product_id
    INNER JOIN inventory_items i ON i.id=r.inventory_item_id
    ORDER BY p.name,i.name")->fetchAll();
  $lowStock=array_values(array_filter($items,fn($i)=>(int)$i['track_stock']===1&&(float)$i['current_quantity']<=(float)$i['reorder_level']));
  $editId=(int)($_GET['edit_id']??0);$editItem=null;
  if($editId>0){$s=$db->prepare('SELECT * FROM inventory_items WHERE id=?');$s->execute([$editId]);$editItem=$s->fetch()?:null;}
  $controlledCount=count(array_filter($items,fn($i)=>(int)$i['track_stock']===1));
  return compact('items','products','inventoryItems','rules','lowStock','editItem','controlledCount','message','error');
 }
 private static function saveItem(PDO $db):void{
  $id=(int)($_POST['id']??0);$code=trim((string)($_POST['item_code']??''));$name=PersianText::normalize($_POST['name']??'');$unit=PersianText::normalize($_POST['unit']??'عدد')?:'عدد';
  $qty=max(0,(float)str_replace(',','.',(string)($_POST['current_quantity']??0)));$reorder=max(0,(float)str_replace(',','.',(string)($_POST['reorder_level']??0)));$track=isset($_POST['track_stock'])?1:0;
  if($name==='')throw new \RuntimeException('نام کالا الزامی است.');
  if($id>0){$db->prepare('UPDATE inventory_items SET item_code=?,name=?,unit=?,current_quantity=?,reorder_level=?,track_stock=?,updated_at=NOW() WHERE id=?')->execute([$code?:null,$name,$unit,$qty,$reorder,$track,$id]);AuditLogger::log($db,'ویرایش کالای انبار','inventory_item',$id,null,['name'=>$name,'quantity'=>$qty,'reorder_level'=>$reorder,'track_stock'=>$track]);return;}
  $db->prepare('INSERT INTO inventory_items(item_code,name,unit,current_quantity,reorder_level,track_stock,status,created_at) VALUES(?,?,?,?,?,?,\'active\',NOW())')->execute([$code?:null,$name,$unit,$qty,$reorder,$track]);$id=(int)$db->lastInsertId();AuditLogger::log($db,'ایجاد کالای انبار','inventory_item',$id,null,['name'=>$name,'quantity'=>$qty,'reorder_level'=>$reorder,'track_stock'=>$track]);
 }
 private static function adjustStock(PDO $db):void{
  $id=(int)($_POST['inventory_item_id']??0);$delta=(float)str_replace(',','.',(string)($_POST['quantity_change']??0));$note=trim((string)($_POST['note']??''));if($id<1||abs($delta)<0.000001)throw new \RuntimeException('کالا و مقدار تغییر موجودی را وارد کنید.');
  $userId=(int)(Auth::user()['id']??0);$db->beginTransaction();try{
   $s=$db->prepare('SELECT * FROM inventory_items WHERE id=? FOR UPDATE');$s->execute([$id]);$item=$s->fetch();if(!$item)throw new \RuntimeException('کالای انبار پیدا نشد.');
   if((int)$item['track_stock']!==1)throw new \RuntimeException('این کالا برای کنترل موجودی علامت نخورده است.');
   $old=(float)$item['current_quantity'];$new=max(0,$old+$delta);$actual=$new-$old;
   $db->prepare('UPDATE inventory_items SET current_quantity=?,updated_at=NOW() WHERE id=?')->execute([$new,$id]);
   $db->prepare("INSERT INTO inventory_movements(inventory_item_id,quantity_change,movement_type,note,created_by,created_at) VALUES(?,?, 'adjustment',?,?,NOW())")->execute([$id,$actual,$note?:null,$userId?:null]);
   $db->commit();AuditLogger::log($db,'اصلاح موجودی','inventory_item',$id,['quantity'=>$old],['quantity'=>$new,'change'=>$actual]);
  }catch(\Throwable $e){$db->rollBack();throw$e;}
 }
 private static function saveRule(PDO $db):void{
  $productId=(int)($_POST['product_id']??0);$itemId=(int)($_POST['inventory_item_id']??0);$qty=(float)str_replace(',','.',(string)($_POST['quantity_per_order']??0));
  if($productId<1||$itemId<1||$qty<=0)throw new \RuntimeException('محصول، کالای انبار و میزان مصرف در هر سفارش را کامل کنید.');
  $db->prepare("INSERT INTO inventory_product_rules(product_id,inventory_item_id,quantity_per_order,created_at) VALUES(?,?,?,NOW())
    ON DUPLICATE KEY UPDATE quantity_per_order=VALUES(quantity_per_order),updated_at=NOW()")->execute([$productId,$itemId,$qty]);
  AuditLogger::log($db,'ثبت فرمول مصرف انبار','inventory_rule',null,null,['product_id'=>$productId,'inventory_item_id'=>$itemId,'quantity_per_order'=>$qty]);
 }
 private static function deleteRule(PDO $db):void{$id=(int)($_POST['id']??0);if($id<1)throw new \RuntimeException('رابطه مصرف نامعتبر است.');$db->prepare('DELETE FROM inventory_product_rules WHERE id=?')->execute([$id]);AuditLogger::log($db,'حذف فرمول مصرف انبار','inventory_rule',$id);}
 private static function toggleItem(PDO $db):void{$id=(int)($_POST['id']??0);if($id<1)throw new \RuntimeException('کالای انبار نامعتبر است.');$db->prepare('UPDATE inventory_items SET track_stock=IF(track_stock=1,0,1),updated_at=NOW() WHERE id=?')->execute([$id]);}
 private static function friendly(\Throwable $e):string{if(str_contains($e->getMessage(),'Duplicate entry'))return'کد کالای واردشده قبلاً ثبت شده است.';return$e->getMessage();}
}
