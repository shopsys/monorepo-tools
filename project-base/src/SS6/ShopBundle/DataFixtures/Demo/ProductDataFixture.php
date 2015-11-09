<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Component\DataFixture\ProductDataFixtureReferenceInjector;
use SS6\ShopBundle\DataFixtures\Demo\ProductDataFixtureLoader;
use SS6\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculator;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator;
use SS6\ShopBundle\Model\Product\ProductEditData;
use SS6\ShopBundle\Model\Product\ProductEditFacade;
use SS6\ShopBundle\Model\Product\ProductVariantFacade;
use SS6\ShopBundle\Model\Product\ProductVisibilityFacade;

class ProductDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface {

	const PRODUCT_PREFIX = 'product_';

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$productDataFixtureLoader = $this->get(ProductDataFixtureLoader::class);
		/* @var $productDataFixtureLoader \SS6\ShopBundle\DataFixtures\Demo\ProductDataFixtureLoader */
		$referenceInjector = $this->get(ProductDataFixtureReferenceInjector::class);
		/* @var $referenceInjector \SS6\ShopBundle\Component\DataFixture\ProductDataFixtureReferenceInjector */

		$referenceInjector->loadReferences($productDataFixtureLoader, $this->referenceRepository);

		$productsEditData = $productDataFixtureLoader->getProductsEditData();
		$productNo = 1;
		$productsByCatnum = [];
		foreach ($productsEditData as $productEditData) {
			$product = $this->createProduct(self::PRODUCT_PREFIX . $productNo, $productEditData);

			if ($product->getCatnum() !== null) {
				$productsByCatnum[$product->getCatnum()] = $product;
			}
			$productNo++;
		}

		$this->createVariants($productsByCatnum);

		$this->runRecalculators();
	}

	private function runRecalculators() {
		$productAvailabilityRecalculator = $this->get(ProductAvailabilityRecalculator::class);
		/* @var $productAvailabilityRecalculator \SS6\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculator */
		$productVisibilityFacade = $this->get(ProductVisibilityFacade::class);
		/* @var $productVisibilityFacade \SS6\ShopBundle\Model\Product\ProductVisibilityFacade */
		$productPriceRecalculator = $this->get(ProductPriceRecalculator::class);
		/* @var $productPriceRecalculator \SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator */
		$em = $this->get(EntityManager::class);
		/* @var $em \Doctrine\ORM\EntityManager */
		$em->clear();

		$productAvailabilityRecalculator->runAllScheduledRecalculations();
		$productPriceRecalculator->runAllScheduledRecalculations();
		$productVisibilityFacade->refreshProductsVisibility();
		// Main variant is set for recalculations after change of variants visibility.
		$productPriceRecalculator->runAllScheduledRecalculations();
	}

	/**
	 * @param string $referenceName
	 * @param \SS6\ShopBundle\Model\Product\ProductEditData $productEditData
	 * @return \SS6\ShopBundle\Model\Product\Product
	 */
	private function createProduct($referenceName, ProductEditData $productEditData) {
		$productEditFacade = $this->get(ProductEditFacade::class);
		/* @var $productEditFacade \SS6\ShopBundle\Model\Product\ProductEditFacade */

		$product = $productEditFacade->create($productEditData);

		$this->addReference($referenceName, $product);

		return $product;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product[catnum] $productsByCatnum
	 */
	private function createVariants(array $productsByCatnum) {
		$loaderService = $this->get(ProductDataFixtureLoader::class);
		/* @var $loaderService \SS6\ShopBundle\DataFixtures\Demo\ProductDataFixtureLoader */
		$productVariantFacade = $this->get(ProductVariantFacade::class);
		/* @var $productVariantFacade \SS6\ShopBundle\Model\Product\ProductVariantFacade */

		$variantCatnumsByMainVariantCatnum = $loaderService->getVariantCatnumsIndexedByMainVariantCatnum();

		foreach ($variantCatnumsByMainVariantCatnum as $mainVariantCatnum => $variantsCatnums) {
			$mainProduct = $productsByCatnum[$mainVariantCatnum];
			/* @var $mainProduct \SS6\ShopBundle\Model\Product\Product */

			$variants = [];
			foreach ($variantsCatnums as $variantCatnum) {
				$variants[] = $productsByCatnum[$variantCatnum];
			}

			$productVariantFacade->createVariant($mainProduct, $variants);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDependencies() {
		return ProductDataFixtureReferenceInjector::getDependencies();
	}

}
