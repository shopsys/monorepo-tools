<?php

namespace Shopsys\ShopBundle\DataFixtures\DemoMultidomain;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\ShopBundle\Component\Setting\Setting;
use Shopsys\ShopBundle\DataFixtures\Base\CurrencyDataFixture;
use Shopsys\ShopBundle\DataFixtures\DemoMultidomain\ArticleDataFixture;
use Shopsys\ShopBundle\DataFixtures\DemoMultidomain\PricingGroupDataFixture;
use Shopsys\ShopBundle\Model\Pricing\PricingSetting;
use Shopsys\ShopBundle\Model\Seo\SeoSettingFacade;

class SettingValueDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {
        $setting = $this->get(Setting::class);
        /* @var $setting \Shopsys\ShopBundle\Component\Setting\Setting */
        $setting->clearCache();

        $termsAndConditionsDomain2 = $this->getReference(ArticleDataFixture::TERMS_AND_CONDITIONS_2);
        /* @var $termsAndConditionsDomain2 \Shopsys\ShopBundle\Model\Article\Article */
        $setting->setForDomain(Setting::TERMS_AND_CONDITIONS_ARTICLE_ID, $termsAndConditionsDomain2->getId(), 2);

        $cookiesDomain2 = $this->getReference(ArticleDataFixture::COOKIES_2);
        /* @var $cookiesDomain2 \Shopsys\ShopBundle\Model\Article\Article */
        $setting->setForDomain(Setting::COOKIES_ARTICLE_ID, $cookiesDomain2->getId(), 2);

        /* @var $pricingGroup2 \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup */
        $pricingGroup2 = $this->getReference(PricingGroupDataFixture::ORDINARY_DOMAIN_2);
        $setting->setForDomain(Setting::DEFAULT_PRICING_GROUP, $pricingGroup2->getId(), 2);

        $orderSentTextEn = '
            <p>
                Order number {number} has been sent, thank you for your purchase.
                We will contact you about next order status. <br /><br />
                <a href="{order_detail_url}">Track</a> the status of your order. <br />
                {transport_instructions} <br />
                {payment_instructions} <br />
            </p>
        ';
        $setting->setForDomain(Setting::ORDER_SUBMITTED_SETTING_NAME, $orderSentTextEn, 2);

        /* @var $defaultCurrency \Shopsys\ShopBundle\Model\Pricing\Currency\Currency */
        $domain2DefaultCurrency = $this->getReference(CurrencyDataFixture::CURRENCY_EUR);
        $setting->setForDomain(PricingSetting::DEFAULT_DOMAIN_CURRENCY, $domain2DefaultCurrency->getId(), 2);

        $setting->setForDomain(
            SeoSettingFacade::SEO_META_DESCRIPTION_MAIN_PAGE,
            'ShopSys Framework - the best solution for your eshop.',
            2
        );
        $setting->setForDomain(SeoSettingFacade::SEO_TITLE_MAIN_PAGE, 'ShopSys Framework - Title page', 2);
        $setting->setForDomain(SeoSettingFacade::SEO_TITLE_ADD_ON, '| Demo eshop', 2);
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
