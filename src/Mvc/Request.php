<?php
/**
 * Created by PhpStorm.
 * User: laurent.d
 * Date: 1/13/20
 * Time: 8:54 AM
 */

namespace QuinenLib\Mvc;


use Cake\Utility\Hash;

class Request
{
    public function getQuery($index = null, $default = null)
    {
        if ($index === null) {
            return $_GET;
        }
        return Hash::get($_GET, $index, $default);
    }

    public function getData($index = null, $default = null)
    {
        if ($index === null) {
            return $_POST;
        }
        return Hash::get($_POST, $index, $default);
    }

    public function getServer($index = null,$default = null){
        if ($index === null) {
            return $_SERVER;
        }
        return Hash::get($_SERVER, $index, $default);
    }
}