<?php

namespace Shopsys\FrameworkBundle\Model\Product;

use Shopsys\FrameworkBundle\Component\FileUpload\ImageUploadData;
use Shopsys\FrameworkBundle\Form\UrlListData;

class ProductEditData
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductData
     */
    public $productData;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Parameter\ProductParameterValueData[]
     */
    public $parameters;

    /**
     * @var \Shopsys\FrameworkBundle\Component\FileUpload\ImageUploadData
     */
    public $images;

    /**
     * @var string[]
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
     * @var \Shopsys\FrameworkBundle\Form\UrlListData
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

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductData $productData
     */
    public function __construct(
        ProductData $productData = null
    ) {
        if ($productData !== null) {
            $this->productData = $productData;
        } else {
            $this->productData = new ProductData();
        }
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
