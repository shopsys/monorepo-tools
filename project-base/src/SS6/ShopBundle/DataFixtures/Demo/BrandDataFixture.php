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
	const SENCOR = 'brand_sencor';
	const A4TECH = 'brand_a4tech';
	const BROTHER = 'brand_brother';
	const VERBATIM = 'brand_verbatim';
	const DLINK = 'brand_dlink';
	const DEFENDER = 'brand_defender';
	const DELONGHI = 'brand_delonghi';
	const GENIUS = 'brand_genius';
	const GIGABYTE = 'brand_gigabyte';
	const HP = 'brand_hp';
	const HTC = 'brand_htc';
	const JURA = 'brand_jura';
	const LOGITECH = 'brand_logitech';
	const MICROSOFT = 'brand_microsoft';
	const SAMSUNG = 'brand_samsung';
	const SONY = 'brand_sony';
	const ORAVA = 'brand_orava';
	const OLYMPUS = 'brand_olympus';

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

		$brandData->name = 'Sencor';
		$this->createBrand(self::SENCOR, $brandData);

		$brandData->name = 'A4tech';
		$this->createBrand(self::A4TECH, $brandData);

		$brandData->name = 'Brother';
		$this->createBrand(self::BROTHER, $brandData);

		$brandData->name = 'Verbatim';
		$this->createBrand(self::VERBATIM, $brandData);

		$brandData->name = 'Dlink';
		$this->createBrand(self::DLINK, $brandData);

		$brandData->name = 'Defender';
		$this->createBrand(self::DEFENDER, $brandData);

		$brandData->name = 'DeLonghi';
		$this->createBrand(self::DELONGHI, $brandData);

		$brandData->name = 'Genius';
		$this->createBrand(self::GENIUS, $brandData);

		$brandData->name = 'Gigabyte';
		$this->createBrand(self::GIGABYTE, $brandData);

		$brandData->name = 'HP';
		$this->createBrand(self::HP, $brandData);

		$brandData->name = 'HTC';
		$this->createBrand(self::HTC, $brandData);

		$brandData->name = 'JURA';
		$this->createBrand(self::JURA, $brandData);

		$brandData->name = 'Logitech';
		$this->createBrand(self::LOGITECH, $brandData);

		$brandData->name = 'Microsoft';
		$this->createBrand(self::MICROSOFT, $brandData);

		$brandData->name = 'Samsung';
		$this->createBrand(self::SAMSUNG, $brandData);

		$brandData->name = 'SONY';
		$this->createBrand(self::SONY, $brandData);

		$brandData->name = 'Orava';
		$this->createBrand(self::ORAVA, $brandData);

		$brandData->name = 'Olympus';
		$this->createBrand(self::OLYMPUS, $brandData);
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
