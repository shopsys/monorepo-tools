<?php

namespace Shopsys\ShopBundle\Model\Feed\Exception;

use Exception;
use Shopsys\ShopBundle\Model\Feed\Exception\FeedException;

class FeedConfigNotFoundException extends Exception implements FeedException
{
    /**
     * @param string $feedName
     * @param \Exception|null $previous
     */
    public function __construct($feedName = '', Exception $previous = null)
    {
        $message = 'Feed config with name "' . $feedName . ' not found.';

        parent::__construct($message, 0, $previous);
    }
}
