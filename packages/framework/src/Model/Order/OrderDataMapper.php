<?php

namespace Shopsys\FrameworkBundle\Model\Order;

class OrderDataMapper
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\OrderDataFactoryInterface
     */
    private $orderDataFactory;

    public function __construct(OrderDataFactoryInterface $orderDataFactory)
    {
        $this->orderDataFactory = $orderDataFactory;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\FrontOrderData $frontOrderData
     * @return \Shopsys\FrameworkBundle\Model\Order\OrderData
     */
    public function getOrderDataFromFrontOrderData(FrontOrderData $frontOrderData)
    {
        $orderData = $this->orderDataFactory->create();
        $orderData->transport = $frontOrderData->transport;
        $orderData->payment = $frontOrderData->payment;
        $orderData->orderNumber = $frontOrderData->orderNumber;
        $orderData->status = $frontOrderData->status;
        $orderData->firstName = $frontOrderData->firstName;
        $orderData->lastName = $frontOrderData->lastName;
        $orderData->email = $frontOrderData->email;
        $orderData->telephone = $frontOrderData->telephone;
        $orderData->street = $frontOrderData->street;
        $orderData->city = $frontOrderData->city;
        $orderData->postcode = $frontOrderData->postcode;
        $orderData->country = $frontOrderData->country;
        $orderData->deliveryAddressSameAsBillingAddress = $frontOrderData->deliveryAddressSameAsBillingAddress;
        $orderData->deliveryFirstName = $frontOrderData->deliveryFirstName;
        $orderData->deliveryLastName = $frontOrderData->deliveryLastName;
        $orderData->deliveryCompanyName = $frontOrderData->deliveryCompanyName;
        $orderData->deliveryTelephone = $frontOrderData->deliveryTelephone;
        $orderData->deliveryStreet = $frontOrderData->deliveryStreet;
        $orderData->deliveryCity = $frontOrderData->deliveryCity;
        $orderData->deliveryPostcode = $frontOrderData->deliveryPostcode;
        $orderData->deliveryCountry = $frontOrderData->deliveryCountry;
        $orderData->note = $frontOrderData->note;
        $orderData->itemsWithoutTransportAndPayment = $frontOrderData->itemsWithoutTransportAndPayment;
        $orderData->orderTransport = $frontOrderData->orderTransport;
        $orderData->orderPayment = $frontOrderData->orderPayment;
        $orderData->domainId = $frontOrderData->domainId;
        $orderData->currency = $frontOrderData->currency;

        if ($frontOrderData->companyCustomer) {
            $orderData->companyName = $frontOrderData->companyName;
            $orderData->companyNumber = $frontOrderData->companyNumber;
            $orderData->companyTaxNumber = $frontOrderData->companyTaxNumber;
        } else {
            $orderData->companyName = null;
            $orderData->companyNumber = null;
            $orderData->companyTaxNumber = null;
        }

        return $orderData;
    }
}
