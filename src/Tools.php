<?php

namespace QuinenLib;

use QuinenLib\Html\Tag;

class Tools
{

    /**
     *  show debug on screen
     */
    public static function debug($var)
    {
        $dbbt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $traces = array_map(function ($t) {
            $fileShort = substr(strrchr($t['file'], '/'), 1);
            $classTypeFunction = $t['class'] . $t['type'] . $t['function'];

            return $fileShort . '.' . $t['line'] . ':' . $classTypeFunction;
        }, $dbbt);
        $trace = implode(' | ', $traces);
        echo new Tag('pre', new Tag('b', $trace) . PHP_EOL . var_export($var, true));
    }
}