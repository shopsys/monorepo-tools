<?php

namespace SS6\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Model\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Model\Setting\Setting;
use SS6\ShopBundle\Model\Setting\SettingValue;

class SettingValueDataFixture extends AbstractReferenceFixture {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {

		$manager->persist(new SettingValue(Setting::INPUT_PRICE_TYPE, Setting::INPUT_PRICE_TYPE_WITHOUT_VAT));

		$manager->flush();
	}

}
