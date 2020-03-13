<?php


use QuinenLib\Legacy\Url;

$url = new Url();
\QuinenLib\Tools::debug([
    $url,
    $url->parse(),
    $url . '',
]);