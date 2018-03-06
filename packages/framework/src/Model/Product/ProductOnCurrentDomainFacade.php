<?php

namespace Shopsys\FrameworkBundle\Model\Product;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Paginator\PaginationResult;
use Shopsys\FrameworkBundle\Model\Category\CategoryRepository;
use Shopsys\FrameworkBundle\Model\Customer\CurrentCustomer;
use Shopsys\FrameworkBundle\Model\Product\Accessory\ProductAccessoryRepository;
use Shopsys\FrameworkBundle\Model\Product\Brand\BrandRepository;
use Shopsys\FrameworkBundle\Model\Product\Detail\ProductDetailFactory;
use Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterConfig;
use Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterCountRepository;
use Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterData;
use Shopsys\FrameworkBundle\Model\Product\Listing\ProductListOrderingModeService;

class ProductOnCurrentDomainFacade
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductRepository
     */
    private $productRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Detail\ProductDetailFactory
     */
    private $productDetailFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\CurrentCustomer
     */
    private $currentCustomer;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Category\CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterCountRepository
     */
    private $productFilterCountRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Accessory\ProductAccessoryRepository
     */
    private $productAccessoryRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Brand\BrandRepository
     */
    private $brandRepository;

    public function __construct(
        ProductRepository $productRepository,
        Domain $domain,
        ProductDetailFactory $productDetailFactory,
        CurrentCustomer $currentCustomer,
        CategoryRepository $categoryRepository,
        ProductFilterCountRepository $productFilterCountRepository,
        ProductAccessoryRepository $productAccessoryRepository,
        BrandRepository $brandRepository
    ) {
        $this->productRepository = $productRepository;
        $this->domain = $domain;
        $this->currentCustomer = $currentCustomer;
        $this->productDetailFactory = $productDetailFactory;
        $this->categoryRepository = $categoryRepository;
        $this->productFilterCountRepository = $productFilterCountRepository;
        $this->productAccessoryRepository = $productAccessoryRepository;
        $this->brandRepository = $brandRepository;
    }

    /**
     * @param int $productId
     * @return \Shopsys\FrameworkBundle\Model\Product\Detail\ProductDetail
     */
    public function getVisibleProductDetailById($productId)
    {
        $product = $this->productRepository->getVisible(
            $productId,
            $this->domain->getId(),
            $this->currentCustomer->getPricingGroup()
        );

        return $this->productDetailFactory->getDetailForProduct($product);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @return \Shopsys\FrameworkBundle\Model\Product\Detail\ProductDetail[]
     */
    public function getAccessoriesProductDetailsForProduct(Product $product)
    {
        $accessories = $this->productAccessoryRepository->getAllOfferedAccessoriesByProduct(
            $product,
            $this->domain->getId(),
            $this->currentCustomer->getPricingGroup()
        );

        return $this->productDetailFactory->getDetailsForProducts($accessories);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @return \Shopsys\FrameworkBundle\Model\Product\Detail\ProductDetail[]
     */
    public function getVariantsProductDetailsForProduct(Product $product)
    {
        $variants = $this->productRepository->getAllSellableVariantsByMainVariant(
            $product,
            $this->domain->getId(),
            $this->currentCustomer->getPricingGroup()
        );

        return $this->productDetailFactory->getDetailsForProducts($variants);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterData $productFilterData
     * @param string $orderingModeId
     * @param int $page
     * @param int $limit
     * @param int $categoryId
     * @return \Shopsys\FrameworkBundle\Component\Paginator\PaginationResult
     */
    public function getPaginatedProductDetailsInCategory(
        ProductFilterData $productFilterData,
        $orderingModeId,
        $page,
        $limit,
        $categoryId
    ) {
        $category = $this->categoryRepository->getById($categoryId);

        $paginationResult = $this->productRepository->getPaginationResultForListableInCategory(
            $category,
            $this->domain->getId(),
            $this->domain->getLocale(),
            $productFilterData,
            $orderingModeId,
            $this->currentCustomer->getPricingGroup(),
            $page,
            $limit
        );
        $products = $paginationResult->getResults();

        return new PaginationResult(
            $paginationResult->getPage(),
            $paginationResult->getPageSize(),
            $paginationResult->getTotalCount(),
            $this->productDetailFactory->getDetailsForProducts($products)
        );
    }

    /**
     * @param string $orderingModeId
     * @param int $page
     * @param int $limit
     * @param int $brandId
     * @return \Shopsys\FrameworkBundle\Component\Paginator\PaginationResult
     */
    public function getPaginatedProductDetailsForBrand(
        $orderingModeId,
        $page,
        $limit,
        $brandId
    ) {
        $brand = $this->brandRepository->getById($brandId);

        $paginationResult = $this->productRepository->getPaginationResultForListableForBrand(
            $brand,
            $this->domain->getId(),
            $this->domain->getLocale(),
            $orderingModeId,
            $this->currentCustomer->getPricingGroup(),
            $page,
            $limit
        );
        $products = $paginationResult->getResults();

        return new PaginationResult(
            $paginationResult->getPage(),
            $paginationResult->getPageSize(),
            $paginationResult->getTotalCount(),
            $this->productDetailFactory->getDetailsForProducts($products)
        );
    }

    /**
     * @param string|null $searchText
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterData $productFilterData
     * @param string $orderingModeId
     * @param int $page
     * @param int $limit
     * @return \Shopsys\FrameworkBundle\Component\Paginator\PaginationResult
     */
    public function getPaginatedProductDetailsForSearch(
        $searchText,
        ProductFilterData $productFilterData,
        $orderingModeId,
        $page,
        $limit
    ) {
        $paginationResult = $this->productRepository->getPaginationResultForSearchListable(
            $searchText,
            $this->domain->getId(),
            $this->domain->getLocale(),
            $productFilterData,
            $orderingModeId,
            $this->currentCustomer->getPricingGroup(),
            $page,
            $limit
        );
        $products = $paginationResult->getResults();

        return new PaginationResult(
            $paginationResult->getPage(),
            $paginationResult->getPageSize(),
            $paginationResult->getTotalCount(),
            $this->productDetailFactory->getDetailsForProducts($products)
        );
    }

    /**
     * @param string|null $searchText
     * @param int $limit
     * @return \Shopsys\FrameworkBundle\Component\Paginator\PaginationResult
     */
    public function getSearchAutocompleteProducts($searchText, $limit)
    {
        $emptyProductFilterData = new ProductFilterData();

        $page = 1;

        $paginationResult = $this->productRepository->getPaginationResultForSearchListable(
            $searchText,
            $this->domain->getId(),
            $this->domain->getLocale(),
            $emptyProductFilterData,
            ProductListOrderingModeService::ORDER_BY_RELEVANCE,
            $this->currentCustomer->getPricingGroup(),
            $page,
            $limit
        );

        return $paginationResult;
    }

    /**
     * @param int $categoryId
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterConfig $productFilterConfig
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterData $productFilterData
     * @return \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterCountData
     */
    public function getProductFilterCountDataInCategory(
        $categoryId,
        ProductFilterConfig $productFilterConfig,
        ProductFilterData $productFilterData
    ) {
        $productsQueryBuilder = $this->productRepository->getListableInCategoryQueryBuilder(
            $this->domain->getId(),
            $this->currentCustomer->getPricingGroup(),
            $this->categoryRepository->getById($categoryId)
        );

        return $this->productFilterCountRepository->getProductFilterCountData(
            $productsQueryBuilder,
            $this->domain->getLocale(),
            $productFilterConfig,
            $productFilterData,
            $this->currentCustomer->getPricingGroup()
        );
    }

    /**
     * @param string|null $searchText
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterConfig $productFilterConfig
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterData $productFilterData
     * @return \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterCountData
     */
    public function getProductFilterCountDataForSearch(
        $searchText,
        ProductFilterConfig $productFilterConfig,
        ProductFilterData $productFilterData
    ) {
        $productsQueryBuilder = $this->productRepository->getListableBySearchTextQueryBuilder(
            $this->domain->getId(),
            $this->currentCustomer->getPricingGroup(),
            $this->domain->getLocale(),
            $searchText
        );

        return $this->productFilterCountRepository->getProductFilterCountData(
            $productsQueryBuilder,
            $this->domain->getLocale(),
            $productFilterConfig,
            $productFilterData,
            $this->currentCustomer->getPricingGroup()
        );
    }
}
