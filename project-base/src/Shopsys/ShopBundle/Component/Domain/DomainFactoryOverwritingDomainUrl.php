<?php

namespace Shopsys\ShopBundle\Component\Domain;

use Shopsys\ShopBundle\Component\Domain\Config\DomainConfig;
use Shopsys\ShopBundle\Component\Domain\Config\DomainsConfigLoader;
use Shopsys\ShopBundle\Component\Setting\Setting;

class DomainFactoryOverwritingDomainUrl
{
    /**
     * @var string|null
     */
    private $overwriteDomainUrl;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Config\DomainsConfigLoader
     */
    private $domainsConfigLoader;

    /**
     * @var \Shopsys\ShopBundle\Component\Setting\Setting
     */
    private $setting;

    /**
     * @param string|null $overwriteDomainUrl
     * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainsConfigLoader $domainsConfigLoader
     * @param \Shopsys\ShopBundle\Component\Setting\Setting
     */
    public function __construct($overwriteDomainUrl, DomainsConfigLoader $domainsConfigLoader, Setting $setting)
    {
        $this->overwriteDomainUrl = $overwriteDomainUrl;
        $this->domainsConfigLoader = $domainsConfigLoader;
        $this->setting = $setting;
    }

    /**
     * @param string $domainsConfigFilepath
     * @param string $domainsUrlsConfigFilepath
     * @return \Shopsys\ShopBundle\Component\Domain\Domain
     */
    public function create($domainsConfigFilepath, $domainsUrlsConfigFilepath)
    {
        $domainConfigs = $this->domainsConfigLoader->loadDomainConfigsFromYaml($domainsConfigFilepath, $domainsUrlsConfigFilepath);
        if ($this->overwriteDomainUrl !== null) {
            $domainConfigs = $this->overwriteDomainUrl($domainConfigs);
        }

        $domain = new Domain($domainConfigs, $this->setting);

        $domainId = getenv('DOMAIN');
        if ($domainId !== false) {
            $domain->switchDomainById($domainId);
        }

        return $domain;
    }

    /**
     * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig[] $domainConfigs
     * @return \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig[]
     */
    public function overwriteDomainUrl(array $domainConfigs)
    {
        $mockedDomainConfigs = [];
        foreach ($domainConfigs as $domainConfig) {
            $mockedDomainConfigs[] = new DomainConfig(
                $domainConfig->getId(),
                $this->overwriteDomainUrl,
                $domainConfig->getName(),
                $domainConfig->getLocale(),
                $domainConfig->getStylesDirectory()
            );
        }

        return $mockedDomainConfigs;
    }
}
