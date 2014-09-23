<?php

namespace SS6\ShopBundle\Tests\Model\Redirect;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Redirect\SubResponseRedirectListener;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\Response;

class SubResponseRedirectListenerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @param bool $redirect
	 * @param bool $send
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function getResponseMock($redirect = false, $send = false) {
		$responseMock = $this->getMockBuilder(Response::class)
			->setMethods(['isRedirection', 'send'])
			->getMock();
		$responseMock->expects($this->once())->method('isRedirection')->will($this->returnValue($redirect));
		$responseMock->expects($send ? $this->once() : $this->never())->method('send');

		return $responseMock;
	}

	public function testOnKernelResponseOneMasterResponse() {
		$eventMock = $this->getMockBuilder(FilterResponseEvent::class)
			->setMethods(['__construct', 'isMasterRequest'])
			->disableOriginalConstructor()
			->getMock();
		$eventMock->expects($this->once())->method('isMasterRequest')->will($this->returnValue(true));

		$subResponseRedirectListener = new SubResponseRedirectListener();
		$subResponseRedirectListener->onKernelResponse($eventMock);
	}

	public function testOnKernelResponseManyRedirectResponses() {
		$eventMock1 = $this->getMockBuilder(FilterResponseEvent::class)
			->setMethods(['__construct', 'isMasterRequest', 'getResponse'])
			->disableOriginalConstructor()
			->getMock();
		$eventMock1->expects($this->once())->method('isMasterRequest')->will($this->returnValue(false));
		$eventMock1->expects($this->once())->method('getResponse')->will($this->returnValue($this->getResponseMock(true)));

		$eventMock2 = $this->getMockBuilder(FilterResponseEvent::class)
			->setMethods(['__construct', 'isMasterRequest', 'getResponse'])
			->disableOriginalConstructor()
			->getMock();
		$eventMock2->expects($this->once())->method('isMasterRequest')->will($this->returnValue(false));
		$eventMock2->expects($this->once())->method('getResponse')->will($this->returnValue($this->getResponseMock()));

		$eventMock3 = $this->getMockBuilder(FilterResponseEvent::class)
			->setMethods(['__construct', 'isMasterRequest', 'getResponse'])
			->disableOriginalConstructor()
			->getMock();
		$eventMock3->expects($this->once())->method('isMasterRequest')->will($this->returnValue(false));
		$eventMock3->expects($this->once())->method('getResponse')->will($this->returnValue($this->getResponseMock(true)));

		$subResponseRedirectListener = new SubResponseRedirectListener();
		$subResponseRedirectListener->onKernelResponse($eventMock1);
		$subResponseRedirectListener->onKernelResponse($eventMock2);

		$this->setExpectedException(\SS6\ShopBundle\Model\Redirect\Exception\TooManyRedirectResponsesException::class);
		$subResponseRedirectListener->onKernelResponse($eventMock3);
	}
	
	public function testOnKernelResponse() {
		$eventMock1 = $this->getMockBuilder(FilterResponseEvent::class)
			->setMethods(['__construct', 'isMasterRequest', 'getResponse'])
			->disableOriginalConstructor()
			->getMock();
		$eventMock1->expects($this->once())->method('isMasterRequest')->will($this->returnValue(false));
		$eventMock1->expects($this->once())->method('getResponse')->will($this->returnValue($this->getResponseMock(true, true)));

		$eventMock2 = $this->getMockBuilder(FilterResponseEvent::class)
			->setMethods(['__construct', 'isMasterRequest', 'getResponse'])
			->disableOriginalConstructor()
			->getMock();
		$eventMock2->expects($this->once())->method('isMasterRequest')->will($this->returnValue(false));
		$eventMock2->expects($this->once())->method('getResponse')->will($this->returnValue($this->getResponseMock()));

		$eventMock3 = $this->getMockBuilder(FilterResponseEvent::class)
			->setMethods(['__construct', 'isMasterRequest'])
			->disableOriginalConstructor()
			->getMock();
		$eventMock3->expects($this->once())->method('isMasterRequest')->will($this->returnValue(true));

		$subResponseRedirectListener = new SubResponseRedirectListener();
		$subResponseRedirectListener->onKernelResponse($eventMock1);
		$subResponseRedirectListener->onKernelResponse($eventMock2);
		$subResponseRedirectListener->onKernelResponse($eventMock3);
	}


}
