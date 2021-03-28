<?php

namespace QuinenLib;

use Bramus\Ansi\Ansi;
use Bramus\Ansi\ControlSequences\EscapeSequences\Enums\SGR;
use QuinenLib\Html\Tag;
use QuinenLib\Utility\Strings;


class Tools
{

    /**
     *  show debug on screen
     */
    public static function debug($var)
    {
        $dbbt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $traces = array_map(function ($t) {
            $fileShort = substr(Strings::strrchr($t['file'], '/', 1), 1);
            $classTypeFunction = \template('{{class}}{{type}}{{function}}', $t);
            return $fileShort . '.' . $t['line'] . ':' . $classTypeFunction;
        }, $dbbt);
        $trace = implode(' | ', $traces);

        if (PHP_SAPI === 'cli') {
            (new Ansi())
                ->color([SGR::COLOR_BG_YELLOW, SGR::COLOR_FG_BLACK])
                ->text($trace)
                ->nostyle()->lf()->cr()
                ->color([SGR::COLOR_BG_BLACK, SGR::COLOR_FG_YELLOW])
                ->text(var_export($var, true))
                ->nostyle()->lf()->cr()->bell();
        } else {
            echo new Tag(
                'pre',
                new Tag(
                    'div',
                    $trace,
                    ['style' => 'background-color:#FF0;color:#000;']
                ) . var_export($var, true),
                [
                    'style' => 'background-color:#EEE;border:1px solid #DDD;border-radius:0em;padding:0em;'
                ]
            );
        }
    }

    /**
     * return json encoding with function non-stringed
     * @param $data
     * @param int $jsonOptions
     * @return string
     */
    public static function jsonEncodeWithFunction($data, $jsonOptions = 0)
    {
        $separator = ':"function(';
        $glue = ':function(';
        $json = json_encode($data, $jsonOptions);
        $jsonExploded = explode($separator, $json);
        $jsonExploded = collection($jsonExploded)->map(function ($str, $index) {

            if ($index) {
                $pos = strrpos($str, ';}"');
                return substr($str, 0, $pos + 2) . substr($str, $pos + 3);
            }

            return $str;
        })->toArray();

        return implode($glue, $jsonExploded);

    }

    public static function humanFileSize($bytes, $decimals = 1)
    {
        $factor = floor((strlen($bytes) - 1) / 3);
        if ($factor > 0) {
            $sz = 'kMGTP';
        }
        return sprintf("%.{$decimals}f", $bytes / (1024 ** $factor)) . ' ' . @$sz[$factor - 1] . 'o';
    }

    public static function errorHandler($errno = -1, $errstr = '', $errfile = '', $errline = -1, array $errcontext = [])
    {
        $endsWith = function ($haystack, $needle) {
            $length = strlen($needle);
            return $length > 0 ? substr($haystack, -$length) === $needle : true;
        };

        $startsWith = function ($haystack, $needle) {
            return strpos($haystack, $needle) === 0;
        };

        unset($errcontext['dbhost']);
        unset($errcontext['dblogin']);
        unset($errcontext['dbpassword']);
        unset($errcontext['dbname']);

        $errors = ['error', 'warning', 'parse', 'notice', 'core_error', 'core_warning', 'compile_error', 'compile_warning',
            'user_error', 'user_warning', 'user_notice', 'strict', 'recoverable_error', 'deprecated', 'user_deprecated', 'all'];
        $errorCode = $errors[log($errno) / log(2)];

        //Undefined index
        $colors = [
            'error' => 'red',
            'warning' => 'orange',
            'notice' => 'blue'
        ];
        $errorColor = 'white';
        foreach ($colors as $errorType => $color) {
            if ($endsWith($errorCode, $errorType)) {
                $errorColor = $color;
                break;
            }
        }

        $e = new \Exception();
        $exception = $e->getTraceAsString();

        $return = '<div style="background: black ;color: white;border-radius: 1em;margin: 1em;padding: 1em">
<div style="display: flex;justify-content:space-between;font-weight: bolder;background:' . $errorColor . ';padding: 1em">
<span style="">' . $errfile . ' : ' . $errline . '</span>
<span style="text-align:right">' . $errorCode . ' : ' . $errstr . '</span>
</div>

<div>
<pre>' . print_r($errcontext, true) . '</pre>
<pre>' .$exception . '</pre>
</div>

</div>';
        echo $return;
    }
}