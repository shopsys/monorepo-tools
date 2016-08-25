<?php

namespace SS6\ShopBundle\DataFixtures\DemoMultidomain;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Component\Setting\Setting;
use SS6\ShopBundle\Component\Setting\SettingValue;
use SS6\ShopBundle\DataFixtures\DemoMultidomain\ArticleDataFixture;
use SS6\ShopBundle\DataFixtures\DemoMultidomain\PricingGroupDataFixture;

class SettingValueDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$termsAndConditionsDomain2 = $this->getReference(ArticleDataFixture::TERMS_AND_CONDITIONS_2);
		/* @var $termsAndConditionsDomain2 \SS6\ShopBundle\Model\Article\Article */
		$manager->persist(new SettingValue(Setting::TERMS_AND_CONDITIONS_ARTICLE_ID, $termsAndConditionsDomain2->getId(), 2));

		$cookiesDomain2 = $this->getReference(ArticleDataFixture::COOKIES_2);
		/* @var $cookiesDomain2 \SS6\ShopBundle\Model\Article\Article */
		$manager->persist(new SettingValue(Setting::COOKIES_ARTICLE_ID, $cookiesDomain2->getId(), 2));

		/* @var $pricingGroup2 \SS6\ShopBundle\Model\Pricing\Group\PricingGroup */
		$pricingGroup2 = $this->getReference(PricingGroupDataFixture::ORDINARY_DOMAIN_2);
		$manager->persist(new SettingValue(Setting::DEFAULT_PRICING_GROUP, $pricingGroup2->getId(), 2));

		$manager->flush();

		$this->clearSettingCache();
	}

	private function clearSettingCache() {
		$setting = $this->get(Setting::class);
		/* @var $setting \SS6\ShopBundle\Component\Setting\Setting */

		$setting->clearCache();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDependencies() {
		return [
			ArticleDataFixture::class,
			PricingGroupDataFixture::class,
		];
	}

}
