<?php

namespace Shopsys\ShopBundle\Tests\Database\Model\Unit;

use Shopsys\ShopBundle\DataFixtures\Base\UnitDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\ProductDataFixture;
use Shopsys\ShopBundle\Model\Product\ProductEditDataFactory;
use Shopsys\ShopBundle\Model\Product\ProductFacade;
use Shopsys\ShopBundle\Model\Product\Unit\UnitData;
use Shopsys\ShopBundle\Model\Product\Unit\UnitFacade;
use Shopsys\ShopBundle\Tests\Test\DatabaseTestCase;

class UnitFacadeTest extends DatabaseTestCase {

    public function testDeleteByIdAndReplace() {
        $em = $this->getEntityManager();
        $unitFacade = $this->getContainer()->get(UnitFacade::class);
        /* @var $unitFacade \Shopsys\ShopBundle\Model\Product\Unit\UnitFacade */
        $productEditDataFactory = $this->getContainer()->get(ProductEditDataFactory::class);
        /* @var $productEditDataFactory \Shopsys\ShopBundle\Model\Product\ProductEditDataFactory */
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /* @var $productFacade \Shopsys\ShopBundle\Model\Product\ProductFacade */

        $unitToDelete = $unitFacade->create(new UnitData(['cs' => 'name']));
        $unitToReplaceWith = $this->getReference(UnitDataFixture::PCS);
        /* @var $newUnit \Shopsys\ShopBundle\Model\Product\Unit\Unit */
        $product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1');
        /* @var $product \Shopsys\ShopBundle\Model\Product\Product */
        $productEditData = $productEditDataFactory->createFromProduct($product);
        /* @var $productEditData \Shopsys\ShopBundle\Model\Product\ProductEditData */

        $productEditData->productData->unit = $unitToDelete;
        $productFacade->edit($product->getId(), $productEditData);

        $unitFacade->deleteById($unitToDelete->getId(), $unitToReplaceWith->getId());

        $em->refresh($product);

        $this->assertEquals($unitToReplaceWith, $product->getUnit());
    }

}
