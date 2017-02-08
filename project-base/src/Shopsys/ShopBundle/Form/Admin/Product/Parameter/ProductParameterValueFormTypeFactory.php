<?php

namespace SS6\ShopBundle\Form\Admin\Product\Parameter;

use SS6\ShopBundle\Model\Product\Parameter\ParameterRepository;

class ProductParameterValueFormTypeFactory {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Parameter\ParameterRepository
	 */
	private $parameterRepository;

	public function __construct(
		ParameterRepository $parameterRepository
	) {
		$this->parameterRepository = $parameterRepository;
	}

	/**
	 * @return \SS6\ShopBundle\Form\Admin\Product\Parameter\ProductParameterValueFormType
	 */
	public function create() {
		$parameters = $this->parameterRepository->getAll();

		return new ProductParameterValueFormType($parameters);
	}

}
