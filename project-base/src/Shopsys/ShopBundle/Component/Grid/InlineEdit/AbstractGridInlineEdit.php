<?php

namespace SS6\ShopBundle\Component\Grid\InlineEdit;

use SS6\ShopBundle\Component\Grid\GridFactoryInterface;
use SS6\ShopBundle\Component\Grid\InlineEdit\GridInlineEditInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractGridInlineEdit implements GridInlineEditInterface {

	/**
	 * @var \Symfony\Component\Form\FormFactory
	 */
	private $formFactory;

	/**
	 * @var \SS6\ShopBundle\Component\Grid\GridFactoryInterface
	 */
	private $gridFactory;

	/**
	 * @param \Symfony\Component\Form\FormFactory $formFactory
	 * @param \SS6\ShopBundle\Component\Grid\GridFactoryInterface $gridFactory
	 */
	public function __construct(FormFactory $formFactory, GridFactoryInterface $gridFactory) {
		$this->formFactory = $formFactory;
		$this->gridFactory = $gridFactory;
	}

	/**
	 * @param mixed $rowId
	 * @return \Symfony\Component\Form\Form
	 */
	public function getForm($rowId) {
		return $this->formFactory->create(
			$this->getFormType($rowId),
			$this->getFormDataObject($rowId)
		);
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param mixed $rowId
	 * @return int
	 */
	public function saveForm(Request $request, $rowId) {
		$form = $this->getForm($rowId);
		$form->handleRequest($request);

		if (!$form->isValid()) {
			$formErrors = [];
			foreach ($form->getErrors(true) as $error) {
				/* @var $error \Symfony\Component\Form\FormError */
				$formErrors[] = $error->getMessage();
			}
			throw new \SS6\ShopBundle\Component\Grid\InlineEdit\Exception\InvalidFormDataException($formErrors);
		}

		$formData = $form->getData();
		if ($rowId !== null) {
			$this->editEntity($rowId, $formData);
		} else {
			$rowId = $this->createEntityAndGetId($formData);
		}

		return $rowId;
	}

	/**
	 * @return \SS6\ShopBundle\Component\Grid\Grid
	 */
	public function getGrid() {
		$grid = $this->gridFactory->create();
		$grid->setInlineEditService($this);

		return $grid;
	}

	/**
	 * @return bool
	 */
	public function canAddNewRow() {
		return true;
	}

	/**
	 * @return string
	 */
	public function getServiceName() {
		return get_called_class();
	}

	/**
	 * @param int $rowId
	 * @return \Symfony\Component\Form\AbstractType
	 */
	abstract protected function getFormType($rowId);

	/**
	 * @param mixed $rowId
	 * @return object
	 */
	abstract protected function getFormDataObject($rowId = null);

	/**
	 * @param mixed $rowId
	 * @param object $formDataObject
	 */
	abstract protected function editEntity($rowId, $formDataObject);

	/**
	 * @param object $formDataObject
	 * @return mixed
	 */
	abstract protected function createEntityAndGetId($formDataObject);

}
