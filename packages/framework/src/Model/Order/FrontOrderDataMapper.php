<?php

namespace Shopsys\FrameworkBundle\Model\Order;

use Shopsys\FrameworkBundle\Model\Customer\User;

class FrontOrderDataMapper
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\FrontOrderData $frontOrderData
     * @param \Shopsys\FrameworkBundle\Model\Customer\User $user
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     */
    public function prefillFrontFormData(FrontOrderData $frontOrderData, User $user, ?Order $order)
    {
        if ($order instanceof Order) {
            $this->prefillTransportAndPaymentFromOrder($frontOrderData, $order);
        }
        $this->prefillFrontFormDataFromCustomer($frontOrderData, $user);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\FrontOrderData $frontOrderData
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     */
    protected function prefillTransportAndPaymentFromOrder(FrontOrderData $frontOrderData, Order $order)
    {
        $frontOrderData->transport = $order->getTransport()->isDeleted() ? null : $order->getTransport();
        $frontOrderData->payment = $order->getPayment()->isDeleted() ? null : $order->getPayment();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\FrontOrderData $frontOrderData
     * @param \Shopsys\FrameworkBundle\Model\Customer\User $user
     */
    protected function prefillFrontFormDataFromCustomer(FrontOrderData $frontOrderData, User $user)
    {
        $frontOrderData->firstName = $user->getFirstName();
        $frontOrderData->lastName = $user->getLastName();
        $frontOrderData->email = $user->getEmail();
        $frontOrderData->telephone = $user->getTelephone();
        $frontOrderData->companyCustomer = $user->getBillingAddress()->isCompanyCustomer();
        $frontOrderData->companyName = $user->getBillingAddress()->getCompanyName();
        $frontOrderData->companyNumber = $user->getBillingAddress()->getCompanyNumber();
        $frontOrderData->companyTaxNumber = $user->getBillingAddress()->getCompanyTaxNumber();
        $frontOrderData->street = $user->getBillingAddress()->getStreet();
        $frontOrderData->city = $user->getBillingAddress()->getCity();
        $frontOrderData->postcode = $user->getBillingAddress()->getPostcode();
        $frontOrderData->country = $user->getBillingAddress()->getCountry();
        if ($user->getDeliveryAddress() !== null) {
            $frontOrderData->deliveryAddressSameAsBillingAddress = false;
            $frontOrderData->deliveryFirstName = $user->getDeliveryAddress()->getFirstName();
            $frontOrderData->deliveryLastName = $user->getDeliveryAddress()->getLastName();
            $frontOrderData->deliveryCompanyName = $user->getDeliveryAddress()->getCompanyName();
            $frontOrderData->deliveryTelephone = $user->getDeliveryAddress()->getTelephone();
            $frontOrderData->deliveryStreet = $user->getDeliveryAddress()->getStreet();
            $frontOrderData->deliveryCity = $user->getDeliveryAddress()->getCity();
            $frontOrderData->deliveryPostcode = $user->getDeliveryAddress()->getPostcode();
            $frontOrderData->deliveryCountry = $user->getDeliveryAddress()->getCountry();
        } else {
            $frontOrderData->deliveryAddressSameAsBillingAddress = true;
        }
    }
}
