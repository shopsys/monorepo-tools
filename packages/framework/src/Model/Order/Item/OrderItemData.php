<?php

namespace Shopsys\FrameworkBundle\Model\Order\Item;

class OrderItemData
{
    /**
     * @var string|null
     */
    public $name;

    /**
     * @var string|null
     */
    public $priceWithVat;

    /**
     * @var string|null
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
}
