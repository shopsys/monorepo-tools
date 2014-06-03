<?php

namespace SS6\ShopBundle\Model\Order\Watcher;

use SS6\ShopBundle\Form\Front\Order\OrderFormData;
use SS6\ShopBundle\Model\FlashMessage\FlashMessage;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Transport\Transport;
use Symfony\Component\HttpFoundation\Session\Session;

class TransportAndPaymentWatcherService {

	const SESSION_ROOT = 'transport_and_payment_watcher';
	const SESSION_TRANSPORT_PRICES = 'transport_prices';
	const SESSION_PAYMENT_PRICES = 'payment_prices';

	/**
	 * @var \SS6\ShopBundle\Model\FlashMessage\FlashMessage
	 */
	private $flashMessage;

	/**
	 * @var Symfony\Component\HttpFoundation\Session\Session
	 */
	private $session;

	/**
	 * @param \SS6\ShopBundle\Model\FlashMessage\FlashMessage $flashMessage
	 */
	public function __construct(FlashMessage $flashMessage, Session $session) {
		$this->flashMessage = $flashMessage;
		$this->session = $session;
	}

	/**
	 *
	 * @param \SS6\ShopBundle\Form\Front\Order\OrderFormData $orderFormData
	 * @param \SS6\ShopBundle\Model\Transport\Transport[] $transports
	 * @param \SS6\ShopBundle\Model\Payment\Payment[] $payments
	 */
	public function checkTransportAndPayment(OrderFormData $orderFormData, $transports, $payments) {
		$transport = $orderFormData->getTransport();
		$payment = $orderFormData->getPayment();

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
			$transportPrice = $transportPrices[$transport->getId()];
			if ($transportPrice !== $transport->getPrice()) {
				$message = 'V průběhu objednávkového procesu byla změněna cena dopravy ' . $transport->getName() .
					', prosím, překontrolujte si objednávku.';
				$this->flashMessage->addInfo($message);
			}
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment $payment
	 */
	private function checkPayment(Payment $payment) {
		$paymentPrices = $this->getRememberedPaymentPrices();

		if (array_key_exists($payment->getId(), $paymentPrices)) {
			$paymentPrice = $paymentPrices[$payment->getId()];
			if ($paymentPrice !== $payment->getPrice()) {
				$message = 'V průběhu objednávkového procesu byla změněna cena platby ' . $payment->getName() .
					', prosím, překontrolujte si objednávku.';
				$this->flashMessage->addInfo($message);
			}
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport[] $transports
	 * @return array
	 */
	private function getTransportPrices($transports) {
		$transportPriceChoices = array();
		foreach ($transports as $transport) {
			$transportPriceChoices[$transport->getId()] = $transport->getPrice();
		}

		return $transportPriceChoices;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment[] $transports
	 * @return array
	 */
	private function getPaymentPrices($transports) {
		$transportPriceChoices = array();
		foreach ($transports as $transport) {
			$transportPriceChoices[$transport->getId()] = $transport->getPrice();
		}

		return $transportPriceChoices;
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
