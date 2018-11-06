<?php

namespace Shopsys\FrameworkBundle\Model\Customer;

class CustomerData
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\UserData
     */
    public $userData;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\BillingAddressData
     */
    public $billingAddressData;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddressData
     */
    public $deliveryAddressData;

    /**
     * @var bool
     */
    public $sendRegistrationMail;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\BillingAddressData $billingAddressData
     * @param \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddressData $deliveryAddressData
     * @param \Shopsys\FrameworkBundle\Model\Customer\UserData $userData
     */
    public function __construct(
        BillingAddressData $billingAddressData,
        DeliveryAddressData $deliveryAddressData,
        UserData $userData
    ) {
        $this->userData = $userData;
        $this->billingAddressData = $billingAddressData;
        $this->deliveryAddressData = $deliveryAddressData;
        $this->sendRegistrationMail = false;
    }
}
