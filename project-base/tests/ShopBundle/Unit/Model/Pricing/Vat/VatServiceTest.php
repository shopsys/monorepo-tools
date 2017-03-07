<?php

namespace Tests\ShopBundle\Unit\Model\Pricing\Vat;

use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Model\Pricing\Vat\Vat;
use Shopsys\ShopBundle\Model\Pricing\Vat\VatData;
use Shopsys\ShopBundle\Model\Pricing\Vat\VatService;

class VatServiceTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $vatService = new VatService();

        $vatDataOriginal = new VatData('vatName', '21.00');
        $vat = $vatService->create($vatDataOriginal);

        $vatDataNew = new VatData();
        $vatDataNew->setFromEntity($vat);

        $this->assertEquals($vatDataOriginal, $vatDataNew);
    }

    public function testEdit()
    {
        $vatService = new VatService();

        $vatDataOld = new VatData('oldVatName', '21.00');
        $vatDataEdit = new VatData('editVatName', '15.00');
        $vat = new Vat($vatDataOld);

        $vatService->edit($vat, $vatDataEdit);

        $this->assertSame('editVatName', $vat->getName());
        $this->assertEquals('21.00', $vat->getPercent());
    }
}
