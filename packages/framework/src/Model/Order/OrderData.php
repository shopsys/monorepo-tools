<?php

namespace Shopsys\FrameworkBundle\Model\Order;

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
