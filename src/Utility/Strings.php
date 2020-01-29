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
    public static function startsWith($string, $startString)
    {
        $len = strlen($startString);
        return (substr($string, 0, $len) === $startString);
    }
}