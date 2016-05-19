<?php

namespace SS6\ShopBundle\Tests\Unit\Model\Product\Availability;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Product\Availability\Availability;
use SS6\ShopBundle\Model\Product\Availability\AvailabilityData;
use SS6\ShopBundle\Model\Product\Availability\AvailabilityService;

/**
 * @UglyTest
 */
class AvailabilityServiceTest extends PHPUnit_Framework_TestCase {

	public function testCreate() {
		$availabilityService = new AvailabilityService();

		$availabilityDataOriginal = new AvailabilityData(['cs' => 'availabilityNameCs', 'en' => 'availabilityNameEn']);
		$availability = $availabilityService->create($availabilityDataOriginal);

		$availabilityDataNew = new AvailabilityData();
		$availabilityDataNew->setFromEntity($availability);

		$this->assertEquals($availabilityDataOriginal, $availabilityDataNew);
	}

	public function testEdit() {
		$availabilityService = new AvailabilityService();

		$availabilityDataOld = new AvailabilityData(['cs' => 'availabilityNameCs', 'en' => 'availabilityNameEn']);
		$availabilityDataEdit = new AvailabilityData(['cs' => 'editAvailabilityNameCs', 'en' => 'editAvailabilityNameEn']);
		$availability = new Availability($availabilityDataOld);

		$availabilityService->edit($availability, $availabilityDataEdit);

		$availabilityDataNew = new AvailabilityData();
		$availabilityDataNew->setFromEntity($availability);

		$this->assertEquals($availabilityDataEdit, $availabilityDataNew);
	}

}
