<?php

namespace Shopsys\FrameworkBundle\Model\Customer;

use Shopsys\FrameworkBundle\Component\Utils\Utils;
use Shopsys\FrameworkBundle\Model\Order\Order;

class CustomerDataFactory implements CustomerDataFactoryInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\BillingAddressDataFactoryInterface
     */
    protected $billingAddressDataFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddressDataFactoryInterface
     */
    protected $deliveryAddressDataFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\UserDataFactoryInterface
     */
    protected $userDataFactory;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\BillingAddressDataFactoryInterface $billingAddressDataFactory
     * @param \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddressDataFactoryInterface $deliveryAddressDataFactory
     * @param \Shopsys\FrameworkBundle\Model\Customer\UserDataFactoryInterface $userDataFactory
     */
    public function __construct(
        BillingAddressDataFactoryInterface $billingAddressDataFactory,
        DeliveryAddressDataFactoryInterface $deliveryAddressDataFactory,
        UserDataFactoryInterface $userDataFactory
    ) {
        $this->billingAddressDataFactory = $billingAddressDataFactory;
        $this->deliveryAddressDataFactory = $deliveryAddressDataFactory;
        $this->userDataFactory = $userDataFactory;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Customer\CustomerData
     */
    public function create(): CustomerData
    {
        return new CustomerData(
            $this->billingAddressDataFactory->create(),
            $this->deliveryAddressDataFactory->create(),
            $this->userDataFactory->create()
        );
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\User $user
     * @return \Shopsys\FrameworkBundle\Model\Customer\CustomerData
     */
    public function createFromUser(User $user): CustomerData
    {
        $customerData = new CustomerData(
            $this->billingAddressDataFactory->createFromBillingAddress($user->getBillingAddress()),
            $this->getDeliveryAddressDataFromUser($user),
            $this->userDataFactory->createFromUser($user)
        );

        return $customerData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\User $user
     * @return \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddressData
     */
    protected function getDeliveryAddressDataFromUser(User $user): DeliveryAddressData
    {
        if ($user->getDeliveryAddress()) {
            return $this->deliveryAddressDataFactory->createFromDeliveryAddress($user->getDeliveryAddress());
        }

        return $this->deliveryAddressDataFactory->create();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\User $user
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     * @return \Shopsys\FrameworkBundle\Model\Customer\CustomerData
     */
    public function createAmendedByOrder(User $user, Order $order): CustomerData
    {
        $billingAddress = $user->getBillingAddress();
        $deliveryAddress = $user->getDeliveryAddress();

        $customerData = $this->createFromUser($user);

        $customerData->userData->firstName = Utils::ifNull($user->getFirstName(), $order->getFirstName());
        $customerData->userData->lastName = Utils::ifNull($user->getLastName(), $order->getLastName());
        $customerData->billingAddressData = $this->getAmendedBillingAddressDataByOrder($order, $billingAddress);
        $customerData->deliveryAddressData = $this->getAmendedDeliveryAddressDataByOrder($order, $deliveryAddress);

        return $customerData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     * @param \Shopsys\FrameworkBundle\Model\Customer\BillingAddress $billingAddress
     * @return \Shopsys\FrameworkBundle\Model\Customer\BillingAddressData
     */
    protected function getAmendedBillingAddressDataByOrder(Order $order, BillingAddress $billingAddress)
    {
        $billingAddressData = $this->billingAddressDataFactory->createFromBillingAddress($billingAddress);

        if ($billingAddress->getStreet() === null) {
            $billingAddressData->companyCustomer = $order->getCompanyNumber() !== null;
            $billingAddressData->companyName = $order->getCompanyName();
            $billingAddressData->companyNumber = $order->getCompanyNumber();
            $billingAddressData->companyTaxNumber = $order->getCompanyTaxNumber();
            $billingAddressData->street = $order->getStreet();
            $billingAddressData->city = $order->getCity();
            $billingAddressData->postcode = $order->getPostcode();
            $billingAddressData->country = $order->getCountry();
        }

        return $billingAddressData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     * @param \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddress|null $deliveryAddress
     * @return \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddressData
     */
    protected function getAmendedDeliveryAddressDataByOrder(Order $order, ?DeliveryAddress $deliveryAddress = null)
    {
        if ($deliveryAddress === null) {
            $deliveryAddressData = $this->deliveryAddressDataFactory->create();
            $deliveryAddressData->addressFilled = !$order->isDeliveryAddressSameAsBillingAddress();
            $deliveryAddressData->street = $order->getDeliveryStreet();
            $deliveryAddressData->city = $order->getDeliveryCity();
            $deliveryAddressData->postcode = $order->getDeliveryPostcode();
            $deliveryAddressData->country = $order->getDeliveryCountry();
            $deliveryAddressData->companyName = $order->getDeliveryCompanyName();
            $deliveryAddressData->firstName = $order->getDeliveryFirstName();
            $deliveryAddressData->lastName = $order->getDeliveryLastName();
            $deliveryAddressData->telephone = $order->getDeliveryTelephone();
        } else {
            $deliveryAddressData = $this->deliveryAddressDataFactory->createFromDeliveryAddress($deliveryAddress);
        }

        if ($deliveryAddress !== null && $deliveryAddress->getTelephone() === null) {
            $deliveryAddressData->telephone = $order->getTelephone();
        }

        return $deliveryAddressData;
    }
}
