<?php

namespace QuinenLib\Legacy;

trait Singleton
{
    protected static $_instance = [];

    public static function getInstance()
    {
        $staticClass = static::class;

        if (!isset(static::$_instance[$staticClass])) {
            static::$_instance[$staticClass] = new $staticClass();
        }

        return static::$_instance[$staticClass];
    }
}