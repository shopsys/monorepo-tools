<?php

namespace SS6\ShopBundle\Model\Customer;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Customer\User;

class UserRepository {

	/**
	 * @var \Doctrine\ORM\EntityRepository
	 */
	private $entityRepository;

	/**
	 * @param \Doctrine\ORM\EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager) {
		$this->entityRepository = $entityManager->getRepository(User::class);
	}

	/**
	 * @param string $email
	 * @return \SS6\ShopBundle\Model\Customer\User
	 */
	public function findUserByEmail($email) {
		return $this->entityRepository->findOneBy(array('email' => $email));
	}

	/**
	 * @param int $id
	 * @return \SS6\ShopBundle\Model\Customer\User
	 * @throws PaymentNotFoundException
	 */
	public function getUserById($id) {
		$criteria = array('id' => $id);
		$user = $this->entityRepository->findOneBy($criteria);
		if ($user === null) {
			throw new Exception\UserNotFoundException($criteria);
		}
		return $user;
	}

}
