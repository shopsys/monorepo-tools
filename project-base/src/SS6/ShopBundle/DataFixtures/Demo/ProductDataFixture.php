<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Component\DataFixture\PersistentReferenceService;
use SS6\ShopBundle\Component\DataFixture\ProductDataFixtureReferenceInjector;
use SS6\ShopBundle\DataFixtures\Demo\ProductDataFixtureLoader;
use SS6\ShopBundle\Model\Product\ProductEditData;
use SS6\ShopBundle\Model\Product\ProductEditFacade;
use SS6\ShopBundle\Model\Product\ProductVariantFacade;

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
		$persistentReferenceService = $this->get(PersistentReferenceService::class);
		/* @var $persistentReferenceService \SS6\ShopBundle\Component\DataFixture\PersistentReferenceService */

		$referenceInjector->loadReferences($productDataFixtureLoader, $persistentReferenceService);

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

		$this->createVariants($productsByCatnum, $productNo);
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
	 * @param int $productNo
	 */
	private function createVariants(array $productsByCatnum, $productNo) {
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

			$mainVariant = $productVariantFacade->createVariant($mainProduct, $variants);
			$this->addReference(self::PRODUCT_PREFIX . $productNo, $mainVariant);
			$productNo++;
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDependencies() {
		return ProductDataFixtureReferenceInjector::getDependencies();
	}

}
