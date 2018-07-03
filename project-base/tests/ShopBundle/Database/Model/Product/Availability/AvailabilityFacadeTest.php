<?php

namespace Tests\ShopBundle\Database\Model\Product\Availability;

use Shopsys\FrameworkBundle\DataFixtures\Base\AvailabilityDataFixture;
use Shopsys\FrameworkBundle\DataFixtures\Demo\ProductDataFixture;
use Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityData;
use Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityFacade;
use Shopsys\FrameworkBundle\Model\Product\ProductDataFactory;
use Shopsys\FrameworkBundle\Model\Product\ProductFacade;
use Tests\ShopBundle\Test\DatabaseTestCase;

class AvailabilityFacadeTest extends DatabaseTestCase
{
    public function testDeleteByIdAndReplace()
    {
        $em = $this->getEntityManager();
        $availabilityFacade = $this->getContainer()->get(AvailabilityFacade::class);
        /* @var $availabilityFacade \Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityFacade */
        $productDataFactory = $this->getContainer()->get(ProductDataFactory::class);
        /* @var $productDataFactory \Shopsys\FrameworkBundle\Model\Product\ProductDataFactory */
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /* @var $productFacade \Shopsys\FrameworkBundle\Model\Product\ProductFacade */

        $availabilityData = new AvailabilityData();
        $availabilityData->name = ['cs' => 'name'];
        $availabilityToDelete = $availabilityFacade->create($availabilityData);
        $availabilityToReplaceWith = $this->getReference(AvailabilityDataFixture::AVAILABILITY_IN_STOCK);
        /* @var $availabilityToReplaceWith \Shopsys\FrameworkBundle\Model\Product\Availability\Availability */
        $product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1');
        /* @var $product \Shopsys\FrameworkBundle\Model\Product\Product */
        $productData = $productDataFactory->createFromProduct($product);
        /* @var $productData \Shopsys\FrameworkBundle\Model\Product\ProductData */

        $productData->availability = $availabilityToDelete;
        $productData->outOfStockAvailability = $availabilityToDelete;

        $productFacade->edit($product->getId(), $productData);

        $availabilityFacade->deleteById($availabilityToDelete->getId(), $availabilityToReplaceWith->getId());

        $em->refresh($product);

        $this->assertEquals($availabilityToReplaceWith, $product->getAvailability());
        $this->assertEquals($availabilityToReplaceWith, $product->getOutOfStockAvailability());
    }
}
