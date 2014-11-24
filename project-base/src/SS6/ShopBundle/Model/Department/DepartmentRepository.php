<?php

namespace SS6\ShopBundle\Model\Department;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Department\Department;

class DepartmentRepository {

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
	private function getDepartmentRepository() {
		return $this->em->getRepository(Department::class);
	}

	/**
	 * @return \SS6\ShopBundle\Model\Department\Department[]
	 */
	public function findAll() {
		return $this->getDepartmentRepository()->findAll();
	}

	/**
	 * @param int $departmentId
	 * @return \SS6\ShopBundle\Model\Department\Department|null
	 */
	public function findById($departmentId) {
		return $this->getDepartmentRepository()->find($departmentId);
	}

	/**
	 * @param int $departmentId
	 * @return \SS6\ShopBundle\Model\Department\Department
	 * @throws \SS6\ShopBundle\Model\Department\Exception\DepartmentNotFoundException
	 */
	public function getById($departmentId) {
		$department = $this->findById($departmentId);

		if ($department === null) {
			throw new \SS6\ShopBundle\Model\Department\Exception\DepartmentNotFoundException($departmentId);
		}

		return $department;
	}

}
