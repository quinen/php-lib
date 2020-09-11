<?php

namespace QuinenLib\Map;

use QuinenLib\Arrays\ContentOptionsTrait;
use QuinenLib\Html\Tag;

/**
 * Class Map
 * re-ecriture de map trait + table + csv
 * sans aucune dependance a cakephp
 * @package QuinenLib\Map
 */
class Map
{
    use ContentOptionsTrait;

    protected $maps;

    protected $options = [
        // champs par default qui doivent etres presents
        'defaults' => [],
        // normalization
        'contentOptions' => ['label', 'field'],
        // proprieté a disptacher dans les options en commun dans les champs indiqués
        'commons' => [
            'class' => ['label', 'field']
        ],
        // see setOptionLabel
        'label' => null,
        // see setOptionFormat
        'format' => null,
        // caller
        'caller' => null
    ];

    protected $defaults = [
        'label' => true,
        'field' => false,
        'format' => true,
        'hide' => false
    ];

    /**
     * Map constructor.
     * @param array $maps
     */
    public function __construct(array $maps = [], array $options = [])
    {
        $this->setOptions($options);
        $this->init($maps);
    }

    /**
     * @param array $data
     * @return Map
     */
    public static function fromData(array $data)
    {
        $maps = array_keys($data);
        return new self($maps);
    }

    public static function get($data, $field, $default = null)
    {
        if (isset($data[$field])) {
            return $data[$field];
        }
        return $default;
    }

    public function toArray()
    {
        return $this->maps;
    }

    public function setOptions($options)
    {
        $this->setOptionLabel();
        $this->setOptionFormat();
        $this->setOptionCaller();
        $this->options = $options + $this->options;
    }

    public function setOptionLabel($label = null)
    {
        if ($label === null) {
            $label = function ($fields) {
                if (is_array($fields)) {
                    $fields = implode(' | ', $fields);
                }
                return ucwords($fields);
            };
        }
        $this->options['label'] = $label;
    }

    public function setOptionFormat($format = null)
    {
        if ($format === null) {
            $format = function ($map) {
                return $map;
            };
        }
        $this->options['format'] = $format;
    }

    /**
     * @param null $caller
     */
    public function setOptionCaller($caller = null)
    {
        if ($caller === null) {
            $caller = $this;
        }
        $this->options['caller'] = $caller;
    }

    public function init($maps)
    {
        $this->maps = $maps;
        $this->normalize();
    }

    /**
     * hydrate
     *
     */
    private function normalize()
    {
        $maps = [];
        // we have an array in $this->>maps
        foreach ($this->maps as $index => $map) {
            if ($map === null) {
                $map = [
                    'label' => false,
                    'field' => false,
                    'format' => false,
                ];
            } elseif ($map === false) {
                // skip element, reason we use a foreach an not an array_map
                continue;
            } elseif (is_scalar($map)) {
                $map = ['field' => $map];
            }
            $map += $this->defaults + $this->options['defaults'];

            // normalization
            foreach ($this->options['contentOptions'] as $contentOption) {
                if (isset($map[$contentOption])) {
                    $map[$contentOption] = $this->getContentOptions($map[$contentOption]);
                }
            }
            // ne pas normalizer format : closure, options 1 a n, ... etc ...

            // commons options 1 > n
            foreach ($this->options['commons'] as $common => $keys) {
                if (isset($map[$common])) {
                    foreach ($keys as $key) {
                        if (isset($map[$key])) {
                            $map[$key][1][$common] = $map[$common];
                        }
                    }
                }

            }

            // auto label
            if ($map['label'][0] === true) {
                if ($map['field'][0] === false || !is_callable($this->options['label'])) {
                    $map['label'][0] = $index;
                } else {
                    $map['label'][0] = $this->options['label']($map['field'][0]);
                }
            }

            // auto format
            if ($map['format'][0] === true) {
                if ($map['field'][0] === false || !is_callable($this->options['format'])) {
                    continue;
                }
                $map = $this->options['format']($map);
            }

            if (is_string($map['format'])) {
                debug_lite($map);
                $methodName = 'format' . ucfirst($map['format']);
                debug_lite($methodName);
                debug_lite(method_exists($this->options['caller'], $methodName));
                var_dump($this->options['caller']);
                if (method_exists($this->options['caller'], $methodName)) {
                    $map['format'] = [[$this->options['caller']], $methodName];
                }
                debug_lite($map);
            }
            $maps[] = $map;
        }
        $this->maps = $maps;
    }


    public function transformArray($data)
    {
        $line = [];
        foreach ($this->maps as $map) {
            $map = \template($map, $data);
            //debug_lite($map);
            if ($map['hide']) {
                continue;
            }

            $this->checkContentOptions($map['field']);

            list($field, $fieldOptions) = $map['field'];

            $value = $this->getValueFromField($field, $data);

            // options based on a function
            if ($fieldOptions instanceof \Closure) {
                $fieldOptions = $fieldOptions($value);
            }

            $format = $this->getFormatFromValue($value, $map['format']);

            if (!is_scalar($format)) {
                $format = new Tag('pre', trim(\json_encode($format, JSON_PRETTY_PRINT), "{}[]\n"));
            }

            $line[] = [$format, $fieldOptions];
        }
        return $line;
    }

    public function transformArrays(array $data, $options = [])
    {
        //debug_lite($data);
        return array_map(function ($line) use ($options) {

            $lineTransformed = $this->transformArray($line);
            //debug_lite([$line,$lineTransformed]);
            if ($options instanceof \Closure) {
                $options = $options($line);
            }
            return [$lineTransformed, $options];
        }, $data);
    }

    private function getValueFromField($field, $data)
    {
        if ($field === false) {
            return false;
        }

        if (is_array($field)) {
            return array_map(function ($oneField) use ($data) {
                return $this->getValueFromField($oneField, $data);
            }, $field);
        }

        if ($field === null) {
            return $data;
        }
        return self::get($data, $field);
    }

    private function getFormatFromValue($value, $format = false)
    {
        if (
            $format && is_callable($format) ||
            (is_array($format) && isset($format[0]) && is_callable($format[0]))
        ) {
            if ($format instanceof \Closure) {
                $callable = $format;
                $format = [];
            } else {
                if (is_string($format)) {
                    $callable = $format;
                    $format = [];
                } else {
                    // c'est un array avec N options probables
                    $callable = array_shift($format);
                    /* @var array $format */
                }
            }

            // format : array with all the parameters

            array_unshift($format, $value);

            $value = \call_user_func_array($callable, $format);
        }
        return $value;
    }


}