<?php

namespace SS6\ShopBundle\Model\PKGrid\InlineEdit;

use SS6\ShopBundle\Model\PKGrid\InlineEdit\GridInlineEditInterface;
use SS6\ShopBundle\Model\PKGrid\GridFactoryInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractGridInlineEdit implements GridInlineEditInterface {

	/**
	 * @var \Symfony\Component\Form\FormFactory
	 */
	private $formFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Availability\AvailabilityGridFactory
	 */
	private $gridFactory;

	/**
	 * @param \Symfony\Component\Form\FormFactory $formFactory
	 * @param \SS6\ShopBundle\Model\PKGrid\GridFactoryInterface $gridFactory
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
			$this->getFormType(),
			$this->getFormDataObject($rowId)
		);
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param mixed $rowId
	 * @return int
	 * @throws \SS6\ShopBundle\Model\PKGrid\InlineEdit\Exception\InvalidFormDataException
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
			throw new \SS6\ShopBundle\Model\PKGrid\InlineEdit\Exception\InvalidFormDataException($formErrors);
		}

		$formDataObject = $form->getData();
		if ($rowId !== null) {
			$rowId = (int)$rowId;
			$this->editEntity($rowId, $formDataObject);
		} else {
			$rowId = $this->createEntityAndGetId($formDataObject);
		}

		return $rowId;
	}
	
	/**
	 * @return \SS6\ShopBundle\Model\PKGrid\PKGrid
	 */
	public function getGrid() {
		$grid = $this->gridFactory->create();
		$grid->setInlineEditService($this);

		return $grid;
	}

	/**
	 * @return string
	 */
	abstract public function getServiceName();

	/**
	 * @return string
	 */
	abstract public function getQueryId();

	/**
	 * @return \Symfony\Component\Form\AbstractType
	 */
	abstract protected function getFormType();

	/**
	 * @param mixed $rowId
	 * @return object
	 */
	abstract protected function getFormDataObject($rowId = null);

	/**
	 * @param int $rowId
	 * @param object $formDataObject
	 */
	abstract protected function editEntity($rowId, $formDataObject);

	/**
	 * @param object $formDataObject
	 * @return int
	 */
	abstract protected function createEntityAndGetId($formDataObject);

}
