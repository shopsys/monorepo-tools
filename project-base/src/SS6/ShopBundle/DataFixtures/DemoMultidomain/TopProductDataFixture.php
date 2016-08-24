<?php

namespace SS6\ShopBundle\DataFixtures\DemoMultidomain;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\DataFixtures\Demo\ProductDataFixture as DemoProductDataFixture;
use SS6\ShopBundle\Model\Product\TopProduct\TopProductData;
use SS6\ShopBundle\Model\Product\TopProduct\TopProductFacade;

class TopProductDataFixture extends AbstractReferenceFixture {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$topProductReferenceNamesOnDomain2 = [
			DemoProductDataFixture::PRODUCT_PREFIX . '14',
			DemoProductDataFixture::PRODUCT_PREFIX . '10',
			DemoProductDataFixture::PRODUCT_PREFIX . '7',
		];

		foreach ($topProductReferenceNamesOnDomain2 as $productReferenceName) {
			$this->createTopProduct($productReferenceName, 2);
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

}
