<?php

namespace Shopsys\FrameworkBundle\Model\Customer;

interface BillingAddressDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Customer\BillingAddressData
     */
    public function create(): BillingAddressData;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\BillingAddress $billingAddress
     * @return \Shopsys\FrameworkBundle\Model\Customer\BillingAddressData
     */
    public function createFromBillingAddress(BillingAddress $billingAddress): BillingAddressData;
}
