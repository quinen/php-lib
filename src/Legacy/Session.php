<?php
/**
 * Created by PhpStorm.
 * User: laurent.d
 * Date: 1/23/20
 * Time: 11:20 AM
 */

namespace QuinenLib\Legacy;

use Cake\Utility\Hash;

class Session
{
    public function read($index = null, $default = null)
    {
        if ($index === null) {
            return $_SESSION;
        }
        return Hash::get($_SESSION, $index, $default);
    }

    public function write($index = null, $value = null)
    {
        return Hash::insert($_SESSION, $index, $value);
    }
}