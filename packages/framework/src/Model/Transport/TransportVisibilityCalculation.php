<?php

namespace Shopsys\FrameworkBundle\Model\Transport;

use Shopsys\FrameworkBundle\Model\Payment\IndependentPaymentVisibilityCalculation;

class TransportVisibilityCalculation
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Transport\IndependentTransportVisibilityCalculation
     */
    private $independentTransportVisibilityCalculation;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Payment\IndependentPaymentVisibilityCalculation
     */
    private $independentPaymentVisibilityCalculation;

    public function __construct(
        IndependentTransportVisibilityCalculation $independentTransportVisibilityCalculation,
        IndependentPaymentVisibilityCalculation $independentPaymentVisibilityCalculation
    ) {
        $this->independentTransportVisibilityCalculation = $independentTransportVisibilityCalculation;
        $this->independentPaymentVisibilityCalculation = $independentPaymentVisibilityCalculation;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Transport\Transport $transport
     * @param \Shopsys\FrameworkBundle\Model\Payment\Payment[] $allPaymentsOnDomain
     * @param int $domainId
     * @return bool
     */
    public function isVisible(Transport $transport, array $allPaymentsOnDomain, $domainId)
    {
        if (!$this->independentTransportVisibilityCalculation->isIndependentlyVisible($transport, $domainId)) {
            return false;
        }

        return $this->existsIndependentlyVisiblePaymentWithTransport($allPaymentsOnDomain, $transport, $domainId);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Payment\Payment[] $payments
     * @param \Shopsys\FrameworkBundle\Model\Transport\Transport $transport
     * @param int $domainId
     * @return bool
     */
    private function existsIndependentlyVisiblePaymentWithTransport(array $payments, Transport $transport, $domainId)
    {
        foreach ($payments as $payment) {
            if ($this->independentPaymentVisibilityCalculation->isIndependentlyVisible($payment, $domainId)) {
                if ($payment->getTransports()->contains($transport)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Transport\Transport[] $transports
     * @param \Shopsys\FrameworkBundle\Model\Payment\Payment[] $visiblePaymentsOnDomain
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Transport\Transport[]
     */
    public function filterVisible(array $transports, array $visiblePaymentsOnDomain, $domainId)
    {
        $visibleTransports = [];

        foreach ($transports as $transport) {
            if ($this->isVisible($transport, $visiblePaymentsOnDomain, $domainId)) {
                $visibleTransports[] = $transport;
            }
        }

        return $visibleTransports;
    }
}
