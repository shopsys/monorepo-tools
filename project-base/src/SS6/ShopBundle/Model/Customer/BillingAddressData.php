<?php

namespace SS6\ShopBundle\Model\Customer;

class BillingAddressData {

	/**
	 * @var string|null
	 */
	public $telephone;

	/**
	 * @var boolean
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
	 * @param string|null $street
	 * @param string|null $city
	 * @param string|null $postcode
	 * @param boolean $companyCustomer
	 * @param string|null $companyName
	 * @param string|null $companyNumber
	 * @param string|null $companyTaxNumber
	 * @param string|null $telephone
	 */
	public function __construct(
		$street = null,
		$city = null,
		$postcode = null,
		$companyCustomer = false,
		$companyName = null,
		$companyNumber = null,
		$companyTaxNumber = null,
		$telephone = null
	) {
		$this->street = $street;
		$this->city = $city;
		$this->postcode = $postcode;
		$this->companyCustomer = $companyCustomer;
		$this->companyName = $companyName;
		$this->companyNumber = $companyNumber;
		$this->companyTaxNumber = $companyTaxNumber;
		$this->telephone = $telephone;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\BillingAddress $billingAddress
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
	}

}
