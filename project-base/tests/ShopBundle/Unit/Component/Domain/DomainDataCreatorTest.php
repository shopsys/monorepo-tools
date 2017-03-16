<?php

namespace Tests\ShopBundle\Unit\Component\Domain;

use Doctrine\ORM\EntityManager;
use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Component\Domain\Config\DomainConfig;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Domain\DomainDataCreator;
use Shopsys\ShopBundle\Component\Domain\Multidomain\MultidomainEntityDataCreator;
use Shopsys\ShopBundle\Component\Setting\Setting;
use Shopsys\ShopBundle\Component\Setting\SettingValueRepository;
use Shopsys\ShopBundle\Component\Translation\TranslatableEntityDataCreator;

class DomainDataCreatorTest extends PHPUnit_Framework_TestCase
{
    public function testCreateNewDomainsDataNoNewDomain()
    {
        $domainConfigs = [
            new DomainConfig(1, 'http://example.com:8080', 'example', 'cs'),
        ];

        $settingMock = $this->getMock(Setting::class, [], [], '', false);
        $settingMock
            ->expects($this->once())
            ->method('getForDomain')
            ->with($this->equalTo(Setting::DOMAIN_DATA_CREATED), $this->equalTo(1))
            ->willReturn(true);

        $domain = new Domain($domainConfigs, $settingMock);

        $emMock = $this->getMock(EntityManager::class, [], [], '', false);

        $settingValueRepositoryMock = $this->getMock(SettingValueRepository::class, [], [], '', false);
        $multidomainEntityDataCreatorMock = $this->getMock(MultidomainEntityDataCreator::class, [], [], '', false);
        $translatableEntityDataCreatorMock = $this->getMock(TranslatableEntityDataCreator::class, [], [], '', false);

        $domainDataCreator = new DomainDataCreator(
            $domain,
            $settingMock,
            $settingValueRepositoryMock,
            $emMock,
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

        $settingMock = $this->getMock(Setting::class, [], [], '', false);
        $settingMock
            ->method('getForDomain')
            ->willReturnCallback(function ($key, $domainId) {
                $this->assertEquals(Setting::DOMAIN_DATA_CREATED, $key);
                if ($domainId === 1) {
                    return true;
                }
                throw new \Shopsys\ShopBundle\Component\Setting\Exception\SettingValueNotFoundException();
            });

        $domain = new Domain($domainConfigs, $settingMock);

        $emMock = $this->getMock(EntityManager::class, [], [], '', false);

        $settingValueRepositoryMock = $this->getMock(SettingValueRepository::class, [], [], '', false);
        $settingValueRepositoryMock
            ->expects($this->any())
            ->method('copyAllMultidomainSettings')
            ->with($this->equalTo(DomainDataCreator::TEMPLATE_DOMAIN_ID), $this->equalTo(2));

        $multidomainEntityDataCreatorMock = $this->getMock(MultidomainEntityDataCreator::class, [], [], '', false);
        $multidomainEntityDataCreatorMock
            ->method('copyAllMultidomainDataForNewDomain')
            ->with($this->equalTo(DomainDataCreator::TEMPLATE_DOMAIN_ID), $this->equalTo(2));

        $translatableEntityDataCreatorMock = $this->getMock(TranslatableEntityDataCreator::class, [], [], '', false);

        $domainDataCreator = new DomainDataCreator(
            $domain,
            $settingMock,
            $settingValueRepositoryMock,
            $emMock,
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

        $settingMock = $this->getMock(Setting::class, [], [], '', false);
        $settingMock
            ->method('get')
            ->willReturnCallback(function ($key, $domainId) {
                $this->assertEquals(Setting::DOMAIN_DATA_CREATED, $key);
                if ($domainId === 1) {
                    return true;
                }
                throw new \Shopsys\ShopBundle\Component\Setting\Exception\SettingValueNotFoundException();
            });

        $domainMock = $this->getMock(Domain::class, [], [], '', false);
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

        $emMock = $this->getMock(EntityManager::class, [], [], '', false);
        $settingValueRepositoryMock = $this->getMock(SettingValueRepository::class, [], [], '', false);
        $multidomainEntityDataCreatorMock = $this->getMock(MultidomainEntityDataCreator::class, [], [], '', false);
        $translatableEntityDataCreatorMock = $this->getMock(TranslatableEntityDataCreator::class, [], [], '', false);
        $translatableEntityDataCreatorMock
            ->expects($this->any())
            ->method('copyAllTranslatableDataForNewLocale')
            ->with($domainConfigWithDataCreated->getLocale(), $domainConfigWithNewLocale->getLocale());

        $domainDataCreator = new DomainDataCreator(
            $domainMock,
            $settingMock,
            $settingValueRepositoryMock,
            $emMock,
            $multidomainEntityDataCreatorMock,
            $translatableEntityDataCreatorMock
        );

        $domainDataCreator->createNewDomainsData();
    }
}
