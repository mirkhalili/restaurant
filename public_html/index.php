<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/Core/Database.php';
require dirname(__DIR__) . '/src/Core/Auth.php';
require dirname(__DIR__) . '/src/Core/Router.php';
require dirname(__DIR__) . '/src/Support/Response.php';
require dirname(__DIR__) . '/src/Http/Controllers/AuthController.php';
require dirname(__DIR__) . '/src/Http/Controllers/DashboardController.php';
require dirname(__DIR__) . '/src/Http/Controllers/CrudController.php';

$config = require dirname(__DIR__) . '/config/config.php';
$db = App\Core\Database::get($config);
App\Core\Auth::start();

$page = App\Core\Router::page();

if (str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/api/v1/')) {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    if ($path === '/api/v1/health') {
        App\Support\Response::json([
            'status' => 'ok',
            'version' => trim((string) file_get_contents(dirname(__DIR__) . '/VERSION')),
        ]);
    }

    App\Support\Response::json(['error' => 'Not Found'], 404);
}

if ($page === 'login') {
    App\Http\Controllers\AuthController::login($db);
    exit;
}

if ($page === 'logout') {
    App\Http\Controllers\AuthController::logout();
}

App\Core\Auth::requireLogin();
$user = App\Core\Auth::user();

if ($page === 'orders') {
    $heading = 'سفارش‌ها';
    $createUrl = '/?page=orders';
    $action = 'create_order';
    $data = App\Http\Controllers\CrudController::orders($db);
} elseif ($page === 'customers') {
    $heading = 'مشتریان';
    $createUrl = '/?page=customers';
    $action = 'create_customer';
    $data = App\Http\Controllers\CrudController::customers($db);
} elseif ($page === 'products') {
    $heading = 'غذاها و محصولات';
    $createUrl = '/?page=products';
    $action = 'create_product';
    $data = App\Http\Controllers\CrudController::products($db);
} elseif (in_array($page, ['inventory', 'kitchen', 'reports', 'users', 'settings', 'audit'], true)) {
    ob_start();
    require dirname(__DIR__) . '/views/module-placeholder.php';
    $content = ob_get_clean();
    $title = 'ماژول';
    require dirname(__DIR__) . '/views/layout.php';
    exit;
} else {
    $data = App\Http\Controllers\DashboardController::index($db);
    ob_start();
    extract($data);
    require dirname(__DIR__) . '/views/dashboard.php';
    $content = ob_get_clean();
    $title = 'داشبورد';
    require dirname(__DIR__) . '/views/layout.php';
    exit;
}

extract($data);
ob_start();
require dirname(__DIR__) . '/views/crud.php';
$content = ob_get_clean();
$title = $heading;
require dirname(__DIR__) . '/views/layout.php';
