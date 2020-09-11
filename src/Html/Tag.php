<?php

namespace QuinenLib\Html;

use QuinenLib\Tools;
use QuinenLib\Legacy\Url;

class Tag
{
    private $tag;
    private $content;
    private $options;

    public function __construct($tag = "div", $content = null, array $options = [])
    {
        $this->tag = $tag;
        $this->content = $content;
        $this->options = $options + [
                '_isAutoClosed' => false
            ];
    }


    public static function link($label, $href = '/', array $options = [])
    {
        $options = [
                'href' => new Url($href) . ''
            ] + $options;

        return new self('a', $label, $options);
    }

    public static function css($href, array $options = [])
    {
        //  <link rel="stylesheet" href="../public/css/views.css?v6" type="text/css" />
        return new self('link', null, $options + [
                '_isAutoClosed' => true,
                'rel' => 'stylesheet',
                'type' => 'text/css',
                'href' => $href . '?' . date('j')
            ]);
    }

    public static function vars()
    {
        $get = new Tag('pre', 'Get ' . var_export($_GET, true));
        $post = new Tag('pre', 'Post ' . var_export($_POST, true));
        if (isset($_SESSION)) {
            $session = new Tag('pre', 'Session ' . var_export($_SESSION, true));
        } else {
            $session = new Tag('pre', 'Session ', ['style' => 'background-color:#ffcccc;']);
        }

        $cookies = new Tag('pre', 'Cookies ' . var_export($_COOKIE, true));
        $server = new Tag('pre', 'Server ' . var_export($_SERVER, true));
        $constants = new Tag('pre', 'Constants ' . var_export(get_defined_constants(true)['user'], true));
        return new Tag(
            'div',
            $get . $post . $session . $cookies . $server . $constants,
            ['style' => 'display:flex;flex-wrap:wrap']
        );
    }

    public function __toString()
    {
        if ($this->content === null) {
            if ($this->options['_isAutoClosed']) {
                return '<' . $this->tag . rtrim($this->getFormattedOptions()) . '/>';
            } else {
                return '<' . $this->tag . '>';
            }

        } else {
            if (is_array($this->content)) {
                $this->content = json_encode($this->content, JSON_PRETTY_PRINT);
            }
        }
        return '<' . $this->tag . rtrim($this->getFormattedOptions()) . '>' . $this->content . '</' . $this->tag . '>';
    }

    private function getFormattedOptions()
    {
        $arrayOptions = array_map(function ($option, $key) {
            if ($key !== '_isAutoClosed') {
                return $key . '="' . $option . '" ';
            }
        }, $this->options, array_keys($this->options));

        if (count($arrayOptions) > 0) {
            array_unshift($arrayOptions, ' ');
        }

        return implode($arrayOptions);
    }
}