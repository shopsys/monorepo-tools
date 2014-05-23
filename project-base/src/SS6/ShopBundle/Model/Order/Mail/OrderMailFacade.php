<?php

namespace SS6\ShopBundle\Model\Order\Mail;

use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Order\Mail\OrderMailService;
use Swift_Mailer;

class OrderMailFacade {

	/**
	 * @var \SS6\ShopBundle\Model\Order\Mail\OrderMailService
	 */
	private $orderMailService;

	/**
	 *
	 * @var \Swift_Mailer
	 */
	private $mailer;

	/**
	 * @param \Swift_Mailer $mailer
	 * @param \SS6\ShopBundle\Model\Order\Mail\OrderMailService $orderMailService
	 */
	public function __construct(Swift_Mailer $mailer, OrderMailService $orderMailService) {
		$this->mailer = $mailer;
		$this->orderMailService = $orderMailService;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @throws \SS6\ShopBundle\Model\Order\Mail\Exception\SendMailFailedException
	 */
	public function sendEmail(Order $order) {
		$message = $this->orderMailService->getMessageByOrder($order);
		$failedRecipients = array();
		$successSend = $this->mailer->send($message, $failedRecipients);
		if (!$successSend && count($failedRecipients) > 0) {
			throw new \SS6\ShopBundle\Model\Order\Mail\Exception\SendMailFailedException($failedRecipients);
		}
	}
}
