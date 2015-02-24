<?php

namespace SS6\ShopBundle\Tests\Component\Router;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Component\Router\DomainRouterFactory;
use SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlRouterFactory;
use SS6\ShopBundle\Component\Router\LocalizedRouterFactory;
use SS6\ShopBundle\Model\Domain\Config\DomainConfig;
use SS6\ShopBundle\Model\Domain\Domain;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;

class DomainRouterFactoryTest extends PHPUnit_Framework_TestCase {

	public function testGetRouter() {

		$domainConfig = new DomainConfig(3, 'http://example.com:8080', 'example', 'en', 'templateDirectory');
		$domain = new Domain([$domainConfig]);

		$localizedRouterMock = $this->getMockBuilder(RouterInterface::class)->getMockForAbstractClass();
		$friendlyUrlRouterMock = $this->getMockBuilder(RouterInterface::class)->getMockForAbstractClass();

		$localizedRouterFactoryMock = $this->getMockBuilder(LocalizedRouterFactory::class)
			->disableOriginalConstructor()
			->setMethods(['getRouter'])
			->getMock();
		$localizedRouterFactoryMock
			->expects($this->once())
			->method('getRouter')
			->willReturnCallback(function ($locale, RequestContext $context) use ($localizedRouterMock) {
				$this->assertEquals('en', $locale);
				$this->assertEquals('example.com', $context->getHost());

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
					$this->assertEquals($domainConfig, $actualDomainConfig);
					$this->assertEquals('example.com', $context->getHost());
					return $friendlyUrlRouterMock;
				}
			);

		$delegatingLoaderMock = $this->getMock(DelegatingLoader::class, [], [], '', false);

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
