<?php

namespace Shopsys\ShopBundle\Model\Product;

use Shopsys\ShopBundle\Form\UrlListData;
use Shopsys\ShopBundle\Model\Product\ProductData;

class ProductEditData
{
    /**
     * @var \Shopsys\ShopBundle\Model\Product\ProductData
     */
    public $productData;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Parameter\ProductParameterValueData[]
     */
    public $parameters;

    /**
     * @var string[]
     */
    public $imagesToUpload;

    /**
     * @var \Shopsys\ShopBundle\Component\Image\Image[]
     */
    public $imagesToDelete;

    /**
     * @var \Shopsys\ShopBundle\Component\Image\Image[imageId]
     */
    public $orderedImages;

    /**
     * @var string[pricingGroupId]
     */
    public $manualInputPrices;

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
     * @var \Shopsys\ShopBundle\Form\UrlListData
     */
    public $urls;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Product[]
     */
    public $accessories;

    /**
     * @var string[]|null[]
     */
    public $heurekaCpcValues;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Product[]
     */
    public $variants;

    /**
     * @var bool[domainId]
     */
    public $showInZboziFeed;

    /**
     * @var string[]|null[]
     */
    public $zboziCpcValues;

    /**
     * @var string[]|null[]
     */
    public $zboziCpcSearchValues;

    /**
     * @param \Shopsys\ShopBundle\Model\Product\ProductData $productData
     * @param \Shopsys\ShopBundle\Model\Product\Parameter\ProductParameterValueData[] $parameters
     * @param string[] $imagesToUpload
     * @param \Shopsys\ShopBundle\Component\Image\Image[] $imagesToDelete
     * @param \Shopsys\ShopBundle\Component\Image\Image[] $orderedImages
     * @param string[] $manualInputPrices
     * @param string[]|null[] $seoTitles
     * @param string[]|null[] $seoMetaDescriptions
     * @param string[]|null[] $descriptions
     * @param string[]|null[] $shortDescriptions
     * @param \Shopsys\ShopBundle\Model\Product\Product[] $accessories
     * @param string[]|null[] $heurekaCpcValues
     * @param \Shopsys\ShopBundle\Model\Product\Product[] $variants
     * @param bool[domainId] $showInZboziFeed
     * @param string[]|null[] $zboziCpcValues
     * @param string[]|null[] $zboziCpcSearchValues
     */
    public function __construct(
        ProductData $productData = null,
        array $parameters = [],
        array $imagesToUpload = [],
        array $imagesToDelete = [],
        array $orderedImages = [],
        array $manualInputPrices = [],
        array $seoTitles = [],
        array $seoMetaDescriptions = [],
        array $descriptions = [],
        array $shortDescriptions = [],
        array $accessories = [],
        array $heurekaCpcValues = [],
        array $variants = [],
        array $showInZboziFeed = [],
        array $zboziCpcValues = [],
        array $zboziCpcSearchValues = []
    ) {
        if ($productData !== null) {
            $this->productData = $productData;
        } else {
            $this->productData = new ProductData();
        }
        $this->parameters = $parameters;
        $this->imagesToUpload = $imagesToUpload;
        $this->imagesToDelete = $imagesToDelete;
        $this->orderedImages = $orderedImages;
        $this->manualInputPrices = $manualInputPrices;
        $this->seoTitles = $seoTitles;
        $this->seoMetaDescriptions = $seoMetaDescriptions;
        $this->descriptions = $descriptions;
        $this->shortDescriptions = $shortDescriptions;
        $this->urls = new UrlListData();
        $this->accessories = $accessories;
        $this->heurekaCpcValues = $heurekaCpcValues;
        $this->variants = $variants;
        $this->showInZboziFeed = $showInZboziFeed;
        $this->zboziCpcValues = $zboziCpcValues;
        $this->zboziCpcSearchValues = $zboziCpcSearchValues;
    }
}
