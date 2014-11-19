<?php

namespace SS6\ShopBundle\Tests\Component\Router;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Component\Router\DomainRouter;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;

class DomainRouterTest extends PHPUnit_Framework_TestCase {

	public function testGetRouter() {
		$context = new RequestContext();
		$basicRouterMock = $this->getMockBuilder(RouterInterface::class)->getMockForAbstractClass();
		$localizedRouterMock = $this->getMockBuilder(RouterInterface::class)->getMockForAbstractClass();

		$domainRouter = new DomainRouter($context, $basicRouterMock, $localizedRouterMock);
		$this->setExpectedException(\SS6\ShopBundle\Component\Router\Exception\NotSupportedException::class);
		$domainRouter->setContext($context);
	}

}
