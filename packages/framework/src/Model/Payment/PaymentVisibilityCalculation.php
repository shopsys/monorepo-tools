<?php

namespace Shopsys\FrameworkBundle\Model\Payment;

use Shopsys\FrameworkBundle\Model\Transport\IndependentTransportVisibilityCalculation;

class PaymentVisibilityCalculation
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Payment\IndependentPaymentVisibilityCalculation
     */
    private $independentPaymentVisibilityCalculation;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Transport\IndependentTransportVisibilityCalculation
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
     * @param \Shopsys\FrameworkBundle\Model\Payment\Payment[] $payments
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Payment\Payment[]
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
     * @param \Shopsys\FrameworkBundle\Model\Payment\Payment $payment
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
     * @param \Shopsys\FrameworkBundle\Model\Payment\Payment $payment
     * @param int $domainId
     * @return bool
     */
    private function hasIndependentlyVisibleTransport(Payment $payment, $domainId)
    {
        foreach ($payment->getTransports() as $transport) {
            /* @var $transport \Shopsys\FrameworkBundle\Model\Transport\Transport */
            if ($this->independentTransportVisibilityCalculation->isIndependentlyVisible($transport, $domainId)) {
                return true;
            }
        }

        return false;
    }
}
