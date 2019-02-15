<?php

namespace Shopsys\FrameworkBundle\Model\Payment;

use Doctrine\ORM\Mapping as ORM;
use Shopsys\FrameworkBundle\Component\Money\Money;
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
    protected $payment;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    protected $currency;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Money\Money
     *
     * @ORM\Column(type="money", precision=20, scale=6)
     */
    protected $price;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Payment\Payment $payment
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency $currency
     * @param string $price
     */
    public function __construct(Payment $payment, Currency $currency, $price)
    {
        $this->payment = $payment;
        $this->currency = $currency;
        $this->setPrice($price);
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
        return $this->price->toValue();
    }

    /**
     * @param string $price
     */
    public function setPrice($price)
    {
        $this->price = Money::fromValue($price);
    }
}
