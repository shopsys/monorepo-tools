<?php

namespace SS6\ShopBundle\DataFixtures\Performance;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Component\DataFixture\ProductDataFixtureReferenceInjector;
use SS6\ShopBundle\DataFixtures\Demo\ProductDataFixtureLoader;
use SS6\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculator;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductEditData;
use SS6\ShopBundle\Model\Product\ProductEditFacade;
use SS6\ShopBundle\Model\Product\ProductVariantFacade;
use SS6\ShopBundle\Model\Product\ProductVisibilityFacade;

class ProductDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface {

	const PRODUCTS = 40000;
	const BATCH_SIZE = 1000;

	/**
	 * @var int
	 */
	private $randomImportIndex;

	/**
	 * @var int
	 */
	private $countImported;

	/**
	 * @var int
	 */
	private $demoDataIterationCounter;

	/**
	 * @var float
	 */
	private $batchStartMicrotime;

	/**
	 * @var \Doctrine\DBAL\Logging\SQLLogger|null
	 */
	private $sqlLogger;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Product[catnum]
	 */
	private $productsByCatnum;

	public function __construct() {
		$this->randomImportIndex = rand(1, 10000) * 1000000;
		$this->countImported = 0;
		$this->demoDataIterationCounter = 0;
	}

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $objectManager
	 */
	public function load(ObjectManager $objectManager) {
		$em = $this->get('doctrine.orm.entity_manager');
		/* @var $em \Doctrine\ORM\EntityManager */
		$productEditFacade = $this->get(ProductEditFacade::class);
		/* @var $productEditFacade \SS6\ShopBundle\Model\Product\ProductEditFacade */
		$loaderService = $this->get(ProductDataFixtureLoader::class);
		/* @var $loaderService \SS6\ShopBundle\DataFixtures\Demo\ProductDataFixtureLoader */

		// Sql logging during mass data import makes memory leak
		$this->temporailyDisableLogging($em);
		$productsEditData = $this->cleanAndWarmUp($em);
		$variantCatnumsByMainVariantCatnum = $loaderService->getVariantCatnumsIndexedByMainVariantCatnum();

		while ($this->countImported < self::PRODUCTS) {
			$productEditData = next($productsEditData);
			if ($productEditData === false) {
				$this->createVariants($variantCatnumsByMainVariantCatnum);
				$productEditData = reset($productsEditData);
				$this->demoDataIterationCounter++;
			}
			$this->makeProductEditDataUnique($productEditData);
			$product = $productEditFacade->create($productEditData);

			if ($product->getCatnum() !== null) {
				$this->productsByCatnum[$product->getCatnum()] = $product;
			}

			$this->printProgress();
			if ($this->countImported % self::BATCH_SIZE === 0) {
				$productsEditData = $this->cleanAndWarmUp($em);
			}

			$this->countImported++;
		}
		$this->createVariants($variantCatnumsByMainVariantCatnum);
		$this->runRecalculators(true);
		$em->clear();
		$this->reenableLogging($em);
	}

	private function printProgress() {
		$spentMicrotime = microtime(true) - $this->batchStartMicrotime;
		$batchNumber = ceil($this->countImported / self::BATCH_SIZE);
		$totalBatches = ceil(self::PRODUCTS / self::BATCH_SIZE);
		$batchImported = $this->countImported % self::BATCH_SIZE;
		$batchImported = $batchImported ?: self::BATCH_SIZE;
		echo sprintf(
			'Batch %2d / %2d - %3d%% - %4.1f s / %2.3f s' . "\r",
			$batchNumber,
			$totalBatches,
			100 * $this->countImported / self::PRODUCTS,
			$spentMicrotime,
			$spentMicrotime / $batchImported
		);
	}

	/**
	 * @param string[catnum][] $variantCatnumsByMainVariantCatnum
	 */
	private function createVariants(array $variantCatnumsByMainVariantCatnum) {
		$uniqueIndex = $this->getUniqueIndex();
		$productVariantFacade = $this->get(ProductVariantFacade::class);
		/* @var $productVariantFacade \SS6\ShopBundle\Model\Product\ProductVariantFacade */

		foreach ($variantCatnumsByMainVariantCatnum as $mainVariantCatnum => $variantsCatnums) {
			try {
				$mainProduct = $this->getProductByCatnum($mainVariantCatnum . $uniqueIndex);
				$variants = [];
				foreach ($variantsCatnums as $variantCatnum) {
					$variants[] = $this->getProductByCatnum($variantCatnum . $uniqueIndex);
				}
				$productVariantFacade->createVariant($mainProduct, $variants);
			} catch (\Doctrine\ORM\NoResultException $e) {
				continue;
			}
		}
	}

	/**
	 * @param string $catnum
	 * @return \SS6\ShopBundle\Model\Product\Product
	 */
	private function getProductByCatnum($catnum) {
		if (!array_key_exists($catnum, $this->productsByCatnum)) {
			$em = $this->get(EntityManager::class);
			/* @var $em \Doctrine\ORM\EntityManager */

			$query = $em->createQuery('SELECT p FROM ' . Product::class . ' p WHERE p.catnum = :catnum')
				->setParameter('catnum', $catnum);
			$this->productsByCatnum[$catnum] = $query->getSingleResult();
		}

		return $this->productsByCatnum[$catnum];
	}

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 */
	private function temporailyDisableLogging(EntityManager $em) {
		$this->sqlLogger = $em->getConnection()->getConfiguration()->getSQLLogger();
		$em->getConnection()->getConfiguration()->setSQLLogger(null);
	}

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 */
	private function reenableLogging(EntityManager $em) {
		$em->getConnection()->getConfiguration()->setSQLLogger($this->sqlLogger);
		$this->sqlLogger = null;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\ProductEditData $productEditData
	 */
	private function makeProductEditDataUnique(ProductEditData $productEditData) {
		$matches = [];
		$uniqueIndex = $this->getUniqueIndex();

		if (preg_match('/^(.*) #\d+$/', $productEditData->productData->catnum, $matches)) {
			$productEditData->productData->catnum = $matches[1] . $uniqueIndex;
		} else {
			$productEditData->productData->catnum .= $uniqueIndex;
		}

		foreach ($productEditData->productData->name as $locale => $name) {
			if (preg_match('/^(.*) #\d+$/', $name, $matches)) {
				$productEditData->productData->name[$locale] = $matches[1] . $uniqueIndex;
			} else {
				$productEditData->productData->name[$locale] .= $uniqueIndex;
			}
		}
	}

	/**
	 * @return string
	 */
	private function getUniqueIndex() {
		return ' #' . ($this->randomImportIndex + $this->demoDataIterationCounter);
	}

	/**
	 * @param bool $runGlobalRecalculators
	 */
	private function runRecalculators($runGlobalRecalculators = false) {
		$productAvailabilityRecalculator = $this->get(ProductAvailabilityRecalculator::class);
		/* @var $productAvailabilityRecalculator \SS6\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculator */
		$productVisibilityFacade = $this->get(ProductVisibilityFacade::class);
		/* @var $productVisibilityFacade \SS6\ShopBundle\Model\Product\ProductVisibilityFacade */
		$productPriceRecalculator = $this->get(ProductPriceRecalculator::class);
		/* @var $productPriceRecalculator \SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator */

		$productAvailabilityRecalculator->runImmediateRecalculations();
		$productPriceRecalculator->runImmediateRecalculations();
		if ($runGlobalRecalculators) {
			$productVisibilityFacade->refreshProductsVisibility();
		}
	}

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 */
	private function clearResources(EntityManager $em) {
		$this->runRecalculators();
		$em->clear();
		gc_collect_cycles();
		echo "\nMemory usage: " . round(memory_get_usage() / 1024 / 1024, 1) . "MB\n";
	}

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @return \SS6\ShopBundle\Model\Product\ProductEditData[]
	 */
	private function cleanAndWarmUp(EntityManager $em) {
		$this->clearResources($em);
		$this->batchStartMicrotime = microtime(true);
		$this->productsByCatnum = [];

		$productDataFixtureLoader = $this->get(ProductDataFixtureLoader::class);
		/* @var $$productDataFixtureLoader \SS6\ShopBundle\DataFixtures\Demo\ProductDataFixtureLoader */
		$referenceInjector = $this->get(ProductDataFixtureReferenceInjector::class);
		/* @var $referenceInjector \SS6\ShopBundle\Component\DataFixture\ProductDataFixtureReferenceInjector */

		$referenceInjector->loadReferences($productDataFixtureLoader, $this->referenceRepository);

		return $productDataFixtureLoader->getProductsEditData();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDependencies() {
		return ProductDataFixtureReferenceInjector::getDependencies();
	}

}
