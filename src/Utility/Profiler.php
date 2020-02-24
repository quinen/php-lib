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
use QuinenLib\Legacy\Singleton;
use QuinenLib\Tools;

class Profiler
{
    use Singleton;

    const START = '_start';
    const END = '_end';

    protected $bench = [];
    protected $keyLengthMax = 0;

    protected function __construct()
    {
        $this->write(self::START, false);
    }

    protected function write($key)
    {
        if (($strlenKey = \strlen($key)) && $this->keyLengthMax < $strlenKey) {
            $this->keyLengthMax = $strlenKey;
        }

        $keyData = [
            'key' => $key,
            'time' => microtime(true),
            'memory' => memory_get_usage(),
        ];

        if ($key === self::START) {
            $keyData['constants'] = get_defined_constants();
        } else {
            $keyData['constants'] = array_diff_key(get_defined_constants(), $this->{self::START}['constants']);
        }


        if (in_array($key, [self::START, self::END])) {
            $this->{$key} = $keyData;
        } else {
            $this->bench[] = $keyData;
        }

    }


    /*
     * doit retourner une string avec le benching
     *
     * */

    public static function all($options = [])
    {
        self::bench(self::END);
        return self::getInstance()->read($options);
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

    protected function read($options = [])
    {
        return $this->table($options);
    }

    protected function table($options = [])
    {
        $options += [
            'showConstants' => false
        ];

        $maps = [
            [
                'field' => 'key',
                'width' => $this->keyLengthMax,
                'align' => STR_PAD_RIGHT
            ],
            [
                'field' => 'time',
                'width' => 6,
                'format' => function ($f) {
                    return round(($f - $this->{self::START}['time']) * 1000) . ' ms';
                },
                'align' => STR_PAD_LEFT
            ],
            [
                'field' => 'memory',
                'width' => 8,
                'format' => function ($f) {
                    return Tools::humanFileSize($f - $this->{self::START}['memory']);
                },
                'align' => STR_PAD_LEFT
            ],
            [
                'field' => 'constants',
                'format' => function ($f, $d) {
                    if (in_array($d['key'], [self::START, self::END])) {
                        return count($f);
                    } else {
                        return $f;
                    }
                },
                'align' => STR_PAD_LEFT,
                'hide' => !$options['showConstants']
            ]
        ];

        if (isset($this->{self::END})) {
            $this->bench = array_merge($this->bench, [$this->{self::END}]);
        } else {
            self::bench('_printTable');
        }

        $bench = array_merge([$this->{self::START}], $this->bench);

        // head
        $keys = array_keys($bench[0]);
        array_unshift($bench, array_combine($keys, $keys));

        $dataString = implode(PHP_EOL,
            collection($bench)->map(function ($bench, $line) use ($maps) {
                $lines = collection($maps)->map(function ($map) use ($bench, $line) {

                    $map += [
                        'field' => false,
                        'format' => false,
                        'width' => strlen($map['field']),
                        'align' => STR_PAD_BOTH,
                        'alignChar' => ' ',
                        'hide' => false
                    ];

                    // entete
                    if (!$line) {
                        $bench[$map['field']] = $map['field'];
                        $map = [
                                'format' => false,
                                'align' => STR_PAD_BOTH,
                                'alignChar' => '_'
                            ] + $map;
                    }

                    if ($map['hide']) {
                        return;
                    }

                    // value
                    $fieldValue = $bench[$map['field']];

                    // format
                    if ($map['format'] && is_callable($map['format'])) {
                        $fieldValue = $map['format']($fieldValue, $bench);
                    }

                    if (!is_scalar($fieldValue)) {
                        $fieldValue = var_export($fieldValue, true);
                    }

                    $align = STR_PAD_BOTH;
                    if (isset($map['align']) && in_array($map['align'], [STR_PAD_LEFT, STR_PAD_RIGHT, STR_PAD_BOTH])) {
                        $align = $map['align'];
                    }
                    // align

                    return str_pad($fieldValue, $map['width'] + 2, $map['alignChar'], $align);
                });
                return '|' . implode('|', $lines->toArray()) . '|';
            })->toArray()
        );
        return PHP_EOL . $dataString . PHP_EOL;
    }
}