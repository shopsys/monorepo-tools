<?php

namespace Shopsys\FrameworkBundle\Model\Pricing;

class Price
{
    /**
     * @var string
     */
    private $priceWithoutVat;

    /**
     * @var string
     */
    private $priceWithVat;

    /**
     * @var string
     */
    private $vatAmount;

    /**
     * @param string $priceWithoutVat
     * @param string $priceWithVat
     */
    public function __construct($priceWithoutVat, $priceWithVat)
    {
        $this->priceWithoutVat = $priceWithoutVat;
        $this->priceWithVat = $priceWithVat;
        $this->vatAmount = $priceWithVat - $priceWithoutVat;
    }

    /**
     * @return string
     */
    public function getPriceWithoutVat()
    {
        return $this->priceWithoutVat;
    }

    /**
     * @return string
     */
    public function getPriceWithVat()
    {
        return $this->priceWithVat;
    }

    /**
     * @return string
     */
    public function getVatAmount()
    {
        return $this->vatAmount;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price $priceToAdd
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price
     */
    public function add(self $priceToAdd)
    {
        return new self(
            $this->priceWithoutVat + $priceToAdd->getPriceWithoutVat(),
            $this->priceWithVat + $priceToAdd->getPriceWithVat()
        );
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price $priceToSubtract
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price
     */
    public function subtract(self $priceToSubtract)
    {
        return new self(
            $this->priceWithoutVat - $priceToSubtract->getPriceWithoutVat(),
            $this->priceWithVat - $priceToSubtract->getPriceWithVat()
        );
    }
}
