<?php

namespace SS6\ShopBundle\Model\Order\Mail;

use SS6\ShopBundle\Model\Mail\MailTemplateFacade;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Order\Mail\OrderMailService;
use Swift_Mailer;

class OrderMailFacade {

	/**
	 * @var \Swift_Mailer
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
	 * @param \Swift_Mailer $mailer
	 * @param \SS6\ShopBundle\Model\Mail\MailTemplateFacade $mailTemplateFacade
	 * @param \SS6\ShopBundle\Model\Order\Mail\OrderMailService $orderMailService
	 */
	public function __construct(
		Swift_Mailer $mailer,
		MailTemplateFacade $mailTemplateFacade,
		OrderMailService $orderMailService
	) {
		$this->mailer = $mailer;
		$this->mailTemplateFacade = $mailTemplateFacade;
		$this->orderMailService = $orderMailService;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @throws \SS6\ShopBundle\Model\Order\Mail\Exception\SendMailFailedException
	 */
	public function sendEmail(Order $order) {
		$templateName = $this->orderMailService->getMailTemplateNameOrder($order);
		$mailTemplate = $this->mailTemplateFacade->get($templateName);
		$message = $this->orderMailService->getMessageByOrder($order, $mailTemplate);

		$failedRecipients = array();
		$successSend = $this->mailer->send($message, $failedRecipients);
		if (!$successSend && count($failedRecipients) > 0) {
			throw new \SS6\ShopBundle\Model\Order\Mail\Exception\SendMailFailedException($failedRecipients);
		}
	}
}
