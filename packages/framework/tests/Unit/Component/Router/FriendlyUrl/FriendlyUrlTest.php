<?php

declare(strict_types=1);

namespace Tests\FrameworkBundle\Unit\Component\Router\FriendlyUrl;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrl;
use Shopsys\FrameworkBundle\Component\Setting\Setting;

class FriendlyUrlTest extends TestCase
{
    public function testGetAbsoluteUrl()
    {
        $domainConfigs = [
            new DomainConfig(1, 'http://example.cz', 'example.cz', 'cs'),
        ];
        $settingMock = $this->createMock(Setting::class);
        $domain = new Domain($domainConfigs, $settingMock);

        $friendlyUrl = new FriendlyUrl('routeName', 1, 1, 'slug/');
        $absoluteUrl = $friendlyUrl->getAbsoluteUrl($domain);

        $this->assertSame('http://example.cz/slug/', $absoluteUrl);
    }
}
