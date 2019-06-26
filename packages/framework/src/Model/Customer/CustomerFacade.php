<?php

namespace Shopsys\FrameworkBundle\Model\Customer;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Model\Customer\Mail\CustomerMailFacade;
use Shopsys\FrameworkBundle\Model\Order\Order;

class CustomerFacade
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\UserRepository
     */
    protected $userRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\CustomerDataFactoryInterface
     */
    protected $customerDataFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\Mail\CustomerMailFacade
     */
    protected $customerMailFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\BillingAddressFactoryInterface
     */
    protected $billingAddressFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddressFactoryInterface
     */
    protected $deliveryAddressFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\BillingAddressDataFactoryInterface
     */
    protected $billingAddressDataFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\UserFactoryInterface
     */
    protected $userFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\CustomerPasswordFacade
     */
    protected $customerPasswordFacade;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Model\Customer\UserRepository $userRepository
     * @param \Shopsys\FrameworkBundle\Model\Customer\CustomerDataFactoryInterface $customerDataFactory
     * @param \Shopsys\FrameworkBundle\Model\Customer\Mail\CustomerMailFacade $customerMailFacade
     * @param \Shopsys\FrameworkBundle\Model\Customer\BillingAddressFactoryInterface $billingAddressFactory
     * @param \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddressFactoryInterface $deliveryAddressFactory
     * @param \Shopsys\FrameworkBundle\Model\Customer\BillingAddressDataFactoryInterface $billingAddressDataFactory
     * @param \Shopsys\FrameworkBundle\Model\Customer\UserFactoryInterface $userFactory
     * @param \Shopsys\FrameworkBundle\Model\Customer\CustomerPasswordFacade $customerPasswordFacade
     */
    public function __construct(
        EntityManagerInterface $em,
        UserRepository $userRepository,
        CustomerDataFactoryInterface $customerDataFactory,
        CustomerMailFacade $customerMailFacade,
        BillingAddressFactoryInterface $billingAddressFactory,
        DeliveryAddressFactoryInterface $deliveryAddressFactory,
        BillingAddressDataFactoryInterface $billingAddressDataFactory,
        UserFactoryInterface $userFactory,
        CustomerPasswordFacade $customerPasswordFacade
    ) {
        $this->em = $em;
        $this->userRepository = $userRepository;
        $this->customerDataFactory = $customerDataFactory;
        $this->customerMailFacade = $customerMailFacade;
        $this->billingAddressFactory = $billingAddressFactory;
        $this->deliveryAddressFactory = $deliveryAddressFactory;
        $this->billingAddressDataFactory = $billingAddressDataFactory;
        $this->userFactory = $userFactory;
        $this->customerPasswordFacade = $customerPasswordFacade;
    }

    /**
     * @param int $userId
     * @return \Shopsys\FrameworkBundle\Model\Customer\User
     */
    public function getUserById($userId)
    {
        return $this->userRepository->getUserById($userId);
    }

    /**
     * @param string $email
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Customer\User|null
     */
    public function findUserByEmailAndDomain($email, $domainId)
    {
        return $this->userRepository->findUserByEmailAndDomain($email, $domainId);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\UserData $userData
     * @return \Shopsys\FrameworkBundle\Model\Customer\User
     */
    public function register(UserData $userData)
    {
        $billingAddressData = $this->billingAddressDataFactory->create();
        $billingAddress = $this->billingAddressFactory->create($billingAddressData);

        $user = $this->userFactory->create(
            $userData,
            $billingAddress,
            null
        );

        $this->setEmail($userData->email, $user);

        $this->em->persist($billingAddress);
        $this->em->persist($user);
        $this->em->flush();

        $this->customerMailFacade->sendRegistrationMail($user);

        return $user;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\CustomerData $customerData
     * @return \Shopsys\FrameworkBundle\Model\Customer\User
     */
    public function create(CustomerData $customerData)
    {
        $billingAddress = $this->billingAddressFactory->create($customerData->billingAddressData);
        $deliveryAddress = $this->deliveryAddressFactory->create($customerData->deliveryAddressData);

        $user = $this->userFactory->create(
            $customerData->userData,
            $billingAddress,
            $deliveryAddress
        );

        $this->setEmail($customerData->userData->email, $user);

        $this->em->persist($user);
        $this->em->flush($user);

        if ($customerData->sendRegistrationMail) {
            $this->customerMailFacade->sendRegistrationMail($user);
        }

        return $user;
    }

    /**
     * @param int $userId
     * @param \Shopsys\FrameworkBundle\Model\Customer\CustomerData $customerData
     * @return \Shopsys\FrameworkBundle\Model\Customer\User
     */
    protected function edit($userId, CustomerData $customerData)
    {
        $user = $this->getUserById($userId);

        $user->edit($customerData->userData);

        if ($customerData->userData->password !== null) {
            $this->customerPasswordFacade->changePassword($user, $customerData->userData->password);
        }

        $user->getBillingAddress()->edit($customerData->billingAddressData);

        $this->editDeliveryAddress($user, $customerData->deliveryAddressData);

        return $user;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\User $user
     * @param \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddressData $deliveryAddressData
     */
    protected function editDeliveryAddress(User $user, DeliveryAddressData $deliveryAddressData): void
    {
        if (!$deliveryAddressData->addressFilled) {
            $user->setDeliveryAddress(null);
            return;
        }

        $deliveryAddress = $user->getDeliveryAddress();
        if ($deliveryAddress instanceof DeliveryAddress) {
            $deliveryAddress->edit($deliveryAddressData);
        } else {
            $user->setDeliveryAddress($this->deliveryAddressFactory->create($deliveryAddressData));
        }
    }

    /**
     * @param int $userId
     * @param \Shopsys\FrameworkBundle\Model\Customer\CustomerData $customerData
     * @return \Shopsys\FrameworkBundle\Model\Customer\User
     */
    public function editByAdmin($userId, CustomerData $customerData)
    {
        $user = $this->edit($userId, $customerData);

        $this->setEmail($customerData->userData->email, $user);

        $this->em->flush();

        return $user;
    }

    /**
     * @param int $userId
     * @param \Shopsys\FrameworkBundle\Model\Customer\CustomerData $customerData
     * @return \Shopsys\FrameworkBundle\Model\Customer\User
     */
    public function editByCustomer($userId, CustomerData $customerData)
    {
        $user = $this->edit($userId, $customerData);

        $this->em->flush();

        return $user;
    }

    /**
     * @param int $userId
     */
    public function delete($userId)
    {
        $user = $this->getUserById($userId);

        $this->em->remove($user);
        $this->em->flush();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\User $user
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     */
    public function amendCustomerDataFromOrder(User $user, Order $order)
    {
        $this->edit(
            $user->getId(),
            $this->customerDataFactory->createAmendedByOrder($user, $order)
        );

        $this->em->flush();
    }

    /**
     * @param string $email
     * @param \Shopsys\FrameworkBundle\Model\Customer\User $user
     */
    protected function setEmail(string $email, User $user): void
    {
        $userByEmailAndDomain = $this->findUserByEmailAndDomain(
            $email,
            $user->getDomainId()
        );

        if ($userByEmailAndDomain !== null && $user->getId() !== $userByEmailAndDomain->getId()) {
            throw new \Shopsys\FrameworkBundle\Model\Customer\Exception\DuplicateEmailException($email);
        }

        $user->setEmail($email);
    }
}
