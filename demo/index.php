<?php

use QuinenLib\Mvc\Application;

define('DIR_WWW_ROOT', dirname(__DIR__));

if (!file_exists(dirname(__DIR__) . '/vendor/autoload.php')) {
    die("no vendor/autoload file");
}
require_once dirname(__DIR__) . '/vendor/autoload.php';



//require_once 'views/index.php';
// inpired by https://github.com/wdalmut/simple-mvc
(new Application(__DIR__))->run();
