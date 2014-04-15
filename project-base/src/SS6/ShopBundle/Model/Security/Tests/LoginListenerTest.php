<?php

namespace SS6\ShopBundle\Model\Security\Tests;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Security\LoginListener;

class LoginListenerTest extends PHPUnit_Framework_TestCase {
	
	public function testOnSecurityInteractiveLoginUnique() {
		$emMock = $this->getMockBuilder('Doctrine\ORM\EntityManager')
			->setMethods(array('__construct', 'persist', 'flush'))
			->disableOriginalConstructor()
			->getMock();
		$emMock->expects($this->atLeastOnce())->method('persist');
		$emMock->expects($this->once())->method('flush');
		
		$userMock = $this->getMock('SS6\ShopBundle\Model\Security\UniqueLoginInterface');
		$userMock->expects($this->once())->method('setLoginToken');
		
		$tokenMock = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
		$tokenMock->expects($this->once())->method('getUser')->will($this->returnValue($userMock));
		
		$eventMock = $this->getMockBuilder('Symfony\Component\Security\Http\Event\InteractiveLoginEvent')
			->setMethods(array('__construct', 'getAuthenticationToken'))
			->disableOriginalConstructor()
			->getMock();
		$eventMock->expects($this->once())->method('getAuthenticationToken')->will($this->returnValue($tokenMock));
	
		$loginListener = new LoginListener($emMock);
		$loginListener->onSecurityInteractiveLogin($eventMock);
	}
	
	public function testOnSecurityInteractiveLoginTimelimit() {
		$emMock = $this->getMockBuilder('Doctrine\ORM\EntityManager')
			->setMethods(array('__construct', 'persist', 'flush'))
			->disableOriginalConstructor()
			->getMock();
		$emMock->expects($this->any())->method('persist');
		$emMock->expects($this->any())->method('flush');
		
		$userMock = $this->getMock('SS6\ShopBundle\Model\Security\TimelimitLoginInterface');
		$userMock->expects($this->once())->method('setLastActivity');
		
		$tokenMock = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
		$tokenMock->expects($this->once())->method('getUser')->will($this->returnValue($userMock));
		
		$eventMock = $this->getMockBuilder('Symfony\Component\Security\Http\Event\InteractiveLoginEvent')
			->setMethods(array('__construct', 'getAuthenticationToken'))
			->disableOriginalConstructor()
			->getMock();
		$eventMock->expects($this->once())->method('getAuthenticationToken')->will($this->returnValue($tokenMock));
	
		$loginListener = new LoginListener($emMock);
		$loginListener->onSecurityInteractiveLogin($eventMock);
	}
}
