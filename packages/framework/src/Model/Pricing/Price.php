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
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $priceWithoutVat
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $priceWithVat
     */
    public function __construct(Money $priceWithoutVat, Money $priceWithVat)
    {
        $this->priceWithoutVat = $priceWithoutVat;
        $this->priceWithVat = $priceWithVat;
        $this->vatAmount = $priceWithVat->subtract($priceWithoutVat);
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
            $this->priceWithoutVat->add($priceToAdd->priceWithoutVat),
            $this->priceWithVat->add($priceToAdd->priceWithVat)
        );
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price $priceToSubtract
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price
     */
    public function subtract(self $priceToSubtract): self
    {
        return new self(
            $this->priceWithoutVat->subtract($priceToSubtract->priceWithoutVat),
            $this->priceWithVat->subtract($priceToSubtract->priceWithVat)
        );
    }
}
