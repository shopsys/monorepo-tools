<?php

namespace Shopsys\FrameworkBundle\Model\Customer;

class BillingAddressData
{
    /**
     * @var string|null
     */
    public $telephone;

    /**
     * @var bool
     */
    public $companyCustomer;

    /**
     * @var string|null
     */
    public $companyName;

    /**
     * @var string|null
     */
    public $companyNumber;

    /**
     * @var string|null
     */
    public $companyTaxNumber;

    /**
     * @var string|null
     */
    public $street;

    /**
     * @var string|null
     */
    public $city;

    /**
     * @var string|null
     */
    public $postcode;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Country\Country|null
     */
    public $country;

    public function __construct()
    {
        $this->companyCustomer = false;
    }
}
