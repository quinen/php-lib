<?php

use QuinenLib\Html\Tag;
use QuinenLib\Legacy\Url;
use QuinenLib\Tools;

echo 'index du dossier demo';

// liste des fichiers a la racine de ce dossier
// genere une liste ul/li avec un lien sur chaque template

Tools::debug(collection(new DirectoryIterator(__DIR__))->filter(function ($file) {
    return !$file->isDot();
})->map(function ($file) {
    $name = str_replace('.php', '', $file->getBasename());
    return Tag::link($name, '/' . $name) . '';
})->toArray());

echo QuinenLib\Html\Tag::vars();