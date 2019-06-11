<?php

namespace Shopsys\FrameworkBundle\Model\Order\Item;

class OrderItemData
{
    /**
     * @var string|null
     */
    public $name;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Money\Money|null
     */
    public $priceWithVat;

    /**
     * If this attribute is set to true, all prices in this data object other that $priceWithVat will be ignored.
     * The prices will be recalculated when the OrderItem entity is edited.
     * This means you can set only a single price ($priceWithVat) and others will be calculated automatically.
     *
     * @var bool
     */
    public $usePriceCalculation = true;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Money\Money|null
     */
    public $priceWithoutVat;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Money\Money|null
     */
    public $totalPriceWithVat;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Money\Money|null
     */
    public $totalPriceWithoutVat;

    /**
     * @var string|null
     */
    public $vatPercent;

    /**
     * @var int|null
     */
    public $quantity;

    /**
     * @var string|null
     */
    public $unitName;

    /**
     * @var string|null
     */
    public $catnum;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Transport\Transport|null
     */
    public $transport;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Payment\Payment|null
     */
    public $payment;
}
