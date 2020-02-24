<?php

namespace QuinenLib\Legacy;

trait Singleton
{
    protected static $_instance = [];

    protected function __construct()
    {

    }

    /**
     * @return $this
     */
    final public static function getInstance()
    {
        $staticClass = self::class;
        if (!isset(self::$_instance[$staticClass])) {
            self::$_instance[$staticClass] = new $staticClass();
        }
        return self::$_instance[$staticClass];
    }

    final protected function __clone()
    {
    }
}