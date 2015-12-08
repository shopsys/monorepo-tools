<?php

namespace SS6\ShopBundle\Tests\Unit\Component\Domain;

use Doctrine\ORM\EntityManager;
use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Component\Domain\Config\DomainConfig;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Component\Domain\DomainDataCreator;
use SS6\ShopBundle\Component\Domain\Multidomain\MultidomainEntityDataCreator;
use SS6\ShopBundle\Component\Setting\Setting;

class DomainDataCreatorTest extends PHPUnit_Framework_TestCase {

	public function testCreateNewDomainsDataNoNewDomain() {
		$domainConfigs = [
			new DomainConfig(1, 'http://example.com:8080', 'example', 'cs', 'design1', 'stylesDirectory'),
		];

		$domain = new Domain($domainConfigs);

		$settingMock = $this->getMock(Setting::class, [], [], '', false);
		$settingMock
			->expects($this->once())
			->method('get')
			->with($this->equalTo(Setting::DOMAIN_DATA_CREATED), $this->equalTo(1))
			->willReturn(true);

		$emMock = $this->getMock(EntityManager::class, [], [], '', false);

		$multidomainEntityDataCreatorMock = $this->getMock(MultidomainEntityDataCreator::class, [], [], '', false);

		$domainDataCreator = new DomainDataCreator($domain, $settingMock, $emMock, $multidomainEntityDataCreatorMock);
		$newDomainsDataCreated = $domainDataCreator->createNewDomainsData();

		$this->assertEquals(0, $newDomainsDataCreated);
	}

	public function testCreateNewDomainsDataOneNewDomain() {
		$domainConfigs = [
			new DomainConfig(1, 'http://example.com:8080', 'example', 'cs', 'design1', 'stylesDirectory'),
			new DomainConfig(2, 'http://example.com:8080', 'example', 'cs', 'design2', 'stylesDirectory'),
		];

		$domain = new Domain($domainConfigs);

		$settingMock = $this->getMock(Setting::class, [], [], '', false);
		$settingMock
			->expects($this->exactly(count($domainConfigs)))
			->method('get')
			->willReturnCallback(function ($key, $domainId) {
				$this->assertEquals(Setting::DOMAIN_DATA_CREATED, $key);
				if ($domainId === 1) {
					return true;
				}
				throw new \SS6\ShopBundle\Component\Setting\Exception\SettingValueNotFoundException();
			});
		$settingMock
			->expects($this->once())
			->method('copyAllMultidomainSettings')
			->with($this->equalTo(DomainDataCreator::TEMPLATE_DOMAIN_ID), $this->equalTo(2));

		$emMock = $this->getMock(EntityManager::class, [], [], '', false);

		$multidomainEntityDataCreatorMock = $this->getMock(MultidomainEntityDataCreator::class, [], [], '', false);
		$multidomainEntityDataCreatorMock
			->method('copyAllMultidomainDataForNewDomain')
			->with($this->equalTo(DomainDataCreator::TEMPLATE_DOMAIN_ID), $this->equalTo(2));

		$domainDataCreator = new DomainDataCreator($domain, $settingMock, $emMock, $multidomainEntityDataCreatorMock);
		$newDomainsDataCreated = $domainDataCreator->createNewDomainsData();

		$this->assertEquals(1, $newDomainsDataCreated);
	}

}
