<?php

// liste des fichiers a la racine de ce dossier
// genere une liste ul/li avec un lien sur chaque template

use QuinenLib\Html\Tag;

echo implode(' | ',collection(new DirectoryIterator(__DIR__))->filter(function ($file) {
    /** @var DirectoryIterator $file */
    return !$file->isDot();
})->map(function ($file) {
    /** @var SplFileObject $file */
    $name = str_replace('.php', '', $file->getBasename());
    return Tag::link($name, '/' . $name) . '';
})->toArray());