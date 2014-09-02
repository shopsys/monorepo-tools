<?php

namespace SS6\ShopBundle\Model\Customer;

class BillingAddressData {

	/**
	 * @var string|null
	 */
	private $telephone;

	/**
	 * @var boolean
	 */
	private $companyCustomer;

	/**
	 * @var string|null
	 */
	private $companyName;

	/**
	 * @var string|null
	 */
	private $companyNumber;

	/**
	 * @var string|null
	 */
	private $companyTaxNumber;

	/**
	 * @var string|null
	 */
	private $street;

	/**
	 * @var string|null
	 */
	private $city;

	/**
	 * @var string|null
	 */
	private $postcode;

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
	 * @return string|null
	 */
	public function getTelephone() {
		return $this->telephone;
	}

	/**
	 * @return boolean
	 */
	public function getCompanyCustomer() {
		return $this->companyCustomer;
	}

	/**
	 * @return string|null
	 */
	public function getCompanyName() {
		return $this->companyName;
	}

	/**
	 * @return string|null
	 */
	public function getCompanyNumber() {
		return $this->companyNumber;
	}

	/**
	 * @return string|null
	 */
	public function getCompanyTaxNumber() {
		return $this->companyTaxNumber;
	}

	/**
	 * @return string|null
	 */
	public function getStreet() {
		return $this->street;
	}

	/**
	 * @return string|null
	 */
	public function getCity() {
		return $this->city;
	}

	/**
	 * @return string|null
	 */
	public function getPostcode() {
		return $this->postcode;
	}

	/**
	 * @param string|null $telephone
	 */
	public function setTelephone($telephone) {
		$this->telephone = $telephone;
	}

	/**
	 * @param bool $companyCustomer
	 */
	public function setCompanyCustomer($companyCustomer) {
		$this->companyCustomer = $companyCustomer;
	}

	/**
	 * @param string|null $companyName
	 */
	public function setCompanyName($companyName) {
		$this->companyName = $companyName;
	}

	/**
	 * @param string|null $companyNumber
	 */
	public function setCompanyNumber($companyNumber) {
		$this->companyNumber = $companyNumber;
	}

	/**
	 * @param string|null $companyTaxNumber
	 */
	public function setCompanyTaxNumber($companyTaxNumber) {
		$this->companyTaxNumber = $companyTaxNumber;
	}

	/**
	 * @param string|null $street
	 */
	public function setStreet($street) {
		$this->street = $street;
	}

	/**
	 * @param string|null $city
	 */
	public function setCity($city) {
		$this->city = $city;
	}

	/**
	 * @param string|null $postcode
	 */
	public function setPostcode($postcode) {
		$this->postcode = $postcode;
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
