<?php

namespace Shopsys\FrameworkBundle\DataFixtures\DemoMultidomain;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Component\Setting\Setting;
use Shopsys\FrameworkBundle\DataFixtures\Demo\CurrencyDataFixture;
use Shopsys\FrameworkBundle\Model\Pricing\PricingSetting;
use Shopsys\FrameworkBundle\Model\Seo\SeoSettingFacade;

class SettingValueDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Setting\Setting
     */
    private $setting;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Setting\Setting $setting
     */
    public function __construct(Setting $setting)
    {
        $this->setting = $setting;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $termsAndConditionsDomain2 = $this->getReference(ArticleDataFixture::ARTICLE_TERMS_AND_CONDITIONS_2);
        /* @var $termsAndConditionsDomain2 \Shopsys\FrameworkBundle\Model\Article\Article */
        $this->setting->setForDomain(Setting::TERMS_AND_CONDITIONS_ARTICLE_ID, $termsAndConditionsDomain2->getId(), 2);

        $privacyPolicyDomain2 = $this->getReference(ArticleDataFixture::ARTICLE_PRIVACY_POLICY_2);
        /* @var $privacyPolicyDomain2 \Shopsys\FrameworkBundle\Model\Article\Article */
        $this->setting->setForDomain(Setting::PRIVACY_POLICY_ARTICLE_ID, $privacyPolicyDomain2->getId(), 2);

        $cookiesDomain2 = $this->getReference(ArticleDataFixture::ARTICLE_COOKIES_2);
        /* @var $cookiesDomain2 \Shopsys\FrameworkBundle\Model\Article\Article */
        $this->setting->setForDomain(Setting::COOKIES_ARTICLE_ID, $cookiesDomain2->getId(), 2);

        /* @var $pricingGroup2 \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup */
        $pricingGroup2 = $this->getReference(PricingGroupDataFixture::PRICING_GROUP_ORDINARY_DOMAIN_2);
        $this->setting->setForDomain(Setting::DEFAULT_PRICING_GROUP, $pricingGroup2->getId(), 2);

        $orderSentText = '
            <p>
                Objednávka číslo {number} byla odeslána, děkujeme za Váš nákup.
                Budeme Vás kontaktovat o dalším průběhu vyřizování. <br /><br />
                Uschovejte si permanentní <a href="{order_detail_url}">odkaz na detail objednávky</a>. <br />
                {transport_instructions} <br />
                {payment_instructions} <br />
            </p>
        ';
        $this->setting->setForDomain(Setting::ORDER_SENT_PAGE_CONTENT, $orderSentText, 2);

        /* @var $defaultCurrency \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency */
        $domain2DefaultCurrency = $this->getReference(CurrencyDataFixture::CURRENCY_EUR);
        $this->setting->setForDomain(PricingSetting::DEFAULT_DOMAIN_CURRENCY, $domain2DefaultCurrency->getId(), 2);

        $this->setting->setForDomain(
            SeoSettingFacade::SEO_META_DESCRIPTION_MAIN_PAGE,
            'Shopsys Framework - nejlepší řešení pro váš internetový obchod.',
            2
        );

        $this->setting->setForDomain(SeoSettingFacade::SEO_TITLE_MAIN_PAGE, 'Shopsys Framework - Titulní strana', 2);
        $this->setting->setForDomain(SeoSettingFacade::SEO_TITLE_ADD_ON, '| Demo obchod', 2);

        $personalDataDisplaySiteContent = 'Zadáním e-mailu níže si můžete nechat zobrazit vaše osobní údaje, která evidujeme v našem internetovém obchodu.
         Pro ověření vaší totožnosti vám po zadání e-mailové adresy bude zaslán e-mail s odkazem. 
         Klikem na odkaz se dostanete na stránku s přehledem těchto osobních údajů - půjde o všechny údaje evidované k dané e-mailové adrese.';

        $personalDataExportSiteContent = 'Zadáním e-mailu níže si můžete stáhnout své osobní a jiné informace (například historii objednávek)
         z našeho internetového obchodu. Pro ověření vaší totožnosti vám po zadání e-mailové adresy bude zaslán e-mail s odkazem.
         Klikem na odkaz se dostanete na stránku s s možností stažení těchto informací ve strojově čitelném formátu - půjde o údaje
         evidované k dané e-mailové adrese na této doméně internetového obchodu.';

        $this->setting->setForDomain(Setting::PERSONAL_DATA_DISPLAY_SITE_CONTENT, $personalDataDisplaySiteContent, 2);
        $this->setting->setForDomain(Setting::PERSONAL_DATA_EXPORT_SITE_CONTENT, $personalDataExportSiteContent, 2);
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
