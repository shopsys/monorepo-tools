<?php

namespace Shopsys\ProductFeed;

interface DeliveryFeedItemInterface extends FeedItemInterface
{
    /**
     * @return int
     */
    public function getStockQuantity();
}
