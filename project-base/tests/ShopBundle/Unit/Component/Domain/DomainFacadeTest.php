<?php

namespace Tests\ShopBundle\Unit\Component\Domain;

use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Component\Domain\Config\DomainConfig;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Domain\DomainFacade;
use Shopsys\ShopBundle\Component\Domain\DomainService;
use Shopsys\ShopBundle\Component\FileUpload\FileUpload;
use Shopsys\ShopBundle\Component\Setting\Setting;
use Shopsys\ShopBundle\Model\Pricing\Currency\Currency;
use Shopsys\ShopBundle\Model\Pricing\PricingSetting;
use Symfony\Component\Filesystem\Filesystem;

class DomainFacadeTest extends PHPUnit_Framework_TestCase
{
    public function testGeDomainConfigsByCurrency()
    {
        $testDomainConfigs = [
            1 => new DomainConfig(1, 'http://example.com:8080', 'example', 'cs'),
            2 => new DomainConfig(2, 'http://example.org:8080', 'example.org', 'en'),
            3 => new DomainConfig(3, 'http://example.edu:8080', 'example.edu', 'en'),
        ];
        $settingMock = $this->createMock(Setting::class);
        $domain = new Domain($testDomainConfigs, $settingMock);

        $currencyMock = $this->getMockBuilder(Currency::class)
            ->setMethods(['getId'])
            ->disableOriginalConstructor()
            ->getMock();
        $currencyMock->expects($this->any())->method('getId')->willReturn(1);

        $pricingSettingMock = $this->getMockBuilder(PricingSetting::class)
            ->setMethods(['getDomainDefaultCurrencyIdByDomainId'])
            ->disableOriginalConstructor()
            ->getMock();
        $pricingSettingMock
            ->expects($this->any())
            ->method('getDomainDefaultCurrencyIdByDomainId')
            ->willReturnMap([
                [1, 1],
                [2, 2],
                [3, 1],
            ]);

        $domainServiceMock = $this->createMock(DomainService::class);
        $filesystemMock = $this->createMock(Filesystem::class);
        $fileUploadMock = $this->createMock(FileUpload::class);

        $domainFacade = new DomainFacade(
            'domainImagesDirectory',
            $domain,
            $pricingSettingMock,
            $domainServiceMock,
            $filesystemMock,
            $fileUploadMock
        );
        $domainConfigs = $domainFacade->getDomainConfigsByCurrency($currencyMock);

        $this->assertCount(2, $domainConfigs);
        $this->assertContains($testDomainConfigs[1], $domainConfigs);
        $this->assertContains($testDomainConfigs[3], $domainConfigs);
    }
}
