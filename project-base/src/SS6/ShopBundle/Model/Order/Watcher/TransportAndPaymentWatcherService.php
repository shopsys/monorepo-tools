<?php

namespace SS6\ShopBundle\Model\Order\Watcher;

use SS6\ShopBundle\Model\FlashMessage\Bag;
use SS6\ShopBundle\Model\Order\OrderData;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Payment\PaymentPriceCalculation;
use SS6\ShopBundle\Model\Pricing\Currency\Currency;
use SS6\ShopBundle\Model\Transport\Transport;
use SS6\ShopBundle\Model\Transport\TransportPriceCalculation;
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
	 * @var \SS6\ShopBundle\Model\Payment\PaymentPriceCalculation
	 */
	private $paymentPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Transport\TransportPriceCalculation
	 */
	private $transportPriceCalculation;

	/**
	 * @param \SS6\ShopBundle\Model\FlashMessage\Bag $flashMessageBag
	 * @param \Symfony\Component\HttpFoundation\Session\Session $session
	 * @param \SS6\ShopBundle\Model\Payment\PaymentPriceCalculation $paymentPriceCalculation
	 * @param \SS6\ShopBundle\Model\Transport\TransportPriceCalculation $transportPriceCalculation
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
	 * @param \SS6\ShopBundle\Model\Order\OrderData $orderData
	 * @param \SS6\ShopBundle\Model\Transport\Transport[] $transports
	 * @param \SS6\ShopBundle\Model\Payment\Payment[] $payments
	 * @return \SS6\ShopBundle\Model\Order\Watcher\TransportAndPaymentCheckResult
	 */
	public function checkTransportAndPayment(OrderData $orderData, $transports, $payments) {
		$transport = $orderData->transport;
		$payment = $orderData->payment;

		$transportPriceChanged = false;
		if ($transport !== null) {
			$transportPriceChanged = $this->checkTransportPrice($transport, $orderData->currency);
		}

		$paymentPriceChanged = false;
		if ($payment !== null) {
			$paymentPriceChanged = $this->checkPaymentPrice($payment, $orderData->currency);
		}

		$this->rememberTransportAndPayment($transports, $payments, $orderData->currency);

		return new TransportAndPaymentCheckResult($transportPriceChanged, $paymentPriceChanged);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\Currency $currency
	 * @return boolean
	 */
	private function checkTransportPrice(Transport $transport, Currency $currency) {
		$transportPrices = $this->getRememberedTransportPrices();

		if (array_key_exists($transport->getId(), $transportPrices)) {
			$rememberedTransportPriceValue = $transportPrices[$transport->getId()];
			$transportPrice = $this->transportPriceCalculation->calculatePrice($transport, $currency);

			if ($rememberedTransportPriceValue != $transportPrice->getPriceWithVat()) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment $payment
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\Currency $currency
	 * @return boolean
	 */
	private function checkPaymentPrice(Payment $payment, Currency $currency) {
		$paymentPrices = $this->getRememberedPaymentPrices();

		if (array_key_exists($payment->getId(), $paymentPrices)) {
			$rememberedPaymentPriceValue = $paymentPrices[$payment->getId()];
			$paymentPrice = $this->paymentPriceCalculation->calculatePrice($payment, $currency);

			if ($rememberedPaymentPriceValue !== $paymentPrice->getPriceWithVat()) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport[] $transports
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\Currency $currency
	 * @return array
	 */
	private function getTransportPrices($transports, Currency $currency) {
		$transportPriceValues = [];
		foreach ($transports as $transport) {
			$transportPrice = $this->transportPriceCalculation->calculatePrice($transport, $currency);
			$transportPriceValues[$transport->getId()] = $transportPrice->getPriceWithVat();
		}

		return $transportPriceValues;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment[] $payments
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\Currency $currency
	 * @return array
	 */
	private function getPaymentPrices($payments, Currency $currency) {
		$paymentPriceValues = [];
		foreach ($payments as $payment) {
			$paymentPrice = $this->paymentPriceCalculation->calculatePrice($payment, $currency);
			$paymentPriceValues[$payment->getId()] = $paymentPrice->getPriceWithVat();
		}

		return $paymentPriceValues;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport[] $transports
	 * @param \SS6\ShopBundle\Model\Payment\Payment[] $payments
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\Currency $currency
	 */
	private function rememberTransportAndPayment($transports, $payments, Currency $currency) {
		$this->session->set(self::SESSION_ROOT, [
			self::SESSION_TRANSPORT_PRICES => $this->getTransportPrices($transports, $currency),
			self::SESSION_PAYMENT_PRICES => $this->getPaymentPrices($payments, $currency),
		]);
	}

	/**
	 * @return array
	 */
	private function getRememberedTransportAndPayment() {
		return $this->session->get(self::SESSION_ROOT, [
			self::SESSION_TRANSPORT_PRICES => [],
			self::SESSION_PAYMENT_PRICES => [],
		]);
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
