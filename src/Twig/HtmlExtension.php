<?php
/**
 * Created by PhpStorm.
 * User: laurent
 * Date: 10/09/20
 * Time: 11:38
 */

namespace QuinenLib\Twig;

use QuinenLib\Html\FontAwesome5;
use QuinenLib\Html\Table;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class HtmlExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('table', [$this, 'table'], ['is_safe' => ['html']])
        ];
    }

    public function getFilters()
    {
        return [
            new TwigFilter('fa5', function ($name, array $options = []) {
                return new FontAwesome5($name, $options);
            }, ['is_safe' => ['html']])
        ];
    }

    public function table(array $data, array $maps = [], array $options = [])
    {
        return (string)(new Table($data, $maps, $options));
    }

    /**
     * Initializes the runtime environment.
     *
     * This is where you can load some file that contains filter functions for instance.
     *
     * @deprecated since 1.23 (to be removed in 2.0), implement \Twig_Extension_InitRuntimeInterface instead
     */
    public function initRuntime(Environment $environment)
    {
        // TODO: Implement initRuntime() method.
    }

    /**
     * Returns a list of global variables to add to the existing list.
     *
     * @return array An array of global variables
     *
     * @deprecated since 1.23 (to be removed in 2.0), implement \Twig_Extension_GlobalsInterface instead
     */
    public function getGlobals()
    {
        // TODO: Implement getGlobals() method.
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     *
     * @deprecated since 1.26 (to be removed in 2.0), not used anymore internally
     */
    public function getName()
    {
        // TODO: Implement getName() method.
    }
}