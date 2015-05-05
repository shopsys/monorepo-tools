<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Model\Advert\Advert;
use SS6\ShopBundle\Model\Advert\AdvertData;
use SS6\ShopBundle\Model\Advert\AdvertEditFacade;
use SS6\ShopBundle\Model\Advert\AdvertPosition;

class AdvertDataFixture extends AbstractReferenceFixture {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {

		$advertData = new AdvertData();
		$advertData->domainId = 2;
		$advertData->name = 'Ukázková reklama';
		$advertData->type = Advert::TYPE_CODE;
		$advertData->hidden = false;
		$advertData->positionName = AdvertPosition::POSITION_FOOTER;
		// @codingStandardsIgnoreStart
		$advertData->code = '<a href="http://www.seznam.cz/"><img src="http://archive.womadelaide.com.au/2013/media/W1siZiIsIjIwMTMvMDMvMDMvMTdfNTZfNTJfNDA5X2ludGVybm9kZV9zdHJlYW1pbmdfYmFubmVyX3dpZGUuanBnIl0sWyJwIiwidGh1bWIiLCIxMTIweCJdXQ/internode-streaming-banner-wide.jpg" alt="banner" /></a>';
		// @codingStandardsIgnoreStop
		$this->createAdvert($advertData);

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
