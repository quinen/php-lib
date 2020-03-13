<?php
/**
 * Created by PhpStorm.
 * User: laurent
 * Date: 13/03/20
 * Time: 16:46
 */

namespace QuinenLib\Mvc;


use QuinenLib\Legacy\Request;

class Application extends Container
{

    private $basePath;


    public function __construct($basePath, array $options = [])
    {
        $options += [
            'request' => new Request(),
            'router'    => new Router()
            //'pathViews' => $basePath . '/views',
        ];

        $this->basePath = $basePath;

        $this->add('app', $this);
        $this->add('request', $options['request']);
        $this->add('router', $options['router']);
        //$this->add('view', new View($options['pathViews']));
    }

    public function run()
    {


    }
}