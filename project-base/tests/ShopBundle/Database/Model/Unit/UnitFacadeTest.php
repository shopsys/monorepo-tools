<?php

namespace Tests\ShopBundle\Database\Model\Unit;

use Shopsys\FrameworkBundle\DataFixtures\Demo\ProductDataFixture;
use Shopsys\FrameworkBundle\DataFixtures\Demo\UnitDataFixture;
use Shopsys\FrameworkBundle\Model\Product\ProductDataFactoryInterface;
use Shopsys\FrameworkBundle\Model\Product\ProductFacade;
use Shopsys\FrameworkBundle\Model\Product\Unit\UnitData;
use Shopsys\FrameworkBundle\Model\Product\Unit\UnitFacade;
use Tests\ShopBundle\Test\DatabaseTestCase;

class UnitFacadeTest extends DatabaseTestCase
{
    public function testDeleteByIdAndReplace()
    {
        $em = $this->getEntityManager();
        $unitFacade = $this->getContainer()->get(UnitFacade::class);
        /* @var $unitFacade \Shopsys\FrameworkBundle\Model\Product\Unit\UnitFacade */
        $productDataFactory = $this->getContainer()->get(ProductDataFactoryInterface::class);
        /* @var $productDataFactory \Shopsys\ShopBundle\Model\Product\ProductDataFactory */
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /* @var $productFacade \Shopsys\FrameworkBundle\Model\Product\ProductFacade */

        $unitData = new UnitData();
        $unitData->name = ['cs' => 'name'];
        $unitToDelete = $unitFacade->create($unitData);
        $unitToReplaceWith = $this->getReference(UnitDataFixture::UNIT_PIECES);
        /* @var $newUnit \Shopsys\FrameworkBundle\Model\Product\Unit\Unit */
        $product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1');
        /* @var $product \Shopsys\ShopBundle\Model\Product\Product */
        $productData = $productDataFactory->createFromProduct($product);
        /* @var $productData \Shopsys\ShopBundle\Model\Product\ProductData */

        $productData->unit = $unitToDelete;
        $productFacade->edit($product->getId(), $productData);

        $unitFacade->deleteById($unitToDelete->getId(), $unitToReplaceWith->getId());

        $em->refresh($product);

        $this->assertEquals($unitToReplaceWith, $product->getUnit());
    }
}
