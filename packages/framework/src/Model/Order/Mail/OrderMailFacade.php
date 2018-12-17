<?php

namespace Shopsys\FrameworkBundle\Model\Order\Mail;

use Shopsys\FrameworkBundle\Model\Mail\Mailer;
use Shopsys\FrameworkBundle\Model\Mail\MailTemplateFacade;
use Shopsys\FrameworkBundle\Model\Order\Order;
use Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus;

class OrderMailFacade
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Mail\Mailer
     */
    protected $mailer;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Mail\MailTemplateFacade
     */
    protected $mailTemplateFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Mail\OrderMail
     */
    protected $orderMail;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Mail\Mailer $mailer
     * @param \Shopsys\FrameworkBundle\Model\Mail\MailTemplateFacade $mailTemplateFacade
     * @param \Shopsys\FrameworkBundle\Model\Order\Mail\OrderMail $orderMail
     */
    public function __construct(
        Mailer $mailer,
        MailTemplateFacade $mailTemplateFacade,
        OrderMail $orderMail
    ) {
        $this->mailer = $mailer;
        $this->mailTemplateFacade = $mailTemplateFacade;
        $this->orderMail = $orderMail;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     */
    public function sendEmail(Order $order)
    {
        $mailTemplate = $this->getMailTemplateByStatusAndDomainId($order->getStatus(), $order->getDomainId());
        $messageData = $this->orderMail->createMessage($mailTemplate, $order);
        $messageData->attachmentsFilepaths = $this->mailTemplateFacade->getMailTemplateAttachmentsFilepaths($mailTemplate);
        $this->mailer->send($messageData);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus $orderStatus
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Mail\MailTemplate
     */
    public function getMailTemplateByStatusAndDomainId(OrderStatus $orderStatus, $domainId)
    {
        $templateName = OrderMail::getMailTemplateNameByStatus($orderStatus);

        return $this->mailTemplateFacade->get($templateName, $domainId);
    }
}
