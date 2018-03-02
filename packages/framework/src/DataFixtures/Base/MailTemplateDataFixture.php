<?php

namespace Shopsys\FrameworkBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Mail\MailTemplate;
use Shopsys\FrameworkBundle\Model\Mail\MailTemplateData;

class MailTemplateDataFixture extends AbstractReferenceFixture
{
    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function load(ObjectManager $manager)
    {
        $mailTemplateData = new MailTemplateData();
        $mailTemplateData->sendMail = true;

        $mailTemplateData->subject = 'Thank you for your order no. {number} placed at {date}';
        $mailTemplateData->body = 'Dear customer,<br /><br />'
            . 'Your order has been placed successfully.<br /><br />'
            . 'You will be contacted when the order state changes.<br />'
            . 'Order number: {number} <br />'
            . 'Date and time of creation: {date} <br />'
            . 'E-shop link: {url} <br />'
            . 'Order detail link: {order_detail_url} <br />'
            . 'Shipping: {transport} <br />'
            . 'Payment: {payment} <br />'
            . 'Total price including VAT: {total_price} <br />'
            . 'Billing address:<br /> {billing_address} <br />'
            . 'Delivery address: {delivery_address} <br />'
            . 'Note: {note} <br />'
            . 'Products: {products} <br />'
            . '{transport_instructions} <br />'
            . '{payment_instructions}';

        $this->createMailTemplate($manager, 'order_status_1', $mailTemplateData);

        $mailTemplateData->sendMail = false;
        $mailTemplateData->subject = 'Order status has changed';
        $mailTemplateData->body = 'Dear customer, <br /><br />'
            . 'Your order is being processed.';

        $this->createMailTemplate($manager, 'order_status_2', $mailTemplateData);

        $mailTemplateData->subject = 'Order status has changed';
        $mailTemplateData->body = 'Dear customer, <br /><br />'
            . 'Processing your order has been finished.';

        $this->createMailTemplate($manager, 'order_status_3', $mailTemplateData);

        $mailTemplateData->subject = 'Order status has changed';
        $mailTemplateData->body = 'Dear customer, <br /><br />'
            . 'Your order has been cancelled.';

        $this->createMailTemplate($manager, 'order_status_4', $mailTemplateData);

        $mailTemplateData->sendMail = true;
        $mailTemplateData->subject = 'Reset password request';
        $mailTemplateData->body = 'Dear customer.<br /><br />'
            . 'You can set a new password following this link: <a href="{new_password_url}">{new_password_url}</a>';

        $this->createMailTemplate($manager, MailTemplate::RESET_PASSWORD_NAME, $mailTemplateData);

        $mailTemplateData->subject = 'Registration completed';
        $mailTemplateData->body = 'Dear customer, <br /><br />'
            . 'your registration is completed. <br />'
            . 'Name: {first_name} {last_name}<br />'
            . 'Email: {email}<br />'
            . 'E-shop link: {url}<br />'
            . 'Log in page: {login_page}';

        $this->createMailTemplate($manager, MailTemplate::REGISTRATION_CONFIRM_NAME, $mailTemplateData);
    }

    private function createMailTemplate(
        ObjectManager $manager,
        $name,
        MailTemplateData $mailTemplateData
    ) {
        $mailTemplate = new MailTemplate($name, Domain::FIRST_DOMAIN_ID, $mailTemplateData);
        $manager->persist($mailTemplate);
        $manager->flush($mailTemplate);
    }
}
