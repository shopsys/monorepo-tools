<?php

namespace Tests\FrameworkBundle\Unit\Model\Product\Flag;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Model\Product\Flag\Flag;
use Shopsys\FrameworkBundle\Model\Product\Flag\FlagData;
use Shopsys\FrameworkBundle\Model\Product\Flag\FlagDataFactory;
use Shopsys\FrameworkBundle\Model\Product\Flag\FlagService;

class FlagServiceTest extends TestCase
{
    public function testEdit()
    {
        $flagService = new FlagService();

        $flagDataOld = new FlagData();
        $flagDataOld->name = ['cs' => 'flagNameCs', 'en' => 'flagNameEn'];
        $flagDataOld->rgbColor = '#336699';
        $flagDataEdit = new FlagData();
        $flagDataEdit->name = ['cs' => 'editFlagNameCs', 'en' => 'editFlagNameEn'];
        $flagDataEdit->rgbColor = '#00CCFF';
        $flag = new Flag($flagDataOld);

        $flagService->edit($flag, $flagDataEdit);

        $flagDataFactory = new FlagDataFactory();
        $flagDataNew = $flagDataFactory->createFromFlag($flag);

        $this->assertEquals($flagDataEdit, $flagDataNew);
    }
}
