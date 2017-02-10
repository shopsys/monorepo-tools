<?php

namespace Shopsys\ShopBundle\Component\Domain;

use Shopsys\ShopBundle\Component\Domain\Config\DomainsConfigLoader;
use Shopsys\ShopBundle\Component\Setting\Setting;

class DomainFactory
{

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Config\DomainsConfigLoader
     */
    private $domainsConfigLoader;

    /**
     * @var \Shopsys\ShopBundle\Component\Setting\Setting
     */
    private $setting;

    /**
     * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainsConfigLoader $domainsConfigLoader
     * @param \Shopsys\ShopBundle\Component\Setting\Setting
     */
    public function __construct(DomainsConfigLoader $domainsConfigLoader, Setting $setting) {
        $this->domainsConfigLoader = $domainsConfigLoader;
        $this->setting = $setting;
    }

    /**
     * @param string $domainsConfigFilepath
     * @param string $domainsUrlsConfigFilepath
     * @return \Shopsys\ShopBundle\Component\Domain\Domain
     */
    public function create($domainsConfigFilepath, $domainsUrlsConfigFilepath) {
        $domainConfigs = $this->domainsConfigLoader->loadDomainConfigsFromYaml($domainsConfigFilepath, $domainsUrlsConfigFilepath);
        $domain = new Domain($domainConfigs, $this->setting);

        $domainId = getenv('DOMAIN');
        if ($domainId !== false) {
            $domain->switchDomainById($domainId);
        }

        return $domain;
    }

}
