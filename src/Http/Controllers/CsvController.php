<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use PDO;
use App\Support\PersianDate;

final class CsvController
{
    public static function customers(PDO $db, array $file): int
    {
        $h = self::open($file);
        $header = self::readRow($h);
        $map = self::header($header);
        if (!$map) {
            fclose($h);
            throw new \RuntimeException('ستون‌های فایل CSV قابل شناسایی نیستند. فایل باید دارای ردیف عنوان باشد.');
        }

        $n = 0;
        $db->beginTransaction();
        try {
            while (($r = self::readRow($h)) !== null) {
                if (!self::hasData($r)) continue;

                $first = self::get($r, $map, ['نام', 'نام کوچک', 'first_name']);
                $last = self::get($r, $map, ['نام خانوادگی', 'last_name']);
                $name = trim($first . ' ' . $last);
                if ($name === '') $name = self::get($r, $map, ['نام و نام خانوادگی', 'نام کامل', 'name']);

                $phone = self::normalizePhone(self::get($r, $map, ['شماره تلفن', 'تلفن', 'شماره تماس', 'phone']));
                if ($phone === '') continue;

                $s = $db->prepare(
                    'INSERT INTO customers(subscription_code,first_name,last_name,name,phone,mobile,membership_date,address,birth_date,created_at)
                     VALUES(?,?,?,?,?,?,?,?,?,NOW())
                     ON DUPLICATE KEY UPDATE
                     subscription_code=VALUES(subscription_code),
                     first_name=VALUES(first_name),
                     last_name=VALUES(last_name),
                     name=VALUES(name),
                     mobile=VALUES(mobile),
                     membership_date=VALUES(membership_date),
                     address=VALUES(address),
                     birth_date=VALUES(birth_date),
                     updated_at=NOW()'
                );

                $s->execute([
                    self::get($r, $map, ['کد اشتراک', 'كد اشتراک', 'subscription_code']) ?: null,
                    $first,
                    $last,
                    $name !== '' ? $name : $phone,
                    $phone,
                    self::normalizePhone(self::get($r, $map, ['تلفن همراه', 'موبایل', 'شماره موبایل', 'mobile'])) ?: null,
                    PersianDate::toGregorian(self::get($r, $map, ['تاریخ عضویت', 'تاريخ عضویت', 'membership_date'])),
                    self::get($r, $map, ['آدرس', 'نشانی', 'address']) ?: null,
                    PersianDate::toGregorian(self::get($r, $map, ['تاریخ تولد', 'تاريخ تولد', 'birth_date']))
                ]);
                $n++;
            }
            $db->commit();
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        } finally {
            fclose($h);
        }

        return $n;
    }

    public static function products(PDO $db, array $file): int
    {
        $h = self::open($file);
        $header = self::readRow($h);
        $map = self::header($header);
        if (!$map) {
            fclose($h);
            throw new \RuntimeException('ستون‌های فایل CSV قابل شناسایی نیستند. فایل باید دارای ردیف عنوان باشد.');
        }

        $n = 0;
        $db->beginTransaction();
        try {
            while (($r = self::readRow($h)) !== null) {
                if (!self::hasData($r)) continue;

                $code = self::get($r, $map, ['کد کالا', 'كد كالا', 'كد کالا', 'product_code']);
                $name = self::get($r, $map, ['نام کالا', 'نام محصول', 'نام', 'name']);
                if ($code === '' || $name === '') continue;

                $priceRaw = self::get($r, $map, ['قیمت واحد', 'قيمت واحد', 'قیمت', 'price']);
                $price = (float) str_replace([',', '٬', ' '], '', self::normalizeDigits($priceRaw));

                $status = mb_strtolower(self::get($r, $map, ['فعال', 'وضعیت', 'status']));
                $status = in_array($status, ['0', 'false', 'inactive', 'غیرفعال'], true) ? 'inactive' : 'active';

                $s = $db->prepare(
                    'INSERT INTO products(product_code,name,price,unit,product_type,status,created_at)
                     VALUES(?,?,?,?,?,?,NOW())
                     ON DUPLICATE KEY UPDATE
                     name=VALUES(name),
                     price=VALUES(price),
                     unit=VALUES(unit),
                     product_type=VALUES(product_type),
                     status=VALUES(status),
                     updated_at=NOW()'
                );

                $s->execute([
                    $code,
                    $name,
                    $price,
                    self::get($r, $map, ['واحد', 'unit']) ?: null,
                    self::get($r, $map, ['نوع کالا', 'نوع محصول', 'product_type']) ?: null,
                    $status
                ]);
                $n++;
            }
            $db->commit();
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        } finally {
            fclose($h);
        }

        return $n;
    }

    private static function open(array $f)
    {
        if (($f['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('فایل CSV معتبر نیست یا بارگذاری نشده است.');
        }
        if (($f['size'] ?? 0) <= 0) {
            throw new \RuntimeException('فایل CSV خالی است.');
        }

        $h = fopen($f['tmp_name'], 'rb');
        if (!$h) throw new \RuntimeException('خواندن CSV ممکن نیست.');
        return $h;
    }

    private static function readRow($h): ?array
    {
        $line = fgets($h);
        if ($line === false) return null;

        $line = self::decode($line);
        $delimiter = self::delimiter($line);
        $row = str_getcsv($line, $delimiter);

        if (isset($row[0])) {
            $row[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $row[0]);
        }

        return $row;
    }

    private static function delimiter(string $line): string
    {
        $counts = [
            ',' => substr_count($line, ','),
            ';' => substr_count($line, ';'),
            "\t" => substr_count($line, "\t"),
            '|' => substr_count($line, '|')
        ];
        arsort($counts);
        $delimiter = array_key_first($counts);
        return ($counts[$delimiter] ?? 0) > 0 ? $delimiter : ',';
    }

    private static function decode(string $value): string
    {
        if (str_starts_with($value, "\xFF\xFE") || str_starts_with($value, "\xFE\xFF")) {
            $converted = @mb_convert_encoding($value, 'UTF-8', 'UTF-16');
            if ($converted !== false) return $converted;
        }

        if (preg_match('//u', $value) === 1) return $value;

        $converted = @mb_convert_encoding($value, 'UTF-8', 'Windows-1256, ISO-8859-6, CP1252');
        return $converted !== false ? $converted : $value;
    }

    private static function header(array $h): array
    {
        $m = [];
        foreach ($h as $i => $v) {
            $key = self::normalizeHeader((string) $v);
            if ($key !== '') $m[$key] = $i;
        }
        return $m;
    }

    private static function normalizeHeader(string $value): string
    {
        $value = preg_replace('/^\xEF\xBB\xBF/', '', $value) ?? $value;
        $value = str_replace(["\xC2\xA0", "\xE2\x80\x8C", "\xE2\x80\x8D"], ' ', $value);
        $value = str_replace(['ي', 'ى', 'ئ'], 'ی', $value);
        $value = str_replace(['ك'], 'ک', $value);
        $value = preg_replace('/\s+/u', ' ', trim($value)) ?? trim($value);
        return mb_strtolower($value);
    }

    private static function get(array $r, array $m, array $names): string
    {
        foreach ($names as $n) {
            $k = self::normalizeHeader($n);
            if (isset($m[$k])) return trim((string) ($r[$m[$k]] ?? ''));
        }
        return '';
    }

    private static function hasData(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') return true;
        }
        return false;
    }

    private static function normalizeDigits(string $value): string
    {
        return strtr($value, [
            '۰'=>'0','۱'=>'1','۲'=>'2','۳'=>'3','۴'=>'4',
            '۵'=>'5','۶'=>'6','۷'=>'7','۸'=>'8','۹'=>'9',
            '٠'=>'0','١'=>'1','٢'=>'2','٣'=>'3','٤'=>'4',
            '٥'=>'5','٦'=>'6','٧'=>'7','٨'=>'8','٩'=>'9'
        ]);
    }

    private static function normalizePhone(string $value): string
    {
        return preg_replace('/\s+/', '', self::normalizeDigits(trim($value))) ?? trim($value);
    }
}
