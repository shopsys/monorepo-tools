<?php

namespace Shopsys\ShopBundle\Model\Order\Mail;

use Shopsys\ShopBundle\Model\Mail\MailerService;
use Shopsys\ShopBundle\Model\Mail\MailTemplateFacade;
use Shopsys\ShopBundle\Model\Order\Mail\OrderMailService;
use Shopsys\ShopBundle\Model\Order\Order;
use Shopsys\ShopBundle\Model\Order\Status\OrderStatus;
use Shopsys\ShopBundle\Model\Order\Status\OrderStatusRepository;

class OrderMailFacade {

    /**
     * @var \Shopsys\ShopBundle\Model\Mail\MailerService
     */
    private $mailer;

    /**
     * @var \Shopsys\ShopBundle\Model\Mail\MailTemplateFacade
     */
    private $mailTemplateFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Order\Mail\OrderMailService
     */
    private $orderMailService;

    /**
     * @var \Shopsys\ShopBundle\Model\Order\Status\OrderStatusRepository
     */
    private $orderStatusRepository;

    /**
     * @param \Shopsys\ShopBundle\Model\Mail\MailerService $mailer
     * @param \Shopsys\ShopBundle\Model\Mail\MailTemplateFacade $mailTemplateFacade
     * @param \Shopsys\ShopBundle\Model\Order\Mail\OrderMailService $orderMailService
     * @param \Shopsys\ShopBundle\Model\Order\Status\OrderStatusRepository $orderStatusRepository
     */
    public function __construct(
        MailerService $mailer,
        MailTemplateFacade $mailTemplateFacade,
        OrderMailService $orderMailService,
        OrderStatusRepository $orderStatusRepository
    ) {
        $this->mailer = $mailer;
        $this->mailTemplateFacade = $mailTemplateFacade;
        $this->orderMailService = $orderMailService;
        $this->orderStatusRepository = $orderStatusRepository;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Order\Order $order
     */
    public function sendEmail(Order $order) {
        $mailTemplate = $this->getMailTemplateByStatusAndDomainId($order->getStatus(), $order->getDomainId());
        $messageData = $this->orderMailService->getMessageDataByOrder($order, $mailTemplate);
        $messageData->attachmentsFilepaths = $this->mailTemplateFacade->getMailTemplateAttachmentsFilepaths($mailTemplate);
        $this->mailer->send($messageData);
    }
    /**
     * @param \Shopsys\ShopBundle\Model\Order\Status\OrderStatus $orderStatus
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Model\Mail\MailTemplate
     */
    public function getMailTemplateByStatusAndDomainId(OrderStatus $orderStatus, $domainId) {
        $templateName = $this->orderMailService->getMailTemplateNameByStatus($orderStatus);

        return $this->mailTemplateFacade->get($templateName, $domainId);
    }

}
