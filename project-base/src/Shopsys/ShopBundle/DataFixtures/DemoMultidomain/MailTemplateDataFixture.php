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
