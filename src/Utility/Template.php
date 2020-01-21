<?php
/**
 * Created by PhpStorm.
 * User: laurent.d
 * Date: 1/21/20
 * Time: 11:35 AM
 */

namespace QuinenLib\Utility;

use Cake\Utility\Hash;

class Template
{

    public function __invoke($template, $data)
    {
        return self::templateArray($template, $data);
    }

    public static function templateArray($template, $data)
    {
        if (is_scalar($template)) {
            return self::templateString($template, $data);
        } else {
            if (is_array($template)) {
                return collection($template)->map(function ($oneTemplate) use ($data) {
                    return self::templateArray($oneTemplate, $data);
                })->toArray();
            } else {
                return $template;
            }
        }
    }

    public static function templateString($string, $data)
    {
        preg_match_all('#\{\{([\w\._]+)\}\}#', $string, $matches);
        $string = collection($matches)->transpose()->reduce(function ($reducer, $match) use ($data) {
            $value = Hash::get($data, $match[1]);
            if (is_array($value)) {
                $value = json_encode($value);
            }
            $reducer = str_replace($match[0], $value, $reducer);
            return $reducer;
        }, $string);
        return $string;
    }
}