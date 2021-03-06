<?php
/**
 * Created by PhpStorm.
 * User: laurent
 * Date: 10/09/20
 * Time: 18:33
 */

namespace QuinenLib\Html;


use QuinenLib\Arrays\ContentOptionsTrait;
use QuinenLib\Map\Map;

class Table
{
    use ContentOptionsTrait;
    use FormatTrait;

    /** @var Map $maps * */
    private $maps;
    private $data;
    private $options;

    public function __construct($data, array $maps = [], array $options = [])
    {

        $optionsMaps = [
            'caller' => $this
        ];

        if (isset($options['maps'])) {
            $options['maps'] += $optionsMaps;
        } else {
            $options['maps'] = $optionsMaps;
        }

        $options += [
        ];

        $this->setMaps($maps, $options['maps']);
        unset($options['maps']);

        $this->setData($data);
        $this->options = $options;
    }

    public function setMaps($maps, $options)
    {
        $this->maps = new Map($maps, $options);
    }

    /**
     * @param $data
     */
    public function setData($data)
    {
        $this->data = $this->maps->transformArrays($data);
    }

    private function getHead()
    {
        $ths = [];
        $ths = array_map(function ($map) {
            return new Tag('th', $map['label'][0], $map['label'][1]);
        }, $this->maps->toArray());
        $thead = new Tag('tr', PHP_EOL . "\t" . implode(PHP_EOL . "\t", $ths) . PHP_EOL);
        return new Tag('thead', $thead);
    }

    private function getBody()
    {
        $trs = array_map(function ($line) {
            list($line, $lineOptions) = $this->getContentOptions($line);
            return new Tag('tr', $this->getLine($line) . PHP_EOL . "\t", $lineOptions);
        }, $this->data);
        $tbody = PHP_EOL . "\t" . implode(PHP_EOL . "\t", $trs) . PHP_EOL;
        return new Tag('tbody', $tbody);
    }

    private function getLine($line)
    {
        $tds = array_map(function ($cell) {
            //debug_lite($cell);
            list($cell, $cellOptions) = $this->getContentOptions($cell);
            //debug_lite([$cell,$cellOptions]);
            return new Tag('td', $cell, $cellOptions);
        }, $line);
        return PHP_EOL . "\t\t" . implode(PHP_EOL . "\t\t", $tds);
    }

    public function __toString()
    {
        return (string)new Tag('table', $this->getHead() . $this->getBody(), $this->options);
    }


}