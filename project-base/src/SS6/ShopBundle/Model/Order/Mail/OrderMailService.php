<?php

namespace SS6\ShopBundle\Model\Order\Mail;

use SS6\ShopBundle\Model\Mail\MailTemplate;
use SS6\ShopBundle\Model\Order\Order;
use Swift_Message;

class OrderMailService {

	const MAIL_TEMPLATE_NAME_PREFIX = 'order_status_';

	/**
	 * @var string
	 */
	private $senderEmail;
	
	/**
	 * @param string $senderEmail
	 */
	public function __construct($senderEmail) {
		$this->senderEmail = $senderEmail;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @param \SS6\ShopBundle\Model\Mail\MailTemplate $mailTemplate
	 * @return \Swift_Message
	 */
	public function getMessageByOrder(Order $order, MailTemplate $mailTemplate) {
		$toEmail = $order->getEmail();
		$body = $mailTemplate->getBody();

		$message = Swift_Message::newInstance()
			->setSubject($mailTemplate->getSubject())
			->setFrom($this->senderEmail)
			->setTo($toEmail)
			->setContentType('text/plain; charset=UTF-8')
			->setBody(strip_tags($body), 'text/plain')
			->addPart($body, 'text/html');
		
		return $message;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @return string
	 */
	public function getMailTemplateNameOrder(Order $order) {
		$orderStatus = $order->getStatus();
		
		return self::MAIL_TEMPLATE_NAME_PREFIX . $orderStatus->getId();
	}
}
