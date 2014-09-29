<?php

namespace SS6\ShopBundle\Tests\Model\Form;

use Doctrine\ORM\EntityManager;
use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Mail\MailTemplate;
use SS6\ShopBundle\Model\Mail\MailTemplateData;
use SS6\ShopBundle\Model\Mail\MailTemplateFacade;
use SS6\ShopBundle\Model\Mail\MailTemplateRepository;
use SS6\ShopBundle\Model\Order\Status\OrderStatusRepository;

class MailTemplateFacadeTest extends PHPUnit_Framework_TestCase {

	public function testEdit() {
		$emMock = $this->getMockBuilder(EntityManager::class)
			->setMethods(['__construct', 'flush'])
			->disableOriginalConstructor()
			->getMock();
		$emMock->expects($this->once())->method('flush');

		$mailTemplateRepositoryMock = $this->getMock(MailTemplateRepository::class, [], [], '', false);

		$orderStatusRepositoryMock = $this->getMockBuilder(OrderStatusRepository::class)
			->disableOriginalConstructor()
			->getMock();

		$mailTemplateFacade = new MailTemplateFacade($emMock, $mailTemplateRepositoryMock, $orderStatusRepositoryMock);

		$originMailTemplateData = new MailTemplateData();
		$originMailTemplateData->setSubject('subject');
		$originMailTemplateData->setBody('body');
		$mailTemplate = new MailTemplate('templateName', $originMailTemplateData);

		$expectedMailTemplateData = new MailTemplateData();
		$expectedMailTemplateData->setName('templateName');
		$expectedMailTemplateData->setSubject('editedSubject');
		$expectedMailTemplateData->setBody('editedBody');
		$mailTemplateFacade->edit($mailTemplate, $expectedMailTemplateData);

		$actualMailTemplateData = new MailTemplateData();
		$actualMailTemplateData->setFromEntity($mailTemplate);

		$this->assertEquals($expectedMailTemplateData, $actualMailTemplateData);
	}
}
