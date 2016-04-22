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

class ProductRepositoryTest extends DatabaseTestCase{

	public function getAllListableQueryBuilderProvider() {
		return [
			[1, true, 'Visible and not selling denied product is not listed'],
			[6, false, 'Visible and selling denied product is listed'],
			[53, false, 'Product variant is listed'],
			[148, true, 'Product main variant is not listed'],
		];
	}

	/**
	 * @dataProvider getAllListableQueryBuilderProvider
	 */
	public function testGetAllListableQueryBuilder($productReferenceId, $isExpectedInResult, $failMessage) {
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

		$this->assertSame(in_array($product, $result, true), $isExpectedInResult, $failMessage);
	}

	public function getAllSellableQueryBuilderProvider() {
		return [
			[1, true, 'Visible and not selling denied product is not sellable'],
			[6, false, 'Visible and selling denied product is sellable'],
			[53, true, 'Product variant is not listed'],
			[148, false, 'Product main variant is listed'],
		];
	}

	/**
	 * @dataProvider getAllSellableQueryBuilderProvider
	 */
	public function testGetAllSellableQueryBuilder($productReferenceId, $isExpectedInResult, $failMessage) {
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

		$this->assertSame(in_array($product, $result, true), $isExpectedInResult, $failMessage);
	}

	public function getAllOfferedQueryBuilderProvider() {
		return [
			[1, true, 'Visible and not selling denied product is not offered'],
			[6, false, 'Visible and selling denied product is offered'],
			[53, true, 'Product variant is not offered'],
			[69, true, 'Product main variant is not offered'],
		];
	}

	/**
	 * @dataProvider getAllOfferedQueryBuilderProvider
	 */
	public function testGetAllOfferedQueryBuilder($productReferenceId, $isExpectedInResult, $failMessage) {
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

		$this->assertSame(in_array($product, $result, true), $isExpectedInResult, $failMessage);
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
