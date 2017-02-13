<?php

namespace Shopsys\ShopBundle\Component\BaseObject;

use IteratorAggregate;

abstract class Object implements IteratorAggregate
{
    public function __call($name, $arguments)
    {
        $message = 'Cannot call method ' . get_class($this) . '::' . $name . '()';
        throw new \Shopsys\ShopBundle\Component\BaseObject\Exception\BaseObjectException($message);
    }

    public static function __callStatic($name, $arguments)
    {
        $message = 'Cannot call static method ' . get_called_class() . '::' . $name . '()';
        throw new \Shopsys\ShopBundle\Component\BaseObject\Exception\BaseObjectException($message);
    }

    public function &__get($name)
    {
        $message = 'Cannot read non-existent property ' . get_class($this) . ' ::$' . $name;
        throw new \Shopsys\ShopBundle\Component\BaseObject\Exception\BaseObjectException($message);
    }

    public function __set($name, $value)
    {
        $message = 'Cannot set non-existent property ' . get_class($this) . ' ::$' . $name;
        throw new \Shopsys\ShopBundle\Component\BaseObject\Exception\BaseObjectException($message);
    }

    public function __isset($name)
    {
        $message = 'Cannot isset non-existent property ' . get_class($this) . '::$' . $name;
        throw new \Shopsys\ShopBundle\Component\BaseObject\Exception\BaseObjectException($message);
    }

    public function __unset($name)
    {
        $message = 'Cannot unset non-existent property ' . get_class($this) . '::$' . $name;
        throw new \Shopsys\ShopBundle\Component\BaseObject\Exception\BaseObjectException($message);
    }

    public function getIterator()
    {
        $message = 'Cannot iterate object ' . get_class($this);
        throw new \Shopsys\ShopBundle\Component\BaseObject\Exception\BaseObjectException($message);
    }
}
