<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\DataFixtures\Demo\ProductDataFixture;
use SS6\ShopBundle\Model\Product\TopProduct\TopProductData;
use SS6\ShopBundle\Model\Product\TopProduct\TopProductFacade;

class TopProductDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$topProductReferenceNames = [
			ProductDataFixture::PRODUCT_PREFIX . '1',
			ProductDataFixture::PRODUCT_PREFIX . '17',
			ProductDataFixture::PRODUCT_PREFIX . '9',
		];
		foreach ($topProductReferenceNames as $productReferenceName) {
			$this->createTopProduct($productReferenceName);
		}
	}

	/**
	 * @param string $productReferenceName
	 */
	private function createTopProduct($productReferenceName) {
		$topProductFacade = $this->get(TopProductFacade::class);
		/* @var $topProductFacade \SS6\ShopBundle\Model\Product\TopProduct\TopProductFacade */

		$topProductData = new TopProductData();
		$topProductData->product = $this->getReference($productReferenceName);

		$topProductFacade->create($topProductData, Domain::FIRST_DOMAIN_ID);
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
