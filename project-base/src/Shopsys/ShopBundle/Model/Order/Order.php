<?php

namespace Shopsys\ShopBundle\Model\Order;

use Doctrine\ORM\Mapping as ORM;
use Shopsys\FrameworkBundle\Model\Customer\User;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderItemFactoryInterface;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderItemPriceCalculation;
use Shopsys\FrameworkBundle\Model\Order\Order as BaseOrder;
use Shopsys\FrameworkBundle\Model\Order\OrderData as BaseOrderData;
use Shopsys\FrameworkBundle\Model\Order\OrderEditResult;
use Shopsys\FrameworkBundle\Model\Order\OrderPriceCalculation;

/**
 * @ORM\Table(name="orders")
 * @ORM\Entity
 */
class Order extends BaseOrder
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\OrderData $orderData
     * @param string $orderNumber
     * @param string $urlHash
     * @param \Shopsys\FrameworkBundle\Model\Customer\User|null $user
     */
    public function __construct(
        BaseOrderData $orderData,
        string $orderNumber,
        string $urlHash,
        ?User $user = null
    ) {
        parent::__construct($orderData, $orderNumber, $urlHash, $user);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Order\OrderData $orderData
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemPriceCalculation $orderItemPriceCalculation
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemFactoryInterface $orderItemFactory
     * @param \Shopsys\FrameworkBundle\Model\Order\OrderPriceCalculation $orderPriceCalculation
     * @return \Shopsys\FrameworkBundle\Model\Order\OrderEditResult
     */
    public function edit(
        BaseOrderData $orderData,
        OrderItemPriceCalculation $orderItemPriceCalculation,
        OrderItemFactoryInterface $orderItemFactory,
        OrderPriceCalculation $orderPriceCalculation
    ): OrderEditResult {
        return parent::edit($orderData, $orderItemPriceCalculation, $orderItemFactory, $orderPriceCalculation);
    }
}
