<?php

namespace Tests\ShopBundle\Unit\Component\Router;

use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Component\Domain\Config\DomainConfig;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Router\DomainRouterFactory;
use Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlRouter;
use Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlRouterFactory;
use Shopsys\ShopBundle\Component\Router\LocalizedRouterFactory;
use Shopsys\ShopBundle\Component\Setting\Setting;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;

class DomainRouterFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testGetRouter()
    {
        $domainConfig = new DomainConfig(3, 'http://example.com:8080', 'example', 'en');
        $settingMock = $this->createMock(Setting::class);
        $domain = new Domain([$domainConfig], $settingMock);

        $localizedRouterMock = $this->getMockBuilder(RouterInterface::class)->getMockForAbstractClass();
        $friendlyUrlRouterMock = $this->getMockBuilder(FriendlyUrlRouter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $localizedRouterFactoryMock = $this->getMockBuilder(LocalizedRouterFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRouter'])
            ->getMock();
        $localizedRouterFactoryMock
            ->expects($this->once())
            ->method('getRouter')
            ->willReturnCallback(function ($locale, RequestContext $context) use ($localizedRouterMock) {
                $this->assertSame('en', $locale);
                $this->assertSame('example.com', $context->getHost());

                return $localizedRouterMock;
            });

        $friendlyUrlRouterFactoryMock = $this->getMockBuilder(FriendlyUrlRouterFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['createRouter'])
            ->getMock();
        $friendlyUrlRouterFactoryMock
            ->expects($this->once())
            ->method('createRouter')
            ->willReturnCallback(
                function (DomainConfig $actualDomainConfig, RequestContext $context) use ($domainConfig, $friendlyUrlRouterMock) {
                    $this->assertSame($domainConfig, $actualDomainConfig);
                    $this->assertSame('example.com', $context->getHost());
                    return $friendlyUrlRouterMock;
                }
            );

        $delegatingLoaderMock = $this->createMock(DelegatingLoader::class);

        $domainRouterFactory = new DomainRouterFactory(
            'routerConfiguration',
            $delegatingLoaderMock,
            $localizedRouterFactoryMock,
            $friendlyUrlRouterFactoryMock,
            $domain
        );
        $router = $domainRouterFactory->getRouter(3);

        $this->assertInstanceOf(RouterInterface::class, $router);
    }
}
