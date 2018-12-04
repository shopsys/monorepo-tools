<?php

namespace Tests\FrameworkBundle\Unit\Model\Security;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Model\Security\Authenticator;
use StdClass;
use Symfony\Component\HttpKernel\Debug\TraceableEventDispatcher;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class AuthenticatorTest extends TestCase
{
    public function testCheckLoginProcessWithRequestError()
    {
        $authenticator = $this->getAuthenticator();

        $requestMock = $this->createMock('\Symfony\Component\HttpFoundation\Request');
        /* @var $requestMock \Symfony\Component\HttpFoundation\Request|\PHPUnit\Framework\MockObject\MockObject */
        $requestMock->expects($this->never())->method('getSession');

        $requestMock->attributes = $this->createMock('\Symfony\Component\HttpFoundation\ParameterBag');
        $requestMock->attributes->expects($this->once())->method('has')->will($this->returnValue(true));
        $requestMock->attributes->expects($this->once())->method('get')->will($this->returnValue(new StdClass()));

        $this->expectException('Shopsys\FrameworkBundle\Model\Security\Exception\LoginFailedException');
        $authenticator->checkLoginProcess($requestMock);
    }

    public function testCheckLoginProcessWithSessionError()
    {
        $authenticator = $this->getAuthenticator();

        $sessionMock = $this->createMock('\Symfony\Component\HttpFoundation\Session\SessionInterface');
        $sessionMock->expects($this->atLeastOnce())->method('get')->will($this->returnValue(new StdClass()));
        $sessionMock->expects($this->atLeastOnce())->method('remove');

        $requestMock = $this->createMock('\Symfony\Component\HttpFoundation\Request');
        /* @var $requestMock \Symfony\Component\HttpFoundation\Request|\PHPUnit\Framework\MockObject\MockObject */
        $requestMock->expects($this->once())->method('getSession')->will($this->returnValue($sessionMock));

        $requestMock->attributes = $this->createMock('\Symfony\Component\HttpFoundation\ParameterBag');
        $requestMock->attributes->expects($this->once())->method('has')->will($this->returnValue(false));
        $requestMock->attributes->expects($this->never())->method('get');

        $this->expectException('Shopsys\FrameworkBundle\Model\Security\Exception\LoginFailedException');
        $authenticator->checkLoginProcess($requestMock);
    }

    public function testCheckLoginProcessWithoutSessionError()
    {
        $authenticator = $this->getAuthenticator();

        $sessionMock = $this->createMock('\Symfony\Component\HttpFoundation\Session\SessionInterface');
        $sessionMock->expects($this->once())->method('get')->will($this->returnValue(null));
        $sessionMock->expects($this->once())->method('remove');

        $requestMock = $this->createMock('\Symfony\Component\HttpFoundation\Request');
        /* @var $requestMock \Symfony\Component\HttpFoundation\Request|\PHPUnit\Framework\MockObject\MockObject */
        $requestMock->expects($this->once())->method('getSession')->will($this->returnValue($sessionMock));

        $requestMock->attributes = $this->createMock('\Symfony\Component\HttpFoundation\ParameterBag');
        $requestMock->attributes->expects($this->once())->method('has')->will($this->returnValue(false));
        $requestMock->attributes->expects($this->never())->method('get');

        $this->assertSame(true, $authenticator->checkLoginProcess($requestMock));
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Security\Authenticator
     */
    private function getAuthenticator()
    {
        $tokenStorageMock = $this->createMock(TokenStorage::class);
        $traceableEventDispatcherMock = $this->createMock(TraceableEventDispatcher::class);

        return new Authenticator($tokenStorageMock, $traceableEventDispatcherMock);
    }
}
