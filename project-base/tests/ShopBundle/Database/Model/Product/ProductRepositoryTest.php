<?php

namespace Tests\ShopBundle\Database\Model\Product;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\DataFixtures\Demo\CategoryDataFixture;
use Shopsys\FrameworkBundle\DataFixtures\Demo\PricingGroupDataFixture;
use Shopsys\FrameworkBundle\DataFixtures\Demo\ProductDataFixture;
use Shopsys\FrameworkBundle\Model\Category\Category;
use Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterData;
use Shopsys\FrameworkBundle\Model\Product\Listing\ProductListOrderingModeService;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductDataFactory;
use Shopsys\FrameworkBundle\Model\Product\ProductFacade;
use Shopsys\FrameworkBundle\Model\Product\ProductRepository;
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
        /* @var $productRepository \Shopsys\FrameworkBundle\Model\Product\ProductRepository */
        $pricingGroup = $this->getReference(PricingGroupDataFixture::PRICING_GROUP_ORDINARY_DOMAIN_1);
        /* @var $pricingGroup \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup */

        $domain = $this->getContainer()->get(Domain::class);
        /* @var $domain \Shopsys\FrameworkBundle\Component\Domain\Domain */

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
        /* @var $productRepository \Shopsys\FrameworkBundle\Model\Product\ProductRepository */
        $pricingGroup = $this->getReference(PricingGroupDataFixture::PRICING_GROUP_ORDINARY_DOMAIN_1);
        /* @var $pricingGroup \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup */

        $domain = $this->getContainer()->get(Domain::class);
        /* @var $domain \Shopsys\FrameworkBundle\Component\Domain\Domain */

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
        /* @var $productRepository \Shopsys\FrameworkBundle\Model\Product\ProductRepository */
        $pricingGroup = $this->getReference(PricingGroupDataFixture::PRICING_GROUP_ORDINARY_DOMAIN_1);
        /* @var $pricingGroup \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup */

        $domain = $this->getContainer()->get(Domain::class);
        /* @var $domain \Shopsys\FrameworkBundle\Component\Domain\Domain */

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
        $category = $this->getReference(CategoryDataFixture::CATEGORY_FOOD);
        /* @var $category \Shopsys\FrameworkBundle\DataFixtures\Demo\CategoryDataFixture */
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

        $this->setProductOrderingPriority($product1, 2);
        $this->setProductOrderingPriority($product2, 3);

        $results = $this->getProductsForSearchOrderedByPriority('sencor');
        $this->assertSame($product2, $results[0]);
        $this->assertSame($product1, $results[1]);

        $this->setProductOrderingPriority($product2, 1);

        $results = $this->getProductsForSearchOrderedByPriority('sencor');
        $this->assertSame($product1, $results[0]);
        $this->assertSame($product2, $results[1]);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param int $priority
     */
    private function setProductOrderingPriority(Product $product, $priority)
    {
        $productDataFactory = $this->getContainer()->get(ProductDataFactory::class);
        /* @var $productDataFactory \Shopsys\FrameworkBundle\Model\Product\ProductDataFactory */
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /* @var $productFacade \Shopsys\FrameworkBundle\Model\Product\ProductFacade */

        $productData = $productDataFactory->createFromProduct($product);
        $productData->orderingPriority = $priority;
        $productFacade->edit($product->getId(), $productData);
    }

    /**
     * @param string $searchText
     * @return \Shopsys\FrameworkBundle\Model\Product\Product[]
     */
    private function getProductsForSearchOrderedByPriority($searchText)
    {
        $productRepository = $this->getContainer()->get(ProductRepository::class);
        /* @var $productRepository \Shopsys\FrameworkBundle\Model\Product\ProductRepository */
        $pricingGroup = $this->getReference(PricingGroupDataFixture::PRICING_GROUP_ORDINARY_DOMAIN_1);
        /* @var $pricingGroup \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup */

        $paginationResult = $productRepository->getPaginationResultForSearchListable(
            $searchText,
            1,
            'en',
            new ProductFilterData(),
            ProductListOrderingModeService::ORDER_BY_PRIORITY,
            $pricingGroup,
            1,
            PHP_INT_MAX
        );

        return $paginationResult->getResults();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\Category $category
     * @return \Shopsys\FrameworkBundle\Model\Product\Product[]
     */
    private function getProductsInCategoryOrderedByPriority(Category $category)
    {
        $productRepository = $this->getContainer()->get(ProductRepository::class);
        /* @var $productRepository \Shopsys\FrameworkBundle\Model\Product\ProductRepository */
        $pricingGroup = $this->getReference(PricingGroupDataFixture::PRICING_GROUP_ORDINARY_DOMAIN_1);
        /* @var $pricingGroup \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup */

        $paginationResult = $productRepository->getPaginationResultForListableInCategory(
            $category,
            1,
            'en',
            new ProductFilterData(),
            ProductListOrderingModeService::ORDER_BY_PRIORITY,
            $pricingGroup,
            1,
            PHP_INT_MAX
        );

        return $paginationResult->getResults();
    }
}
