<?php
/**
 * Created by PhpStorm.
 * User: laurent
 * Date: 10/09/20
 * Time: 11:38
 */

namespace QuinenLib\Twig;


use QuinenLib\Html\Bs4;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;
use Twig\TwigFunction;

class Bs4Extension extends AbstractExtension implements GlobalsInterface
{

    public function getFunctions()
    {
        return [
            new TwigFunction('bs4_badge', [(new Bs4()), 'badge'], ['is_safe' => ['html']]),
            new TwigFunction('bs4_menuTitle', [(new Bs4()), 'menuTitle'], ['is_safe' => ['html']]),
            new TwigFunction('bs4_table', [(new Bs4()), 'table'], ['is_safe' => ['html']])
        ];
    }

    public function getFilters()
    {
        return [
        ];
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
    public function getGlobals(): array
    {
        return [
            'bs4' => (new Bs4())
        ];
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