<?php

namespace SS6\ShopBundle\Model\Customer;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Customer\RegistrationService;

class RegistrationFacade {

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

		$user = $this->registrationService->create($firstName,
			$lastName,
			$email,
			$password,
			$userByEmail);

		$this->em->persist($user);
		$this->em->flush();

		return $user;
	}

}
