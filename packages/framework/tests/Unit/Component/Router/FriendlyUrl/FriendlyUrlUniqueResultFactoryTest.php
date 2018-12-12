<?php

namespace Tests\FrameworkBundle\Unit\Component\Router\FriendlyUrl;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrl;
use Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlFactory;
use Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlUniqueResultFactory;
use Shopsys\FrameworkBundle\Component\Setting\Setting;

class FriendlyUrlUniqueResultFactoryTest extends TestCase
{
    public function testCreateNewUnique()
    {
        $domainConfigs = [
            new DomainConfig(1, 'http://example.com', 'example.com', 'en'),
        ];
        $settingMock = $this->createMock(Setting::class);
        $domain = new Domain($domainConfigs, $settingMock);

        $friendlyUrlUniqueResultFactory = new FriendlyUrlUniqueResultFactory(new FriendlyUrlFactory($domain));

        $attempt = 1;
        $friendlyUrl = new FriendlyUrl('route_name', 7, 1, 'name');
        $matchedRouteData = null;
        $friendlyUrlUniqueResult = $friendlyUrlUniqueResultFactory->create(
            $attempt,
            $friendlyUrl,
            'name',
            $matchedRouteData
        );

        $this->assertTrue($friendlyUrlUniqueResult->isUnique());
        $this->assertSame($friendlyUrl, $friendlyUrlUniqueResult->getFriendlyUrlForPersist());
    }

    public function testCreateOldUnique()
    {
        $domainConfigs = [
            new DomainConfig(1, 'http://example.com', 'example.com', 'en'),
        ];
        $settingMock = $this->createMock(Setting::class);
        $domain = new Domain($domainConfigs, $settingMock);

        $friendlyUrlUniqueResultFactory = new FriendlyUrlUniqueResultFactory(new FriendlyUrlFactory($domain));

        $attempt = 1;
        $friendlyUrl = new FriendlyUrl('route_name', 7, 1, 'name');
        $matchedRouteData = [
            '_route' => $friendlyUrl->getRouteName(),
            'id' => $friendlyUrl->getEntityId(),
        ];
        $friendlyUrlUniqueResult = $friendlyUrlUniqueResultFactory->create(
            $attempt,
            $friendlyUrl,
            'name',
            $matchedRouteData
        );

        $this->assertTrue($friendlyUrlUniqueResult->isUnique());
        $this->assertNull($friendlyUrlUniqueResult->getFriendlyUrlForPersist());
    }

    public function testCreateNotUnique()
    {
        $domainConfigs = [
            new DomainConfig(1, 'http://example.com', 'example.com', 'en'),
        ];
        $settingMock = $this->createMock(Setting::class);
        $domain = new Domain($domainConfigs, $settingMock);

        $friendlyUrlUniqueResultFactory = new FriendlyUrlUniqueResultFactory(new FriendlyUrlFactory($domain));

        $attempt = 3;
        $friendlyUrl = new FriendlyUrl('route_name', 7, 1, 'name');
        $matchedRouteData = [
            '_route' => 'another_route_name',
            'id' => 7,
        ];
        $friendlyUrlUniqueResult = $friendlyUrlUniqueResultFactory->create(
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
}
