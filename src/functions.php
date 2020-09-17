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

if (!function_exists('debug_lite')) {
    /**
     * fonction debug sans aucune dependance externe, a copier coller
     * @param $var
     */
    function debug_lite($var)
    {
        $dbbt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        $info = $dbbt[0]['file'] . '.' . $dbbt[0]['line'];

        $ve = function ($expression, $return = FALSE) {
            $export = var_export($expression, TRUE);
            $patterns = [
                "/array \(/" => '[',
                "/^([ ]*)\)(,?)$/m" => '$1]$2',
                "/=>[ ]?\n[ ]+\[/" => '=> [',
                "/([ ]*)(\'[^\']+\') => ([\[\'])/" => '$1$2 => $3',
            ];
            $export = preg_replace(array_keys($patterns), array_values($patterns), $export);
            if ((bool)$return) return $export; else echo $export;
        };

        try {
            $export = $ve($var, true);
        } catch (\Exception $e) {
            $export = print_r($var, true);
        }

        if (PHP_SAPI === 'cli') {
            echo PHP_EOL . str_pad($info, 80, '_', STR_PAD_LEFT) . PHP_EOL . $export . PHP_EOL;
        } else {
            echo '<pre>' . PHP_EOL . $info . PHP_EOL . $export . PHP_EOL . '</pre>';
        }
    }
}