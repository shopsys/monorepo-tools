<?php

namespace Shopsys\FrameworkBundle\Model\Customer;

class DeliveryAddressData
{
    /**
     * @var bool
     */
    public $addressFilled;

    /**
     * @var string|null
     */
    public $companyName;

    /**
     * @var string|null
     */
    public $firstName;

    /**
     * @var string|null
     */
    public $lastName;

    /**
     * @var string|null
     */
    public $telephone;

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
        $this->addressFilled = false;
    }
}
