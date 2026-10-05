<?php
declare(strict_types=1);

require __DIR__ . '/src/Core/Database.php';
require __DIR__ . '/src/Core/Auth.php';
require __DIR__ . '/src/Core/Router.php';
require __DIR__ . '/src/Support/Response.php';
require __DIR__ . '/src/Support/PersianDate.php';
require __DIR__ . '/src/Http/Controllers/AuthController.php';
require __DIR__ . '/src/Http/Controllers/DashboardController.php';
require __DIR__ . '/src/Http/Controllers/CsvController.php';
require __DIR__ . '/src/Http/Controllers/RestaurantController.php';
require __DIR__ . '/src/Http/Controllers/CrudController.php';

$config = require __DIR__ . '/config/config.php';
$db = App\Core\Database::get($config);
App\Core\Auth::start($config);

$page = App\Core\Router::page();

if (str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/api/v1/')) {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if ($path === '/api/v1/health') App\Support\Response::json(['status'=>'ok','version'=>trim((string)file_get_contents(__DIR__.'/VERSION'))]);
    App\Support\Response::json(['error'=>'Not Found'],404);
}

if ($page === 'login') { App\Http\Controllers\AuthController::login($db); exit; }
if ($page === 'logout') App\Http\Controllers\AuthController::logout();

App\Core\Auth::requireLogin();
$user = App\Core\Auth::user();

if ($page === 'orders') {
    $data = App\Http\Controllers\RestaurantController::page($db);
    $title = 'ثبت سفارش و فاکتور';
    $page = 'orders';
    extract($data);
    ob_start(); require __DIR__.'/views/orders.php'; $content=ob_get_clean();
    require __DIR__.'/views/layout.php'; exit;
}

if ($page === 'customers') {
    $data = App\Http\Controllers\CrudController::customers($db);
    $title = 'مشتریان';
    $page = 'customers';
    extract($data);
    ob_start(); require __DIR__.'/views/customers.php'; $content = ob_get_clean();
    require __DIR__.'/views/layout.php'; exit;
}

if ($page === 'products') {
    $data = App\Http\Controllers\CrudController::products($db);
    $title = 'منو و محصولات';
    $page = 'products';
    extract($data);
    ob_start(); require __DIR__.'/views/products.php'; $content = ob_get_clean();
    require __DIR__.'/views/layout.php'; exit;
}

} elseif (in_array($page,['inventory','kitchen','reports','users','settings','audit'],true)) {
    ob_start(); require __DIR__.'/views/module-placeholder.php'; $content=ob_get_clean();
    $title='ماژول'; require __DIR__.'/views/layout.php'; exit;
} else {
    $data=App\Http\Controllers\DashboardController::index($db);
    ob_start(); extract($data); require __DIR__.'/views/dashboard.php'; $content=ob_get_clean();
    $title='داشبورد'; require __DIR__.'/views/layout.php'; exit;
}


