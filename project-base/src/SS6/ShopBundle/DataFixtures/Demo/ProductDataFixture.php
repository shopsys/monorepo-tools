<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\DataFixtures\Base\AvailabilityDataFixture;
use SS6\ShopBundle\DataFixtures\Base\DepartmentDataFixture;
use SS6\ShopBundle\DataFixtures\Base\VatDataFixture;
use SS6\ShopBundle\Model\Product\ProductEditData;

class ProductDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$loaderService = $this->get('ss6.shop.data_fixtures.product_data_fixture_loader');
		/* @var $loaderService ProductDataFixtureLoader */

		$vats = array(
			'high' => $this->getReference(VatDataFixture::VAT_HIGH),
			'low' => $this->getReference(VatDataFixture::VAT_LOW),
			'zero' => $this->getReference(VatDataFixture::VAT_ZERO)
		);
		$availabilities = array(
			'in-stock' => $this->getReference(AvailabilityDataFixture::IN_STOCK),
			'out-of-stock' => $this->getReference(AvailabilityDataFixture::OUT_OF_STOCK),
			'on-request' => $this->getReference(AvailabilityDataFixture::ON_REQUEST)
		);
		$departments = array(
			'1' => $this->getReference(DepartmentDataFixture::TV),
			'2' => $this->getReference(DepartmentDataFixture::PHOTO),
			'3' => $this->getReference(DepartmentDataFixture::PRINTERS),
			'4' => $this->getReference(DepartmentDataFixture::PC),
			'5' => $this->getReference(DepartmentDataFixture::PHONES),
			'6' => $this->getReference(DepartmentDataFixture::COFFEE),
			'7' => $this->getReference(DepartmentDataFixture::BOOKS),
			'8' => $this->getReference(DepartmentDataFixture::TOYS),
		);

		$loaderService->injectReferences($vats, $availabilities, $departments);
		$productsEditData = $loaderService->getProductsEditData();
		$productNo = 1;
		foreach ($productsEditData as $productEditData) {
			$this->createProduct($manager, 'product_' . $productNo, $productEditData);
			$productNo++;
		}

		$manager->flush();
	}

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 * @param string $referenceName
	 * @param \SS6\ShopBundle\Model\Product\ProductEditData $productEditData
	 */
	private function createProduct(ObjectManager $manager, $referenceName, ProductEditData $productEditData) {
		$productEditFacade = $this->get('ss6.shop.product.product_edit_facade');
		/* @var $productEditFacade \SS6\ShopBundle\Model\Product\ProductEditFacade */

		$this->persistParemeters($manager, $productEditData->parameters);

		$product = $productEditFacade->create($productEditData);

		$this->addReference($referenceName, $product);
	}

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ProductParameterValueData[] $productParameterValuesData
	 */
	private function persistParemeters(ObjectManager $manager, array $productParameterValuesData) {
		foreach ($productParameterValuesData as $productParameterValueData) {
			$manager->persist($productParameterValueData->getParameter());
		}

		// Doctrine doesn't know how to resolve persisting order and fill autoincrement IDs
		// into foreign keys of related entities. That's why explicit flush() is needed.
		$manager->flush();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDependencies() {
		return array(
			VatDataFixture::class,
			AvailabilityDataFixture::class,
			DepartmentDataFixture::class,
		);
	}

}
