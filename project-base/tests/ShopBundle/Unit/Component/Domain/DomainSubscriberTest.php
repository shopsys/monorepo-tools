<?php

namespace Tests\ShopBundle\Unit\Component\Domain;

use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Domain\DomainSubscriber;
use Shopsys\ShopBundle\Component\Setting\Setting;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class DomainSubscriberTest extends PHPUnit_Framework_TestCase
{
    public function testOnKernelRequestWithoutMasterRequest()
    {
        $eventMock = $this->getMockBuilder(GetResponseEvent::class)
            ->setMethods(['__construct', 'isMasterRequest'])
            ->disableOriginalConstructor()
            ->getMock();
        $eventMock->expects($this->once())->method('isMasterRequest')->will($this->returnValue(false));
        $settingMock = $this->createMock(Setting::class);

        $domain = new Domain([], $settingMock);

        $domainSubscriber = new DomainSubscriber($domain);
        $domainSubscriber->onKernelRequest($eventMock);
    }

    public function testOnKernelRequestWithMasterRequestAndSetDomain()
    {
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

        $domainSubscriber = new DomainSubscriber($domainMock);
        $domainSubscriber->onKernelRequest($eventMock);
    }

    public function testOnKernelRequestWithMasterRequest()
    {
        $getRequestResult = new Request();
        $eventMock = $this->getMockBuilder(GetResponseEvent::class)
            ->setMethods(['__construct', 'isMasterRequest', 'getRequest'])
            ->disableOriginalConstructor()
            ->getMock();
        $eventMock->expects($this->once())->method('isMasterRequest')->will($this->returnValue(true));
        $eventMock->expects($this->once())->method('getRequest')->will($this->returnValue($getRequestResult));

        $exception = new \Shopsys\ShopBundle\Component\Domain\Exception\NoDomainSelectedException();
        $domainMock = $this->getMockBuilder(Domain::class)
            ->setMethods(['__construct', 'getId', 'switchDomainByRequest'])
            ->disableOriginalConstructor()
            ->getMock();
        $domainMock->expects($this->once())->method('getId')->willThrowException($exception);
        $domainMock->expects($this->once())->method('switchDomainByRequest')->with($this->equalTo($getRequestResult));

        $domainSubscriber = new DomainSubscriber($domainMock);
        $domainSubscriber->onKernelRequest($eventMock);
    }
}
