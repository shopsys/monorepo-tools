<?php

namespace SS6\ShopBundle\Tests\Unit\Model\Product\Availability;

use SS6\ShopBundle\DataFixtures\Base\AvailabilityDataFixture;
use SS6\ShopBundle\Model\Product\Availability\Availability;
use SS6\ShopBundle\Model\Product\Availability\AvailabilityFacade;
use SS6\ShopBundle\Model\Product\Availability\ProductAvailabilityCalculation;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductData;
use SS6\ShopBundle\Tests\Test\FunctionalTestCase;

class ProductAvailabilityCalculationTest extends FunctionalTestCase {

	/**
	 * @dataProvider getTestGetCalculatedAvailabilityData
	 */
	public function testGetCalculatedAvailability(
		$usingStock,
		$stockQuantity,
		Availability $availability = null,
		Availability $outOfStockAvailability = null,
		Availability $defaultInStockAvailability = null,
		Availability $expectedCalculatedAvailability = null
	) {
		$productData = new ProductData();
		$productData->usingStock = $usingStock;
		$productData->stockQuantity = $stockQuantity;
		$productData->availability = $availability;
		$productData->outOfStockAvailability = $outOfStockAvailability;

		$product = new Product($productData);

		$availabilityFacadeMock = $this->getMockBuilder(AvailabilityFacade::class)
			->setMethods(['getDefaultInStockAvailability'])
			->disableOriginalConstructor()
			->getMock();
		$availabilityFacadeMock->expects($this->any())->method('getDefaultInStockAvailability')
			->will($this->returnValue($defaultInStockAvailability));

		$productAvailabilityCalculation = new ProductAvailabilityCalculation($availabilityFacadeMock);

		$calculatedAvailability = $productAvailabilityCalculation->getCalculatedAvailability($product);

		$this->assertSame($expectedCalculatedAvailability, $calculatedAvailability);
	}

	public function getTestGetCalculatedAvailabilityData() {
		return [
			[
				'usingStock' => false,
				'stockQuantity' => null,
				'availability' => $this->getReference(AvailabilityDataFixture::IN_STOCK),
				'outOfStockAvailability' => null,
				'defaultInStockAvailability' => $this->getReference(AvailabilityDataFixture::IN_STOCK),
				'calculatedAvailability' => $this->getReference(AvailabilityDataFixture::IN_STOCK),
			],
			[
				'usingStock' => true,
				'stockQuantity' => 5,
				'availability' => null,
				'outOfStockAvailability' => $this->getReference(AvailabilityDataFixture::OUT_OF_STOCK),
				'defaultInStockAvailability' => $this->getReference(AvailabilityDataFixture::IN_STOCK),
				'calculatedAvailability' => $this->getReference(AvailabilityDataFixture::IN_STOCK),
			],
			[
				'usingStock' => true,
				'stockQuantity' => 0,
				'availability' => null,
				'outOfStockAvailability' => $this->getReference(AvailabilityDataFixture::OUT_OF_STOCK),
				'defaultInStockAvailability' => $this->getReference(AvailabilityDataFixture::IN_STOCK),
				'calculatedAvailability' => $this->getReference(AvailabilityDataFixture::OUT_OF_STOCK),
			],
			[
				'usingStock' => true,
				'stockQuantity' => -1,
				'availability' => null,
				'outOfStockAvailability' => $this->getReference(AvailabilityDataFixture::OUT_OF_STOCK),
				'defaultInStockAvailability' => $this->getReference(AvailabilityDataFixture::IN_STOCK),
				'calculatedAvailability' => $this->getReference(AvailabilityDataFixture::OUT_OF_STOCK),
			],
		];
	}

}
