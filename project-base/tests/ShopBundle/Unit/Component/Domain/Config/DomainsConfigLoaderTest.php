<?php

namespace Tests\ShopBundle\Unit\Component\Domain\Config;

use Shopsys\ShopBundle\Component\Domain\Config\DomainConfig;
use Shopsys\ShopBundle\Component\Domain\Config\DomainsConfigLoader;
use Tests\ShopBundle\Test\FunctionalTestCase;

class DomainsConfigLoaderTest extends FunctionalTestCase
{
    public function testLoadDomainConfigsFromYaml()
    {
        $domainsConfigFilepath = $this->getContainer()->getParameter('shopsys.domain_config_filepath');
        $domainsUrlsConfigFilepath = $this->getContainer()->getParameter('shopsys.domain_urls_config_filepath');
        $domainsConfigLoader = $this->getContainer()->get(DomainsConfigLoader::class);
        /* @var $domainsConfigLoader \Shopsys\ShopBundle\Component\Domain\Config\DomainsConfigLoader */

        $domainConfigs = $domainsConfigLoader->loadDomainConfigsFromYaml($domainsConfigFilepath, $domainsUrlsConfigFilepath);

        $this->assertGreaterThan(0, count($domainConfigs));

        foreach ($domainConfigs as $domainConfig) {
            $this->assertInstanceOf(DomainConfig::class, $domainConfig);
        }
    }

    public function testLoadDomainConfigsFromYamlConfigFileNotFound()
    {
        $domainsConfigLoader = $this->getContainer()->get(DomainsConfigLoader::class);
        /* @var $domainsConfigLoader \Shopsys\ShopBundle\Component\Domain\Config\DomainsConfigLoader */
        $domainsUrlsConfigFilepath = $this->getContainer()->getParameter('shopsys.domain_urls_config_filepath');

        $this->setExpectedException(\Symfony\Component\Filesystem\Exception\FileNotFoundException::class);
        $domainsConfigLoader->loadDomainConfigsFromYaml('nonexistentFilename', $domainsUrlsConfigFilepath);
    }

    public function testLoadDomainConfigsFromYamlUrlsConfigFileNotFound()
    {
        $domainsConfigLoader = $this->getContainer()->get(DomainsConfigLoader::class);
        /* @var $domainsConfigLoader \Shopsys\ShopBundle\Component\Domain\Config\DomainsConfigLoader */
        $domainsConfigFilepath = $this->getContainer()->getParameter('shopsys.domain_config_filepath');

        $this->setExpectedException(\Symfony\Component\Filesystem\Exception\FileNotFoundException::class);
        $domainsConfigLoader->loadDomainConfigsFromYaml($domainsConfigFilepath, 'nonexistentFilename');
    }

    public function testLoadDomainConfigsFromYamlDomainConfigsDoNotMatchException()
    {
        $domainsConfigLoader = $this->getContainer()->get(DomainsConfigLoader::class);
        /* @var $domainsConfigLoader \Shopsys\ShopBundle\Component\Domain\Config\DomainsConfigLoader */
        $domainsConfigFilepath = __DIR__ . '/test_domains.yml';
        $domainsUrlsConfigFilepath = __DIR__ . '/test_domains_urls.yml';

        $this->setExpectedException(\Shopsys\ShopBundle\Component\Domain\Config\Exception\DomainConfigsDoNotMatchException::class);

        $domainsConfigLoader->loadDomainConfigsFromYaml($domainsConfigFilepath, $domainsUrlsConfigFilepath);
    }
}
