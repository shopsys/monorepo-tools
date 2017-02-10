<?php

namespace Shopsys\ShopBundle\Tests\Unit\Model\Security;

use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Model\Security\LoginService;
use StdClass;
use Symfony\Component\HttpKernel\Debug\TraceableEventDispatcher;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class LoginServiceTest extends PHPUnit_Framework_TestCase {

	public function testCheckLoginProcessWithRequestError() {
		$loginService = $this->getLoginService();

		$requestMock = $this->getMock('\Symfony\Component\HttpFoundation\Request');
		/* @var $requestMock \Symfony\Component\HttpFoundation\Request|\PHPUnit_Framework_MockObject_MockObject */
		$requestMock->expects($this->never())->method('getSession');

		$requestMock->attributes = $this->getMock('\Symfony\Component\HttpFoundation\ParameterBag');
		$requestMock->attributes->expects($this->once())->method('has')->will($this->returnValue(true));
		$requestMock->attributes->expects($this->once())->method('get')->will($this->returnValue(new StdClass()));

		$this->setExpectedException('Shopsys\ShopBundle\Model\Security\Exception\LoginFailedException');
		$loginService->checkLoginProcess($requestMock);
	}

	public function testCheckLoginProcessWithSessionError() {
		$loginService = $this->getLoginService();

		$sessionMock = $this->getMock('\Symfony\Component\HttpFoundation\Session\SessionInterface');
		$sessionMock->expects($this->atLeastOnce())->method('get')->will($this->returnValue(new StdClass()));
		$sessionMock->expects($this->atLeastOnce())->method('remove');

		$requestMock = $this->getMock('\Symfony\Component\HttpFoundation\Request');
		/* @var $requestMock \Symfony\Component\HttpFoundation\Request|\PHPUnit_Framework_MockObject_MockObject */
		$requestMock->expects($this->once())->method('getSession')->will($this->returnValue($sessionMock));

		$requestMock->attributes = $this->getMock('\Symfony\Component\HttpFoundation\ParameterBag');
		$requestMock->attributes->expects($this->once())->method('has')->will($this->returnValue(false));
		$requestMock->attributes->expects($this->never())->method('get');

		$this->setExpectedException('Shopsys\ShopBundle\Model\Security\Exception\LoginFailedException');
		$loginService->checkLoginProcess($requestMock);
	}

	public function testCheckLoginProcessWithoutSessionError() {
		$loginService = $this->getLoginService();

		$sessionMock = $this->getMock('\Symfony\Component\HttpFoundation\Session\SessionInterface');
		$sessionMock->expects($this->once())->method('get')->will($this->returnValue(null));
		$sessionMock->expects($this->once())->method('remove');

		$requestMock = $this->getMock('\Symfony\Component\HttpFoundation\Request');
		/* @var $requestMock \Symfony\Component\HttpFoundation\Request|\PHPUnit_Framework_MockObject_MockObject */
		$requestMock->expects($this->once())->method('getSession')->will($this->returnValue($sessionMock));

		$requestMock->attributes = $this->getMock('\Symfony\Component\HttpFoundation\ParameterBag');
		$requestMock->attributes->expects($this->once())->method('has')->will($this->returnValue(false));
		$requestMock->attributes->expects($this->never())->method('get');

		$this->assertSame(true, $loginService->checkLoginProcess($requestMock));
	}

	/**
	 * @return \Shopsys\ShopBundle\Model\Security\LoginService
	 */
	private function getLoginService() {
		$tokenStorageMock = $this->getMock(TokenStorage::class, [], [], '', false);
		$traceableEventDispatcherMock = $this->getMock(TraceableEventDispatcher::class, [], [], '', false);

		return new LoginService($tokenStorageMock, $traceableEventDispatcherMock);
	}
}
