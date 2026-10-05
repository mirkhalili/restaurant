<?php
namespace App\Core; final class Router{public static function page():string{return preg_replace('/[^a-z0-9_-]/i','',$_GET['page']??'dashboard')?:'dashboard';}}
