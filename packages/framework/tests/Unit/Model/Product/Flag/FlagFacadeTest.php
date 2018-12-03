<?php

namespace Tests\FrameworkBundle\Unit\Model\Product\Flag;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver;
use Shopsys\FrameworkBundle\Model\Product\Flag\FlagData;
use Shopsys\FrameworkBundle\Model\Product\Flag\FlagDataFactory;
use Shopsys\FrameworkBundle\Model\Product\Flag\FlagFactory;

class FlagFacadeTest extends TestCase
{
    public function testCreate()
    {
        $flagFactory = new FlagFactory(new EntityNameResolver([]));

        $flagDataOriginal = new FlagData();
        $flagDataOriginal->name = ['cs' => 'flagNameCs', 'en' => 'flagNameEn'];
        $flagDataOriginal->rgbColor = '#336699';
        $flag = $flagFactory->create($flagDataOriginal);

        $flagDataFactory = new FlagDataFactory();
        $flagDataNew = $flagDataFactory->createFromFlag($flag);

        $this->assertEquals($flagDataOriginal, $flagDataNew);
    }
}
