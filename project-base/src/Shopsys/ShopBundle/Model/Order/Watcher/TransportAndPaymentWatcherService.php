<?php

namespace Shopsys\ShopBundle\Model\Order\Watcher;

use Shopsys\ShopBundle\Component\FlashMessage\Bag;
use Shopsys\ShopBundle\Model\Order\OrderData;
use Shopsys\ShopBundle\Model\Order\Preview\OrderPreview;
use Shopsys\ShopBundle\Model\Payment\Payment;
use Shopsys\ShopBundle\Model\Payment\PaymentPriceCalculation;
use Shopsys\ShopBundle\Model\Pricing\Currency\Currency;
use Shopsys\ShopBundle\Model\Transport\Transport;
use Shopsys\ShopBundle\Model\Transport\TransportPriceCalculation;
use Symfony\Component\HttpFoundation\Session\Session;

class TransportAndPaymentWatcherService
{
    const SESSION_ROOT = 'transport_and_payment_watcher';
    const SESSION_TRANSPORT_PRICES = 'transport_prices';
    const SESSION_PAYMENT_PRICES = 'payment_prices';

    /**
     * @var \Shopsys\ShopBundle\Component\FlashMessage\Bag
     */
    private $flashMessageBag;

    /**
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    private $session;

    /**
     * @var \Shopsys\ShopBundle\Model\Payment\PaymentPriceCalculation
     */
    private $paymentPriceCalculation;

    /**
     * @var \Shopsys\ShopBundle\Model\Transport\TransportPriceCalculation
     */
    private $transportPriceCalculation;

    /**
     * @param \Shopsys\ShopBundle\Component\FlashMessage\Bag $flashMessageBag
     * @param \Symfony\Component\HttpFoundation\Session\Session $session
     * @param \Shopsys\ShopBundle\Model\Payment\PaymentPriceCalculation $paymentPriceCalculation
     * @param \Shopsys\ShopBundle\Model\Transport\TransportPriceCalculation $transportPriceCalculation
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
     * @param \Shopsys\ShopBundle\Model\Order\OrderData $orderData
     * @param \Shopsys\ShopBundle\Model\Order\Preview\OrderPreview $orderPreview
     * @param \Shopsys\ShopBundle\Model\Transport\Transport[] $transports
     * @param \Shopsys\ShopBundle\Model\Payment\Payment[] $payments
     * @return \Shopsys\ShopBundle\Model\Order\Watcher\TransportAndPaymentCheckResult
     */
    public function checkTransportAndPayment(OrderData $orderData, OrderPreview $orderPreview, $transports, $payments)
    {
        $transport = $orderData->transport;
        $payment = $orderData->payment;

        $transportPriceChanged = false;
        if ($transport !== null) {
            $transportPriceChanged = $this->checkTransportPrice(
                $transport,
                $orderData->currency,
                $orderPreview,
                $orderData->domainId
            );
        }

        $paymentPriceChanged = false;
        if ($payment !== null) {
            $paymentPriceChanged = $this->checkPaymentPrice(
                $payment,
                $orderData->currency,
                $orderPreview,
                $orderData->domainId
            );
        }

        $this->rememberTransportAndPayment(
            $transports,
            $payments,
            $orderData->currency,
            $orderPreview,
            $orderData->domainId
        );

        return new TransportAndPaymentCheckResult($transportPriceChanged, $paymentPriceChanged);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Transport\Transport $transport
     * @param \Shopsys\ShopBundle\Model\Pricing\Currency\Currency $currency
     * @param \Shopsys\ShopBundle\Model\Order\Preview\OrderPreview $orderPreview
     * @param int $domainId
     * @return bool
     */
    private function checkTransportPrice(
        Transport $transport,
        Currency $currency,
        OrderPreview $orderPreview,
        $domainId
    ) {
        $transportPrices = $this->getRememberedTransportPrices();

        if (array_key_exists($transport->getId(), $transportPrices)) {
            $rememberedTransportPriceValue = $transportPrices[$transport->getId()];
            $transportPrice = $this->transportPriceCalculation->calculatePrice(
                $transport,
                $currency,
                $orderPreview->getProductsPrice(),
                $domainId
            );

            if ($rememberedTransportPriceValue != $transportPrice->getPriceWithVat()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Payment\Payment $payment
     * @param \Shopsys\ShopBundle\Model\Pricing\Currency\Currency $currency
     * @param \Shopsys\ShopBundle\Model\Order\Preview\OrderPreview $orderPreview
     * @param int $domainId
     * @return bool
     */
    private function checkPaymentPrice(
        Payment $payment,
        Currency $currency,
        OrderPreview $orderPreview,
        $domainId
    ) {
        $paymentPrices = $this->getRememberedPaymentPrices();

        if (array_key_exists($payment->getId(), $paymentPrices)) {
            $rememberedPaymentPriceValue = $paymentPrices[$payment->getId()];
            $paymentPrice = $this->paymentPriceCalculation->calculatePrice(
                $payment,
                $currency,
                $orderPreview->getProductsPrice(),
                $domainId
            );

            if ($rememberedPaymentPriceValue !== $paymentPrice->getPriceWithVat()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Transport\Transport[] $transports
     * @param \Shopsys\ShopBundle\Model\Pricing\Currency\Currency $currency
     * @param \Shopsys\ShopBundle\Model\Order\Preview\OrderPreview $orderPreview
     * @param int $domainId
     * @return array
     */
    private function getTransportPrices(
        $transports,
        Currency $currency,
        OrderPreview $orderPreview,
        $domainId
    ) {
        $transportPriceValues = [];
        foreach ($transports as $transport) {
            $transportPrice = $this->transportPriceCalculation->calculatePrice(
                $transport,
                $currency,
                $orderPreview->getProductsPrice(),
                $domainId
            );
            $transportPriceValues[$transport->getId()] = $transportPrice->getPriceWithVat();
        }

        return $transportPriceValues;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Payment\Payment[] $payments
     * @param \Shopsys\ShopBundle\Model\Pricing\Currency\Currency $currency
     * @param \Shopsys\ShopBundle\Model\Order\Preview\OrderPreview $orderPreview
     * @param int $domainId
     * @return array
     */
    private function getPaymentPrices(
        $payments,
        Currency $currency,
        OrderPreview $orderPreview,
        $domainId
    ) {
        $paymentPriceValues = [];
        foreach ($payments as $payment) {
            $paymentPrice = $this->paymentPriceCalculation->calculatePrice(
                $payment,
                $currency,
                $orderPreview->getProductsPrice(),
                $domainId
            );
            $paymentPriceValues[$payment->getId()] = $paymentPrice->getPriceWithVat();
        }

        return $paymentPriceValues;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Transport\Transport[] $transports
     * @param \Shopsys\ShopBundle\Model\Payment\Payment[] $payments
     * @param \Shopsys\ShopBundle\Model\Pricing\Currency\Currency $currency
     * @param \Shopsys\ShopBundle\Model\Order\Preview\OrderPreview $orderPreview
     * @param int $domainId
     */
    private function rememberTransportAndPayment(
        array $transports,
        array $payments,
        Currency $currency,
        OrderPreview $orderPreview,
        $domainId
    ) {
        $this->session->set(self::SESSION_ROOT, [
            self::SESSION_TRANSPORT_PRICES => $this->getTransportPrices(
                $transports,
                $currency,
                $orderPreview,
                $domainId
            ),
            self::SESSION_PAYMENT_PRICES => $this->getPaymentPrices(
                $payments,
                $currency,
                $orderPreview,
                $domainId
            ),
        ]);
    }

    /**
     * @return array
     */
    private function getRememberedTransportAndPayment()
    {
        return $this->session->get(self::SESSION_ROOT, [
            self::SESSION_TRANSPORT_PRICES => [],
            self::SESSION_PAYMENT_PRICES => [],
        ]);
    }

    /**
     * @return array
     */
    private function getRememberedTransportPrices()
    {
        return $this->getRememberedTransportAndPayment()[self::SESSION_TRANSPORT_PRICES];
    }

    /**
     * @return array
     */
    private function getRememberedPaymentPrices()
    {
        return $this->getRememberedTransportAndPayment()[self::SESSION_PAYMENT_PRICES];
    }
}
