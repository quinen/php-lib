<?php
/**
 * Created by PhpStorm.
 * User: laurent
 * Date: 18/09/20
 * Time: 11:12
 */

namespace QuinenLib\Html;


use QuinenLib\Utility\Arrays;
use QuinenLib\Utility\Strings;

class FontAwesome5
{
    private const STYLE_SOLID = 'fas';

    private $isFixedWidth = true;

    private $name;

    private $options;

    private $rotates = [
        90 => 'rotate-90',
        180 => 'rotate-180',
        270 => 'rotate-270',
        'h' => 'flip-horizontal',
        'v' => 'flip-vertical',
        'b' => 'flip-both'
    ];

    private $rotate = false;

    private $sizes = ['xs', 'sm', 'lg', 2, 3, 4, 5, 6, 7, 8, 9, 10];

    private $size = false;

    private $styles = [
        // solid
        self::STYLE_SOLID => [],
        // regular
        'far' => [],
        // light
        'fal' => [],
        // duotone
        'fad' => [],
        // brand
        'fab' => ['chrome', 'firefox', 'safari']
    ];

    private $style = [];


    /**
     * FontAwesome5 constructor.
     *
     * @param $name can be user or fas-user
     * @param array $options
     */
    public function __construct($name, array $options = [])
    {
        $options += [
            'size' => $this->size,
            'isFixedWidth' => $this->isFixedWidth,
            'rotate' => $this->rotate
        ];

        $this->setNameAndStyleFromName($name);
        $this->size = $options['size'];
        unset($options['size']);
        $this->isFixedWidth = $options['isFixedWidth'];
        unset($options['isFixedWidth']);
        $this->rotate = $options['rotate'];
        unset($options['rotate']);

        $this->options = $options;
    }

    private function setNameAndStyleFromName($name)
    {

        $names = explode(' ', $name);
        foreach ($names as $k => $name) {
            // if name start with a style, then set style and name
            // else set juste the name and style to solid
            $startWithStyle = array_reduce(array_keys($this->styles), function ($r, $style) use ($name) {
                if (!$r) {
                    if (Strings::startsWith($name, $style . '-')) {
                        $r = $style;
                    }
                }
                return $r;
            }, false);

            if ($startWithStyle) {
                $this->name[$k] = mb_substr($name, 4);
                $this->style[$k] = $startWithStyle;
            } else {
                $this->name[$k] = $name;
                $this->style[$k] = $this->getDefaultStyleFromName($name);
            }
        }
    }

    public function __toString()
    {
        $return = '';
        foreach ($this->name as $k => $name) {
            $options = [
                    'class' => trim(implode(' ', [
                        $this->style[$k],
                        ' fa-' . $name,
                        $this->getSize(),
                        ($this->isFixedWidth ? 'fa-fw' : ''),
                        $this->getRotate()
                    ]))
                ] + $this->options;

            $return .= (string)new Tag('i', '', $options);
        }
        return $return;

    }

    private function getDefaultStyleFromName($name)
    {
        foreach ($this->styles as $style => $icons) {
            if (in_array($name, $icons)) {
                return $style;
            }
        }
        return self::STYLE_SOLID;
    }

    private function getSize()
    {
        if ($this->size) {
            $size = (is_integer($this->size) ? $this->size . 'x' : $this->size);
            return 'fa-' . $size;
        }
    }

    private function getRotate()
    {
        if ($this->rotate) {
            $rotate = $this->rotates[$this->rotate];
            return 'fa-' . $rotate;
        }
    }
}