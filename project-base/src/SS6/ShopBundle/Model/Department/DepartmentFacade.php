<?php

namespace SS6\ShopBundle\Model\Department;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Department\DepartmentData;
use SS6\ShopBundle\Model\Department\DepartmentService;
use SS6\ShopBundle\Model\Department\DepartmentRepository;

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

	public function __construct(
		EntityManager $em,
		DepartmentRepository $departmentRepository,
		DepartmentService $departmentService
	) {
		$this->em = $em;
		$this->departmentRepository = $departmentRepository;
		$this->departmentService = $departmentService;
	}

	/**
	 * @param int $departmentId
	 * @return \SS6\ShopBundle\Model\Department\Department
	 */
	public function getById($departmentId) {
		return $this->departmentRepository->getById($departmentId);
	}

	/**
	 * @return \SS6\ShopBundle\Model\Department\Department[]
	 */
	public function getAll() {
		return $this->departmentRepository->findAll();
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

}
