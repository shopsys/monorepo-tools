<?php

namespace Shopsys\ShopBundle\Model\Order;

use Doctrine\ORM\Mapping as ORM;
use Shopsys\FrameworkBundle\Model\Customer\User;
use Shopsys\FrameworkBundle\Model\Order\Order as BaseOrder;
use Shopsys\FrameworkBundle\Model\Order\OrderData as BaseOrderData;

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
     */
    public function edit(BaseOrderData $orderData)
    {
        parent::edit($orderData);
    }
}
