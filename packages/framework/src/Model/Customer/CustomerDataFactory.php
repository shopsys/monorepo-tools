<?php

namespace Shopsys\FrameworkBundle\Model\Customer;

class CustomerDataFactory implements CustomerDataFactoryInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\BillingAddressDataFactoryInterface
     */
    private $billingAddressDataFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddressDataFactoryInterface
     */
    private $deliveryAddressDataFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\UserDataFactoryInterface
     */
    private $userDataFactory;

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
}
