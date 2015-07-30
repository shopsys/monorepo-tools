<?php

namespace SS6\ShopBundle\Model\Order;

use SS6\ShopBundle\Model\Order\Item\OrderItemData;
use SS6\ShopBundle\Model\Order\Order;

/**
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class OrderData {

	/**
	 * @var \SS6\ShopBundle\Model\Transport\Transport
	 */
	public $transport;

	/**
	 * @var \SS6\ShopBundle\Model\Payment\Payment
	 */
	public $payment;

	/**
	 * @var string
	 */
	public $orderNumber;

	/**
	 * @var int
	 */
	public $statusId;

	/**
	 * @var string
	 */
	public $firstName;

	/**
	 * @var string
	 */
	public $lastName;

	/**
	 * @var string
	 */
	public $email;

	/**
	 * @var string
	 */
	public $telephone;

	/**
	 * @var string
	 */
	public $companyName;

	/**
	 * @var string
	 */
	public $companyNumber;

	/**
	 * @var string
	 */
	public $companyTaxNumber;

	/**
	 * @var string
	 */
	public $street;

	/**
	 * @var string
	 */
	public $city;

	/**
	 * @var string
	 */
	public $postcode;

	/**
	 * @var bool
	 */
	public $deliveryAddressFilled;

	/**
	 * @var string
	 */
	public $deliveryContactPerson;

	/**
	 * @var string
	 */
	public $deliveryCompanyName;

	/**
	 * @var string
	 */
	public $deliveryTelephone;

	/**
	 * @var string
	 */
	public $deliveryStreet;

	/**
	 * @var string
	 */
	public $deliveryCity;

	/**
	 * @var string
	 */
	public $deliveryPostcode;

	/**
	 * @var string
	 */
	public $note;

	/**
	 * @var \SS6\ShopBundle\Model\Order\Item\OrderItemData[]
	 */
	public $items;

	/**
	 * @var int
	 */
	public $domainId;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Currency\Currency
	 */
	public $currency;

	/**
	 * @var \SS6\ShopBundle\Model\Administrator\Administrator|null
	 */
	public $createdAsAdministrator;

	/**
	 * @var string|null
	 */
	public $createdAsAdministratorName;

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 */
	public function setFromEntity(Order $order) {
		$this->orderNumber = $order->getNumber();
		$this->statusId = $order->getStatus()->getId();
		$this->firstName = $order->getFirstName();
		$this->lastName = $order->getLastName();
		$this->email = $order->getEmail();
		$this->telephone = $order->getTelephone();
		$this->companyName = $order->getCompanyName();
		$this->companyNumber = $order->getCompanyNumber();
		$this->companyTaxNumber = $order->getCompanyTaxNumber();
		$this->street = $order->getStreet();
		$this->city = $order->getCity();
		$this->postcode = $order->getPostcode();
		$this->deliveryContactPerson = $order->getDeliveryContactPerson();
		$this->deliveryCompanyName = $order->getDeliveryCompanyName();
		$this->deliveryTelephone = $order->getDeliveryTelephone();
		$this->deliveryStreet = $order->getDeliveryStreet();
		$this->deliveryCity = $order->getDeliveryCity();
		$this->deliveryPostcode = $order->getDeliveryPostcode();
		$this->note = $order->getNote();
		$orderItemsData = [];
		foreach ($order->getItems() as $orderItem) {
			$orderItemData = new OrderItemData();
			$orderItemData->setFromEntity($orderItem);
			$orderItemsData[$orderItem->getId()] = $orderItemData;
		}
		$this->items = $orderItemsData;
		$this->domainId = $order->getDomainId();
		$this->currency = $order->getCurrency();
		$this->createdAsAdministrator = $order->getCreatedAsAdministrator();
		$this->createdAsAdministratorName = $order->getCreatedAsAdministratorName();
	}

}
