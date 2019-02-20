<?php

namespace Shopsys\FrameworkBundle\Model\Pricing;

use Shopsys\FrameworkBundle\Component\Money\Money;

class Price
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Money\Money
     */
    protected $priceWithoutVat;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Money\Money
     */
    protected $priceWithVat;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Money\Money
     */
    protected $vatAmount;

    /**
     * @param string $priceWithoutVat
     * @param string $priceWithVat
     */
    public function __construct($priceWithoutVat, $priceWithVat)
    {
        $this->priceWithoutVat = Money::fromValue($priceWithoutVat);
        $this->priceWithVat = Money::fromValue($priceWithVat);
        $this->vatAmount = $this->priceWithVat->subtract($this->priceWithoutVat);
    }

    /**
     * @return string
     */
    public function getPriceWithoutVat(): string
    {
        return $this->priceWithoutVat->toValue();
    }

    /**
     * @return string
     */
    public function getPriceWithVat(): string
    {
        return $this->priceWithVat->toValue();
    }

    /**
     * @return string
     */
    public function getVatAmount(): string
    {
        return $this->vatAmount->toValue();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price $priceToAdd
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price
     */
    public function add(self $priceToAdd): self
    {
        return new self(
            $this->priceWithoutVat->add($priceToAdd->priceWithoutVat)->toValue(),
            $this->priceWithVat->add($priceToAdd->priceWithVat)->toValue()
        );
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price $priceToSubtract
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price
     */
    public function subtract(self $priceToSubtract): self
    {
        return new self(
            $this->priceWithoutVat->subtract($priceToSubtract->priceWithoutVat)->toValue(),
            $this->priceWithVat->subtract($priceToSubtract->priceWithVat)->toValue()
        );
    }
}
