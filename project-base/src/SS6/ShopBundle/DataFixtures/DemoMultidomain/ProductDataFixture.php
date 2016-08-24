<?php

namespace SS6\ShopBundle\DataFixtures\DemoMultidomain;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Component\DataFixture\PersistentReferenceFacade;
use SS6\ShopBundle\Component\DataFixture\ProductDataFixtureReferenceInjector;
use SS6\ShopBundle\DataFixtures\Demo\ProductDataFixtureCsvReader;
use SS6\ShopBundle\DataFixtures\Demo\ProductDataFixtureLoader;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductEditData;
use SS6\ShopBundle\Model\Product\ProductEditDataFactory;
use SS6\ShopBundle\Model\Product\ProductEditFacade;

class ProductDataFixture extends AbstractReferenceFixture {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$productDataFixtureLoader = $this->get(ProductDataFixtureLoader::class);
		/* @var $productDataFixtureLoader \SS6\ShopBundle\DataFixtures\Demo\ProductDataFixtureLoader */
		$referenceInjector = $this->get(ProductDataFixtureReferenceInjector::class);
		/* @var $referenceInjector \SS6\ShopBundle\Component\DataFixture\ProductDataFixtureReferenceInjector */
		$persistentReferenceFacade = $this->get(PersistentReferenceFacade::class);
		/* @var $persistentReferenceFacade \SS6\ShopBundle\Component\DataFixture\PersistentReferenceFacade */
		$productDataFixtureCsvReader = $this->get(ProductDataFixtureCsvReader::class);
		/* @var $productDataFixtureCsvReader \SS6\ShopBundle\DataFixtures\Demo\ProductDataFixtureCsvReader */
		$productEditFacade = $this->get(ProductEditFacade::class);
		/* @var $productEditFacade \SS6\ShopBundle\Model\Product\ProductEditFacade */

		$referenceInjector->loadReferences($productDataFixtureLoader, $persistentReferenceFacade);

		$csvRows = $productDataFixtureCsvReader->getProductDataFixtureCsvRows();
		foreach ($csvRows as $row) {
			$productCatnum = $productDataFixtureLoader->getCatnumFromRow($row);
			$product = $productEditFacade->getOneByCatnumExcludeMainVariants($productCatnum);
			$this->editProduct($product, $row);

			if ($product->isVariant() && $product->getCatnum() === $product->getMainVariant()->getCatnum()) {
				$this->editProduct($product->getMainVariant(), $row);
			}
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param array $row
	 */
	private function editProduct(Product $product, array $row) {
		$productEditDataFactory = $this->get(ProductEditDataFactory::class);
		/* @var $productEditDataFactory \SS6\ShopBundle\Model\Product\ProductEditDataFactory */
		$productEditFacade = $this->get(ProductEditFacade::class);
		/* @var $productEditFacade \SS6\ShopBundle\Model\Product\ProductEditFacade */
		$productDataFixtureLoader = $this->get(ProductDataFixtureLoader::class);
		/* @var $productDataFixtureLoader \SS6\ShopBundle\DataFixtures\Demo\ProductDataFixtureLoader */

		$productEditData = $productEditDataFactory->createFromProduct($product);
		$productDataFixtureLoader->updateProductEditDataFromCsvRowForSecondDomain($productEditData, $row);
		$productEditFacade->edit($product->getId(), $productEditData);
	}

}
