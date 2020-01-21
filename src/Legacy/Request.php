<?php
/**
 * Created by PhpStorm.
 * User: laurent.d
 * Date: 1/13/20
 * Time: 8:54 AM
 */

namespace QuinenLib\Legacy;


use Cake\Utility\Hash;

class Request
{
    public function getQuery($index = null)
    {
        if ($index === null) {
            return $_GET;
        }
        return Hash::get($_GET, $index);
    }

}