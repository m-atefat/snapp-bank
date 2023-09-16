<?php

namespace App\Helpers;

class Normalizer
{
    public static function toEnglish(string $string): string
    {
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $english = range(0, 9);

        return str_replace($arabic, $english, str_replace($persian, $english, $string));
    }

    public static function toPersian(string $string): string
    {
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $english = range(0, 9);

        return str_replace($english, $persian, $string);
    }
}
