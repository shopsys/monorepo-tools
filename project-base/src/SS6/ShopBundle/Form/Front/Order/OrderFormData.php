<?php

namespace SS6\ShopBundle\Form\Front\Order;

use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Transport\Transport;

/**
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class OrderFormData {

	/**
	 * @var \SS6\ShopBundle\Model\Transport\Transport
	 */
	private $transport;

	/**
	 * @var \SS6\ShopBundle\Model\Payment\Payment
	 */
	private $payment;

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
	 * @var boolean
	 */
	private $deliveryAddressFilled;

	/**
	 * @var string|null
	 */
	private $deliveryContactPerson;

	/**
	 * @var string|null
	 */
	private $deliveryCompanyName;

	/**
	 * @var string|null
	 */
	private $deliveryTelephone;

	/**
	 * @var string|null
	 */
	private $deliveryStreet;

	/**
	 * @var string|null
	 */
	private $deliveryCity;

	/**
	 * @var string|null
	 */
	private $deliveryPostcode;

	/**
	 * @var string|null
	 */
	private $note;

	/**
	 * @return \SS6\ShopBundle\Model\Transport\Transport
	 */
	public function getTransport() {
		return $this->transport;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Payment\Payment
	 */
	public function getPayment() {
		return $this->payment;
	}

	/**
	 * @return string
	 */
	public function getFirstName() {
		return $this->firstName;
	}

	/**
	 * @return string
	 */
	public function getLastName() {
		return $this->lastName;
	}

	/**
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * @return string
	 */
	public function getTelephone() {
		return $this->telephone;
	}

	/**
	 * @return boolean
	 */
	public function isCompanyCustomer() {
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
	 * @return string
	 */
	public function getStreet() {
		return $this->street;
	}

	/**
	 * @return string
	 */
	public function getCity() {
		return $this->city;
	}

	/**
	 * @return string
	 */
	public function getPostcode() {
		return $this->postcode;
	}

	/**
	 * @return boolean
	 */
	public function isDeliveryAddressFilled() {
		return $this->deliveryAddressFilled;
	}

	/**
	 * @return string|null
	 */
	public function getDeliveryContactPerson() {
		return $this->deliveryContactPerson;
	}

	/**
	 * @return string|null
	 */
	public function getDeliveryCompanyName() {
		return $this->deliveryCompanyName;
	}

	/**
	 * @return string|null
	 */
	public function getDeliveryTelephone() {
		return $this->deliveryTelephone;
	}

	/**
	 * @return string|null
	 */
	public function getDeliveryStreet() {
		return $this->deliveryStreet;
	}

	/**
	 * @return string|null
	 */
	public function getDeliveryCity() {
		return $this->deliveryCity;
	}

	/**
	 * @return string|null
	 */
	public function getDeliveryPostcode() {
		return $this->deliveryPostcode;
	}

	/**
	 * @return string|null
	 */
	public function getNote() {
		return $this->note;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport|null $transport
	 */
	public function setTransport(Transport $transport = null) {
		$this->transport = $transport;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment|null $payment
	 */
	public function setPayment(Payment $payment = null) {
		$this->payment = $payment;
	}

	/**
	 * @param string $firstName
	 */
	public function setFirstName($firstName) {
		$this->firstName = $firstName;
	}

	/**
	 * @param string $lastName
	 */
	public function setLastName($lastName) {
		$this->lastName = $lastName;
	}

	/**
	 * @param string $email
	 */
	public function setEmail($email) {
		$this->email = $email;
	}

	/**
	 * @param string|null $telephone
	 */
	public function setTelephone($telephone) {
		$this->telephone = $telephone;
	}

	/**
	 * @param boolean $companyCustomer
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
	 * @param boolean $deliveryAddressFilled
	 */
	public function setDeliveryAddressFilled($deliveryAddressFilled) {
		$this->deliveryAddressFilled = $deliveryAddressFilled;
	}

	/**
	 * @param string|null $deliveryContactPerson
	 */
	public function setDeliveryContactPerson($deliveryContactPerson) {
		$this->deliveryContactPerson = $deliveryContactPerson;
	}

	/**
	 * @param string|null $deliveryCompanyName
	 */
	public function setDeliveryCompanyName($deliveryCompanyName) {
		$this->deliveryCompanyName = $deliveryCompanyName;
	}

	/**
	 * @param string|null $deliveryTelephone
	 */
	public function setDeliveryTelephone($deliveryTelephone) {
		$this->deliveryTelephone = $deliveryTelephone;
	}

	/**
	 * @param string|null $deliveryStreet
	 */
	public function setDeliveryStreet($deliveryStreet) {
		$this->deliveryStreet = $deliveryStreet;
	}

	/**
	 * @param string|null $deliveryCity
	 */
	public function setDeliveryCity($deliveryCity) {
		$this->deliveryCity = $deliveryCity;
	}

	/**
	 * @param string|null $deliveryPostcode
	 */
	public function setDeliveryPostcode($deliveryPostcode) {
		$this->deliveryPostcode = $deliveryPostcode;
	}

	/**
	 * @param string|null $note
	 */
	public function setNote($note) {
		$this->note = $note;
	}

}
