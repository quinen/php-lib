<?php
/**
 * Created by PhpStorm.
 * User: laurent.d
 * Date: 1/29/20
 * Time: 2:06 PM
 */

namespace QuinenLib\Utility;


class Strings
{
    public static function startsWith(string $string, string $startString)
    {
        $len = strlen($startString);
        return (substr($string, 0, $len) === $startString);
    }

    public static function strrchr(string $haystack, string $needle, int $pos = 0)
    {
        if ($pos === 0) {
            return \strrchr($haystack, $needle);
        }

        $strrpos = \strrpos($haystack, $needle);

        for ($i = $pos; $i > 0; $i--) {
            $strrpos = strrpos(substr($haystack, 0, $strrpos), $needle);
        }

        if ($strrpos !== false) {
            return substr($haystack, $strrpos);
        }
        return false;

    }
}