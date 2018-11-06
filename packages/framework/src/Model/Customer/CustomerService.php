<?php

namespace Shopsys\FrameworkBundle\Model\Customer;

use Shopsys\FrameworkBundle\Component\Utils\Utils;
use Shopsys\FrameworkBundle\Model\Order\Order;

class CustomerService
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\CustomerPasswordService
     */
    private $customerPasswordService;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddressFactoryInterface
     */
    protected $deliveryAddressFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\UserFactoryInterface
     */
    protected $userFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\CustomerDataFactoryInterface
     */
    private $customerDataFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\BillingAddressDataFactoryInterface
     */
    private $billingAddressDataFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddressDataFactoryInterface
     */
    private $deliveryAddressDataFactory;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\CustomerPasswordService $customerPasswordService
     * @param \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddressFactoryInterface $deliveryAddressFactory
     * @param \Shopsys\FrameworkBundle\Model\Customer\UserFactoryInterface $userFactory
     * @param \Shopsys\FrameworkBundle\Model\Customer\CustomerDataFactoryInterface $customerDataFactory
     * @param \Shopsys\FrameworkBundle\Model\Customer\BillingAddressDataFactoryInterface $billingAddressDataFactory
     * @param \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddressDataFactoryInterface $deliveryAddressDataFactory
     */
    public function __construct(
        CustomerPasswordService $customerPasswordService,
        DeliveryAddressFactoryInterface $deliveryAddressFactory,
        UserFactoryInterface $userFactory,
        CustomerDataFactoryInterface $customerDataFactory,
        BillingAddressDataFactoryInterface $billingAddressDataFactory,
        DeliveryAddressDataFactoryInterface $deliveryAddressDataFactory
    ) {
        $this->customerPasswordService = $customerPasswordService;
        $this->deliveryAddressFactory = $deliveryAddressFactory;
        $this->userFactory = $userFactory;
        $this->customerDataFactory = $customerDataFactory;
        $this->billingAddressDataFactory = $billingAddressDataFactory;
        $this->deliveryAddressDataFactory = $deliveryAddressDataFactory;
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

        $user = $this->userFactory->create(
            $userData,
            $billingAddress,
            $deliveryAddress
        );
        $this->customerPasswordService->changePassword($user, $userData->password);

        return $user;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\User $user
     * @param \Shopsys\FrameworkBundle\Model\Customer\UserData $userData
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
     * @param \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddressData $deliveryAddressData
     * @return \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddress|null
     */
    public function createDeliveryAddress(DeliveryAddressData $deliveryAddressData)
    {
        if ($deliveryAddressData->addressFilled) {
            $deliveryAddress = $this->deliveryAddressFactory->create($deliveryAddressData);
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
                $deliveryAddress = $this->deliveryAddressFactory->create($deliveryAddressData);
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

        $customerData = $this->customerDataFactory->createFromUser($user);

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
    private function getAmendedDeliveryAddressDataByOrder(Order $order, DeliveryAddress $deliveryAddress = null)
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
