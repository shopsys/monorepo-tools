<?php

namespace SS6\ShopBundle\Model\Department;

use SS6\ShopBundle\Component\Validator;
use SS6\ShopBundle\Model\Department\Department;

/**
 * @Validator\Auto(entity="SS6\ShopBundle\Model\Department\Department")
 */
class DepartmentData {

	/**
	 * @var string[]
	 */
	private $name;

	/**
	 * @var \SS6\ShopBundle\Model\Department\Department|null
	 */
	private $parent;

	/**
	 * @param string[] $name
	 * @param \SS6\ShopBundle\Model\Department\Department|null $parent
	 */
	public function __construct(array $name = [], Department $parent = null) {
		$this->name = $name;
		$this->parent = $parent;
	}

	/**
	 * @return string[]
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string[] $name
	 */
	public function setName(array $name) {
		$this->name = $name;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Department\Department
	 */
	public function getParent() {
		return $this->parent;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Department\Department\null $parent
	 */
	public function setParent(Department $parent = null) {
		$this->parent = $parent;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Department\Department $department
	 */
	public function setFromEntity(Department $department) {
		$translations = $department->getTranslations();
		$names = [];
		foreach ($translations as $translate) {
			$names[$translate->getLocale()] = $translate->getName();
		}
		$this->setName($names);
		$this->setParent($department->getParent());
	}

}
