<?php

namespace Shopsys\FrameworkBundle\DataFixtures\DemoMultidomain;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Model\Mail\MailTemplate;
use Shopsys\FrameworkBundle\Model\Mail\MailTemplateData;
use Shopsys\FrameworkBundle\Model\Mail\MailTemplateDataFactoryInterface;
use Shopsys\FrameworkBundle\Model\Mail\MailTemplateFacade;

class MailTemplateDataFixture extends AbstractReferenceFixture
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Mail\MailTemplateFacade
     */
    private $mailTemplateFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Mail\MailTemplateDataFactoryInterface
     */
    private $mailTemplateDataFactory;

    public function __construct(
        MailTemplateFacade $mailTemplateFacade,
        MailTemplateDataFactoryInterface $mailTemplateDataFactory
    ) {
        $this->mailTemplateFacade = $mailTemplateFacade;
        $this->mailTemplateDataFactory = $mailTemplateDataFactory;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $mailTemplateData = $this->mailTemplateDataFactory->create();
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

        $mailTemplateData = $this->mailTemplateDataFactory->create();
        $mailTemplateData->name = 'order_status_2';
        $mailTemplateData->sendMail = false;
        $mailTemplateData->subject = 'Stav objednávky se změnil';
        $mailTemplateData->body = 'Vážený zákazníku, <br /><br />'
            . 'Vaše objednávka se zpracovává.';

        $this->updateMailTemplate($mailTemplateData);

        $mailTemplateData = $this->mailTemplateDataFactory->create();
        $mailTemplateData->name = 'order_status_3';
        $mailTemplateData->sendMail = false;
        $mailTemplateData->subject = 'Stav objednávky se změnil';
        $mailTemplateData->body = 'Vážený zákazníku, <br /><br />'
            . 'zpracování objednávky bylo dokončeno.';

        $this->updateMailTemplate($mailTemplateData);

        $mailTemplateData = $this->mailTemplateDataFactory->create();
        $mailTemplateData->name = 'order_status_4';
        $mailTemplateData->sendMail = false;
        $mailTemplateData->subject = 'Stav objednávky se změnil';
        $mailTemplateData->body = 'Vážený zákazníku, <br /><br />'
            . 'Vaše objednávka byla zrušena.';

        $this->updateMailTemplate($mailTemplateData);

        $mailTemplateData = $this->mailTemplateDataFactory->create();
        $mailTemplateData->name = 'reset_password';
        $mailTemplateData->sendMail = true;
        $mailTemplateData->subject = 'Žádost o heslo';
        $mailTemplateData->body = 'Vážený zákazníku,<br /><br />'
            . 'na tomto odkazu můžete nastavit nové heslo: <a href="{new_password_url}">{new_password_url}</a>';

        $this->updateMailTemplate($mailTemplateData);

        $mailTemplateData = $this->mailTemplateDataFactory->create();
        $mailTemplateData->name = 'registration_confirm';
        $mailTemplateData->sendMail = true;
        $mailTemplateData->subject = 'Registrace byla dokončena';
        $mailTemplateData->body = 'Vážený zákazníku, <br /><br />'
            . 'Vaše registrace je dokončena. <br />'
            . 'Jméno: {first_name} {last_name}<br />'
            . 'E-mail: {email}<br />'
            . 'Adresa e-shopu: {url}<br />'
            . 'Přihlašovací stránka: {login_page}';

        $this->updateMailTemplate($mailTemplateData);

        $mailTemplateData = $this->mailTemplateDataFactory->create();
        $mailTemplateData->name = MailTemplate::PERSONAL_DATA_ACCESS_NAME;
        $mailTemplateData->sendMail = true;
        $mailTemplateData->subject = 'Přehled osobních údajů - {domain}';
        $mailTemplateData->body = 'Vážený zákazníku,<br /><br />
            na základě vašeho zadaného emailu {e-mail}, Vám zasíláme odkaz na zobrazení osobních údajů. Klikem na odkaz níže se dostanete na stránku s <br/>  
            přehledem všech osobních údajů, které k Vašemu e-mailu evidujeme na našem e-shopu {domain}.<br/><br/>
            Pro zobrazení osobních údajů klikněte zde - {url}<br/>
            Odkaz je platný 24 hodin.<br/><br/>
            S pozdravem<br/>
            tým {domain}';

        $this->updateMailTemplate($mailTemplateData);

        $mailTemplateData = $this->mailTemplateDataFactory->create();
        $mailTemplateData->name = MailTemplate::PERSONAL_DATA_EXPORT_NAME;
        $mailTemplateData->sendMail = true;
        $mailTemplateData->subject = ' Export osobních údajů - {domain}';
        $mailTemplateData->body = 'Vážený zákazníku,<br/><br/>
            na základě vašeho zadaného emailu {e-mail}, Vám zasíláme odkaz ke stažení Vašich<br/>
            údajů evidovaných na našem internetovém obchodě ve strojově čitelném formátu.<br/>
            Klikem na odkaz se dostanete na stránku s s možností stažení těchto informací, které k<br/>
            Vašemu e-mailu evidujeme na našem internetovém obchodu {domain}.<br/><br/>
            Pro přechod na stažení údajů, prosím, klikněte zde - {url}<br/>
            Odkaz je platný 24 hodin.<br/><br/>
            S pozdravem<br/>
            tým {domain}';

        $this->updateMailTemplate($mailTemplateData);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Mail\MailTemplateData $mailTemplateData
     */
    private function updateMailTemplate(MailTemplateData $mailTemplateData)
    {
        $domainId = 2;

        $this->mailTemplateFacade->saveMailTemplatesData([$mailTemplateData], $domainId);
    }
}
