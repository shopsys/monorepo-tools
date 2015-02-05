<?php

namespace SS6\ShopBundle\Model\Customer;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroup;

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
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Customer\User|null
	 */
	public function findUserByEmailAndDomain($email, $domainId) {
		return $this->getUserRepository()->findOneBy([
			'email' => mb_strtolower($email),
			'domainId' => $domainId,
		]);
	}

	/**
	 * @param string $email
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Customer\User|null
	 */
	public function getUserByEmailAndDomain($email, $domainId) {
		$user = $this->findUserByEmailAndDomain($email, $domainId);

		if ($user === null) {
			throw new \SS6\ShopBundle\Model\Customer\Exception\UserNotFoundByEmailAndDomainException(
				$email,
				$domainId
			);
		}

		return $user;
	}

	/**
	 * @param int $id
	 * @return \SS6\ShopBundle\Model\Customer\User
	 */
	public function getUserById($id) {
		$user = $this->getUserRepository()->find($id);
		if ($user === null) {
			throw new \SS6\ShopBundle\Model\Customer\Exception\UserNotFoundException($id);
		}
		return $user;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @return zSS6\ShopBundle\Model\Customer\User[]
	 */
	public function getAllByPricingGroup(PricingGroup $pricingGroup) {
		return $this->getUserRepository()->findBy(['pricingGroup' => $pricingGroup]);
	}

}
