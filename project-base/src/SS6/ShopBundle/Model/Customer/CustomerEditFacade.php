<?php

namespace SS6\ShopBundle\Model\Customer;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Component\Condition;
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
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Order\OrderRepository $orderRepository
	 * @param \SS6\ShopBundle\Model\Customer\UserRepository $userRepository
	 * @param \SS6\ShopBundle\Model\Order\OrderService $orderService
	 * @param \SS6\ShopBundle\Model\Customer\RegistrationService $registrationService
	 */
	public function __construct(EntityManager $em,
			OrderRepository $orderRepository,
			UserRepository $userRepository,
			OrderService $orderService,
			RegistrationService $registrationService) {
		$this->em = $em;
		$this->orderRepository = $orderRepository;
		$this->userRepository = $userRepository;
		$this->orderService = $orderService;
		$this->registrationService = $registrationService;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\UserData $userData
	 * @return \SS6\ShopBundle\Model\Customer\User
	 */
	public function register(UserData $userData) {
		$userByEmail = $this->userRepository->findUserByEmail($userData->getEmail());

		$billingAddress = new BillingAddress(new BillingAddressData());

		$user = $this->registrationService->create(
			$userData,
			$billingAddress,
			null,
			$userByEmail
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
		$billingAddress = new BillingAddress($customerData->getBillingAddress());
		$this->em->persist($billingAddress);

		$deliveryAddress = $this->registrationService->createDeliveryAddress($customerData->getDeliveryAddress());
		if ($deliveryAddress !== null) {
			$this->em->persist($deliveryAddress);
		}

		$userByEmail = $this->userRepository->findUserByEmail($customerData->getUser()->getEmail());

		$user = $this->registrationService->create(
			$customerData->getUser(),
			$billingAddress,
			$deliveryAddress,
			$userByEmail
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

		$this->registrationService->edit($user, $customerData->getUser());

		$user->getBillingAddress()->edit($customerData->getBillingAddress());

		$oldDeliveryAddress = $user->getDeliveryAddress();
		$deliveryAddress = $this->registrationService->editDeliveryAddress(
			$user,
			$customerData->getDeliveryAddress(),
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

		$userByEmail = $this->userRepository->findUserByEmail($customerData->getUser()->getEmail());
		$this->registrationService->changeEmail($user, $customerData->getUser()->getEmail(), $userByEmail);

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
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function amendCustomerDataFromOrder(User $user, Order $order) {
		$billingAddress = $user->getBillingAddress();
		$deliveryAddress = $user->getDeliveryAddress();

		if ($billingAddress->getStreet() === null) {
			$companyCustomer = true;
			$companyName = $order->getCompanyName();
			$companyNumber = $order->getCompanyNumber();
			$companyTaxNumber = $order->getCompanyTaxNumber();
			$street = $order->getStreet();
			$city = $order->getCity();
			$postcode = $order->getPostcode();
		} else {
			$companyCustomer = $billingAddress->isCompanyCustomer();
			$companyName = $billingAddress->getCompanyName();
			$companyNumber = $billingAddress->getCompanyNumber();
			$companyTaxNumber = $billingAddress->getCompanyTaxNumber();
			$street = Condition::ifNull($billingAddress->getStreet(), $order->getStreet());
			$city = Condition::ifNull($billingAddress->getCity(), $order->getCity());
			$postcode = Condition::ifNull($billingAddress->getPostcode(), $order->getPostcode());
		}

		if ($billingAddress == null || $billingAddress->getTelephone() === null) {
			$telephone = $order->getTelephone();
		} else {
			$telephone = $billingAddress->getTelephone();
		}

		if ($deliveryAddress === null) {
			$deliveryAddressFilled = $order->getDeliveryStreet() !== null;
			$deliveryCompanyName = $order->getDeliveryCompanyName();
			$deliveryContactPerson = $order->getDeliveryContactPerson();
			$deliveryTelephone = $order->getDeliveryTelephone();
			$deliveryStreet = $order->getDeliveryStreet();
			$deliveryCity = $order->getDeliveryCity();
			$deliveryPostcode = $order->getDeliveryPostcode();
			$deliveryCountry = null;
		} else {
			$deliveryAddressFilled = true;
			$deliveryCompanyName = $deliveryAddress->getCompanyName();
			$deliveryContactPerson = $deliveryAddress->getContactPerson();
			$deliveryTelephone = $deliveryAddress->getTelephone();
			$deliveryStreet = $deliveryAddress->getStreet();
			$deliveryCity = $deliveryAddress->getCity();
			$deliveryPostcode = $deliveryAddress->getPostcode();
			$deliveryCountry = $deliveryAddress->getCountry();
		}

		$user = $this->edit(
			$user->getId(),
			Condition::ifNull($user->getFirstName(), $order->getFirstName()),
			Condition::ifNull($user->getLastName(), $order->getLastName()),
			null,
			$telephone,
			$companyCustomer,
			$companyName,
			$companyNumber,
			$companyTaxNumber,
			$street,
			$city,
			$postcode,
			$billingAddress->getCountry(),
			$deliveryAddressFilled,
			$deliveryCompanyName,
			$deliveryContactPerson,
			$deliveryTelephone,
			$deliveryStreet,
			$deliveryCity,
			$deliveryPostcode,
			$deliveryCountry
		);

		$this->em->flush();
	}


}
