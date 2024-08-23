<?php
namespace App\Helpers;
class RandomString
{
    public static function numeric(int $length): string
    {
        $numbers = '0123456789';
        return substr(str_shuffle(str_repeat($numbers, $length)), 0, $length);
    }
}
