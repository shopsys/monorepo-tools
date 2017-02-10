<?php

namespace Shopsys\ShopBundle\Tests\Unit\Model\Product\Flag;

use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Model\Product\Flag\Flag;
use Shopsys\ShopBundle\Model\Product\Flag\FlagData;
use Shopsys\ShopBundle\Model\Product\Flag\FlagService;

class FlagServiceTest extends PHPUnit_Framework_TestCase
{

    public function testCreate() {
        $flagService = new FlagService();

        $flagDataOriginal = new FlagData(['cs' => 'flagNameCs', 'en' => 'flagNameEn'], '#336699');
        $flag = $flagService->create($flagDataOriginal);

        $flagDataNew = new FlagData();
        $flagDataNew->setFromEntity($flag);

        $this->assertEquals($flagDataOriginal, $flagDataNew);
    }

    public function testEdit() {
        $flagService = new FlagService();

        $flagDataOld = new FlagData(['cs' => 'flagNameCs', 'en' => 'flagNameEn'], '#336699');
        $flagDataEdit = new FlagData(['cs' => 'editFlagNameCs', 'en' => 'editFlagNameEn'], '#00CCFF');
        $flag = new Flag($flagDataOld);

        $flagService->edit($flag, $flagDataEdit);

        $flagDataNew = new FlagData();
        $flagDataNew->setFromEntity($flag);

        $this->assertEquals($flagDataEdit, $flagDataNew);
    }

}
