<?php

namespace Shopsys\FrameworkBundle\Model\Payment\Exception;

use Exception;

class PaymentDomainNotFoundException extends Exception implements PaymentException
{
    /**
     * @param int|null $paymentId
     * @param int $domainId
     * @param \Exception|null $previous
     */
    public function __construct(?int $paymentId = null, int $domainId, ?Exception $previous = null)
    {
        $paymentDescription = $paymentId !== null ? sprintf('with ID %d', $paymentId) : 'without ID';
        $message = sprintf('PaymentDomain for payment %s and domain ID %d not found.', $paymentDescription, $domainId);

        parent::__construct($message, 0, $previous);
    }
}
