<?php

namespace SS6\ShopBundle\Tests\Database\Model\Product;

use SS6\ShopBundle\DataFixtures\Demo\ProductDataFixture;
use SS6\ShopBundle\Model\Product\ProductEditDataFactory;
use SS6\ShopBundle\Model\Product\ProductEditFacade;
use SS6\ShopBundle\Model\Product\ProductSellingDeniedRecalculator;
use SS6\ShopBundle\Tests\Test\DatabaseTestCase;

/**
 * @UglyTest
 */
class ProductSellingDeniedRecalculatorTest extends DatabaseTestCase {

	public function testCalculateSellingDeniedForProductSellableVariant() {
		$em = $this->getEntityManager();
		$productSellingDeniedRecalculator = $this->getContainer()->get(ProductSellingDeniedRecalculator::class);
		/* @var $productSellingDeniedRecalculator \SS6\ShopBundle\Model\Product\ProductSellingDeniedRecalculator */
		$productEditFacade = $this->getContainer()->get(ProductEditFacade::class);
		/* @var $productEditFacade \SS6\ShopBundle\Model\Product\ProductEditFacade */
		$productEditDataFactory = $this->getContainer()->get(ProductEditDataFactory::class);
		/* @var $productEditDataFactory \SS6\ShopBundle\Model\Product\ProductEditDataFactory */

		$variant1 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '53');
		/* @var $variant1 \SS6\ShopBundle\Model\Product\Product */
		$variant2 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '54');
		/* @var $variant2 \SS6\ShopBundle\Model\Product\Product */
		$variant3 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '69');
		/* @var $variant3 \SS6\ShopBundle\Model\Product\Product */
		$mainVariant = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '148');
		/* @var $mainVariant \SS6\ShopBundle\Model\Product\Product */

		$variant1ProductEditData = $productEditDataFactory->createFromProduct($variant1);
		$variant1ProductEditData->productData->sellingDenied = true;
		$productEditFacade->edit($variant1->getId(), $variant1ProductEditData);

		$productSellingDeniedRecalculator->calculateSellingDeniedForProduct($variant1);

		$em->refresh($variant1);
		$em->refresh($variant2);
		$em->refresh($variant3);
		$em->refresh($mainVariant);

		$this->assertTrue($variant1->getCalculatedSellingDenied());
		$this->assertFalse($variant2->getCalculatedSellingDenied());
		$this->assertFalse($variant3->getCalculatedSellingDenied());
		$this->assertFalse($mainVariant->getCalculatedSellingDenied());
	}

	public function testCalculateSellingDeniedForProductNotSellableVariants() {
		$em = $this->getEntityManager();
		$productSellingDeniedRecalculator = $this->getContainer()->get(ProductSellingDeniedRecalculator::class);
		/* @var $productSellingDeniedRecalculator \SS6\ShopBundle\Model\Product\ProductSellingDeniedRecalculator */
		$productEditFacade = $this->getContainer()->get(ProductEditFacade::class);
		/* @var $productEditFacade \SS6\ShopBundle\Model\Product\ProductEditFacade */
		$productEditDataFactory = $this->getContainer()->get(ProductEditDataFactory::class);
		/* @var $productEditDataFactory \SS6\ShopBundle\Model\Product\ProductEditDataFactory */

		$variant1 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '53');
		/* @var $variant1 \SS6\ShopBundle\Model\Product\Product */
		$variant2 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '54');
		/* @var $variant2 \SS6\ShopBundle\Model\Product\Product */
		$variant3 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '69');
		/* @var $variant2 \SS6\ShopBundle\Model\Product\Product */
		$mainVariant = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '148');
		/* @var $variant2 \SS6\ShopBundle\Model\Product\Product */

		$variant1ProductEditData = $productEditDataFactory->createFromProduct($variant1);
		$variant1ProductEditData->productData->sellingDenied = true;
		$productEditFacade->edit($variant1->getId(), $variant1ProductEditData);
		$variant2ProductEditData = $productEditDataFactory->createFromProduct($variant2);
		$variant2ProductEditData->productData->sellingDenied = true;
		$productEditFacade->edit($variant2->getId(), $variant2ProductEditData);
		$variant3ProductEditData = $productEditDataFactory->createFromProduct($variant3);
		$variant3ProductEditData->productData->sellingDenied = true;
		$productEditFacade->edit($variant3->getId(), $variant3ProductEditData);

		$productSellingDeniedRecalculator->calculateSellingDeniedForProduct($mainVariant);

		$em->refresh($variant1);
		$em->refresh($variant2);
		$em->refresh($variant3);
		$em->refresh($mainVariant);

		$this->assertTrue($variant1->getCalculatedSellingDenied());
		$this->assertTrue($variant2->getCalculatedSellingDenied());
		$this->assertTrue($variant3->getCalculatedSellingDenied());
		$this->assertTrue($mainVariant->getCalculatedSellingDenied());
	}

	public function testCalculateSellingDeniedForProductNotSellableMainVariant() {
		$em = $this->getEntityManager();
		$productSellingDeniedRecalculator = $this->getContainer()->get(ProductSellingDeniedRecalculator::class);
		/* @var $productSellingDeniedRecalculator \SS6\ShopBundle\Model\Product\ProductSellingDeniedRecalculator */
		$productEditFacade = $this->getContainer()->get(ProductEditFacade::class);
		/* @var $productEditFacade \SS6\ShopBundle\Model\Product\ProductEditFacade */
		$productEditDataFactory = $this->getContainer()->get(ProductEditDataFactory::class);
		/* @var $productEditDataFactory \SS6\ShopBundle\Model\Product\ProductEditDataFactory */

		$variant1 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '53');
		/* @var $variant1 \SS6\ShopBundle\Model\Product\Product */
		$variant2 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '54');
		/* @var $variant2 \SS6\ShopBundle\Model\Product\Product */
		$variant3 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '69');
		/* @var $variant3 \SS6\ShopBundle\Model\Product\Product */
		$mainVariant = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '148');
		/* @var $mainVariant \SS6\ShopBundle\Model\Product\Product */

		$mainVariantProductEditData = $productEditDataFactory->createFromProduct($mainVariant);
		$mainVariantProductEditData->productData->sellingDenied = true;
		$productEditFacade->edit($mainVariant->getId(), $mainVariantProductEditData);

		$productSellingDeniedRecalculator->calculateSellingDeniedForProduct($mainVariant);

		$em->refresh($variant1);
		$em->refresh($variant2);
		$em->refresh($variant3);
		$em->refresh($mainVariant);

		$this->assertTrue($variant1->getCalculatedSellingDenied());
		$this->assertTrue($variant2->getCalculatedSellingDenied());
		$this->assertTrue($variant3->getCalculatedSellingDenied());
		$this->assertTrue($mainVariant->getCalculatedSellingDenied());
	}
}
