<?php

namespace SS6\ShopBundle\Model\Department;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
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

	/**
	 * @param string $locale
	 * @return \SS6\ShopBundle\Model\Department\Department[]
	 */
	public function getAllWithTranslation($locale) {
		return $this->getAllWithTranslationQueryBuilder($locale)->getQuery()->execute();
	}

	/**
	 * @param string $locale
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	private function getAllWithTranslationQueryBuilder($locale) {
		$qb = $this->em->createQueryBuilder()
			->select('d')
			->from(Department::class, 'd')
			->join('d.translations', 'dt', Join::WITH, 'dt.locale = :locale')
			->where('dt.name IS NOT NULL');
		$qb->setParameter('locale', $locale);

		return $qb;
	}

}
