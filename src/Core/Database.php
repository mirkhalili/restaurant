<?php
namespace App\Core; use PDO;
final class Database { private static ?PDO $pdo=null; public static function get(array $c):PDO{if(self::$pdo)return self::$pdo;$d="mysql:host={$c['db']['host']};port={$c['db']['port']};dbname={$c['db']['name']};charset=utf8mb4";return self::$pdo=new PDO($d,$c['db']['user'],$c['db']['pass'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]);}}
