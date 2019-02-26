<?php

namespace Shopsys\FrameworkBundle\Model\Product;

use Shopsys\FrameworkBundle\Component\FileUpload\ImageUploadData;
use Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\UrlListData;

class ProductData
{
    /**
     * @var string[]|null[]
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
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat|null
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
     * @var bool
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
     * @var \Shopsys\FrameworkBundle\Model\Product\Unit\Unit|null
     */
    public $unit;

    /**
     * @var string Product::OUT_OF_STOCK_ACTION_*
     */
    public $outOfStockAction;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Availability\Availability|null
     */
    public $availability;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Availability\Availability|null
     */
    public $outOfStockAvailability;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Flag\Flag[]
     */
    public $flags;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Category\Category[][]
     */
    public $categoriesByDomainId;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Brand\Brand|null
     */
    public $brand;

    /**
     * @var string[]|null[]
     */
    public $variantAlias;

    /**
     * @var int
     */
    public $orderingPriority;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Parameter\ProductParameterValueData[]
     */
    public $parameters;

    /**
     * @var \Shopsys\FrameworkBundle\Component\FileUpload\ImageUploadData
     */
    public $images;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Money\Money[]|null[]
     */
    public $manualInputPricesByPricingGroupId;

    /**
     * @var string[]|null[]
     */
    public $seoTitles;

    /**
     * @var string[]|null[]
     */
    public $seoMetaDescriptions;

    /**
     * @var string[]|null[]
     */
    public $descriptions;

    /**
     * @var string[]|null[]
     */
    public $shortDescriptions;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\UrlListData
     */
    public $urls;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Product[]
     */
    public $accessories;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Product[]
     */
    public $variants;

    /**
     * @var string[]|null[]
     */
    public $seoH1s;

    /**
     * @var array
     */
    public $pluginData;

    public function __construct()
    {
        $this->name = [];
        $this->sellingDenied = false;
        $this->hidden = false;
        $this->flags = [];
        $this->usingStock = false;
        $this->categoriesByDomainId = [];
        $this->variantAlias = [];
        $this->orderingPriority = 0;
        $this->parameters = [];
        $this->images = new ImageUploadData();
        $this->manualInputPricesByPricingGroupId = [];
        $this->seoTitles = [];
        $this->seoMetaDescriptions = [];
        $this->descriptions = [];
        $this->shortDescriptions = [];
        $this->urls = new UrlListData();
        $this->accessories = [];
        $this->variants = [];
        $this->seoH1s = [];
        $this->pluginData = [];
    }
}
