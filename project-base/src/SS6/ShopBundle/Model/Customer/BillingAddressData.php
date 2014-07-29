<?php

namespace SS6\ShopBundle\Model\Customer;

class BillingAddressData {

	/**
	 * @var string
	 */
	private $telephone;

	/**
	 * @var boolean
	 */
	private $companyCustomer;

	/**
	 * @var string
	 */
	private $companyName;

	/**
	 * @var string
	 */
	private $companyNumber;

	/**
	 * @var string
	 */
	private $companyTaxNumber;

	/**
	 * @var string
	 */
	private $street;

	/**
	 * @var string
	 */
	private $city;

	/**
	 * @var string
	 */
	private $postcode;

	/**
	 * @var string
	 */
	private $country;

	/**
	 * @param string|null $street
	 * @param string|null $city
	 * @param string|null $postcode
	 * @param string|null $country
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
		$country = null,
		$companyCustomer = false,
		$companyName = null,
		$companyNumber = null,
		$companyTaxNumber = null,
		$telephone = null
	) {
		$this->street = $street;
		$this->city = $city;
		$this->postcode = $postcode;
		$this->country = $country;
		$this->companyCustomer = $companyCustomer;
		$this->companyName = $companyName;
		$this->companyNumber = $companyNumber;
		$this->companyTaxNumber = $companyTaxNumber;
		$this->telephone = $telephone;
	}

	public function getTelephone() {
		return $this->telephone;
	}

	public function getCompanyCustomer() {
		return $this->companyCustomer;
	}

	public function getCompanyName() {
		return $this->companyName;
	}

	public function getCompanyNumber() {
		return $this->companyNumber;
	}

	public function getCompanyTaxNumber() {
		return $this->companyTaxNumber;
	}

	public function getStreet() {
		return $this->street;
	}

	public function getCity() {
		return $this->city;
	}

	public function getPostcode() {
		return $this->postcode;
	}

	public function getCountry() {
		return $this->country;
	}

	public function setTelephone($telephone) {
		$this->telephone = $telephone;
	}

	public function setCompanyCustomer($companyCustomer) {
		$this->companyCustomer = $companyCustomer;
	}

	public function setCompanyName($companyName) {
		$this->companyName = $companyName;
	}

	public function setCompanyNumber($companyNumber) {
		$this->companyNumber = $companyNumber;
	}

	public function setCompanyTaxNumber($companyTaxNumber) {
		$this->companyTaxNumber = $companyTaxNumber;
	}

	public function setStreet($street) {
		$this->street = $street;
	}

	public function setCity($city) {
		$this->city = $city;
	}

	public function setPostcode($postcode) {
		$this->postcode = $postcode;
	}

	public function setCountry($country) {
		$this->country = $country;
	}

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
