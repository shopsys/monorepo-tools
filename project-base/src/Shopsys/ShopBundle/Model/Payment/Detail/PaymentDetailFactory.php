<?php

namespace Shopsys\ShopBundle\Model\Payment\Detail;

use Shopsys\ShopBundle\Model\Payment\Payment;
use Shopsys\ShopBundle\Model\Payment\PaymentPriceCalculation;

class PaymentDetailFactory {

    /**
     * @var \Shopsys\ShopBundle\Model\Payment\PaymentPriceCalculation
     */
    private $paymentPriceCalculation;

    /**
     * @param \Shopsys\ShopBundle\Model\Payment\PaymentPriceCalculation $paymentPriceCalculation
     */
    public function __construct(PaymentPriceCalculation $paymentPriceCalculation) {
        $this->paymentPriceCalculation = $paymentPriceCalculation;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Payment\Payment $payment
     * @return \Shopsys\ShopBundle\Model\Payment\Detail\PaymentDetail
     */
    public function createDetailForPayment(Payment $payment) {
        return new PaymentDetail(
            $payment,
            $this->getIndependentPrices($payment)
        );
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Payment\Payment[] $payments
     * @return \Shopsys\ShopBundle\Model\Payment\Detail\PaymentDetail[]
     */
    public function createDetailsForPayments(array $payments) {
        $details = [];

        foreach ($payments as $payment) {
            $details[] = $this->createDetailForPayment($payment);
        }

        return $details;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Payment\Payment $payment
     * @return \Shopsys\ShopBundle\Model\Pricing\Price
     */
    private function getIndependentPrices(Payment $payment) {
        $prices = [];
        foreach ($payment->getPrices() as $paymentInputPrice) {
            $currency = $paymentInputPrice->getCurrency();
            $prices[$currency->getId()] = $this->paymentPriceCalculation->calculateIndependentPrice($payment, $currency);
        }
        return $prices;
    }

}
