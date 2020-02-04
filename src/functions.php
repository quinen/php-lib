<?php

use QuinenLib\Utility\Template;

if (!function_exists('template')) {
    /**
     * fonction basique de templating
     * remplace toutes les occurences de {{index}} dans template par sa valeur dans $data
     * si la valeur n'est pas trouvée, alors le template est effacé sans erreurs
     *
     * @param string|array|\Traversable $template
     * @param array|ArrayAccess $data
     * @return string|array|\Traversable
     */
    function template($template, $data)
    {
        $templater = new Template();
        return $templater($template, $data);
    }
}