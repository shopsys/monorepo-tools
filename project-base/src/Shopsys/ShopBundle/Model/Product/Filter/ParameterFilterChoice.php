<?php

namespace SS6\ShopBundle\Model\Product\Filter;

use SS6\ShopBundle\Model\Product\Parameter\Parameter;

class ParameterFilterChoice {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Parameter\Parameter
	 */
	private $parameter;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Parameter\ParameterValue[]
	 */
	private $values = [];

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

	/**
	 * @return \SS6\ShopBundle\Model\Product\Parameter\Parameter
	 */
	public function getParameter() {
		return $this->parameter;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Parameter\ParameterValue[]
	 */
	public function getValues() {
		return $this->values;
	}

}
