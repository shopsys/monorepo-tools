<?php

namespace SS6\ShopBundle\Model\Order;

use SS6\ShopBundle\Model\Order\Mail\OrderMailService;

class OrderConfirmationTextFacade {

	/**
	 * @var \SS6\ShopBundle\Model\Order\OrderFacade
	 */
	private $orderFacade;

	public function __construct(OrderFacade $orderFacade) {
		$this->orderFacade = $orderFacade;
	}

	/**
	 * @param string $text
	 * @param int $orderId
	 * @return string
	 */
	public function replaceTextVariables($text, $orderId) {
		$order = $this->orderFacade->getById($orderId);

		$transport = $order->getTransport();
		$payment = $order->getPayment();

		$transportInstructions = $transport->getInstructions();
		$paymentInstructions = $payment->getInstructions();

		$variables = [
			OrderMailService::VARIABLE_TRANSPORT_INSTRUCTIONS => $transportInstructions,
			OrderMailService::VARIABLE_PAYMENT_INSTRUCTIONS => $paymentInstructions,
		];
		$finalText = strtr($text, $variables);
		return $finalText;
	}

}