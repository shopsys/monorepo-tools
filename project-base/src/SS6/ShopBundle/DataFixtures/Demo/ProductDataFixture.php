<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\DataFixtures\Base\AvailabilityDataFixture;
use SS6\ShopBundle\DataFixtures\Base\VatDataFixture;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductData;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class ProductDataFixture extends AbstractFixture implements DependentFixtureInterface, ContainerAwareInterface {

	/**
	 * @var \Symfony\Component\DependencyInjection\ContainerInterface
	 */
	private $container;

	/**
	 * @param DependencyInjection\ContainerInterface $container
	 */
	public function setContainer(ContainerInterface $container = null) {
		$this->container = $container;
	}

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$loaderService = $this->container->get('ss6.shop.data_fixtures.product_data_fixture_loader');
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

		$loaderService->injectReferences($vats, $availabilities);
		$productsData = $loaderService->getProductsData();
		$productNo = 1;
		foreach ($productsData as $productData) {
			$this->createProduct($manager, 'product_' . $productNo, $productData);
			$productNo++;
		}

		$manager->flush();
	}

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 * @param string $referenceName
	 * @param \SS6\ShopBundle\Model\Product\ProductData $productData
	 */
	private function createProduct(ObjectManager $manager, $referenceName, ProductData $productData) {
		$product = new Product($productData);

		$manager->persist($product);
		$this->addReference($referenceName, $product);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDependencies() {
		return array(
			VatDataFixture::class,
			AvailabilityDataFixture::class,
		);
	}

}
