<?php

namespace Tests\ShopBundle\Unit\Component\Domain;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Domain\DomainDataCreator;
use Shopsys\FrameworkBundle\Component\Domain\Multidomain\MultidomainEntityDataCreator;
use Shopsys\FrameworkBundle\Component\Setting\Setting;
use Shopsys\FrameworkBundle\Component\Setting\SettingValueRepository;
use Shopsys\FrameworkBundle\Component\Translation\TranslatableEntityDataCreator;

class DomainDataCreatorTest extends TestCase
{
    public function testCreateNewDomainsDataNoNewDomain()
    {
        $domainConfigs = [
            new DomainConfig(1, 'http://example.com:8080', 'example', 'cs'),
        ];

        $settingMock = $this->createMock(Setting::class);
        $settingMock
            ->expects($this->once())
            ->method('getForDomain')
            ->with($this->equalTo(Setting::DOMAIN_DATA_CREATED), $this->equalTo(1))
            ->willReturn(true);

        $domain = new Domain($domainConfigs, $settingMock);

        $settingValueRepositoryMock = $this->createMock(SettingValueRepository::class);
        $multidomainEntityDataCreatorMock = $this->createMock(MultidomainEntityDataCreator::class);
        $translatableEntityDataCreatorMock = $this->createMock(TranslatableEntityDataCreator::class);

        $domainDataCreator = new DomainDataCreator(
            $domain,
            $settingMock,
            $settingValueRepositoryMock,
            $multidomainEntityDataCreatorMock,
            $translatableEntityDataCreatorMock
        );
        $newDomainsDataCreated = $domainDataCreator->createNewDomainsData();

        $this->assertEquals(0, $newDomainsDataCreated);
    }

    public function testCreateNewDomainsDataOneNewDomain()
    {
        $domainConfigs = [
            new DomainConfig(1, 'http://example.com:8080', 'example', 'cs'),
            new DomainConfig(2, 'http://example.com:8080', 'example', 'cs'),
        ];

        $settingMock = $this->createMock(Setting::class);
        $settingMock
            ->method('getForDomain')
            ->willReturnCallback(function ($key, $domainId) {
                $this->assertEquals(Setting::DOMAIN_DATA_CREATED, $key);
                if ($domainId === 1) {
                    return true;
                }
                throw new \Shopsys\FrameworkBundle\Component\Setting\Exception\SettingValueNotFoundException();
            });

        $domain = new Domain($domainConfigs, $settingMock);

        $settingValueRepositoryMock = $this->createMock(SettingValueRepository::class);
        $settingValueRepositoryMock
            ->expects($this->any())
            ->method('copyAllMultidomainSettings')
            ->with($this->equalTo(DomainDataCreator::TEMPLATE_DOMAIN_ID), $this->equalTo(2));

        $multidomainEntityDataCreatorMock = $this->createMock(MultidomainEntityDataCreator::class);
        $multidomainEntityDataCreatorMock
            ->method('copyAllMultidomainDataForNewDomain')
            ->with($this->equalTo(DomainDataCreator::TEMPLATE_DOMAIN_ID), $this->equalTo(2));

        $translatableEntityDataCreatorMock = $this->createMock(TranslatableEntityDataCreator::class);

        $domainDataCreator = new DomainDataCreator(
            $domain,
            $settingMock,
            $settingValueRepositoryMock,
            $multidomainEntityDataCreatorMock,
            $translatableEntityDataCreatorMock
        );
        $newDomainsDataCreated = $domainDataCreator->createNewDomainsData();

        $this->assertEquals(1, $newDomainsDataCreated);
    }

    public function testCreateNewDomainsDataNewLocale()
    {
        $domainConfigWithDataCreated = new DomainConfig(1, 'http://example.com:8080', 'example', 'cs');
        $domainConfigWithNewLocale = new DomainConfig(2, 'http://example.com:8080', 'example', 'en');
        $domainConfigs = [
            $domainConfigWithDataCreated,
            $domainConfigWithNewLocale,
        ];

        $settingMock = $this->createMock(Setting::class);
        $settingMock
            ->method('get')
            ->willReturnCallback(function ($key, $domainId) {
                $this->assertEquals(Setting::DOMAIN_DATA_CREATED, $key);
                if ($domainId === 1) {
                    return true;
                }
                throw new \Shopsys\FrameworkBundle\Component\Setting\Exception\SettingValueNotFoundException();
            });

        $domainMock = $this->createMock(Domain::class);
        $domainMock
            ->expects($this->any())
            ->method('getAllIncludingDomainConfigsWithoutDataCreated')
            ->willReturn($domainConfigs);
        $domainMock
            ->expects($this->any())
            ->method('getAll')
            ->willReturn([$domainConfigWithDataCreated]);
        $domainMock
            ->expects($this->any())
            ->method('getDomainConfigById')
            ->willReturn($domainConfigWithDataCreated);

        $settingValueRepositoryMock = $this->createMock(SettingValueRepository::class);
        $multidomainEntityDataCreatorMock = $this->createMock(MultidomainEntityDataCreator::class);
        $translatableEntityDataCreatorMock = $this->createMock(TranslatableEntityDataCreator::class);
        $translatableEntityDataCreatorMock
            ->expects($this->any())
            ->method('copyAllTranslatableDataForNewLocale')
            ->with($domainConfigWithDataCreated->getLocale(), $domainConfigWithNewLocale->getLocale());

        $domainDataCreator = new DomainDataCreator(
            $domainMock,
            $settingMock,
            $settingValueRepositoryMock,
            $multidomainEntityDataCreatorMock,
            $translatableEntityDataCreatorMock
        );

        $domainDataCreator->createNewDomainsData();
    }
}
