<?php

namespace Shopsys\FrameworkBundle\Model\Order;

use Shopsys\FrameworkBundle\Model\Customer\User;

class OrderFactory implements OrderFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\OrderData $orderData
     * @param string $orderNumber
     * @param string $urlHash
     * @param \Shopsys\FrameworkBundle\Model\Customer\User|null $user
     * @return \Shopsys\FrameworkBundle\Model\Order\Order
     */
    public function create(
        OrderData $orderData,
        string $orderNumber,
        string $urlHash,
        ?User $user
    ): Order {
        return new Order($orderData, $orderNumber, $urlHash, $user);
    }
}
