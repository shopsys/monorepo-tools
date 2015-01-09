<?php

namespace SS6\ShopBundle\Model\Order\Mail;

use SS6\ShopBundle\Component\Router\DomainRouterFactory;
use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Mail\MailTemplate;
use SS6\ShopBundle\Model\Mail\MessageData;
use SS6\ShopBundle\Model\Mail\Setting\MailSetting;
use SS6\ShopBundle\Model\Order\Item\OrderItemPriceCalculation;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;
use SS6\ShopBundle\Model\Setting\Setting;
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
	const VARIABLE_TRANSPORT_INSTRUCTIONS = '{transport_instructions}';
	const VARIABLE_PAYMENT_INSTRUCTIONS = '{payment_instructions}';

	/**
	 * @var \SS6\ShopBundle\Model\Setting\Setting
	 */
	private $setting;

	/**
	 * @var \SS6\ShopBundle\Component\Router\DomainRouterFactory
	 */
	private $domainRouterFactory;

	/**
	 *
	 * @var \Twig_Environment
	 */
	private $twig;

	/**
	 * @var \SS6\ShopBundle\Model\Order\Item\OrderItemPriceCalculation
	 */
	private $orderItemPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	public function __construct(
		Setting $setting,
		DomainRouterFactory $domainRouterFactory,
		Twig_Environment $twig,
		OrderItemPriceCalculation $orderItemPriceCalculation,
		Domain $domain
	) {
		$this->setting = $setting;
		$this->domainRouterFactory = $domainRouterFactory;
		$this->twig = $twig;
		$this->orderItemPriceCalculation = $orderItemPriceCalculation;
		$this->domain = $domain;
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
		$router = $this->domainRouterFactory->getRouter($order->getDomainId());

		$domainConfig = $this->domain->getDomainConfigById($order->getDomainId());
		/* @var $domainConfig \SS6\ShopBundle\Model\Domain\Config\DomainConfig */

		$transport = $order->getTransport();
		/* @var $transport \SS6\ShopBundle\Model\Transport\Transport */

		$payment = $order->getPayment();
		/* @var $payment \SS6\ShopBundle\Model\Payment\Payment */

		$transportInstructions = $transport->getInstructions($domainConfig->getLocale());
		$paymentInstructions = $payment->getInstructions($domainConfig->getLocale());

		return array(
			self::VARIABLE_NUMBER  => $order->getNumber(),
			self::VARIABLE_DATE => $order->getCreatedAt()->format('d-m-Y H:i'),
			self::VARIABLE_URL => $router->generate('front_homepage', array(), true),
			self::VARIABLE_TRANSPORT => $order->getTransportName(),
			self::VARIABLE_PAYMENT => $order->getPaymentName(),
			self::VARIABLE_TOTAL_PRICE => $order->getTotalPriceWithVat(),
			self::VARIABLE_BILLING_ADDRESS => $this->getBillingAddressHtmlTable($order),
			self::VARIABLE_DELIVERY_ADDRESS => $this->getDeliveryAddressHtmlTable($order),
			self::VARIABLE_NOTE  => $order->getNote(),
			self::VARIABLE_PRODUCTS => $this->getProductsHtmlTable($order),
			self::VARIABLE_ORDER_DETAIL_URL => $this->getOrderDetailUrl($order),
			self::VARIABLE_TRANSPORT_INSTRUCTIONS => $transportInstructions,
			self::VARIABLE_PAYMENT_INSTRUCTIONS => $paymentInstructions,
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
			self::VARIABLE_NUMBER,
			self::VARIABLE_DATE,
			self::VARIABLE_URL,
			self::VARIABLE_TRANSPORT,
			self::VARIABLE_PAYMENT,
			self::VARIABLE_TOTAL_PRICE,
			self::VARIABLE_BILLING_ADDRESS,
			self::VARIABLE_DELIVERY_ADDRESS,
			self::VARIABLE_NOTE,
			self::VARIABLE_PRODUCTS,
			self::VARIABLE_ORDER_DETAIL_URL,
			self::VARIABLE_TRANSPORT_INSTRUCTIONS,
			self::VARIABLE_PAYMENT_INSTRUCTIONS,
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
		return $this->domainRouterFactory->getRouter($order->getDomainId())->generate(
			'front_customer_order_detail_unregistered', ['urlHash' => $order->getUrlHash()], true
		);
	}

}
