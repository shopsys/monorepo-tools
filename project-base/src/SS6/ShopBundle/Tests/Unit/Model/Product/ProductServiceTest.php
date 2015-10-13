<?php

namespace SS6\ShopBundle\Tests\Unit\Model\Product;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Pricing\BasePriceCalculation;
use SS6\ShopBundle\Model\Pricing\InputPriceCalculation;
use SS6\ShopBundle\Model\Pricing\PricingSetting;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Pricing\Vat\VatData;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculation;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductData;
use SS6\ShopBundle\Model\Product\ProductService;

class ProductServiceTest extends PHPUnit_Framework_TestCase {

	public function testEditSchedulesPriceRecalculation() {
		$productPriceCalculationMock = $this->getMockBuilder(ProductPriceCalculation::class)
			->disableOriginalConstructor()
			->getMock();
		$inputPriceCalculationMock = $this->getMockBuilder(InputPriceCalculation::class)
			->disableOriginalConstructor()
			->getMock();
		$basePriceCalculationMock = $this->getMockBuilder(BasePriceCalculation::class)
			->disableOriginalConstructor()
			->getMock();
		$pricingSettingMock = $this->getMockBuilder(PricingSetting::class)
			->disableOriginalConstructor()
			->getMock();
		$productPriceRecalculationSchedulerMock = $this->getMockBuilder(ProductPriceRecalculationScheduler::class)
			->disableOriginalConstructor()
			->getMock();
		$productPriceRecalculationSchedulerMock->expects($this->once())->method('scheduleRecalculatePriceForProduct');

		$productService = new ProductService(
			$productPriceCalculationMock,
			$inputPriceCalculationMock,
			$basePriceCalculationMock,
			$pricingSettingMock,
			$productPriceRecalculationSchedulerMock
		);

		$productData = new ProductData();
		$product = new Product($productData);

		$productService->edit($product, $productData);
	}

	public function testSetInputPriceSchedulesPriceRecalculation() {
		$productPriceCalculationMock = $this->getMockBuilder(ProductPriceCalculation::class)
			->disableOriginalConstructor()
			->getMock();
		$inputPriceCalculationMock = $this->getMockBuilder(InputPriceCalculation::class)
			->disableOriginalConstructor()
			->getMock();
		$basePriceCalculationMock = $this->getMockBuilder(BasePriceCalculation::class)
			->disableOriginalConstructor()
			->getMock();
		$pricingSettingMock = $this->getMockBuilder(PricingSetting::class)
			->disableOriginalConstructor()
			->getMock();
		$productPriceRecalculationSchedulerMock = $this->getMockBuilder(ProductPriceRecalculationScheduler::class)
			->disableOriginalConstructor()
			->getMock();
		$productPriceRecalculationSchedulerMock->expects($this->once())->method('scheduleRecalculatePriceForProduct');

		$productService = new ProductService(
			$productPriceCalculationMock,
			$inputPriceCalculationMock,
			$basePriceCalculationMock,
			$pricingSettingMock,
			$productPriceRecalculationSchedulerMock
		);

		$productData = new ProductData();
		$product = new Product($productData);

		$productService->setInputPrice($product, 100);
	}

	public function testChangeVatSchedulesPriceRecalculation() {
		$productPriceCalculationMock = $this->getMockBuilder(ProductPriceCalculation::class)
			->disableOriginalConstructor()
			->getMock();
		$inputPriceCalculationMock = $this->getMockBuilder(InputPriceCalculation::class)
			->disableOriginalConstructor()
			->getMock();
		$basePriceCalculationMock = $this->getMockBuilder(BasePriceCalculation::class)
			->disableOriginalConstructor()
			->getMock();
		$pricingSettingMock = $this->getMockBuilder(PricingSetting::class)
			->disableOriginalConstructor()
			->getMock();
		$productPriceRecalculationSchedulerMock = $this->getMockBuilder(ProductPriceRecalculationScheduler::class)
			->disableOriginalConstructor()
			->getMock();
		$productPriceRecalculationSchedulerMock->expects($this->once())->method('scheduleRecalculatePriceForProduct');

		$productService = new ProductService(
			$productPriceCalculationMock,
			$inputPriceCalculationMock,
			$basePriceCalculationMock,
			$pricingSettingMock,
			$productPriceRecalculationSchedulerMock
		);

		$productData = new ProductData();
		$product = new Product($productData);

		$vatData = new VatData();
		$vat = new Vat($vatData);

		$productService->changeVat($product, $vat);
	}

	public function testDeleteNotVariant() {
		$productPriceCalculationMock = $this->getMock(ProductPriceCalculation::class, null, [], '', false);
		$inputPriceCalculationMock = $this->getMock(InputPriceCalculation::class, null, [], '', false);
		$basePriceCalculationMock = $this->getMock(BasePriceCalculation::class, null, [], '', false);
		$pricingSettingMock = $this->getMock(PricingSetting::class, null, [], '', false);
		$productPriceRecalculationSchedulerMock = $this->getMock(ProductPriceRecalculationScheduler::class, null, [], '', false);

		$productService = new ProductService(
			$productPriceCalculationMock,
			$inputPriceCalculationMock,
			$basePriceCalculationMock,
			$pricingSettingMock,
			$productPriceRecalculationSchedulerMock
		);

		$productData = new ProductData();
		$product = new Product($productData);

		$this->assertNull($productService->delete($product)->getProductForRecalculations());
	}

	public function testDeleteVariant() {
		$productPriceCalculationMock = $this->getMock(ProductPriceCalculation::class, null, [], '', false);
		$inputPriceCalculationMock = $this->getMock(InputPriceCalculation::class, null, [], '', false);
		$basePriceCalculationMock = $this->getMock(BasePriceCalculation::class, null, [], '', false);
		$pricingSettingMock = $this->getMock(PricingSetting::class, null, [], '', false);
		$productPriceRecalculationSchedulerMock = $this->getMock(ProductPriceRecalculationScheduler::class, null, [], '', false);

		$productService = new ProductService(
			$productPriceCalculationMock,
			$inputPriceCalculationMock,
			$basePriceCalculationMock,
			$pricingSettingMock,
			$productPriceRecalculationSchedulerMock
		);

		$productData = new ProductData();
		$mainVariant = new Product($productData);
		$variant = new Product($productData);
		$mainVariant->setVariants([$variant]);

		$this->assertSame($mainVariant, $productService->delete($variant)->getProductForRecalculations());
	}

	public function testDeleteMainVariant() {
		$productPriceCalculationMock = $this->getMock(ProductPriceCalculation::class, null, [], '', false);
		$inputPriceCalculationMock = $this->getMock(InputPriceCalculation::class, null, [], '', false);
		$basePriceCalculationMock = $this->getMock(BasePriceCalculation::class, null, [], '', false);
		$pricingSettingMock = $this->getMock(PricingSetting::class, null, [], '', false);
		$productPriceRecalculationSchedulerMock = $this->getMock(ProductPriceRecalculationScheduler::class, null, [], '', false);

		$productService = new ProductService(
			$productPriceCalculationMock,
			$inputPriceCalculationMock,
			$basePriceCalculationMock,
			$pricingSettingMock,
			$productPriceRecalculationSchedulerMock
		);

		$productData = new ProductData();
		$mainVariant = new Product($productData);
		$variant = new Product($productData);
		$mainVariant->setVariants([$variant]);

		$this->assertNull($productService->delete($mainVariant)->getProductForRecalculations());
		$this->assertFalse($variant->isVariant());
	}

	public function testMarkProductForVisibilityRecalculation() {
		$productPriceCalculationMock = $this->getMock(ProductPriceCalculation::class, null, [], '', false);
		$inputPriceCalculationMock = $this->getMock(InputPriceCalculation::class, null, [], '', false);
		$basePriceCalculationMock = $this->getMock(BasePriceCalculation::class, null, [], '', false);
		$pricingSettingMock = $this->getMock(PricingSetting::class, null, [], '', false);
		$productPriceRecalculationSchedulerMock = $this->getMock(ProductPriceRecalculationScheduler::class, null, [], '', false);

		$productService = new ProductService(
			$productPriceCalculationMock,
			$inputPriceCalculationMock,
			$basePriceCalculationMock,
			$pricingSettingMock,
			$productPriceRecalculationSchedulerMock
		);

		$productMock = $this->getMock(
			Product::class,
			['markForVisibilityRecalculation', 'isMainVariant', 'isVariant'],
			[],
			'',
			false
		);
		$productMock->expects($this->atLeastOnce())->method('markForVisibilityRecalculation');
		$productMock->expects($this->any())->method('isMainVariant')->willReturn(false);
		$productMock->expects($this->atLeastOnce())->method('isVariant')->willReturn(false);

		$productService->markProductForVisibilityRecalculation($productMock);
	}

	public function testMarkProductForVisibilityRecalculationMainVariant() {
		$productPriceCalculationMock = $this->getMock(ProductPriceCalculation::class, null, [], '', false);
		$inputPriceCalculationMock = $this->getMock(InputPriceCalculation::class, null, [], '', false);
		$basePriceCalculationMock = $this->getMock(BasePriceCalculation::class, null, [], '', false);
		$pricingSettingMock = $this->getMock(PricingSetting::class, null, [], '', false);
		$productPriceRecalculationSchedulerMock = $this->getMock(ProductPriceRecalculationScheduler::class, null, [], '', false);

		$productService = new ProductService(
			$productPriceCalculationMock,
			$inputPriceCalculationMock,
			$basePriceCalculationMock,
			$pricingSettingMock,
			$productPriceRecalculationSchedulerMock
		);

		$variantMock = $this->getMock(Product::class, ['markForVisibilityRecalculation'], [], '', false);
		$variantMock->expects($this->atLeastOnce())->method('markForVisibilityRecalculation');

		$mainVariantMock = $this->getMock(
			Product::class,
			['markForVisibilityRecalculation', 'isMainVariant', 'isVariant', 'getVariants'],
			[],
			'',
			false
		);
		$mainVariantMock->expects($this->atLeastOnce())->method('markForVisibilityRecalculation');
		$mainVariantMock->expects($this->atLeastOnce())->method('isMainVariant')->willReturn(true);
		$mainVariantMock->expects($this->any())->method('isVariant')->willReturn(false);
		$mainVariantMock->expects($this->atLeastOnce())->method('getVariants')->willReturn([$variantMock]);

		$productService->markProductForVisibilityRecalculation($mainVariantMock);
	}

	public function testMarkProductForVisibilityRecalculationVariant() {
		$productPriceCalculationMock = $this->getMock(ProductPriceCalculation::class, null, [], '', false);
		$inputPriceCalculationMock = $this->getMock(InputPriceCalculation::class, null, [], '', false);
		$basePriceCalculationMock = $this->getMock(BasePriceCalculation::class, null, [], '', false);
		$pricingSettingMock = $this->getMock(PricingSetting::class, null, [], '', false);
		$productPriceRecalculationSchedulerMock = $this->getMock(ProductPriceRecalculationScheduler::class, null, [], '', false);

		$productService = new ProductService(
			$productPriceCalculationMock,
			$inputPriceCalculationMock,
			$basePriceCalculationMock,
			$pricingSettingMock,
			$productPriceRecalculationSchedulerMock
		);

		$mainVariantMock = $this->getMock(Product::class, ['markForVisibilityRecalculation'], [], '', false);
		$mainVariantMock->expects($this->once())->method('markForVisibilityRecalculation');

		$variantMock = $this->getMock(
			Product::class,
			['markForVisibilityRecalculation', 'isMainVariant', 'isVariant', 'getMainVariant'],
			[],
			'',
			false
		);
		$variantMock->expects($this->atLeastOnce())->method('markForVisibilityRecalculation');
		$variantMock->expects($this->any())->method('isMainVariant')->willReturn(false);
		$variantMock->expects($this->atLeastOnce())->method('isVariant')->willReturn(true);
		$variantMock->expects($this->atLeastOnce())->method('getMainVariant')->willReturn($mainVariantMock);

		$productService->markProductForVisibilityRecalculation($variantMock);
	}

}
