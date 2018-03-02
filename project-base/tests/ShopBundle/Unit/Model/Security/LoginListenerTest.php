<?php

namespace Tests\ShopBundle\Unit\Model\Security;

use Doctrine\ORM\EntityManager;
use PHPUnit_Framework_TestCase;
use Shopsys\FrameworkBundle\Model\Administrator\Activity\AdministratorActivityFacade;
use Shopsys\FrameworkBundle\Model\Administrator\Administrator;
use Shopsys\FrameworkBundle\Model\Customer\User;
use Shopsys\FrameworkBundle\Model\Order\OrderFlowFacade;
use Shopsys\FrameworkBundle\Model\Security\LoginListener;
use Shopsys\FrameworkBundle\Model\Security\TimelimitLoginInterface;
use Shopsys\FrameworkBundle\Model\Security\UniqueLoginInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginListenerTest extends PHPUnit_Framework_TestCase
{
    public function testOnSecurityInteractiveLoginUnique()
    {
        $emMock = $this->getMockBuilder(EntityManager::class)
            ->setMethods(['__construct', 'persist', 'flush'])
            ->disableOriginalConstructor()
            ->getMock();
        $emMock->expects($this->once())->method('flush');

        $userMock = $this->createMock(UniqueLoginInterface::class);
        $userMock->expects($this->once())->method('setLoginToken');

        $tokenMock = $this->createMock(TokenInterface::class);
        $tokenMock->expects($this->once())->method('getUser')->will($this->returnValue($userMock));

        $eventMock = $this->getMockBuilder(InteractiveLoginEvent::class)
            ->setMethods(['__construct', 'getAuthenticationToken'])
            ->disableOriginalConstructor()
            ->getMock();
        $eventMock->expects($this->once())->method('getAuthenticationToken')->will($this->returnValue($tokenMock));

        $orderFlowFacadeMock = $this->getMockBuilder(OrderFlowFacade::class)
            ->setMethods(['__construct'])
            ->disableOriginalConstructor()
            ->getMock();

        $administratorActivityFacadeMock = $this->createMock(AdministratorActivityFacade::class);

        $loginListener = new LoginListener($emMock, $orderFlowFacadeMock, $administratorActivityFacadeMock);
        $loginListener->onSecurityInteractiveLogin($eventMock);
    }

    public function testOnSecurityInteractiveLoginTimelimit()
    {
        $emMock = $this->getMockBuilder(EntityManager::class)
            ->setMethods(['__construct', 'persist', 'flush'])
            ->disableOriginalConstructor()
            ->getMock();
        $emMock->expects($this->any())->method('flush');

        $userMock = $this->createMock(TimelimitLoginInterface::class);
        $userMock->expects($this->once())->method('setLastActivity');

        $tokenMock = $this->createMock(TokenInterface::class);
        $tokenMock->expects($this->once())->method('getUser')->will($this->returnValue($userMock));

        $eventMock = $this->getMockBuilder(InteractiveLoginEvent::class)
            ->setMethods(['__construct', 'getAuthenticationToken'])
            ->disableOriginalConstructor()
            ->getMock();
        $eventMock->expects($this->once())->method('getAuthenticationToken')->will($this->returnValue($tokenMock));

        $orderFlowFacadeMock = $this->getMockBuilder(OrderFlowFacade::class)
            ->setMethods(['__construct'])
            ->disableOriginalConstructor()
            ->getMock();

        $administratorActivityFacadeMock = $this->createMock(AdministratorActivityFacade::class);

        $loginListener = new LoginListener($emMock, $orderFlowFacadeMock, $administratorActivityFacadeMock);
        $loginListener->onSecurityInteractiveLogin($eventMock);
    }

    public function testOnSecurityInteractiveLoginResetOrderForm()
    {
        $emMock = $this->getMockBuilder(EntityManager::class)
            ->setMethods(['__construct', 'persist', 'flush'])
            ->disableOriginalConstructor()
            ->getMock();
        $emMock->expects($this->any())->method('flush');

        $userMock = $this->getMockBuilder(User::class)
            ->setMethods(['__construct'])
            ->disableOriginalConstructor()
            ->getMock();

        $tokenMock = $this->createMock(TokenInterface::class);
        $tokenMock->expects($this->once())->method('getUser')->will($this->returnValue($userMock));

        $eventMock = $this->getMockBuilder(InteractiveLoginEvent::class)
            ->setMethods(['__construct', 'getAuthenticationToken'])
            ->disableOriginalConstructor()
            ->getMock();
        $eventMock->expects($this->once())->method('getAuthenticationToken')->will($this->returnValue($tokenMock));

        $orderFlowFacadeMock = $this->getMockBuilder(OrderFlowFacade::class)
            ->setMethods(['__construct', 'resetOrderForm'])
            ->disableOriginalConstructor()
            ->getMock();
        $orderFlowFacadeMock->expects($this->once())->method('resetOrderForm');

        $administratorActivityFacadeMock = $this->createMock(AdministratorActivityFacade::class);

        $loginListener = new LoginListener($emMock, $orderFlowFacadeMock, $administratorActivityFacadeMock);
        $loginListener->onSecurityInteractiveLogin($eventMock);
    }

    public function testOnSecurityInteractiveLoginAdministrator()
    {
        $emMock = $this->getMockBuilder(EntityManager::class)
            ->setMethods(['__construct', 'persist', 'flush'])
            ->disableOriginalConstructor()
            ->getMock();
        $emMock->expects($this->once())->method('flush');

        $administratorMock = $this->createMock(Administrator::class);
        $administratorMock->expects($this->once())->method('setLoginToken');

        $tokenMock = $this->createMock(TokenInterface::class);
        $tokenMock->expects($this->once())->method('getUser')->will($this->returnValue($administratorMock));

        $eventMock = $this->getMockBuilder(InteractiveLoginEvent::class)
            ->setMethods(['__construct', 'getAuthenticationToken', 'getRequest'])
            ->disableOriginalConstructor()
            ->getMock();
        $eventMock->expects($this->once())->method('getAuthenticationToken')->will($this->returnValue($tokenMock));
        $eventMock->expects($this->once())->method('getRequest')->will($this->returnValue(new Request()));

        $orderFlowFacadeMock = $this->getMockBuilder(OrderFlowFacade::class)
            ->setMethods(['__construct'])
            ->disableOriginalConstructor()
            ->getMock();

        $administratorActivityFacadeMock = $this->getMockBuilder(AdministratorActivityFacade::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $administratorActivityFacadeMock->expects($this->once())->method('create');

        $loginListener = new LoginListener($emMock, $orderFlowFacadeMock, $administratorActivityFacadeMock);
        $loginListener->onSecurityInteractiveLogin($eventMock);
    }
}
