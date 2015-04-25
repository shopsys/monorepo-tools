<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Model\Advert\AdvertData;
use SS6\ShopBundle\Model\Advert\AdvertEditFacade;

class AdvertDataFixture extends AbstractReferenceFixture {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		// @codingStandardsIgnoreStart
		$advertData = new AdvertData();
		$advertData->domainId = 1;
		$advertData->name = 'Reklamní plocha index';
		$advertData->type = 'code';
		$advertData->hidden = false;
		$advertData->positionName = 'header';
		$advertData->code = '<a href="http://www.seznam.cz/"><img src="http://archive.womadelaide.com.au/2013/media/W1siZiIsIjIwMTMvMDMvMDMvMTdfNTZfNTJfNDA5X2ludGVybm9kZV9zdHJlYW1pbmdfYmFubmVyX3dpZGUuanBnIl0sWyJwIiwidGh1bWIiLCIxMTIweCJdXQ/internode-streaming-banner-wide.jpg" alt="banner" /></a>';
		$this->createAdvert($advertData);

		// @codingStandardsIgnoreStop

		$manager->flush();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Advert\AdvertData $advertData
	 */
	private function createAdvert(AdvertData $advertData) {
		$advertEdditFacade = $this->get(AdvertEditFacade::class);
		/* @var $advertEdditFacade \SS6\ShopBundle\Model\Advert\AdvertEditFacade */
		$advertEdditFacade->create($advertData);
	}
}
