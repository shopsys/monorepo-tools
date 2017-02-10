<?php

namespace Shopsys\ShopBundle\Model\Customer;

use Shopsys\ShopBundle\Model\Country\Country;

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
     * @var \Shopsys\ShopBundle\Model\Country\Country|null
     */
    public $country;

    /**
     * @param string|null $street
     * @param string|null $city
     * @param string|null $postcode
     * @param bool $companyCustomer
     * @param string|null $companyName
     * @param string|null $companyNumber
     * @param string|null $companyTaxNumber
     * @param string|null $telephone
     * @param \Shopsys\ShopBundle\Model\Country\Country|null $country
     */
    public function __construct(
        $street = null,
        $city = null,
        $postcode = null,
        $companyCustomer = false,
        $companyName = null,
        $companyNumber = null,
        $companyTaxNumber = null,
        $telephone = null,
        Country $country = null
    ) {
        $this->street = $street;
        $this->city = $city;
        $this->postcode = $postcode;
        $this->companyCustomer = $companyCustomer;
        $this->companyName = $companyName;
        $this->companyNumber = $companyNumber;
        $this->companyTaxNumber = $companyTaxNumber;
        $this->telephone = $telephone;
        $this->country = $country;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Customer\BillingAddress $billingAddress
     */
    public function setFromEntity(BillingAddress $billingAddress) {
        $this->telephone = $billingAddress->getTelephone();
        $this->companyCustomer = $billingAddress->isCompanyCustomer();
        $this->companyName = $billingAddress->getCompanyName();
        $this->companyNumber = $billingAddress->getCompanyNumber();
        $this->companyTaxNumber = $billingAddress->getCompanyTaxNumber();
        $this->street = $billingAddress->getStreet();
        $this->city = $billingAddress->getCity();
        $this->postcode = $billingAddress->getPostcode();
        $this->country = $billingAddress->getCountry();
    }

}
