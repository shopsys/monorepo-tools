<?php

namespace Tests\FrameworkBundle\Unit\Model\Product\Flag;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Model\Product\Flag\Flag;
use Shopsys\FrameworkBundle\Model\Product\Flag\FlagData;
use Shopsys\FrameworkBundle\Model\Product\Flag\FlagFactory;
use Shopsys\FrameworkBundle\Model\Product\Flag\FlagService;

class FlagServiceTest extends TestCase
{
    public function testCreate()
    {
        $flagService = new FlagService(new FlagFactory());

        $flagDataOriginal = new FlagData();
        $flagDataOriginal->name = ['cs' => 'flagNameCs', 'en' => 'flagNameEn'];
        $flagDataOriginal->rgbColor = '#336699';
        $flag = $flagService->create($flagDataOriginal);

        $flagDataNew = new FlagData();
        $flagDataNew->setFromEntity($flag);

        $this->assertEquals($flagDataOriginal, $flagDataNew);
    }

    public function testEdit()
    {
        $flagService = new FlagService(new FlagFactory());

        $flagDataOld = new FlagData();
        $flagDataOld->name = ['cs' => 'flagNameCs', 'en' => 'flagNameEn'];
        $flagDataOld->rgbColor = '#336699';
        $flagDataEdit = new FlagData();
        $flagDataEdit->name = ['cs' => 'editFlagNameCs', 'en' => 'editFlagNameEn'];
        $flagDataEdit->rgbColor = '#00CCFF';
        $flag = new Flag($flagDataOld);

        $flagService->edit($flag, $flagDataEdit);

        $flagDataNew = new FlagData();
        $flagDataNew->setFromEntity($flag);

        $this->assertEquals($flagDataEdit, $flagDataNew);
    }
}
