<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Model\Product\Brand\BrandData;
use SS6\ShopBundle\Model\Product\Brand\BrandFacade;

class BrandDataFixture extends AbstractReferenceFixture {

	const APPLE = 'brand_apple';
	const CANON = 'brand_canon';
	const LG = 'brand_lg';
	const PHILIPS = 'brand_philips';

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$brandData = new BrandData();
		$brandData->name = 'Apple';
		$this->createBrand(self::APPLE, $brandData);

		$brandData->name = 'Canon';
		$this->createBrand(self::CANON, $brandData);

		$brandData->name = 'LG';
		$this->createBrand(self::LG, $brandData);

		$brandData->name = 'Philips';
		$this->createBrand(self::PHILIPS, $brandData);
	}

	/**
	 * @param string $referenceName
	 * @param \SS6\ShopBundle\Model\Product\Brand\BrandData $brandData
	 */
	private function createBrand($referenceName, BrandData $brandData) {
		$brandFacade = $this->get(BrandFacade::class);
		/* @var $brandFacade \SS6\ShopBundle\Model\Product\Brand\BrandFacade */

		$brand = $brandFacade->create($brandData);
		$this->addReference($referenceName, $brand);
	}

}
