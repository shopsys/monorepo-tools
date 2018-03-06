<?php

namespace Tests\ShopBundle\Unit\Component\Domain;

use PHPUnit_Framework_TestCase;
use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Setting\Setting;
use Symfony\Component\HttpFoundation\Request;

class DomainTest extends PHPUnit_Framework_TestCase
{
    public function testGetIdNotSet()
    {
        $domainConfigs = [
            new DomainConfig(1, 'http://example.com:8080', 'example', 'cs'),
            new DomainConfig(2, 'http://example.org:8080', 'example.org', 'en'),
        ];
        $settingMock = $this->createMock(Setting::class);

        $domain = new Domain($domainConfigs, $settingMock);
        $this->expectException(\Shopsys\FrameworkBundle\Component\Domain\Exception\NoDomainSelectedException::class);
        $domain->getId();
    }

    public function testSwitchDomainByRequest()
    {
        $domainConfigs = [
            new DomainConfig(1, 'http://example.com:8080', 'example.com', 'cs'),
            new DomainConfig(2, 'http://example.org:8080', 'example.org', 'en'),
        ];
        $settingMock = $this->createMock(Setting::class);

        $domain = new Domain($domainConfigs, $settingMock);

        $requestMock = $this->getMockBuilder(Request::class)
            ->setMethods(['getSchemeAndHttpHost'])
            ->getMock();
        $requestMock
            ->expects($this->atLeastOnce())
            ->method('getSchemeAndHttpHost')
            ->will($this->returnValue('http://example.com:8080'));

        $domain->switchDomainByRequest($requestMock);
        $this->assertSame(1, $domain->getId());
        $this->assertSame('example.com', $domain->getName());
        $this->assertSame('cs', $domain->getLocale());
    }

    public function testGetAllIncludingDomainConfigsWithoutDataCreated()
    {
        $domainConfigs = [
            new DomainConfig(1, 'http://example.com:8080', 'example.com', 'cs'),
            new DomainConfig(2, 'http://example.org:8080', 'example.org', 'en'),
        ];
        $settingMock = $this->createMock(Setting::class);

        $domain = new Domain($domainConfigs, $settingMock);

        $this->assertSame($domainConfigs, $domain->getAllIncludingDomainConfigsWithoutDataCreated());
    }

    public function testGetAll()
    {
        $domainConfigWithDataCreated = new DomainConfig(1, 'http://example.com:8080', 'example.com', 'cs');
        $domainConfigWithoutDataCreated = new DomainConfig(2, 'http://example.org:8080', 'example.org', 'en');
        $domainConfigs = [
            $domainConfigWithDataCreated,
            $domainConfigWithoutDataCreated,
        ];
        $settingMock = $this->createMock(Setting::class);
        $settingMock
            ->expects($this->exactly(count($domainConfigs)))
            ->method('getForDomain')
            ->willReturnCallback(function ($key, $domainId) use ($domainConfigWithDataCreated) {
                $this->assertEquals(Setting::DOMAIN_DATA_CREATED, $key);
                if ($domainId === $domainConfigWithDataCreated->getId()) {
                    return true;
                }
                throw new \Shopsys\FrameworkBundle\Component\Setting\Exception\SettingValueNotFoundException();
            });

        $domain = new Domain($domainConfigs, $settingMock);

        $this->assertSame([$domainConfigWithDataCreated], $domain->getAll());
    }

    public function testGetDomainConfigById()
    {
        $domainConfigs = [
            new DomainConfig(1, 'http://example.com:8080', 'example.com', 'cs'),
            new DomainConfig(2, 'http://example.org:8080', 'example.org', 'en'),
        ];
        $settingMock = $this->createMock(Setting::class);

        $domain = new Domain($domainConfigs, $settingMock);

        $this->assertSame($domainConfigs[0], $domain->getDomainConfigById(1));
        $this->assertSame($domainConfigs[1], $domain->getDomainConfigById(2));

        $this->expectException(\Shopsys\FrameworkBundle\Component\Domain\Exception\InvalidDomainIdException::class);
        $domain->getDomainConfigById(3);
    }
}
