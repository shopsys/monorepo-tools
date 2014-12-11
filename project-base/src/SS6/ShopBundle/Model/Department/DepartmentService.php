<?php

namespace SS6\ShopBundle\Model\Department;

use SS6\ShopBundle\Model\Department\Department;
use SS6\ShopBundle\Model\Department\DepartmentData;

class DepartmentService {

	/**
	 * @param \SS6\ShopBundle\Model\Department\DepartmentData $departmentData
	 * @return \SS6\ShopBundle\Model\Department\Department
	 */
	public function create(DepartmentData $departmentData) {
		return new Department($departmentData);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Department\Department $department
	 * @param \SS6\ShopBundle\Model\Department\DepartmentData $departmentData
	 * @return \SS6\ShopBundle\Model\Department\Department
	 */
	public function edit(Department $department, DepartmentData $departmentData) {
		$department->edit($departmentData);

		return $department;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Department\Department $department
	 */
	public function setChildrensAsSiblings(Department $department) {
		foreach ($department->getChildrens() as $children) {
			$children->setParent($department->getParent());
		}
	}

}
