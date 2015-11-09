<?php

namespace SS6\ShopBundle\Tests\Unit\Model\Product\Availability;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\DataFixtures\Base\AvailabilityDataFixture;
use SS6\ShopBundle\Model\Product\Availability\Availability;
use SS6\ShopBundle\Model\Product\Availability\AvailabilityFacade;
use SS6\ShopBundle\Model\Product\Availability\ProductAvailabilityCalculation;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductData;
use SS6\ShopBundle\Model\Product\ProductRepository;
use SS6\ShopBundle\Model\Product\ProductSellingDeniedRecalculator;
use SS6\ShopBundle\Model\Product\ProductVisibilityFacade;
use SS6\ShopBundle\Tests\Test\FunctionalTestCase;

class ProductAvailabilityCalculationTest extends FunctionalTestCase {

	/**
	 * @dataProvider getTestGetCalculatedAvailabilityData
	 */
	public function testGetCalculatedAvailability(
		$usingStock,
		$stockQuantity,
		$outOfStockAction,
		Availability $availability = null,
		Availability $outOfStockAvailability = null,
		Availability $defaultInStockAvailability = null,
		Availability $expectedCalculatedAvailability = null
	) {
		$productData = new ProductData();
		$productData->usingStock = $usingStock;
		$productData->stockQuantity = $stockQuantity;
		$productData->availability = $availability;
		$productData->outOfStockAction = $outOfStockAction;
		$productData->outOfStockAvailability = $outOfStockAvailability;

		$product = new Product($productData);

		$availabilityFacadeMock = $this->getMockBuilder(AvailabilityFacade::class)
			->setMethods(['getDefaultInStockAvailability'])
			->disableOriginalConstructor()
			->getMock();
		$availabilityFacadeMock->expects($this->any())->method('getDefaultInStockAvailability')
			->will($this->returnValue($defaultInStockAvailability));

		$productSellingDeniedRecalculatorMock = $this->getMock(ProductSellingDeniedRecalculator::class,	[],	[], '',	false);
		$productVisibilityFacadeMock = $this->getMock(ProductVisibilityFacade::class,	[],	[], '',	false);
		$entityManagerMock = $this->getMock(EntityManager::class,	[],	[], '',	false);
		$productRepositoryMock = $this->getMock(ProductRepository::class,	[],	[], '',	false);

		$productAvailabilityCalculation = new ProductAvailabilityCalculation(
			$availabilityFacadeMock,
			$productSellingDeniedRecalculatorMock,
			$productVisibilityFacadeMock,
			$entityManagerMock,
			$productRepositoryMock
		);

		$calculatedAvailability = $productAvailabilityCalculation->getCalculatedAvailability($product);

		$this->assertSame($expectedCalculatedAvailability, $calculatedAvailability);
	}

	public function getTestGetCalculatedAvailabilityData() {
		return [
			[
				'usingStock' => false,
				'stockQuantity' => null,
				'outOfStockAction' => null,
				'availability' => $this->getReference(AvailabilityDataFixture::IN_STOCK),
				'outOfStockAvailability' => null,
				'defaultInStockAvailability' => $this->getReference(AvailabilityDataFixture::IN_STOCK),
				'calculatedAvailability' => $this->getReference(AvailabilityDataFixture::IN_STOCK),
			],
			[
				'usingStock' => true,
				'stockQuantity' => null,
				'outOfStockAction' => Product::OUT_OF_STOCK_ACTION_HIDE,
				'availability' => $this->getReference(AvailabilityDataFixture::IN_STOCK),
				'outOfStockAvailability' => null,
				'defaultInStockAvailability' => $this->getReference(AvailabilityDataFixture::IN_STOCK),
				'calculatedAvailability' => $this->getReference(AvailabilityDataFixture::IN_STOCK),
			],
			[
				'usingStock' => true,
				'stockQuantity' => 5,
				'outOfStockAction' => Product::OUT_OF_STOCK_ACTION_SET_ALTERNATE_AVAILABILITY,
				'availability' => null,
				'outOfStockAvailability' => $this->getReference(AvailabilityDataFixture::OUT_OF_STOCK),
				'defaultInStockAvailability' => $this->getReference(AvailabilityDataFixture::IN_STOCK),
				'calculatedAvailability' => $this->getReference(AvailabilityDataFixture::IN_STOCK),
			],
			[
				'usingStock' => true,
				'stockQuantity' => 0,
				'outOfStockAction' => Product::OUT_OF_STOCK_ACTION_SET_ALTERNATE_AVAILABILITY,
				'availability' => null,
				'outOfStockAvailability' => $this->getReference(AvailabilityDataFixture::OUT_OF_STOCK),
				'defaultInStockAvailability' => $this->getReference(AvailabilityDataFixture::IN_STOCK),
				'calculatedAvailability' => $this->getReference(AvailabilityDataFixture::OUT_OF_STOCK),
			],
			[
				'usingStock' => true,
				'stockQuantity' => -1,
				'outOfStockAction' => Product::OUT_OF_STOCK_ACTION_SET_ALTERNATE_AVAILABILITY,
				'availability' => null,
				'outOfStockAvailability' => $this->getReference(AvailabilityDataFixture::OUT_OF_STOCK),
				'defaultInStockAvailability' => $this->getReference(AvailabilityDataFixture::IN_STOCK),
				'calculatedAvailability' => $this->getReference(AvailabilityDataFixture::OUT_OF_STOCK),
			],
		];
	}

	public function testGetCalculatedAvailabilityMainVariant() {
		$productData = new ProductData();

		$productData->availability = $this->getReference(AvailabilityDataFixture::IN_STOCK);
		$variant1 = new Product($productData);

		$productData->availability = $this->getReference(AvailabilityDataFixture::ON_REQUEST);
		$variant2 = new Product($productData);

		$productData->availability = $this->getReference(AvailabilityDataFixture::OUT_OF_STOCK);
		$variant3 = new Product($productData);

		$productData->availability = $this->getReference(AvailabilityDataFixture::PREPARING);
		$variant4 = new Product($productData);

		$variants = [$variant1, $variant2, $variant3, $variant4];
		$mainVariant = new Product(new ProductData(), $variants);

		$availabilityFacadeMock = $this->getMock(AvailabilityFacade::class, [], [], '', false);
		$productSellingDeniedRecalculatorMock = $this->getMock(ProductSellingDeniedRecalculator::class,	[],	[], '',	false);
		$productVisibilityFacadeMock = $this->getMock(ProductVisibilityFacade::class,	[],	[], '',	false);
		$entityManagerMock = $this->getMock(EntityManager::class,	[],	[], '',	false);

		$productRepositoryMock = $this->getMock(ProductRepository::class,	[],	[], '',	false);
		$productRepositoryMock
			->expects($this->atLeastOnce())
			->method('getAtLeastSomewhereSellableVariantsByMainVariant')
			->with($this->equalTo($mainVariant))
			->willReturn($variants);

		$productAvailabilityCalculation = new ProductAvailabilityCalculation(
			$availabilityFacadeMock,
			$productSellingDeniedRecalculatorMock,
			$productVisibilityFacadeMock,
			$entityManagerMock,
			$productRepositoryMock
		);

		$variant1->setCalculatedAvailability($productAvailabilityCalculation->getCalculatedAvailability($variant1));
		$variant2->setCalculatedAvailability($productAvailabilityCalculation->getCalculatedAvailability($variant2));
		$variant3->setCalculatedAvailability($productAvailabilityCalculation->getCalculatedAvailability($variant3));
		$variant4->setCalculatedAvailability($productAvailabilityCalculation->getCalculatedAvailability($variant4));

		$mainVariantCalculatedAvailability = $productAvailabilityCalculation->getCalculatedAvailability($mainVariant);

		$this->assertSame($variant1->getCalculatedAvailability(), $mainVariantCalculatedAvailability);
	}

	public function testGetCalculatedAvailabilityMainVariantWithNoSellableVariants() {
		$productData = new ProductData();
		$productData->availability = $this->getReference(AvailabilityDataFixture::ON_REQUEST);
		$variant = new Product($productData);

		$mainVariant = new Product(new ProductData(), [$variant]);

		$availabilityFacadeMock = $this->getMock(AvailabilityFacade::class, ['getDefaultInStockAvailability'], [], '', false);
		$defaultInStockAvailability = $this->getReference(AvailabilityDataFixture::IN_STOCK);
		$availabilityFacadeMock
			->expects($this->any())
			->method('getDefaultInStockAvailability')
			->willReturn($defaultInStockAvailability);
		$productSellingDeniedRecalculatorMock = $this->getMock(ProductSellingDeniedRecalculator::class,	[],	[], '',	false);
		$productVisibilityFacadeMock = $this->getMock(ProductVisibilityFacade::class,	[],	[], '',	false);
		$entityManagerMock = $this->getMock(EntityManager::class,	[],	[], '',	false);

		$productRepositoryMock = $this->getMock(ProductRepository::class,	[],	[], '',	false);
		$productRepositoryMock
			->expects($this->atLeastOnce())
			->method('getAtLeastSomewhereSellableVariantsByMainVariant')
			->with($this->equalTo($mainVariant))
			->willReturn([]);

		$productAvailabilityCalculation = new ProductAvailabilityCalculation(
			$availabilityFacadeMock,
			$productSellingDeniedRecalculatorMock,
			$productVisibilityFacadeMock,
			$entityManagerMock,
			$productRepositoryMock
		);

		$variant->setCalculatedAvailability($productAvailabilityCalculation->getCalculatedAvailability($variant));

		$mainVariantCalculatedAvailability = $productAvailabilityCalculation->getCalculatedAvailability($mainVariant);

		$this->assertSame($defaultInStockAvailability, $mainVariantCalculatedAvailability);
	}

}
