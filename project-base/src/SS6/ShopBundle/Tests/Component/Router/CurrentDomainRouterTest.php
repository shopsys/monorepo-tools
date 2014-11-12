<?php

namespace SS6\ShopBundle\Tests\Component\Router;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Component\Router\CurrentDomainRouter;
use SS6\ShopBundle\Component\Router\LocalizedRouterFactory;
use SS6\ShopBundle\Model\Domain\Config\DomainConfig;
use SS6\ShopBundle\Model\Domain\Domain;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\RequestContext;

class CurrentDomainRouterTest extends PHPUnit_Framework_TestCase {

	public function testDelegateRouter() {
		$domainConfigs = new DomainConfig(1, 'example.com', 'en', 'en');
		$domain = new Domain([$domainConfigs]);
		$domain->switchDomainById(1);

		$generateResult = 'generateResult';
		$pathInfo = 'pathInfo';
		$matchResult = 'matchResult';
		$getRouteCollectionResult = 'getRouteCollectionResult';
		$routerMock = $this->getMockBuilder(Router::class)
			->setMethods(['__construct', 'generate', 'match', 'getRouteCollection'])
			->disableOriginalConstructor()
			->getMock();
		$routerMock->expects($this->once())->method('generate')->willReturn($generateResult);
		$routerMock->expects($this->once())->method('match')->with($this->equalTo($pathInfo))->willReturn($matchResult);
		$routerMock->expects($this->once())->method('getRouteCollection')->willReturn($getRouteCollectionResult);

		$domainRouterFactoryMock = $this->getMockBuilder(LocalizedRouterFactory::class)
			->setMethods(['__construct', 'getRouter'])
			->disableOriginalConstructor()
			->getMock();
		$domainRouterFactoryMock->expects($this->exactly(3))->method('getRouter')->willReturn($routerMock);

		$context = new RequestContext();

		$currentDomainRouter = new CurrentDomainRouter($domain, $domainRouterFactoryMock);
		$currentDomainRouter->setContext($context);

		$this->assertEquals($generateResult, $currentDomainRouter->generate(''));
		$this->assertEquals($matchResult, $currentDomainRouter->match($pathInfo));
		$this->assertEquals($getRouteCollectionResult, $currentDomainRouter->getRouteCollection());
	}

}
