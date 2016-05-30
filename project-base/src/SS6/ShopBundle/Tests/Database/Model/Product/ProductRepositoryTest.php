<?php

namespace SS6\ShopBundle\Tests\Database\Model\Product;

use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\DataFixtures\Base\PricingGroupDataFixture;
use SS6\ShopBundle\DataFixtures\Demo\CategoryDataFixture;
use SS6\ShopBundle\DataFixtures\Demo\ProductDataFixture;
use SS6\ShopBundle\Model\Category\Category;
use SS6\ShopBundle\Model\Product\Filter\ProductFilterData;
use SS6\ShopBundle\Model\Product\Listing\ProductListOrderingModeService;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductEditDataFactory;
use SS6\ShopBundle\Model\Product\ProductEditFacade;
use SS6\ShopBundle\Model\Product\ProductRepository;
use SS6\ShopBundle\Tests\Test\DatabaseTestCase;

/**
 * @UglyTest
 */
class ProductRepositoryTest extends DatabaseTestCase{

	public function testVisibleAndNotSellingDeniedProductIsListed() {
		$this->getAllListableQueryBuilderTest(1, true);
	}

	public function testVisibleAndSellingDeniedProductIsNotListed() {
		$this->getAllListableQueryBuilderTest(6, false);
	}

	public function testProductVariantIsNotListed() {
		$this->getAllListableQueryBuilderTest(53, false);
	}

	public function testProductMainVariantIsListed() {
		$this->getAllListableQueryBuilderTest(148, true);
	}

	private function getAllListableQueryBuilderTest($productReferenceId, $isExpectedInResult) {
		$productRepository = $this->getContainer()->get(ProductRepository::class);
		/* @var $productRepository \SS6\ShopBundle\Model\Product\ProductRepository */
		$pricingGroup = $this->getReference(PricingGroupDataFixture::ORDINARY_DOMAIN_1);
		/* @var $pricingGroup \SS6\ShopBundle\Model\Pricing\Group\PricingGroup */

		$domain = $this->getContainer()->get(Domain::class);
		/* @var $domain \SS6\ShopBundle\Component\Domain\Domain */

		$product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . $productReferenceId);
		$productId = $product->getId();

		$queryBuilder = $productRepository->getAllListableQueryBuilder($domain->getId(), $pricingGroup);
		$queryBuilder->andWhere('p.id = :id')
			->setParameter('id', $productId);
		$result = $queryBuilder->getQuery()->execute();

		$this->assertSame(in_array($product, $result, true), $isExpectedInResult);
	}

	public function testVisibleAndNotSellingDeniedProductIsSellable() {
		$this->getAllSellableQueryBuilderTest(1, true);
	}

	public function testVisibleAndSellingDeniedProductIsNotSellable() {
		$this->getAllSellableQueryBuilderTest(6, false);
	}

	public function testProductVariantIsSellable() {
		$this->getAllSellableQueryBuilderTest(53, true);
	}

	public function testProductMainVariantIsNotSellable() {
		$this->getAllSellableQueryBuilderTest(148, false);
	}

	private function getAllSellableQueryBuilderTest($productReferenceId, $isExpectedInResult) {
		$productRepository = $this->getContainer()->get(ProductRepository::class);
		/* @var $productRepository \SS6\ShopBundle\Model\Product\ProductRepository */
		$pricingGroup = $this->getReference(PricingGroupDataFixture::ORDINARY_DOMAIN_1);
		/* @var $pricingGroup \SS6\ShopBundle\Model\Pricing\Group\PricingGroup */

		$domain = $this->getContainer()->get(Domain::class);
		/* @var $domain \SS6\ShopBundle\Component\Domain\Domain */

		$product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . $productReferenceId);
		$productId = $product->getId();

		$queryBuilder = $productRepository->getAllSellableQueryBuilder($domain->getId(), $pricingGroup);
		$queryBuilder->andWhere('p.id = :id')
			->setParameter('id', $productId);
		$result = $queryBuilder->getQuery()->execute();

		$this->assertSame(in_array($product, $result, true), $isExpectedInResult);
	}

	public function testVisibleAndNotSellingDeniedProductIsOfferred() {
		$this->getAllOfferedQueryBuilderTest(1, true);
	}

	public function testVisibleAndSellingDeniedProductIsNotOfferred() {
		$this->getAllOfferedQueryBuilderTest(6, false);
	}

	public function testProductVariantIsOfferred() {
		$this->getAllOfferedQueryBuilderTest(53, true);
	}

	public function testProductMainVariantIsOfferred() {
		$this->getAllOfferedQueryBuilderTest(69, true);
	}

	private function getAllOfferedQueryBuilderTest($productReferenceId, $isExpectedInResult) {
		$productRepository = $this->getContainer()->get(ProductRepository::class);
		/* @var $productRepository \SS6\ShopBundle\Model\Product\ProductRepository */
		$pricingGroup = $this->getReference(PricingGroupDataFixture::ORDINARY_DOMAIN_1);
		/* @var $pricingGroup \SS6\ShopBundle\Model\Pricing\Group\PricingGroup */

		$domain = $this->getContainer()->get(Domain::class);
		/* @var $domain \SS6\ShopBundle\Component\Domain\Domain */

		$product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . $productReferenceId);
		$productId = $product->getId();

		$queryBuilder = $productRepository->getAllOfferedQueryBuilder($domain->getId(), $pricingGroup);
		$queryBuilder->andWhere('p.id = :id')
			->setParameter('id', $productId);
		$result = $queryBuilder->getQuery()->execute();

		$this->assertSame(in_array($product, $result, true), $isExpectedInResult);
	}

	public function testOrderingByProductPriorityInCategory() {
		$category = $this->getReference(CategoryDataFixture::PREFIX . CategoryDataFixture::FOOD);
		/* @var $category \SS6\ShopBundle\DataFixtures\Demo\CategoryDataFixture */
		$product1 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . 70);
		$product2 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . 71);

		$this->setProductOrderingPriority($product1, 0);
		$this->setProductOrderingPriority($product2, -1);

		$results = $this->getProductsInCategoryOrderedByPriority($category);
		$this->assertSame($product1, $results[0]);
		$this->assertSame($product2, $results[1]);

		$this->setProductOrderingPriority($product2, 1);

		$results = $this->getProductsInCategoryOrderedByPriority($category);
		$this->assertSame($product2, $results[0]);
		$this->assertSame($product1, $results[1]);
	}

	public function testOrderingByProductPriorityInSearch() {
		$product1 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . 1);
		$product2 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . 45);

		$this->setProductOrderingPriority($product1, 0);
		$this->setProductOrderingPriority($product2, 1);

		$results = $this->getProductsForSearchOrderedByPriority('sencor');
		$this->assertSame($product2, $results[0]);
		$this->assertSame($product1, $results[1]);

		$this->setProductOrderingPriority($product2, -1);

		$results = $this->getProductsForSearchOrderedByPriority('sencor');
		$this->assertSame($product1, $results[0]);
		$this->assertSame($product2, $results[1]);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param int $priority
	 */
	private function setProductOrderingPriority(Product $product, $priority) {
		$productEditDataFactory = $this->getContainer()->get(ProductEditDataFactory::class);
		/* @var $productEditDataFactory \SS6\ShopBundle\Model\Product\ProductEditDataFactory */
		$productEditFacade = $this->getContainer()->get(ProductEditFacade::class);
		/* @var $productEditFacade \SS6\ShopBundle\Model\Product\ProductEditFacade */

		$productEditData = $productEditDataFactory->createFromProduct($product);
		$productEditData->productData->orderingPriority = $priority;
		$productEditFacade->edit($product->getId(), $productEditData);
	}

	/**
	 * @param string $searchText
	 * @return \SS6\ShopBundle\Model\Product\Product[]
	 */
	private function getProductsForSearchOrderedByPriority($searchText) {
		$productRepository = $this->getContainer()->get(ProductRepository::class);
		/* @var $productRepository \SS6\ShopBundle\Model\Product\ProductRepository */
		$pricingGroup = $this->getReference(PricingGroupDataFixture::ORDINARY_DOMAIN_1);
		/* @var $pricingGroup \SS6\ShopBundle\Model\Pricing\Group\PricingGroup */

		$paginationResult = $productRepository->getPaginationResultForSearchListable(
			$searchText,
			1,
			'cs',
			new ProductFilterData(),
			ProductListOrderingModeService::ORDER_BY_PRIORITY,
			$pricingGroup,
			1,
			PHP_INT_MAX
		);

		return $paginationResult->getResults();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Category\Category $category
	 * @return \SS6\ShopBundle\Model\Product\Product[]
	 */
	private function getProductsInCategoryOrderedByPriority(Category $category) {
		$productRepository = $this->getContainer()->get(ProductRepository::class);
		/* @var $productRepository \SS6\ShopBundle\Model\Product\ProductRepository */
		$pricingGroup = $this->getReference(PricingGroupDataFixture::ORDINARY_DOMAIN_1);
		/* @var $pricingGroup \SS6\ShopBundle\Model\Pricing\Group\PricingGroup */

		$paginationResult = $productRepository->getPaginationResultForListableInCategory(
			$category,
			1,
			'cs',
			new ProductFilterData(),
			ProductListOrderingModeService::ORDER_BY_PRIORITY,
			$pricingGroup,
			1,
			PHP_INT_MAX
		);

		return $paginationResult->getResults();
	}
}
