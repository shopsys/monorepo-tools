<?php

namespace SS6\ShopBundle\Tests\Component\Router\FriendlyUrl;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl;
use SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlService;
use SS6\ShopBundle\Model\Domain\Config\DomainConfig;
use SS6\ShopBundle\Model\Domain\Domain;

class FriendlyUrlServiceTest extends PHPUnit_Framework_TestCase {

	public function testCreateFriendlyUrls() {
		$domainConfigs = [
			new DomainConfig(1, 'http://example.cz', 'example.cz', 'cs', 'templateDir'),
			new DomainConfig(2, 'http://example.com', 'example.com', 'en', 'templateDir'),
		];
		$domain = new Domain($domainConfigs);

		$friendlyUrlService = new FriendlyUrlService($domain);

		$routeName = 'route_name';
		$entityId = '7';
		$namesByLocale = [
			'cs' => 'cs-name',
			'en' => 'en-name',
		];

		$friendlyUrls = $friendlyUrlService->createFriendlyUrls($routeName, $entityId, $namesByLocale);
		$this->assertCount(2, $friendlyUrls);
		foreach ($friendlyUrls as $friendlyUrl) {
			$this->assertSame($entityId, $friendlyUrl->getEntityId());
			$this->assertSame($routeName, $friendlyUrl->getRouteName());
			if ($friendlyUrl->getDomainId() === 1) {
				$this->assertSame($namesByLocale['cs'], $friendlyUrl->getUrl());
			} elseif ($friendlyUrl->getDomainId() === 2) {
				$this->assertSame($namesByLocale['en'], $friendlyUrl->getUrl());
			}
		}
	}

	public function testGetFriendlyUrlUniqueResultNewUnique() {
		$domainConfigs = [
			new DomainConfig(1, 'http://example.com', 'example.com', 'en', 'templateDir'),
		];
		$domain = new Domain($domainConfigs);

		$friendlyUrlService = new FriendlyUrlService($domain);

		$attempt = 1;
		$friendlyUrl = new FriendlyUrl('route_name', 7, 1, 'name');
		$namesByLocale = [
			'en' => 'name',
		];
		$matchedRouteData = null;
		$friendlyUrlUniqueResult = $friendlyUrlService->getFriendlyUrlUniqueResult(
			$attempt,
			$friendlyUrl,
			$namesByLocale,
			$matchedRouteData
		);

		$this->assertTrue($friendlyUrlUniqueResult->isUnique());
		$this->assertSame($friendlyUrl, $friendlyUrlUniqueResult->getFriendlyUrlForPersist());
	}

	public function testGetFriendlyUrlUniqueResultOldUnique() {
		$domainConfigs = [
			new DomainConfig(1, 'http://example.com', 'example.com', 'en', 'templateDir'),
		];
		$domain = new Domain($domainConfigs);

		$friendlyUrlService = new FriendlyUrlService($domain);

		$attempt = 1;
		$friendlyUrl = new FriendlyUrl('route_name', 7, 1, 'name');
		$namesByLocale = [
			'en' => 'name',
		];
		$matchedRouteData = [
			'_route' => $friendlyUrl->getRouteName(),
			'id' => $friendlyUrl->getEntityId(),
		];
		$friendlyUrlUniqueResult = $friendlyUrlService->getFriendlyUrlUniqueResult(
			$attempt,
			$friendlyUrl,
			$namesByLocale,
			$matchedRouteData
		);

		$this->assertTrue($friendlyUrlUniqueResult->isUnique());
		$this->assertNull($friendlyUrlUniqueResult->getFriendlyUrlForPersist());
	}

	public function testGetFriendlyUrlUniqueResultNotUnique() {
		$domainConfigs = [
			new DomainConfig(1, 'http://example.com', 'example.com', 'en', 'templateDir'),
		];
		$domain = new Domain($domainConfigs);

		$friendlyUrlService = new FriendlyUrlService($domain);

		$attempt = 3;
		$friendlyUrl = new FriendlyUrl('route_name', 7, 1, 'name');
		$namesByLocale = [
			'en' => 'name',
		];
		$matchedRouteData = [
			'_route' => 'another_route_name',
			'id' => 7,
		];
		$friendlyUrlUniqueResult = $friendlyUrlService->getFriendlyUrlUniqueResult(
			$attempt,
			$friendlyUrl,
			$namesByLocale,
			$matchedRouteData
		);

		$friendlyUrlForPersist = $friendlyUrlUniqueResult->getFriendlyUrlForPersist();
		$this->assertFalse($friendlyUrlUniqueResult->isUnique());
		$this->assertSame($friendlyUrl->getRouteName(), $friendlyUrlForPersist->getRouteName());
		$this->assertSame($friendlyUrl->getEntityId(), $friendlyUrlForPersist->getEntityId());
		$this->assertSame($friendlyUrl->getDomainId(), $friendlyUrlForPersist->getDomainId());
		$this->assertSame('name-4', $friendlyUrlForPersist->getUrl());
	}

}
