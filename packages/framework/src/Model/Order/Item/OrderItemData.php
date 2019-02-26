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
     * @var \Shopsys\FrameworkBundle\Component\Money\Money|null
     */
    public $priceWithoutVat;

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
