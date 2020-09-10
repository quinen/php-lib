<?php
/**
 * Created by PhpStorm.
 * User: laurent
 * Date: 10/09/20
 * Time: 18:33
 */

namespace QuinenLib\Html;


use QuinenLib\Map\Map;

class Table
{
    private $maps;
    private $data;

    public function __construct(array $data, array $maps = [], array $options = [])
    {
        $this->maps = new Map($maps);
        $this->data = $this->maps->transformArrays($data);
    }

    private function getHead(){
        $ths = [];
        $ths = array_map(function($map){
            return new Tag('th',$map['label'][0],$map['label'][1]);
        },$this->maps->toArray());
        $thead = new Tag('tr',implode(PHP_EOL,$ths));
        return new Tag('thead',$thead);
    }

    private function getBody(){

    }

    public function __toString()
    {

        //debug_lite($this->data);
        return (string)$this->getHead();
        return print_r($this->data,true);
        return \json_encode($this->data);
    }
}