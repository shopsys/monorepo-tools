<?php

namespace SS6\ShopBundle\Model\Department;

use SS6\ShopBundle\Component\Validator;
use SS6\ShopBundle\Model\Department\Department;

/**
 * @Validator\Auto(entity="SS6\ShopBundle\Model\Department\Department")
 */
class DepartmentData {

	/**
	 * @var array
	 */
	private $names;

	/**
	 * @var \SS6\ShopBundle\Model\Department\Department|null
	 */
	private $parent;

	/**
	 * @param array $names
	 */
	public function __construct($names = array(), Department $parent = null) {
		$this->names = $names;
		$this->parent = $parent;
	}

	/**
	 * @return array
	 */
	public function getNames() {
		return $this->names;
	}

	/**
	 * @param array $names
	 */
	public function setNames($names) {
		$this->names = $names;
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
		$names = array();
		foreach ($translations as $translate) {
			$names[$translate->getLocale()] = $translate->getName();
		}
		$this->setNames($names);
	}

}
