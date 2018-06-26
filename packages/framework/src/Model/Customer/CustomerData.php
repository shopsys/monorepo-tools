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

    public function __construct(
        BillingAddressData $billingAddressData
    ) {
        $this->userData = new UserData();
        $this->billingAddressData = $billingAddressData;
        $this->deliveryAddressData = new DeliveryAddressData();
        $this->sendRegistrationMail = false;
    }
}
