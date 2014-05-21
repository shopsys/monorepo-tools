<?php

namespace SS6\ShopBundle\Form\Admin\Order;

/**
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class OrderFormData {

	/**
	 * @var int
	 */
	private $id;

	/**
	 * @var int
	 */
	private $customerId;

	/**
	 * @var string
	 */
	private $orderNumber;

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
	private $zip;

	/**
	 * @var string
	 */
	private $deliveryFirstName;

	/**
	 * @var string
	 */
	private $deliveryLastName;

	/**
	 * @var string
	 */
	private $deliveryCompanyName;

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
	private $deliveryZip;

	/**
	 * @var string
	 */
	private $note;

	/**
	 * @var array
	 */
	private $items;

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return int
	 */
	public function getCustomerId() {
		return $this->customerId;
	}

	/**
	 * @return string
	 */
	public function getOrderNumber() {
		return $this->orderNumber;
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
	 * @return string
	 */
	public function getCompanyName() {
		return $this->companyName;
	}

	/**
	 * @return string
	 */
	public function getCompanyNumber() {
		return $this->companyNumber;
	}

	/**
	 * @return string
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
	public function getZip() {
		return $this->zip;
	}

	/**
	 * @return string
	 */
	public function getDeliveryFirstName() {
		return $this->deliveryFirstName;
	}

	/**
	 * @return string
	 */
	public function getDeliveryLastName() {
		return $this->deliveryLastName;
	}

	/**
	 * @return string
	 */
	public function getDeliveryCompanyName() {
		return $this->deliveryCompanyName;
	}

	/**
	 * @return string
	 */
	public function getDeliveryTelephone() {
		return $this->deliveryTelephone;
	}

	/**
	 * @return string
	 */
	public function getDeliveryStreet() {
		return $this->deliveryStreet;
	}

	/**
	 * @return string
	 */
	public function getDeliveryCity() {
		return $this->deliveryCity;
	}

	/**
	 * @return string
	 */
	public function getDeliveryZip() {
		return $this->deliveryZip;
	}

	/**
	 * @return string
	 */
	public function getNote() {
		return $this->note;
	}

	/**
	 * @return \SS6\ShopBundle\Form\Admin\Order\OrderItemFormData[]
	 */
	public function getItems() {
		return $this->items;
	}

	/**
	 * @param int $id
	 */
	public function setId($id) {
		$this->id = $id;
	}

	/**
	 * @param int $customerId
	 */
	public function setCustomerId($customerId) {
		$this->customerId = $customerId;
	}

	/**
	 * @param string $orderNumber
	 */
	public function setOrderNumber($orderNumber) {
		$this->orderNumber = $orderNumber;
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
	 * @param string $telephone
	 */
	public function setTelephone($telephone) {
		$this->telephone = $telephone;
	}

	/**
	 * @param string $companyName
	 */
	public function setCompanyName($companyName) {
		$this->companyName = $companyName;
	}

	/**
	 * @param string $companyNumber
	 */
	public function setCompanyNumber($companyNumber) {
		$this->companyNumber = $companyNumber;
	}

	/**
	 * @param string $companyTaxNumber
	 */
	public function setCompanyTaxNumber($companyTaxNumber) {
		$this->companyTaxNumber = $companyTaxNumber;
	}

	/**
	 * @param string $street
	 */
	public function setStreet($street) {
		$this->street = $street;
	}

	/**
	 * @param string $city
	 */
	public function setCity($city) {
		$this->city = $city;
	}

	/**
	 * @param string $zip
	 */
	public function setZip($zip) {
		$this->zip = $zip;
	}

	/**
	 * @param string $deliveryFirstName
	 */
	public function setDeliveryFirstName($deliveryFirstName) {
		$this->deliveryFirstName = $deliveryFirstName;
	}

	/**
	 * @param string $deliveryLastName
	 */
	public function setDeliveryLastName($deliveryLastName) {
		$this->deliveryLastName = $deliveryLastName;
	}

	/**
	 * @param string $deliveryCompanyName
	 */
	public function setDeliveryCompanyName($deliveryCompanyName) {
		$this->deliveryCompanyName = $deliveryCompanyName;
	}

	/**
	 * @param string $deliveryTelephone
	 */
	public function setDeliveryTelephone($deliveryTelephone) {
		$this->deliveryTelephone = $deliveryTelephone;
	}

	/**
	 * @param string $deliveryStreet
	 */
	public function setDeliveryStreet($deliveryStreet) {
		$this->deliveryStreet = $deliveryStreet;
	}

	/**
	 * @param string $deliveryCity
	 */
	public function setDeliveryCity($deliveryCity) {
		$this->deliveryCity = $deliveryCity;
	}

	/**
	 * @param string $deliveryZip
	 */
	public function setDeliveryZip($deliveryZip) {
		$this->deliveryZip = $deliveryZip;
	}

	/**
	 * @param string $note
	 */
	public function setNote($note) {
		$this->note = $note;
	}

	/**
	 * @param \SS6\ShopBundle\Form\Admin\Order\OrderItemFormData[] $items
	 */
	public function setItems(array $items) {
		$this->items = $items;
	}

}
