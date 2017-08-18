<?php

namespace Shopsys\ShopBundle\Model\Feed\Delivery;

use Shopsys\ProductFeed\DeliveryFeedItemInterface;

class DeliveryFeedItem implements DeliveryFeedItemInterface
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
