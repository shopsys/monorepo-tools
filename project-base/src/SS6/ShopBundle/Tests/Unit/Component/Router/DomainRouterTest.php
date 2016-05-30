<?php

namespace SS6\ShopBundle\Tests\Unit\Component\Router;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Component\Router\DomainRouter;
use SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlRouter;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;

/**
 * @UglyTest
 */
class DomainRouterTest extends PHPUnit_Framework_TestCase {

	public function testGetRouter() {
		$context = new RequestContext();
		$basicRouterMock = $this->getMockBuilder(RouterInterface::class)->getMockForAbstractClass();
		$localizedRouterMock = $this->getMockBuilder(RouterInterface::class)->getMockForAbstractClass();
		$friendlyUrlRouterMock = $this->getMockBuilder(FriendlyUrlRouter::class)
			->disableOriginalConstructor()
			->getMock();

		$domainRouter = new DomainRouter($context, $basicRouterMock, $localizedRouterMock, $friendlyUrlRouterMock);
		$this->setExpectedException(\SS6\ShopBundle\Component\Router\Exception\NotSupportedException::class);
		$domainRouter->setContext($context);
	}

}
