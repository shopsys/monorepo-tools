<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\DataFixtures\Base\AvailabilityDataFixture;
use SS6\ShopBundle\DataFixtures\Base\FlagDataFixture;
use SS6\ShopBundle\DataFixtures\Base\VatDataFixture;
use SS6\ShopBundle\DataFixtures\Demo\CategoryDataFixture;
use SS6\ShopBundle\Model\Product\ProductEditData;

class ProductDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$loaderService = $this->get('ss6.shop.data_fixtures.product_data_fixture_loader');
		/* @var $loaderService \SS6\ShopBundle\DataFixtures\Demo\ProductDataFixtureLoader */

		$vats = [
			'high' => $this->getReference(VatDataFixture::VAT_HIGH),
			'low' => $this->getReference(VatDataFixture::VAT_LOW),
			'zero' => $this->getReference(VatDataFixture::VAT_ZERO),
		];
		$availabilities = [
			'in-stock' => $this->getReference(AvailabilityDataFixture::IN_STOCK),
			'out-of-stock' => $this->getReference(AvailabilityDataFixture::OUT_OF_STOCK),
			'on-request' => $this->getReference(AvailabilityDataFixture::ON_REQUEST),
		];
		$categories = [
			'1' => $this->getReference(CategoryDataFixture::TV),
			'2' => $this->getReference(CategoryDataFixture::PHOTO),
			'3' => $this->getReference(CategoryDataFixture::PRINTERS),
			'4' => $this->getReference(CategoryDataFixture::PC),
			'5' => $this->getReference(CategoryDataFixture::PHONES),
			'6' => $this->getReference(CategoryDataFixture::COFFEE),
			'7' => $this->getReference(CategoryDataFixture::BOOKS),
			'8' => $this->getReference(CategoryDataFixture::TOYS),
		];

		$flags = [
			'action' => $this->getReference(FlagDataFixture::ACTION_PRODUCT),
			'new' => $this->getReference(FlagDataFixture::NEW_PRODUCT),
			'top' => $this->getReference(FlagDataFixture::TOP_PRODUCT),
		];

		$loaderService->injectReferences($vats, $availabilities, $categories, $flags);
		$productsEditData = $loaderService->getProductsEditData();
		$productNo = 1;
		foreach ($productsEditData as $productEditData) {
			$this->createProduct('product_' . $productNo, $productEditData);
			$productNo++;
		}

		$manager->flush();
	}

	/**
	 * @param string $referenceName
	 * @param \SS6\ShopBundle\Model\Product\ProductEditData $productEditData
	 */
	private function createProduct($referenceName, ProductEditData $productEditData) {
		$productEditFacade = $this->get('ss6.shop.product.product_edit_facade');
		/* @var $productEditFacade \SS6\ShopBundle\Model\Product\ProductEditFacade */

		$product = $productEditFacade->create($productEditData);

		$this->addReference($referenceName, $product);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDependencies() {
		return [
			VatDataFixture::class,
			AvailabilityDataFixture::class,
			CategoryDataFixture::class,
		];
	}

}
