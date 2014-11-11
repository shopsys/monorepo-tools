<?php

namespace SS6\ShopBundle\DataFixtures\Base;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\DataFixtures\Base\VatDataFixture;
use SS6\ShopBundle\Model\Pricing\PricingSetting;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Setting\SettingValue;
use SS6\ShopBundle\Model\Setting\Setting;

class SettingValueDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$vat = $this->getReference(VatDataFixture::VAT_HIGH);
		/* @var $vat \SS6\ShopBundle\Model\Pricing\Vat\Vat */

		$manager->persist(new SettingValue(PricingSetting::INPUT_PRICE_TYPE, PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT));
		$manager->persist(new SettingValue(PricingSetting::ROUNDING_TYPE, PricingSetting::ROUNDING_TYPE_INTEGER));
		$manager->persist(new SettingValue(Vat::SETTING_DEFAULT_VAT, $vat->getId()));
		// @codingStandardsIgnoreStart
		$manager->persist(new SettingValue(Setting::ORDER_SUBMITTED_SETTING_NAME, '<p>Objednávka byla odeslána, děkujeme za Váš nákup. Budeme Vás kontaktovat o dalším průběhu vyřizování.</p>'));
		// @codingStandardsIgnoreStop

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
