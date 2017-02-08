<?php

namespace SS6\ShopBundle\Model\Order\Mail;

use SS6\ShopBundle\Model\Mail\MailerService;
use SS6\ShopBundle\Model\Mail\MailTemplateFacade;
use SS6\ShopBundle\Model\Order\Mail\OrderMailService;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;
use SS6\ShopBundle\Model\Order\Status\OrderStatusRepository;

class OrderMailFacade {

	/**
	 * @var \SS6\ShopBundle\Model\Mail\MailerService
	 */
	private $mailer;

	/**
	 * @var \SS6\ShopBundle\Model\Mail\MailTemplateFacade
	 */
	private $mailTemplateFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Order\Mail\OrderMailService
	 */
	private $orderMailService;

	/**
	 * @var \SS6\ShopBundle\Model\Order\Status\OrderStatusRepository
	 */
	private $orderStatusRepository;

	/**
	 * @param \SS6\ShopBundle\Model\Mail\MailerService $mailer
	 * @param \SS6\ShopBundle\Model\Mail\MailTemplateFacade $mailTemplateFacade
	 * @param \SS6\ShopBundle\Model\Order\Mail\OrderMailService $orderMailService
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatusRepository $orderStatusRepository
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
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 */
	public function sendEmail(Order $order) {
		$mailTemplate = $this->getMailTemplateByStatusAndDomainId($order->getStatus(), $order->getDomainId());
		$messageData = $this->orderMailService->getMessageDataByOrder($order, $mailTemplate);
		$messageData->attachmentsFilepaths = $this->mailTemplateFacade->getMailTemplateAttachmentsFilepaths($mailTemplate);
		$this->mailer->send($messageData);
	}
	/**
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatus $orderStatus
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Mail\MailTemplate
	 */
	public function getMailTemplateByStatusAndDomainId(OrderStatus $orderStatus, $domainId) {
		$templateName = $this->orderMailService->getMailTemplateNameByStatus($orderStatus);

		return $this->mailTemplateFacade->get($templateName, $domainId);
	}

}
