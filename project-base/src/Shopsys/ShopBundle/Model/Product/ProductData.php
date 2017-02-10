<?php

namespace Shopsys\ShopBundle\Model\Product;

use Shopsys\ShopBundle\Model\Product\Product;

/**
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class ProductData
{

    /**
     * @var array
     */
    public $name;

    /**
     * @var string|null
     */
    public $catnum;

    /**
     * @var string|null
     */
    public $partno;

    /**
     * @var string|null
     */
    public $ean;

    /**
     * @var string
     */
    public $price;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Vat\Vat|null
     */
    public $vat;

    /**
     * @var \DateTime|null
     */
    public $sellingFrom;

    /**
     * @var \DateTime|null
     */
    public $sellingTo;

    /**
     * @var bool|null
     */
    public $sellingDenied;

    /**
     * @var bool
     */
    public $hidden;

    /**
     * @var bool
     */
    public $usingStock;

    /**
     * @var int|null
     */
    public $stockQuantity;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Unit\Unit|null
     */
    public $unit;

    /**
     * @var string
     */
    public $outOfStockAction;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Availability\Availability|null
     */
    public $availability;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Availability\Availability|null
     */
    public $outOfStockAvailability;

    /**
     * @var array
     */
    public $flags;

    /**
     * @var \Shopsys\ShopBundle\Model\Category\Category[domainId][]
     */
    public $categoriesByDomainId;

    /**
     * @var string
     */
    public $priceCalculationType;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Brand\Brand|null
     */
    public $brand;

    /**
     * @var string[]
     */
    public $variantAlias;

    /**
     * @var int
     */
    public $orderingPriority;

    public function __construct() {
        $this->name = [];
        $this->price = 0;
        $this->sellingDenied = false;
        $this->hidden = false;
        $this->flags = [];
        $this->usingStock = false;
        $this->categoriesByDomainId = [];
        $this->priceCalculationType = Product::PRICE_CALCULATION_TYPE_AUTO;
        $this->variantAlias = [];
        $this->orderingPriority = 0;
    }

}
