<?php

use QuinenLib\Utility\Template;

if (!function_exists('template')) {
    function template($template, $data)
    {
        $templater = new Template();
        return $templater($template, $data);
    }
}