<?php
/**
 * Created by PhpStorm.
 * User: laurent.d
 * Date: 1/17/20
 * Time: 11:01 AM
 */

namespace QuinenLib\Utility;

/*
 * class to study memory size and time executed
 * */

use Cake\Utility\Hash;
use QuinenLib\Tools;

class Profiler
{
    private static $instance;
    protected $bench = [];

    public static function all()
    {
        return self::getInstance()->read();
    }

    /*
     * doit retourner une string avec le benching
     *
     * */
    protected function read()
    {
        self::bench('_end');
        return $this->table();
    }

    public static function bench($key = null)
    {
        if ($key === null) {
            $dbbt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
            $file = basename(Hash::get($dbbt, '0.file')) . '.' . Hash::get($dbbt, '0.line');
            $method = Hash::get($dbbt, '1.class') . Hash::get($dbbt, '1.type') . Hash::get($dbbt, '1.function');
            $key = $file . ':' . $method;
        }

        self::getInstance()->write($key);
    }

    protected function write($key)
    {
        $this->bench[] = [
            'key' => $key,
            'time' => microtime(true),
            'memory' => memory_get_usage()
        ];
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
            self::bench('_start');
        }
        return self::$instance;
    }

    protected function table()
    {
        $maps = [
            [
                'field' => 'key'
            ],
            [
                'field' => 'time'
            ],
            [
                'field' => 'memory'
            ],
        ];

        // calc values
        $maps = collection($maps)->map(function ($map, $line) {
            $map['calc'] = collection($this->bench)->reduce(function ($reducer, $bench) use ($map, $line) {

                $fieldValue = $bench[$map['field']];
                if (!isset($reducer['min']) || (isset($reducer['min']) && $reducer['min'] > $fieldValue)) {
                    $reducer['min'] = $fieldValue;
                }
                $fieldValue = strlen($fieldValue);
                if (!isset($reducer['maxWidth']) || (isset($reducer['maxWidth']) && $reducer['maxWidth'] < $fieldValue)) {
                    $reducer['maxWidth'] = $fieldValue;
                }
                return $reducer;
            }, ['min' => null, 'maxWidth' => null]);
            return $map;
        })->toArray();

        // head
        $keys = array_keys($this->bench[0]);
        array_unshift($this->bench, array_combine($keys, $keys));

        $dataString = implode(PHP_EOL,
            collection($this->bench)->map(function ($bench, $line) use ($maps) {
                return implode('|', collection($maps)->map(function ($map, $column) use ($bench, $line) {
                    $fieldValue = $bench[$map['field']];

                    if ($line && $map['field'] == 'time') {
                        $fieldValue = round($fieldValue - $map['calc']['min'], 3) . 's';
                    }

                    if ($line && $map['field'] == 'memory') {
                        $fieldValue = Tools::humanFileSize($fieldValue - $map['calc']['min']);
                    }

                    return str_pad($fieldValue, $map['calc']['maxWidth']);
                })->toArray());
            })->toArray()
        );
        return PHP_EOL . $dataString . PHP_EOL;
    }
}