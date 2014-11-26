<?php

namespace SS6\ShopBundle\Model\Department\Grid;

use SS6\ShopBundle\Form\Admin\Department\DepartmentFormType;
use SS6\ShopBundle\Model\Grid\InlineEdit\AbstractGridInlineEdit;
use SS6\ShopBundle\Model\Department\DepartmentData;
use SS6\ShopBundle\Model\Department\DepartmentFacade;
use SS6\ShopBundle\Model\Department\Grid\DepartmentGridFactory;
use Symfony\Component\Form\FormFactory;

class DepartmentInlineEdit extends AbstractGridInlineEdit {

	/**
	 * @var \SS6\ShopBundle\Model\Department\DepartmentFacade
	 */
	private $departmentFacade;

	/**
	 * @param \Symfony\Component\Form\FormFactory $formFactory
	 * @param \SS6\ShopBundle\Model\Department\Grid\DepartmentGridFactory $departmentGridFactory
	 * @param \SS6\ShopBundle\Model\Department\DepartmentFacade $departmentFacade
	 */
	public function __construct(
		FormFactory $formFactory,
		DepartmentGridFactory $departmentGridFactory,
		DepartmentFacade $departmentFacade
	) {
		$this->departmentFacade = $departmentFacade;

		parent::__construct($formFactory, $departmentGridFactory);
	}

	/**
	 * @return string
	 */
	public function getServiceName() {
		return 'ss6.shop.department.department_inline_edit';
	}

	/**
	 * @param \SS6\ShopBundle\Model\Department\DepartmentData $departmentData
	 * @return int
	 */
	protected function createEntityAndGetId($departmentData) {
		$department = $this->departmentFacade->create($departmentData);

		return $department->getId();
	}

	/**
	 * @param int $departmentId
	 * @param \SS6\ShopBundle\Model\Department\DepartmentData $departmentData
	 */
	protected function editEntity($departmentId, $departmentData) {
		$this->departmentFacade->edit($departmentId, $departmentData);
	}

	/**
	 * @param int|null $departmentId
	 * @return \SS6\ShopBundle\Model\Department\DepartmentData
	 */
	protected function getFormDataObject($departmentId = null) {
		$departmentData = new DepartmentData();

		if ($departmentId !== null) {
			$departmentId = (int)$departmentId;
			$department = $this->departmentFacade->getById($departmentId);
			$departmentData->setFromEntity($department);
		}

		return $departmentData;
	}

	/**
	 * @return \SS6\ShopBundle\Form\Admin\Department\DepartmentFormType
	 */
	protected function getFormType() {
		return new DepartmentFormType();
	}

}
