<?php

namespace Tests\ShopBundle\Functional\Model\Product\Search;

use Elasticsearch\Client;
use Shopsys\FrameworkBundle\Component\Elasticsearch\ElasticsearchStructureManager;
use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Model\Product\Listing\ProductListOrderingConfig;
use Shopsys\FrameworkBundle\Model\Product\Search\FilterQuery;
use Shopsys\FrameworkBundle\Model\Product\Search\FilterQueryFactory;
use Shopsys\ShopBundle\DataFixtures\Demo\PricingGroupDataFixture;
use Tests\ShopBundle\Test\TransactionFunctionalTestCase;

class FilterQueryTest extends TransactionFunctionalTestCase
{
    private const ELASTICSEARCH_INDEX = 'product';

    public function testBrand(): void
    {
        $filter = $this->createFilter()
            ->filterByBrands([1]);

        $this->assertIdWithFilter($filter, [5]);
    }

    public function testFlag(): void
    {
        $filter = $this->createFilter()
            ->filterByFlags([3])
            ->applyDefaultOrdering();

        $this->assertIdWithFilter($filter, [1, 5, 50, 16, 33, 70, 39, 40, 45]);
    }

    public function testFlagBrand(): void
    {
        $filter = $this->createFilter()
            ->filterByBrands([12])
            ->filterByFlags([1])
            ->applyDefaultOrdering();

        $this->assertIdWithFilter($filter, [17, 19]);
    }

    public function testMultiFilter(): void
    {
        /** @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup */
        $pricingGroup = $this->getReference(PricingGroupDataFixture::PRICING_GROUP_ORDINARY_DOMAIN_1);

        $filter = $this->createFilter()
            ->filterOnlyInStock()
            ->filterByCategory([9])
            ->filterByFlags([1])
            ->filterByPrices($pricingGroup, null, Money::create(20));

        $this->assertIdWithFilter($filter, [50]);
    }

    public function testParameters(): void
    {
        $parameters = [50 => [109, 115], 49 => [105, 121], 10 => [107]];

        $filter = $this->createFilter()
            ->filterByParameters($parameters);

        $this->assertIdWithFilter($filter, [25, 28]);
    }

    public function testOrdering(): void
    {
        /** @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup */
        $pricingGroup = $this->getReference(PricingGroupDataFixture::PRICING_GROUP_ORDINARY_DOMAIN_1);

        $filter = $this->createFilter()
            ->filterByCategory([9])
            ->applyDefaultOrdering();

        $this->assertIdWithFilter($filter, [72, 25, 27, 29, 28, 26, 50, 33, 39, 40], 'top');

        $nameAscFilter = $filter->applyOrdering(ProductListOrderingConfig::ORDER_BY_NAME_ASC, $pricingGroup);
        $this->assertIdWithFilter($nameAscFilter, [72, 25, 27, 29, 28, 26, 50, 33, 39, 40], 'name asc');

        $nameDescFilter = $filter->applyOrdering(ProductListOrderingConfig::ORDER_BY_NAME_DESC, $pricingGroup);
        $this->assertIdWithFilter($nameDescFilter, [40, 39, 33, 50, 26, 28, 29, 27, 25, 72], 'name desc');

        $priceAscFilter = $filter->applyOrdering(ProductListOrderingConfig::ORDER_BY_PRICE_ASC, $pricingGroup);
        $this->assertIdWithFilter($priceAscFilter, [40, 33, 50, 39, 29, 25, 26, 27, 28, 72], 'price asc');

        $priceDescFilter = $filter->applyOrdering(ProductListOrderingConfig::ORDER_BY_PRICE_DESC, $pricingGroup);
        $this->assertIdWithFilter($priceDescFilter, [72, 28, 27, 26, 25, 29, 39, 50, 33, 40], 'price desc');
    }

    public function testMatchQuery(): void
    {
        $filter = $this->createFilter();

        $kittyFilter = $filter->search('kitty');
        $this->assertIdWithFilter($kittyFilter, [1, 102, 101]);

        $mg3550Filer = $filter->search('mg3550');
        $this->assertIdWithFilter($mg3550Filer, [9, 144, 10, 145]);
    }

    public function testPagination(): void
    {
        $filter = $this->createFilter()
            ->filterByCategory([9])
            ->applyDefaultOrdering();

        $this->assertIdWithFilter($filter, [72, 25, 27, 29, 28, 26, 50, 33, 39, 40]);

        $limit5Filter = $filter->setLimit(5);
        $this->assertIdWithFilter($limit5Filter, [72, 25, 27, 29, 28]);

        $limit1Filter = $filter->setLimit(1);
        $this->assertIdWithFilter($limit1Filter, [72]);

        $limit4Page2Filter = $filter->setLimit(4)
            ->setPage(2);
        $this->assertIdWithFilter($limit4Page2Filter, [28, 26, 50, 33]);

        $limit4Page3Filter = $filter->setLimit(4)
            ->setPage(3);
        $this->assertIdWithFilter($limit4Page3Filter, [39, 40]);

        $limit4Page4Filter = $filter->setLimit(4)
            ->setPage(4);
        $this->assertIdWithFilter($limit4Page4Filter, []);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Search\FilterQuery $filterQuery
     * @param int[] $ids
     * @param string $message
     */
    protected function assertIdWithFilter(FilterQuery $filterQuery, array $ids, string $message = ''): void
    {
        /** @var \Elasticsearch\Client $es */
        $es = $this->getContainer()->get(Client::class);

        $params = $filterQuery->getQuery();

        $params['_source'] = false;

        $result = $es->search($params);
        $this->assertSame($ids, $this->extractIds($result), $message);
    }

    /**
     * @param array $result
     * @return int[]
     */
    protected function extractIds(array $result): array
    {
        $hits = $result['hits']['hits'];

        return array_map(static function ($element) {
            return (int)$element['_id'];
        }, $hits);
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Search\FilterQuery
     */
    protected function createFilter(): FilterQuery
    {
        /** @var \Shopsys\FrameworkBundle\Model\Product\Search\FilterQueryFactory $filterQueryFactory */
        $filterQueryFactory = $this->getContainer()->get(FilterQueryFactory::class);

        /** @var \Shopsys\FrameworkBundle\Component\Elasticsearch\ElasticsearchStructureManager $elasticSearchStructureManager */
        $elasticSearchStructureManager = $this->getContainer()->get(ElasticsearchStructureManager::class);

        $elasticSearchIndexName = $elasticSearchStructureManager->getIndexName(1, self::ELASTICSEARCH_INDEX);

        $filter = $filterQueryFactory->create($elasticSearchIndexName);

        return $filter->filterOnlySellable();
    }
}
