<?php

namespace SS6\ShopBundle\Tests\Database\Model\Product\Availability;

use SS6\ShopBundle\DataFixtures\Base\AvailabilityDataFixture;
use SS6\ShopBundle\DataFixtures\Demo\ProductDataFixture;
use SS6\ShopBundle\Model\Product\Availability\AvailabilityData;
use SS6\ShopBundle\Model\Product\Availability\AvailabilityFacade;
use SS6\ShopBundle\Model\Product\ProductEditDataFactory;
use SS6\ShopBundle\Model\Product\ProductEditFacade;
use SS6\ShopBundle\Tests\Test\DatabaseTestCase;

/**
 * @UglyTest
 */
class AvailabilityFacadeTest extends DatabaseTestCase {

	public function testDeleteByIdAndReplace() {
		$em = $this->getEntityManager();
		$availabilityFacade = $this->getContainer()->get(AvailabilityFacade::class);
		/* @var $availabilityFacade \SS6\ShopBundle\Model\Product\Availability\AvailabilityFacade */
		$productEditDataFactory = $this->getContainer()->get(ProductEditDataFactory::class);
		/* @var $productEditDataFactory \SS6\ShopBundle\Model\Product\ProductEditDataFactory */
		$productEditFacade = $this->getContainer()->get(ProductEditFacade::class);
		/* @var $productEditFacade \SS6\ShopBundle\Model\Product\ProductEditFacade */

		$availabilityToDelete = $availabilityFacade->create(new AvailabilityData(['cs' => 'name']));
		$availabilityToReplaceWith = $this->getReference(AvailabilityDataFixture::IN_STOCK);
		/* @var $availabilityToReplaceWith \SS6\ShopBundle\Model\Product\Availability\Availability */
		$product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1');
		/* @var $product \SS6\ShopBundle\Model\Product\Product */
		$productEditData = $productEditDataFactory->createFromProduct($product);
		/* @var $productEditData \SS6\ShopBundle\Model\Product\ProductEditData */

		$productEditData->productData->availability = $availabilityToDelete;
		$productEditData->productData->outOfStockAvailability = $availabilityToDelete;

		$productEditFacade->edit($product->getId(), $productEditData);

		$availabilityFacade->deleteById($availabilityToDelete->getId(), $availabilityToReplaceWith->getId());

		$em->refresh($product);

		$this->assertEquals($availabilityToReplaceWith, $product->getAvailability());
		$this->assertEquals($availabilityToReplaceWith, $product->getOutOfStockAvailability());
	}
}
