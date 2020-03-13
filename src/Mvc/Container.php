<?php
/**
 * Created by PhpStorm.
 * User: laurent
 * Date: 13/03/20
 * Time: 16:55
 */

namespace QuinenLib\Mvc;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use QuinenLib\Arrays\ContentOptionsTrait;
use Throwable;

class Container implements ContainerInterface
{
    use ContentOptionsTrait;
    private $container = [];

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get($id)
    {
        // TODO: Implement get() method.
        if ($this->has($id)) {

            if (is_callable($this->container[$id][0])) {
                $this->container[$id] = call_user_func_array($this->container[$id][0], $this->container[$id][1]);
            }

            return $this->container[$id];


        } else {
            throw new ContainerNotFoundException($id);
        }
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has($id)
    {
        // TODO: Implement has() method.
        return isset($this->container[$id]);
    }

    public function add($id, $content)
    {
        list($content, $contentOptions) = $this->getContentOptions($content);
        $this->container[$id] = [$content, $contentOptions];
        return $this;
    }
}

class ContainerNotFoundException extends \Exception implements NotFoundExceptionInterface
{
    public function __construct($id = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct('No entry was found for "' . $id . '" identifier', $code, $previous);
    }
}