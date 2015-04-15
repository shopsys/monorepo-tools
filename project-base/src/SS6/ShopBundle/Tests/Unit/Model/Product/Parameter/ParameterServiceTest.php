<?php

namespace SS6\ShopBundle\Tests\Unit\Model\Product\Parameter;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Product\Parameter\Parameter;
use SS6\ShopBundle\Model\Product\Parameter\ParameterData;
use SS6\ShopBundle\Model\Product\Parameter\ParameterService;

class ParameterServiceTest extends PHPUnit_Framework_TestCase {

	public function testCreate() {
		$parameterService = new ParameterService();

		$parameterDataOriginal = new ParameterData(['cs' => 'parameterName']);
		$parameter = $parameterService->create($parameterDataOriginal);

		$parameterDataNew = new ParameterData();
		$parameterDataNew->setFromEntity($parameter);

		$this->assertEquals($parameterDataOriginal, $parameterDataNew);
	}

	public function testEdit() {
		$parameterService = new ParameterService();

		$parameterDataOld = new ParameterData(['cs' => 'oldParameterName']);
		$parameterDataEdit = new ParameterData(['cs' => 'editParameterName']);
		$parameter = new Parameter($parameterDataOld);

		$parameterService->edit($parameter, $parameterDataEdit);

		$parameterDataNew = new ParameterData();
		$parameterDataNew->setFromEntity($parameter);

		$this->assertEquals($parameterDataEdit, $parameterDataNew);
	}

}
