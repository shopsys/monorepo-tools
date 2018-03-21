<?php

namespace Shopsys\FrameworkBundle\DataFixtures\DemoMultidomain;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Component\Setting\Setting;
use Shopsys\FrameworkBundle\DataFixtures\Base\CurrencyDataFixture;
use Shopsys\FrameworkBundle\Model\Pricing\PricingSetting;
use Shopsys\FrameworkBundle\Model\Seo\SeoSettingFacade;

class SettingValueDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{
    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $setting = $this->get(Setting::class);
        /* @var $setting \Shopsys\FrameworkBundle\Component\Setting\Setting */
        $setting->clearCache();

        $termsAndConditionsDomain2 = $this->getReference(ArticleDataFixture::ARTICLE_TERMS_AND_CONDITIONS_2);
        /* @var $termsAndConditionsDomain2 \Shopsys\FrameworkBundle\Model\Article\Article */
        $setting->setForDomain(Setting::TERMS_AND_CONDITIONS_ARTICLE_ID, $termsAndConditionsDomain2->getId(), 2);

        $privacyPolicyDomain2 = $this->getReference(ArticleDataFixture::ARTICLE_PRIVACY_POLICY_2);
        /* @var $privacyPolicyDomain2 \Shopsys\FrameworkBundle\Model\Article\Article */
        $setting->setForDomain(Setting::PRIVACY_POLICY_ARTICLE_ID, $privacyPolicyDomain2->getId(), 2);

        $cookiesDomain2 = $this->getReference(ArticleDataFixture::ARTICLE_COOKIES_2);
        /* @var $cookiesDomain2 \Shopsys\FrameworkBundle\Model\Article\Article */
        $setting->setForDomain(Setting::COOKIES_ARTICLE_ID, $cookiesDomain2->getId(), 2);

        /* @var $pricingGroup2 \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup */
        $pricingGroup2 = $this->getReference(PricingGroupDataFixture::PRICING_GROUP_ORDINARY_DOMAIN_2);
        $setting->setForDomain(Setting::DEFAULT_PRICING_GROUP, $pricingGroup2->getId(), 2);

        $orderSentText = '
            <p>
                Objednávka číslo {number} byla odeslána, děkujeme za Váš nákup.
                Budeme Vás kontaktovat o dalším průběhu vyřizování. <br /><br />
                Uschovejte si permanentní <a href="{order_detail_url}">odkaz na detail objednávky</a>. <br />
                {transport_instructions} <br />
                {payment_instructions} <br />
            </p>
        ';
        $setting->setForDomain(Setting::ORDER_SENT_PAGE_CONTENT, $orderSentText, 2);

        /* @var $defaultCurrency \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency */
        $domain2DefaultCurrency = $this->getReference(CurrencyDataFixture::CURRENCY_EUR);
        $setting->setForDomain(PricingSetting::DEFAULT_DOMAIN_CURRENCY, $domain2DefaultCurrency->getId(), 2);

        $setting->setForDomain(
            SeoSettingFacade::SEO_META_DESCRIPTION_MAIN_PAGE,
            'Shopsys Framework - nejlepší řešení pro váš internetový obchod.',
            2
        );
        $setting->setForDomain(SeoSettingFacade::SEO_TITLE_MAIN_PAGE, 'Shopsys Framework - Titulní strana', 2);
        $setting->setForDomain(SeoSettingFacade::SEO_TITLE_ADD_ON, '| Demo obchod', 2);

        $personalDataSiteContent = 'Zadáním e-mailu níže si můžete nechat zobrazit vaše osobní údaje, která evidujeme v našem internetovém obchodu.
         Pro ověření vaší totožnosti vám po zadání e-mailové adresy bude zaslán e-mail s odkazem. 
         Klikem na odkaz se dostanete na stránku s přehledem těchto osobních údajů - půjde o všechny údaje evidované k dané e-mailové adrese.';

        $setting->setForDomain(Setting::PERSONAL_DATA_SITE_CONTENT, $personalDataSiteContent, 2);
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return [
            ArticleDataFixture::class,
            PricingGroupDataFixture::class,
        ];
    }
}
