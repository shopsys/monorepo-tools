<?php

namespace Shopsys\FrameworkBundle\Model\Customer;

class DeliveryAddressFactory implements DeliveryAddressFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddressData $data
     * @return \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddress
     */
    public function create(DeliveryAddressData $data): DeliveryAddress
    {
        return new DeliveryAddress($data);
    }
}
