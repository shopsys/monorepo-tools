<?php

namespace SS6\ShopBundle\Model\Customer;

class DeliveryAddressFormData {

	/**
	 * @var boolean
	 */
	private $addressFilled;

	/**
	 * @var string
	 */
	private $companyName;

	/**
	 * @var string
	 */
	private $contactPerson;

	/**
	 * @var string
	 */
	private $telephone;

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

	public function getAddressFilled() {
		return $this->addressFilled;
	}

	public function getCompanyName() {
		return $this->companyName;
	}

	public function getContactPerson() {
		return $this->contactPerson;
	}

	public function getTelephone() {
		return $this->telephone;
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

	public function setAddressFilled($addressFilled) {
		$this->addressFilled = $addressFilled;
	}

	public function setCompanyName($companyName) {
		$this->companyName = $companyName;
	}

	public function setContactPerson($contactPerson) {
		$this->contactPerson = $contactPerson;
	}

	public function setTelephone($telephone) {
		$this->telephone = $telephone;
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

	public function setFromEntity(DeliveryAddress $deliveryAddress = null) {
		if ($deliveryAddress !== null) {
			$this->addressFilled = true;
			$this->companyName = $deliveryAddress->getCompanyName();
			$this->contactPerson = $deliveryAddress->getContactPerson();
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
