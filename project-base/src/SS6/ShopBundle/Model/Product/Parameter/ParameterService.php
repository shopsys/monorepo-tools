<?php

namespace SS6\ShopBundle\Model\Product\Parameter;

use SS6\ShopBundle\Model\Product\Parameter\Parameter;
use SS6\ShopBundle\Model\Product\Parameter\ParameterData;

class ParameterService {

	/**
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ParameterData $parameterData
	 * @return \SS6\ShopBundle\Model\Product\Parameter\Parameter
	 */
	public function create(ParameterData $parameterData) {
		return new Parameter($parameterData);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Parameter\Parameter $parameter
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ParameterData $parameterData
	 * @return \SS6\ShopBundle\Model\Product\Parameter\Parameter
	 */
	public function edit(Parameter $parameter, ParameterData $parameterData) {
		$parameter->edit($parameterData);

		return $parameter;
	}

}
