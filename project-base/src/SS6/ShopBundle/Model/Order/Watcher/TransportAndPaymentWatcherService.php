<?php

namespace SS6\ShopBundle\Model\Order\Watcher;

use SS6\ShopBundle\Model\Order\OrderData;
use SS6\ShopBundle\Model\FlashMessage\Bag;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Payment\PriceCalculation as PaymentPriceCalculation;
use SS6\ShopBundle\Model\Transport\Transport;
use SS6\ShopBundle\Model\Transport\PriceCalculation as TransportPriceCalculation;
use Symfony\Component\HttpFoundation\Session\Session;

class TransportAndPaymentWatcherService {

	const SESSION_ROOT = 'transport_and_payment_watcher';
	const SESSION_TRANSPORT_PRICES = 'transport_prices';
	const SESSION_PAYMENT_PRICES = 'payment_prices';

	/**
	 * @var \SS6\ShopBundle\Model\FlashMessage\Bag
	 */
	private $flashMessageBag;

	/**
	 * @var \Symfony\Component\HttpFoundation\Session\Session
	 */
	private $session;

	/**
	 * @var \SS6\ShopBundle\Model\Payment\PriceCalculation
	 */
	private $paymentPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Transport\PriceCalculation
	 */
	private $transportPriceCalculation;

	/**
	 * @param \SS6\ShopBundle\Model\FlashMessage\Bag $flashMessageBag
	 * @param \Symfony\Component\HttpFoundation\Session\Session $session
	 * @param \SS6\ShopBundle\Model\Payment\PriceCalculation $paymentPriceCalculation
	 * @param \SS6\ShopBundle\Model\Transport\PriceCalculation $transportPriceCalculation
	 */
	public function __construct(
		Bag $flashMessageBag,
		Session $session,
		PaymentPriceCalculation $paymentPriceCalculation,
		TransportPriceCalculation $transportPriceCalculation
	) {
		$this->flashMessageBag = $flashMessageBag;
		$this->session = $session;
		$this->paymentPriceCalculation = $paymentPriceCalculation;
		$this->transportPriceCalculation = $transportPriceCalculation;
	}

	/**
	 *
	 * @param \SS6\ShopBundle\Model\Order\OrderData $orderData
	 * @param \SS6\ShopBundle\Model\Transport\Transport[] $transports
	 * @param \SS6\ShopBundle\Model\Payment\Payment[] $payments
	 */
	public function checkTransportAndPayment(OrderData $orderData, $transports, $payments) {
		$transport = $orderData->getTransport();
		$payment = $orderData->getPayment();

		if ($transport !== null) {
			$this->checkTransport($transport);
		}

		if ($payment !== null) {
			$this->checkPayment($payment);
		}

		$this->rememberTransportAndPayment($transports, $payments);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 */
	private function checkTransport(Transport $transport) {
		$transportPrices = $this->getRememberedTransportPrices();

		if (array_key_exists($transport->getId(), $transportPrices)) {
			$rememberedTransportPriceValue = $transportPrices[$transport->getId()];
			$transportPrice = $this->transportPriceCalculation->calculatePrice($transport);

			if ($rememberedTransportPriceValue != $transportPrice->getBasePriceWithVat()) {
				$message = 'V průběhu objednávkového procesu byla změněna cena dopravy ' . $transport->getName() .
					'. Prosím, překontrolujte si objednávku.';
				$this->flashMessageBag->addInfo($message);
			}
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment $payment
	 */
	private function checkPayment(Payment $payment) {
		$paymentPrices = $this->getRememberedPaymentPrices();

		if (array_key_exists($payment->getId(), $paymentPrices)) {
			$rememberedPaymentPriceValue = $paymentPrices[$payment->getId()];
			$paymentPrice = $this->paymentPriceCalculation->calculatePrice($payment);

			if ($rememberedPaymentPriceValue !== $paymentPrice->getBasePriceWithVat()) {
				$message = 'V průběhu objednávkového procesu byla změněna cena platby ' . $payment->getName() .
					'. Prosím, překontrolujte si objednávku.';
				$this->flashMessageBag->addInfo($message);
			}
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport[] $transports
	 * @return array
	 */
	private function getTransportPrices($transports) {
		$transportPriceValues = array();
		foreach ($transports as $transport) {
			$transportPrice = $this->transportPriceCalculation->calculatePrice($transport);
			$transportPriceValues[$transport->getId()] = $transportPrice->getBasePriceWithVat();
		}

		return $transportPriceValues;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment[] $payments
	 * @return array
	 */
	private function getPaymentPrices($payments) {
		$paymentPriceValues = array();
		foreach ($payments as $payment) {
			$paymentPrice = $this->paymentPriceCalculation->calculatePrice($payment);
			$paymentPriceValues[$payment->getId()] = $paymentPrice->getBasePriceWithVat();
		}

		return $paymentPriceValues;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport[] $transports
	 * @param \SS6\ShopBundle\Model\Payment\Payment[] $payments
	 */
	private function rememberTransportAndPayment($transports, $payments) {
		$this->session->set(self::SESSION_ROOT, array(
			self::SESSION_TRANSPORT_PRICES => $this->getTransportPrices($transports),
			self::SESSION_PAYMENT_PRICES => $this->getPaymentPrices($payments),
		));
	}

	/**
	 * @return array
	 */
	private function getRememberedTransportAndPayment() {
		return $this->session->get(self::SESSION_ROOT, array(
			self::SESSION_TRANSPORT_PRICES => array(),
			self::SESSION_PAYMENT_PRICES => array(),
		));
	}

	/**
	 * @return array
	 */
	private function getRememberedTransportPrices() {
		return $this->getRememberedTransportAndPayment()[self::SESSION_TRANSPORT_PRICES];
	}

	/**
	 * @return array
	 */
	private function getRememberedPaymentPrices() {
		return $this->getRememberedTransportAndPayment()[self::SESSION_PAYMENT_PRICES];
	}

}
