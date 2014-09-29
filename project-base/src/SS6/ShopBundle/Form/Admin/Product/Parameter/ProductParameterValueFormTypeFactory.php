<?php

namespace SS6\ShopBundle\Form\Admin\Product\Parameter;

use SS6\ShopBundle\Model\Product\Parameter\ParameterRepository;
use SS6\ShopBundle\Model\Product\Parameter\ParameterValueToParameterTextTransformer;

class ProductParameterValueFormTypeFactory {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Parameter\ParameterRepository
	 */
	private $parameterRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Parameter\ParameterValueToParameterTextTransformer
	 */
	private $parameterValueToParameterTextTransformer;

	public function __construct(
		ParameterRepository $parameterRepository,
		ParameterValueToParameterTextTransformer $parameterValueToParameterTextTransformer
	) {
		$this->parameterRepository = $parameterRepository;
		$this->parameterValueToParameterTextTransformer = $parameterValueToParameterTextTransformer;
	}

	/**
	 * @return \SS6\ShopBundle\Form\Admin\Product\Parameter\ProductParameterValueFormType
	 */
	public function create() {
		$parameters = $this->parameterRepository->findAll();

		return new ProductParameterValueFormType(
			$parameters,
			$this->parameterValueToParameterTextTransformer
		);
	}

}
