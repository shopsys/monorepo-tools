<?php

namespace SS6\ShopBundle\Tests\Model\Form;

use SS6\ShopBundle\Component\Router\CurrentDomainRouter;
use SS6\ShopBundle\Component\Test\FunctionalTestCase;
use SS6\ShopBundle\Model\Mail\MailTemplate;
use SS6\ShopBundle\Model\Mail\MailTemplateData;
use SS6\ShopBundle\Model\Order\Item\PriceCalculation;
use SS6\ShopBundle\Model\Order\Mail\OrderMailService;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;
use SS6\ShopBundle\Model\Setting\Setting;
use Swift_Message;
use Symfony\Cmf\Component\Routing\ChainRouter;
use Twig_Environment;

class OrderMailServiceTest extends FunctionalTestCase {

	public function testGetMailTemplateNameByStatus() {
		$routerMock = $this->getMockBuilder(ChainRouter::class)
			->disableOriginalConstructor()
			->getMock();
		$currentDomainRouterMock = $this->getMockBuilder(CurrentDomainRouter::class)
			->disableOriginalConstructor()
			->getMock();
		$twigMock = $this->getMockBuilder(Twig_Environment::class)
			->disableOriginalConstructor()
			->getMock();
		$orderItemPriceCalculationMock = $this->getMockBuilder(PriceCalculation::class)
			->disableOriginalConstructor()
			->getMock();
		$settingMock = $this->getMockBuilder(Setting::class)
			->disableOriginalConstructor()
			->getMock();

		$orderMailService = new OrderMailService(
			$settingMock,
			$routerMock,
			$currentDomainRouterMock,
			$twigMock,
			$orderItemPriceCalculationMock
		);
		
		$orderStatus1 = new OrderStatus('statusName1', OrderStatus::TYPE_NEW, 1);
		$orderStatus2 = new OrderStatus('statusName2', OrderStatus::TYPE_IN_PROGRESS, 2);

		$mailTempleteName1 = $orderMailService->getMailTemplateNameByStatus($orderStatus1);
		$mailTempleteName2 = $orderMailService->getMailTemplateNameByStatus($orderStatus2);

		$this->assertNotEmpty($mailTempleteName1);
		$this->assertInternalType('string', $mailTempleteName1);

		$this->assertNotEmpty($mailTempleteName2);
		$this->assertInternalType('string', $mailTempleteName2);

		$this->assertNotEquals($mailTempleteName1, $mailTempleteName2);
	}

	public function testGetMessageByOrder() {
		$routerMock = $this->getMockBuilder(ChainRouter::class)
			->disableOriginalConstructor()
			->getMock();
		$currentDomainRouterMock = $this->getMockBuilder(CurrentDomainRouter::class)
			->disableOriginalConstructor()
			->getMock();
		$twigMock = $this->getMockBuilder(Twig_Environment::class)
			->disableOriginalConstructor()
			->getMock();
		$orderItemPriceCalculationMock = $this->getMockBuilder(PriceCalculation::class)
			->disableOriginalConstructor()
			->getMock();
		$settingMock = $this->getMockBuilder(Setting::class)
			->disableOriginalConstructor()
			->getMock();

		$orderMailService = new OrderMailService(
			$settingMock,
			$routerMock,
			$currentDomainRouterMock,
			$twigMock,
			$orderItemPriceCalculationMock
		);

		$order = $this->getReference('order_1');

		$mailTemplateData = new MailTemplateData();
		$mailTemplateData->setSubject('subject');
		$mailTemplateData->setBody('body');
		$mailTemplate = new MailTemplate('templateName', 1, $mailTemplateData);

		$message = $orderMailService->getMessageByOrder($order, $mailTemplate);

		$this->assertInstanceOf(Swift_Message::class, $message);
		$this->assertEquals($mailTemplate->getSubject(), $message->getSubject());
		$this->assertEquals($mailTemplate->getBody(), $message->getBody());
	}

}
