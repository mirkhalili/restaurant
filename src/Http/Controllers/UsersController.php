<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Core\Auth;
use App\Support\PersianText;
use PDO;

final class UsersController
{
    private static function isManager(): bool
    {
        $u=Auth::user();
        return in_array($u['role_name']??'', ['مدیر سیستم','مدیر رستوران'], true);
    }

    public static function users(PDO $db): array
    {
        if(!self::isManager()) throw new \RuntimeException('دسترسی این بخش فقط برای مدیر مجاز است.');
        $message=null;$error=null;
        try{
            if($_SERVER['REQUEST_METHOD']==='POST'){
                $action=$_POST['action']??'';
                if($action==='create_user') self::create($db,$message);
                elseif($action==='update_user') self::update($db,$message);
                elseif($action==='toggle_user') self::toggle($db,$message);
            }
        }catch(\Throwable $e){$error=self::friendly($e);}
        $rows=$db->query("SELECT u.id,u.email,u.display_name,u.mobile,u.status,u.created_at,r.name role_name
                          FROM users u JOIN roles r ON r.id=u.role_id ORDER BY u.id DESC LIMIT 300")->fetchAll();
        $roles=$db->query("SELECT id,name FROM roles ORDER BY id")->fetchAll();
        $edit=null;
        if(isset($_GET['edit'])){
            $s=$db->prepare('SELECT id,email,display_name,mobile,status,role_id FROM users WHERE id=? LIMIT 1');
            $s->execute([(int)$_GET['edit']]); $edit=$s->fetch()?:null;
        }
        return compact('rows','roles','edit','message','error');
    }

    public static function profile(PDO $db): array
    {
        $message=null;$error=null;$u=Auth::user();
        try{
            if($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action']??'')==='update_profile'){
                $id=(int)($u['id']??0);
                $email=trim((string)($_POST['email']??''));
                $name=PersianText::normalize($_POST['display_name']??'');
                $mobile=self::digits($_POST['mobile']??'');
                $password=(string)($_POST['password']??'');
                if($id<1||!filter_var($email,FILTER_VALIDATE_EMAIL)) throw new \RuntimeException('ایمیل معتبر وارد کنید.');
                if($name==='') throw new \RuntimeException('نام نمایشی الزامی است.');
                if($password!=='' && mb_strlen($password)<8) throw new \RuntimeException('رمز عبور جدید باید حداقل ۸ کاراکتر باشد.');
                if($password!==''){
                    $s=$db->prepare('UPDATE users SET email=?,display_name=?,mobile=?,password_hash=?,updated_at=NOW() WHERE id=?');
                    $s->execute([$email,$name,$mobile?:null,password_hash($password,PASSWORD_DEFAULT),$id]);
                }else{
                    $s=$db->prepare('UPDATE users SET email=?,display_name=?,mobile=?,updated_at=NOW() WHERE id=?');
                    $s->execute([$email,$name,$mobile?:null,$id]);
                }
                $s=$db->prepare("SELECT u.*,r.name role_name FROM users u JOIN roles r ON r.id=u.role_id WHERE u.id=?");
                $s->execute([$id]); $_SESSION['user']=$s->fetch()?:$_SESSION['user'];
                $u=Auth::user(); $message='پروفایل با موفقیت به‌روزرسانی شد.';
            }
        }catch(\Throwable $e){$error=self::friendly($e);}
        return compact('u','message','error');
    }

    private static function create(PDO $db, ?string &$message): void
    {
        $email=trim((string)($_POST['email']??''));
        $name=PersianText::normalize($_POST['display_name']??'');
        $mobile=self::digits($_POST['mobile']??'');
        $role=(int)($_POST['role_id']??0);
        $password=(string)($_POST['password']??'');
        if(!filter_var($email,FILTER_VALIDATE_EMAIL)||$name===''||$role<1||mb_strlen($password)<8) throw new \RuntimeException('ایمیل، نام نمایشی، نقش و رمز عبور حداقل ۸ کاراکتری الزامی است.');
        $s=$db->prepare('INSERT INTO users(role_id,email,display_name,mobile,password_hash,status,created_at) VALUES(?,?,?,?,?,"active",NOW())');
        $s->execute([$role,$email,$name,$mobile?:null,password_hash($password,PASSWORD_DEFAULT)]);
        $message='کاربر جدید با موفقیت ایجاد شد.';
    }

    private static function update(PDO $db, ?string &$message): void
    {
        $id=(int)($_POST['id']??0);$email=trim((string)($_POST['email']??''));
        $name=PersianText::normalize($_POST['display_name']??'');$mobile=self::digits($_POST['mobile']??'');
        $role=(int)($_POST['role_id']??0);$status=($_POST['status']??'active')==='active'?'active':'inactive';
        $password=(string)($_POST['password']??'');
        if($id<1||!filter_var($email,FILTER_VALIDATE_EMAIL)||$name===''||$role<1) throw new \RuntimeException('اطلاعات کاربر کامل نیست.');
        if($password!==''){
            if(mb_strlen($password)<8) throw new \RuntimeException('رمز عبور جدید باید حداقل ۸ کاراکتر باشد.');
            $s=$db->prepare('UPDATE users SET role_id=?,email=?,display_name=?,mobile=?,password_hash=?,status=?,updated_at=NOW() WHERE id=?');
            $s->execute([$role,$email,$name,$mobile?:null,password_hash($password,PASSWORD_DEFAULT),$status,$id]);
        }else{
            $s=$db->prepare('UPDATE users SET role_id=?,email=?,display_name=?,mobile=?,status=?,updated_at=NOW() WHERE id=?');
            $s->execute([$role,$email,$name,$mobile?:null,$status,$id]);
        }
        if((int)(Auth::user()['id']??0)===$id){
            $s=$db->prepare("SELECT u.*,r.name role_name FROM users u JOIN roles r ON r.id=u.role_id WHERE u.id=?");
            $s->execute([$id]); $_SESSION['user']=$s->fetch()?:$_SESSION['user'];
        }
        $message='کاربر با موفقیت ویرایش شد.';
    }

    private static function toggle(PDO $db, ?string &$message): void
    {
        $id=(int)($_POST['id']??0);
        if($id===(int)(Auth::user()['id']??0)) throw new \RuntimeException('حساب کاربری جاری را نمی‌توان غیرفعال کرد.');
        $s=$db->prepare("UPDATE users SET status=IF(status='active','inactive','active'),updated_at=NOW() WHERE id=?");
        $s->execute([$id]); $message='وضعیت کاربر تغییر کرد.';
    }

    private static function digits(string $v): string
    {
        return strtr(trim($v),['۰'=>'0','۱'=>'1','۲'=>'2','۳'=>'3','۴'=>'4','۵'=>'5','۶'=>'6','۷'=>'7','۸'=>'8','۹'=>'9','٠'=>'0','١'=>'1','٢'=>'2','٣'=>'3','٤'=>'4','٥'=>'5','٦'=>'6','٧'=>'7','٨'=>'8','٩'=>'9']);
    }

    private static function friendly(\Throwable $e): string
    {
        $m=$e->getMessage();
        if(str_contains($m,'Duplicate entry')) return 'این ایمیل قبلاً برای کاربر دیگری ثبت شده است.';
        return $m;
    }
}
