<?php

namespace SS6\ShopBundle\Tests\Database\Model\Product;

use DateTime;
use SS6\ShopBundle\DataFixtures\Base\PricingGroupDataFixture;
use SS6\ShopBundle\DataFixtures\Demo\CategoryDataFixture;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Pricing\Vat\VatData;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductEditData;
use SS6\ShopBundle\Model\Product\ProductVisibility;
use SS6\ShopBundle\Tests\Test\DatabaseTestCase;

class ProductVisibilityRepositoryTest extends DatabaseTestCase {

	/**
	 * @return \SS6\ShopBundle\Model\Product\ProductEditData
	 */
	private function getDefaultProductEditData() {
		$category = $this->getReference(CategoryDataFixture::ELECTRONICS);

		$em = $this->getEntityManager();
		$vat = new Vat(new VatData('vat', 21));
		$em->persist($vat);

		$productEditData = new ProductEditData();
		$productEditData->productData->name = ['cs' => 'Name'];
		$productEditData->productData->vat = $vat;
		$productEditData->productData->price = 100;
		$productEditData->productData->priceCalculationType = Product::PRICE_CALCULATION_TYPE_AUTO;
		$productEditData->productData->hidden = false;
		$productEditData->productData->sellable = true;
		$productEditData->productData->hiddenOnDomains = [];
		$productEditData->productData->categories = [$category];

		return $productEditData;
	}

	public function testIsVisibleOnAnyDomainWhenHidden() {
		$em = $this->getEntityManager();
		$productEditFacade = $this->getContainer()->get('ss6.shop.product.product_edit_facade');
		$productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);
		/* @var $productPriceRecalculator \SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator */

		$productEditData = $this->getDefaultProductEditData();
		$productEditData->productData->hidden = true;
		$product = $productEditFacade->create($productEditData);
		$productPriceRecalculator->runImmediatelyRecalculations();

		$em->flush();
		$id = $product->getId();
		$em->clear();

		$productVisibilityRepository = $this->getContainer()->get('ss6.shop.product.product_visibility_repository');
		/* @var $productVisibilityRepository \SS6\ShopBundle\Model\Product\ProductVisibilityRepository */
		$productVisibilityRepository->refreshProductsVisibility();

		$productAgain = $em->getRepository(Product::class)->find($id);
		/* @var $productAgain \SS6\ShopBundle\Model\Product\Product */

		$productVisibility1 = $em->getRepository(ProductVisibility::class)->findOneBy([
			'product' => $productAgain,
			'pricingGroup' => $this->getReference(PricingGroupDataFixture::ORDINARY_DOMAIN_1)->getId(),
			'domainId' => 1,
		]);
		/* @var $productVisibility1 \SS6\ShopBundle\Model\Product\ProductVisibility */

		$this->assertFalse($productAgain->isVisible());
		$this->assertFalse($productVisibility1->isVisible());
	}

	public function testIsVisibleOnAnyDomainWhenNotHidden() {
		$em = $this->getEntityManager();
		$productEditFacade = $this->getContainer()->get('ss6.shop.product.product_edit_facade');
		$productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);
		/* @var $productPriceRecalculator \SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator */

		$productEditData = $this->getDefaultProductEditData();
		$product = $productEditFacade->create($productEditData);
		$productPriceRecalculator->runImmediatelyRecalculations();

		$em->flush();
		$id = $product->getId();
		$em->clear();

		$productVisibilityRepository = $this->getContainer()->get('ss6.shop.product.product_visibility_repository');
		/* @var $productVisibilityRepository \SS6\ShopBundle\Model\Product\ProductVisibilityRepository */
		$productVisibilityRepository->refreshProductsVisibility();

		$productAgain = $em->getRepository(Product::class)->find($id);
		/* @var $productAgain \SS6\ShopBundle\Model\Product\Product */

		$productVisibility1 = $em->getRepository(ProductVisibility::class)->findOneBy([
			'product' => $productAgain->getId(),
			'pricingGroup' => $this->getReference(PricingGroupDataFixture::ORDINARY_DOMAIN_1)->getId(),
			'domainId' => 1,
		]);
		/* @var $productVisibility1 \SS6\ShopBundle\Model\Product\ProductVisibility */

		$this->assertTrue($productAgain->isVisible());
		$this->assertTrue($productVisibility1->isVisible());
	}

	public function testIsVisibleOnAnyDomainWhenSellingInFuture() {
		$em = $this->getEntityManager();
		$productEditFacade = $this->getContainer()->get('ss6.shop.product.product_edit_facade');
		$productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);
		/* @var $productPriceRecalculator \SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator */

		$sellingFrom = new DateTime('now');
		$sellingFrom->modify('+1 day');

		$productEditData = $this->getDefaultProductEditData();
		$productEditData->productData->sellingFrom = $sellingFrom;
		$product = $productEditFacade->create($productEditData);
		$productPriceRecalculator->runImmediatelyRecalculations();

		$em->flush();
		$id = $product->getId();
		$em->clear();

		$productVisibilityRepository = $this->getContainer()->get('ss6.shop.product.product_visibility_repository');
		/* @var $productVisibilityRepository \SS6\ShopBundle\Model\Product\ProductVisibilityRepository */
		$productVisibilityRepository->refreshProductsVisibility();

		$productAgain = $em->getRepository(Product::class)->find($id);
		/* @var $productAgain \SS6\ShopBundle\Model\Product\Product */

		$this->assertFalse($productAgain->isVisible());
	}

	public function testIsVisibleOnAnyDomainWhenSellingInPast() {
		$em = $this->getEntityManager();
		$productEditFacade = $this->getContainer()->get('ss6.shop.product.product_edit_facade');
		$productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);
		/* @var $productPriceRecalculator \SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator */

		$sellingTo = new DateTime('now');
		$sellingTo->modify('-1 day');

		$productEditData = $this->getDefaultProductEditData();
		$productEditData->productData->sellingTo = $sellingTo;
		$product = $productEditFacade->create($productEditData);
		$productPriceRecalculator->runImmediatelyRecalculations();

		$em->flush();
		$id = $product->getId();
		$em->clear();

		$productVisibilityRepository = $this->getContainer()->get('ss6.shop.product.product_visibility_repository');
		/* @var $productVisibilityRepository \SS6\ShopBundle\Model\Product\ProductVisibilityRepository */
		$productVisibilityRepository->refreshProductsVisibility();

		$productAgain = $em->getRepository(Product::class)->find($id);
		/* @var $productAgain \SS6\ShopBundle\Model\Product\Product */

		$this->assertFalse($productAgain->isVisible());
	}

	public function testIsVisibleOnAnyDomainWhenSellingNow() {
		$em = $this->getEntityManager();
		$productEditFacade = $this->getContainer()->get('ss6.shop.product.product_edit_facade');
		$productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);
		/* @var $productPriceRecalculator \SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator */

		$sellingFrom = new DateTime('now');
		$sellingFrom->modify('-1 day');
		$sellingTo = new DateTime('now');
		$sellingTo->modify('+1 day');

		$productEditData = $this->getDefaultProductEditData();
		$productEditData->productData->sellingFrom = $sellingFrom;
		$productEditData->productData->sellingTo = $sellingTo;
		$product = $productEditFacade->create($productEditData);
		$productPriceRecalculator->runImmediatelyRecalculations();

		$em->flush();
		$id = $product->getId();
		$em->clear();

		$productVisibilityRepository = $this->getContainer()->get('ss6.shop.product.product_visibility_repository');
		/* @var $productVisibilityRepository \SS6\ShopBundle\Model\Product\ProductVisibilityRepository */
		$productVisibilityRepository->refreshProductsVisibility();

		$productAgain = $em->getRepository(Product::class)->find($id);
		/* @var $productAgain \SS6\ShopBundle\Model\Product\Product */

		$this->assertTrue($productAgain->isVisible());
	}

	public function testIsNotVisibleWhenZeroOrNullPrice() {
		$em = $this->getEntityManager();
		$productEditFacade = $this->getContainer()->get('ss6.shop.product.product_edit_facade');
		$productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);
		/* @var $productPriceRecalculator \SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator */

		$productEditData = $this->getDefaultProductEditData();
		$productEditData->productData->price = 0;
		$product1 = $productEditFacade->create($productEditData);

		$productEditData->productData->price = null;
		$product2 = $productEditFacade->create($productEditData);
		$productPriceRecalculator->runImmediatelyRecalculations();

		$product1Id = $product1->getId();
		$product2Id = $product2->getId();
		$em->clear();

		$productVisibilityRepository = $this->getContainer()->get('ss6.shop.product.product_visibility_repository');
		/* @var $productVisibilityRepository \SS6\ShopBundle\Model\Product\ProductVisibilityRepository */
		$productVisibilityRepository->refreshProductsVisibility();

		$product1Again = $em->getRepository(Product::class)->find($product1Id);
		/* @var $product1Again \SS6\ShopBundle\Model\Product\Product */
		$product2Again = $em->getRepository(Product::class)->find($product2Id);
		/* @var $product2Again \SS6\ShopBundle\Model\Product\Product */

		$this->assertFalse($product1Again->isVisible());
		$this->assertFalse($product2Again->isVisible());
	}

	public function testIsVisibleWithEmptyName() {
		$em = $this->getEntityManager();
		$productEditFacade = $this->getContainer()->get('ss6.shop.product.product_edit_facade');
		/* @var $productEditFacade \SS6\ShopBundle\Model\Product\ProductEditFacade */
		$productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);
		/* @var $productPriceRecalculator \SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator */

		$productEditData = $this->getDefaultProductEditData();
		$productEditData->productData->name = ['cs' => 'Name'];
		$product = $productEditFacade->create($productEditData);
		$productPriceRecalculator->runImmediatelyRecalculations();

		$productId = $product->getId();
		$em->clear();

		$productVisibilityRepository = $this->getContainer()->get('ss6.shop.product.product_visibility_repository');
		/* @var $productVisibilityRepository \SS6\ShopBundle\Model\Product\ProductVisibilityRepository */
		$productVisibilityRepository->refreshProductsVisibility();

		$productFromDb = $em->getRepository(Product::class)->find($productId);
		/* @var $productFromDb \SS6\ShopBundle\Model\Product\Product */

		$productVisibility1 = $em->getRepository(ProductVisibility::class)->findOneBy([
			'product' => $productId,
			'pricingGroup' => $this->getReference(PricingGroupDataFixture::ORDINARY_DOMAIN_1)->getId(),
			'domainId' => 1,
		]);
		/* @var $productVisibility1 \SS6\ShopBundle\Model\Product\ProductVisibility */

		$productVisibility2 = $em->getRepository(ProductVisibility::class)->findOneBy([
			'product' => $productId,
			'pricingGroup' => $this->getReference(PricingGroupDataFixture::ORDINARY_DOMAIN_2)->getId(),
			'domainId' => 2,
		]);
		/* @var $productVisibility1 \SS6\ShopBundle\Model\Product\ProductVisibility */

		$this->assertTrue($productFromDb->isVisible());
		$this->assertTrue($productVisibility1->isVisible());
		$this->assertFalse($productVisibility2->isVisible());
	}

	public function testIsVisibleAccordingToVisibilityOfCategory() {
		$em = $this->getEntityManager();
		$productEditFacade = $this->getContainer()->get('ss6.shop.product.product_edit_facade');
		/* @var $productEditFacade \SS6\ShopBundle\Model\Product\ProductEditFacade */
		$productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);
		/* @var $productPriceRecalculator \SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator */

		$category = $this->getReference(CategoryDataFixture::TOYS);

		$productEditData = $this->getDefaultProductEditData();
		$productEditData->productData->categories = [$category];
		$product = $productEditFacade->create($productEditData);
		$productPriceRecalculator->runImmediatelyRecalculations();

		$productId = $product->getId();
		$em->clear();

		$productVisibilityRepository = $this->getContainer()->get('ss6.shop.product.product_visibility_repository');
		/* @var $productVisibilityRepository \SS6\ShopBundle\Model\Product\ProductVisibilityRepository */
		$productVisibilityRepository->refreshProductsVisibility();

		$productFromDb = $em->getRepository(Product::class)->find($productId);
		/* @var $productFromDb \SS6\ShopBundle\Model\Product\Product */

		$productVisibility1 = $em->getRepository(ProductVisibility::class)->findOneBy([
			'product' => $productId,
			'pricingGroup' => $this->getReference(PricingGroupDataFixture::ORDINARY_DOMAIN_1)->getId(),
			'domainId' => 1,
		]);
		/* @var $productVisibility1 \SS6\ShopBundle\Model\Product\ProductVisibility */

		$productVisibility2 = $em->getRepository(ProductVisibility::class)->findOneBy([
			'product' => $productId,
			'pricingGroup' => $this->getReference(PricingGroupDataFixture::ORDINARY_DOMAIN_2)->getId(),
			'domainId' => 2,
		]);
		/* @var $productVisibility1 \SS6\ShopBundle\Model\Product\ProductVisibility */

		$this->assertTrue($productFromDb->isVisible());
		$this->assertTrue($productVisibility1->isVisible());
		$this->assertFalse($productVisibility2->isVisible());
	}

	public function testIsNotVisibleWhenNullOrZeroManualPrice() {
		$em = $this->getEntityManager();
		$productEditFacade = $this->getContainer()->get('ss6.shop.product.product_edit_facade');
		/* @var $productEditFacade \SS6\ShopBundle\Model\Product\ProductEditFacade */
		$productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);
		/* @var $productPriceRecalculator \SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator */
		$pricingGroupFacade = $this->getContainer()->get(PricingGroupFacade::class);
		/* @var $pricingGroupFacade \SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade */

		$productEditData = $this->getDefaultProductEditData();
		$productEditData->productData->priceCalculationType = Product::PRICE_CALCULATION_TYPE_MANUAL;

		$allPricingGroups = $pricingGroupFacade->getAll();
		foreach ($allPricingGroups as $pricingGroup) {
			$productEditData->manualInputPrices[$pricingGroup->getId()] = 10;
		}

		$pricingGroupWithZeroPriceId = $this->getReference(PricingGroupDataFixture::ORDINARY_DOMAIN_1)->getId();
		$pricingGroupWithNullPriceId = $this->getReference(PricingGroupDataFixture::ORDINARY_DOMAIN_2)->getId();

		$productEditData->manualInputPrices[$pricingGroupWithZeroPriceId] = 0;
		$productEditData->manualInputPrices[$pricingGroupWithNullPriceId] = null;

		$product = $productEditFacade->create($productEditData);
		$productPriceRecalculator->runImmediatelyRecalculations();

		$productId = $product->getId();
		$em->clear();

		$productVisibilityRepository = $this->getContainer()->get('ss6.shop.product.product_visibility_repository');
		/* @var $productVisibilityRepository \SS6\ShopBundle\Model\Product\ProductVisibilityRepository */
		$productVisibilityRepository->refreshProductsVisibility();

		$productFromDb = $em->getRepository(Product::class)->find($productId);
		/* @var $productFromDb \SS6\ShopBundle\Model\Product\Product */

		$productVisibility1 = $em->getRepository(ProductVisibility::class)->findOneBy([
			'product' => $productId,
			'pricingGroup' => $pricingGroupWithZeroPriceId,
			'domainId' => 1,
		]);
		/* @var $productVisibility1 \SS6\ShopBundle\Model\Product\ProductVisibility */

		$productVisibility2 = $em->getRepository(ProductVisibility::class)->findOneBy([
			'product' => $productId,
			'pricingGroup' => $pricingGroupWithNullPriceId,
			'domainId' => 2,
		]);
		/* @var $productVisibility1 \SS6\ShopBundle\Model\Product\ProductVisibility */

		$this->assertTrue($productFromDb->isVisible());
		$this->assertFalse($productVisibility1->isVisible());
		$this->assertFalse($productVisibility2->isVisible());
	}

}
