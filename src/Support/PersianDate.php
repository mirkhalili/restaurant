<?php
namespace App\Support;
final class PersianDate {
 public static function format(?string $date): string {
  if(!$date)return '—'; $ts=strtotime($date); if(!$ts)return $date;
  [$y,$m,$d]=array_map('intval',explode('-',date('Y-m-d',$ts))); [$jy,$jm,$jd]=self::toJalali($y,$m,$d);
  $months=['فروردین','اردیبهشت','خرداد','تیر','مرداد','شهریور','مهر','آبان','آذر','دی','بهمن','اسفند'];
  return $jy.'/'.$jm.'/'.$jd.' - '.date('H:i',$ts);
 }
 public static function today(): string { return self::format(date('Y-m-d H:i:s')); }
 private static function toJalali(int $gy,int $gm,int $gd):array {
  $gdm=[0,31,59,90,120,151,181,212,243,273,304,334];$gy2=$gm>2?$gy+1:$gy;
  $days=355666+365*$gy+intdiv($gy2+3,4)-intdiv($gy2+99,100)+intdiv($gy2+399,400)+$gd+$gdm[$gm-1];
  $jy=-1595+33*intdiv($days,12053);$days%=12053;$jy+=4*intdiv($days,1461);$days%=1461;
  if($days>365){$jy+=intdiv($days-1,365);$days=($days-1)%365;}
  $jm=$days<186?1+intdiv($days,31):7+intdiv($days-186,30);$jd=1+($days<186?$days%31:($days-186)%30);
  return [$jy,$jm,$jd];
 }
}