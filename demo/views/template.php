<?php

$data = [
    'toto' => 'oooo',
    'titi' => [
        'tata' => 'aaaa'
    ]
];

$template = '| {{toto}} | {{titi.tata}} | {{titi.toto}} |';

echo '<pre>' . var_export([$data, $template, template($template, $data)], true) . '</pre>';

$template2 = [
    'titi' => ' valeur de titi : {{titi}}',
    'toto' => ' valeur de toto : {{toto}}'
];

echo '<pre>' . var_export([$data, $template2, template($template2, $data)], true) . '</pre>';