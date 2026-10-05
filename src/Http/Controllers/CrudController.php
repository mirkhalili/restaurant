<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use PDO;

final class CrudController
{
    public static function customers(PDO $db): array
    {
        $message=null;$error=null;
        try {
            if($_SERVER['REQUEST_METHOD']==='POST') {
                switch($_POST['action']??'') {
                    case 'create_customer': RestaurantController::addCustomer($db); $message='مشتری با موفقیت ثبت شد.'; break;
                    case 'import_customers_csv': $message=CsvController::customers($db,$_FILES['csv']??[]).' مشتری وارد/به‌روزرسانی شد.'; break;
                }
            }
        } catch(\Throwable $e){$error=self::friendly($e);}
        $rows=$db->query('SELECT * FROM customers ORDER BY id DESC LIMIT 300')->fetchAll();
        return compact('rows','message','error');
    }

    public static function products(PDO $db): array
    {
        $message=null;$error=null;
        try {
            if($_SERVER['REQUEST_METHOD']==='POST') {
                switch($_POST['action']??'') {
                    case 'create_product': RestaurantController::addProduct($db); $message='محصول با موفقیت ثبت شد.'; break;
                    case 'import_products_csv': $message=CsvController::products($db,$_FILES['csv']??[]).' محصول وارد/به‌روزرسانی شد.'; break;
                }
            }
        } catch(\Throwable $e){$error=self::friendly($e);}
        $rows=$db->query('SELECT * FROM products ORDER BY id DESC LIMIT 300')->fetchAll();
        return compact('rows','message','error');
    }

    public static function orders(PDO $db): array
    {
        return ['rows'=>$db->query('SELECT * FROM orders ORDER BY id DESC LIMIT 100')->fetchAll()];
    }

    private static function friendly(\Throwable $e): string
    {
        $m=$e->getMessage();
        return str_contains($m,'Duplicate entry') ? 'رکورد تکراری است؛ شماره تلفن، کد اشتراک یا کد کالا را بررسی کنید.' : $m;
    }
}
