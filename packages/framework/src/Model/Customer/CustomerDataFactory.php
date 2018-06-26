<?php

namespace Shopsys\FrameworkBundle\Model\Customer;

class CustomerDataFactory implements CustomerDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Customer\CustomerData
     */
    public function create(): CustomerData
    {
        return new CustomerData();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\User $user
     * @return \Shopsys\FrameworkBundle\Model\Customer\CustomerData
     */
    public function createFromUser(User $user): CustomerData
    {
        $customerData = new CustomerData();
        $customerData->userData->setFromEntity($user);
        $customerData->billingAddressData->setFromEntity($user->getBillingAddress());
        $customerData->deliveryAddressData->setFromEntity($user->getDeliveryAddress());

        return $customerData;
    }
}
