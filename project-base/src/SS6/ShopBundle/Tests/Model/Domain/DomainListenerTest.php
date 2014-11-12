<?php

namespace SS6\ShopBundle\Tests\Model\Domain;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Domain\DomainListener;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Request;

class DomainListenerTest extends PHPUnit_Framework_TestCase {

	public function testOnKernelRequestWithoutMasterRequest() {
		$eventMock = $this->getMockBuilder(GetResponseEvent::class)
			->setMethods(['__construct', 'isMasterRequest'])
			->disableOriginalConstructor()
			->getMock();
		$eventMock->expects($this->once())->method('isMasterRequest')->will($this->returnValue(false));

		$domain = new Domain([]);

		$domainListener = new DomainListener($domain);
		$domainListener->onKernelRequest($eventMock);
	}

	public function testOnKernelRequestWithMasterRequestAndSetDomain() {
		$eventMock = $this->getMockBuilder(GetResponseEvent::class)
			->setMethods(['__construct', 'isMasterRequest'])
			->disableOriginalConstructor()
			->getMock();
		$eventMock->expects($this->once())->method('isMasterRequest')->will($this->returnValue(true));

		$domainMock = $this->getMockBuilder(Domain::class)
			->setMethods(['__construct', 'getId'])
			->disableOriginalConstructor()
			->getMock();
		$domainMock->expects($this->once())->method('getId');

		$domainListener = new DomainListener($domainMock);
		$domainListener->onKernelRequest($eventMock);
	}

	public function testOnKernelRequestWithMasterRequest() {
		$getRequestResult = new Request();
		$eventMock = $this->getMockBuilder(GetResponseEvent::class)
			->setMethods(['__construct', 'isMasterRequest', 'getRequest'])
			->disableOriginalConstructor()
			->getMock();
		$eventMock->expects($this->once())->method('isMasterRequest')->will($this->returnValue(true));
		$eventMock->expects($this->once())->method('getRequest')->will($this->returnValue($getRequestResult));

		$exception = new \SS6\ShopBundle\Model\Domain\Exception\NoDomainSelectedException();
		$domainMock = $this->getMockBuilder(Domain::class)
			->setMethods(['__construct', 'getId', 'switchDomainByRequest'])
			->disableOriginalConstructor()
			->getMock();
		$domainMock->expects($this->once())->method('getId')->willThrowException($exception);
		$domainMock->expects($this->once())->method('switchDomainByRequest')->with($this->equalTo($getRequestResult));

		$domainListener = new DomainListener($domainMock);
		$domainListener->onKernelRequest($eventMock);
	}

}
