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
    public $options = [
        'constants' => false
    ];
    protected $bench = [];

    protected function __construct()
    {
        $this->write(self::START, false);
    }

    protected function write($key)
    {
        $keyData = [
            'key' => $key,
            'time' => microtime(true),
            'memory' => memory_get_usage(),
        ];


        if ($key === self::START) {
            $keyData['constants'] = get_defined_constants();
        } else {
            if ($this->options['constants']) {
                $keyData['constants'] = array_diff_key(get_defined_constants(), $this->{self::START}['constants']);
            }
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

    public static function all()
    {
        self::bench(self::END);
        return self::getInstance()->read();
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

    protected function read()
    {
        $maps = [
            [
                'field' => 'key',
                'align' => STR_PAD_RIGHT
            ],
            [
                'field' => 'time',
                'format' => function ($f) {
                    return round(($f - $this->{self::START}['time']) * 1000) . ' ms';
                },
                'align' => STR_PAD_LEFT
            ],
            [
                'field' => 'memory',
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
                        $c = collection($f)->map(function ($value, $constant) {
                            return compact('constant', 'value');
                        })->toList();

                        if (!empty($c)) {
                            return $this->table($c, [
                                [
                                    'field' => 'constant',
                                    //'width' => 40,
                                    'align' => STR_PAD_RIGHT,
                                    'alignChar' => '.'
                                ],
                                [
                                    'field' => 'value',
                                    //'width' => 110,
                                    'align' => STR_PAD_RIGHT
                                ]
                            ]);

                        }

                    }
                },
                'align' => STR_PAD_LEFT,
                'hide' => !$this->options['constants']
            ]
        ];

        if (isset($this->{self::END})) {
            $this->bench = array_merge($this->bench, [$this->{self::END}]);
        } else {
            self::bench('_printTable');
        }

        $data = array_merge([$this->{self::START}], $this->bench);

        return $this->table($data, $maps);
    }

    protected function table($data, $maps = [])
    {
        $head = $this->initTableHead($data, $maps);

        $dataValued = array_map(function ($bench) use (&$maps) {
            $lines = array_reduce(array_keys($maps), function ($reducer, $key) use ($bench, &$maps) {

                // on s'assure que la map est un array
                if (is_scalar($maps[$key])) {
                    $maps[$key] = [
                        'field' => $maps[$key]
                    ];
                }

                $map = $maps[$key];

                $map += [
                    'field' => false,
                    'format' => false,
                    'align' => STR_PAD_BOTH,
                    'alignChar' => ' ',
                    'hide' => false
                ];

                if ($map['hide']) {
                    return $reducer;
                }

                // value
                $fieldValue = $bench[$map['field']];

                // format
                if ($map['format'] && is_callable($map['format'])) {
                    $fieldValue = $map['format']($fieldValue, $bench);
                }

                $fieldValueLength = strlen($fieldValue);

                if (!is_scalar($fieldValue) || is_bool($fieldValue)) {
                    $fieldValue = var_export($fieldValue, true);
                }

                if (!isset($map['width']) || $map['width'] < $fieldValueLength) {
                    $maps[$key]['width'] = $fieldValueLength;
                }

                if (!isset($maps[$key]['alignChar'])) {
                    $maps[$key]['alignChar'] = $map['alignChar'];
                }


                //return str_pad($fieldValue, $map['width'] + 2, $map['alignChar'], $align);
                $reducer[] = $fieldValue;
                return $reducer;
            }, []);
            return $lines;
        }, $data);

        $dataFormatted = array_map(function ($line) use ($maps) {
            return '|' . implode('|', array_map(function ($column, $columnIndex) use ($maps) {
                    return str_pad($column, ($maps[$columnIndex]['width'] + 2) % 160, $maps[$columnIndex]['alignChar'],
                        $maps[$columnIndex]['align']);
                }, $line, array_keys($line))) . '|';
        }, $dataValued);

        $headFormatted = '|' . implode('|',
                array_reduce(array_keys($head), function ($reducer, $columnIndex) use ($maps, $head) {
                    $column = $head[$columnIndex];

                    $map = [
                            'align' => STR_PAD_BOTH,
                            'alignChar' => '_',
                        ] + $maps[$columnIndex];
                    if ($column) {
                        $reducer[] = str_pad($column, ($map['width'] + 2) % 160, $map['alignChar'], $map['align']);
                    }
                    return $reducer;

                }, [])) . '|';

        return PHP_EOL . $headFormatted . PHP_EOL . implode(PHP_EOL, $dataFormatted) . PHP_EOL;
    }

    private function initTableHead($data, &$maps = [])
    {
        if (empty($maps)) {
            $keys = array_keys($data[0]);
            $maps = array_combine($keys, $keys);
        }

        return array_map(function ($map, $key) use (&$maps) {
            // on s'assure que la map est un array
            if (is_scalar($map)) {
                $map = $maps[$key] = [
                    'label' => $maps[$key],
                    'field' => $maps[$key],
                    'hide' => false
                ];
            } else {
                $map += [
                    'label' => $map['field'],
                    'hide' => false
                ];
            }
            if (!$map['hide']) {
                return $map['label'];
            } else {
                return false;
            }


        }, $maps, array_keys($maps));
    }

    public static function options($options)
    {
        self::getInstance()->options = $options + self::getInstance()->options;
    }
}