<?php

namespace SS6\ShopBundle\Model\Department;

use SS6\ShopBundle\Component\Validator;
use SS6\ShopBundle\Model\Department\Department;

/**
 * @Validator\Auto(entity="SS6\ShopBundle\Model\Department\Department")
 */
class DepartmentData {

	/**
	 * @var string|null
	 */
	private $name;

	/**
	 * @param string|null $name
	 */
	public function __construct($name = null) {
		$this->name = $name;
	}

	/**
	 * @return string|null
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Department\Department $department
	 */
	public function setFromEntity(Department $department) {
		$this->name = $department->getName();
	}

}
