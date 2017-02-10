<?php

namespace Shopsys\ShopBundle\Tests\Unit\Model\Security;

use Doctrine\ORM\EntityManager;
use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Model\Administrator\Activity\AdministratorActivityFacade;
use Shopsys\ShopBundle\Model\Administrator\Administrator;
use Shopsys\ShopBundle\Model\Customer\User;
use Shopsys\ShopBundle\Model\Order\OrderFlowFacade;
use Shopsys\ShopBundle\Model\Security\LoginListener;
use Shopsys\ShopBundle\Model\Security\TimelimitLoginInterface;
use Shopsys\ShopBundle\Model\Security\UniqueLoginInterface;
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

        $userMock = $this->getMock(UniqueLoginInterface::class);
        $userMock->expects($this->once())->method('setLoginToken');

        $tokenMock = $this->getMock(TokenInterface::class);
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

        $administratorActivityFacadeMock = $this->getMock(AdministratorActivityFacade::class, [], [], '', false);

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

        $userMock = $this->getMock(TimelimitLoginInterface::class);
        $userMock->expects($this->once())->method('setLastActivity');

        $tokenMock = $this->getMock(TokenInterface::class);
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

        $administratorActivityFacadeMock = $this->getMock(AdministratorActivityFacade::class, [], [], '', false);

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

        $tokenMock = $this->getMock(TokenInterface::class);
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

        $administratorActivityFacadeMock = $this->getMock(AdministratorActivityFacade::class, [], [], '', false);

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

        $administratorMock = $this->getMock(Administrator::class, [], [], '', false);
        $administratorMock->expects($this->once())->method('setLoginToken');

        $tokenMock = $this->getMock(TokenInterface::class);
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
