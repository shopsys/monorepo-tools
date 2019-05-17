<?php

namespace Tests\ShopBundle\Functional\Model\Product;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Model\Category\Category;
use Shopsys\FrameworkBundle\Model\Product\Filter\ParameterFilterData;
use Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterConfigFactory;
use Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterCountData;
use Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterData;
use Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterRepository;
use Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterValue;
use Shopsys\FrameworkBundle\Model\Product\ProductOnCurrentDomainFacadeInterface;
use Shopsys\ShopBundle\DataFixtures\Demo\BrandDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\CategoryDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\FlagDataFixture;
use Tests\ShopBundle\Test\TransactionFunctionalTestCase;

abstract class ProductOnCurrentDomainFacadeCountDataTest extends TransactionFunctionalTestCase
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterConfigFactory
     */
    protected $productFilterConfigFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductOnCurrentDomainFacadeInterface
     */
    protected $productOnCurrentDomainFacade;

    protected function setUp()
    {
        parent::setUp();
        $this->productFilterConfigFactory = $this->getContainer()->get(ProductFilterConfigFactory::class);
        $this->domain = $this->getContainer()->get(Domain::class);
        $this->productOnCurrentDomainFacade = $this->getProductOnCurrentDomainFacade();
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\ProductOnCurrentDomainFacadeInterface
     */
    abstract public function getProductOnCurrentDomainFacade(): ProductOnCurrentDomainFacadeInterface;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\Category $category
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterData $filterData
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterCountData $expectedCountData
     * @dataProvider categoryTestCasesProvider
     */
    public function testCategory(Category $category, ProductFilterData $filterData, ProductFilterCountData $expectedCountData): void
    {
        $filterConfig = $this->productFilterConfigFactory->createForCategory($this->domain->getId(), $this->domain->getLocale(), $category);
        $countData = $this->productOnCurrentDomainFacade->getProductFilterCountDataInCategory($category->getId(), $filterConfig, $filterData);

        $this->assertEquals($expectedCountData, $this->removeEmptyParameters($countData));
    }

    /**
     * @return array[]
     */
    public function categoryTestCasesProvider(): array
    {
        return [
            'no-filter' => $this->categoryNoFilterTestCase(),
            'one-flag' => $this->categoryOneFlagTestCase(),
            'one-brand' => $this->categoryOneBrandTestCase(),
            'all-flags-all-brands' => $this->categoryAllFlagsAllBrandsTestCase(),
            'price' => $this->categoryPriceTestCase(),
            'stock' => $this->categoryStockTestCase(),
            'flag-brand-parameters' => $this->categoryFlagBrandAndParametersTestCase(),
            'parameters' => $this->categoryParametersTestCase(),
        ];
    }

    /**
     * @param string $searchText
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterData $filterData
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterCountData $expectedCountData
     * @dataProvider searchTestCasesProvider
     */
    public function testSearch(string $searchText, ProductFilterData $filterData, ProductFilterCountData $expectedCountData): void
    {
        $filterConfig = $this->productFilterConfigFactory->createForSearch($this->domain->getId(), $this->domain->getLocale(), $searchText);
        $countData = $this->productOnCurrentDomainFacade->getProductFilterCountDataForSearch($searchText, $filterConfig, $filterData);

        $this->assertEquals($expectedCountData, $this->removeEmptyParameters($countData));
    }

    /**
     * @return array[]
     */
    public function searchTestCasesProvider(): array
    {
        return [
            'no-filter' => $this->searchNoFilterTestCase(),
            'one-flag' => $this->searchOneFlagTestCase(),
            'one-brand' => $this->searchOneBrandTestCase(),
            'price' => $this->searchPriceTestCase(),
            'stock' => $this->searchStockTestCase(),
            'price-stock-flag-brands' => $this->searchPriceStockFlagBrandsTestCase(),
        ];
    }

    /**
     * @return array
     */
    private function categoryNoFilterTestCase(): array
    {
        $category = $this->getReference(CategoryDataFixture::CATEGORY_PRINTERS);
        $filterData = new ProductFilterData();
        $countData = new ProductFilterCountData();

        $countData->countInStock = 10;
        $countData->countByBrandId = [
            2 => 6,
            14 => 2,
        ];
        $countData->countByFlagId = [
            1 => 5,
            2 => 2,
        ];
        $countData->countByParameterIdAndValueId = [
            32 => [
                8 => 10,
            ],
            11 => [
                58 => 8,
                124 => 2,
            ],
            30 => [
                8 => 5,
                12 => 5,
            ],
            29 => [
                54 => 7,
                189 => 3,
            ],
            31 => [
                56 => 3,
                97 => 7,
            ],
            28 => [
                52 => 10,
            ],
            4 => [
                8 => 10,
            ],
            10 => [
                60 => 1,
                62 => 9,
            ],
            33 => [
                8 => 8,
                12 => 2,
            ],
        ];

        return [
            $category,
            $filterData,
            $countData,
        ];
    }

    /**
     * @return array
     */
    private function categoryOneFlagTestCase(): array
    {
        $category = $this->getReference(CategoryDataFixture::CATEGORY_PRINTERS);
        $filterData = new ProductFilterData();
        $filterData->flags[] = $this->getReference(FlagDataFixture::FLAG_TOP_PRODUCT);

        $countData = new ProductFilterCountData();

        $countData->countInStock = 2;
        $countData->countByBrandId = [
            2 => 2,
        ];
        $countData->countByFlagId = [
            1 => 3,
        ];
        $countData->countByParameterIdAndValueId = [
            32 => [
                8 => 2,
            ],
            11 => [
                58 => 2,
            ],
            30 => [
                8 => 1,
                12 => 1,
            ],
            29 => [
                54 => 1,
                189 => 1,
            ],
            31 => [
                56 => 1,
                97 => 1,
            ],
            28 => [
                52 => 2,
            ],
            4 => [
                8 => 2,
            ],
            10 => [
                60 => 1,
                62 => 1,
            ],
            33 => [
                8 => 2,
            ],
        ];

        return [
            $category,
            $filterData,
            $countData,
        ];
    }

    /**
     * @return array
     */
    private function categoryOneBrandTestCase(): array
    {
        $category = $this->getReference(CategoryDataFixture::CATEGORY_PRINTERS);
        $filterData = new ProductFilterData();
        $filterData->brands[] = $this->getReference(BrandDataFixture::BRAND_CANON);
        $countData = new ProductFilterCountData();
        $countData->countInStock = 6;
        $countData->countByFlagId = [
            1 => 3,
            2 => 2,
        ];
        $countData->countByBrandId = [
            14 => 2,
        ];
        $countData->countByParameterIdAndValueId = [
            32 => [
                8 => 6,
            ],
            11 => [
                58 => 6,
            ],
            30 => [
                8 => 3,
                12 => 3,
            ],
            29 => [
                54 => 3,
                189 => 3,
            ],
            31 => [
                56 => 2,
                97 => 4,
            ],
            28 => [
                52 => 6,
            ],
            4 => [
                8 => 6,
            ],
            10 => [
                60 => 1,
                62 => 5,
            ],
            33 => [
                8 => 6,
            ],
        ];

        return [
            $category,
            $filterData,
            $countData,
        ];
    }

    /**
     * @return array
     */
    private function categoryAllFlagsAllBrandsTestCase(): array
    {
        $category = $this->getReference(CategoryDataFixture::CATEGORY_PRINTERS);
        $filterData = new ProductFilterData();
        $filterData->brands[] = $this->getReference(BrandDataFixture::BRAND_CANON);
        $filterData->brands[] = $this->getReference(BrandDataFixture::BRAND_HP);
        $filterData->flags[] = $this->getReference(FlagDataFixture::FLAG_TOP_PRODUCT);
        $filterData->flags[] = $this->getReference(FlagDataFixture::FLAG_NEW_PRODUCT);

        $countData = new ProductFilterCountData();
        $countData->countInStock = 4;
        $countData->countByParameterIdAndValueId = [
            32 => [
                8 => 4,
            ],
            11 => [
                58 => 4,
            ],
            30 => [
                8 => 2,
                12 => 2,
            ],
            29 => [
                54 => 3,
                189 => 1,
            ],
            31 => [
                56 => 2,
                97 => 2,
            ],
            28 => [
                52 => 4,
            ],
            4 => [
                8 => 4,
            ],
            10 => [
                60 => 1,
                62 => 3,
            ],
            33 => [
                8 => 4,
            ],
        ];

        return [
            $category,
            $filterData,
            $countData,
        ];
    }

    /**
     * @return array
     */
    private function categoryPriceTestCase(): array
    {
        $category = $this->getReference(CategoryDataFixture::CATEGORY_PRINTERS);
        $filterData = new ProductFilterData();
        $filterData->minimalPrice = Money::create(1000);
        $filterData->maximalPrice = Money::create(80000);

        $countData = new ProductFilterCountData();
        $countData->countInStock = 6;
        $countData->countByBrandId = [
            2 => 4,
            14 => 2,
        ];
        $countData->countByFlagId = [
            1 => 3,
            2 => 2,
        ];
        $countData->countByParameterIdAndValueId = [
            32 => [
                8 => 6,
            ],
            11 => [
                58 => 6,
            ],
            30 => [
                8 => 3,
                12 => 3,
            ],
            29 => [
                54 => 4,
                189 => 2,
            ],
            31 => [
                56 => 1,
                97 => 5,
            ],
            28 => [
                52 => 6,
            ],
            4 => [
                8 => 6,
            ],
            10 => [
                60 => 1,
                62 => 5,
            ],
            33 => [
                8 => 6,
            ],
        ];

        return [
            $category,
            $filterData,
            $countData,
        ];
    }

    /**
     * @return array
     */
    private function categoryStockTestCase(): array
    {
        $category = $this->getReference(CategoryDataFixture::CATEGORY_PHONES);
        $filterData = new ProductFilterData();
        $filterData->inStock = true;

        $countData = new ProductFilterCountData();
        $countData->countInStock = 2;
        $countData->countByBrandId = [
            3 => 1,
            20 => 1,
        ];
        $countData->countByFlagId = [
            1 => 2,
        ];
        $countData->countByParameterIdAndValueId = [
            17 => [
                8 => 1,
            ],
            11 => [
                24 => 1,
            ],
            19 => [
                12 => 1,
            ],
            12 => [
                12 => 1,
            ],
            18 => [
                12 => 1,
            ],
            14 => [
                28 => 1,
            ],
            16 => [
                32 => 1,
            ],
            15 => [
                30 => 1,
            ],
            13 => [
                26 => 1,
            ],
            10 => [
                22 => 1,
            ],
        ];

        return [
            $category,
            $filterData,
            $countData,
        ];
    }

    /**
     * @return array
     */
    private function categoryFlagBrandAndParametersTestCase(): array
    {
        $category = $this->getReference(CategoryDataFixture::CATEGORY_PRINTERS);
        $filterData = new ProductFilterData();
        $filterData->brands[] = $this->getReference(BrandDataFixture::BRAND_CANON);
        $filterData->flags[] = $this->getReference(FlagDataFixture::FLAG_NEW_PRODUCT);
        $filterData->parameters[] = $this->createParameterFilterData(
            ['en' => 'Dimensions'],
            [['en' => '449x304x152 mm']]
        );
        $filterData->parameters[] = $this->createParameterFilterData(
            ['en' => 'Print resolution'],
            [['en' => '2400x600'], ['en' => '4800x1200']]
        );
        $filterData->parameters[] = $this->createParameterFilterData(
            ['en' => 'Weight'],
            [['en' => '3.5 kg']]
        );

        $countData = new ProductFilterCountData();
        $countData->countInStock = 2;
        $countData->countByBrandId = [
            14 => 1,
        ];
        $countData->countByFlagId = [];
        $countData->countByParameterIdAndValueId = [
            32 => [
                8 => 2,
            ],
            11 => [
                58 => 2,
            ],
            30 => [
                8 => 1,
                12 => 1,
            ],
            29 => [
                54 => 1,
                189 => 1,
            ],
            31 => [
                56 => 1,
                97 => 1,
            ],
            28 => [
                52 => 2,
            ],
            4 => [
                8 => 2,
            ],
            10 => [
                60 => 1,
                62 => 2,
            ],
            33 => [
                8 => 2,
            ],
        ];

        return [
            $category,
            $filterData,
            $countData,
        ];
    }

    /**
     * @return array
     */
    private function categoryParametersTestCase(): array
    {
        $category = $this->getReference(CategoryDataFixture::CATEGORY_PRINTERS);
        $filterData = new ProductFilterData();
        $filterData->parameters[] = $this->createParameterFilterData(
            ['en' => 'Dimensions'],
            [['en' => '449x304x152 mm']]
        );
        $filterData->parameters[] = $this->createParameterFilterData(
            ['en' => 'Print resolution'],
            [['en' => '2400x600'], ['en' => '4800x1200']]
        );
        $filterData->parameters[] = $this->createParameterFilterData(
            ['en' => 'Weight'],
            [['en' => '3.5 kg']]
        );

        $countData = new ProductFilterCountData();
        $countData->countInStock = 7;
        $countData->countByBrandId = [
            14 => 2,
            2 => 5,
        ];
        $countData->countByFlagId = [
            1 => 3,
            2 => 1,
        ];
        $countData->countByParameterIdAndValueId = [
            32 => [
                8 => 7,
            ],
            11 => [
                58 => 7,
                124 => 2,
            ],
            30 => [
                8 => 3,
                12 => 4,
            ],
            29 => [
                54 => 4,
                189 => 3,
            ],
            31 => [
                56 => 1,
                97 => 6,
            ],
            28 => [
                52 => 7,
            ],
            4 => [
                8 => 7,
            ],
            10 => [
                60 => 1,
                62 => 7,
            ],
            33 => [
                8 => 7,
            ],
        ];

        return [
            $category,
            $filterData,
            $countData,
        ];
    }

    /**
     * @param array $namesByLocale
     * @param array $valuesTextsByLocales
     * @return \Shopsys\FrameworkBundle\Model\Product\Filter\ParameterFilterData
     */
    private function createParameterFilterData(array $namesByLocale, array $valuesTextsByLocales)
    {
        /** @var \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterRepository $parameterRepository */
        $parameterRepository = $this->getContainer()->get(ParameterRepository::class);

        $parameter = $parameterRepository->findParameterByNames($namesByLocale);
        $parameterValues = $this->getParameterValuesByLocalesAndTexts($valuesTextsByLocales);

        $parameterFilterData = new ParameterFilterData();
        $parameterFilterData->parameter = $parameter;
        $parameterFilterData->values = $parameterValues;

        return $parameterFilterData;
    }

    /**
     * @param array[] $valuesTextsByLocales
     * @return \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterValue[]
     */
    private function getParameterValuesByLocalesAndTexts(array $valuesTextsByLocales)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $parameterValues = [];

        foreach ($valuesTextsByLocales as $valueTextsByLocales) {
            foreach ($valueTextsByLocales as $locale => $text) {
                /** @var \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterValue $parameterValue */
                $parameterValue = $em->getRepository(ParameterValue::class)->findOneBy([
                    'text' => $text,
                    'locale' => $locale,
                ]);
                $parameterValues[] = $parameterValue;
            }
        }

        return $parameterValues;
    }

    /**
     * @return array
     */
    private function searchNoFilterTestCase(): array
    {
        $filterData = new ProductFilterData();
        $countData = new ProductFilterCountData();
        $countData->countInStock = 38;
        $countData->countByBrandId = [
            8 => 1,
            11 => 1,
            19 => 2,
            10 => 1,
            2 => 10,
            4 => 1,
            16 => 1,
            15 => 1,
            6 => 1,
            14 => 2,
            12 => 2,
            3 => 2,
        ];
        $countData->countByFlagId = [
            1 => 15,
            2 => 5,
            3 => 3,
        ];

        return [
            'print',
            $filterData,
            $countData,
        ];
    }

    /**
     * @return array
     */
    private function searchOneFlagTestCase(): array
    {
        $filterData = new ProductFilterData();
        $filterData->flags[] = $this->getReference(FlagDataFixture::FLAG_NEW_PRODUCT);
        $countData = new ProductFilterCountData();
        $countData->countInStock = 11;
        $countData->countByBrandId = [
            2 => 3,
            3 => 1,
            10 => 1,
            11 => 1,
            12 => 1,
            14 => 1,
            15 => 1,
            16 => 1,
            19 => 2,
        ];
        $countData->countByFlagId = [
            2 => 2,
            3 => 2,
        ];

        return [
            'print',
            $filterData,
            $countData,
        ];
    }

    /**
     * @return array
     */
    private function searchOneBrandTestCase(): array
    {
        $filterData = new ProductFilterData();
        $filterData->brands[] = $this->getReference(BrandDataFixture::BRAND_CANON);
        $countData = new ProductFilterCountData();

        $countData->countInStock = 10;
        $countData->countByBrandId = [
            3 => 2,
            4 => 1,
            6 => 1,
            8 => 1,
            10 => 1,
            11 => 1,
            12 => 2,
            14 => 2,
            15 => 1,
            16 => 1,
            19 => 2,
        ];
        $countData->countByFlagId = [
            1 => 3,
            2 => 2,
        ];

        return [
            'print',
            $filterData,
            $countData,
        ];
    }

    /**
     * @return array
     */
    private function searchPriceTestCase(): array
    {
        $filterData = new ProductFilterData();
        $filterData->minimalPrice = Money::create(5000);
        $filterData->maximalPrice = Money::create(50000);
        $countData = new ProductFilterCountData();
        $countData->countInStock = 9;
        $countData->countByBrandId = [
            2 => 4,
            3 => 1,
            4 => 1,
            11 => 1,
            15 => 1,
        ];
        $countData->countByFlagId = [
            1 => 2,
            2 => 2,
        ];

        return [
            'print',
            $filterData,
            $countData,
        ];
    }

    /**
     * @return array
     */
    private function searchStockTestCase(): array
    {
        $filterData = new ProductFilterData();
        $filterData->inStock = true;
        $countData = new ProductFilterCountData();
        $countData->countInStock = 38;
        $countData->countByBrandId = [
            2 => 10,
            3 => 2,
            4 => 1,
            6 => 1,
            8 => 1,
            10 => 1,
            11 => 1,
            12 => 2,
            14 => 2,
            16 => 1,
        ];
        $countData->countByFlagId = [
            1 => 11,
            2 => 4,
            3 => 2,
        ];

        return [
            'print',
            $filterData,
            $countData,
        ];
    }

    /**
     * @return array
     */
    private function searchPriceStockFlagBrandsTestCase(): array
    {
        $filterData = new ProductFilterData();
        $filterData->inStock = true;
        $filterData->flags[] = $this->getReference(FlagDataFixture::FLAG_NEW_PRODUCT);
        $filterData->brands[] = $this->getReference(BrandDataFixture::BRAND_DELONGHI);
        $filterData->brands[] = $this->getReference(BrandDataFixture::BRAND_DEFENDER);
        $filterData->brands[] = $this->getReference(BrandDataFixture::BRAND_GENIUS);
        $filterData->brands[] = $this->getReference(BrandDataFixture::BRAND_HP);
        $filterData->maximalPrice = Money::create(20000);

        $countData = new ProductFilterCountData();
        $countData->countInStock = 3;
        $countData->countByBrandId = [
            2 => 3,
            3 => 1,
        ];

        return [
            'print',
            $filterData,
            $countData,
        ];
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterCountData $countData
     * @return \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterCountData
     */
    private function removeEmptyParameters(ProductFilterCountData $countData): ProductFilterCountData
    {
        $result = clone $countData;
        foreach ($countData->countByParameterIdAndValueId as $parameterId => $values) {
            if (empty($values)) {
                unset($result->countByParameterIdAndValueId[$parameterId]);
            }
        }
        return $result;
    }
}
