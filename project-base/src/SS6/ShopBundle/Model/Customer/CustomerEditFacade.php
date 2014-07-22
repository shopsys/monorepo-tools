<?php

namespace SS6\ShopBundle\Model\Customer;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Customer\RegistrationService;
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
	 * @param string $firstName
	 * @param string $lastName
	 * @param string $email
	 * @param string $password
	 * @return \SS6\ShopBundle\Model\Customer\User
	 */
	public function register($firstName, $lastName, $email, $password) {
		$userByEmail = $this->userRepository->findUserByEmail($email);

		$billingAddress = new BillingAddress();
		$deliveryAddress = new DeliveryAddress();

		$user = $this->registrationService->create($firstName,
			$lastName,
			$email,
			$password,
			$billingAddress,
			$deliveryAddress,
			$userByEmail);

		$this->em->persist($deliveryAddress);
		$this->em->persist($billingAddress);
		$this->em->persist($user);
		$this->em->flush();

		return $user;
	}

	/**
	 * @param string $firstName
	 * @param string $lastName
	 * @param string $email
	 * @param string $password
	 * @param string|null $telephone
	 * @param boolean $companyCustomer
	 * @param string|null $companyName
	 * @param string|null $companyNumber
	 * @param string|null $companyTaxNumber
	 * @param string|null $street
	 * @param string|null $city
	 * @param string|null $postcode
	 * @param string|null $country
	 * @param boolean $deliveryAddressFilled
	 * @param string|null $deliveryCompanyName
	 * @param string|null $deliveryContactPerson
	 * @param string|null $deliveryTelephone
	 * @param string|null $deliveryStreet
	 * @param string|null $deliveryCity
	 * @param string|null $deliveryPostcode
	 * @param string|null $deliveryCountry
	 * @return \SS6\ShopBundle\Model\Customer\User
	 */
	public function create($firstName, $lastName, $email, $password,
			$telephone = null, $companyCustomer = false, $companyName = null, $companyNumber = null,
			$companyTaxNumber = null, $street = null, $city = null, $postcode = null, $country = null,
			$deliveryAddressFilled = false, $deliveryCompanyName = null, $deliveryContactPerson = null,
			$deliveryTelephone = null, $deliveryStreet = null, $deliveryCity = null, $deliveryPostcode = null,
			$deliveryCountry = null) {

		$billingAddress = new BillingAddress(
			$street,
			$city,
			$postcode,
			$country,
			$companyCustomer,
			$companyName,
			$companyNumber,
			$companyTaxNumber,
			$telephone
		);
		$this->em->persist($billingAddress);

		$userByEmail = $this->userRepository->findUserByEmail($email);

		$user = $this->registrationService->create($firstName,
			$lastName,
			$email,
			$password,
			$billingAddress,
			$userByEmail
		);
		$this->em->persist($user);

		$deliveryAddress = $this->registrationService->editDeliveryAddress(
			$user,
			null,
			$deliveryAddressFilled,
			$deliveryCompanyName,
			$deliveryContactPerson,
			$deliveryStreet,
			$deliveryCity,
			$deliveryPostcode,
			$deliveryCountry,
			$deliveryTelephone
		);
		if ($deliveryAddress !== null) {
			$this->em->persist($deliveryAddress);
		}
		
		$this->em->flush();

		return $user;
	}

	/**
	 * @param int $userId
	 * @param string $firstName
	 * @param string $lastName
	 * @param string|null $password
	 * @param string|null $telephone
	 * @param boolean $companyCustomer
	 * @param string|null $companyName
	 * @param string|null $companyNumber
	 * @param string|null $companyTaxNumber
	 * @param string|null $street
	 * @param string|null $city
	 * @param string|null $postcode
	 * @param string|null $country
	 * @param boolean $deliveryAddressFilled
	 * @param string|null $deliveryCompanyName
	 * @param string|null $deliveryContactPerson
	 * @param string|null $deliveryTelephone
	 * @param string|null $deliveryStreet
	 * @param string|null $deliveryCity
	 * @param string|null $deliveryPostcode
	 * @param string|null $deliveryCountry
	 * @return \SS6\ShopBundle\Model\Customer\User
	 */
	private function edit($userId, $firstName, $lastName, $password = null,
			$telephone = null, $companyCustomer = false, $companyName = null, $companyNumber = null,
			$companyTaxNumber = null, $street = null, $city = null, $postcode = null, $country = null,
			$deliveryAddressFilled = false, $deliveryCompanyName = null, $deliveryContactPerson = null,
			$deliveryTelephone = null, $deliveryStreet = null, $deliveryCity = null, $deliveryPostcode = null,
			$deliveryCountry = null) {

		$user = $this->userRepository->getUserById($userId);

		$this->registrationService->edit(
			$user,
			$firstName,
			$lastName,
			$password
		);

		$this->registrationService->editBillingAddress(
			$user->getBillingAddress(),
			$street,
			$city,
			$postcode,
			$country,
			$companyCustomer,
			$companyName,
			$companyNumber,
			$companyTaxNumber,
			$telephone
		);

		$oldDeliveryAddress = $user->getDeliveryAddress();
		$deliveryAddress = $this->registrationService->editDeliveryAddress(
			$user,
			$oldDeliveryAddress,
			$deliveryAddressFilled,
			$deliveryCompanyName,
			$deliveryContactPerson,
			$deliveryStreet,
			$deliveryCity,
			$deliveryPostcode,
			$deliveryCountry,
			$deliveryTelephone
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
	 * @param string $firstName
	 * @param string $lastName
	 * @param string $email
	 * @param string|null $password
	 * @param string|null $telephone
	 * @param boolean $companyCustomer
	 * @param string|null $companyName
	 * @param string|null $companyNumber
	 * @param string|null $companyTaxNumber
	 * @param string|null $street
	 * @param string|null $city
	 * @param string|null $postcode
	 * @param string|null $country
	 * @param boolean $deliveryAddressFilled
	 * @param string|null $deliveryCompanyName
	 * @param string|null $deliveryContactPerson
	 * @param string|null $deliveryTelephone
	 * @param string|null $deliveryStreet
	 * @param string|null $deliveryCity
	 * @param string|null $deliveryPostcode
	 * @param string|null $deliveryCountry
	 * @return \SS6\ShopBundle\Model\Customer\User
	 */
	public function editByAdmin($userId, $firstName, $lastName, $email, $password = null,
			$telephone = null, $companyCustomer = false, $companyName = null, $companyNumber = null,
			$companyTaxNumber = null, $street = null, $city = null, $postcode = null, $country = null,
			$deliveryAddressFilled = false, $deliveryCompanyName = null, $deliveryContactPerson = null,
			$deliveryTelephone = null, $deliveryStreet = null, $deliveryCity = null, $deliveryPostcode = null,
			$deliveryCountry = null) {

		$user = $this->edit(
			$userId,
			$firstName,
			$lastName,
			$password,
			$telephone,
			$companyCustomer,
			$companyName,
			$companyNumber,
			$companyTaxNumber,
			$street,
			$city,
			$postcode,
			$country,
			$deliveryAddressFilled,
			$deliveryCompanyName,
			$deliveryContactPerson,
			$deliveryTelephone,
			$deliveryStreet,
			$deliveryCity,
			$deliveryPostcode,
			$deliveryCountry
		);

		$userByEmail = $this->userRepository->findUserByEmail($email);
		$this->registrationService->changeEmail($user, $email, $userByEmail);

		$this->em->flush();

		return $user;
	}

	/**
	 * @param int $userId
	 * @param string $firstName
	 * @param string $lastName
	 * @param string|null $password
	 * @param string|null $telephone
	 * @param boolean $companyCustomer
	 * @param string|null $companyName
	 * @param string|null $companyNumber
	 * @param string|null $companyTaxNumber
	 * @param string|null $street
	 * @param string|null $city
	 * @param string|null $postcode
	 * @param string|null $country
	 * @param boolean $deliveryAddressFilled
	 * @param string|null $deliveryCompanyName
	 * @param string|null $deliveryContactPerson
	 * @param string|null $deliveryTelephone
	 * @param string|null $deliveryStreet
	 * @param string|null $deliveryCity
	 * @param string|null $deliveryPostcode
	 * @param string|null $deliveryCountry
	 * @return \SS6\ShopBundle\Model\Customer\User
	 */
	public function editByCustomer($userId, $firstName, $lastName, $password = null,
			$telephone = null, $companyCustomer = false, $companyName = null, $companyNumber = null,
			$companyTaxNumber = null, $street = null, $city = null, $postcode = null, $country = null,
			$deliveryAddressFilled = false, $deliveryCompanyName = null, $deliveryContactPerson = null,
			$deliveryTelephone = null, $deliveryStreet = null, $deliveryCity = null,
			$deliveryPostcode = null, $deliveryCountry = null) {

		$user = $this->edit(
			$userId,
			$firstName,
			$lastName,
			$password,
			$telephone,
			$companyCustomer,
			$companyName,
			$companyNumber,
			$companyTaxNumber,
			$street,
			$city,
			$postcode,
			$country,
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

}
