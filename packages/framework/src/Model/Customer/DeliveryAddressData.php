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

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddress|null $deliveryAddress
     */
    public function setFromEntity(DeliveryAddress $deliveryAddress = null)
    {
        if ($deliveryAddress !== null) {
            $this->addressFilled = true;
            $this->companyName = $deliveryAddress->getCompanyName();
            $this->firstName = $deliveryAddress->getFirstName();
            $this->lastName = $deliveryAddress->getLastName();
            $this->telephone = $deliveryAddress->getTelephone();
            $this->street = $deliveryAddress->getStreet();
            $this->city = $deliveryAddress->getCity();
            $this->postcode = $deliveryAddress->getPostcode();
            $this->country = $deliveryAddress->getCountry();
        } else {
            $this->addressFilled = false;
        }
    }
}
