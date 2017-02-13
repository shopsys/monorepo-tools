<?php

namespace Shopsys\ShopBundle\Tests\Database\Model\Product\Availability;

use Shopsys\ShopBundle\DataFixtures\Base\AvailabilityDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\ProductDataFixture;
use Shopsys\ShopBundle\Model\Product\Availability\AvailabilityData;
use Shopsys\ShopBundle\Model\Product\Availability\AvailabilityFacade;
use Shopsys\ShopBundle\Model\Product\ProductEditDataFactory;
use Shopsys\ShopBundle\Model\Product\ProductFacade;
use Shopsys\ShopBundle\Tests\Test\DatabaseTestCase;

class AvailabilityFacadeTest extends DatabaseTestCase
{
    public function testDeleteByIdAndReplace()
    {
        $em = $this->getEntityManager();
        $availabilityFacade = $this->getContainer()->get(AvailabilityFacade::class);
        /* @var $availabilityFacade \Shopsys\ShopBundle\Model\Product\Availability\AvailabilityFacade */
        $productEditDataFactory = $this->getContainer()->get(ProductEditDataFactory::class);
        /* @var $productEditDataFactory \Shopsys\ShopBundle\Model\Product\ProductEditDataFactory */
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /* @var $productFacade \Shopsys\ShopBundle\Model\Product\ProductFacade */

        $availabilityToDelete = $availabilityFacade->create(new AvailabilityData(['cs' => 'name']));
        $availabilityToReplaceWith = $this->getReference(AvailabilityDataFixture::IN_STOCK);
        /* @var $availabilityToReplaceWith \Shopsys\ShopBundle\Model\Product\Availability\Availability */
        $product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1');
        /* @var $product \Shopsys\ShopBundle\Model\Product\Product */
        $productEditData = $productEditDataFactory->createFromProduct($product);
        /* @var $productEditData \Shopsys\ShopBundle\Model\Product\ProductEditData */

        $productEditData->productData->availability = $availabilityToDelete;
        $productEditData->productData->outOfStockAvailability = $availabilityToDelete;

        $productFacade->edit($product->getId(), $productEditData);

        $availabilityFacade->deleteById($availabilityToDelete->getId(), $availabilityToReplaceWith->getId());

        $em->refresh($product);

        $this->assertEquals($availabilityToReplaceWith, $product->getAvailability());
        $this->assertEquals($availabilityToReplaceWith, $product->getOutOfStockAvailability());
    }
}
