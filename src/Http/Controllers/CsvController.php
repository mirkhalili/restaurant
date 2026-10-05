<?php
namespace App\Http\Controllers;
use PDO;
use App\\Support\\PersianDate;
final class CsvController {
 public static function customers(PDO $db,array $file): int {
  $h=self::open($file);$map=self::header(fgetcsv($h,0,',')?:[]);$n=0;
  $db->beginTransaction();
  try{while(($r=fgetcsv($h,0,','))!==false){if(!array_filter($r,'strlen'))continue;$first=self::get($r,$map,['نام','نام کوچک','first_name']);$last=self::get($r,$map,['نام خانوادگی','last_name']);$name=trim($first.' '.$last);if($name==='')$name=self::get($r,$map,['نام و نام خانوادگی','name']);$phone=self::get($r,$map,['شماره تلفن','تلفن','phone']);if($phone==='')continue;$s=$db->prepare('INSERT INTO customers(subscription_code,first_name,last_name,name,phone,mobile,membership_date,address,birth_date,created_at) VALUES(?,?,?,?,?,?,?,?,?,NOW()) ON DUPLICATE KEY UPDATE first_name=VALUES(first_name),last_name=VALUES(last_name),name=VALUES(name),mobile=VALUES(mobile),membership_date=VALUES(membership_date),address=VALUES(address),birth_date=VALUES(birth_date),updated_at=NOW()');$s->execute([self::get($r,$map,['كد اشتراک','کد اشتراک','subscription_code'])?:null,$first,$last,$name,$phone,self::get($r,$map,['تلفن همراه','موبایل','mobile'])?:null,PersianDate::toGregorian(self::get($r,$map,['تاريخ عضویت','تاریخ عضویت','membership_date'])),self::get($r,$map,['آدرس','address'])?:null,PersianDate::toGregorian(self::get($r,$map,['تاريخ تولد','تاریخ تولد','birth_date']))]);$n++;}$db->commit();}catch(\Throwable $e){$db->rollBack();throw $e;}fclose($h);return $n;
 }
 public static function products(PDO $db,array $file): int {
  $h=self::open($file);$map=self::header(fgetcsv($h,0,',')?:[]);$n=0;$db->beginTransaction();
  try{while(($r=fgetcsv($h,0,','))!==false){if(!array_filter($r,'strlen'))continue;$code=self::get($r,$map,['كد كالا','کد کالا','product_code']);$name=self::get($r,$map,['نام کالا','نام','name']);if($code===''||$name==='')continue;$price=(float)str_replace(',','',self::get($r,$map,['قيمت واحد','قیمت واحد','price']));$status=mb_strtolower(self::get($r,$map,['فعال','status']));$status=in_array($status,['0','false','inactive','غیرفعال'],true)?'inactive':'active';$s=$db->prepare('INSERT INTO products(product_code,name,price,unit,product_type,status,created_at) VALUES(?,?,?,?,?,?,NOW()) ON DUPLICATE KEY UPDATE name=VALUES(name),price=VALUES(price),unit=VALUES(unit),product_type=VALUES(product_type),status=VALUES(status),updated_at=NOW()');$s->execute([$code,$name,$price,self::get($r,$map,['واحد','unit']),self::get($r,$map,['نوع کالا','product_type']),$status]);$n++;}$db->commit();}catch(\Throwable $e){$db->rollBack();throw $e;}fclose($h);return $n;
 }
 private static function open(array $f){if(($f['error']??UPLOAD_ERR_NO_FILE)!==UPLOAD_ERR_OK)throw new \RuntimeException('فایل CSV معتبر نیست.');$h=fopen($f['tmp_name'],'rb');if(!$h)throw new \RuntimeException('خواندن CSV ممکن نیست.');return $h;}
 private static function header(array $h):array{$m=[];foreach($h as $i=>$v){$v=preg_replace('/^\\xEF\\xBB\\xBF/','',trim((string)$v));$m[mb_strtolower($v)]=$i;}return $m;}
 private static function get(array $r,array $m,array $names):string{foreach($names as $n){$k=mb_strtolower($n);if(isset($m[$k]))return trim((string)($r[$m[$k]]??''));}return '';}
}