<?php

namespace Shopsys\FrameworkBundle\Component\Domain;

use Shopsys\FrameworkBundle\Component\Domain\Config\DomainsConfigLoader;
use Shopsys\FrameworkBundle\Component\Setting\Setting;

class DomainFactory
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Config\DomainsConfigLoader
     */
    private $domainsConfigLoader;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Setting\Setting
     */
    private $setting;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainsConfigLoader $domainsConfigLoader
     * @param \Shopsys\FrameworkBundle\Component\Setting\Setting
     */
    public function __construct(DomainsConfigLoader $domainsConfigLoader, Setting $setting)
    {
        $this->domainsConfigLoader = $domainsConfigLoader;
        $this->setting = $setting;
    }

    /**
     * @param string $domainsConfigFilepath
     * @param string $domainsUrlsConfigFilepath
     * @return \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    public function create($domainsConfigFilepath, $domainsUrlsConfigFilepath)
    {
        $domainConfigs = $this->domainsConfigLoader->loadDomainConfigsFromYaml($domainsConfigFilepath, $domainsUrlsConfigFilepath);
        $domain = new Domain($domainConfigs, $this->setting);

        $domainId = getenv('DOMAIN');
        if ($domainId !== false) {
            $domain->switchDomainById($domainId);
        }

        return $domain;
    }
}
