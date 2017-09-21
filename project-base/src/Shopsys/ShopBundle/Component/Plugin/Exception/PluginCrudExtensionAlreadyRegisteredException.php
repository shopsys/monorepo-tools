<?php

namespace Shopsys\ShopBundle\Component\Plugin\Exception;

use Exception;

class PluginCrudExtensionAlreadyRegisteredException extends Exception implements PluginException
{
    /**
     * @param string $type
     * @param string $key
     * @param \Exception|null $previous
     */
    public function __construct($type, $key, Exception $previous = null)
    {
        $message = sprintf('Plugin CRUD extension of type "%s" with key "%s" was already registered.', $type, $key);

        parent::__construct($message, 0, $previous);
    }
}
