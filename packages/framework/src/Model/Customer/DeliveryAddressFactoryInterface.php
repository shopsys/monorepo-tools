<?php

namespace Shopsys\FrameworkBundle\Model\Customer;

interface DeliveryAddressFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddressData $data
     * @return \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddress|null
     */
    public function create(DeliveryAddressData $data): ?DeliveryAddress;
}
