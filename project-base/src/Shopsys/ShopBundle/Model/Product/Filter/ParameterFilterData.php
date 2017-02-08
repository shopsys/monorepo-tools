<?php

namespace SS6\ShopBundle\Model\Product\Filter;

use SS6\ShopBundle\Model\Product\Parameter\Parameter;

class ParameterFilterData {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Parameter\Parameter
	 */
	public $parameter;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Parameter\ParameterValue[]
	 */
	public $values = [];

	/**
	 * @param \SS6\ShopBundle\Model\Product\Parameter\Parameter $parameter
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ParameterValue[] $values
	 */
	public function __construct(
		Parameter $parameter = null,
		array $values = []
	) {
		$this->parameter = $parameter;
		$this->values = $values;
	}

}
