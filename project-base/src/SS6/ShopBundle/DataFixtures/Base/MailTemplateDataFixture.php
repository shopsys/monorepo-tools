<?php

namespace SS6\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Model\Mail\MailTemplate;
use SS6\ShopBundle\Model\Mail\MailTemplateData;

class MailTemplateDataFixture extends AbstractReferenceFixture {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function load(ObjectManager $manager) {
		$mailTemplateData = new MailTemplateData();
		$mailTemplateData->setSubject('Děkujeme za objednávku č. {number} ze dne {date}');
		$mailTemplateData->setBody('Dobrý den,<br /><br />'
			. 'Vaše objednávka byla úspěšně vytvořena.<br /><br />'
			. 'O dalších stavech objednávky Vás budeme informovat.<br />'
			. 'Čislo objednávky: {number} <br />'
			. 'Datum a čas vytvoření: {date} <br />'
			. 'URL adresa eshopu: {url} <br />'
			. 'Doprava: {transport} <br />'
			. 'Platba: {payment} <br />'
			. 'Celková cena s DPH: {total_price} <br />'
			. 'Fakturační adresa:<br /> {billing_address} <br />'
			. 'Doručovací adresa: {delivery_address} <br />'
			. 'Poznámka: {note} <br />'
			. 'Produkty: {products} <br />');
		$mailTemplateData->setSendMail(true);

		$mailTemplate = new MailTemplate('order_status_1', 1, $mailTemplateData);
		$manager->persist($mailTemplate);

		$mailTemplateData->setSubject('Změna stavu vaší objednávky');
		$mailTemplateData->setBody('Dobrý den, <br /><br />'
			. 'Vaši objednávku již vyřizujeme.');

		$mailTemplate = new MailTemplate('order_status_2', 1, $mailTemplateData);
		$manager->persist($mailTemplate);

		$mailTemplateData->setSubject('Změna stavu vaší objednávky');
		$mailTemplateData->setBody('Dobrý den, <br /><br />'
			. 'Vaše objednávka je vyřízena.');

		$mailTemplate = new MailTemplate('order_status_3', 1, $mailTemplateData);
		$manager->persist($mailTemplate);

		$mailTemplateData->setSubject('Změna stavu vaší objednávky');
		$mailTemplateData->setBody('Dobrý den, <br /><br />'
			. 'Vaše objednávka byla stornována.');

		$mailTemplate = new MailTemplate('order_status_4', 1, $mailTemplateData);
		$manager->persist($mailTemplate);

		$mailTemplateData->setSubject('Potvrzení registrace');
		$mailTemplateData->setBody('Dobrý den, <br /><br />'
			. 'potvrzujeme Vaši registraci v eshopu. <br />'
			. 'Jméno: {first_name} {last_name}<br />'
			. 'Email: {email}<br />'
			. 'URL adresa eshopu: {url}<br />'
			. 'Přihlašovací stránka: {login_page}');

		$mailTemplate = new MailTemplate(MailTemplate::REGISTRATION_CONFIRM_NAME, 1, $mailTemplateData);
		$manager->persist($mailTemplate);

		$mailTemplateData->setSubject('Děkujeme za objednávku na druhé doméně');
		$mailTemplateData->setBody('Dobrý den,<br /><br />'
			. 'Vaše objednávka byla úspěšně vytvořena.<br /><br />'
			. 'O dalších stavech objednávky Vás budeme informovat.');

		$mailTemplate = new MailTemplate('order_status_1', 2, $mailTemplateData);
		$manager->persist($mailTemplate);

		$mailTemplateData->setSubject('Změna stavu vaší objednávky na druhé doméně');
		$mailTemplateData->setBody('Dobrý den, <br /><br />'
			. 'Vaši objednávku již vyřizujeme.');

		$mailTemplate = new MailTemplate('order_status_2', 2, $mailTemplateData);
		$manager->persist($mailTemplate);

		$mailTemplateData->setSubject('Změna stavu vaší objednávky na druhé doméně');
		$mailTemplateData->setBody('Dobrý den, <br /><br />'
			. 'Vaše objednávka je vyřízena.');

		$mailTemplate = new MailTemplate('order_status_3', 2, $mailTemplateData);
		$manager->persist($mailTemplate);

		$mailTemplateData->setSubject('Změna stavu vaší objednávky na druhé doméně');
		$mailTemplateData->setBody('Dobrý den, <br /><br />'
			. 'Vaše objednávka byla stornována.');

		$mailTemplate = new MailTemplate('order_status_4', 2, $mailTemplateData);
		$manager->persist($mailTemplate);

		$mailTemplateData->setSubject('Potvrzení registrace na druhé doméně');
		$mailTemplateData->setBody('Dobrý den, <br /><br />'
			. 'potvrzujeme Vaši registraci v eshopu.<br />'
			. 'Jméno: {first_name} {last_name}<br />'
			. 'Email: {email}<br />'
			. 'URL adresa eshopu: {url}<br />'
			. 'Přihlašovací stránka: {login_page}');

		$mailTemplate = new MailTemplate(MailTemplate::REGISTRATION_CONFIRM_NAME, 2, $mailTemplateData);
		$manager->persist($mailTemplate);

		$manager->flush();
	}

}
