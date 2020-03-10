<?php
/**
 * Created by PhpStorm.
 * User: laurent.d
 * Date: 3/10/20
 * Time: 2:01 PM
 */

namespace QuinenLib\Legacy;


class Url
{
    const SCHEME_HTTP = 'http';
    const SCHEME_HTTPS = 'https';

    private $server;
    private $url;
    private $isFull;


    public function __construct($url = null, array $options = [])
    {
        $options += [
            'server' => (new Request())->getServer(),
            'isFull' => false
        ];

        $this->server = $options['server'];
        $this->isFull = $options['isFull'];

        if ($url === null) {
            $url = $this->server['REQUEST_URI'];
        }
        $this->url = $url;
    }

    public function parse()
    {
        $parsedUrl = parse_url($this->url);
        $parsedUrl += [
            'scheme' => $this->getScheme(),
            'host' => $this->getHost(),
            'prepath' => $this->getPrepath(),
            'route' => $this->getRoute()
        ];
        return $parsedUrl;
    }

    public function getScheme()
    {
        $https = [
            'REQUEST_SCHEME' => self::SCHEME_HTTPS,
            'HTTPS' => 'on',
            'SERVER_PORT' => '443'
        ];

        $isHttps = collection($https)->some(function ($v, $k) {
            return (!empty($this->server[$k]) && $this->server[$k] === $v);
        });

        return $isHttps ? self::SCHEME_HTTPS : self::SCHEME_HTTP;
    }

    public function getHost()
    {
        if (!empty($this->server['HTTP_HOST'])) {
            $host = $this->server['HTTP_HOST'];
        } else {
            $host = $this->server['SERVER_NAME'];
        }
        return explode(':', $host)[0];
    }

    public function getPrepath()
    {
        // sur index point d'entree unique
        //define('DIR_WWW_ROOT',dirname(__DIR__));
        $docRoot = str_replace($this->server['SCRIPT_NAME'], '', $this->server['SCRIPT_FILENAME']);
        return str_replace($docRoot, '', DIR_WWW_ROOT);
    }

    public function getRoute()
    {
        return str_replace($this->getPrepath(), '', parse_url($this->url, PHP_URL_PATH));
    }

    public function __toString()
    {
        $urlString = '';

        if ($this->isFull) {
            $urlString .= $this->getScheme() . '://' . $this->getHost();
        }

        $urlString .= $this->getPrepath() . $this->getRoute();

        if (($query = parse_url($this->url, PHP_URL_QUERY)) && !empty($query)) {
            $urlString .= '?' . $query;
        }

        return $urlString;
    }

}