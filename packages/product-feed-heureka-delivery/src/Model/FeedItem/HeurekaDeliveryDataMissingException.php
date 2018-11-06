<?php

namespace Shopsys\ProductFeed\HeurekaDeliveryBundle\Model\FeedItem;

use Exception;

class HeurekaDeliveryDataMissingException extends Exception
{
    /**
     * @param string $key
     * @param \Exception|null $previous
     */
    public function __construct(string $key, Exception $previous = null)
    {
        $message = sprintf('Feed item cannot be created - key "%s" missing from data row.', $key);

        parent::__construct($message, 0, $previous);
    }
}
