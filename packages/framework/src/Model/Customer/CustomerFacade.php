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
     * @var \Shopsys\FrameworkBundle\Model\Customer\CustomerService
     */
    protected $customerService;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\Mail\CustomerMailFacade
     */
    protected $customerMailFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\BillingAddressFactoryInterface
     */
    protected $billingAddressFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\BillingAddressDataFactoryInterface
     */
    private $billingAddressDataFactory;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Model\Customer\UserRepository $userRepository
     * @param \Shopsys\FrameworkBundle\Model\Customer\CustomerService $customerService
     * @param \Shopsys\FrameworkBundle\Model\Customer\Mail\CustomerMailFacade $customerMailFacade
     * @param \Shopsys\FrameworkBundle\Model\Customer\BillingAddressFactoryInterface $billingAddressFactory
     * @param \Shopsys\FrameworkBundle\Model\Customer\BillingAddressDataFactoryInterface $billingAddressDataFactory
     */
    public function __construct(
        EntityManagerInterface $em,
        UserRepository $userRepository,
        CustomerService $customerService,
        CustomerMailFacade $customerMailFacade,
        BillingAddressFactoryInterface $billingAddressFactory,
        BillingAddressDataFactoryInterface $billingAddressDataFactory
    ) {
        $this->em = $em;
        $this->userRepository = $userRepository;
        $this->customerService = $customerService;
        $this->customerMailFacade = $customerMailFacade;
        $this->billingAddressFactory = $billingAddressFactory;
        $this->billingAddressDataFactory = $billingAddressDataFactory;
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
        $userByEmailAndDomain = $this->findUserByEmailAndDomain($userData->email, $userData->domainId);

        $billingAddressData = $this->billingAddressDataFactory->create();
        $billingAddress = $this->billingAddressFactory->create($billingAddressData);

        $user = $this->customerService->create(
            $userData,
            $billingAddress,
            null,
            $userByEmailAndDomain
        );

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
        $toFlush = [];
        $billingAddress = $this->billingAddressFactory->create($customerData->billingAddressData);
        $this->em->persist($billingAddress);
        $toFlush[] = $billingAddress;

        $deliveryAddress = $this->customerService->createDeliveryAddress($customerData->deliveryAddressData);
        if ($deliveryAddress !== null) {
            $this->em->persist($deliveryAddress);
            $toFlush[] = $deliveryAddress;
        }

        $userByEmailAndDomain = $this->findUserByEmailAndDomain(
            $customerData->userData->email,
            $customerData->userData->domainId
        );

        $user = $this->customerService->create(
            $customerData->userData,
            $billingAddress,
            $deliveryAddress,
            $userByEmailAndDomain
        );
        $this->em->persist($user);
        $toFlush[] = $user;

        $this->em->flush($toFlush);

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

        $this->customerService->edit($user, $customerData->userData);

        $user->getBillingAddress()->edit($customerData->billingAddressData);

        $oldDeliveryAddress = $user->getDeliveryAddress();
        $deliveryAddress = $this->customerService->editDeliveryAddress(
            $user,
            $customerData->deliveryAddressData,
            $oldDeliveryAddress
        );

        if ($deliveryAddress !== null) {
            $this->em->persist($deliveryAddress);
        } else {
            if ($oldDeliveryAddress !== null) {
                $this->em->remove($oldDeliveryAddress);
            }
        }

        return $user;
    }

    /**
     * @param int $userId
     * @param \Shopsys\FrameworkBundle\Model\Customer\CustomerData $customerData
     * @return \Shopsys\FrameworkBundle\Model\Customer\User
     */
    public function editByAdmin($userId, CustomerData $customerData)
    {
        $user = $this->edit($userId, $customerData);

        $userByEmailAndDomain = $this->findUserByEmailAndDomain(
            $customerData->userData->email,
            $customerData->userData->domainId
        );
        $this->customerService->changeEmail($user, $customerData->userData->email, $userByEmailAndDomain);

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
            $this->customerService->getAmendedCustomerDataByOrder($user, $order)
        );

        $this->em->flush();
    }
}
