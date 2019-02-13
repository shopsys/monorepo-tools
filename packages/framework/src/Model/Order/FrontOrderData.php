<?php

namespace Shopsys\FrameworkBundle\Model\Order;

class FrontOrderData extends OrderData
{
    /**
     * @var bool
     */
    public $companyCustomer;

    /**
     * @var bool
     */
    public $newsletterSubscription;

    /**
     * @var bool
     */
    public $disallowHeurekaVerifiedByCustomers;

    public function __construct()
    {
        parent::__construct();
        $this->companyCustomer = false;
        $this->newsletterSubscription = false;
        $this->disallowHeurekaVerifiedByCustomers = false;
    }
}
