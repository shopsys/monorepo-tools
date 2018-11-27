<?php

namespace Shopsys\FrameworkBundle\Model\Customer;

use Shopsys\FrameworkBundle\Model\Order\Order;

interface CustomerDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Customer\CustomerData
     */
    public function create(): CustomerData;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\User $user
     * @return \Shopsys\FrameworkBundle\Model\Customer\CustomerData
     */
    public function createFromUser(User $user): CustomerData;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\User $user
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     * @return \Shopsys\FrameworkBundle\Model\Customer\CustomerData
     */
    public function createAmendedByOrder(User $user, Order $order): CustomerData;
}
