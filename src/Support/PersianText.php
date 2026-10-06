<?php
declare(strict_types=1);

namespace App\Support;

final class PersianText
{
    public static function normalize(?string $value): string
    {
        $value = (string)$value;
        $value = str_replace(['ي','ى'], 'ی', $value);
        $value = str_replace(['ك'], 'ک', $value);
        $value = str_replace(["\xC2\xA0","\xE2\x80\x8C","\xE2\x80\x8D"], ' ', $value);
        return preg_replace('/\s+/u', ' ', trim($value)) ?? trim($value);
    }
}
