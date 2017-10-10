<?php

namespace Shopsys\ShopBundle\Component\Domain;

use Shopsys\ShopBundle\Component\Domain\Multidomain\MultidomainEntityDataCreator;
use Shopsys\ShopBundle\Component\Setting\Setting;
use Shopsys\ShopBundle\Component\Setting\SettingValueRepository;
use Shopsys\ShopBundle\Component\Translation\TranslatableEntityDataCreator;

class DomainDataCreator
{
    const TEMPLATE_DOMAIN_ID = 1;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\ShopBundle\Component\Setting\Setting
     */
    private $setting;

    /**
     * @var \Shopsys\ShopBundle\Component\Setting\SettingValueRepository
     */
    private $settingValueRepository;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Multidomain\MultidomainEntityDataCreator
     */
    private $multidomainEntityDataCreator;

    /**
     * @var \Shopsys\ShopBundle\Component\Translation\TranslatableEntityDataCreator
     */
    private $translatableEntityDataCreator;

    public function __construct(
        Domain $domain,
        Setting $setting,
        SettingValueRepository $settingValueRepository,
        MultidomainEntityDataCreator $multidomainEntityDataCreator,
        TranslatableEntityDataCreator $translatableEntityDataCreator
    ) {
        $this->domain = $domain;
        $this->setting = $setting;
        $this->settingValueRepository = $settingValueRepository;
        $this->multidomainEntityDataCreator = $multidomainEntityDataCreator;
        $this->translatableEntityDataCreator = $translatableEntityDataCreator;
    }

    /**
     * @return int
     */
    public function createNewDomainsData()
    {
        $newDomainsCount = 0;
        foreach ($this->domain->getAllIncludingDomainConfigsWithoutDataCreated() as $domainConfig) {
            $domainId = $domainConfig->getId();
            try {
                $this->setting->getForDomain(Setting::DOMAIN_DATA_CREATED, $domainId);
            } catch (\Shopsys\ShopBundle\Component\Setting\Exception\SettingValueNotFoundException $ex) {
                $this->settingValueRepository->copyAllMultidomainSettings(self::TEMPLATE_DOMAIN_ID, $domainId);
                $this->setting->clearCache();
                $this->setting->setForDomain(Setting::BASE_URL, $domainConfig->getUrl(), $domainId);
                $this->multidomainEntityDataCreator->copyAllMultidomainDataForNewDomain(self::TEMPLATE_DOMAIN_ID, $domainId);
                $locale = $domainConfig->getLocale();
                if ($this->isNewLocale($locale)) {
                    $this->translatableEntityDataCreator->copyAllTranslatableDataForNewLocale(
                        $this->getTemplateLocale(),
                        $locale
                    );
                }
                $newDomainsCount++;
            }
        }

        return $newDomainsCount;
    }

    /**
     * @param string $locale
     * @return bool
     */
    private function isNewLocale($locale)
    {
        foreach ($this->domain->getAll() as $domainConfig) {
            if ($domainConfig->getLocale() === $locale) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return string
     */
    private function getTemplateLocale()
    {
        return $this->domain->getDomainConfigById(self::TEMPLATE_DOMAIN_ID)->getLocale();
    }
}
