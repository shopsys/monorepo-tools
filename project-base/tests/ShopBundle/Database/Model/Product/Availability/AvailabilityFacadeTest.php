<?php

namespace Tests\ShopBundle\Database\Model\Product\Availability;

use Shopsys\FrameworkBundle\DataFixtures\Base\AvailabilityDataFixture;
use Shopsys\FrameworkBundle\DataFixtures\Demo\ProductDataFixture;
use Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityData;
use Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityFacade;
use Shopsys\FrameworkBundle\Model\Product\ProductEditDataFactory;
use Shopsys\FrameworkBundle\Model\Product\ProductFacade;
use Tests\ShopBundle\Test\DatabaseTestCase;

class AvailabilityFacadeTest extends DatabaseTestCase
{
    public function testDeleteByIdAndReplace()
    {
        $em = $this->getEntityManager();
        $availabilityFacade = $this->getServiceByType(AvailabilityFacade::class);
        /* @var $availabilityFacade \Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityFacade */
        $productEditDataFactory = $this->getServiceByType(ProductEditDataFactory::class);
        /* @var $productEditDataFactory \Shopsys\FrameworkBundle\Model\Product\ProductEditDataFactory */
        $productFacade = $this->getServiceByType(ProductFacade::class);
        /* @var $productFacade \Shopsys\FrameworkBundle\Model\Product\ProductFacade */

        $availabilityToDelete = $availabilityFacade->create(new AvailabilityData(['cs' => 'name']));
        $availabilityToReplaceWith = $this->getReference(AvailabilityDataFixture::AVAILABILITY_IN_STOCK);
        /* @var $availabilityToReplaceWith \Shopsys\FrameworkBundle\Model\Product\Availability\Availability */
        $product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1');
        /* @var $product \Shopsys\FrameworkBundle\Model\Product\Product */
        $productEditData = $productEditDataFactory->createFromProduct($product);
        /* @var $productEditData \Shopsys\FrameworkBundle\Model\Product\ProductEditData */

        $productEditData->productData->availability = $availabilityToDelete;
        $productEditData->productData->outOfStockAvailability = $availabilityToDelete;

        $productFacade->edit($product->getId(), $productEditData);

        $availabilityFacade->deleteById($availabilityToDelete->getId(), $availabilityToReplaceWith->getId());

        $em->refresh($product);

        $this->assertEquals($availabilityToReplaceWith, $product->getAvailability());
        $this->assertEquals($availabilityToReplaceWith, $product->getOutOfStockAvailability());
    }
}
