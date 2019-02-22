<?php

namespace Tests\ShopBundle\Functional\Model\Product\Availability;

use Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityData;
use Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityFacade;
use Shopsys\FrameworkBundle\Model\Product\ProductDataFactoryInterface;
use Shopsys\FrameworkBundle\Model\Product\ProductFacade;
use Shopsys\ShopBundle\DataFixtures\Demo\AvailabilityDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\ProductDataFixture;
use Tests\ShopBundle\Test\TransactionFunctionalTestCase;

class AvailabilityFacadeTest extends TransactionFunctionalTestCase
{
    public function testDeleteByIdAndReplace()
    {
        $em = $this->getEntityManager();
        /** @var \Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityFacade $availabilityFacade */
        $availabilityFacade = $this->getContainer()->get(AvailabilityFacade::class);
        /** @var \Shopsys\ShopBundle\Model\Product\ProductDataFactory $productDataFactory */
        $productDataFactory = $this->getContainer()->get(ProductDataFactoryInterface::class);
        /** @var \Shopsys\FrameworkBundle\Model\Product\ProductFacade $productFacade */
        $productFacade = $this->getContainer()->get(ProductFacade::class);

        $availabilityData = new AvailabilityData();
        $availabilityData->name = ['cs' => 'name'];
        $availabilityToDelete = $availabilityFacade->create($availabilityData);
        /** @var \Shopsys\FrameworkBundle\Model\Product\Availability\Availability $availabilityToReplaceWith */
        $availabilityToReplaceWith = $this->getReference(AvailabilityDataFixture::AVAILABILITY_IN_STOCK);
        /** @var \Shopsys\ShopBundle\Model\Product\Product $product */
        $product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1');
        /** @var \Shopsys\FrameworkBundle\Model\Product\ProductData $productData */
        $productData = $productDataFactory->createFromProduct($product);

        $productData->availability = $availabilityToDelete;
        $productData->outOfStockAvailability = $availabilityToDelete;

        $productFacade->edit($product->getId(), $productData);

        $availabilityFacade->deleteById($availabilityToDelete->getId(), $availabilityToReplaceWith->getId());

        $em->refresh($product);

        $this->assertEquals($availabilityToReplaceWith, $product->getAvailability());
        $this->assertEquals($availabilityToReplaceWith, $product->getOutOfStockAvailability());
    }
}
