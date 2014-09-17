<?php

namespace SS6\ShopBundle\DataFixtures\Base;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\DataFixtures\Base\VatDataFixture;
use SS6\ShopBundle\Model\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Setting\Setting;
use SS6\ShopBundle\Model\Setting\SettingValue;

class SettingValueDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$vat = $this->getReference(VatDataFixture::VAT_HIGH);
		/* @var $vat \SS6\ShopBundle\Model\Pricing\Vat\Vat */

		$manager->persist(new SettingValue(Setting::INPUT_PRICE_TYPE, Setting::INPUT_PRICE_TYPE_WITHOUT_VAT));
		$manager->persist(new SettingValue(Vat::SETTING_DEFAULT_VAT, $vat->getId()));

		$manager->flush();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDependencies() {
		return array(
			VatDataFixture::class,
		);
	}

}
