<?php
declare(strict_types=1);
namespace App\Core;
use PDO;
final class Auth{
 public static function start(array $config=[]):void{if(session_status()===PHP_SESSION_ACTIVE)return;$s=$config['session']??[];session_name($s['name']??'restaurant_session');session_set_cookie_params(['lifetime'=>0,'path'=>'/','secure'=>(bool)($s['secure']??true),'httponly'=>true,'samesite'=>'Lax']);session_start();}
 public static function user():?array{self::start();return $_SESSION['user']??null;}
 public static function login(PDO $db,string $identifier,string $password):bool{$q=$db->prepare("SELECT u.*,r.name role_name FROM users u JOIN roles r ON r.id=u.role_id WHERE (u.email=? OR u.username=?) AND u.status='active' LIMIT 1");$q->execute([$identifier,$identifier]);$u=$q->fetch();if(!$u||!password_verify($password,$u['password_hash']))return false;self::start();session_regenerate_id(true);$_SESSION['user']=$u;return true;}
 public static function logout():void{self::start();$_SESSION=[];if(ini_get('session.use_cookies')){$p=session_get_cookie_params();setcookie(session_name(),' ',time()-42000,$p['path'],$p['domain']??'',(bool)$p['secure'],(bool)$p['httponly']);}session_destroy();}
 public static function requireLogin():void{if(!self::user()){header('Location: /?page=login');exit;}}
}