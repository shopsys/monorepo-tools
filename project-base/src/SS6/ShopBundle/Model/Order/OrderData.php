<?php

namespace SS6\ShopBundle\Model\Order;

use SS6\ShopBundle\Model\Order\Item\OrderItemData;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Pricing\Currency\Currency;
use SS6\ShopBundle\Model\Transport\Transport;

/**
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class OrderData {

	/**
	 * @var \SS6\ShopBundle\Model\Transport\Transport
	 */
	private $transport;

	/**
	 * @var \SS6\ShopBundle\Model\Payment\Payment
	 */
	private $payment;

	/**
	 * @var int
	 */
	private $customerId;

	/**
	 * @var string
	 */
	private $orderNumber;

	/**
	 * @var int
	 */
	private $statusId;

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
	 * @var boolean
	 */
	private $deliveryAddressFilled;

	/**
	 * @var string
	 */
	private $deliveryContactPerson;

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
	private $deliveryPostcode;

	/**
	 * @var string
	 */
	private $note;

	/**
	 * @var \SS6\ShopBundle\Model\Order\Item\OrderItemData[]
	 */
	private $items;

	/**
	 * @var int
	 */
	private $domainId;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Currency\Currency
	 */
	private $currency;

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
	 * @return int
	 */
	public function getStatusId() {
		return $this->statusId;
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
	public function getPostcode() {
		return $this->postcode;
	}

	/**
	 * @return string
	 */
	public function getDeliveryContactPerson() {
		return $this->deliveryContactPerson;
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
	public function getDeliveryPostcode() {
		return $this->deliveryPostcode;
	}

	/**
	 * @return string
	 */
	public function getNote() {
		return $this->note;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Order\Item\OrderItemData[]
	 */
	public function getItems() {
		return $this->items;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Transport\Transport|null
	 */
	public function getTransport() {
		return $this->transport;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Payment\Payment;
	 */
	public function getPayment() {
		return $this->payment;
	}

	/**
	 * @return int
	 */
	public function getDomainId() {
		return $this->domainId;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Currency\Currency
	 */
	public function getCurrency() {
		return $this->currency;
	}

	/**
	 * @return boolean
	 */
	public function isCompanyCustomer() {
		return $this->companyCustomer;
	}

	/**
	 * @return boolean
	 */
	public function isDeliveryAddressFilled() {
		return $this->deliveryAddressFilled;
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
	 * @param int $statusId
	 */
	public function setStatusId($statusId) {
		$this->statusId = $statusId;
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
	 * @param string $postcode
	 */
	public function setPostcode($postcode) {
		$this->postcode = $postcode;
	}

	/**
	 * @param string $deliveryContactPerson
	 */
	public function setDeliveryContactPerson($deliveryContactPerson) {
		$this->deliveryContactPerson = $deliveryContactPerson;
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
	 * @param string $deliveryPostcode
	 */
	public function setDeliveryPostcode($deliveryPostcode) {
		$this->deliveryPostcode = $deliveryPostcode;
	}

	/**
	 * @param string $note
	 */
	public function setNote($note) {
		$this->note = $note;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Item\OrderItemData[] $items
	 */
	public function setItems(array $items) {
		$this->items = $items;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 */
	public function setTransport(Transport $transport = null) {
		$this->transport = $transport;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment $payment
	 */
	public function setPayment(Payment $payment = null) {
		$this->payment = $payment;
	}

	/**
	 * @param boolean $companyCustomer
	 */
	public function setCompanyCustomer($companyCustomer) {
		$this->companyCustomer = $companyCustomer;
	}

	/**
	 * @param boolean $deliveryAddressFilled
	 */
	public function setDeliveryAddressFilled($deliveryAddressFilled) {
		$this->deliveryAddressFilled = $deliveryAddressFilled;
	}

	/**
	 * @param int $domainId
	 */
	public function setDomainId($domainId) {
		$this->domainId = $domainId;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\Currency $currency
	 */
	public function setCurrency(Currency $currency) {
		$this->currency = $currency;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 */
	public function setFromEntity(Order $order) {
		$this->setOrderNumber($order->getNumber());
		$this->setStatusId($order->getStatus()->getId());
		if ($order->getCustomer()) {
			$this->setCustomerId($order->getCustomer()->getId());
		}
		$this->setFirstName($order->getFirstName());
		$this->setLastName($order->getLastName());
		$this->setEmail($order->getEmail());
		$this->setTelephone($order->getTelephone());
		$this->setCompanyName($order->getCompanyName());
		$this->setCompanyNumber($order->getCompanyNumber());
		$this->setCompanyTaxNumber($order->getCompanyTaxNumber());
		$this->setStreet($order->getStreet());
		$this->setCity($order->getCity());
		$this->setPostcode($order->getPostcode());
		$this->setDeliveryContactPerson($order->getDeliveryContactPerson());
		$this->setDeliveryCompanyName($order->getDeliveryCompanyName());
		$this->setDeliveryTelephone($order->getDeliveryTelephone());
		$this->setDeliveryStreet($order->getDeliveryStreet());
		$this->setDeliveryCity($order->getDeliveryCity());
		$this->setDeliveryPostcode($order->getDeliveryPostcode());
		$this->setNote($order->getNote());
		$orderItemsData = array();
		foreach ($order->getItems() as $orderItem) {
			$orderItemData = new OrderItemData();
			$orderItemData->setFromEntity($orderItem);
			$orderItemsData[$orderItem->getId()] = $orderItemData;
		}
		$this->setItems($orderItemsData);
		$this->setDomainId($order->getDomainId());
		$this->setCurrency($order->getCurrency());
	}

}
