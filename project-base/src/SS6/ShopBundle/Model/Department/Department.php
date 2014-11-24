<?php

namespace SS6\ShopBundle\Model\Department;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="departments")
 * @ORM\Entity
 */
class Department {

	/**
	 * @var integer
	 *
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=50)
	 */
	private $name;

	/**
	 * @param \SS6\ShopBundle\Model\Department\DepartmentData $departmentData
	 */
	public function __construct(DepartmentData $departmentData) {
		$this->name = $departmentData->getName();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Department\DepartmentData $departmentData
	 */
	public function edit(DepartmentData $departmentData) {
		$this->name = $departmentData->getName();
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return name
	 */
	public function getName() {
		return $this->name;
	}

}
