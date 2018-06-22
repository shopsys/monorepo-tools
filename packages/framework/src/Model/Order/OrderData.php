<?php

namespace Shopsys\FrameworkBundle\Model\Order;

use Shopsys\FrameworkBundle\Model\Order\Item\OrderItemData;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderPaymentData;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderTransportData;

class OrderData
{
    const NEW_ITEM_PREFIX = 'new_';

    /**
     * @var \Shopsys\FrameworkBundle\Model\Transport\Transport|null
     */
    public $transport;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Payment\Payment|null
     */
    public $payment;

    /**
     * @var string|null
     */
    public $orderNumber;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus|null
     */
    public $status;

    /**
     * @var string|null
     */
    public $firstName;

    /**
     * @var string|null
     */
    public $lastName;

    /**
     * @var string|null
     */
    public $email;

    /**
     * @var string|null
     */
    public $telephone;

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
     * @var \Shopsys\FrameworkBundle\Model\Country\Country|null
     */
    public $country;

    /**
     * @var bool|null
     */
    public $deliveryAddressSameAsBillingAddress;

    /**
     * @var string|null
     */
    public $deliveryFirstName;

    /**
     * @var string|null
     */
    public $deliveryLastName;

    /**
     * @var string|null
     */
    public $deliveryCompanyName;

    /**
     * @var string|null
     */
    public $deliveryTelephone;

    /**
     * @var string|null
     */
    public $deliveryStreet;

    /**
     * @var string|null
     */
    public $deliveryCity;

    /**
     * @var string|null
     */
    public $deliveryPostcode;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Country\Country|null
     */
    public $deliveryCountry;

    /**
     * @var string|null
     */
    public $note;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemData[]
     */
    public $itemsWithoutTransportAndPayment;

    /**
     * @var \DateTime|null
     */
    public $createdAt;

    /**
     * @var int|null
     */
    public $domainId;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency|null
     */
    public $currency;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Administrator\Administrator|null
     */
    public $createdAsAdministrator;

    /**
     * @var string|null
     */
    public $createdAsAdministratorName;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Item\OrderPaymentData|null
     */
    public $orderPayment;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Item\OrderTransportData|null
     */
    public $orderTransport;

    public function __construct()
    {
        $this->itemsWithoutTransportAndPayment = [];
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     */
    public function setFromEntity(Order $order)
    {
        $this->orderNumber = $order->getNumber();
        $this->status = $order->getStatus();
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
        $this->country = $order->getCountry();
        $this->deliveryAddressSameAsBillingAddress = $order->isDeliveryAddressSameAsBillingAddress();
        if (!$order->isDeliveryAddressSameAsBillingAddress()) {
            $this->deliveryFirstName = $order->getDeliveryFirstName();
            $this->deliveryLastName = $order->getDeliveryLastName();
            $this->deliveryCompanyName = $order->getDeliveryCompanyName();
            $this->deliveryTelephone = $order->getDeliveryTelephone();
            $this->deliveryStreet = $order->getDeliveryStreet();
            $this->deliveryCity = $order->getDeliveryCity();
            $this->deliveryPostcode = $order->getDeliveryPostcode();
            $this->deliveryCountry = $order->getDeliveryCountry();
        }
        $this->note = $order->getNote();
        $orderItemsWithoutTransportAndPaymentData = [];
        foreach ($order->getItemsWithoutTransportAndPayment() as $orderItem) {
            $orderItemData = new OrderItemData();
            $orderItemData->setFromEntity($orderItem);
            $orderItemsWithoutTransportAndPaymentData[$orderItem->getId()] = $orderItemData;
        }
        $this->itemsWithoutTransportAndPayment = $orderItemsWithoutTransportAndPaymentData;
        $this->createdAt = $order->getCreatedAt();
        $this->domainId = $order->getDomainId();
        $this->currency = $order->getCurrency();
        $this->createdAsAdministrator = $order->getCreatedAsAdministrator();
        $this->createdAsAdministratorName = $order->getCreatedAsAdministratorName();
        $this->orderTransport = new OrderTransportData();
        $this->orderTransport->setFromEntity($order->getOrderTransport());
        $this->orderPayment = new OrderPaymentData();
        $this->orderPayment->setFromEntity($order->getOrderPayment());
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemData[]
     */
    public function getNewItemsWithoutTransportAndPayment()
    {
        $newItemsWithoutTransportAndPayment = [];
        foreach ($this->itemsWithoutTransportAndPayment as $index => $item) {
            if (strpos($index, self::NEW_ITEM_PREFIX) === 0) {
                $newItemsWithoutTransportAndPayment[] = $item;
            }
        }

        return $newItemsWithoutTransportAndPayment;
    }
}
