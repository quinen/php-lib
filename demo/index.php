<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

echo "index du dossier demo";

include('templates/template.php');


echo '<pre>' . var_export($_SERVER, true) . '</pre>';
