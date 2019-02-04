<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Setting\Setting;
use Shopsys\FrameworkBundle\Model\Pricing\PricingSetting;
use Shopsys\FrameworkBundle\Model\Seo\SeoSettingFacade;

class MultidomainSettingValueDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Setting\Setting
     */
    protected $setting;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected $domain;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Setting\Setting $setting
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     */
    public function __construct(Setting $setting, Domain $domain)
    {
        $this->setting = $setting;
        $this->domain = $domain;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->domain->getAllIdsExcludingFirstDomain() as $domainId) {
            $this->loadForDomain($domainId);
        }
    }

    /**
     * @param int $domainId
     */
    protected function loadForDomain(int $domainId)
    {
        $termsAndConditionsDomain = $this->getReferenceForDomain(MultidomainArticleDataFixture::ARTICLE_TERMS_AND_CONDITIONS, $domainId);
        /* @var $termsAndConditionsDomain \Shopsys\FrameworkBundle\Model\Article\Article */
        $this->setting->setForDomain(Setting::TERMS_AND_CONDITIONS_ARTICLE_ID, $termsAndConditionsDomain->getId(), $domainId);

        $privacyPolicyDomain = $this->getReferenceForDomain(MultidomainArticleDataFixture::ARTICLE_PRIVACY_POLICY, $domainId);
        /* @var $privacyPolicyDomain \Shopsys\FrameworkBundle\Model\Article\Article */
        $this->setting->setForDomain(Setting::PRIVACY_POLICY_ARTICLE_ID, $privacyPolicyDomain->getId(), $domainId);

        $cookiesDomain = $this->getReferenceForDomain(MultidomainArticleDataFixture::ARTICLE_COOKIES, $domainId);
        /* @var $cookiesDomain \Shopsys\FrameworkBundle\Model\Article\Article */
        $this->setting->setForDomain(Setting::COOKIES_ARTICLE_ID, $cookiesDomain->getId(), $domainId);

        /* @var $pricingGroup \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup */
        $pricingGroup = $this->getReferenceForDomain(MultidomainPricingGroupDataFixture::PRICING_GROUP_ORDINARY_DOMAIN, $domainId);
        $this->setting->setForDomain(Setting::DEFAULT_PRICING_GROUP, $pricingGroup->getId(), $domainId);

        $orderSentText = '
            <p>
                Objednávka číslo {number} byla odeslána, děkujeme za Váš nákup.
                Budeme Vás kontaktovat o dalším průběhu vyřizování. <br /><br />
                Uschovejte si permanentní <a href="{order_detail_url}">odkaz na detail objednávky</a>. <br />
                {transport_instructions} <br />
                {payment_instructions} <br />
            </p>
        ';
        $this->setting->setForDomain(Setting::ORDER_SENT_PAGE_CONTENT, $orderSentText, $domainId);

        /* @var $defaultCurrency \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency */
        $defaultCurrency = $this->getReference(CurrencyDataFixture::CURRENCY_EUR);
        $this->setting->setForDomain(PricingSetting::DEFAULT_DOMAIN_CURRENCY, $defaultCurrency->getId(), $domainId);

        $this->setting->setForDomain(
            SeoSettingFacade::SEO_META_DESCRIPTION_MAIN_PAGE,
            'Shopsys Framework - nejlepší řešení pro váš internetový obchod.',
            $domainId
        );

        $this->setting->setForDomain(SeoSettingFacade::SEO_TITLE_MAIN_PAGE, 'Shopsys Framework - Titulní strana', $domainId);
        $this->setting->setForDomain(SeoSettingFacade::SEO_TITLE_ADD_ON, '| Demo obchod', $domainId);

        $personalDataDisplaySiteContent = 'Zadáním e-mailu níže si můžete nechat zobrazit vaše osobní údaje, která evidujeme v našem internetovém obchodu.
         Pro ověření vaší totožnosti vám po zadání e-mailové adresy bude zaslán e-mail s odkazem. 
         Klikem na odkaz se dostanete na stránku s přehledem těchto osobních údajů - půjde o všechny údaje evidované k dané e-mailové adrese.';

        $personalDataExportSiteContent = 'Zadáním e-mailu níže si můžete stáhnout své osobní a jiné informace (například historii objednávek)
         z našeho internetového obchodu. Pro ověření vaší totožnosti vám po zadání e-mailové adresy bude zaslán e-mail s odkazem.
         Klikem na odkaz se dostanete na stránku s s možností stažení těchto informací ve strojově čitelném formátu - půjde o údaje
         evidované k dané e-mailové adrese na této doméně internetového obchodu.';

        $this->setting->setForDomain(Setting::PERSONAL_DATA_DISPLAY_SITE_CONTENT, $personalDataDisplaySiteContent, $domainId);
        $this->setting->setForDomain(Setting::PERSONAL_DATA_EXPORT_SITE_CONTENT, $personalDataExportSiteContent, $domainId);
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return [
            MultidomainArticleDataFixture::class,
            MultidomainPricingGroupDataFixture::class,
            SettingValueDataFixture::class,
        ];
    }
}
