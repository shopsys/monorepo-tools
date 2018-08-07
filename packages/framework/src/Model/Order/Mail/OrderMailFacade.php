<?php

namespace Shopsys\FrameworkBundle\Model\Order\Mail;

use Shopsys\FrameworkBundle\Model\Mail\MailerService;
use Shopsys\FrameworkBundle\Model\Mail\MailTemplateFacade;
use Shopsys\FrameworkBundle\Model\Order\Order;
use Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus;

class OrderMailFacade
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Mail\MailerService
     */
    protected $mailer;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Mail\MailTemplateFacade
     */
    protected $mailTemplateFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Mail\OrderMailService
     */
    protected $orderMailService;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Mail\MailerService $mailer
     * @param \Shopsys\FrameworkBundle\Model\Mail\MailTemplateFacade $mailTemplateFacade
     * @param \Shopsys\FrameworkBundle\Model\Order\Mail\OrderMailService $orderMailService
     */
    public function __construct(
        MailerService $mailer,
        MailTemplateFacade $mailTemplateFacade,
        OrderMailService $orderMailService
    ) {
        $this->mailer = $mailer;
        $this->mailTemplateFacade = $mailTemplateFacade;
        $this->orderMailService = $orderMailService;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     */
    public function sendEmail(Order $order)
    {
        $mailTemplate = $this->getMailTemplateByStatusAndDomainId($order->getStatus(), $order->getDomainId());
        $messageData = $this->orderMailService->getMessageDataByOrder($order, $mailTemplate);
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
        $templateName = $this->orderMailService->getMailTemplateNameByStatus($orderStatus);

        return $this->mailTemplateFacade->get($templateName, $domainId);
    }
}
