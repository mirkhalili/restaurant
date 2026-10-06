<?php
namespace App\Http\Controllers;
use App\Core\Auth;use App\Support\AuditLogger;use PDO;
final class AuthController{
 public static function login(PDO $db):void{if($_SERVER['REQUEST_METHOD']==='POST'){if(Auth::login($db,trim((string)($_POST['identifier']??'')),(string)($_POST['password']??''))){AuditLogger::log($db,'ورود به سامانه','auth',null,null,['identifier'=>trim((string)($_POST['identifier']??''))]);header('Location: /');exit;}$error='نام کاربری/ایمیل یا رمز عبور صحیح نیست.';}require __DIR__.'/../../../views/login.php';}
 public static function logout(PDO $db):void{AuditLogger::log($db,'خروج از سامانه','auth');Auth::logout();header('Location: /?page=login');exit;}
}