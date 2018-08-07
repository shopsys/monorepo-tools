<?php

namespace Shopsys\FrameworkBundle\Model\Customer;

class UserFactory implements UserFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\UserData $userData
     * @param \Shopsys\FrameworkBundle\Model\Customer\BillingAddress $billingAddress
     * @param \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddress|null $deliveryAddress
     * @return \Shopsys\FrameworkBundle\Model\Customer\User
     */
    public function create(
        UserData $userData,
        BillingAddress $billingAddress,
        ?DeliveryAddress $deliveryAddress
    ): User {
        return new User($userData, $billingAddress, $deliveryAddress);
    }
}
