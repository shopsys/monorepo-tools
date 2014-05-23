<?php

namespace SS6\ShopBundle\Model\Order\Mail;

use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;
use SS6\ShopBundle\Model\Order\Status\OrderStatusRepository;
use Swift_Message;
use Symfony\Bundle\TwigBundle\Debug\TimedTwigEngine;

class OrderMailService {

	const MAIL_TEMPLATE_NEW = '@SS6Shop/Common/Mail/Order/new.html.twig';
	const MAIL_TEMPLATE_OTHER = '@SS6Shop/Common/Mail/Order/other.html.twig';

	/**
	 * @var string
	 */
	private $senderEmail;

	/**
	 * @var \Symfony\Bundle\TwigBundle\Debug\TimedTwigEngine
	 */
	private $templating;
	
	/**
	 * @param string $senderEmail
	 * @param \Symfony\Bundle\TwigBundle\Debug\TimedTwigEngine $templating
	 */
	public function __construct($senderEmail, TimedTwigEngine $templating) {
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
			->setBody($body);
		
		return $message;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatus $status
	 * @return string
	 */
	private function getTemplateByStatus(OrderStatus $status) {
		if ($status->getId() == OrderStatusRepository::STATUS_NEW) {
			return self::MAIL_TEMPLATE_NEW;
		} else {
			return self::MAIL_TEMPLATE_OTHER;
		}
	}
}
