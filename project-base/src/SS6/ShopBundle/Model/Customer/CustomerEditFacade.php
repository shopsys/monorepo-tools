<?php

namespace SS6\ShopBundle\Model\Customer;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Customer\RegistrationService;

class CustomerEditFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\UserRepository
	 */
	private $userRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\RegistrationService
	 */
	private $registrationService;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Customer\UserRepository $userRepository
	 * @param \SS6\ShopBundle\Model\Customer\RegistrationService $registrationService
	 */
	public function __construct(EntityManager $em, UserRepository $userRepository,
			RegistrationService $registrationService) {
		$this->em = $em;
		$this->userRepository = $userRepository;
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
	 * @param string|null $companyName
	 * @param string|null $companyNumber
	 * @param string|null $companyTaxNumber
	 * @param string|null $street
	 * @param string|null $city
	 * @param string|null $zip
	 * @param string|null $country
	 * @param string|null $deliveryCompanyName
	 * @param string|null $deliveryConatactPerson
	 * @param string|null $deliveryTelephone
	 * @param string|null $deliveryStreet
	 * @param string|null $deliveryCity
	 * @param string|null $deliveryZip
	 * @param string|null $deliveryCountry
	 * @return \SS6\ShopBundle\Model\Customer\User
	 */
	public function create($firstName, $lastName, $email, $password,
			$telephone = null, $companyName = null, $companyNumber = null, $companyTaxNumber = null,
			$street = null, $city = null, $zip = null, $country = null,
			$deliveryCompanyName = null, $deliveryConatactPerson = null, $deliveryTelephone = null,
			$deliveryStreet = null, $deliveryCity = null, $deliveryZip = null, $deliveryCountry = null) {

		$userByEmail = $this->userRepository->findUserByEmail($email);

		$billingAddress = new BillingAddress(
			$street,
			$city,
			$zip,
			$country,
			$companyName,
			$companyNumber,
			$companyTaxNumber,
			$telephone);
		
		$deliveryAddress = new DeliveryAddress(
			$deliveryStreet,
			$deliveryCity,
			$deliveryZip,
			$deliveryCountry,
			$deliveryCompanyName,
			$deliveryConatactPerson,
			$deliveryTelephone);

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
	 * @param int $userId
	 * @param string $firstName
	 * @param string $lastName
	 * @param string $email
	 * @param string|null $password
	 * @param string|null $telephone
	 * @param string|null $companyName
	 * @param string|null $companyNumber
	 * @param string|null $companyTaxNumber
	 * @param string|null $street
	 * @param string|null $city
	 * @param string|null $zip
	 * @param string|null $country
	 * @param string|null $deliveryCompanyName
	 * @param string|null $deliveryConatactPerson
	 * @param string|null $deliveryTelephone
	 * @param string|null $deliveryStreet
	 * @param string|null $deliveryCity
	 * @param string|null $deliveryZip
	 * @param string|null $deliveryCountry
	 * @return \SS6\ShopBundle\Model\Customer\User
	 */
	public function edit($userId, $firstName, $lastName, $email, $password = null,
			$telephone = null, $companyName = null, $companyNumber = null, $companyTaxNumber = null,
			$street = null, $city = null, $zip = null, $country = null,
			$deliveryCompanyName = null, $deliveryConatactPerson = null, $deliveryTelephone = null,
			$deliveryStreet = null, $deliveryCity = null, $deliveryZip = null, $deliveryCountry = null) {

		$user = $this->userRepository->getUserById($userId);

		$billingAddress = $user->getBillingAddress();
		$billingAddress->edit(
			$street,
			$city,
			$zip,
			$country,
			$companyName,
			$companyNumber,
			$companyTaxNumber,
			$telephone);

		$deliveryAddress = $user->getDeliveryAddress();
		$deliveryAddress->edit(
			$deliveryStreet,
			$deliveryCity,
			$deliveryZip,
			$deliveryCountry,
			$deliveryCompanyName,
			$deliveryConatactPerson,
			$deliveryTelephone);

		$userByEmail = $this->userRepository->findUserByEmail($email);

		$this->registrationService->edit(
			$user,
			$firstName,
			$lastName,
			$email,
			$password,
			$userByEmail);

		$this->em->persist($deliveryAddress);
		$this->em->persist($billingAddress);
		$this->em->persist($user);
		$this->em->flush();

		return $user;
	}

}
