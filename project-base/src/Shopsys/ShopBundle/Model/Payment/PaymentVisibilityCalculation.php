<?php

namespace Shopsys\ShopBundle\Model\Payment;

use Shopsys\ShopBundle\Model\Transport\IndependentTransportVisibilityCalculation;

class PaymentVisibilityCalculation
{
    /**
     * @var \Shopsys\ShopBundle\Model\Payment\IndependentPaymentVisibilityCalculation
     */
    private $independentPaymentVisibilityCalculation;

    /**
     * @var \Shopsys\ShopBundle\Model\Transport\IndependentTransportVisibilityCalculation
     */
    private $independentTransportVisibilityCalculation;

    public function __construct(
        IndependentPaymentVisibilityCalculation $independentPaymentVisibilityCalculation,
        IndependentTransportVisibilityCalculation $independentTransportVisibilityCalculation
    ) {
        $this->independentPaymentVisibilityCalculation = $independentPaymentVisibilityCalculation;
        $this->independentTransportVisibilityCalculation = $independentTransportVisibilityCalculation;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Payment\Payment[] $payments
     * @return \Shopsys\ShopBundle\Model\Payment\Payment[]
     */
    public function filterVisible(array $payments, $domainId)
    {
        $visiblePayments = [];
        foreach ($payments as $payment) {
            if ($this->isVisible($payment, $domainId)) {
                $visiblePayments[] = $payment;
            }
        }

        return $visiblePayments;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Payment\Payment $payment
     * @param int $domainId
     * @return bool
     */
    private function isVisible(Payment $payment, $domainId)
    {
        if (!$this->independentPaymentVisibilityCalculation->isIndependentlyVisible($payment, $domainId)) {
            return false;
        }

        return $this->hasIndependentlyVisibleTransport($payment, $domainId);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Payment\Payment $payment
     * @param int $domainId
     * @return bool
     */
    private function hasIndependentlyVisibleTransport(Payment $payment, $domainId)
    {
        foreach ($payment->getTransports() as $transport) {
            /* @var $transport \Shopsys\ShopBundle\Model\Transport\Transport */
            if ($this->independentTransportVisibilityCalculation->isIndependentlyVisible($transport, $domainId)) {
                return true;
            }
        }

        return false;
    }
}
