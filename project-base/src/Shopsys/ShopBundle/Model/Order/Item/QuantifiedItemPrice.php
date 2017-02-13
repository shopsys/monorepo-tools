<?php

namespace Shopsys\ShopBundle\Model\Order\Item;

use Shopsys\ShopBundle\Model\Pricing\Price;
use Shopsys\ShopBundle\Model\Pricing\Vat\Vat;

class QuantifiedItemPrice
{
    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Price
     */
    private $unitPrice;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Price
     */
    private $totalPrice;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Vat\Vat
     */
    private $vat;

    /**
     * @param \Shopsys\ShopBundle\Model\Pricing\Price $unitPrice
     * @param \Shopsys\ShopBundle\Model\Pricing\Price $totalPrice
     * @param \Shopsys\ShopBundle\Model\Pricing\Vat\Vat $vat
     */
    public function __construct(
        Price $unitPrice,
        Price $totalPrice,
        Vat $vat
    ) {
        $this->unitPrice = $unitPrice;
        $this->totalPrice = $totalPrice;
        $this->vat = $vat;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Pricing\Price
     */
    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Pricing\Price
     */
    public function getTotalPrice()
    {
        return $this->totalPrice;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Pricing\Vat\Vat
     */
    public function getVat()
    {
        return $this->vat;
    }
}
