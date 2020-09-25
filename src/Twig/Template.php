<?php
/**
 * Created by PhpStorm.
 * User: laurent
 * Date: 25/09/20
 * Time: 10:18
 */

namespace QuinenLib\Twig;


use Twig\Environment;

class Template
{
    private $env;

    private $context = [];

    public function __construct(Environment $env, array $context = [])
    {
        $this->env = $env;
        $this->context = $context;
    }

    public function render($template, $context)
    {
        if (is_scalar($template)) {
            return $this->renderString($template, $context);
        } else {
            if (is_array($template)) {
                return array_map(
                    function ($templateValue) use ($context) {
                        return $this->render($templateValue, $context);
                    },
                    $template
                );
            } else if ($template instanceof \Traversable) {
                return $template;
            } else {
                return $template;
            }
        }
    }

    private function renderString($template, $context)
    {
        if (!is_array($context)) {
            if (is_object($context)) {
                $context = json_decode(json_encode($context), true);
            } else {
                die($context);
            }

        }
        return $this->env->render($this->env->createTemplate($template), $context + $this->context);
    }
}