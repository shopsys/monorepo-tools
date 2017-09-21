<?php

namespace Shopsys\ShopBundle\Model\Order;

use Shopsys\ShopBundle\Model\Order\Item\OrderItemData;
use Shopsys\ShopBundle\Model\Order\Item\OrderPaymentData;
use Shopsys\ShopBundle\Model\Order\Item\OrderTransportData;

/**
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class OrderData
{
    const NEW_ITEM_PREFIX = 'new_';

    /**
     * @var \Shopsys\ShopBundle\Model\Transport\Transport
     */
    public $transport;

    /**
     * @var \Shopsys\ShopBundle\Model\Payment\Payment
     */
    public $payment;

    /**
     * @var string
     */
    public $orderNumber;

    /**
     * @var \Shopsys\ShopBundle\Model\Order\Status\OrderStatus
     */
    public $status;

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
     * @var \Shopsys\ShopBundle\Model\Country\Country
     */
    public $country;

    /**
     * @var bool
     */
    public $deliveryAddressSameAsBillingAddress;

    /**
     * @var string
     */
    public $deliveryFirstName;

    /**
     * @var string
     */
    public $deliveryLastName;

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
     * @var \Shopsys\ShopBundle\Model\Country\Country
     */
    public $deliveryCountry;

    /**
     * @var string
     */
    public $note;

    /**
     * @var \Shopsys\ShopBundle\Model\Order\Item\OrderItemData[]
     */
    public $itemsWithoutTransportAndPayment;

    /**
     * @var \DateTime|null
     */
    public $createdAt;

    /**
     * @var int
     */
    public $domainId;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Currency\Currency
     */
    public $currency;

    /**
     * @var \Shopsys\ShopBundle\Model\Administrator\Administrator|null
     */
    public $createdAsAdministrator;

    /**
     * @var string|null
     */
    public $createdAsAdministratorName;

    /**
     * @var \Shopsys\ShopBundle\Model\Order\Item\OrderPaymentData
     */
    public $orderPayment;

    /**
     * @var \Shopsys\ShopBundle\Model\Order\Item\OrderTransportData
     */
    public $orderTransport;

    /**
     * @param \Shopsys\ShopBundle\Model\Order\Order $order
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
     * @return \Shopsys\ShopBundle\Model\Order\Item\OrderItemData[]
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
