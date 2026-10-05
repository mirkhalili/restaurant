<?php
declare(strict_types=1);

namespace App\Support;

final class PersianDate
{
    private const MONTHS = ['فروردین','اردیبهشت','خرداد','تیر','مرداد','شهریور','مهر','آبان','آذر','دی','بهمن','اسفند'];

    public static function today(): string
    {
        return self::format(date('Y-m-d H:i:s'), true);
    }

    public static function format(?string $date, bool $withTime = true): string
    {
        if (!$date) return '—';
        $ts = strtotime($date);
        if (!$ts) return self::digits((string)$date);
        [$jy,$jm,$jd] = self::toJalali((int)date('Y',$ts),(int)date('n',$ts),(int)date('j',$ts));
        $value = sprintf('%04d/%02d/%02d', $jy, $jm, $jd);
        if ($withTime) $value .= ' - ' . date('H:i', $ts);
        return self::digits($value);
    }

    public static function toGregorian(?string $value): ?string
    {
        $value = trim(self::latinDigits((string)$value));
        if ($value === '') return null;
        $parts = preg_split('/[\/-]/', $value);
        if (count($parts) !== 3) return null;
        [$jy,$jm,$jd] = array_map('intval', $parts);
        if ($jy < 1200 || $jm < 1 || $jm > 12 || $jd < 1 || $jd > 31) return null;
        [$gy,$gm,$gd] = self::fromJalali($jy,$jm,$jd);
        return sprintf('%04d-%02d-%02d', $gy,$gm,$gd);
    }

    public static function digits(string $value): string
    {
        return strtr($value, ['0'=>'۰','1'=>'۱','2'=>'۲','3'=>'۳','4'=>'۴','5'=>'۵','6'=>'۶','7'=>'۷','8'=>'۸','9'=>'۹']);
    }

    public static function latinDigits(string $value): string
    {
        return strtr($value, ['۰'=>'0','۱'=>'1','۲'=>'2','۳'=>'3','۴'=>'4','۵'=>'5','۶'=>'6','۷'=>'7','۸'=>'8','۹'=>'9']);
    }

    private static function toJalali(int $gy,int $gm,int $gd): array
    {
        $gdm=[0,31,59,90,120,151,181,212,243,273,304,334];
        $gy2=$gm>2?$gy+1:$gy;
        $days=355666+365*$gy+intdiv($gy2+3,4)-intdiv($gy2+99,100)+intdiv($gy2+399,400)+$gd+$gdm[$gm-1];
        $jy=-1595+33*intdiv($days,12053); $days%=12053;
        $jy+=4*intdiv($days,1461); $days%=1461;
        if($days>365){$jy+=intdiv($days-1,365);$days=($days-1)%365;}
        $jm=$days<186?1+intdiv($days,31):7+intdiv($days-186,30);
        $jd=1+($days<186?$days%31:($days-186)%30);
        return [$jy,$jm,$jd];
    }

    private static function fromJalali(int $jy,int $jm,int $jd): array
    {
        $jy += 1595;
        $days = -355668 + 365*$jy + intdiv($jy,33)*8 + intdiv(($jy%33)+3,4) + $jd;
        $days += $jm <= 6 ? ($jm-1)*31 : (($jm-7)*30+186);
        $gy = 400*intdiv($days,146097);
        $days %= 146097;
        if ($days > 36524) {
            $gy += 100*intdiv(--$days,36524);
            $days %= 36524;
            if ($days >= 365) $days++;
        }
        $gy += 4*intdiv($days,1461);
        $days %= 1461;
        if ($days > 365) { $gy += intdiv($days-1,365); $days = ($days-1)%365; }
        $gd = $days + 1;
        $leap = ($gy%4===0 && $gy%100!==0) || ($gy%400===0);
        $gdm=[31,$leap?29:28,31,30,31,30,31,31,30,31,30,31];
        $gm=1;
        while ($gd>$gdm[$gm-1]) { $gd-=$gdm[$gm-1]; $gm++; }
        return [$gy,$gm,$gd];
    }
}
