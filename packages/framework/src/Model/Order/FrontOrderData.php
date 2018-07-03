<?php

namespace Shopsys\FrameworkBundle\Model\Order;

class FrontOrderData extends OrderData
{
    /**
     * @var bool|null
     */
    public $companyCustomer;

    /**
     * @var bool|null
     */
    public $newsletterSubscription;

    /**
     * @var bool|null
     */
    public $disallowHeurekaVerifiedByCustomers;
}
