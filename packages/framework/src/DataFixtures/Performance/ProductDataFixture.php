<?php

namespace Shopsys\FrameworkBundle\DataFixtures\Performance;

use Doctrine\ORM\EntityManagerInterface;
use Faker\Generator as Faker;
use Shopsys\FrameworkBundle\Component\Console\ProgressBarFactory;
use Shopsys\FrameworkBundle\Component\DataFixture\PersistentReferenceFacade;
use Shopsys\FrameworkBundle\Component\Doctrine\SqlLoggerFacade;
use Shopsys\FrameworkBundle\DataFixtures\Demo\ProductDataFixtureCsvReader;
use Shopsys\FrameworkBundle\DataFixtures\Demo\ProductDataFixtureLoader;
use Shopsys\FrameworkBundle\DataFixtures\ProductDataFixtureReferenceInjector;
use Shopsys\FrameworkBundle\Model\Category\Category;
use Shopsys\FrameworkBundle\Model\Category\CategoryRepository;
use Shopsys\FrameworkBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductData;
use Shopsys\FrameworkBundle\Model\Product\ProductFacade;
use Shopsys\FrameworkBundle\Model\Product\ProductVariantFacade;
use Symfony\Component\Console\Output\OutputInterface;

class ProductDataFixture
{
    const BATCH_SIZE = 1000;

    const FIRST_PERFORMANCE_PRODUCT = 'first_performance_product';

    /**
     * @var int
     */
    private $productTotalCount;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductFacade
     */
    private $productFacade;

    /**
     * @var \Shopsys\FrameworkBundle\DataFixtures\Demo\ProductDataFixtureLoader
     */
    private $productDataFixtureLoader;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Doctrine\SqlLoggerFacade
     */
    private $sqlLoggerFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductVariantFacade
     */
    private $productVariantFacade;

    /**
     * @var \Shopsys\FrameworkBundle\DataFixtures\ProductDataFixtureReferenceInjector
     */
    private $productDataReferenceInjector;

    /**
     * @var \Shopsys\FrameworkBundle\Component\DataFixture\PersistentReferenceFacade
     */
    private $persistentReferenceFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Category\CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var int
     */
    private $countImported;

    /**
     * @var int
     */
    private $demoDataIterationCounter;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Product[]
     */
    private $productsByCatnum;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler
     */
    private $productAvailabilityRecalculationScheduler;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler
     */
    private $productPriceRecalculationScheduler;

    /**
     * @var \Shopsys\FrameworkBundle\DataFixtures\Demo\ProductDataFixtureCsvReader
     */
    private $productDataFixtureCsvReader;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Console\ProgressBarFactory
     */
    private $progressBarFactory;

    /**
     * @param int $productTotalCount
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductFacade $productFacade
     * @param \Shopsys\FrameworkBundle\DataFixtures\Demo\ProductDataFixtureLoader $productDataFixtureLoader
     * @param \Shopsys\FrameworkBundle\Component\Doctrine\SqlLoggerFacade $sqlLoggerFacade
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductVariantFacade $productVariantFacade
     * @param \Shopsys\FrameworkBundle\DataFixtures\ProductDataFixtureReferenceInjector $productDataReferenceInjector
     * @param \Shopsys\FrameworkBundle\Component\DataFixture\PersistentReferenceFacade $persistentReferenceFacade
     * @param \Shopsys\FrameworkBundle\Model\Category\CategoryRepository $categoryRepository
     * @param \Faker\Generator $faker
     * @param \Shopsys\FrameworkBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler $productAvailabilityRecalculationScheduler
     * @param \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler $productPriceRecalculationScheduler
     * @param \Shopsys\FrameworkBundle\DataFixtures\Demo\ProductDataFixtureCsvReader $productDataFixtureCsvReader
     * @param \Shopsys\FrameworkBundle\Component\Console\ProgressBarFactory $progressBarFactory
     */
    public function __construct(
        $productTotalCount,
        EntityManagerInterface $em,
        ProductFacade $productFacade,
        ProductDataFixtureLoader $productDataFixtureLoader,
        SqlLoggerFacade $sqlLoggerFacade,
        ProductVariantFacade $productVariantFacade,
        ProductDataFixtureReferenceInjector $productDataReferenceInjector,
        PersistentReferenceFacade $persistentReferenceFacade,
        CategoryRepository $categoryRepository,
        Faker $faker,
        ProductAvailabilityRecalculationScheduler $productAvailabilityRecalculationScheduler,
        ProductPriceRecalculationScheduler $productPriceRecalculationScheduler,
        ProductDataFixtureCsvReader $productDataFixtureCsvReader,
        ProgressBarFactory $progressBarFactory
    ) {
        $this->productTotalCount = $productTotalCount;
        $this->em = $em;
        $this->productFacade = $productFacade;
        $this->productDataFixtureLoader = $productDataFixtureLoader;
        $this->sqlLoggerFacade = $sqlLoggerFacade;
        $this->productVariantFacade = $productVariantFacade;
        $this->productDataReferenceInjector = $productDataReferenceInjector;
        $this->persistentReferenceFacade = $persistentReferenceFacade;
        $this->categoryRepository = $categoryRepository;
        $this->countImported = 0;
        $this->demoDataIterationCounter = 0;
        $this->faker = $faker;
        $this->productAvailabilityRecalculationScheduler = $productAvailabilityRecalculationScheduler;
        $this->productPriceRecalculationScheduler = $productPriceRecalculationScheduler;
        $this->productDataFixtureCsvReader = $productDataFixtureCsvReader;
        $this->progressBarFactory = $progressBarFactory;
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function load(OutputInterface $output)
    {
        // Sql logging during mass data import makes memory leak
        $this->sqlLoggerFacade->temporarilyDisableLogging();

        $this->cleanAndLoadReferences();
        $csvRows = $this->productDataFixtureCsvReader->getProductDataFixtureCsvRows();
        $variantCatnumsByMainVariantCatnum = $this->productDataFixtureLoader->getVariantCatnumsIndexedByMainVariantCatnum(
            $csvRows
        );

        $progressBar = $this->progressBarFactory->create($output, $this->productTotalCount);

        while ($this->countImported < $this->productTotalCount) {
            $row = next($csvRows);
            if ($row === false) {
                $this->createVariants($variantCatnumsByMainVariantCatnum);
                $row = reset($csvRows);
                $this->demoDataIterationCounter++;
            }
            $productData = $this->productDataFixtureLoader->createProductDataFromRowForFirstDomain($row);
            $this->productDataFixtureLoader->updateProductDataFromCsvRowForSecondDomain($productData, $row);
            $this->makeProductDataUnique($productData);
            $this->setRandomPerformanceCategoriesToProductData($productData);
            $product = $this->productFacade->create($productData);

            if ($this->countImported === 0) {
                $this->persistentReferenceFacade->persistReference(self::FIRST_PERFORMANCE_PRODUCT, $product);
            }

            if ($product->getCatnum() !== null) {
                $this->productsByCatnum[$product->getCatnum()] = $product;
            }

            if ($this->countImported % self::BATCH_SIZE === 0) {
                $currentKey = key($csvRows);
                $this->cleanAndLoadReferences();
                $this->setArrayPointerByKey($csvRows, $currentKey);
            }

            $this->countImported++;

            $progressBar->setProgress($this->countImported);
        }
        $this->createVariants($variantCatnumsByMainVariantCatnum);

        $progressBar->finish();

        $this->em->clear();
        $this->sqlLoggerFacade->reenableLogging();
    }

    /**
     * @param string[][] $variantCatnumsByMainVariantCatnum
     */
    private function createVariants(array $variantCatnumsByMainVariantCatnum)
    {
        $uniqueIndex = $this->getUniqueIndex();

        foreach ($variantCatnumsByMainVariantCatnum as $mainVariantCatnum => $variantsCatnums) {
            try {
                $mainProduct = $this->getProductByCatnum($mainVariantCatnum . $uniqueIndex);
                $variants = [];
                foreach ($variantsCatnums as $variantCatnum) {
                    $variants[] = $this->getProductByCatnum($variantCatnum . $uniqueIndex);
                }
                $this->productVariantFacade->createVariant($mainProduct, $variants);
            } catch (\Doctrine\ORM\NoResultException $e) {
                continue;
            }
        }
    }

    /**
     * @param string $catnum
     * @return \Shopsys\FrameworkBundle\Model\Product\Product
     */
    private function getProductByCatnum($catnum)
    {
        if (!array_key_exists($catnum, $this->productsByCatnum)) {
            $query = $this->em->createQuery('SELECT p FROM ' . Product::class . ' p WHERE p.catnum = :catnum')
                ->setParameter('catnum', $catnum);
            $this->productsByCatnum[$catnum] = $query->getSingleResult();
        }

        return $this->productsByCatnum[$catnum];
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductData $productData
     */
    private function makeProductDataUnique(ProductData $productData)
    {
        $matches = [];
        $uniqueIndex = $this->getUniqueIndex();

        if (preg_match('/^(.*) #\d+$/', $productData->catnum, $matches)) {
            $productData->catnum = $matches[1] . $uniqueIndex;
        } else {
            $productData->catnum .= $uniqueIndex;
        }

        foreach ($productData->name as $locale => $name) {
            if (preg_match('/^(.*) #\d+$/', $name, $matches)) {
                $productData->name[$locale] = $matches[1] . $uniqueIndex;
            } else {
                $productData->name[$locale] .= $uniqueIndex;
            }
        }
    }

    /**
     * @return string
     */
    private function getUniqueIndex()
    {
        return ' #' . $this->demoDataIterationCounter;
    }

    private function clearResources()
    {
        $this->productAvailabilityRecalculationScheduler->cleanScheduleForImmediateRecalculation();
        $this->productPriceRecalculationScheduler->cleanScheduleForImmediateRecalculation();
        $this->em->clear();
        gc_collect_cycles();
    }

    private function cleanAndLoadReferences()
    {
        $this->clearResources();
        $this->productsByCatnum = [];

        $onlyForFirstDomain = false;
        $this->productDataReferenceInjector->loadReferences(
            $this->productDataFixtureLoader,
            $this->persistentReferenceFacade,
            $onlyForFirstDomain
        );
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductData $productData
     */
    private function setRandomPerformanceCategoriesToProductData(ProductData $productData)
    {
        $this->cleanPerformanceCategoriesFromProductDataByDomainId($productData, 1);
        $this->cleanPerformanceCategoriesFromProductDataByDomainId($productData, 2);
        $this->addRandomPerformanceCategoriesToProductDataByDomainId($productData, 1);
        $this->addRandomPerformanceCategoriesToProductDataByDomainId($productData, 2);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductData $productData
     * @param int $domainId
     */
    private function cleanPerformanceCategoriesFromProductDataByDomainId(ProductData $productData, $domainId)
    {
        foreach ($productData->categoriesByDomainId[$domainId] as $key => $category) {
            if ($this->isPerformanceCategory($category)) {
                unset($productData->categoriesByDomainId[$domainId][$key]);
            }
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductData $productData
     * @param int $domainId
     */
    private function addRandomPerformanceCategoriesToProductDataByDomainId(ProductData $productData, $domainId)
    {
        $performanceCategoryIds = $this->getPerformanceCategoryIds();
        $randomPerformanceCategoryIds = $this->faker->randomElements(
            $performanceCategoryIds,
            $this->faker->numberBetween(1, 4)
        );
        $randomPerformanceCategories = $this->categoryRepository->getCategoriesByIds($randomPerformanceCategoryIds);

        foreach ($randomPerformanceCategories as $performanceCategory) {
            if (!in_array($performanceCategory, $productData->categoriesByDomainId[$domainId], true)) {
                $productData->categoriesByDomainId[$domainId][] = $performanceCategory;
            }
        }
    }

    /**
     * @return int[]
     */
    private function getPerformanceCategoryIds()
    {
        $allCategoryIds = $this->categoryRepository->getAllIds();
        $firstPerformanceCategory = $this->persistentReferenceFacade->getReference(
            CategoryDataFixture::FIRST_PERFORMANCE_CATEGORY
        );
        $firstPerformanceCategoryKey = array_search($firstPerformanceCategory->getId(), $allCategoryIds, true);

        return array_slice($allCategoryIds, $firstPerformanceCategoryKey);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\Category $category
     * @return bool
     */
    private function isPerformanceCategory(Category $category)
    {
        $firstPerformanceCategory = $this->persistentReferenceFacade->getReference(
            CategoryDataFixture::FIRST_PERFORMANCE_CATEGORY
        );
        /* @var $firstPerformanceCategory \Shopsys\FrameworkBundle\Model\Category\Category */

        return $category->getId() >= $firstPerformanceCategory->getId();
    }

    /**
     * @param array $array
     * @param string|int $key
     */
    private function setArrayPointerByKey(array &$array, $key)
    {
        reset($array);
        while (key($array) !== $key) {
            if (each($array) === false) {
                throw new \Shopsys\FrameworkBundle\DataFixtures\Performance\Exception\UndefinedArrayKeyException($key);
            }
        }
    }
}
