<?php

namespace SS6\ShopBundle\Model\Order;

use SS6\ShopBundle\Model\Order\Item\OrderItemData;
use SS6\ShopBundle\Model\Order\Item\OrderPaymentData;
use SS6\ShopBundle\Model\Order\Item\OrderTransportData;
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
	public $deliveryAddressSameAsBillingAddress;

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
	public $itemsWithoutTransportAndPayment;

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
	 * @var \SS6\ShopBundle\Model\Order\Item\OrderPaymentData
	 */
	public $orderPayment;

	/**
	 * @var \SS6\ShopBundle\Model\Order\Item\OrderTransportData
	 */
	public $orderTransport;

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
		$this->deliveryAddressSameAsBillingAddress = $order->isDeliveryAddressSameAsBillingAddress();
		if (!$order->isDeliveryAddressSameAsBillingAddress()) {
			$this->deliveryContactPerson = $order->getDeliveryContactPerson();
			$this->deliveryCompanyName = $order->getDeliveryCompanyName();
			$this->deliveryTelephone = $order->getDeliveryTelephone();
			$this->deliveryStreet = $order->getDeliveryStreet();
			$this->deliveryCity = $order->getDeliveryCity();
			$this->deliveryPostcode = $order->getDeliveryPostcode();
		}
		$this->note = $order->getNote();
		$orderItemsWithoutTransportAndPaymentData = [];
		foreach ($order->getItemsWithoutTransportAndPayment() as $orderItem) {
			$orderItemData = new OrderItemData();
			$orderItemData->setFromEntity($orderItem);
			$orderItemsWithoutTransportAndPaymentData[$orderItem->getId()] = $orderItemData;
		}
		$this->itemsWithoutTransportAndPayment = $orderItemsWithoutTransportAndPaymentData;
		$this->domainId = $order->getDomainId();
		$this->currency = $order->getCurrency();
		$this->createdAsAdministrator = $order->getCreatedAsAdministrator();
		$this->createdAsAdministratorName = $order->getCreatedAsAdministratorName();
		$this->orderTransport = new OrderTransportData();
		$this->orderTransport->setFromEntity($order->getOrderTransport());
		$this->orderPayment = new OrderPaymentData();
		$this->orderPayment->setFromEntity($order->getOrderPayment());
	}

}
