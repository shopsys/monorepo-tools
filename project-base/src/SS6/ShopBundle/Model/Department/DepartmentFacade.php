<?php

namespace SS6\ShopBundle\Model\Department;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Department\DepartmentData;
use SS6\ShopBundle\Model\Department\DepartmentService;
use SS6\ShopBundle\Model\Department\DepartmentRepository;
use SS6\ShopBundle\Model\Domain\Domain;

class DepartmentFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Department\DepartmentRepository
	 */
	private $departmentRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Department\DepartmentService
	 */
	private $departmentService;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	public function __construct(
		EntityManager $em,
		DepartmentRepository $departmentRepository,
		DepartmentService $departmentService,
		Domain $domain
	) {
		$this->em = $em;
		$this->departmentRepository = $departmentRepository;
		$this->departmentService = $departmentService;
		$this->domain = $domain;
	}

	/**
	 * @param int $departmentId
	 * @return \SS6\ShopBundle\Model\Department\Department
	 */
	public function getById($departmentId) {
		return $this->departmentRepository->getById($departmentId);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Department\DepartmentData $departmentData
	 * @return \SS6\ShopBundle\Model\Department\Department
	 */
	public function create(DepartmentData $departmentData) {
		$department = $this->departmentService->create($departmentData);
		$this->em->persist($department);
		$this->em->flush();

		return $department;
	}

	/**
	 * @param int $departmentId
	 * @param \SS6\ShopBundle\Model\Department\DepartmentData $departmentData
	 * @return \SS6\ShopBundle\Model\Department\Department
	 */
	public function edit($departmentId, DepartmentData $departmentData) {
		$department = $this->departmentRepository->getById($departmentId);
		$this->departmentService->edit($department, $departmentData);
		$this->em->flush();

		return $department;
	}

	/**
	 * @param int $departmentId
	 */
	public function deleteById($departmentId) {
		$department = $this->departmentRepository->getById($departmentId);

		$this->em->remove($department);
		$this->em->flush();
	}

	/**
	 * @return \SS6\ShopBundle\Model\Department\Department[]
	 */
	public function getAllInRootWithTranslation() {
		$locale = $this->domain->getLocale();
		return $this->departmentRepository->getAllInRootWithTranslation($locale);
	}

	/**
	 * @return \SS6\ShopBundle\Model\Department\Department[]
	 */
	public function getAll() {
		return $this->departmentRepository->getAll();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Department\Department $department
	 * @return \SS6\ShopBundle\Model\Department\Department[]
	 */
	public function getAllWithoutBranch(Department $department) {
		return $this->departmentRepository->getAllWithoutBranch($department);
	}

}
