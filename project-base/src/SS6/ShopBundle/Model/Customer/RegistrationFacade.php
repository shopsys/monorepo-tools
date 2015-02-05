<?php

namespace SS6\ShopBundle\Model\Customer;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Customer\UserRepository;

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
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Customer\UserRepository $userRepository
	 */
	public function __construct(
		EntityManager $em,
		UserRepository $userRepository
	) {
		$this->em = $em;
		$this->userRepository = $userRepository;
	}

	/**
	 * @param string $email
	 * @param int $domainId
	 */
	public function resetPassword($email, $domainId) {
		$user = $this->userRepository->getUserByEmailAndDomain($email, $domainId);
	}

}
