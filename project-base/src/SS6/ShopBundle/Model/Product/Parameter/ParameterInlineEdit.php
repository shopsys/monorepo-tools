<?php

namespace SS6\ShopBundle\Model\Product\Parameter;

use SS6\ShopBundle\Component\Grid\InlineEdit\AbstractGridInlineEdit;
use SS6\ShopBundle\Form\Admin\Product\Parameter\ParameterFormType;
use SS6\ShopBundle\Model\Product\Parameter\ParameterData;
use SS6\ShopBundle\Model\Product\Parameter\ParameterFacade;
use SS6\ShopBundle\Model\Product\Parameter\ParameterGridFactory;
use Symfony\Component\Form\FormFactory;

class ParameterInlineEdit extends AbstractGridInlineEdit {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Parameter\ParameterFacade
	 */
	private $parameterFacade;

	/**
	 * @param \Symfony\Component\Form\FormFactory $formFactory
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ParameterGridFactory $parameterGridFactory
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ParameterFacade $parameterFacade
	 */
	public function __construct(
		FormFactory $formFactory,
		ParameterGridFactory $parameterGridFactory,
		ParameterFacade $parameterFacade
	) {
		$this->parameterFacade = $parameterFacade;

		parent::__construct($formFactory, $parameterGridFactory);
	}
	/**
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ParameterData $parameterData
	 * @return int
	 */
	protected function createEntityAndGetId($parameterData) {
		$parameter = $this->parameterFacade->create($parameterData);

		return $parameter->getId();
	}

	/**
	 * @param int $parameterId
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ParameterData $parameterData
	 */
	protected function editEntity($parameterId, $parameterData) {
		$this->parameterFacade->edit($parameterId, $parameterData);
	}

	/**
	 * @param int|null $parameterId
	 * @return \SS6\ShopBundle\Model\Product\Parameter\ParameterData
	 */
	protected function getFormDataObject($parameterId = null) {
		$parameterData = new ParameterData();

		if ($parameterId !== null) {
			$parameterId = (int)$parameterId;
			$parameter = $this->parameterFacade->getById($parameterId);
			$parameterData->setFromEntity($parameter);
		}

		return $parameterData;
	}

	/**
	 * @param int $rowId
	 * @return \SS6\ShopBundle\Form\Admin\Product\Parameter\ParameterFormType
	 */
	protected function getFormType($rowId) {
		return new ParameterFormType();
	}

}
