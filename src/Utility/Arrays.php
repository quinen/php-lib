<?php
/**
 * Created by PhpStorm.
 * User: laurent.d
 * Date: 1/24/20
 * Time: 3:55 PM
 */

namespace QuinenLib\Utility;

use Cake\ORM\Query;
use Cake\Utility\Hash;

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

    public static function copyFrom($array, $maps)
    {

        if ($array instanceof Query) {
            $array = $array->toArray();
        }

        return collection($maps)->reduce(function ($reducer, $to, $from) use ($array) {
            if (is_integer($from)) {
                $from = $to;
            }
            return Hash::insert($reducer, $to, Hash::get($array, $from));
        }, []);
    }
}