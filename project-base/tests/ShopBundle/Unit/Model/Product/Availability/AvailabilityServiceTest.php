<?php

namespace Tests\ShopBundle\Unit\Model\Product\Availability;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Model\Product\Availability\Availability;
use Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityData;
use Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityService;

class AvailabilityServiceTest extends TestCase
{
    public function testCreate()
    {
        $availabilityService = new AvailabilityService();

        $availabilityDataOriginal = new AvailabilityData(['cs' => 'availabilityNameCs', 'en' => 'availabilityNameEn']);
        $availability = $availabilityService->create($availabilityDataOriginal);

        $availabilityDataNew = new AvailabilityData();
        $availabilityDataNew->setFromEntity($availability);

        $this->assertEquals($availabilityDataOriginal, $availabilityDataNew);
    }

    public function testEdit()
    {
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
