<?php
namespace App\Helpers;

class RandomString
{
    public static function numeric(int $length): string
    {
        $numbers = '0123456789';
        return substr(str_shuffle(str_repeat($numbers, $length)), 0, $length);
    }

    public static function numericSecure(int $length): string
    {
        $number = "";
        for ($x = 1; $x <= $length; $x++) {
            // Set each digit
            $number .= random_int(0, 9);
        }
        return $number;
    }
}
