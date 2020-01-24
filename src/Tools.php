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

    /**
     * return json encoding with function non-stringed
     * @param $data
     * @param int $jsonOptions
     * @return string
     */
    public static function jsonEncodeWithFunction($data, $jsonOptions = 0)
    {
        $separator = ':"function(';
        $glue = ':function(';
        $json = json_encode($data, $jsonOptions);
        $jsonExploded = explode($separator, $json);
        $jsonExploded = collection($jsonExploded)->map(function ($str, $index) {

            if ($index) {
                $pos = strrpos($str, ';}"');
                return substr($str, 0, $pos + 2) . substr($str, $pos + 3);
            }

            return $str;
        })->toArray();

        return implode($glue, $jsonExploded);

    }
}