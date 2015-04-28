<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\DataFixtures\Demo\ProductDataFixture;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\TopProduct\TopProduct;
use SS6\ShopBundle\Model\Product\TopProduct\TopProductData;

class TopProductDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$topProductsOnDomainData = [
			// $productReferenceName => $domainId
			ProductDataFixture::PRODUCT_PREFIX . '1' => 1,
			ProductDataFixture::PRODUCT_PREFIX . '17' => 1,
			ProductDataFixture::PRODUCT_PREFIX . '9' => 1,
			ProductDataFixture::PRODUCT_PREFIX . '14' => 2,
			ProductDataFixture::PRODUCT_PREFIX . '10' => 2,
			ProductDataFixture::PRODUCT_PREFIX . '7' => 2,
		];
		foreach ($topProductsOnDomainData as $productReferenceName => $domainId) {
			$product = $this->getReference($productReferenceName);
			$this->createTopProduct($manager, $product, $domainId);
		}

		$manager->flush();
	}

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param int $domainId
	 */
	private function createTopProduct(ObjectManager $manager, Product $product, $domainId) {
		$topProductData = new TopProductData();
		$topProductData->product = $product;

		$topProduct = new TopProduct($domainId, $topProductData);

		$manager->persist($topProduct);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDependencies() {
		return [
			ProductDataFixture::class,
		];
	}
}
