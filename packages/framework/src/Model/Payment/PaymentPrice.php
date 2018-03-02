<?php

namespace Shopsys\FrameworkBundle\Model\Payment;

use Doctrine\ORM\Mapping as ORM;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency;

/**
 * @ORM\Table(name="payment_prices")
 * @ORM\Entity
 */
class PaymentPrice
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Payment\Payment
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Shopsys\FrameworkBundle\Model\Payment\Payment", inversedBy="prices")
     * @ORM\JoinColumn(nullable=false)
     */
    private $payment;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $currency;

    /**
     * @var string
     *
     * @ORM\Column(type="decimal", precision=20, scale=6)
     */
    private $price;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Payment\Payment $payment
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency $currency
     * @param string $price
     */
    public function __construct(Payment $payment, Currency $currency, $price)
    {
        $this->payment = $payment;
        $this->currency = $currency;
        $this->price = $price;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Payment\Payment
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param string $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }
}
