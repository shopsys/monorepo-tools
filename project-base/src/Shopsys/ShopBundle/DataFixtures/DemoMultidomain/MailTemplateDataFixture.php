<?php

namespace Shopsys\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\ShopBundle\Model\Mail\MailTemplateData;
use Shopsys\ShopBundle\Model\Mail\MailTemplateFacade;

class MailTemplateDataFixture extends AbstractReferenceFixture
{
    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $mailTemplateData = new MailTemplateData();
        $mailTemplateData->name = 'order_status_1';
        $mailTemplateData->sendMail = true;
        $mailTemplateData->subject = 'Děkujeme za objednávku č. {number} ze dne {date}';
        $mailTemplateData->body = 'Dobrý den,<br /><br />'
            . 'Vaše objednávka byla úspěšně vytvořena.<br /><br />'
            . 'O dalších stavech objednávky Vás budeme informovat.<br />'
            . 'Čislo objednávky: {number} <br />'
            . 'Datum a čas vytvoření: {date} <br />'
            . 'URL adresa eshopu: {url} <br />'
            . 'URL adresa na detail objednávky: {order_detail_url} <br />'
            . 'Doprava: {transport} <br />'
            . 'Platba: {payment} <br />'
            . 'Celková cena s DPH: {total_price} <br />'
            . 'Fakturační adresa:<br /> {billing_address} <br />'
            . 'Doručovací adresa: {delivery_address} <br />'
            . 'Poznámka: {note} <br />'
            . 'Produkty: {products} <br />'
            . '{transport_instructions} <br />'
            . '{payment_instructions}';

        $this->updateMailTemplate($mailTemplateData);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Mail\MailTemplateData $mailTemplateData
     */
    private function updateMailTemplate(MailTemplateData $mailTemplateData)
    {
        $mailTemplateFacade = $this->get(MailTemplateFacade::class);
        /* @var $mailTemplateFacade \Shopsys\ShopBundle\Model\Mail\MailTemplateFacade */

        $domainId = 2;

        $mailTemplateFacade->saveMailTemplatesData([$mailTemplateData], $domainId);
    }
}
