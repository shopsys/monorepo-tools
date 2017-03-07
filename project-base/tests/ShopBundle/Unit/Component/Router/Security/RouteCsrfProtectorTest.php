<?php

namespace Tests\ShopBundle\Unit\Component\Router\Security;

use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Component\Router\Security\RouteCsrfProtector;
use Tests\ShopBundle\Unit\Component\Router\Security\DummyController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManager;

class RouteCsrfProtectorTest extends PHPUnit_Framework_TestCase
{
    public function testSubRequest()
    {
        $annotationReader = new AnnotationReader();
        $tokenManagerMock = $this->getMock(CsrfTokenManager::class, [], [], '', false);

        $eventMock = $this->getMockBuilder(FilterControllerEvent::class)
            ->disableOriginalConstructor()
            ->setMethods(['isMasterRequest', 'getController'])
            ->getMock();
        $eventMock->expects($this->atLeastOnce())->method('isMasterRequest')->willReturn(false);
        $eventMock->expects($this->never())->method('getController');

        $routeCsrfProtector = new RouteCsrfProtector($annotationReader, $tokenManagerMock);
        $routeCsrfProtector->onKernelController($eventMock);
    }

    public function testRequestWithoutProtection()
    {
        $annotationReader = new AnnotationReader();
        $tokenManagerMock = $this->getMock(CsrfTokenManager::class, [], [], '', false);

        $eventMock = $this->getMockBuilder(FilterControllerEvent::class)
            ->disableOriginalConstructor()
            ->setMethods(['isMasterRequest', 'getController', 'getRequest'])
            ->getMock();
        $eventMock->expects($this->atLeastOnce())->method('isMasterRequest')->willReturn(true);
        $eventMock
            ->expects($this->atLeastOnce())
            ->method('getController')
            ->willReturn([DummyController::class, 'withoutProtectionAction']);
        $eventMock->expects($this->never())->method('getRequest');

        $routeCsrfProtector = new RouteCsrfProtector($annotationReader, $tokenManagerMock);
        $routeCsrfProtector->onKernelController($eventMock);
    }

    public function testRequestWithProtection()
    {
        $validCsrfToken = 'validCsrfToken';
        $request = new Request([
            RouteCsrfProtector::CSRF_TOKEN_REQUEST_PARAMETER => $validCsrfToken,
        ]);
        $annotationReader = new AnnotationReader();
        $tokenManagerMock = $this->getMock(CsrfTokenManager::class, ['isTokenValid'], [], '', false);
        $tokenManagerMock
            ->expects($this->atLeastOnce())
            ->method('isTokenValid')
            ->with($this->callback(function (CsrfToken $token) use ($validCsrfToken) {
                return $token->getValue() === $validCsrfToken;
            }))
            ->willReturn(true);

        $eventMock = $this->getMockBuilder(FilterControllerEvent::class)
            ->disableOriginalConstructor()
            ->setMethods(['isMasterRequest', 'getController', 'getRequest'])
            ->getMock();
        $eventMock->expects($this->atLeastOnce())->method('isMasterRequest')->willReturn(true);
        $eventMock
            ->expects($this->atLeastOnce())
            ->method('getController')
            ->willReturn([DummyController::class, 'withProtectionAction']);
        $eventMock->expects($this->atLeastOnce())->method('getRequest')->willReturn($request);

        $routeCsrfProtector = new RouteCsrfProtector($annotationReader, $tokenManagerMock);
        $routeCsrfProtector->onKernelController($eventMock);
    }

    public function testRequestWithProtectionWithoutCsrfToken()
    {
        $request = new Request();
        $annotationReader = new AnnotationReader();
        $tokenManagerMock = $this->getMock(CsrfTokenManager::class, [], [], '', false);

        $eventMock = $this->getMockBuilder(FilterControllerEvent::class)
            ->disableOriginalConstructor()
            ->setMethods(['isMasterRequest', 'getController', 'getRequest'])
            ->getMock();
        $eventMock->expects($this->atLeastOnce())->method('isMasterRequest')->willReturn(true);
        $eventMock
            ->expects($this->atLeastOnce())
            ->method('getController')
            ->willReturn([DummyController::class, 'withProtectionAction']);
        $eventMock->expects($this->atLeastOnce())->method('getRequest')->willReturn($request);

        $routeCsrfProtector = new RouteCsrfProtector($annotationReader, $tokenManagerMock);

        $this->setExpectedException(\Symfony\Component\HttpKernel\Exception\BadRequestHttpException::class);
        $routeCsrfProtector->onKernelController($eventMock);
    }

    public function testRequestWithProtectionWithInvalidCsrfToken()
    {
        $invalidCsrfToken = 'invalidCsrfToken';
        $request = new Request([
            RouteCsrfProtector::CSRF_TOKEN_REQUEST_PARAMETER => $invalidCsrfToken,
        ]);
        $annotationReader = new AnnotationReader();
        $tokenManagerMock = $this->getMock(CsrfTokenManager::class, ['isTokenValid'], [], '', false);
        $tokenManagerMock
            ->expects($this->atLeastOnce())
            ->method('isTokenValid')
            ->with($this->callback(function (CsrfToken $token) use ($invalidCsrfToken) {
                return $token->getValue() === $invalidCsrfToken;
            }))
            ->willReturn(false);

        $eventMock = $this->getMockBuilder(FilterControllerEvent::class)
            ->disableOriginalConstructor()
            ->setMethods(['isMasterRequest', 'getController', 'getRequest'])
            ->getMock();
        $eventMock->expects($this->atLeastOnce())->method('isMasterRequest')->willReturn(true);
        $eventMock
            ->expects($this->atLeastOnce())
            ->method('getController')
            ->willReturn([DummyController::class, 'withProtectionAction']);
        $eventMock->expects($this->atLeastOnce())->method('getRequest')->willReturn($request);

        $routeCsrfProtector = new RouteCsrfProtector($annotationReader, $tokenManagerMock);

        $this->setExpectedException(\Symfony\Component\HttpKernel\Exception\BadRequestHttpException::class);
        $routeCsrfProtector->onKernelController($eventMock);
    }
}
