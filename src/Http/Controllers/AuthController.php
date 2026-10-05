<?php
namespace App\Http\Controllers; use App\Core\Auth; use PDO;
final class AuthController{public static function login(PDO $db):void{if($_SERVER['REQUEST_METHOD']==='POST'){if(Auth::login($db,trim($_POST['email']??''),$_POST['password']??'')){header('Location: /');exit;}$error='ایمیل یا رمز عبور صحیح نیست.';}require __DIR__.'/../../../views/login.php';}public static function logout():void{Auth::logout();header('Location: /?page=login');exit;}}
