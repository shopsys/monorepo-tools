<?php

namespace SS6\ShopBundle\Tests\Unit\Model\Domain;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Domain\Config\DomainConfig;
use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Domain\DomainFacade;
use SS6\ShopBundle\Model\Image\ImageService;
use SS6\ShopBundle\Model\Pricing\Currency\Currency;
use SS6\ShopBundle\Model\Pricing\PricingSetting;

class DomainFacadeTest extends PHPUnit_Framework_TestCase {

	public function testGeDomainConfigsByCurrency() {
		$testDomainConfigs = [
			1 => new DomainConfig(1, 'http://example.com:8080', 'example', 'cs', 'design1', 'stylesDirectory'),
			2 => new DomainConfig(2, 'http://example.org:8080', 'example.org', 'en', 'design2', 'stylesDirectory'),
			3 => new DomainConfig(3, 'http://example.edu:8080', 'example.edu', 'en', 'design3', 'stylesDirectory'),
		];
		$domain = new Domain($testDomainConfigs);

		$currencyMock = $this->getMock(Currency::class, ['getId'], [], '', false);
		$currencyMock->expects($this->any())->method('getId')->willReturn(1);

		$pricingSettingMock = $this->getMock(PricingSetting::class, ['getDomainDefaultCurrencyIdByDomainId'], [], '', false);
		$pricingSettingMock
			->expects($this->any())
			->method('getDomainDefaultCurrencyIdByDomainId')
			->willReturnMap([
				[1, 1],
				[2, 2],
				[3, 1],
			]);

		$imageServiceMock = $this->getMock(ImageService::class, [], [], '', false);

		$domainFacade = new DomainFacade($domain, $pricingSettingMock, $imageServiceMock);
		$domainConfigs = $domainFacade->getDomainConfigsByCurrency($currencyMock);

		$this->assertCount(2, $domainConfigs);
		$this->assertContains($testDomainConfigs[1], $domainConfigs);
		$this->assertContains($testDomainConfigs[3], $domainConfigs);
	}

}
