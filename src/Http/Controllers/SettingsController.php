<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Support\PersianText;
use PDO;

final class SettingsController
{
    public static function categories(PDO $db): array
    {
        $message=null; $error=null;
        try {
            if ($_SERVER['REQUEST_METHOD']==='POST') {
                $action=$_POST['action']??'';
                if ($action==='create_category') {
                    $name=PersianText::normalize($_POST['name']??'');
                    $color=trim((string)($_POST['color']??'#2563eb'));
                    if($name==='') throw new \RuntimeException('نام دسته‌بندی الزامی است.');
                    if(!preg_match('/^#[0-9A-Fa-f]{6}$/',$color)) throw new \RuntimeException('رنگ انتخابی نامعتبر است.');
                    $s=$db->prepare('INSERT INTO product_categories(name,color,sort_order,status,created_at) VALUES(?,?,?,?,NOW())');
                    $s->execute([$name,$color,(int)($_POST['sort_order']??0),($_POST['status']??'active')==='active'?'active':'inactive']);
                    $message='دسته‌بندی با موفقیت ایجاد شد.';
                } elseif($action==='update_category') {
                    $id=(int)($_POST['id']??0);$name=PersianText::normalize($_POST['name']??'');$color=trim((string)($_POST['color']??'#2563eb'));
                    if($id<1||$name==='') throw new \RuntimeException('اطلاعات دسته‌بندی کامل نیست.');
                    $s=$db->prepare('UPDATE product_categories SET name=?,color=?,sort_order=?,status=?,updated_at=NOW() WHERE id=?');
                    $s->execute([$name,$color,(int)($_POST['sort_order']??0),($_POST['status']??'active')==='active'?'active':'inactive',$id]);
                    $message='دسته‌بندی ویرایش شد.';
                } elseif($action==='delete_category') {
                    $id=(int)($_POST['id']??0);
                    $s=$db->prepare('DELETE FROM product_categories WHERE id=?');$s->execute([$id]);$message='دسته‌بندی حذف شد.';
                }
            }
        } catch(\Throwable $e){$error=self::friendly($e);}
        $rows=$db->query('SELECT c.*,COUNT(p.id) product_count FROM product_categories c LEFT JOIN products p ON p.category_id=c.id GROUP BY c.id ORDER BY c.sort_order,c.name')->fetchAll();
        return compact('rows','message','error');
    }

    private static function friendly(\Throwable $e): string {
        $m=$e->getMessage();
        if(str_contains($m,'Duplicate entry')) return 'این دسته‌بندی قبلاً ثبت شده است.';
        return $m;
    }
}
