<?php

namespace SS6\ShopBundle\DataFixtures\Base;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\DataFixtures\Base\CurrencyDataFixture;
use SS6\ShopBundle\DataFixtures\Base\PricingGroupDataFixture;
use SS6\ShopBundle\DataFixtures\Base\VatDataFixture;
use SS6\ShopBundle\Model\Mail\Setting\MailSetting;
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
		$pricingGroup1 = $this->getReference(PricingGroupDataFixture::ORDINARY_DOMAIN_1);
		/* @var $pricingGroup2 \SS6\ShopBundle\Model\Pricing\Group\PricingGroup */
		$pricingGroup2 = $this->getReference(PricingGroupDataFixture::ORDINARY_DOMAIN_2);
		/* @var $pricingGroup2 \SS6\ShopBundle\Model\Pricing\Group\PricingGroup */
		$defaultCurrency = $this->getReference(CurrencyDataFixture::CURRENCY_CZK);
		/* @var $defaultCurrency \SS6\ShopBundle\Model\Pricing\Currency\Currency */

		// @codingStandardsIgnoreStart
		$manager->persist(new SettingValue(PricingSetting::INPUT_PRICE_TYPE, PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT, SettingValue::DOMAIN_ID_COMMON));
		$manager->persist(new SettingValue(PricingSetting::ROUNDING_TYPE, PricingSetting::ROUNDING_TYPE_INTEGER, SettingValue::DOMAIN_ID_COMMON));
		$manager->persist(new SettingValue(Vat::SETTING_DEFAULT_VAT, $vat->getId(), SettingValue::DOMAIN_ID_COMMON));
		$manager->persist(new SettingValue(Setting::ORDER_SUBMITTED_SETTING_NAME, '<p>Objednávka byla odeslána, děkujeme za Váš nákup. Budeme Vás kontaktovat o dalším průběhu vyřizování.</p>', SettingValue::DOMAIN_ID_COMMON));
		$manager->persist(new SettingValue(MailSetting::MAIN_ADMIN_MAIL, 'no-reply@netdevelo.cz', 1));
		$manager->persist(new SettingValue(MailSetting::MAIN_ADMIN_MAIL_NAME, 'Shopsys', 1));
		$manager->persist(new SettingValue(MailSetting::MAIN_ADMIN_MAIL, 'no-reply@netdevelo.cz', 2));
		$manager->persist(new SettingValue(MailSetting::MAIN_ADMIN_MAIL_NAME, '2.Shopsys', 2));
		$manager->persist(new SettingValue(Setting::DEFAULT_PRICING_GROUP, $pricingGroup1->getId(), 1));
		$manager->persist(new SettingValue(Setting::DEFAULT_PRICING_GROUP, $pricingGroup2->getId(), 2));
		$manager->persist(new SettingValue(PricingSetting::DEFAULT_CURRENCY, $defaultCurrency->getId(), SettingValue::DOMAIN_ID_COMMON));
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
