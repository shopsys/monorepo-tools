<?php

namespace Shopsys\ShopBundle\Model\Customer;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Model\Customer\Mail\CustomerMailFacade;
use Shopsys\ShopBundle\Model\Order\Order;

class CustomerFacade
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Model\Customer\UserRepository
     */
    private $userRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Customer\CustomerService
     */
    private $customerService;

    /**
     * @var \Shopsys\ShopBundle\Model\Customer\Mail\CustomerMailFacade
     */
    private $customerMailFacade;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Shopsys\ShopBundle\Model\Customer\UserRepository $userRepository
     * @param \Shopsys\ShopBundle\Model\Customer\CustomerService $customerService
     * @param \Shopsys\ShopBundle\Model\Customer\Mail\CustomerMailFacade $customerMailFacade
     */
    public function __construct(
        EntityManager $em,
        UserRepository $userRepository,
        CustomerService $customerService,
        CustomerMailFacade $customerMailFacade
    ) {
        $this->em = $em;
        $this->userRepository = $userRepository;
        $this->customerService = $customerService;
        $this->customerMailFacade = $customerMailFacade;
    }

    /**
     * @param int $userId
     * @return \Shopsys\ShopBundle\Model\Customer\User
     */
    public function getUserById($userId)
    {
        return $this->userRepository->getUserById($userId);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Customer\UserData $userData
     * @return \Shopsys\ShopBundle\Model\Customer\User
     */
    public function register(UserData $userData)
    {
        $userByEmailAndDomain = $this->userRepository->findUserByEmailAndDomain($userData->email, $userData->domainId);

        $billingAddress = new BillingAddress(new BillingAddressData());

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
     * @param \Shopsys\ShopBundle\Model\Customer\CustomerData $customerData
     * @return \Shopsys\ShopBundle\Model\Customer\User
     */
    public function create(CustomerData $customerData)
    {
        $toFlush = [];
        $billingAddress = new BillingAddress($customerData->billingAddressData);
        $this->em->persist($billingAddress);
        $toFlush[] = $billingAddress;

        $deliveryAddress = $this->customerService->createDeliveryAddress($customerData->deliveryAddressData);
        if ($deliveryAddress !== null) {
            $this->em->persist($deliveryAddress);
            $toFlush[] = $deliveryAddress;
        }

        $userByEmailAndDomain = $this->userRepository->findUserByEmailAndDomain(
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
     * @param \Shopsys\ShopBundle\Model\Customer\CustomerData $customerData
     * @return \Shopsys\ShopBundle\Model\Customer\User
     */
    private function edit($userId, CustomerData $customerData)
    {
        $user = $this->userRepository->getUserById($userId);

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
     * @param \Shopsys\ShopBundle\Model\Customer\CustomerData $customerData
     * @return \Shopsys\ShopBundle\Model\Customer\User
     */
    public function editByAdmin($userId, CustomerData $customerData)
    {
        $user = $this->edit($userId, $customerData);

        $userByEmailAndDomain = $this->userRepository->findUserByEmailAndDomain(
            $customerData->userData->email,
            $customerData->userData->domainId
        );
        $this->customerService->changeEmail($user, $customerData->userData->email, $userByEmailAndDomain);

        $this->em->flush();

        return $user;
    }

    /**
     * @param int $userId
     * @param \Shopsys\ShopBundle\Model\Customer\CustomerData $customerData
     * @return \Shopsys\ShopBundle\Model\Customer\User
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
        $user = $this->userRepository->getUserById($userId);

        $this->em->remove($user);
        $this->em->flush();
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Customer\User $user
     * @param \Shopsys\ShopBundle\Model\Order\Order $order
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
