<?php
/**
 * Created by PhpStorm.
 * User: laurent
 * Date: 10/09/20
 * Time: 18:47
 */

namespace QuinenLib\Symfony;

use ArrayAccess;
use JsonSerializable;
use ReflectionClass;
use function Symfony\Component\String\u;

abstract class Entity implements ArrayAccess, JsonSerializable
{

    public function __get($name)
    {
        return \json_encode(['get', $name]);
    }


    public function __call($name, $arguments)
    {
        return $this[$name];
        return \json_encode(['call', $name, $arguments, $this[$name], $this->{$name}]);
    }

    /**
     * Whether a offset exists
     * @link https://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        $getter = $this->getMethodNameFromField($offset);
        $property = $this->getPropertyNameFromField($offset);

        if (method_exists($this, $getter) || isset($this->{$property})) {
            return true;
        }

        return false;
    }

    /**
     * Offset to retrieve
     * @link https://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        $getter = $this->getMethodNameFromField($offset);
        $property = $this->getPropertyNameFromField($offset);
        if (method_exists($this, $getter)) {
            return $this->{$getter}();
        } elseif (isset($this->{$offset})) {
            return $this->{$property};
        }
        return null;
        //throw new \Exception('undefined offset : `' . $offset . '` on ' . self::class);
    }

    /**
     * Offset to set
     * @link https://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $setter = $this->getMethodNameFromField($offset, 'set');
        $property = $this->getPropertyNameFromField($offset);
        if (method_exists($this, $setter)) {
            $this->{$setter}($value);
        } elseif (isset($this->{$property})) {
            $this->{$property} = $value;
        }
    }

    /**
     * Offset to unset
     * @link https://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        $property = $this->getPropertyNameFromField($offset);
        unset($this->{$property});
    }

    private function getMethodNameFromField($field, $prefix = 'get')
    {
        return u($field)->camel()->title()->ensureStart('get')->toString();
    }

    private function getPropertyNameFromField($field)
    {
        return u($field)->camel()->toString();
    }

    /**
     * Return data which should be serialized by json_encode().
     *
     * @return  mixed
     *
     * @since   1.0
     * @throws \ReflectionException
     */
    public function jsonSerialize()
    {
        $fields = [];
        foreach ((new ReflectionClass($this))->getProperties() as $property) {
            $fields[$property->name] = $this[$property->name];
        }
        return $fields;
    }

    public function __toString()
    {
        return static::class . PHP_EOL . \json_encode($this, JSON_PRETTY_PRINT);
    }

}