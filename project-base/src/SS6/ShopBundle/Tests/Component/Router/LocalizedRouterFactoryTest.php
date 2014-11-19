<?php

namespace SS6\ShopBundle\Tests\Component\Router;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Component\Router\LocalizedRouterFactory;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Bundle\FrameworkBundle\Routing\DelegatingLoader;

class LocalizedRouterFactoryTest extends PHPUnit_Framework_TestCase {

	public function testGetRouterRouterNotResolvedException() {
		$localeRoutersConfiguration = [];
		$delegatingLoaderMock = $this->getMock(DelegatingLoader::class, [], [], '', false);
		$context = new RequestContext();

		$localizedRouterFactory = new LocalizedRouterFactory($localeRoutersConfiguration, $delegatingLoaderMock);
		$this->setExpectedException(\SS6\ShopBundle\Component\Router\Exception\RouterNotResolvedException::class);
		$localizedRouterFactory->getRouter('en', $context);
	}

	public function testGetRouter() {
		$localeRoutersConfiguration = ['en' => 'pathToResource', 'cs' => 'pathToAnotherResource'];
		$delegatingLoaderMock = $this->getMock(DelegatingLoader::class, [], [], '', false);
		$context1 = new RequestContext();
		$context1->setHost('host1');
		$context2 = new RequestContext();
		$context2->setHost('host2');

		$localizedRouterFactory = new LocalizedRouterFactory($localeRoutersConfiguration, $delegatingLoaderMock);

		$router1 = $localizedRouterFactory->getRouter('en', $context1);
		$router2 = $localizedRouterFactory->getRouter('en', $context2);
		$router3 = $localizedRouterFactory->getRouter('en', $context1);
		$router4 = $localizedRouterFactory->getRouter('cs', $context1);

		$this->assertInstanceOf(RouterInterface::class, $router1);
		$this->assertInstanceOf(RouterInterface::class, $router2);
		$this->assertInstanceOf(RouterInterface::class, $router3);
		$this->assertInstanceOf(RouterInterface::class, $router4);

		$this->assertEquals($router1, $router3);
		$this->assertNotEquals($router1, $router2);
		$this->assertNotEquals($router1, $router4);
	}

}
