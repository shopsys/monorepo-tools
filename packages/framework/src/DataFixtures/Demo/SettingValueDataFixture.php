<?php

namespace Shopsys\FrameworkBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Setting\Setting;
use Shopsys\FrameworkBundle\DataFixtures\Base\PricingGroupDataFixture;

class SettingValueDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{
    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $setting = $this->get(Setting::class);
        /* @var $setting \Shopsys\FrameworkBundle\Component\Setting\Setting */

        // Any previously executed data fixture using Setting (even transitively) would fill the Setting cache.
        // As EM identity map is cleared after each fixture we should clear the Setting cache before editing the values.
        $setting->clearCache();

        $termsAndConditions = $this->getReference(ArticleDataFixture::ARTICLE_TERMS_AND_CONDITIONS_1);
        $privacyPolicy = $this->getReference(ArticleDataFixture::ARTICLE_PRIVACY_POLICY_1);
        /* @var $termsAndConditions \Shopsys\FrameworkBundle\Model\Article\Article */
        $cookies = $this->getReference(ArticleDataFixture::ARTICLE_COOKIES_1);
        /* @var $cookies \Shopsys\FrameworkBundle\Model\Article\Article */

        $personalDataSiteContent = 'By entering an email below, you can view your personal information that we register in our online store. 
        An email with a link will be sent to you after entering your email address, to verify your identity. 
        Clicking on the link will take you to a page listing all the personal details we have connected to your email address.';

        $setting->setForDomain(Setting::COOKIES_ARTICLE_ID, $cookies->getId(), Domain::FIRST_DOMAIN_ID);
        $setting->setForDomain(Setting::TERMS_AND_CONDITIONS_ARTICLE_ID, $termsAndConditions->getId(), Domain::FIRST_DOMAIN_ID);
        $setting->setForDomain(Setting::PRIVACY_POLICY_ARTICLE_ID, $privacyPolicy->getId(), Domain::FIRST_DOMAIN_ID);
        $setting->setForDomain(Setting::PERSONAL_DATA_SITE_CONTENT, $personalDataSiteContent, Domain::FIRST_DOMAIN_ID);
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
