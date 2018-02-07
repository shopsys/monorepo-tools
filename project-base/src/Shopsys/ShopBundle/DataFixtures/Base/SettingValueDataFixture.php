<?php

namespace Shopsys\ShopBundle\DataFixtures\Base;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Setting\Setting;
use Shopsys\ShopBundle\Component\Setting\SettingValue;
use Shopsys\ShopBundle\Component\String\HashGenerator;
use Shopsys\ShopBundle\Model\Mail\Setting\MailSetting;
use Shopsys\ShopBundle\Model\Pricing\PricingSetting;
use Shopsys\ShopBundle\Model\Pricing\Vat\Vat;
use Shopsys\ShopBundle\Model\Seo\SeoSettingFacade;

class SettingValueDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{
    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function load(ObjectManager $manager)
    {
        $vat = $this->getReference(VatDataFixture::VAT_HIGH);
        /* @var $vat \Shopsys\ShopBundle\Model\Pricing\Vat\Vat */
        $pricingGroup1 = $this->getReference(PricingGroupDataFixture::PRICING_GROUP_ORDINARY_DOMAIN_1);
        /* @var $pricingGroup2 \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup */
        $defaultCurrency = $this->getReference(CurrencyDataFixture::CURRENCY_CZK);
        /* @var $defaultCurrency \Shopsys\ShopBundle\Model\Pricing\Currency\Currency */
        $defaultInStockAvailability = $this->getReference(AvailabilityDataFixture::AVAILABILITY_IN_STOCK);
        /* @var $defaultInStockAvailability \Shopsys\ShopBundle\Model\Product\Availability\Availability */
        $hashGenerator = $this->get(HashGenerator::class);
        /* @var $hashGenerator \Shopsys\ShopBundle\Component\String\HashGenerator */
        $defaultUnit = $this->getReference(UnitDataFixture::UNIT_PIECES);
        /* @var $defaultUnit \Shopsys\ShopBundle\Model\Product\Unit\Unit */

        $orderSentText = '
            <p>
                Order number {number} has been sent, thank you for your purchase.
                We will contact you about next order status. <br /><br />
                <a href="{order_detail_url}">Track</a> the status of your order. <br />
                {transport_instructions} <br />
                {payment_instructions} <br />
            </p>
        ';

        // @codingStandardsIgnoreStart
        $manager->persist(new SettingValue(PricingSetting::INPUT_PRICE_TYPE, PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT, SettingValue::DOMAIN_ID_COMMON));
        $manager->persist(new SettingValue(PricingSetting::ROUNDING_TYPE, PricingSetting::ROUNDING_TYPE_INTEGER, SettingValue::DOMAIN_ID_COMMON));
        $manager->persist(new SettingValue(Vat::SETTING_DEFAULT_VAT, $vat->getId(), SettingValue::DOMAIN_ID_COMMON));
        $manager->persist(new SettingValue(Setting::ORDER_SENT_PAGE_CONTENT, $orderSentText, Domain::FIRST_DOMAIN_ID));
        $manager->persist(new SettingValue(MailSetting::MAIN_ADMIN_MAIL, 'no-reply@netdevelo.cz', Domain::FIRST_DOMAIN_ID));
        $manager->persist(new SettingValue(MailSetting::MAIN_ADMIN_MAIL_NAME, 'Shopsys', Domain::FIRST_DOMAIN_ID));
        $manager->persist(new SettingValue(Setting::DEFAULT_PRICING_GROUP, $pricingGroup1->getId(), Domain::FIRST_DOMAIN_ID));
        $manager->persist(new SettingValue(PricingSetting::DEFAULT_CURRENCY, $defaultCurrency->getId(), SettingValue::DOMAIN_ID_COMMON));
        $manager->persist(new SettingValue(PricingSetting::DEFAULT_DOMAIN_CURRENCY, $defaultCurrency->getId(), Domain::FIRST_DOMAIN_ID));
        $manager->persist(new SettingValue(Setting::DEFAULT_AVAILABILITY_IN_STOCK, $defaultInStockAvailability->getId(), SettingValue::DOMAIN_ID_COMMON));
        $manager->persist(new SettingValue(PricingSetting::FREE_TRANSPORT_AND_PAYMENT_PRICE_LIMIT, null, Domain::FIRST_DOMAIN_ID));
        $manager->persist(new SettingValue(SeoSettingFacade::SEO_META_DESCRIPTION_MAIN_PAGE, 'ShopSys Framework - the best solution for your eshop.', Domain::FIRST_DOMAIN_ID));
        $manager->persist(new SettingValue(SeoSettingFacade::SEO_TITLE_MAIN_PAGE, 'ShopSys Framework - Title page', Domain::FIRST_DOMAIN_ID));
        $manager->persist(new SettingValue(SeoSettingFacade::SEO_TITLE_ADD_ON, '| Demo eshop', Domain::FIRST_DOMAIN_ID));
        $manager->persist(new SettingValue(Setting::TERMS_AND_CONDITIONS_ARTICLE_ID, null, Domain::FIRST_DOMAIN_ID));
        $manager->persist(new SettingValue(Setting::COOKIES_ARTICLE_ID, null, Domain::FIRST_DOMAIN_ID));
        $manager->persist(new SettingValue(Setting::DOMAIN_DATA_CREATED, true, Domain::FIRST_DOMAIN_ID));
        $manager->persist(new SettingValue(Setting::FEED_HASH, $hashGenerator->generateHash(10), SettingValue::DOMAIN_ID_COMMON));
        $manager->persist(new SettingValue(Setting::DEFAULT_UNIT, $defaultUnit->getId(), SettingValue::DOMAIN_ID_COMMON));
        // @codingStandardsIgnoreStop

        $manager->flush();

        $this->clearSettingCache();
    }

    private function clearSettingCache()
    {
        $setting = $this->get(Setting::class);
        /* @var $setting \Shopsys\ShopBundle\Component\Setting\Setting */

        $setting->clearCache();
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return [
            AvailabilityDataFixture::class,
            VatDataFixture::class,
        ];
    }
}
