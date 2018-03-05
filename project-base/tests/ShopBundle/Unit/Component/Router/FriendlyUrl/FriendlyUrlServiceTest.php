<?php

namespace Tests\ShopBundle\Unit\Component\Router\FriendlyUrl;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrl;
use Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlService;
use Shopsys\FrameworkBundle\Component\Setting\Setting;

class FriendlyUrlServiceTest extends TestCase
{
    public function testCreateFriendlyUrls()
    {
        $domainConfigs = [
            new DomainConfig(1, 'http://example.cz', 'example.cz', 'cs'),
            new DomainConfig(2, 'http://example.com', 'example.com', 'en'),
        ];
        $settingMock = $this->createMock(Setting::class);
        $domain = new Domain($domainConfigs, $settingMock);

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
                $this->assertSame($namesByLocale['cs'] . '/', $friendlyUrl->getSlug());
            } elseif ($friendlyUrl->getDomainId() === 2) {
                $this->assertSame($namesByLocale['en'] . '/', $friendlyUrl->getSlug());
            }
        }
    }

    public function testGetFriendlyUrlUniqueResultNewUnique()
    {
        $domainConfigs = [
            new DomainConfig(1, 'http://example.com', 'example.com', 'en'),
        ];
        $settingMock = $this->createMock(Setting::class);
        $domain = new Domain($domainConfigs, $settingMock);

        $friendlyUrlService = new FriendlyUrlService($domain);

        $attempt = 1;
        $friendlyUrl = new FriendlyUrl('route_name', 7, 1, 'name');
        $matchedRouteData = null;
        $friendlyUrlUniqueResult = $friendlyUrlService->getFriendlyUrlUniqueResult(
            $attempt,
            $friendlyUrl,
            'name',
            $matchedRouteData
        );

        $this->assertTrue($friendlyUrlUniqueResult->isUnique());
        $this->assertSame($friendlyUrl, $friendlyUrlUniqueResult->getFriendlyUrlForPersist());
    }

    public function testGetFriendlyUrlUniqueResultOldUnique()
    {
        $domainConfigs = [
            new DomainConfig(1, 'http://example.com', 'example.com', 'en'),
        ];
        $settingMock = $this->createMock(Setting::class);
        $domain = new Domain($domainConfigs, $settingMock);

        $friendlyUrlService = new FriendlyUrlService($domain);

        $attempt = 1;
        $friendlyUrl = new FriendlyUrl('route_name', 7, 1, 'name');
        $matchedRouteData = [
            '_route' => $friendlyUrl->getRouteName(),
            'id' => $friendlyUrl->getEntityId(),
        ];
        $friendlyUrlUniqueResult = $friendlyUrlService->getFriendlyUrlUniqueResult(
            $attempt,
            $friendlyUrl,
            'name',
            $matchedRouteData
        );

        $this->assertTrue($friendlyUrlUniqueResult->isUnique());
        $this->assertNull($friendlyUrlUniqueResult->getFriendlyUrlForPersist());
    }

    public function testGetFriendlyUrlUniqueResultNotUnique()
    {
        $domainConfigs = [
            new DomainConfig(1, 'http://example.com', 'example.com', 'en'),
        ];
        $settingMock = $this->createMock(Setting::class);
        $domain = new Domain($domainConfigs, $settingMock);

        $friendlyUrlService = new FriendlyUrlService($domain);

        $attempt = 3;
        $friendlyUrl = new FriendlyUrl('route_name', 7, 1, 'name');
        $matchedRouteData = [
            '_route' => 'another_route_name',
            'id' => 7,
        ];
        $friendlyUrlUniqueResult = $friendlyUrlService->getFriendlyUrlUniqueResult(
            $attempt,
            $friendlyUrl,
            'name',
            $matchedRouteData
        );

        $friendlyUrlForPersist = $friendlyUrlUniqueResult->getFriendlyUrlForPersist();
        $this->assertFalse($friendlyUrlUniqueResult->isUnique());
        $this->assertSame($friendlyUrl->getRouteName(), $friendlyUrlForPersist->getRouteName());
        $this->assertSame($friendlyUrl->getEntityId(), $friendlyUrlForPersist->getEntityId());
        $this->assertSame($friendlyUrl->getDomainId(), $friendlyUrlForPersist->getDomainId());
        $this->assertSame('name-4/', $friendlyUrlForPersist->getSlug());
    }

    public function testGetAbsoluteUrlByFriendlyUrl()
    {
        $domainConfigs = [
            new DomainConfig(1, 'http://example.cz', 'example.cz', 'cs'),
        ];
        $settingMock = $this->createMock(Setting::class);
        $domain = new Domain($domainConfigs, $settingMock);

        $friendlyUrlService = new FriendlyUrlService($domain);
        $friendlyUrl = new FriendlyUrl('routeName', 1, 1, 'slug/');
        $absoluteUrl = $friendlyUrlService->getAbsoluteUrlByFriendlyUrl($friendlyUrl);

        $this->assertSame('http://example.cz/slug/', $absoluteUrl);
    }
}
