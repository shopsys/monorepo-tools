<?php

namespace SS6\ShopBundle\Model\Order\Mail;

use SS6\ShopBundle\Model\Mail\MailTemplate;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;
use Swift_Message;

class OrderMailService {

	const MAIL_TEMPLATE_NAME_PREFIX = 'order_status_';
	const VARIABLE_NUMBER = '{number}';
	const VARIABLE_DATE = '{date}';
	const VARIABLE_URL = '{url}';
	const VARIABLE_TRANSPORT = '{transport}';
	const VARIABLE_PAYMENT = '{payment}';
	const VARIABLE_TOTAL_PRICE = '{total_price}';
	const VARIABLE_BILLING_ADDRESS = '{billing_address}';
	const VARIABLE_DELIVERY_ADDRESS = '{delivery_address}';
	const VARIABLE_NOTE = '{note}';
	const VARIABLE_PRODUCTS = '{products}';

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
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatus $orderStatus
	 * @return string
	 */
	public function getMailTemplateNameByStatus(OrderStatus $orderStatus) {
		return self::MAIL_TEMPLATE_NAME_PREFIX . $orderStatus->getId();
	}

	/**
	 * @return array
	 */
	public function getOrderStatusesTemplateVariables() {
		return array(
			self::VARIABLE_NUMBER  => 'Číslo objednávky',
			self::VARIABLE_DATE => 'Datum a čas vytvoření objednávky',
			self::VARIABLE_URL => 'URL adresa e-shopu',
			self::VARIABLE_TRANSPORT => 'Název zvolené dopravy',
			self::VARIABLE_PAYMENT => 'Název zvolené platby',
			self::VARIABLE_TOTAL_PRICE => 'Celková cena za objednávku (s DPH)',
			self::VARIABLE_BILLING_ADDRESS => 'Fakturační adresa - jméno, příjmení, firma, ič, dič a fakt. adresa',
			self::VARIABLE_DELIVERY_ADDRESS => 'Dodací adresa',
			self::VARIABLE_NOTE  => 'Poznámka',
			self::VARIABLE_PRODUCTS => 'Seznam zboží v objednávce (název, počet kusů, cena za kus s DPH, celková cena za položku s DPH)',
		);
	}
}
