<?php

namespace SS6\CoreBundle\Model\Security\Tests\Service;

use PHPUnit_Framework_TestCase;
use SS6\CoreBundle\Model\Security\Service\LoginService;
use StdClass;

class LoginServiceTest extends PHPUnit_Framework_TestCase {

	public function testCheckLoginProcessWithRequestError() {
		$loginService = new LoginService();

		$requestMock = $this->getMock('\Symfony\Component\HttpFoundation\Request');
		$requestMock->expects($this->never())->method('getSession');

		$requestMock->attributes = $this->getMock('\Symfony\Component\HttpFoundation\ParameterBag');
		$requestMock->attributes->expects($this->once())->method('has')->will($this->returnValue(true));
		$requestMock->attributes->expects($this->once())->method('get')->will($this->returnValue(new StdClass));

		$this->setExpectedException('SS6\CoreBundle\Model\Security\Exception\LoginFailedException');
		$loginService->checkLoginProcess($requestMock);
	}

	public function testCheckLoginProcessWithSessionError() {
		$loginService = new LoginService();

		$sessionMock = $this->getMock('\Symfony\Component\HttpFoundation\Session\SessionInterface');
		$sessionMock->expects($this->atLeastOnce())->method('get')->will($this->returnValue(new StdClass));
		$sessionMock->expects($this->atLeastOnce())->method('remove');

		$requestMock = $this->getMock('\Symfony\Component\HttpFoundation\Request');
		$requestMock->expects($this->once())->method('getSession')->will($this->returnValue($sessionMock));

		$requestMock->attributes = $this->getMock('\Symfony\Component\HttpFoundation\ParameterBag');
		$requestMock->attributes->expects($this->once())->method('has')->will($this->returnValue(false));
		$requestMock->attributes->expects($this->never())->method('get');

		$this->setExpectedException('SS6\CoreBundle\Model\Security\Exception\LoginFailedException');
		$loginService->checkLoginProcess($requestMock);
	}

	public function testCheckLoginProcessWithoutSessionError() {
		$loginService = new LoginService();

		$sessionMock = $this->getMock('\Symfony\Component\HttpFoundation\Session\SessionInterface');
		$sessionMock->expects($this->once())->method('get')->will($this->returnValue(null));
		$sessionMock->expects($this->once())->method('remove');

		$requestMock = $this->getMock('\Symfony\Component\HttpFoundation\Request');
		$requestMock->expects($this->once())->method('getSession')->will($this->returnValue($sessionMock));

		$requestMock->attributes = $this->getMock('\Symfony\Component\HttpFoundation\ParameterBag');
		$requestMock->attributes->expects($this->once())->method('has')->will($this->returnValue(false));
		$requestMock->attributes->expects($this->never())->method('get');

		$this->assertEquals(true, $loginService->checkLoginProcess($requestMock));
	}
}
