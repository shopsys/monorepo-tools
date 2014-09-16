<?php

namespace SS6\ShopBundle\Tests\Model\Form;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Mail\MailTemplate;
use SS6\ShopBundle\Model\Mail\MailTemplateData;
use SS6\ShopBundle\Model\Order\Mail\OrderMailService;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;
use Swift_Message;

class OrderMailServiceTest extends PHPUnit_Framework_TestCase {

	public function testGetMailTemplateNameByStatus() {
		$orderMailService = new OrderMailService('no-reply@netdevelo.cz');
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
		$orderMailService = new OrderMailService('no-reply@netdevelo.cz');

		$orderMock = $this->getMock(Order::class, [], [], '', false);

		$mailTemplateData = new MailTemplateData();
		$mailTemplateData->setSubject('subject');
		$mailTemplateData->setBody('body');
		$mailTemplate = new MailTemplate('templateName', $mailTemplateData);

		$message = $orderMailService->getMessageByOrder($orderMock, $mailTemplate);

		$this->assertInstanceOf(Swift_Message::class, $message);
		$this->assertEquals($mailTemplate->getSubject(), $message->getSubject());
		$this->assertEquals($mailTemplate->getBody(), $message->getBody());
	}
}
