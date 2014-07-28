<?php

namespace SS6\ShopBundle\Model\Customer;

/**
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class CustomerFormData {

	/**
	 * @var int
	 */
	private $id;

	/**
	 * @var string
	 */
	private $firstName;

	/**
	 * @var string
	 */
	private $lastName;

	/**
	 * @var string
	 */
	private $email;

	/**
	 * @var string
	 */
	private $password;

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
	 * @var boolean
	 */
	private $deliveryAddressFilled;

	/**
	 * @var string
	 */
	private $deliveryCompanyName;

	/**
	 * @var string
	 */
	private $deliveryContactPerson;

	/**
	 * @var string
	 */
	private $deliveryTelephone;

	/**
	 * @var string
	 */
	private $deliveryStreet;

	/**
	 * @var string
	 */
	private $deliveryCity;

	/**
	 * @var string
	 */
	private $deliveryPostcode;

	/**
	 * @var string
	 */
	private $deliveryCountry;

	public function getId() {
		return $this->id;
	}

	public function getFirstName() {
		return $this->firstName;
	}

	public function getLastName() {
		return $this->lastName;
	}

	public function getEmail() {
		return $this->email;
	}

	public function getPassword() {
		return $this->password;
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

	public function getDeliveryAddressFilled() {
		return $this->deliveryAddressFilled;
	}

	public function getDeliveryCompanyName() {
		return $this->deliveryCompanyName;
	}

	public function getDeliveryContactPerson() {
		return $this->deliveryContactPerson;
	}

	public function getDeliveryTelephone() {
		return $this->deliveryTelephone;
	}

	public function getDeliveryStreet() {
		return $this->deliveryStreet;
	}

	public function getDeliveryCity() {
		return $this->deliveryCity;
	}

	public function getDeliveryPostcode() {
		return $this->deliveryPostcode;
	}

	public function getDeliveryCountry() {
		return $this->deliveryCountry;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function setFirstName($firstName) {
		$this->firstName = $firstName;
	}

	public function setLastName($lastName) {
		$this->lastName = $lastName;
	}

	public function setEmail($email) {
		$this->email = $email;
	}

	public function setPassword($password) {
		$this->password = $password;
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

	public function setDeliveryAddressFilled($deliveryAddressFilled) {
		$this->deliveryAddressFilled = $deliveryAddressFilled;
	}

	public function setDeliveryCompanyName($deliveryCompanyName) {
		$this->deliveryCompanyName = $deliveryCompanyName;
	}

	public function setDeliveryContactPerson($deliveryContactPerson) {
		$this->deliveryContactPerson = $deliveryContactPerson;
	}

	public function setDeliveryTelephone($deliveryTelephone) {
		$this->deliveryTelephone = $deliveryTelephone;
	}

	public function setDeliveryStreet($deliveryStreet) {
		$this->deliveryStreet = $deliveryStreet;
	}

	public function setDeliveryCity($deliveryCity) {
		$this->deliveryCity = $deliveryCity;
	}

	public function setDeliveryPostcode($deliveryPostcode) {
		$this->deliveryPostcode = $deliveryPostcode;
	}

	public function setDeliveryCountry($deliveryCountry) {
		$this->deliveryCountry = $deliveryCountry;
	}

	public function setFromEntity(User $user) {
		$this->id = $user->getId();
		$this->firstName = $user->getFirstName();
		$this->lastName = $user->getLastName();
		$this->telephone = $user->getBillingAddress()->getTelephone();
		$this->email = $user->getEmail();
		$this->companyCustomer = $user->getBillingAddress()->isCompanyCustomer();
		$this->companyName = $user->getBillingAddress()->getCompanyName();
		$this->companyNumber = $user->getBillingAddress()->getCompanyNumber();
		$this->companyTaxNumber = $user->getBillingAddress()->getCompanyTaxNumber();
		$this->street = $user->getBillingAddress()->getStreet();
		$this->city = $user->getBillingAddress()->getCity();
		$this->postcode = $user->getBillingAddress()->getPostcode();
		$this->country = $user->getBillingAddress()->getCountry();
		if ($user->getDeliveryAddress() !== null) {
			$this->deliveryAddressFilled = true;
			$this->deliveryCompanyName = $user->getDeliveryAddress()->getCompanyName();
			$this->deliveryContactPerson = $user->getDeliveryAddress()->getContactPerson();
			$this->deliveryTelephone = $user->getDeliveryAddress()->getTelephone();
			$this->deliveryStreet = $user->getDeliveryAddress()->getStreet();
			$this->deliveryCity = $user->getDeliveryAddress()->getCity();
			$this->deliveryPostcode = $user->getDeliveryAddress()->getPostcode();
			$this->deliveryCountry = $user->getDeliveryAddress()->getCountry();
		} else {
			$this->deliveryAddressFilled = false;
		}
	}

}
