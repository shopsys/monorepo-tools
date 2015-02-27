<?php

namespace SS6\ShopBundle\Model\Administrator;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Administrator\Administrator;

class AdministratorRepository {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 */
	public function __construct(EntityManager $em) {
		$this->em = $em;
	}

	/**
	 * @return \Doctrine\ORM\EntityRepository
	 */
	private function getAdministratorRepository() {
		return $this->em->getRepository(Administrator::class);
	}

	/**
	 * @param int $administratorId
	 * @return \SS6\ShopBundle\Model\Administrator\Administrator|null
	 */
	public function findById($administratorId) {
		return $this->getAdministratorRepository()->find($administratorId);
	}

	/**
	 * @param int $administratorId
	 * @return \SS6\ShopBundle\Model\Administrator\Administrator
	 */
	public function getById($administratorId) {
		$administrator = $this->getAdministratorRepository()->find($administratorId);
		if ($administrator === null) {
			$message = 'Administrator with ID ' . $administratorId . ' not found.';
			throw new \SS6\ShopBundle\Model\Administrator\Exception\AdministratorNotFoundException($message);
		}

		return $administrator;
	}

	/**
	 * @param type $administratorUserName
	 * @return \SS6\ShopBundle\Model\Administrator\Administrator
	 */
	public function findByUserName($administratorUserName) {
		return $this->getAdministratorRepository()->findOneBy(['username' => $administratorUserName]);
	}

	private function getAllQueryBuilder() {
		return $this->em->createQueryBuilder()
			->select('a')
			->from(Administrator::class, 'a');
	}

	/**
	 * @return int
	 */
	public function getCount() {
		return (int)($this->getAllQueryBuilder()
			->select('COUNT(a)')
			->getQuery()->getSingleScalarResult());
	}
}
