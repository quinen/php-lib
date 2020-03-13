<?php
/**
 * Created by PhpStorm.
 * User: laurent
 * Date: 13/03/20
 * Time: 17:39
 */

namespace QuinenLib\Mvc;


class View
{
    private $basePath;

    public function __construct($basePath)
    {
        $this->basePath = $basePath;
    }
}