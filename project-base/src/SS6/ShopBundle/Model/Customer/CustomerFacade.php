<?php

namespace SS6\ShopBundle\Model\Customer;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Customer\CustomerService;
use SS6\ShopBundle\Model\Customer\Mail\CustomerMailFacade;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Order\OrderRepository;
use SS6\ShopBundle\Model\Order\OrderService;

class CustomerFacade {

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
	 * @var \SS6\ShopBundle\Model\Customer\CustomerService
	 */
	private $customerService;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\Mail\CustomerMailFacade
	 */
	private $customerMailFacade;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Order\OrderRepository $orderRepository
	 * @param \SS6\ShopBundle\Model\Customer\UserRepository $userRepository
	 * @param \SS6\ShopBundle\Model\Order\OrderService $orderService
	 * @param \SS6\ShopBundle\Model\Customer\CustomerService $customerService
	 * @param \SS6\ShopBundle\Model\Customer\Mail\CustomerMailFacade $customerMailFacade
	 */
	public function __construct(
		EntityManager $em,
		OrderRepository $orderRepository,
		UserRepository $userRepository,
		OrderService $orderService,
		CustomerService $customerService,
		CustomerMailFacade $customerMailFacade
	) {
		$this->em = $em;
		$this->orderRepository = $orderRepository;
		$this->userRepository = $userRepository;
		$this->orderService = $orderService;
		$this->customerService = $customerService;
		$this->customerMailFacade = $customerMailFacade;
	}

	/**
	 * @param int $userId
	 * @return \SS6\ShopBundle\Model\Customer\User
	 */
	public function getUserById($userId) {
		return $this->userRepository->getUserById($userId);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\UserData $userData
	 * @return \SS6\ShopBundle\Model\Customer\User
	 */
	public function register(UserData $userData) {
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
	 * @param \SS6\ShopBundle\Model\Customer\CustomerData $customerData
	 * @return \SS6\ShopBundle\Model\Customer\User
	 */
	public function create(CustomerData $customerData) {
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
	 * @param \SS6\ShopBundle\Model\Customer\CustomerData $customerData
	 * @return \SS6\ShopBundle\Model\Customer\User
	 */
	private function edit($userId, CustomerData $customerData) {
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
	 * @param \SS6\ShopBundle\Model\Customer\CustomerData $customerData
	 * @return \SS6\ShopBundle\Model\Customer\User
	 */
	public function editByAdmin($userId, CustomerData $customerData) {
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
			$this->customerService->getAmendedCustomerDataByOrder($user, $order)
		);

		$this->em->flush();
	}

}
