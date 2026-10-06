<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use PDO;
use App\Support\PersianText;

final class CrudController
{
    public static function customers(PDO $db): array
    {
        $message=null;$error=null;
        try {
            if($_SERVER['REQUEST_METHOD']==='POST') {
                switch($_POST['action']??'') {
                    case 'create_customer': RestaurantController::addCustomer($db); $message='مشتری با موفقیت ثبت شد.'; break;
                    case 'update_customer': RestaurantController::updateCustomer($db); $message='مشخصات مشتری ویرایش شد.'; break;
                    case 'delete_customer': RestaurantController::deleteCustomer($db); $message='مشتری حذف شد.'; break;
                    case 'import_customers_csv': $message=CsvController::customers($db,$_FILES['csv']??[]).' مشتری وارد/به‌روزرسانی شد.'; break;
                }
            }
        } catch(\Throwable $e){$error=self::friendly($e);}
        $rows=$db->query('SELECT * FROM customers ORDER BY id DESC LIMIT 300')->fetchAll();
        $edit=null;
        if(isset($_GET['edit'])) {
            $s=$db->prepare('SELECT * FROM customers WHERE id=? LIMIT 1');$s->execute([(int)$_GET['edit']]);$edit=$s->fetch()?:null;
        }
        return compact('rows','message','error','edit');
    }

    public static function products(PDO $db): array
    {
        $message=null;$error=null;
        try {
            if($_SERVER['REQUEST_METHOD']==='POST') {
                switch($_POST['action']??'') {
                    case 'create_product': RestaurantController::addProduct($db); $message='محصول با موفقیت ثبت شد.'; break;
                    case 'update_product': RestaurantController::updateProduct($db); $message='محصول ویرایش شد.'; break;
                    case 'delete_product': RestaurantController::deleteProduct($db); $message='محصول حذف شد.'; break;
                    case 'bulk_delete_products': $message=RestaurantController::bulkDeleteProducts($db).' محصول حذف شد.'; break;
                    case 'bulk_assign_product_type': $message=RestaurantController::bulkAssignProductType($db).' محصول به نوع انتخاب‌شده منتقل شد.'; break;
                    case 'import_products_csv': $message=CsvController::products($db,$_FILES['csv']??[]).' محصول وارد/به‌روزرسانی شد.'; break;
                }
            }
        } catch(\Throwable $e){$error=self::friendly($e);}
        $rows=$db->query('SELECT p.*,c.name AS category_name,c.color AS category_color FROM products p LEFT JOIN product_categories c ON c.name=p.product_type ORDER BY p.id DESC LIMIT 300')->fetchAll();
        $edit=null;
        if(isset($_GET['edit'])) {
            $s=$db->prepare('SELECT * FROM products WHERE id=? LIMIT 1');$s->execute([(int)$_GET['edit']]);$edit=$s->fetch()?:null;
        }
        $categories=$db->query("SELECT * FROM product_categories WHERE status='active' ORDER BY sort_order,name")->fetchAll();
        return compact('rows','message','error','edit','categories');
    }

    public static function orders(PDO $db): array
    {
        return ['rows'=>$db->query('SELECT * FROM orders ORDER BY id DESC LIMIT 100')->fetchAll()];
    }

    private static function friendly(\Throwable $e): string
    {
        $m=$e->getMessage();
        if(str_contains($m,'Duplicate entry')) return 'رکورد تکراری است؛ شماره تلفن، کد اشتراک یا کد کالا را بررسی کنید.';
        if(str_contains($m,'foreign key')||str_contains($m,'Cannot delete')) return 'این رکورد در سفارش‌های ثبت‌شده استفاده شده و قابل حذف نیست.';
        return $m;
    }
}
