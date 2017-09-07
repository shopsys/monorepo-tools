<?php

namespace Shopsys\ShopBundle\Model\Feed\Exception;

use Exception;
use Shopsys\ProductFeed\FeedConfigInterface;

class FeedItemRepositoryForFeedConfigNotFoundException extends Exception implements FeedException
{
    /**
     * @param \Shopsys\ProductFeed\FeedConfigInterface $feedConfig
     * @param \Exception|null $previous
     */
    public function __construct(FeedConfigInterface $feedConfig, Exception $previous = null)
    {
        $message = 'FeedItemRepository for feed "' . $feedConfig->getFeedName() . '" not found.';

        parent::__construct($message, 0, $previous);
    }
}
