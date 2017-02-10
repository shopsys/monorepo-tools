<?php

namespace Shopsys\ShopBundle\Model\Customer;

use Shopsys\ShopBundle\Model\Country\Country;

class DeliveryAddressData {

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
     * @var \Shopsys\ShopBundle\Model\Country\Country|null
     */
    public $country;

    /**
     * @param bool $addressFilled
     * @param string|null $street
     * @param string|null $city
     * @param string|null $postcode
     * @param string|null $companyName
     * @param string|null $firstName
     * @param null|null $lastName
     * @param string|null $telephone
     * @param \Shopsys\ShopBundle\Model\Country\Country|null $country
     */
    public function __construct(
        $addressFilled = false,
        $street = null,
        $city = null,
        $postcode = null,
        $companyName = null,
        $firstName = null,
        $lastName = null,
        $telephone = null,
        Country $country = null
    ) {
        $this->addressFilled = $addressFilled;
        $this->street = $street;
        $this->city = $city;
        $this->postcode = $postcode;
        $this->companyName = $companyName;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->telephone = $telephone;
        $this->country = $country;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Customer\DeliveryAddress|null $deliveryAddress
     */
    public function setFromEntity(DeliveryAddress $deliveryAddress = null) {
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
