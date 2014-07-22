<?php

namespace SS6\ShopBundle\Model\Order\Mail;

use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;
use Swift_Message;
use Symfony\Bundle\TwigBundle\TwigEngine;

class OrderMailService {

	const MAIL_TEMPLATE_NEW = '@SS6Shop/Common/Mail/Order/new.html.twig';
	const MAIL_TEMPLATE_OTHER = '@SS6Shop/Common/Mail/Order/other.html.twig';

	/**
	 * @var string
	 */
	private $senderEmail;

	/**
	 * @var \Symfony\Bundle\TwigBundle\TwigEngine
	 */
	private $templating;
	
	/**
	 * @param string $senderEmail
	 * @param \Symfony\Bundle\TwigBundle\TwigEngine $templating
	 */
	public function __construct($senderEmail, TwigEngine $templating) {
		$this->senderEmail = $senderEmail;
		$this->templating = $templating;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @return \Swift_Message
	 */
	public function getMessageByOrder(Order $order) {
		$subject = 'Objednávka ' . $order->getNumber() . ': ' . $order->getStatus()->getName();
		$toEmail = $order->getEmail();
		$template = $this->getTemplateByStatus($order->getStatus());
		$parameters = array(
			'order' => $order
		);
		$body = $this->templating->render($template, $parameters);

		$message = Swift_Message::newInstance()
			->setSubject($subject)
			->setFrom($this->senderEmail)
			->setTo($toEmail)
			->setContentType('text/plain; charset=UTF-8')
			->setBody(strip_tags($body), 'text/plain')
			->addPart($body, 'text/html');
		
		return $message;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatus $status
	 * @return string
	 */
	private function getTemplateByStatus(OrderStatus $status) {
		if ($status->getType() == OrderStatus::TYPE_NEW) {
			return self::MAIL_TEMPLATE_NEW;
		} else {
			return self::MAIL_TEMPLATE_OTHER;
		}
	}
}
