<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Model\Order\PromoCode\PromoCodeData;
use SS6\ShopBundle\Model\Order\PromoCode\PromoCodeFacade;

class PromoCodeDataFixture extends AbstractReferenceFixture {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$promoCodeFacade = $this->get(PromoCodeFacade::class);
		/* @var $promoCodeFacade \SS6\ShopBundle\Model\Order\PromoCode\PromoCodeFacade */
		$promoCodeFacade->create(new PromoCodeData('test', 10.0));
	}
}
