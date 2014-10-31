<?php

namespace SS6\ShopBundle\Model\Order\Mail;

use SS6\ShopBundle\Model\Mail\MailTemplate;
use SS6\ShopBundle\Model\Order\Item\PriceCalculation;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;
use Swift_Message;
use Symfony\Component\Routing\Router;
use Twig_Environment;

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
	 *
	 * @var \Twig_Environment
	 */
	private $twig;

	/**
	 * @var \SS6\ShopBundle\Model\Order\Item\PriceCalculation
	 */
	private $orderItemPriceCalculation;

	/**
	 * @param type $senderEmail
	 * @param \Symfony\Component\Routing\Router $router
	 * @param Twig_Environment $twig
	 * @param \SS6\ShopBundle\Model\Order\Item\PriceCalculation
	 */
	public function __construct(
		$senderEmail,
		Router $router,
		Twig_Environment $twig,
		PriceCalculation $orderItemPriceCalculation
	) {
		$this->senderEmail = $senderEmail;
		$this->router = $router;
		$this->twig = $twig;
		$this->orderItemPriceCalculation = $orderItemPriceCalculation;
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
			self::VARIABLE_TRANSPORT => $order->getTransportName(),
			self::VARIABLE_PAYMENT => $order->getPaymentName(),
			self::VARIABLE_TOTAL_PRICE => $order->getTotalPriceWithVat(),
			self::VARIABLE_BILLING_ADDRESS => $this->getBillingAddressHtmlTable($order),
			self::VARIABLE_DELIVERY_ADDRESS => $this->getDeliveryAddressHtmlTable($order),
			self::VARIABLE_NOTE  => $order->getNote(),
			self::VARIABLE_PRODUCTS => $this->getProductsHtmlTable($order),
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
	private function getBillingAddressHtmlTable(Order $order) {

		return $this->twig->render('@SS6Shop/Mail/Order/billingAddress.html.twig', array(
			'order' => $order,
		));

	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @return string
	 */
	private function getDeliveryAddressHtmlTable(Order $order) {

		return $this->twig->render('@SS6Shop/Mail/Order/deliveryAddress.html.twig', array(
			'order' => $order,
		));
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @return string
	 */
	private function getProductsHtmlTable(Order $order) {
		$orderItemTotalPricesById = $this->orderItemPriceCalculation->calculateTotalPricesIndexedById($order->getItems());

		return $this->twig->render('@SS6Shop/Mail/Order/products.html.twig', array(
			'order' => $order,
			'orderItemTotalPricesById' => $orderItemTotalPricesById,
		));

	}
}
