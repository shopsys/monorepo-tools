<?php

declare(strict_types=1);

namespace Tests\FrameworkBundle\Unit\Component\Router\FriendlyUrl;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlFactory;
use Shopsys\FrameworkBundle\Component\Setting\Setting;

class FriendlyUrlFactoryTest extends TestCase
{
    public function testCreateForAllDomains()
    {
        $domainConfigs = [
            new DomainConfig(1, 'http://example.cz', 'example.cz', 'cs'),
            new DomainConfig(2, 'http://example.com', 'example.com', 'en'),
        ];
        $settingMock = $this->createMock(Setting::class);
        $domain = new Domain($domainConfigs, $settingMock);

        $friendlyUrlFactory = new FriendlyUrlFactory($domain);

        $routeName = 'route_name';
        $entityId = 7;
        $namesByLocale = [
            'cs' => 'cs-name',
            'en' => 'en-name',
        ];

        $friendlyUrls = $friendlyUrlFactory->createForAllDomains($routeName, $entityId, $namesByLocale);
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
}
