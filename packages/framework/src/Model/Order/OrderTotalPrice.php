<?php

namespace Shopsys\FrameworkBundle\Model\Order;

use Shopsys\FrameworkBundle\Component\Money\Money;

class OrderTotalPrice
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Money\Money
     */
    protected $priceWithVat;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Money\Money
     */
    protected $priceWithoutVat;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Money\Money
     */
    protected $productPriceWithVat;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $priceWithVat
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $priceWithoutVat
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $productPriceWithVat
     */
    public function __construct(Money $priceWithVat, Money $priceWithoutVat, Money $productPriceWithVat)
    {
        $this->priceWithVat = $priceWithVat;
        $this->priceWithoutVat = $priceWithoutVat;
        $this->productPriceWithVat = $productPriceWithVat;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\Money\Money
     */
    public function getPriceWithVat(): Money
    {
        return $this->priceWithVat;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\Money\Money
     */
    public function getPriceWithoutVat(): Money
    {
        return $this->priceWithoutVat;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\Money\Money
     */
    public function getProductPriceWithVat(): Money
    {
        return $this->productPriceWithVat;
    }
}
