<?php

namespace SS6\ShopBundle\Model\Customer;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Customer\User;

class UserRepository {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @param \Doctrine\ORM\EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager) {
		$this->em = $entityManager;
	}

	/**
	 * @return \Doctrine\ORM\EntityRepository
	 */
	private function getUserRepository() {
		return $this->em->getRepository(User::class);
	}

	/**
	 * @param string $email
	 * @return \SS6\ShopBundle\Model\Customer\User
	 */
	public function findUserByEmail($email) {
		return $this->getUserRepository()->findOneBy(array('email' => mb_strtolower($email)));
	}

	/**
	 * @param int $id
	 * @return \SS6\ShopBundle\Model\Customer\User
	 * @throws \SS6\ShopBundle\Model\Customer\Exception\UserNotFoundException
	 */
	public function getUserById($id) {
		$criteria = array('id' => $id);
		$user = $this->getUserRepository()->findOneBy($criteria);
		if ($user === null) {
			throw new Exception\UserNotFoundException($criteria);
		}
		return $user;
	}

}
