<?php
/**
 * Created by PhpStorm.
 * User: laurent.d
 * Date: 1/21/20
 * Time: 3:26 PM
 */

namespace QuinenLib\Arrays;

use Cake\Core\Configure;
use Cake\ORM\Entity;
use Cake\Utility\Hash;

trait MapTrait
{
    use CurrentContextTrait;
    use ContentOptionsTrait;

    abstract public function getMapFormat($map);

    abstract public function getMapLabel($field);

    /**
     * @return array
     */
    public function getMapCommonOptions()
    {
        return [];
    }

    /**
     * normalize maps in a loop of array with label, field,
     *
     * @param array $maps
     * @param $row
     * @return array
     */
    protected function normalizeMaps(array $maps, $row = [], $options = [])
    {
        $optionsDefault = [
            'callbackClass' => $this,
            'callbackFormatPrefix' => 'format',
        ];

        $options += $optionsDefault;

        if ($row instanceof Entity) {
            $this->setCurrentContext($row->getSource());
            $row = $row->toArray();
        } elseif (empty($row)) {
            // ok car rien a en tirer
        } elseif ($this->getCurrentContext() !== $this->getDefaultContext()) {
            // ok car settings externe particulier
        } else {
            debug([
                'les données ne sont pas une Entité, veuillez ne pas renvoyer d\'array sauf cas particuliers (subContext, stats), dans ce cas veuillez presiser le context a false ou au plugin.controller correspondant',
                $this->getCurrentContext(),
                $this->getDefaultContext(),
                $row,
            ]);

        }

        if (empty($maps)) {
            $maps = array_keys($row);
        }

        return collection($maps)->map(function ($map, $index) use ($options) {
            if ($map === null) {
                $map = [
                    'label' => false,
                    'field' => false,
                    'format' => false,
                ];
            } elseif ($map === false) {
                $map = ['hide' => true];
            } elseif (is_scalar($map)) {
                $map = [
                    'field' => $map
                ];
            }

            $mapDefault = [
                // entete des données
                'label' => true,
                // donnée
                'field' => false,
                // formatage
                'format' => true,
                // caché ?
                'hide' => false,

                // tableOptions ////////////////////////////////////////////////
                // fusion des lignes si données equivalentes
                'rowspan' => false,
                // gestion d'entete sur 2 ou 3 lignes, voir TableTrait
                //'zone' => false,
                //'group' => false,

                // TODO isSort
            ];

            $mapCommon = $this->getMapCommonOptions();

            $map += $mapDefault + $mapCommon;

            // normalization
            $map['label'] = $this->getContentOptions($map['label']);
            $map['field'] = $this->getContentOptions($map['field']);
            // ne pas normalizer format : closure, options 1 a n, ... etc ...

            if (isset($map['zone'])) {
                $map['zone'] = $this->getContentOptions($map['zone']);
            }

            if (isset($map['group'])) {
                $map['group'] = $this->getContentOptions($map['group']);
            }

            if (Configure::read('debug')) {
                $map = Hash::insert($map, 'label.1.title', $map['field'][0]);
            }

            // dispatch common options
            foreach ($mapCommon as $option => $defaultValue) {
                foreach (['label', 'field', 'group', 'zone'] as $i) {
                    if (isset($map[$i])) {
                        //debug(get_class($this));
                        /* @var array $map [$i] */
                        $map[$i][1] = $this->addClass($map[$i][1], $map[$option], $option);
                    }
                }
            }

            // auto labeling with field
            if (true === $map['label'][0]) {
                if ($map['field'][0] !== false) {
                    $map = Hash::insert($map, 'label.0',
                        $this->getMapLabelFromFieldNormalized($map['field'][0], $options));
                } else {
                    $map = Hash::insert($map, 'label.0', $index);
                }
            }

            // automate format from outside, based on map data
            if ($map['format'] === true) {
                if ($map['field'][0] === false) {
                    return $map;
                }
                $map = ($options['callbackClass'])->getMapFormat($map, $options);
            }

            // string = method inside or native function
            if (is_string($map['format'])) {

                $function = $map['format'];

                if (!empty($options['callbackFormatPrefix'])) {
                    $method = $options['callbackFormatPrefix'] . ucfirst($map['format']);
                } else {
                    $method = $map['format'];
                }

                if (method_exists($options['callbackClass'], $method)) {
                    $map['format'] = [[$options['callbackClass'], $method]];
                } else {
                    $map['format'] = $function;
                }
            }

            return $map;
        })->toArray();
    }

    protected function getMapLabelFromFieldNormalized($field, $options)
    {
        if (is_array($field)) {
            return implode(' &amp; ', array_map(function ($f) use ($options) {
                return $this->getMapLabelFromFieldNormalized($f, $options);
            }, $field));
        }

        return ($options['callbackClass'])->getMapLabel($field, $options);
    }

    protected function transformMapsWithDatas($maps, $datas, $lineOptions = [])
    {
        return collection($datas)->map(function ($line) use ($maps, $lineOptions) {
            $lineTransformed = $this->transformMapsWithLine($maps, $line);
            if ($lineOptions instanceof \Closure) {
                $lineOptions = $lineOptions($line);
            }
            return [$lineTransformed, $lineOptions];
        });
    }

    protected function transformMapsWithLine($maps, $row)
    {
        return collection($maps)->reduce(function ($reducer, $map, $i) use ($row) {

            // template replacing
            $map = \template($map, $row);

            if ($map['hide']) {
                return $reducer;
            }

            $this->checkContentOptions($map['field']);

            // get field path and TD options
            list($field, $fieldOptions) = $map['field'];

            // get value from field
            $value = $this->getMapValueFromField($field, $row);
            // if td formating
            if ($fieldOptions instanceof \Closure) {
                $fieldOptions = $fieldOptions($value);
            }

            $wasArray = is_array($value);
            if ($wasArray) {
                $nbValue = count($value);
            }

            // format value if necessary
            $valueFormatted = $this->getMapValueFormatted($value, $map['format']);
            //debug([$field, $value, $wasArray, $valueFormatted]);
            // fieldOptions returned
            if (
                (is_array($valueFormatted) && !$wasArray) ||
                ($wasArray && is_array($valueFormatted) && $nbValue !== count($valueFormatted))
            ) {
                list($valueFormatted, $fieldOptions) = $valueFormatted;
            }
            // after here, fieldOptions not updated

            // show array
            if (is_array($valueFormatted)) {
                $valueFormatted = '<pre>' . trim(json_encode($valueFormatted, JSON_PRETTY_PRINT), '[]\n') . '</pre>';
            }

            $reducer[] = [$valueFormatted, $fieldOptions];

            return $reducer;

        }, []);
    }

    protected function getMapValueFromField($field, $data = [])
    {
        if ($field === false) {
            return false;
        }

        if (is_array($field)) {
            return array_map(function ($oneField) use ($data) {
                return $this->getMapValueFromField($oneField, $data);
            }, $field);
        }

        if (Configure::read('debug')) {
            if (!(is_array($data) || $data instanceof \ArrayAccess)) {
                debug([$field, $data]);
                return null;
            }
        }
        return Hash::get($data, $field);
    }

    /**
     * @param mixed $value
     * @param bool|string|array $format
     * @return mixed
     */
    protected function getMapValueFormatted($value, $format = false)
    {
        if ($format && (
                is_callable($format) ||
                (is_array($format) && isset($format[0]) && is_callable($format[0]))
            )
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

            array_unshift($format, $value);

            $value = \call_user_func_array($callable, $format);
        }
        return $value;
    }


}