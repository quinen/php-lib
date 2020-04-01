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

    public static function jsonTrim($stream)
    {
        $firstAccolade = mb_strpos($stream, '{');
        $lastAccolade = mb_strrpos($stream, '}');
        //debug([$stream,$firstAccolade,$lastAccolade,mb_strlen($stream)]);
        if ($firstAccolade !== 0 || $lastAccolade !== (mb_strlen($stream) - 1)) {
            $stream = mb_substr($stream, $firstAccolade, $lastAccolade-1);
        }
        return $stream;
    }

    public static function isStringUTF8($_string)
    {
        return preg_match('%(?:
        [\xC2-\xDF][\x80-\xBF]              # non-overlong 2-byte
        |\xE0[\xA0-\xBF][\x80-\xBF]         # excluding overlongs
        |[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
        |\xED[\x80-\x9F][\x80-\xBF]         # excluding surrogates
        |\xF0[\x90-\xBF][\x80-\xBF]{2}      # planes 1-3
        |[\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
        |\xF4[\x80-\x8F][\x80-\xBF]{2}      # plane 16
        )+%xs', $_string);
    }
}