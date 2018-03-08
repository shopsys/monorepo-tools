<?php

namespace Tests\ShopBundle\Unit\Model\Product\Parameter;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Model\Product\Parameter\Parameter;
use Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterData;
use Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterService;

class ParameterServiceTest extends TestCase
{
    public function testCreate()
    {
        $parameterService = new ParameterService();

        $parameterDataOriginal = new ParameterData(['cs' => 'parameterName']);
        $parameter = $parameterService->create($parameterDataOriginal);

        $parameterDataNew = new ParameterData();
        $parameterDataNew->setFromEntity($parameter);

        $this->assertEquals($parameterDataOriginal, $parameterDataNew);
    }

    public function testEdit()
    {
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
