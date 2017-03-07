<?php

namespace Tests\ShopBundle\Database\Model\Product;

use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\DataFixtures\Base\PricingGroupDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\CategoryDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\ProductDataFixture;
use Shopsys\ShopBundle\Model\Category\Category;
use Shopsys\ShopBundle\Model\Product\Filter\ProductFilterData;
use Shopsys\ShopBundle\Model\Product\Listing\ProductListOrderingModeService;
use Shopsys\ShopBundle\Model\Product\Product;
use Shopsys\ShopBundle\Model\Product\ProductEditDataFactory;
use Shopsys\ShopBundle\Model\Product\ProductFacade;
use Shopsys\ShopBundle\Model\Product\ProductRepository;
use Tests\ShopBundle\Test\DatabaseTestCase;

class ProductRepositoryTest extends DatabaseTestCase
{
    public function testVisibleAndNotSellingDeniedProductIsListed()
    {
        $this->getAllListableQueryBuilderTest(1, true);
    }

    public function testVisibleAndSellingDeniedProductIsNotListed()
    {
        $this->getAllListableQueryBuilderTest(6, false);
    }

    public function testProductVariantIsNotListed()
    {
        $this->getAllListableQueryBuilderTest(53, false);
    }

    public function testProductMainVariantIsListed()
    {
        $this->getAllListableQueryBuilderTest(148, true);
    }

    private function getAllListableQueryBuilderTest($productReferenceId, $isExpectedInResult)
    {
        $productRepository = $this->getContainer()->get(ProductRepository::class);
        /* @var $productRepository \Shopsys\ShopBundle\Model\Product\ProductRepository */
        $pricingGroup = $this->getReference(PricingGroupDataFixture::ORDINARY_DOMAIN_1);
        /* @var $pricingGroup \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup */

        $domain = $this->getContainer()->get(Domain::class);
        /* @var $domain \Shopsys\ShopBundle\Component\Domain\Domain */

        $product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . $productReferenceId);
        $productId = $product->getId();

        $queryBuilder = $productRepository->getAllListableQueryBuilder($domain->getId(), $pricingGroup);
        $queryBuilder->andWhere('p.id = :id')
            ->setParameter('id', $productId);
        $result = $queryBuilder->getQuery()->execute();

        $this->assertSame(in_array($product, $result, true), $isExpectedInResult);
    }

    public function testVisibleAndNotSellingDeniedProductIsSellable()
    {
        $this->getAllSellableQueryBuilderTest(1, true);
    }

    public function testVisibleAndSellingDeniedProductIsNotSellable()
    {
        $this->getAllSellableQueryBuilderTest(6, false);
    }

    public function testProductVariantIsSellable()
    {
        $this->getAllSellableQueryBuilderTest(53, true);
    }

    public function testProductMainVariantIsNotSellable()
    {
        $this->getAllSellableQueryBuilderTest(148, false);
    }

    private function getAllSellableQueryBuilderTest($productReferenceId, $isExpectedInResult)
    {
        $productRepository = $this->getContainer()->get(ProductRepository::class);
        /* @var $productRepository \Shopsys\ShopBundle\Model\Product\ProductRepository */
        $pricingGroup = $this->getReference(PricingGroupDataFixture::ORDINARY_DOMAIN_1);
        /* @var $pricingGroup \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup */

        $domain = $this->getContainer()->get(Domain::class);
        /* @var $domain \Shopsys\ShopBundle\Component\Domain\Domain */

        $product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . $productReferenceId);
        $productId = $product->getId();

        $queryBuilder = $productRepository->getAllSellableQueryBuilder($domain->getId(), $pricingGroup);
        $queryBuilder->andWhere('p.id = :id')
            ->setParameter('id', $productId);
        $result = $queryBuilder->getQuery()->execute();

        $this->assertSame(in_array($product, $result, true), $isExpectedInResult);
    }

    public function testVisibleAndNotSellingDeniedProductIsOfferred()
    {
        $this->getAllOfferedQueryBuilderTest(1, true);
    }

    public function testVisibleAndSellingDeniedProductIsNotOfferred()
    {
        $this->getAllOfferedQueryBuilderTest(6, false);
    }

    public function testProductVariantIsOfferred()
    {
        $this->getAllOfferedQueryBuilderTest(53, true);
    }

    public function testProductMainVariantIsOfferred()
    {
        $this->getAllOfferedQueryBuilderTest(69, true);
    }

    private function getAllOfferedQueryBuilderTest($productReferenceId, $isExpectedInResult)
    {
        $productRepository = $this->getContainer()->get(ProductRepository::class);
        /* @var $productRepository \Shopsys\ShopBundle\Model\Product\ProductRepository */
        $pricingGroup = $this->getReference(PricingGroupDataFixture::ORDINARY_DOMAIN_1);
        /* @var $pricingGroup \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup */

        $domain = $this->getContainer()->get(Domain::class);
        /* @var $domain \Shopsys\ShopBundle\Component\Domain\Domain */

        $product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . $productReferenceId);
        $productId = $product->getId();

        $queryBuilder = $productRepository->getAllOfferedQueryBuilder($domain->getId(), $pricingGroup);
        $queryBuilder->andWhere('p.id = :id')
            ->setParameter('id', $productId);
        $result = $queryBuilder->getQuery()->execute();

        $this->assertSame(in_array($product, $result, true), $isExpectedInResult);
    }

    public function testOrderingByProductPriorityInCategory()
    {
        $category = $this->getReference(CategoryDataFixture::PREFIX . CategoryDataFixture::FOOD);
        /* @var $category \Shopsys\ShopBundle\DataFixtures\Demo\CategoryDataFixture */
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

    public function testOrderingByProductPriorityInSearch()
    {
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
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @param int $priority
     */
    private function setProductOrderingPriority(Product $product, $priority)
    {
        $productEditDataFactory = $this->getContainer()->get(ProductEditDataFactory::class);
        /* @var $productEditDataFactory \Shopsys\ShopBundle\Model\Product\ProductEditDataFactory */
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /* @var $productFacade \Shopsys\ShopBundle\Model\Product\ProductFacade */

        $productEditData = $productEditDataFactory->createFromProduct($product);
        $productEditData->productData->orderingPriority = $priority;
        $productFacade->edit($product->getId(), $productEditData);
    }

    /**
     * @param string $searchText
     * @return \Shopsys\ShopBundle\Model\Product\Product[]
     */
    private function getProductsForSearchOrderedByPriority($searchText)
    {
        $productRepository = $this->getContainer()->get(ProductRepository::class);
        /* @var $productRepository \Shopsys\ShopBundle\Model\Product\ProductRepository */
        $pricingGroup = $this->getReference(PricingGroupDataFixture::ORDINARY_DOMAIN_1);
        /* @var $pricingGroup \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup */

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
     * @param \Shopsys\ShopBundle\Model\Category\Category $category
     * @return \Shopsys\ShopBundle\Model\Product\Product[]
     */
    private function getProductsInCategoryOrderedByPriority(Category $category)
    {
        $productRepository = $this->getContainer()->get(ProductRepository::class);
        /* @var $productRepository \Shopsys\ShopBundle\Model\Product\ProductRepository */
        $pricingGroup = $this->getReference(PricingGroupDataFixture::ORDINARY_DOMAIN_1);
        /* @var $pricingGroup \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup */

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
