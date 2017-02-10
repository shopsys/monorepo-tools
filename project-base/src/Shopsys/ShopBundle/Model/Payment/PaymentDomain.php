<?php

namespace Shopsys\ShopBundle\Model\Payment;

use Doctrine\ORM\Mapping as ORM;
use Shopsys\ShopBundle\Model\Payment\Payment;

/**
 * @ORM\Table(name="payment_domains")
 * @ORM\Entity
 */
class PaymentDomain
{

    /**
     * @var \Shopsys\ShopBundle\Model\Payment\Payment
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Shopsys\ShopBundle\Model\Payment\Payment")
     * @ORM\JoinColumn(nullable=false)
     */
    private $payment;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $domainId;

    /**
     * @param \Shopsys\ShopBundle\Model\Payment\Payment $payment
     * @param int $domainId
     */
    public function __construct(Payment $payment, $domainId) {
        $this->payment = $payment;
        $this->domainId = $domainId;
    }

    /**
     * @return int
     */
    public function getDomainId() {
        return $this->domainId;
    }
}
