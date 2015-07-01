<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\DataFixtures\Demo\ProductDataFixture;
use SS6\ShopBundle\Model\Product\TopProduct\TopProductData;
use SS6\ShopBundle\Model\Product\TopProduct\TopProductFacade;

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
			$this->createTopProduct($productReferenceName, $domainId);
		}
	}

	/**
	 * @param string $productReferenceName
	 * @param int $domainId
	 */
	private function createTopProduct($productReferenceName, $domainId) {
		$topProductFacade = $this->get(TopProductFacade::class);
		/* @var $topProductFacade \SS6\ShopBundle\Model\Product\TopProduct\TopProductFacade */

		$topProductData = new TopProductData();
		$topProductData->product = $this->getReference($productReferenceName);

		$topProductFacade->create($topProductData, $domainId);
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
