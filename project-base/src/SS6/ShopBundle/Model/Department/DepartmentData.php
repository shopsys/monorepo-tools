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
	 * @param array $names
	 */
	public function __construct($names = array()) {
		$this->names = $names;
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
