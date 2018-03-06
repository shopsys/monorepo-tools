<?php

namespace Shopsys\FrameworkBundle\Model\Customer;

use Shopsys\FrameworkBundle\Component\Utils;
use Shopsys\FrameworkBundle\Model\Order\Order;

class CustomerService
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\CustomerPasswordService
     */
    private $customerPasswordService;

    public function __construct(CustomerPasswordService $customerPasswordService)
    {
        $this->customerPasswordService = $customerPasswordService;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\UserData $userData
     * @param \Shopsys\FrameworkBundle\Model\Customer\BillingAddress $billingAddress
     * @param \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddress|null $deliveryAddress
     * @param \Shopsys\FrameworkBundle\Model\Customer\User|null $userByEmail
     * @return \Shopsys\FrameworkBundle\Model\Customer\User
     */
    public function create(
        UserData $userData,
        BillingAddress $billingAddress,
        DeliveryAddress $deliveryAddress = null,
        User $userByEmail = null
    ) {
        if ($userByEmail instanceof User) {
            $isSameEmail = (mb_strtolower($userByEmail->getEmail()) === mb_strtolower($userData->email));
            $isSameDomain = ($userByEmail->getDomainId() === $userData->domainId);
            if ($isSameEmail && $isSameDomain) {
                throw new \Shopsys\FrameworkBundle\Model\Customer\Exception\DuplicateEmailException($userData->email);
            }
        }

        $user = new User(
            $userData,
            $billingAddress,
            $deliveryAddress
        );
        $this->customerPasswordService->changePassword($user, $userData->password);

        return $user;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\User $user
     * @param \Shopsys\FrameworkBundle\Model\Customer\UserData
     */
    public function edit(User $user, UserData $userData)
    {
        $user->edit($userData);

        if ($userData->password !== null) {
            $this->customerPasswordService->changePassword($user, $userData->password);
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddressData
     * @return \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddress|null
     */
    public function createDeliveryAddress(DeliveryAddressData $deliveryAddressData)
    {
        if ($deliveryAddressData->addressFilled) {
            $deliveryAddress = new DeliveryAddress($deliveryAddressData);
        } else {
            $deliveryAddress = null;
        }

        return $deliveryAddress;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\User $user
     * @param \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddressData $deliveryAddressData
     * @param \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddress|null $deliveryAddress
     * @return \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddress|null
     */
    public function editDeliveryAddress(
        User $user,
        DeliveryAddressData $deliveryAddressData,
        DeliveryAddress $deliveryAddress = null
    ) {
        if ($deliveryAddressData->addressFilled) {
            if ($deliveryAddress instanceof DeliveryAddress) {
                $deliveryAddress->edit($deliveryAddressData);
            } else {
                $deliveryAddress = new DeliveryAddress($deliveryAddressData);
                $user->setDeliveryAddress($deliveryAddress);
            }
        } else {
            $user->setDeliveryAddress(null);
            $deliveryAddress = null;
        }

        return $deliveryAddress;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\User $user
     * @param string $email
     * @param \Shopsys\FrameworkBundle\Model\Customer\User|null $userByEmail
     */
    public function changeEmail(User $user, $email, User $userByEmail = null)
    {
        if ($email !== null) {
            $email = mb_strtolower($email);
        }

        if ($userByEmail instanceof User) {
            if (mb_strtolower($userByEmail->getEmail()) === $email && $user !== $userByEmail) {
                throw new \Shopsys\FrameworkBundle\Model\Customer\Exception\DuplicateEmailException($email);
            }
        }

        $user->changeEmail($email);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\User $user
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     * @return \Shopsys\FrameworkBundle\Model\Customer\CustomerData
     */
    public function getAmendedCustomerDataByOrder(User $user, Order $order)
    {
        $billingAddress = $user->getBillingAddress();
        $deliveryAddress = $user->getDeliveryAddress();

        $customerData = new CustomerData();
        $customerData->setFromEntity($user);

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
    private function getAmendedBillingAddressDataByOrder(Order $order, BillingAddress $billingAddress)
    {
        $billingAddressData = new BillingAddressData();
        $billingAddressData->setFromEntity($billingAddress);

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

        if ($billingAddress->getTelephone() === null) {
            $billingAddressData->telephone = $order->getTelephone();
        }

        return $billingAddressData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     * @param \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddress|null $deliveryAddress
     * @return \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddressData
     */
    private function getAmendedDeliveryAddressDataByOrder(Order $order, DeliveryAddress $deliveryAddress = null)
    {
        $deliveryAddressData = new DeliveryAddressData();

        if ($deliveryAddress === null) {
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
            $deliveryAddressData->setFromEntity($deliveryAddress);
        }

        if ($deliveryAddress !== null && $deliveryAddress->getTelephone() === null) {
            $deliveryAddressData->telephone = $order->getTelephone();
        }

        return $deliveryAddressData;
    }
}
