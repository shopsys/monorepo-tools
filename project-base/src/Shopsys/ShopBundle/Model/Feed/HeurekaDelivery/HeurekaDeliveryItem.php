<?php

namespace Shopsys\ShopBundle\Model\Feed\HeurekaDelivery;

use Shopsys\ProductFeed\FeedItemInterface;

class HeurekaDeliveryItem implements FeedItemInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $stockQuantity;

    /**
     * @param int $id
     * @param int $stockQuantity
     */
    public function __construct($id, $stockQuantity)
    {
        $this->id = $id;
        $this->stockQuantity = $stockQuantity;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getStockQuantity()
    {
        return $this->stockQuantity;
    }
}
