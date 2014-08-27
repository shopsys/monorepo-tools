<?php

namespace SS6\ShopBundle\Tests\Model\Product\Parameter;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Product\Parameter\Parameter;
use SS6\ShopBundle\Model\Product\Parameter\ParameterData;
use SS6\ShopBundle\Model\Product\Parameter\ParameterService;

class ParameterServiceTest extends PHPUnit_Framework_TestCase {

	public function testCreate() {
		$availabilityService = new ParameterService();

		$availabilityDataOriginal = new ParameterData('availabilityName');
		$availability = $availabilityService->create($availabilityDataOriginal);

		$availabilityDataNew = new ParameterData();
		$availabilityDataNew->setFromEntity($availability);

		$this->assertEquals($availabilityDataOriginal, $availabilityDataNew);
	}

	public function testEdit() {
		$availabilityService = new ParameterService();

		$availabilityDataOld = new ParameterData('oldParameterName');
		$availabilityDataEdit = new ParameterData('editParameterName');
		$availability = new Parameter($availabilityDataOld);

		$availabilityService->edit($availability, $availabilityDataEdit);

		$availabilityDataNew = new ParameterData();
		$availabilityDataNew->setFromEntity($availability);

		$this->assertEquals($availabilityDataEdit, $availabilityDataNew);
	}

}
