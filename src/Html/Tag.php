<?php

namespace QuinenLib\Html;

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

    public static function css($href, array $options = [])
    {
        //  <link rel="stylesheet" href="../public/css/templates.css?v6" type="text/css" />
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
        $session = new Tag('pre', 'Session ' . var_export($_SESSION, true));
        $cookies = new Tag('pre', 'Cookies ' . var_export($_COOKIE, true));
        $server = new Tag('pre', 'Server ' . var_export($_SERVER, true));
        return new Tag('div', $get . $post . $session . $cookies . $server, ['style' => 'display:flex;flex-wrap:wrap']);
    }

    public function __toString()
    {
        if ($this->content === null) {
            if ($this->options['_isAutoClosed']) {
                return '<' . $this->tag . $this->getFormattedOptions() . '/>';
            } else {
                return '<' . $this->tag . '>';
            }

        } else {
            if (is_array($this->content)) {
                $this->content = json_encode($this->content, JSON_PRETTY_PRINT);
            }
        }
        return '<' . $this->tag . $this->getFormattedOptions() . '>' . $this->content . '</' . $this->tag . '>';
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