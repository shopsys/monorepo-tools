<?php

namespace SS6\ShopBundle\Model\Product\Parameter;

use SS6\ShopBundle\Form\Admin\Product\Parameter\ParameterFormType;
use SS6\ShopBundle\Model\PKGrid\InlineEdit\GridInlineEditInterface;
use SS6\ShopBundle\Model\Product\Parameter\ParameterData;
use SS6\ShopBundle\Model\Product\Parameter\ParameterFacade;
use SS6\ShopBundle\Model\Product\Parameter\ParameterGridFactory;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;

class ParameterInlineEdit implements GridInlineEditInterface {

	/**
	 * @var \Symfony\Component\Form\FormFactory
	 */
	private $formFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Parameter\ParameterFacade
	 */
	private $parameterFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Parameter\ParameterGridFactory
	 */
	private $parameterGridFactory;

	/**
	 * @var string
	 */
	private $serviceName;

	/**
	 * @var string
	 */
	private $queryId;

	/**
	 * @param \Symfony\Component\Form\FormFactory $formFactory
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ParameterFacade $parameterFacade
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ParameterGridFactory $parameterGridFactory
	 */
	public function __construct(
		FormFactory $formFactory,
		ParameterFacade $parameterFacade,
		ParameterGridFactory $parameterGridFactory
	) {
		$this->formFactory = $formFactory;
		$this->parameterFacade = $parameterFacade;
		$this->parameterGridFactory = $parameterGridFactory;

		$this->serviceName = 'ss6.shop.product.parameter.parameter_inline_edit';
		$this->queryId = 'a.id';
	}

	/**
	 * @param mixed $parameterId
	 * @return \Symfony\Component\Form\Form
	 */
	public function getForm($parameterId) {
		$parameterId = (int)$parameterId;
		$parameter = $this->parameterFacade->getById($parameterId);
		
		$parameterData = new ParameterData();
		$parameterData->setFromEntity($parameter);

		return $this->formFactory->create(new ParameterFormType(), $parameterData);
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param mixed $parameterId
	 * @throws \SS6\ShopBundle\Model\PKGrid\InlineEdit\Exception\InvalidFormDataException
	 */
	public function saveForm(Request $request, $parameterId) {
		$parameterId = (int)$parameterId;

		$form = $this->getForm($parameterId);
		$form->handleRequest($request);
		
		if ($form->isValid()) {
			$parameterData = $form->getData();
			$this->parameterFacade->edit($parameterId, $parameterData);
		} else {
			$formErrors = [];
			foreach ($form->getErrors(true) as $error) {
				/* @var $error \Symfony\Component\Form\FormError */
				$formErrors[] = $error->getMessage();
			}
			throw new \SS6\ShopBundle\Model\PKGrid\InlineEdit\Exception\InvalidFormDataException($formErrors);
		}
	}

	/**
	 * @return \SS6\ShopBundle\Model\PKGrid\PKGrid
	 */
	public function getGrid() {
		$grid = $this->parameterGridFactory->create();
		$grid->setInlineEditService($this);

		return $grid;
	}

	/**
	 * @return string
	 */
	public function getServiceName() {
		return $this->serviceName;
	}

	/**
	 * @return string
	 */
	public function getQueryId() {
		return $this->queryId;
	}

}
