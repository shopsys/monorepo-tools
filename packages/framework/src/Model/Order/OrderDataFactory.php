<?php

namespace Shopsys\FrameworkBundle\Model\Order;

use Shopsys\FrameworkBundle\Model\Order\Item\OrderItemData;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderPaymentData;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderTransportData;

class OrderDataFactory implements OrderDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Order\OrderData
     */
    public function create(): OrderData
    {
        return new OrderData();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     * @return \Shopsys\FrameworkBundle\Model\Order\OrderData
     */
    public function createFromOrder(Order $order): OrderData
    {
        $orderData = new OrderData();
        $this->fillFromOrder($orderData, $order);

        return $orderData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\OrderData $orderData
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     */
    protected function fillFromOrder(OrderData $orderData, Order $order)
    {
        $orderData->orderNumber = $order->getNumber();
        $orderData->status = $order->getStatus();
        $orderData->firstName = $order->getFirstName();
        $orderData->lastName = $order->getLastName();
        $orderData->email = $order->getEmail();
        $orderData->telephone = $order->getTelephone();
        $orderData->companyName = $order->getCompanyName();
        $orderData->companyNumber = $order->getCompanyNumber();
        $orderData->companyTaxNumber = $order->getCompanyTaxNumber();
        $orderData->street = $order->getStreet();
        $orderData->city = $order->getCity();
        $orderData->postcode = $order->getPostcode();
        $orderData->country = $order->getCountry();
        $orderData->deliveryAddressSameAsBillingAddress = $order->isDeliveryAddressSameAsBillingAddress();
        if (!$order->isDeliveryAddressSameAsBillingAddress()) {
            $orderData->deliveryFirstName = $order->getDeliveryFirstName();
            $orderData->deliveryLastName = $order->getDeliveryLastName();
            $orderData->deliveryCompanyName = $order->getDeliveryCompanyName();
            $orderData->deliveryTelephone = $order->getDeliveryTelephone();
            $orderData->deliveryStreet = $order->getDeliveryStreet();
            $orderData->deliveryCity = $order->getDeliveryCity();
            $orderData->deliveryPostcode = $order->getDeliveryPostcode();
            $orderData->deliveryCountry = $order->getDeliveryCountry();
        }
        $orderData->note = $order->getNote();
        $orderItemsWithoutTransportAndPaymentData = [];
        foreach ($order->getItemsWithoutTransportAndPayment() as $orderItem) {
            $orderItemData = new OrderItemData();
            $orderItemData->setFromEntity($orderItem);
            $orderItemsWithoutTransportAndPaymentData[$orderItem->getId()] = $orderItemData;
        }
        $orderData->itemsWithoutTransportAndPayment = $orderItemsWithoutTransportAndPaymentData;
        $orderData->createdAt = $order->getCreatedAt();
        $orderData->domainId = $order->getDomainId();
        $orderData->currency = $order->getCurrency();
        $orderData->createdAsAdministrator = $order->getCreatedAsAdministrator();
        $orderData->createdAsAdministratorName = $order->getCreatedAsAdministratorName();
        $orderData->orderTransport = new OrderTransportData();
        $orderData->orderTransport->setFromEntity($order->getOrderTransport());
        $orderData->orderPayment = new OrderPaymentData();
        $orderData->orderPayment->setFromEntity($order->getOrderPayment());
    }
}
