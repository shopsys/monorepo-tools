<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\Product;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Elasticsearch\ElasticsearchStructureManager;
use Shopsys\FrameworkBundle\Component\Paginator\PaginationResult;
use Shopsys\FrameworkBundle\Model\Customer\CurrentCustomer;
use Shopsys\FrameworkBundle\Model\Product\Accessory\ProductAccessoryRepository;
use Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterConfig;
use Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterCountData;
use Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterData;
use Shopsys\FrameworkBundle\Model\Product\Listing\ProductListOrderingConfig;
use Shopsys\FrameworkBundle\Model\Product\Search\FilterQuery;
use Shopsys\FrameworkBundle\Model\Product\Search\FilterQueryFactory;
use Shopsys\FrameworkBundle\Model\Product\Search\ProductElasticsearchRepository;
use Shopsys\FrameworkBundle\Model\Product\Search\ProductFilterCountDataElasticsearchRepository;
use Shopsys\FrameworkBundle\Model\Product\Search\ProductFilterDataToQueryTransformer;

class ProductOnCurrentDomainElasticFacade implements ProductOnCurrentDomainFacadeInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductRepository
     */
    protected $productRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Accessory\ProductAccessoryRepository
     */
    protected $productAccessoryRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Search\ProductElasticsearchRepository
     */
    protected $productElasticsearchRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Search\ProductFilterCountDataElasticsearchRepository
     */
    protected $productFilterCountDataElasticsearchRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Elasticsearch\ElasticsearchStructureManager
     */
    protected $elasticsearchStructureManager;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Search\ProductFilterDataToQueryTransformer
     */
    protected $productFilterDataToQueryTransformer;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Search\FilterQueryFactory
     */
    protected $filterQueryFactory;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductRepository $productRepository
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     * @param \Shopsys\FrameworkBundle\Model\Customer\CurrentCustomer $currentCustomer
     * @param \Shopsys\FrameworkBundle\Model\Product\Accessory\ProductAccessoryRepository $productAccessoryRepository
     * @param \Shopsys\FrameworkBundle\Model\Product\Search\ProductElasticsearchRepository $productElasticsearchRepository
     * @param \Shopsys\FrameworkBundle\Model\Product\Search\ProductFilterCountDataElasticsearchRepository $productFilterCountDataElasticsearchRepository
     * @param \Shopsys\FrameworkBundle\Component\Elasticsearch\ElasticsearchStructureManager $elasticsearchStructureManager
     * @param \Shopsys\FrameworkBundle\Model\Product\Search\ProductFilterDataToQueryTransformer $productFilterDataToQueryTransformer
     * @param \Shopsys\FrameworkBundle\Model\Product\Search\FilterQueryFactory $filterQueryFactory
     */
    public function __construct(
        ProductRepository $productRepository,
        Domain $domain,
        CurrentCustomer $currentCustomer,
        ProductAccessoryRepository $productAccessoryRepository,
        ProductElasticsearchRepository $productElasticsearchRepository,
        ProductFilterCountDataElasticsearchRepository $productFilterCountDataElasticsearchRepository,
        ElasticsearchStructureManager $elasticsearchStructureManager,
        ProductFilterDataToQueryTransformer $productFilterDataToQueryTransformer,
        FilterQueryFactory $filterQueryFactory
    ) {
        $this->productRepository = $productRepository;
        $this->domain = $domain;
        $this->currentCustomer = $currentCustomer;
        $this->productAccessoryRepository = $productAccessoryRepository;
        $this->productElasticsearchRepository = $productElasticsearchRepository;
        $this->productFilterCountDataElasticsearchRepository = $productFilterCountDataElasticsearchRepository;
        $this->elasticsearchStructureManager = $elasticsearchStructureManager;
        $this->productFilterDataToQueryTransformer = $productFilterDataToQueryTransformer;
        $this->filterQueryFactory = $filterQueryFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getVisibleProductById($productId): Product
    {
        return $this->productRepository->getVisible(
            $productId,
            $this->domain->getId(),
            $this->currentCustomer->getPricingGroup()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessoriesForProduct(Product $product): array
    {
        return $this->productAccessoryRepository->getAllOfferedAccessoriesByProduct(
            $product,
            $this->domain->getId(),
            $this->currentCustomer->getPricingGroup()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getVariantsForProduct(Product $product): array
    {
        return $this->productRepository->getAllSellableVariantsByMainVariant(
            $product,
            $this->domain->getId(),
            $this->currentCustomer->getPricingGroup()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPaginatedProductsInCategory(ProductFilterData $productFilterData, $orderingModeId, $page, $limit, $categoryId): PaginationResult
    {
        $filterQuery = $this->createProductsInCategoryFilterQuery($productFilterData, $orderingModeId, $page, $limit, $categoryId);

        $productIds = $this->productElasticsearchRepository->getSortedProductIdsByFilterQuery($filterQuery);

        $listableProductsByIds = $this->productRepository->getListableByIds($this->domain->getId(), $this->currentCustomer->getPricingGroup(), $productIds->getIds());

        return new PaginationResult($page, $limit, $productIds->getTotal(), $listableProductsByIds);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterData $productFilterData
     * @param string $orderingModeId
     * @param int $page
     * @param int $limit
     * @param int $categoryId
     * @return \Shopsys\FrameworkBundle\Model\Product\Search\FilterQuery
     */
    protected function createProductsInCategoryFilterQuery(ProductFilterData $productFilterData, $orderingModeId, $page, $limit, $categoryId): FilterQuery
    {
        return $this->createFilterQueryWithProductFilterData($productFilterData, $orderingModeId, $page, $limit)
            ->filterByCategory([$categoryId]);
    }

    /**
     * {@inheritdoc}
     */
    public function getPaginatedProductsForBrand($orderingModeId, $page, $limit, $brandId): PaginationResult
    {
        $emptyProductFilterData = new ProductFilterData();

        $filterQuery = $this->createProductsForBrandFilterQuery($emptyProductFilterData, $orderingModeId, $page, $limit, $brandId);

        $productIds = $this->productElasticsearchRepository->getSortedProductIdsByFilterQuery($filterQuery);

        $listableProductsByIds = $this->productRepository->getListableByIds($this->domain->getId(), $this->currentCustomer->getPricingGroup(), $productIds->getIds());

        return new PaginationResult($page, $limit, $productIds->getTotal(), $listableProductsByIds);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterData $productFilterData
     * @param string $orderingModeId
     * @param int $page
     * @param int $limit
     * @param int $brandId
     * @return \Shopsys\FrameworkBundle\Model\Product\Search\FilterQuery
     */
    protected function createProductsForBrandFilterQuery(ProductFilterData $productFilterData, $orderingModeId, $page, $limit, $brandId): FilterQuery
    {
        return $this->createFilterQueryWithProductFilterData($productFilterData, $orderingModeId, $page, $limit)
            ->filterByBrands([$brandId]);
    }

    /**
     * {@inheritdoc}
     */
    public function getPaginatedProductsForSearch($searchText, ProductFilterData $productFilterData, $orderingModeId, $page, $limit): PaginationResult
    {
        $filterQuery = $this->createProductsForSearchTextFilterQuery($productFilterData, $orderingModeId, $page, $limit, $searchText);

        $productIds = $this->productElasticsearchRepository->getSortedProductIdsByFilterQuery($filterQuery);

        $listableProductsByIds = $this->productRepository->getListableByIds($this->domain->getId(), $this->currentCustomer->getPricingGroup(), $productIds->getIds());

        return new PaginationResult($page, $limit, $productIds->getTotal(), $listableProductsByIds);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterData $productFilterData
     * @param string $orderingModeId
     * @param int $page
     * @param int $limit
     * @param string $searchText
     * @return \Shopsys\FrameworkBundle\Model\Product\Search\FilterQuery
     */
    protected function createProductsForSearchTextFilterQuery(ProductFilterData $productFilterData, $orderingModeId, $page, $limit, $searchText): FilterQuery
    {
        $searchText = $searchText ?? '';

        return $this->createFilterQueryWithProductFilterData($productFilterData, $orderingModeId, $page, $limit)
            ->search($searchText);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterData $productFilterData
     * @param string $orderingModeId
     * @param int $page
     * @param int $limit
     * @return \Shopsys\FrameworkBundle\Model\Product\Search\FilterQuery
     */
    protected function createFilterQueryWithProductFilterData(ProductFilterData $productFilterData, $orderingModeId, $page, $limit): FilterQuery
    {
        $filterQuery = $this->filterQueryFactory->create($this->getIndexName())
            ->filterOnlySellable()
            ->filterOnlyVisible($this->currentCustomer->getPricingGroup())
            ->setPage($page)
            ->setLimit($limit)
            ->applyOrdering($orderingModeId, $this->currentCustomer->getPricingGroup());

        $filterQuery = $this->productFilterDataToQueryTransformer->addBrandsToQuery($productFilterData, $filterQuery);
        $filterQuery = $this->productFilterDataToQueryTransformer->addFlagsToQuery($productFilterData, $filterQuery);
        $filterQuery = $this->productFilterDataToQueryTransformer->addParametersToQuery($productFilterData, $filterQuery);
        $filterQuery = $this->productFilterDataToQueryTransformer->addStockToQuery($productFilterData, $filterQuery);
        $filterQuery = $this->productFilterDataToQueryTransformer->addPricesToQuery($productFilterData, $filterQuery, $this->currentCustomer->getPricingGroup());

        return $filterQuery;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchAutocompleteProducts($searchText, $limit): PaginationResult
    {
        $emptyProductFilterData = new ProductFilterData();
        $page = 1;

        return $this->getPaginatedProductsForSearch($searchText, $emptyProductFilterData, ProductListOrderingConfig::ORDER_BY_RELEVANCE, $page, $limit);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductFilterCountDataInCategory($categoryId, ProductFilterConfig $productFilterConfig, ProductFilterData $productFilterData): ProductFilterCountData
    {
        $baseFilterQuery = $this->filterQueryFactory->create($this->getIndexName())
            ->filterOnlySellable()
            ->filterOnlyVisible($this->currentCustomer->getPricingGroup())
            ->filterByCategory([$categoryId]);
        $baseFilterQuery = $this->productFilterDataToQueryTransformer->addPricesToQuery($productFilterData, $baseFilterQuery, $this->currentCustomer->getPricingGroup());
        $baseFilterQuery = $this->productFilterDataToQueryTransformer->addStockToQuery($productFilterData, $baseFilterQuery);

        return $this->productFilterCountDataElasticsearchRepository->getProductFilterCountDataInCategory(
            $productFilterData,
            $baseFilterQuery
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getProductFilterCountDataForSearch($searchText, ProductFilterConfig $productFilterConfig, ProductFilterData $productFilterData): ProductFilterCountData
    {
        $searchText = $searchText ?? '';

        $baseFilterQuery = $this->filterQueryFactory->create($this->getIndexName())
            ->filterOnlySellable()
            ->filterOnlyVisible($this->currentCustomer->getPricingGroup())
            ->search($searchText);
        $baseFilterQuery = $this->productFilterDataToQueryTransformer->addPricesToQuery($productFilterData, $baseFilterQuery, $this->currentCustomer->getPricingGroup());
        $baseFilterQuery = $this->productFilterDataToQueryTransformer->addStockToQuery($productFilterData, $baseFilterQuery);

        return $this->productFilterCountDataElasticsearchRepository->getProductFilterCountDataInSearch(
            $productFilterData,
            $baseFilterQuery
        );
    }

    /**
     * @return string
     */
    protected function getIndexName(): string
    {
        return $this->elasticsearchStructureManager->getAliasName(
            $this->domain->getId(),
            ProductElasticsearchRepository::ELASTICSEARCH_INDEX
        );
    }
}
