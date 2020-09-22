<?php
/**
 * Created by PhpStorm.
 * User: laurent
 * Date: 11/09/20
 * Time: 15:00
 */

namespace QuinenLib\Html;


use QuinenLib\Utility\Strings;

class Bs4
{
    use FormatTrait;

    const THEME_LIGHT = 'light';
    const THEME_DARK = 'dark';

    const VARIANT_PRIMARY = 'primary';

    public $theme = self::THEME_LIGHT;

    private $variants = [
        self::VARIANT_PRIMARY,
        'secondary',
        'success',
        'danger',
        'warning',
        'info',
        'light',
        'dark'
    ];


    public function badge($content, array $options = [])
    {
        $options += [
            'variant' => self::VARIANT_PRIMARY
        ];

        $options = $this->concatValueInOptions($options, 'badge badge-' . $options['variant']);
        unset($options['variant']);

        return (new Tag('span', $content, $options));
    }

    public function menuTitle($title, array $options = [])
    {
        $options += [
            'color' => self::VARIANT_PRIMARY
        ];

        return new Tag(
            'span',
            implode('&nbsp;', array_map(function ($word) use ($options) {
                $firstLetter = mb_strtoupper(mb_substr($word, 0, 1));
                $firstLetter = new Tag(
                    'span',
                    $firstLetter,
                    [
                        'class' => 'font-weight-bold',
                        'style' => 'color:var(--' . $options['color'] . ')'
                    ]);
                $word = $firstLetter . mb_substr($word, 1);
                return $word;
            }, explode(' ', $title))),
            ['title' => $title]
        );


    }

    public function table(array $data, array $maps = [], array $options = [])
    {
        $options += [
            'isTableDark' => ($this->theme === self::THEME_DARK ? true : false),
            'isTableStriped' => true,
            'isTableBordered' => true,
            'isTableBorderless' => false,
            'isTableHover' => true,
            'isTableSm' => true,
            'isTableResponsive' => false,
            'class' => 'table',
        ];

        $options = $this->convertOptionsBooleanToClass($options);

        return (string)(new Table($data, $maps, $options));
    }


    private function concatValueInOptions(array $data, string $value, string $key = 'class')
    {
        if (isset($data[$key])) {
            $data[$key] .= ' ' . $value;
        } else {
            $data[$key] = $value;
        }
        return $data;
    }

    private function convertOptionsBooleanToClass(array $data)
    {
        $return = $data;
        foreach ($data as $key => $value) {
            if ($key === 'class') {
                continue;
            } else if (Strings::startsWith($key, 'is')) {
                unset($return[$key]);
                if ($value) {
                    $class = trim(mb_strtolower(preg_replace(
                        '/([A-Z])/',
                        '-${1}',
                        mb_substr($key, 2)
                    )), '-');
                    $return = $this->concatValueInOptions($return, $class);
                }
            } else {
                $return[$key] = $value;
            }
        }
        return $return;
    }
}