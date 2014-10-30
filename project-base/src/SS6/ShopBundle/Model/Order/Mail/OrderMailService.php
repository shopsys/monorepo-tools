<?php

namespace SS6\ShopBundle\Model\Order\Mail;

use SS6\ShopBundle\Model\Mail\MailTemplate;
use SS6\ShopBundle\Model\Order\Item\OrderProduct;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;
use Swift_Message;
use Symfony\Component\Routing\Router;

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
	 * @var \Symfony\Component\Routing\Router
	 */
	private $router;

	/**
	 * @param string $senderEmail
	 * @param Symfony\Component\Routing\Router $router
	 */
	public function __construct($senderEmail, Router $router) {
		$this->senderEmail = $senderEmail;
		$this->router = $router;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @param \SS6\ShopBundle\Model\Mail\MailTemplate $mailTemplate
	 * @return \Swift_Message
	 */
	public function getMessageByOrder(Order $order, MailTemplate $mailTemplate) {
		$toEmail = $order->getEmail();
		$body = $this->transformVariables($mailTemplate->getBody(), $order);
		$subject = $this->transformVariables($mailTemplate->getSubject(), $order, true);

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
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatus $orderStatus
	 * @return string
	 */
	public function getMailTemplateNameByStatus(OrderStatus $orderStatus) {
		return self::MAIL_TEMPLATE_NAME_PREFIX . $orderStatus->getId();
	}

	/**
	 * @param string $string
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @return string
	 */
	public function transformVariables($string, Order $order, $isSubject = false) {
		$variableValues = array(
			self::VARIABLE_NUMBER  => $order->getNumber(),
			self::VARIABLE_DATE => $order->getCreatedAt()->format('d-m-Y H:i'),
			self::VARIABLE_URL => $this->router->generate('front_homepage', array(), true),
			self::VARIABLE_TRANSPORT => $order->getTransport()->getName(),
			self::VARIABLE_PAYMENT => $order->getPayment()->getName(),
			self::VARIABLE_TOTAL_PRICE => $order->getTotalPriceWithVat(),
			self::VARIABLE_BILLING_ADDRESS => $this->formatBillingAddress($order),
			self::VARIABLE_DELIVERY_ADDRESS => $this->formatDeliveryAddress($order),
			self::VARIABLE_NOTE  => $order->getNote(),
			self::VARIABLE_PRODUCTS => $this->formatProducts($order),
		);

		if ($isSubject) {
			$variableKeys = array(
				self::VARIABLE_NUMBER,
				self::VARIABLE_DATE,
			);
		} else {
			$variableKeys = array_keys($this->getOrderStatusesTemplateVariables());
		}

		foreach ($variableKeys as $key) {
			$string = str_replace($key, $variableValues[$key], $string);
		}

		return $string;
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

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @return string
	 */
	private function formatBillingAddress(Order $order) {
		$firstName = $order->getFirstName();
		$lastName = $order->getLastName();
		$companyName = $order->getCompanyName();
		$companyNumber = $order->getCompanyNumber();
		$companyTaxNumber = $order->getCompanyTaxNumber();
		$street = $order->getStreet();
		$city = $order->getCity();
		$postcode = $order->getPostcode();

		return '<table>'
		. '<tr>'
			. '<th>Jméno</th>'
			. '<th>Příjmení</th>'
			. '<th>Firma</th>'
			. '<th>IČ</th>'
			. '<th>DIČ</th>'
			. '<th>Ulice</th>'
			. '<th>Město</th>'
			. '<th>PSČ</th>'
		. '</tr>'
		. '<tr>'
			. '<td>' . $firstName . '</td>'
			. '<td>' . $lastName . '</td>'
			. '<td>' . $companyName . '</td>'
			. '<td>' . $companyNumber . '</td>'
			. '<td>' . $companyTaxNumber . '</td>'
			. '<td>' . $street . '</td>'
			. '<td>' . $city . '</td>'
			. '<td>' . $postcode . '</td>'
		. '</tr>'
		. '</table>';

	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @return string
	 */
	private function formatDeliveryAddress(Order $order) {
		$deliveryContactPerson = $order->getDeliveryContactPerson();
		$deliveryCompanyName = $order->getDeliveryCompanyName();
		$deliveryTelephone = $order->getDeliveryTelephone();
		$deliveryStreet = $order->getDeliveryStreet();
		$deliveryCity = $order->getDeliveryCity();
		$deliveryPostcode = $order->getDeliveryPostcode();

		return '<table>'
		. '<tr>'
			. '<th>Kontaktní osoba</th>'
			. '<th>Firma</th>'
			. '<th>Telefon</th>'
			. '<th>Ulice</th>'
			. '<th>Město</th>'
			. '<th>PSČ</th>'
		. '</tr>'
		. '<tr>'
			. '<td>' . $deliveryContactPerson . '</td>'
			. '<td>' . $deliveryCompanyName . '</td>'
			. '<td>' . $deliveryTelephone . '</td>'
			. '<td>' . $deliveryStreet . '</td>'
			. '<td>' . $deliveryCity . '</td>'
			. '<td>' . $deliveryPostcode . '</td>'
		. '</tr>'
		. '</table>';
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @return string
	 */
	private function formatProducts(Order $order) {
		$orderItems = $order->getItems();
		/* @var $orderItems SS6\ShopBundle\Model\Order\Item\OrderItem[] */
		$products = array();
		foreach ($orderItems as $itemIndex => $orderItem) {
			if ($orderItem instanceof OrderProduct) {
				$products[$itemIndex]['name'] = $orderItem->getName();
				$products[$itemIndex]['quantity'] = $orderItem->getQuantity();
				$products[$itemIndex]['unit_price'] = $orderItem->getPriceWithVat();
				$products[$itemIndex]['total_price'] = $orderItem->getQuantity()*$orderItem->getPriceWithVat();
			}
		}
		$table = '<table>'
		. '<tr>'
			. '<th>Název</th>'
			. '<th>Počet kusů</th>'
			. '<th>Cena za kus s DPH</th>'
			. '<th>Celková cena za položku s DPH</th>'
		. '</tr>'
		. '<tr>';

		foreach ($products as $product) {
			$table .= '<td>' . $product['name'] . '</td>'
				. '<td>' . $product['quantity'] . '</td>'
				. '<td>' . $product['unit_price'] . '</td>'
				. '<td>' . $product['total_price'] . '</td></tr>';
		}

		$table .= '</table>';

		return $table;
	}
}
