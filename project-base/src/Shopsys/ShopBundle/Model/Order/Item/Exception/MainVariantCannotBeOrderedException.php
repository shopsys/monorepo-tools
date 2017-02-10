<?php

namespace Shopsys\ShopBundle\Model\Order\Item\Exception;

use Exception;

class MainVariantCannotBeOrderedException extends Exception implements OrderItemException
{

    /**
     * @param string $message
     * @param \Exception|null $previous
     */
    public function __construct($message = '', Exception $previous = null) {
        parent::__construct($message, 0, $previous);
    }

}
