<?php

namespace SS6\ShopBundle\Model\Order\Mail;

use SS6\ShopBundle\Model\Mail\MailTemplate;
use SS6\ShopBundle\Model\Mail\MessageData;
use SS6\ShopBundle\Model\Mail\Setting\MailSetting;
use SS6\ShopBundle\Model\Order\Item\PriceCalculation;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;
use SS6\ShopBundle\Model\Setting\Setting;
use Symfony\Cmf\Component\Routing\ChainRouter;
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
	const VARIABLE_ORDER_DETAIL_URL = '{order_detail_url}';

	/**
	 * @var \SS6\ShopBundle\Model\Setting\Setting
	 */
	private $setting;

	/**
	 * @var \Symfony\Cmf\Component\Routing\ChainRouter
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

	public function __construct(
		Setting $setting,
		ChainRouter $router,
		Twig_Environment $twig,
		PriceCalculation $orderItemPriceCalculation
	) {
		$this->setting = $setting;
		$this->router = $router;
		$this->twig = $twig;
		$this->orderItemPriceCalculation = $orderItemPriceCalculation;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @param \SS6\ShopBundle\Model\Mail\MailTemplate $mailTemplate
	 * @return \SS6\ShopBundle\Model\Mail\MessageData
	 */
	public function getMessageDataByOrder(Order $order, MailTemplate $mailTemplate) {
		return new MessageData(
			$order->getEmail(),
			$mailTemplate->getBody(),
			$mailTemplate->getSubject(),
			$this->setting->get(MailSetting::MAIN_ADMIN_MAIL, $order->getDomainId()),
			$this->setting->get(MailSetting::MAIN_ADMIN_MAIL_NAME, $order->getDomainId()),
			$this->getVariablesReplacementsForBody($order),
			$this->getVariablesReplacementsForSubject($order)
		);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatus $orderStatus
	 * @return string
	 */
	public function getMailTemplateNameByStatus(OrderStatus $orderStatus) {
		return self::MAIL_TEMPLATE_NAME_PREFIX . $orderStatus->getId();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @return array
	 */
	private function getVariablesReplacementsForBody(Order $order) {
		return array(
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
			self::VARIABLE_ORDER_DETAIL_URL => $this->getOrderDetailUrl($order),
		);

	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @return array
	 */
	private function getVariablesReplacementsForSubject(Order $order) {
		return array(
			self::VARIABLE_NUMBER  => $order->getNumber(),
			self::VARIABLE_DATE => $order->getCreatedAt()->format('d-m-Y H:i'),
		);

	}

	/**
	 * @return array
	 */
	public function getTemplateVariables() {
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
			self::VARIABLE_ORDER_DETAIL_URL => 'URL adresa detailu objednávky',
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

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @return string
	 */
	private function getOrderDetailUrl(Order $order) {
		return $this->router->generate(
			'front_customer_order_detail_unregistered', ['urlHash' => $order->getUrlHash()], true
		);
	}

}
