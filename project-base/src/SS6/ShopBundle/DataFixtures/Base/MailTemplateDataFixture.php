<?php

namespace SS6\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Model\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Model\Mail\MailTemplate;
use SS6\ShopBundle\Model\Mail\MailTemplateData;

class MailTemplateDataFixture extends AbstractReferenceFixture {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$mailTemplateData = new MailTemplateData();
		$mailTemplateData->setSubject('Děkujeme za objednávku');
		$mailTemplateData->setBody('Dobrý den,<br /><br />'
			. 'Vaše objednávka byla úspěšně vytvořena.<br /><br />'
			. 'O dalších stavech objednávky Vás budeme informovat.');

		$mailTemplate = new MailTemplate('order_status_1', 1, $mailTemplateData);
		$manager->persist($mailTemplate);

		$mailTemplateData = new MailTemplateData();
		$mailTemplateData->setSubject('Změna stavu vaší objednávky');
		$mailTemplateData->setBody('Dobrý den, <br /><br />'
			. 'Vaši objednávku již vyřizujeme.');

		$mailTemplate = new MailTemplate('order_status_2', 1, $mailTemplateData);
		$manager->persist($mailTemplate);

		$mailTemplateData = new MailTemplateData();
		$mailTemplateData->setSubject('Změna stavu vaší objednávky');
		$mailTemplateData->setBody('Dobrý den, <br /><br />'
			. 'Vaše objednávka je vyřízena.');

		$mailTemplate = new MailTemplate('order_status_3', 1, $mailTemplateData);
		$manager->persist($mailTemplate);

		$mailTemplateData = new MailTemplateData();
		$mailTemplateData->setSubject('Změna stavu vaší objednávky');
		$mailTemplateData->setBody('Dobrý den, <br /><br />'
			. 'Vaše objednávka byla stornována.');

		$mailTemplate = new MailTemplate('order_status_4', 1, $mailTemplateData);
		$manager->persist($mailTemplate);

		$mailTemplateData = new MailTemplateData();
		$mailTemplateData->setSubject('Potvrzení registrace');
		$mailTemplateData->setBody('Dobrý den, <br /><br />'
			. 'potvrzujeme Vaši registraci v eshopu.');

		$mailTemplate = new MailTemplate('registration_confirm', 1, $mailTemplateData);
		$manager->persist($mailTemplate);

		$mailTemplateData = new MailTemplateData();
		$mailTemplateData->setSubject('Děkujeme za objednávku na druhé doméně');
		$mailTemplateData->setBody('Dobrý den,<br /><br />'
			. 'Vaše objednávka byla úspěšně vytvořena.<br /><br />'
			. 'O dalších stavech objednávky Vás budeme informovat.');

		$mailTemplate = new MailTemplate('order_status_1', 2, $mailTemplateData);
		$manager->persist($mailTemplate);

		$mailTemplateData = new MailTemplateData();
		$mailTemplateData->setSubject('Změna stavu vaší objednávky na druhé doméně');
		$mailTemplateData->setBody('Dobrý den, <br /><br />'
			. 'Vaši objednávku již vyřizujeme.');

		$mailTemplate = new MailTemplate('order_status_2', 2, $mailTemplateData);
		$manager->persist($mailTemplate);

		$mailTemplateData = new MailTemplateData();
		$mailTemplateData->setSubject('Změna stavu vaší objednávky na druhé doméně');
		$mailTemplateData->setBody('Dobrý den, <br /><br />'
			. 'Vaše objednávka je vyřízena.');

		$mailTemplate = new MailTemplate('order_status_3', 2, $mailTemplateData);
		$manager->persist($mailTemplate);

		$mailTemplateData = new MailTemplateData();
		$mailTemplateData->setSubject('Změna stavu vaší objednávky na druhé doméně');
		$mailTemplateData->setBody('Dobrý den, <br /><br />'
			. 'Vaše objednávka byla stornována.');

		$mailTemplate = new MailTemplate('order_status_4', 2, $mailTemplateData);
		$manager->persist($mailTemplate);

		$mailTemplateData = new MailTemplateData();
		$mailTemplateData->setSubject('Potvrzení registrace na druhé doméně');
		$mailTemplateData->setBody('Dobrý den, <br /><br />'
			. 'potvrzujeme Vaši registraci v eshopu.');

		$mailTemplate = new MailTemplate('registration_confirm', 2, $mailTemplateData);
		$manager->persist($mailTemplate);

		$manager->flush();
	}

}
