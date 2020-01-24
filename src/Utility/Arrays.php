<?php
/**
 * Created by PhpStorm.
 * User: laurent.d
 * Date: 1/24/20
 * Time: 3:55 PM
 */

namespace QuinenLib\Utility;


class Arrays
{
    /**
     * @param array $data
     * @return array
     */
    public static function filterEmptyExceptZero(array $data = [])
    {
        return collection($data)->filter(function ($value) {
            if ($value === null || $value === '') {
                return false;
            } elseif (is_scalar($value)) {
                return strlen(trim($value));
            }
            return true;
        })->toArray();
    }
}