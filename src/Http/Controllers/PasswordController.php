<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use PDO;

final class PasswordController
{
    public static function forgot(PDO $db): array
    {
        $message=null;$error=null;
        if($_SERVER['REQUEST_METHOD']==='POST'){
            $email=trim((string)($_POST['email']??''));
            if(!filter_var($email,FILTER_VALIDATE_EMAIL)) $error='ایمیل معتبر وارد کنید.';
            else {
                $s=$db->prepare("SELECT id,display_name,email FROM users WHERE email=? AND status='active' LIMIT 1");$s->execute([$email]);$u=$s->fetch();
                if($u){
                    $token=bin2hex(random_bytes(32));$hash=hash('sha256',$token);
                    $db->prepare('DELETE FROM password_resets WHERE user_id=?')->execute([(int)$u['id']]);
                    $db->prepare('INSERT INTO password_resets(user_id,token_hash,expires_at,created_at) VALUES(?,?,DATE_ADD(NOW(),INTERVAL 60 MINUTE),NOW())')->execute([(int)$u['id'],$hash]);
                    $cfg=require __DIR__.'/../../../config/config.php';$base=rtrim((string)($cfg['app_url']??''),'/');
                    $link=$base.'/?page=reset-password&token='.urlencode($token);
                    $from=$cfg['mail']['from']??'no-reply@localhost';
                    $subject='بازیابی رمز عبور سامانه اتوماسیون رستوران';
                    $body="سلام ".($u['display_name']?:'کاربر')."\n\nبرای تعیین رمز جدید از لینک زیر استفاده کنید:\n".$link."\n\nاین لینک تا ۶۰ دقیقه معتبر است.";
                    @mail($u['email'],'=?UTF-8?B?'.base64_encode($subject).'?=',"".$body,"From: ".$from."\r\nContent-Type: text/plain; charset=UTF-8\r\n");
                }
                $message='اگر این ایمیل در سامانه ثبت شده باشد، لینک بازیابی رمز به آن ارسال شده است.';
            }
        }
        return compact('message','error');
    }

    public static function reset(PDO $db): array
    {
        $token=trim((string)($_GET['token']??$_POST['token']??''));$message=null;$error=null;
        $valid=false;$userId=0;
        if($token!==''){
            $s=$db->prepare('SELECT user_id FROM password_resets WHERE token_hash=? AND expires_at>NOW() LIMIT 1');$s->execute([hash('sha256',$token)]);$row=$s->fetch();
            if($row){$valid=true;$userId=(int)$row['user_id'];}
        }
        if($_SERVER['REQUEST_METHOD']==='POST'&&$valid){
            $p=(string)($_POST['password']??'');$c=(string)($_POST['password_confirmation']??'');
            if(mb_strlen($p)<8) $error='رمز عبور باید حداقل ۸ کاراکتر باشد.';
            elseif($p!==$c) $error='تکرار رمز عبور با رمز جدید یکسان نیست.';
            else {
                $db->prepare('UPDATE users SET password_hash=?,updated_at=NOW() WHERE id=?')->execute([password_hash($p,PASSWORD_DEFAULT),$userId]);
                $db->prepare('DELETE FROM password_resets WHERE user_id=?')->execute([$userId]);
                $message='رمز عبور با موفقیت تغییر کرد. اکنون می‌توانید وارد شوید.';
                $valid=false;
            }
        } elseif($token!==''&&!$valid) $error='لینک بازیابی نامعتبر یا منقضی شده است.';
        return compact('token','message','error','valid');
    }
}
