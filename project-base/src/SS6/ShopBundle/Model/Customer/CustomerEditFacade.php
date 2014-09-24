<?php

namespace SS6\ShopBundle\Model\Customer;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Customer\CustomerEditService;
use SS6\ShopBundle\Model\Customer\RegistrationService;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Order\OrderRepository;
use SS6\ShopBundle\Model\Order\OrderService;

class CustomerEditFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Order\OrderRepository
	 */
	private $orderRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\UserRepository
	 */
	private $userRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Order\OrderService
	 */
	private $orderService;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\RegistrationService
	 */
	private $registrationService;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\CustomerEditService
	 */
	private $customerEditService;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Order\OrderRepository $orderRepository
	 * @param \SS6\ShopBundle\Model\Customer\UserRepository $userRepository
	 * @param \SS6\ShopBundle\Model\Order\OrderService $orderService
	 * @param \SS6\ShopBundle\Model\Customer\RegistrationService $registrationService
	 * @param \SS6\ShopBundle\Model\Customer\CustomerEditService $customerEditService
	 */
	public function __construct(
		EntityManager $em,
		OrderRepository $orderRepository,
		UserRepository $userRepository,
		OrderService $orderService,
		RegistrationService $registrationService,
		CustomerEditService $customerEditService
	) {
		$this->em = $em;
		$this->orderRepository = $orderRepository;
		$this->userRepository = $userRepository;
		$this->orderService = $orderService;
		$this->registrationService = $registrationService;
		$this->customerEditService = $customerEditService;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\UserData $userData
	 * @return \SS6\ShopBundle\Model\Customer\User
	 */
	public function register(UserData $userData) {
		$userByEmailAndDomain = $this->userRepository->findUserByEmailAndDomain($userData->getEmail(), $userData->getDomainId());

		$billingAddress = new BillingAddress(new BillingAddressData());

		$user = $this->registrationService->create(
			$userData,
			$billingAddress,
			null,
			$userByEmailAndDomain
		);

		$this->em->persist($billingAddress);
		$this->em->persist($user);
		$this->em->flush();

		return $user;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\CustomerData $customerData
	 * @return \SS6\ShopBundle\Model\Customer\User
	 */
	public function create(CustomerData $customerData) {
		$billingAddress = new BillingAddress($customerData->getBillingAddressData());
		$this->em->persist($billingAddress);

		$deliveryAddress = $this->registrationService->createDeliveryAddress($customerData->getDeliveryAddressData());
		if ($deliveryAddress !== null) {
			$this->em->persist($deliveryAddress);
		}

		$userByEmailAndDomain = $this->userRepository->findUserByEmailAndDomain(
			$customerData->getUserData()->getEmail(),
			$customerData->getUserData()->getDomainId()
		);

		$user = $this->registrationService->create(
			$customerData->getUserData(),
			$billingAddress,
			$deliveryAddress,
			$userByEmailAndDomain
		);
		$this->em->persist($user);
		
		$this->em->flush();

		return $user;
	}

	/**
	 * @param int $userId
	 * @param \SS6\ShopBundle\Model\Customer\CustomerData $customerData
	 * @return \SS6\ShopBundle\Model\Customer\User
	 */
	private function edit($userId, CustomerData $customerData) {
		$user = $this->userRepository->getUserById($userId);

		$this->registrationService->edit($user, $customerData->getUserData());

		$user->getBillingAddress()->edit($customerData->getBillingAddressData());

		$oldDeliveryAddress = $user->getDeliveryAddress();
		$deliveryAddress = $this->registrationService->editDeliveryAddress(
			$user,
			$customerData->getDeliveryAddressData(),
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
	 * @param \SS6\ShopBundle\Model\Customer\CustomerData $customerData
	 * @return \SS6\ShopBundle\Model\Customer\User
	 */
	public function editByAdmin($userId, CustomerData $customerData) {
		$user = $this->edit($userId, $customerData);

		$userByEmailAndDOmain = $this->userRepository->findUserByEmailAndDomain(
			$customerData->getUserData()->getEmail(),
			$customerData->getUserData()->getDomainId()
		);
		$this->registrationService->changeEmail($user, $customerData->getUserData()->getEmail(), $userByEmailAndDOmain);

		$this->em->flush();

		return $user;
	}

	/**
	 * @param int $userId
	 * @param \SS6\ShopBundle\Model\Customer\CustomerData $customerData
	 * @return \SS6\ShopBundle\Model\Customer\User
	 */
	public function editByCustomer($userId, CustomerData $customerData) {
		$user = $this->edit($userId, $customerData);
		
		$this->em->flush();

		return $user;
	}

	/**
	 * @param int $userId
	 */
	public function delete($userId) {
		$user = $this->userRepository->getUserById($userId);

		$orders = $this->orderRepository->findByUserId($userId);
		$this->orderService->detachCustomer($orders);

		$this->em->remove($user);
		$this->em->flush();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 */
	public function amendCustomerDataFromOrder(User $user, Order $order) {
		$this->edit(
			$user->getId(),
			$this->customerEditService->getAmendedCustomerDataByOrder($user, $order)
		);

		$this->em->flush();
	}

}
